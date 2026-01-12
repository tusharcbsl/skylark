<?php

require_once 'connection.php';
if (
isset($_POST['docid'])&&!empty($_POST['docid'])
&&isset($_POST['ip'])&&!empty($_POST['ip'])
&&isset($_POST['username'])&&!empty($_POST['username'])
&&isset($_POST['userid'])&&!empty($_POST['userid'])




) {
                $id = $_POST['docid'];
								$host = $_POST['ip'];
								$username = $_POST['username'];
								$userid = $_POST['userid'];
								$res = array();
	      date_default_timezone_set("Asia/Kolkata");
					$date = date("Y-m-d H:i");
								
                $getDocPath = mysqli_query($con, "select * from tbl_document_master where doc_id='$id'") or die('Error:' . mysqli_error($con));
                $rwgetDocPath = mysqli_fetch_assoc($getDocPath);
                $filePath = $rwgetDocPath['doc_path'];
                $delvrsnfile = $rwgetDocPath['old_doc_name'];
                $del = mysqli_query($con, "delete from tbl_document_master where doc_id='$id'") or die('Error:' . mysqli_error($con));
                unlink('../../extract-here/' . $filePath);
                if ($del) {
                    $log = mysqli_query($con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$userid', '$username',null,null,'Storage Document $delvrsnfile Deleted','$date',null,'$host',null)") or die('error : ' . mysqli_error($con));

                    $docName = explode("_", $rwgetDocPath['doc_name']);
                    $storgId = $docName[0];
                   // echo'<script>taskSuccess("storageFiles?id=' . urlencode(base64_encode($storgId)) . '","Storage Deleted //Successfully !");</script>';
                    //echo'<script>taskSuccess("storageFiles","Document Deleted Successfully !");</script>';
										
										$temp = array();
										$temp['message'] = 'Document Deleted Succesfully';
										$temp['error']= 'false';
										array_push($res,$temp);
										
										
										
                } else {
                    //echo '<script>taskFailed("storageFiles","Document Not Deleted")</script>';
									  $temp = array();
										$temp['message'] = 'Document Not Deleted';
										$temp['error']= 'true';
										array_push($res,$temp);
                }
                mysqli_close($con);
            }
            ?>