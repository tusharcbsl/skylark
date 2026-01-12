<?php
//error_reporting(E_ALL);
require_once 'application/pages/function.php';
require_once 'application/pages/function.php';
require_once './classes/fileManager.php';

error_reporting(0);
function saveNewPdf($db_con){
$fileManager = new fileManager();
	$filename = str_replace(" ", "_", $_POST['filename']);

	$filename = preg_replace("/[^a-zA-Z0-9_ ]/", "", $filename);

	$pagenof = $_POST['pagenof'];
	$pagenot = $_POST['pagenot'];
	$doc_id = $_POST['doc_id'];
	$filepath = $_POST['filepath'];

	

	if($filename==""){

		$msg['status']="fail";
		$msg['msg']="Please enter file name.";

	}else if($pagenof==""){

		$msg['status']="fail";
		$msg['msg']="Please select from page no.";

	}else if($pagenot==""){

		$msg['status']="fail";
		$msg['msg']="Please select to page no.";

	}else if($pagenof>$pagenot){

		$msg['status']="fail";
		$msg['msg']="Page range must be correct.";

	}else{

		$filenamewithext = $filename.'.pdf';

		$checkfile  = mysqli_query($db_con, "Select doc_id from tbl_document_master where old_doc_name = '$filename' or old_doc_name='$filenamewithext'") or die('Error:' . mysqli_error($db_con));

		if(mysqli_num_rows($checkfile)==0){


			$document  = mysqli_query($db_con, "Select * from tbl_document_master where doc_id = '$doc_id'") or die('Error:' . mysqli_error($db_con));


			if(mysqli_num_rows($document)>0){

				$rowD = mysqli_fetch_assoc($document);
				
				

				//$slid =   reset(explode("_", $rowD['doc_name']));

				$slid = $rowD['doc_name'];

				$old_doc_name =  $rowD['old_doc_name'];
				$storage = mysqli_query($db_con, "select sl_name, sl_parent_id, sl_depth_level from tbl_storage_level where sl_id='$slid'") or die('Error');
				$rwStor = mysqli_fetch_assoc($storage);

				$storageName = $rwStor['sl_name'];

				$file = substr( strrchr( $filepath, "/" ), 1); 

				$dir = str_replace( $file, '', $filepath );

			    $storageName = str_replace(" ", "", $storageName);
			    $storageName = preg_replace('/[^A-Za-z0-9\-_]/', '', $storageName);
                            
				$updir = getStoragePath($db_con, $rwStor['sl_parent_id'], $rwStor['sl_depth_level']);
				if(!empty($updir)){
					$updir = $updir . '/';
				}else{
					$updir = '';
				}
				$uploaddir = 'extract-here/'.$updir.$storageName.'/';
                            
			    if (!is_dir($uploaddir)) {
			        mkdir($uploaddir, 0777, TRUE) or die(print_r(error_get_last()));
			    }
			    $fname = preg_replace('/[^A-Za-z0-9_\-@]/', '', $filename);
			    // $filenameEnct=$fname.'.'.$extn;// urlencode(base64_encode($fname)).'.'.$extn;
			    $filenameEnct = urlencode(base64_encode($fname));
			    $filenameEnct = preg_replace('/[^A-Za-z0-9_\-@&]/', '', $filenameEnct);
			    $filenameEnct = $filenameEnct . '.pdf';

			    $filenameEnct = time() . $filenameEnct;

			    $newfilepath = $uploaddir.$filenameEnct;
			    

			    $newfilepath1 = $uploaddir.$filenameEnct;

			    

				$split_pdf = split_pdf($dir, $filepath, $filename, $pagenof, $pagenot, $newfilepath);


				//$newfilepath = $uploaddir.'sp'.$filenameEnct;
				$newfilepath = $uploaddir.$filenameEnct;


				$command ="C:\gs9.25\bin\gswin64.exe -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dPDFSETTINGS=/printer -dNOPAUSE -dQUIET -dBATCH -sOutputFile=".$newfilepath. " ".$newfilepath1." 2>&1";
//print_r(error_get_last());

				exec($command, $error, $exit_code);

				//print_r($error);


				$filesize = filesize($newfilepath);
				
				$fileManager->conntFileServer();
				$uploadInToFTP = $fileManager->uploadFile($newfilepath, ROOT_FTP_FOLDER . '/' .$updir. $storageName . '/' . $filenameEnct);

		        //if($uploadInToFTP){

		        	//secho "success";

		        	$updateDocName = $slid;
		        	$filename = $filename.'.pdf';
		        	$doc_path = $updir.$storageName . '/' . $filenameEnct;

		        	$user_id = $_SESSION['cdes_user_id'];

		        	$numfPage = $pagenot-$pagenof+1;
		        	$date = date('Y-m-d H:i:s');

		        	//$createVrsn = mysqli_query($db_con, "INSERT INTO tbl_document_master(doc_name, old_doc_name, doc_extn, doc_path, doc_size, uploaded_by, noofpages, dateposted, parent_doc_id) VALUES ('$updateDocName', '$filename', 'pdf', '$doc_path', '$filesize', '$user_id', '$numfPage', '$date', '$doc_id')"); //or die('Error:' . mysqli_error($db_con));

		        	$sql2 = "INSERT INTO tbl_document_master SET";
                                $sql2 .= " doc_name='$updateDocName',old_doc_name='$filename',doc_extn='pdf',doc_path='$doc_path',uploaded_by='$user_id',doc_size='$filesize',dateposted='$date',noofpages='$numfPage', parent_doc_id='$doc_id'";

                                 $meta = mysqli_query($db_con, "select * from tbl_metadata_to_storagelevel where sl_id='$slid'");

		        	while ($rwMeta = mysqli_fetch_assoc($meta)) {
                                    $metan = mysqli_query($db_con, "select field_name from tbl_metadata_master where id='$rwMeta[metadata_id]'");
                                    $rwMetan = mysqli_fetch_assoc($metan);
                                    $field = $rwMetan['field_name'];
                                    $value = $rowD[$field];
                                    $sql2 .= ",`$field`='$value'";
                                }
                
                                $multicopyinsert = mysqli_query($db_con, $sql2)or die("Error copy" . mysqli_error($db_con));
                                
		        	if($multicopyinsert){

			        		$insertId = mysqli_insert_id($db_con);

			        		$newdocname = base64_encode($insertId);
                    
	                    //create thumbnail
	                    //$uploadedfilename = $uploaddir . $filenameEnct;

	                    
	                        changePdfToImage($newfilepath,$newdocname);

	                        /* if (FTP_ENABLED) {
	                        	unlink($newfilepath);
	                        } */
	                    

			        		$log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `sl_id`, `doc_id`, `user_name`, `action_name`, `start_date`, `system_ip`, `remarks`) values ('$_SESSION[cdes_user_id]', '$slid', '$insertId', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]','PDF Split','$date', '$host','Split PDF $filename from $old_doc_name')") or die('error :' . mysqli_error($db_con));

			        		$msg['status']="success";
			        		//$msg['storageid']="".base64_decode(urldecode($slid))."";
			        		$msg['msg']="File split successfully";

			        	}else{

			        		$msg['status']="fail";
			        		$msg['msg']="File couldn't save";
			        		

			        	}

			        // }else{
			        // 		$msg['status']="fail";
			        // 		$msg['msg']="File failed to upload";
			        		
			        // }

			}
		}
		else{

			$msg['status']="fail";
    		$msg['msg']="Filename already exist.";
		}
	}

	if($msg['status']=="success"){

		//echo "mmmmmmm";

		echo '<script>taskSuccess("storageFiles?id=' . base64_encode(urlencode($slid)) . '", "' . $msg['msg'] . '");</script>';
	}else{

		//echo "kkkkkkkkkkkkk";

		echo '<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '", "' . $msg['msg'] . '")</script>';
	}
	
}



