<?php
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
error_reporting(0);


require_once '../../sessionstart.php';
if (!isset($_SESSION['cdes_user_id'])) {
    header("location:../../logout.php");
}
require './../config/database.php';
require 'fpdf/fpdf.php';
require 'FPDI/fpdi.php';
require_once '../../classes/ftp.php';

// if(!isset($_POST['token'])){
//    echo "Unauthrized Access";  
// }


if($_POST['action']=="getPdfPageNo"){


	getPdfPageNo();

}else if($_POST['action']=="saveNewPdf1"){

	saveNewPdf($db_con, $fileserver, $port, $ftpUser, $ftpPwd);
}


function getPdfPageNo(){

	$pdf = new FPDI();

	$filepath = $_POST['filepath'];

	$pagecount = $pdf->setSourceFile($filepath);

	$options = '<option value="">Select Page No.</option>';

	if($pagecount>0){

		for($i=1; $i<=$pagecount; $i++){

			$options .= '<option value="'.$i.'">'.$i.'</option>';
		}
	}

	echo $options;
}

function saveNewPdf($db_con, $fileserver, $port, $ftpUser, $ftpPwd){

	$filename = $_POST['filename'];
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


		$document  = mysqli_query($db_con, "Select * from tbl_document_master where doc_id = '$doc_id'") or die('Error:' . mysqli_error($db_con));


		if(mysqli_num_rows($document)>0){

			$rowD = mysqli_fetch_assoc($document);

			$slid =   reset(explode("_", $rowD['doc_name']));

			$old_doc_name =  $rowD['old_doc_name'];
			

			$storage = mysqli_query($db_con, "select sl_name from tbl_storage_level where sl_id='$slid'") or die('Error');
			$rwStor = mysqli_fetch_assoc($storage);

			$storageName = $rwStor['sl_name'];

			$file = substr( strrchr( $filepath, "/" ), 1); 

			$dir = str_replace( $file, '', $filepath );

		    $storageName = str_replace(" ", "", $storageName);
		    $storageName = preg_replace('/[^A-Za-z0-9\-_]/', '', $storageName);
		    $uploaddir = "../../extract-here/" . $storageName . '/';
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



			$split_pdf = split_pdf($dir, $filepath, $filename, $pagenof, $pagenot, $newfilepath);



			$filesize = filesize($newfilepath);

			if (FTP_ENABLED) {

	            $ftp = new ftp();

	            $ftp->conn("$fileserver", "$port", "$ftpUser", "$ftpPwd");

	            $uploadfile = $ftp->put(ROOT_FTP_FOLDER . '/' . $storageName . '/' . $filenameEnct, $newfilepath);

	            //$arr = $ftp->getLogData();

	            if ($uploadfile) {

	            	unlink($newfilepath);

	                $uploadInToFTP = true;
	            } else {

	                $uploadInToFTP = false;

	                echo '<h2>Error:</h2>' . implode('<br />', $arr['error']);
	            }
	        } else {
	            $uploadInToFTP = true;
	        }


	        if($uploadInToFTP){

	        	//secho "success";

	        	$updateDocName = $slid;
	        	$filename = $filename.'.pdf';
	        	$doc_path = $storageName . '/' . $filenameEnct;

	        	$user_id = $_SESSION['cdes_user_id'];

	        	$numfPage = $pagenot-$pagenof+1;
	        	$date = date('Y-m-d H:i:s');

	        	$createVrsn = mysqli_query($db_con, "INSERT INTO tbl_document_master(doc_name, old_doc_name, doc_extn, doc_path, doc_size, uploaded_by, noofpages, dateposted) VALUES ('$updateDocName', '$filename', 'pdf', '$doc_path', '$filesize', '$user_id', '$numfPage', '$date')"); //or die('Error:' . mysqli_error($db_con));

	        	if($createVrsn){

	        		$msg['status']="success";
	        		$msg['storageid']="".base64_decode(urldecode($slid))."";
	        		$msg['msg']="File split successfully";

	        	}else{

	        		$msg['status']="fail";
	        		$msg['msg']="File couldn't save";
	        	}

	        }else{
	        		$msg['status']="fail";
	        		$msg['msg']="File failed to upload";
	        		
	        }


		}
	}

	echo json_encode($msg);
	
}



function split_pdf($end_directory, $filepath, $filename, $pageno_from, $pageno_to, $newfilepath)
{

	
	$pdf = new FPDI();

	//$pagecount = $pdf->setSourceFile($filepath); // How many pages?

	$newfiles=[];
	// Split each page into a new PDF
	for ($i = $pageno_from; $i <=$pageno_to; $i++) {

		$new_pdf = new FPDI();
		$new_pdf->AddPage();
		$new_pdf->setSourceFile($filepath);

		$new_pdf->useTemplate($new_pdf->importPage($i));
		
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


	foreach ($newfiles as $key => $filename) {
		//echo "k";
		if(FTP_ENABLED){
		unlink($filename);
		}
	}


}


?>