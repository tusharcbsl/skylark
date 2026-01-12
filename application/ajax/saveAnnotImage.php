<?php
require_once '../../sessionstart.php';
require_once '../config/database.php';
require_once '../pages/function.php';
require_once '../../classes/fileManager.php';

$docId = $_POST['docId'];
$data = $_POST['image'];
$action = $_POST['action'];
$ticketId = $_POST['ticket'];
$annot_action = $_POST['annot_action'];
$chk = $_POST['chk'];
$activities = $_POST['activities'];
$tid = trim($_POST['tid']);

$file = mysqli_query($db_con, "select * from tbl_document_master where doc_id='$docId'") or die('error d' . mysqli_error($db_con));
$rwFile = mysqli_fetch_assoc($file);
$filePath = $rwFile['doc_path'];
$fname = $rwFile['old_doc_name'];
$doc_extn = $rwFile['doc_extn'];
$filesize = $rwFile['doc_size'];
$slid = $rwFile['doc_name'];
$docName = $rwFile['doc_name'];
$oldDocName = $rwFile['doc_name'];


$user_id = $_SESSION['cdes_user_id'];
$storagePath = substr($filePath, 0, strrpos($filePath, '/'));

$uploaddir = '../../extract-here/'.$storagePath.'/';

$fname = substr($fname, 0, strrpos($fname, '.'));
$fname = str_replace("-", "", $fname);
$nfilename = $fname.time();
$filenameEnct = urlencode(base64_encode($nfilename)).'.'.$doc_extn;

$filepath = $uploaddir.$filenameEnct;

list($type, $data) = explode(';', $data);
list(, $data)      = explode(',', $data);
$data = base64_decode($data);

$upload = file_put_contents($filepath, $data);
$docPath = $storagePath.'/'.$filenameEnct;

if($upload){
    
    if(uploadFileInFtpServer($docPath, $filepath)){
        
        if($action == "savenew"){
            
            $chekFileVersion = mysqli_query($db_con, "SELECT * FROM `tbl_document_master` WHERE find_in_set('$slid', doc_name)") or die('Error:' . mysqli_error($db_con));

            $flVersion = mysqli_num_rows($chekFileVersion);

            $flVersion = $flVersion + 1;

            $file_name = $ticketId . '_' .$flVersion . '.' .$doc_extn; 

            $fileNamewtVersion = $fname.'_'.$flVersion.'.'.$doc_extn;
            
            $docName = explode("_",$docName);

            $updateDocName = $docName[0].'_'.$docId .((!empty($docName[1]))?'_'.$docName[1]:'');
            
            $cols = '';
            $columns = mysqli_query($db_con, "SHOW COLUMNS FROM tbl_document_master");
            while ($rwCols = mysqli_fetch_array($columns)) {
                if ($rwCols['Field'] != 'doc_id') {
                    if (empty($cols)) {
                        $cols = '`' . $rwCols['Field'] . '`';
                    } else {
                        $cols = $cols . ',`' . $rwCols['Field'] . '`';
                    }
                }
            }
            
            
            $createVrsn = mysqli_query($db_con, "INSERT INTO tbl_document_master($cols) select $cols from tbl_document_master where doc_id='$docId'"); // or die('Error:' . mysqli_error($db_con));
           //$createVrsn = mysqli_query($db_con, "INSERT INTO tbl_document_master(doc_name, old_doc_name, doc_extn, doc_path, doc_size, uploaded_by, noofpages, dateposted) VALUES ('$updateDocName', '$file_name', '$doc_extn', '$docPath', '$filesize', '$user_id', '1', '$date')") or die('Error:' . mysqli_error($db_con));
            $insertDociD = mysqli_insert_id($db_con);
            
            if($insertDociD){

                $updateNew = mysqli_query($db_con, "update tbl_document_master set doc_name='$updateDocName' where doc_id='$insertDociD'");
                $qry = mysqli_query($db_con, "update tbl_document_master set doc_extn='$doc_extn', old_doc_name='$fileNamewtVersion', doc_path='$docPath', uploaded_by='$uploadedBy', doc_size='$filesize', noofpages='1', dateposted='$date'where doc_id='$docId'");

                $redirurl = 'imageAnnotation?uid='.urlencode(base64_encode($_SESSION['cdes_user_id'])).'&i='.urlencode(base64_encode($insertDociD)).'&tid='.base64_encode(urlencode($tid));
               
                $response = array("status" => "success", "msg" => "File save as new successfully.", "redirurl" => $redirurl);
            
            }else{
               $response = array("status" => "failed", "msg" => "File could not save.");
            }
            
        }else{
            
            
            $update = mysqli_query($db_con, "update tbl_document_master set doc_path='$docPath' where doc_id='$docId'");
            if($update){
                 $savetype = " and override file";
                $redirurl = 'imageAnnotation?uid='.urlencode(base64_encode($_SESSION['cdes_user_id'])).'&i='.urlencode(base64_encode($docId)).'&tid='.base64_encode(urlencode($tid));
                $response = array("status" => "success", "msg" =>"File Override successfully.", "redirurl" => $redirurl);
            }else{
                $response = array("status" => "failed", "msg" => "File could not override.");
            }
        }
         
    }else{
        $response = array("status" => "failed", "msg" => "Failed to upload file");
       
    }
    
}else{
    $response = array("status" => "failed", "msg" => "Failed to upload file");
}


if($response['status']=='success'){
    $actions ="";
    foreach($activities as $action){
       if(!empty($action)){
        $actions .=" ".$action." added on document".$savetype ;
       } 

    }

    // Insert annotation activity log 
       $log_sql="insert into tbl_ezeefile_logs_wf set 
            user_id='$_SESSION[cdes_user_id]',
            user_name='$_SESSION[admin_user_name] $_SESSION[admin_user_last]',
            action_name='$actions',
            start_date='$date',
            system_ip='$_SERVER[REMOTE_ADDR]]',
            doc_id='$docId'";
        $log_query= mysqli_query($db_con, $log_sql);
} 
   // print_r($response);
 echo json_encode($response);

      

 


function uploadFileInFtpServer($destinationPath, $sourcePath) {

    //encrypt_my_file($sourcePath);

    $fileManager = new fileManager();
	// Connect to file server
	$fileManager->conntFileServer();
	if($fileManager->uploadFile($sourcePath, ROOT_FTP_FOLDER . '/' . $destinationPath)){
		 return true;
	}else{
		 return false;
	}
}
?>