function split_pdf($end_directory, $filepath, $filename, $pageno_from, $pageno_to, $newfilepath)
{

	$pdf = new FPDI();

	$newfiles=[];
	// Split each page into a new PDF
	for ($i = $pageno_from; $i <=$pageno_to; $i++) {

		$new_pdf = new FPDI();

		$new_pdf->setSourceFile($filepath);

		$tppl =  $new_pdf->importPage($i);

		$size = $new_pdf->getTemplateSize($tppl);

		$w = $size['w'];
        $h = $size['h'];

		//print_r($size);

		$o = ($h > $w) ? 'P' : 'L';

		$new_pdf->AddPage($o, array($w, $h));

		$new_pdf->useTemplate($tppl, 0, 0, $w, $h);
		
		try {

			$new_filename = $end_directory.str_replace('.pdf', '', $filename).'_'.$i.".pdf";

			$newfiles[] = $new_filename;

		} catch (Exception $e) {
			echo 'Caught exception: ',  $e->getMessage(), "\n";
		}

		$new_pdf->Output($new_filename, "F");
	}

	

	mergePDF($newfiles, $newfilepath, $end_directory);
	
}


function mergePDF($newfiles, $newfilepath, $end_directory){


	$pdf = new FPDI();
	
	//$files =['split/mypdf_1.pdf', 'split/mypdf_2.pdf'];
	$new_pdf = new FPDI();
	$new_pdf->SetCompression(true);
	foreach ($newfiles as $key => $filename) {

		$pagecount = $pdf->setSourceFile($filename); // How many pages?

		// Split each page into a new PDF
		for ($i = 1; $i <= $pagecount; $i++) {
			
			$new_pdf->AddPage();
			$new_pdf->setSourceFile($filename);
			$pageId = $new_pdf->importPage($i, '/MediaBox');
			$new_pdf->useTemplate($pageId, 10, 10, 200); 
			
			try {

				$new_filename = $end_directory.str_replace('.pdf', '', $filename).'_'.$i.".pdf";

			} catch (Exception $e) {
				echo 'Caught exception: ',  $e->getMessage(), "\n";
			}

		}
	}

	$new_filename = $newfilepath;

	$new_pdf->Output($new_filename, "F");

	gc_collect_cycles();
	foreach ($newfiles as $key => $filename) {
		//echo "k";
		unlink($filename);
	}


}


?>