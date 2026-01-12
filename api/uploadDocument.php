<?php
require'connection.php';
require_once 'classes/function.php';




/*print_r($_POST['metadata']);
print_r($_POST['pagecount']);
print_r($_FILES['file']);
print_r($_POST['userid']);
print_r($_POST['slid']);
print_r($_POST['username']);
print_r($_POST['ip']);*/

if(isset($_POST['metadata'])&&!empty($_POST['metadata'])
   &&isset($_POST['pagecount'])&&!empty($_POST['pagecount'])
   &&isset($_FILES['file'])&&!empty($_FILES['file'])
   &&isset($_POST['userid'])&&!empty($_POST['userid'])
   &&isset($_POST['slid'])&&!empty($_POST['slid'])
   &&isset($_POST['username'])&&!empty($_POST['username'])
   &&isset($_POST['ip'])&&!empty($_POST['ip']))
  
{
	    $id = $_POST['slid'];
        $username = $_POST['username'];
        $host = $_POST['ip'];
	
	    date_default_timezone_set("Asia/Kolkata");
	$date = date("Y-m-d H:i");
  
 //  print_r($id);
 // die;
        
        
        $file_name = $_FILES['file']['name'];
        $file_size = $_FILES['file']['size'];
        $file_type = $_FILES['file']['type'];
        $file_tmp = $_FILES['file']['tmp_name'];
        $pageCount = $_POST['pagecount'];
        
	 $extn = substr($file_name, strrpos($file_name, '.') + 1);
        $fname = substr($file_name, 0, strrpos($file_name, '.'));
  
       $metadata=json_decode($_POST['metadata'],true);


//echo count($data['meta']);
	
//die;

	
	
 $metavals = '';
        $columns = '';
	
	
    
	$m = array();

	

	
	for($i= 0;$i<count($metadata['meta']);$i++){

  $metalabel =$metadata['meta'][$i]['metaLabel'] ;
  $metavalue = $metadata['meta'][$i]['metaEntered'];  
  	
	
  if(!empty($metavalue)){
  
	//$metavals = $metavalue . "," . $metalabel . "";
	  
	  array_push($m,$metavalue);

	  
	  
  }	
	 else{
		 
		  array_push($m,'null');
			
			//$metavals = ",$metalabel";
	
			
	 }
  //echo $metalabel." ".$metavalue."\n" ;
 // echo $metavals;
	
	

}
   // echo $metavals;
	//print_r($m);
	
	//die;
	
	//$m = implode('", "',$m);
	$m = "'" . implode ( "', '", $m ) . "'";
	
	
	
	//print_r($m);
    
	//die;
   
    $mata = "SELECT tmm.field_name FROM tbl_metadata_to_storagelevel tms INNER JOIN tbl_metadata_master tmm  ON tms.metadata_id = tmm.id where tms.sl_id='$id'";
    $meta_run = mysqli_query($con, $mata);
            $i = 1;
            while ($rwmeta = mysqli_fetch_assoc($meta_run)) {
                if (!empty($columns)) {
                    $columns = $columns . ',' . $rwmeta['field_name'] . '';
                } else {
                    $columns = ',' . $rwmeta['field_name'] . '';
                }
                //$colval.$i=$_POST[''];
                $i++;
            }
        }

	

        $user_id = $_POST['userid'];
        $name = explode(".", $file_name);
        $encryptName = urlencode(base64_encode($name[0]));
        $fileExtn = $name[1];


        $strgName = mysqli_query($con, "select * from tbl_storage_level where sl_id = '$id'") or die('Error:' . mysqli_error($con));
        $rwstrgName = mysqli_fetch_assoc($strgName);
        $storageName = $rwstrgName['sl_name'];
        $storageName = str_replace(" ", "", $storageName);
        $storageName = preg_replace('/[^A-Za-z0-9\-]/', '', $storageName);
        $uploaddir = "../extract-here/" . $storageName . '/';
        if (!is_dir($uploaddir)) {
			
            mkdir($uploaddir, 0777, TRUE) or die(print_r(error_get_last()));
        }

        $fname = preg_replace('/[^A-Za-z0-9_\-]/', '', $fname);
        // $filenameEnct=$fname.'.'.$extn;// urlencode(base64_encode($fname)).'.'.$extn;
        $filenameEnct = urlencode(base64_encode($fname));
        $filenameEnct = preg_replace('/[^A-Za-z0-9_\-]/', '', $filenameEnct);
        $filenameEnct = $filenameEnct . '.' . $extn;
        $filenameEnct = time() . $filenameEnct;

        // $upload = move_uploaded_file($file_tmp, $image_path) or die(print_r(error_get_last()));
        $upload = move_uploaded_file($file_tmp, $uploaddir . $filenameEnct) or die('Error' . print_r(error_get_last()));
        if ($upload) {
			
			
			$destinationPath =$storageName.'/'.$filenameEnct;
			$sourcePath = $uploaddir.$filenameEnct; 
			 if(uploadFileInFtpServer($destinationPath, $sourcePath)){
				 
				unlink($sourcePath);
			}
			else
			{	
				$temp = array();
				$temp['error'] = 'false';
				$temp['msg'] = 'File upload failed'; 
				echo json_encode($temp); 
				exit();
			}
			
			if(empty($metadata)||count($metadata['meta'])==0){
				
				
			  $query = "INSERT INTO tbl_document_master(doc_name, old_doc_name, doc_extn, doc_path, uploaded_by, doc_size, noofpages , dateposted) VALUES ('$id', '$file_name', '$fileExtn', '$storageName/$filenameEnct', '$user_id', '$file_size', '$pageCount','$date')" or die('Eror:' . mysqli_error($con));
			
			}
			else{
			
			   
				
			  $query = "INSERT INTO tbl_document_master(doc_name, old_doc_name, doc_extn, doc_path, uploaded_by, doc_size, noofpages $columns, dateposted) VALUES ('$id', '$file_name', '$fileExtn', '$storageName/$filenameEnct', '$user_id', '$file_size', '$pageCount',$m,'$date')" or die('Eror:' . mysqli_error($con));
			
			}
          
           
			//echo $query;
			//die;
			$exe = mysqli_query($con, $query) or die('Error n' . mysqli_error($con));
            $doc_id = mysqli_insert_id($con);
      
            if ($exe) {
                $log = mysqli_query($con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `doc_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$user_id', '$username',null,'$id','$doc_id','Document $file_name Uploaded in $storageName','$date',null,'$host',null)") or die('error : ' . mysqli_error($con));
              
                $res = array();
                $res['message'] = 'File Uploaded Successfully!!';
                $res['error'] = 'false';
                echo json_encode($res);
              
              
              //echo '<script>uploadSuccess("storage?id=' . $_GET['id'] . '", "File Uploaded Successfully!!");</script>';
            } else {
              
                $res = array();
                $res['message'] = 'Opps!! File upload failed';
                $res['error'] = 'true';
                echo json_encode($res);
               // echo '<script>taskFailed("storage?id=' . $_GET['id'] . '", "Opps!! File upload failed")</script>';
            }
        }

    else{
	
	
	   $res = array();
                $res['message'] = 'Opps!! File upload failed';
                $res['error'] = 'true';
                echo json_encode($res);
	
	}




?>

