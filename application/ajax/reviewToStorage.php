<?php
require_once '../../sessionstart.php';
if (!isset($_SESSION['cdes_user_id'])) {
    header("location:../../logout.php");
}
if (isset($_SESSION['lang'])) {
    $file = "../../" . $_SESSION['lang'] . ".json";
} else {
    $file = "../../English.json";
}
$data = file_get_contents($file);
$lang = json_decode($data, true);
require './../config/database.php';
require_once '../../classes/ftp.php';
$ftp = new ftp();
$revdoc_id= base64_decode(urldecode($_POST['revdoc_id']));
$reviewTicketId= base64_decode(urldecode($_POST['reviewTicketId']));
if(!empty($revdoc_id)){
    $res=sendReviewedToStorage($db_con,$revdoc_id,$reviewTicketId,$lang,$fileserver, $port, $ftpUser, $ftpPwd,$ftp);
} else {
   $res=array("status"=>"error","msg"=>$lang['missing_required_parameter']);
   if($res[status]=='success'){
       
   }
}
echo json_encode($res);


// send reviewed file to  storage
function sendReviewedToStorage($db_con,$revdoc_id,$reviewTicketId,$lang,$fileserver, $port, $ftpUser, $ftpPwd,$ftp){
    // Set autocommit to off
    mysqli_autocommit($db_con, FALSE);
    $tempFileRemove = array();
    //select record from review table;
    $sql="select * from tbl_document_reviewer where doc_id='$revdoc_id'";
    $rev_res=mysqli_fetch_assoc(mysqli_query($db_con, $sql));
    $docPath = $rev_res['doc_path'];
    if($rev_res){
    // check for html file.    
    $dc_ext = pathinfo($rev_res['doc_path'], PATHINFO_EXTENSION);
    if($dc_ext=='html'){
        $folderName = "../../temp". '/' . $_SESSION['cdes_user_id']. '/' . preg_replace('/[^A-Za-z0-9\-_]/', '', $rwStor['sl_name']);
        // get localPath;
        if(FTP_ENABLED){
            $localPath = $folderName . '/' . preg_replace('/[^A-Za-z0-9\-_]/', '', $rev_res[old_doc_name]) . '.' . "html";
            $ftp->conn("$fileserver", "$port", "$ftpUser", "$ftpPwd");
            $server_path = ROOT_FTP_FOLDER . '/' . $docPath;
            $ftp->get($localPath, $server_path); // download live "$server_path"  to local "$localpath"
        } else {
            $localPath='../../extract-here/'.$rev_res['doc_path'];  
        }
        $dc_ext='docx';
        $docPathNew= substr($rev_res['doc_path'], 0, strrpos($rev_res['doc_path'], '.'));
        $docPath=$docPathNew.'.'.$dc_ext;
        $new_name=basename($localPath,".html").'.'.$dc_ext;
        $lp=substr($localPath, 0, strrpos($localPath, '/'));
        $newPath=$lp.'/'.$new_name;
        //var_dump(rename($filename, $new_name));
        if(copy($localPath,$lp.'/'.$new_name)){
            if(!unlink($localPath)){
            return array("status" =>'error', "msg" => $lang['utdf']);
            }
            if(FTP_ENABLED){
                if (uploadFileInFtpServer($fileserver, $port, $ftpUser, $ftpPwd, $docPath, $newPath)) {
                $tempFileRemove[] = $server_path; //delete file from old storage after upload in workflow
                } else {
                return array("status" =>'error', "msg" => $lang['ftup'].'fdsfuhybrfd');
                }
            }
        }else{
            return array("status" =>'error', "msg" => $lang['file_cpy_error']);
        }
        
        $rev_res[doc_path]=$docPath;
     }    

     if($rev_res['metadata']!=""){

      $metadata = json_decode($rev_res['metadata']);
      $metasql = "";
      if(count($metadata)>0){

       foreach ($metadata as $key => $metavalue) {

        $metasql .=",".$key."="."'".$metavalue."'";


       }

      }

     }
    $in_sql="insert into tbl_document_master set 
                                                       doc_id='$rev_res[storage_doc_id]',
                                                       doc_name='$rev_res[doc_name]',
                                                       old_doc_name='$rev_res[storage_doc_name]',
                                                       doc_extn='$rev_res[doc_extn]',
                                                       doc_path='$rev_res[doc_path]',
                                                       uploaded_by='$rev_res[uploaded_by]',
                                                       doc_size='$rev_res[doc_size]',
                                                       noofpages='$rev_res[noofpages]',
                                                       dateposted='$rev_res[dateposted]',
                                                       File_Number='$rev_res[File_Number]',
                                                       doc_desc='$rev_res[doc_desc]',
                                                       filename='$rev_res[filename]',
                                                       flag_multidelete='$rev_res[flag_multidelete]'".$metasql."    
                                                      ";
    $in_query= mysqli_query($db_con, $in_sql);//or die(mysqli_error($db_con));
    if($in_query){
        // update Review log table
        $updateReview = mysqli_query($db_con, "update `tbl_reviews_log` set doc_id='$rev_res[storage_doc_id]',in_review='1' where doc_id=$revdoc_id");
        // update Annotation table
        $update_annot= mysqli_query($db_con, "update tbl_anotation set doc_id='$rev_res[storage_doc_id]',is_inreview='0' where doc_id='$revdoc_id' and is_inreview='1'");
        
       // delete record from review table.
       //for version document.
        $del_doc_name=$rev_res['doc_name']."_".$revdoc_id;
        $del_query= mysqli_query($db_con,"delete from tbl_document_reviewer where doc_id='$revdoc_id' or doc_name='$del_doc_name'");
        if($del_query){
            $delTicketID = mysqli_query($db_con, "Delete from `tbl_doc_review` where ticket_id='$reviewTicketId'");
            if($delTicketID){
                $status='success'; $msg=$lang['rev_file_send_to_stor_success']; 
                // Commit all queries.
                mysqli_commit($db_con); 
           }else{
               $status='error'; $msg=$lang['record_deletion_error']; 
           }
        }
        else{
          $status='error'; $msg=$lang['record_deletion_error'];   
        }
    }
    else{
        $status='error'; $msg=$lang['record_insertion_error']; 
    }
    }
    else{
        $status='error'; $msg=$lang['record_not_found']; 
    }
    return array("status"=>$status,"msg"=>$msg);
}

// upload to ftp;
function uploadFileInFtpServer($fileserver, $port, $ftpUser, $ftpPwd, $destinationPath, $sourcePath) {
if(FTP_ENABLED){
    require_once '../../classes/ftp.php';

    $ftp = new ftp();
    $ftp->conn("$fileserver", "$port", "$ftpUser", "$ftpPwd");

    if ($ftp->put(ROOT_FTP_FOLDER . '/' . $destinationPath, $sourcePath)) {
        //unlink($sourcePath);
        return TRUE;
    } else {
        return FALSE;
//         $arr = $ftp->getLogData();
//        if ($arr['error'] != ""){
//
//            echo '<h2>Error:</h2>' . implode('<br />', $arr['error']);
//        }
    }
 }
 else {
 return TRUE;    
 } 
}

