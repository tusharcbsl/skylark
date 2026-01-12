<?php

require_once 'connection.php';

if (isset($_POST['docid'])&&!empty($_POST['docid'])
	&&isset($_POST['fullname'])&&!empty($_POST['fullname'])
	&&isset($_POST['permission'])&&!empty($_POST['permission'])
	&&isset($_POST['roleid'])&&!empty($_POST['roleid'])
	&&isset($_POST['ip'])&&!empty($_POST['ip'])
	&&isset($_POST['userid'])&&!empty($_POST['userid'])
    &&isset($_POST['filename'])&&!empty($_POST['filename'])
	 
	) {
                $id = $_POST['docid'];
	             $username =$_POST['fullname'];
	             $permission = $_POST['permission'];
	             $roleid =$_POST['roleid'];  
	             $host = $_POST['ip']; 
	             $userid = $_POST['userid']; 
	             $filename = $_POST['filename']; 
	             date_default_timezone_set("Asia/Kolkata");
			     $date = date("Y-m-d H:i");
	                
		
	
                $getDocPath = mysqli_query($con, "select * from tbl_document_master where doc_id='$id'") or die('Error:' . mysqli_error($con));
	
                $rwgetDocPath = mysqli_fetch_assoc($getDocPath);
                $filePath = $rwgetDocPath['doc_path'];
                $delfilename = $rwgetDocPath['old_doc_name'];
                $deldocId = $rwgetDocPath['doc_id'];
                $storgId = $rwgetDocPath['doc_name'];
	
                if ( $roleid == 1 && $permission == "Yes") {
                    $path = substr($rwgetDocPath['doc_path'], 0, strrpos($rwgetDocPath['doc_path'], '/') + 1);
                    $pathtxt = '../../extract-here/' . $path . 'TXT/' . $id . '.txt';
                    $del = mysqli_query($con, "DELETE FROM tbl_document_master WHERE doc_id ='$id'") or die('Error:' . mysqli_error($con));
                    if ($del) {
                        unlink('../../extract-here/' . $filePath);
                        unlink($pathtxt);
                        $log = mysqli_query($con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `doc_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$userid', '$username',null,null, '$id', 'Storage Document $filename Deleted','$date',null,'$host',null)") or die('error : ' . mysqli_error($con));
                       // echo'<script>taskSuccess("storageFiles?id=' . urlencode(base64_encode($storgId)) . '","Document Deleted Successfully !");</script>';
						 $result = array();
                         $result['message'] ='Document Deleted Successfully !';
                         $result['error']='false';
                         echo  json_encode($result);
						
                    } else {
                      //  echo'<script>taskFailed("storageFiles?id=' . urlencode(base64_encode($storgId)) . '","Document not Deleted  !");</script>';
						 $result = array();
                         $result['message'] ='Document not Deleted  !';
                         $result['error']='true';
                         echo json_encode($result);
						
                    }
                } elseif ($roleid == 1 && $permission == "No") {
                    $deletefilename = mysqli_query($con, "UPDATE tbl_document_master SET flag_multidelete=0 WHERE doc_id='$id'") or die('Error:' . mysqli_error($con));
                    if ($deletefilename) {
                        $log = mysqli_query($con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `doc_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$userid', '$username',null,null, '$id', 'Storage Document $filename Deleted','$date',null,'$host',null)") or die('error : ' . mysqli_error($con));
                       // echo'<script>taskSuccess("storageFiles?id=' . urlencode(base64_encode($storgId)) . '","Document Deleted Successfully !");</script>';
						
						 $result = array();
                         $result['message'] ='Document Deleted Successfully !';
                         $result['error']='false';
                         echo  json_encode($result);
						
                    } else {
                        //echo '<script>taskSuccess("storageFiles?id=' . urlencode(base64_encode($storgId)) . '","Document Not Deleted")</script
						 $result = array();
                         $result['message'] ='Document not Deleted  !';
                         $result['error']='true';
                         echo json_encode($result);
                    }
                } else {
                    $deletefilename = mysqli_query($con, "UPDATE tbl_document_master SET flag_multidelete=0 WHERE doc_id='$id'") or die('Error:' . mysqli_error($con));
                    if ($deletefilename) {
                        $log = mysqli_query($con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `doc_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$userid', '$username',null,null, '$id', 'Storage Document $filename Deleted','$date',null,'$host',null)") or die('error : ' . mysqli_error($con));
                      //  echo'<script>taskSuccess("storageFiles?id=' . urlencode(base64_encode($storgId)) . '","Document Deleted Successfully !");</script>';
						
						 $result = array();
                         $result['message'] ='Document Deleted Successfully !';
                         $result['error']='false';
                         echo  json_encode($result);
						
                    } else {
                       // echo '<script>taskSuccess("storageFiles?id=' . urlencode(base64_encode($storgId)) . '","Document Not Deleted")</script>';
						 $result = array();
                         $result['message'] ='Document not Deleted  !';
                         $result['error']='true';
                         echo json_encode($result);
                    }
                }
          
            }
?>