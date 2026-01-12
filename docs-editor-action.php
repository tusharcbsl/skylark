<?php
$fileId=base64_decode(urldecode($_GET['dcid']));
$perm=base64_decode(urldecode($_GET['perm']));
$fileName = $meta_row['old_doc_name'];

$url = "http://royankit.ezeepea.com/docs-edit.php";
// $url = BASE_URL."docs-edit.php";
// $url = "https://sargroup.ezeeoffice.co.in/docs-edit.php";

$headers = array("Content-Type:multipart/form-data"); // cURL headers for file uploading
$ch = curl_init();
/*$file = new CURLFile($localPath
//'Input_Image/test1.jpg','image/jpeg','test.jpg'
//        $_FILES['file']['tmp_name'],
//        $_FILES['file']['type'],
//        $_FILES['file']['name']
);*/
$postfields = array('fileId'=>$fileId,'perm'=>$perm,'fileName'=>$fileName);

$options = array(
    CURLOPT_URL => $url,
    //CURLOPT_HEADER => true,
    CURLOPT_POST => 1,
    CURLOPT_SSL_VERIFYHOST => 0,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_HTTPHEADER => $headers,
    CURLOPT_POSTFIELDS => $postfields,
    CURLOPT_RETURNTRANSFER => true

); // cURL options
curl_setopt_array($ch, $options);
$result=curl_exec($ch);



$newfile = preg_replace('/\s+/', '', $result);

$newfile_cont = file_get_contents($newfile);
$oldfile = "extract-here/" . $meta_row['doc_path'];

$oldfile_cont = file_get_contents($oldfile);

if($newfile_cont != $oldfile_cont){
	
	
    
    /*unlink($oldfile);
    copy($newfile, $oldfile);*/

    //create version
    $docName = $meta_row['doc_name'];
    $doc_id = $_POST['docid'];
    $old_file_name = $meta_row['old_doc_name'];
    $oldextn = substr($old_file_name, strrpos($old_file_name, '.') + 1); // old file extn
    $oldfname = substr($old_file_name, 0, strrpos($old_file_name, '.')); // old file name
    $file_name = $old_file_name;    
    $imgs=explode(".",$old_file_name);
    $fname=$imgs[0];
    $doc_extn=end($imgs);
    $extn = substr($file_name, strrpos($file_name, '.') + 1);
    $fname = substr($file_name, 0, strrpos($file_name, '.'));
    $fileExtn = substr($file_name, strrpos($file_name, ".") + 1);

    $updateDocName = $docName . '_' . $doc_id; //storage id followed by doc id
    $chekFileVersion = mysqli_query($db_con, "SELECT * FROM `tbl_document_master` WHERE find_in_set('$updateDocName', doc_name)"); //or die('Errorv2:' . mysqli_error($db_con));
    $flVersion = mysqli_num_rows($chekFileVersion);
    $flVersion = $flVersion + 1;
    $nfilename = $oldfname . '_' . $flVersion;
    $strgName = mysqli_query($db_con, "select * from tbl_storage_level where sl_id = '$docName'");  //or die('Errorv3:' . mysqli_error($db_con));
    $rwstrgName = mysqli_fetch_assoc($strgName);
    $storageName = $rwstrgName['sl_name'];
    $storageName = str_replace(" ", "", $storageName);
    $storageName = preg_replace('/[^A-Za-z0-9\-]/', '', $storageName);
    $uploaddir = "extract-here/" . $storageName . '/';
    if (!is_dir($uploaddir)) {
        mkdir($uploaddir, 0777, TRUE) or die(print_r(error_get_last()));
    }
    $nfilename = preg_replace('/[^A-Za-z0-9_\-]/', '', $nfilename);
    $filenameEnct = urlencode(base64_encode($nfilename));
    $filenameEnct = preg_replace('/[^A-Za-z0-9_\-]/', '', $filenameEnct);
    $filenameEnct = $filenameEnct . '.' . $extn;
    $filenameEnct = time() . $filenameEnct;

    $uploaddir = $uploaddir . $filenameEnct;   
    
    $upload=false;
    //copy file from temp google drive file
    if(copy($newfile, $uploaddir)){
        $upload=true;
    }

    /*$handle = fopen($uploaddir, "w+"); // Modified
    
    while (!$content->getBody()->eof()) { // Modified
        fwrite($handle, $content->getBody()->read(1024)); // Modified
    }
    
    fclose($handle);*/
    $file_size=filesize($uploaddir);       

    $uploadInToFTP = false;
    $unlink_dir=false;
    // encypt file
    // encrypt_my_file($uploaddir);
	

    if ($upload) {

		$fileManager = new fileManager();
		$fileManager->conntFileServer();
		$filepath = $storageName . '/' . $filenameEnct;
		$uploadInToFTP = $fileManager->uploadFile($uploaddir, ROOT_FTP_FOLDER . '/' . $filepath, false);

    }


	
    if ($uploadInToFTP) { 
	
	echo "asdfsadfdsafasdf";
        // decrypt_my_file($uploaddir);
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
        $createVrsn = mysqli_query($db_con, "INSERT INTO tbl_document_master($cols) select $cols from tbl_document_master where doc_id='$doc_id'") or die('Errorv4:' . mysqli_error($db_con));
        $insertDocID = mysqli_insert_id($db_con);

        $olddocname = base64_encode($insertDocID);
        
        //rename old thumbnail
        // rename('thumbnail/'.base64_encode($doc_id).'.jpg', 'thumbnail/'.$olddocname.'.jpg');
        //create thumbnail
        /*$newdocname = base64_encode($doc_id);
        if($extn=='jpg' || $extn=='jpeg' || $extn=='png'){
            createThumbnail2($uploaddir,$newdocname);
        }elseif($extn=='pdf'){
            changePdfToImage($uploaddir,$newdocname);
        }*/
        if($unlink_dir){
            unlink($uploaddir);
        }
        $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `doc_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,'$doc_id','Versioning Document $file_name Added','$_POST[start_date]','$date','$host',null)");
        if ($createVrsn) {
            $updateNew = mysqli_query($db_con, "update tbl_document_master set doc_name='$updateDocName', checkin_checkout='1',gdocid='' where doc_id='$insertDocID'");            
        }
        $updateOld = mysqli_query($db_con, "update tbl_document_master set old_doc_name='$file_name',filename='$fname', doc_extn='$extn', doc_path='$storageName/$filenameEnct', uploaded_by='$_SESSION[cdes_user_id]', doc_size='$file_size', noofpages='1', dateposted='$date' where doc_id='$doc_id'");
    }
    /*try {
        $qry = mysqli_query($db_con, "update tbl_document_master set `gdocid`='' where gdocid='$fileId'")or die('Error alter column:' . mysqli_error($db_con));
        // $service->files->delete($fileId);
    } catch (Exception $e) {
      print "An error occurred: " . $e->getMessage();
    }*/

}
?>