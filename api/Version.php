<?php

require_once 'connection.php';

if(isset($_POST['checkin_docid'])&&!empty($_POST['checkin_docid'])
				&&isset($_POST['checkin_userid'])&&!empty($_POST['checkin_userid'])
				&&isset($_POST['checkin_pagecount'])&&!empty($_POST['checkin_docid'])
				&&isset($_POST['checkin_metadata'])&&!empty($_POST['checkin_metadata'])
				&&isset($_FILES['checkin_file'])&&!empty($_FILES['checkin_file'])
				&&isset($_POST['checkin_username'])&&!empty($_POST['checkin_username'])
				&&isset($_POST['checkin_ip'])&&!empty($_POST['checkin_ip'])
    
  ) 
		
            {
				
				//$metadata = $_POST['checkin_metadata'];
				//$metadata = json_decode($metadata,true);
				
                if (!empty($_FILES['checkin_file']['name'])) {
                    $user_id =$_POST['checkin_userid'];
					$username =$_POST['checkin_username'];
					$host = $_POST['checkin_ip'];
					date_default_timezone_set("Asia/Kolkata");
                    $date = date("Y-m-d H:i"); 
					$metadata = $_POST['checkin_metadata'];
					
					$metadata = json_decode($metadata,true);
					$message = '';
					
					//print_r($metadata);
					
					
					//echo count($metadata['meta']);
					
					//for($i=0;$i<count($metadata['meta']);$i++){
					//echo $metadata['meta'][$i]['metaLabel'];
					//}
					
					
					
					
					//die;
     
                    $doc_id =$_POST['checkin_docid'];
                    $file_name = $_FILES['checkin_file']['name'];
                    $file_size = $_FILES['checkin_file']['size'];
                    $file_type = $_FILES['checkin_file']['type'];
                    $file_tmp = $_FILES['checkin_file']['tmp_name'];
                    $pageCount = $_POST['checkin_pagecount'];
					
					
					   if ($pageCount <= 0) {
                        $pageCount = 1;
                    }
                    $extn = substr($file_name, strrpos($file_name, '.') + 1);
                    $fname = substr($file_name, 0, strrpos($file_name, '.'));

                    $fileExtn = substr($file_name, strrpos($file_name, ".") + 1);
                    $getDocName = mysqli_query($con, "select * from tbl_document_master where doc_id = '$doc_id'") or die('Error:' . mysqli_error($con));
                    $rwgetDocName = mysqli_fetch_assoc($getDocName);
                    $docName = $rwgetDocName['doc_name'];
                    //$docName = explode("_", $docName);
                    $old_file_name = $rwgetDocName['old_doc_name'];
                    $oldextn = substr($old_file_name, strrpos($old_file_name, '.') + 1); // old file extn
                    $oldfname = substr($old_file_name, 0, strrpos($old_file_name, '.')); // old file name

                    $updateDocName = $docName . '_' . $doc_id; //storage id followed by doc id
                    $chekFileVersion = mysqli_query($con, "SELECT * FROM `tbl_document_master` WHERE find_in_set('$updateDocName', doc_name)") or die('Error:' . mysqli_error($con));
                    $flVersion = mysqli_num_rows($chekFileVersion);
                    $flVersion = $flVersion + 1;
                    $nfilename = $oldfname . '_' . $flVersion;

                    $strgName = mysqli_query($con, "select * from tbl_storage_level where sl_id = '$docName'") or die('Error:' . mysqli_error($con));
                    $rwstrgName = mysqli_fetch_assoc($strgName);
                    $storageName = $rwstrgName['sl_name'];
					
				
					
					 $storageName = str_replace(" ", "", $storageName);
                    $storageName = preg_replace('/[^A-Za-z0-9\-]/', '', $storageName);
                    $uploaddir = "../../extract-here/" . $storageName . '/';
                    if (!is_dir($uploaddir)) {
                        mkdir($uploaddir, 777, TRUE) or die(print_r(error_get_last()));
                    }
                    $nfilename = preg_replace('/[^A-Za-z0-9_\-]/', '', $nfilename);
                    // $filenameEnct=$fname.'.'.$extn;// urlencode(base64_encode($fname)).'.'.$extn;
                    $filenameEnct = urlencode(base64_encode($nfilename));
                    $filenameEnct = preg_replace('/[^A-Za-z0-9_\-]/', '', $filenameEnct);
                    $filenameEnct = $filenameEnct . '.' . $extn;
                    $filenameEnct = time() . $filenameEnct;

                    //  $image_path = "images/" . $file_name;
                    $uploaddir = $uploaddir . $filenameEnct;
					
					
                    $upload = move_uploaded_file($file_tmp, $uploaddir) or die(print_r(error_get_last()));
					//echo "upload ok";
					if ($upload) {
						//echo "ok";
                        $cols = '';
                        $columns = mysqli_query($con, "SHOW COLUMNS FROM tbl_document_master");
                        while ($rwCols = mysqli_fetch_array($columns)) {
											
					if ($rwCols['Field'] != 'doc_id') {
                                if (empty($cols)) {
                                    $cols = '`' . $rwCols['Field'] . '`';
                                } else {
                                    $cols = $cols . ',`' . $rwCols['Field'] . '`';
                                }
                            }
                        }
					
					    $createVrsn = mysqli_query($con, "INSERT INTO tbl_document_master($cols) select $cols from tbl_document_master where doc_id='$doc_id'") or die('Error:' . mysqli_error($con));
                        $insertDocID = mysqli_insert_id($con);
                        $getMetaId = mysqli_query($con, "select * from tbl_document_master where doc_id = '$doc_id'") or die('Error:' . mysqli_error($con));
                        //echo "select * from tbl_document_master where doc_id = '$_POST[docid]'";
                        $meta_row = mysqli_fetch_assoc($getMetaId);
                        $getMetaId = mysqli_query($con, "select * from tbl_metadata_to_storagelevel where sl_id = '$meta_row[doc_name]'") or die('Error:' . mysqli_error($con));
                        $i = 1;
						 $count=0;
						 while ($rwgetMetaId = mysqli_fetch_assoc($getMetaId)) {
                            $getMetaName = mysqli_query($con, "select * from tbl_metadata_master where id = '$rwgetMetaId[metadata_id]'") or die('Error:' . mysqli_error($con));
                            $StorageNme = mysqli_query($con, "select sl_name from tbl_storage_level where sl_id='$rwgetMetaId[sl_id]'");
                            $rwStrName = mysqli_fetch_assoc($StorageNme);
							
                            while ($rwgetMetaName = mysqli_fetch_assoc($getMetaName)) {
								
                                $meta = mysqli_query($con, "select `$rwgetMetaName[field_name]` from tbl_document_master where doc_id='$meta_row[doc_id]'");
                                $rwMeta = mysqli_fetch_array($meta);
								
								//$metadata = $_POST['checkin_metadata'];
								//print_r($metadata);
								
                                if ($rwgetMetaName['field_name'] == 'noofpages') {
									
									
                                    
                                } else {
									//echo $count;
									//die;
                                    $fieldValue =$metadata['meta'][$count]['metaEntered'];
								    //print_r($fieldValue);
									$count++;
									
									//die;
                                  $log = mysqli_query($con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$user_id', '$username',null,null,'Versioning Document $file_name Added','$date',null,'$host',null)") or die('error : ' . mysqli_error($con));
                                    if ($createVrsn) {
                                        //echo "update tbl_document_master set `$rwgetMetaName[field_name]` = '$fieldValue', doc_name='$updateDocName' where doc_id='$insertDocID'";
                                        //echo "update tbl_document_master set old_doc_name='$file_name',filename='$fname', doc_extn='$extn', doc_path='$storageName/$filenameEnct', uploaded_by='$user_id', doc_size='$file_size', noofpages='$pageCount', dateposted='$date' where doc_id='$_POST[docid]'";
                                        //die;
                                        $updateNew = mysqli_query($con, "update tbl_document_master set `$rwgetMetaName[field_name]` = '$fieldValue', doc_name='$updateDocName' where doc_id='$insertDocID'");
                                        $updateOld = mysqli_query($con, "update tbl_document_master set `$rwgetMetaName[field_name]` = '$fieldValue', old_doc_name='$file_name',filename='$fname', doc_extn='$extn', doc_path='$storageName/$filenameEnct', uploaded_by='$user_id', doc_size='$file_size', noofpages='$pageCount', dateposted='$date' where doc_id='$doc_id'");
                                        if ($updateNew && $updateOld) {
                                           // echo'<script>taskSuccess("storageFiles?id=' . $pageid . ((isset($_GET[start])) ? ('&start=' . $_GET[start]) : '') . '","Updated Successfully !");</script>';
										$message = 'yes';
										}
																		
                                    }
                                }
                            }
                        }
						 
						if($message = 'yes'){
						 
						                    $res = array();
											$res['message'] = 'Updated Successfully !';
											$res['error'] = 'false';
											echo json_encode($res);	
						
						}
						
						
								}
					
					
					
			}
				 else {

                    $getMetaId = mysqli_query($con, "select * from tbl_document_master where doc_id = '$doc_id'") or die('Error:' . mysqli_error($con));
                    //echo "select * from tbl_document_master where doc_id = '$_POST[docid]'";
                    $meta_row = mysqli_fetch_assoc($getMetaId);
                    $getMetaId = mysqli_query($con, "select * from tbl_metadata_to_storagelevel where sl_id = '$meta_row[doc_name]'") or die('Error:' . mysqli_error($con));
                    //echo "select * from tbl_metadata_to_storagelevel where sl_id = '$meta_row[doc_name]'";
                    $i = 1;
					 $count=0;
                   
                    while ($rwgetMetaId = mysqli_fetch_assoc($getMetaId)) {
                       
                        $getMetaName = mysqli_query($con, "select * from tbl_metadata_master where id = '$rwgetMetaId[metadata_id]'") or die('Error:' . mysqli_error($con));
                        $StorageNme = mysqli_query($con, "select sl_name from tbl_storage_level where sl_id='$rwgetMetaId[sl_id]'");
                        $rwStrName = mysqli_fetch_assoc($StorageNme);
                        while ($rwgetMetaName = mysqli_fetch_assoc($getMetaName)) {
							$count=0;
                            $meta = mysqli_query($con, "select `$rwgetMetaName[field_name]` from tbl_document_master where doc_id='$meta_row[doc_id]'");
                            $rwMeta = mysqli_fetch_array($meta);
                            //$metadatValue = $rwMeta[''];
                            //echo $i; echo '-';
                            if ($rwgetMetaName['field_name'] == 'noofpages') {
                                
                            } else {
		                         //  $j=$i-1;
									//echo $j;
									//die;
                                $fieldValue =$metadata['meta'][$count]['metaEntered'];
							    // print_r($fieldValue);
								$count++;
								
								//die;
                                //echo "update tbl_document_master set `$rwgetMetaName[field_name]` = '$fieldValue' where doc_id = '$_POST[metaId]' or (substring_index(doc_name,'_',-1)='$_POST[metaId]' and substring_index(doc_name,'_',1)='$_POST[metaId]')";
                            $updateMeta = mysqli_query($con, "update tbl_document_master set `$rwgetMetaName[field_name]` = '$fieldValue' where doc_id = '$doc_id' or (substring_index(doc_name,'_',-1)='$doc_id' and substring_index(doc_name,'_',1)='$doc_id]')") or die('Error' . mysqli_error($con));
                                if ($updateMeta) {
                                    //metadata update log
                                    $log = mysqli_query($con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$user_id', '$username',null,null,'MetaData Value $fieldValue Assign in MetaData Field $rwgetMetaName[field_name] in $rwStrName[sl_name]','$date',null,'$host',null)") or die('error : ' . mysqli_error($con));
                                    //echo'<script>taskSuccess("storageFiles?id=' . $pageid . ((isset($_GET[start])) ? ('&start=' . $_GET[start]) : '') . '","MetaData Updated Successfully !");</script>';
									$res = array();
											$res['message'] = 'Updated Successfully !';
											$res['error'] = 'false';
											echo json_encode($res);
											
                                }
                            }
                        }

                        $i++;
                    }
                    mysqli_close($con);

				
				
				
			
				
								
			}		}
								?>