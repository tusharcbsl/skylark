<?php
require_once './loginvalidate.php';
require_once './application/pages/function.php';
require_once './classes/fileManager.php';
$fileManager = new fileManager();


if (isset($_POST['bulkUpload'], $_POST['token'])) {
	
    $flag = 1;
    $message = array();
    $message1 = array();
    $fpathwithname = array();
    $fpath = array();
    $fdocid = array();
    $csvids = array();
    $sourcePath=[];
    $destinationPath=[];
    $slID = mysqli_real_escape_string($db_con, $_POST['storage']);
    mysqli_set_charset($db_con, "utf8");
    $sl = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$slID'");
    $rwSl = mysqli_fetch_assoc($sl);
    $storageName = $rwSl['sl_name'];
    $storageName = str_replace(" ", "", $storageName);
    $storageName = preg_replace('/[^A-Za-z0-9\-]/', '', $storageName);
    $upload = 0;
    $uploadFileCount = 0;
	
    // make upload path based on storage depth level
    $updir = getStoragePath($db_con, $rwSl['sl_parent_id'], $rwSl['sl_depth_level']);
    if(!empty($updir)){
        $updir = $updir . '/';
    }else{
        $updir = '';
    }
    $uploaddir = $updir.$storageName.'/';
    $target_path = 'extract-here/'.$uploaddir;
    if (!is_dir("uploadLogs")) {
        mkdir("uploadLogs", 0777, true);
    }


    $logs = fopen('uploadLogs/' . date('Ymdhis') . '.dat', "a");
	
    for ($i = 0; $i < count($_FILES["zip_upload"]["name"]); $i++) {

        $filename = $_FILES["zip_upload"]["name"][$i];
        $source = $_FILES["zip_upload"]["tmp_name"][$i];
        $type = $_FILES["zip_upload"]["type"][$i];
        $size = $_FILES["zip_upload"]["size"][$i];

        $extn = substr($filename, strrpos($filename, '.') + 1);

        $fname = substr($filename, 0, strrpos($filename, '.'));
        
        if ($extn == "csv") {
            // select all column from document table 
            $columns = mysqli_query($db_con, "SHOW COLUMNS FROM `tbl_document_master`");
            while ($rwCol = mysqli_fetch_array($columns)) {
                $col[] = $rwCol['Field'];
            }
            $row = 1;
            $csv = array_map('str_getcsv', file($source)); // get data into array from csv
            // echo '<pre>';
            // print_r($csv);
            // echo '</pre>';
            $header = $csv[0]; // get header of csv
            array_walk($csv, function(&$a) use ($csv) { // key value conversion
                $a = array_combine($csv[0], $a);
            });
            array_shift($csv); # remove column header
            for ($c = 0; $c < count($header); $c++) {
                $metaName = preg_replace("/[^A-Za-z0-9\-_]/", "", $header[$c]);
                if (!in_array($metaName, $col)) {
                    $flag = 0;
                    $message[] = "column $metaName not found";
                    fwrite($logs, '<p class="text-danger">' . $i . '. ' . $date . " : column $metaName not found\n</p>");
                    break;
                }
            }
        }
    }

    // echo '<pre>';
    // print_r($csv);
    // echo '</pre>';

	// exit;



    $countf = count($_FILES["zip_upload"]["name"]);
    if ($flag == 1) {
        for ($i = 0; $i < $countf; $i++) {
            // echo 'okaaa';
            // exit;
            $filename = $_FILES["zip_upload"]["name"][$i];
            $source = $_FILES["zip_upload"]["tmp_name"][$i];
            $type = $_FILES["zip_upload"]["type"][$i];
            $size = $_FILES["zip_upload"]["size"][$i];
            $extn = substr($filename, strrpos($filename, '.') + 1);
            $fname = substr($filename, 0, strrpos($filename, '.'));
            // echo 'ok'; die;   
            $name = explode(".", $filename);
			
            $accepted_types = ALLOWED_EXTN;
            $ext = pathinfo($filename, PATHINFO_EXTENSION);
            // echo 'tshg';
            // exit;
            if ($extn == "csv") {
                $okay = FALSE;
            } else if (in_array($ext, $accepted_types)) {
                // echo 'tshg';
                // exit;
                $okay = true;
            } else {
                $message[] = "$filename file extention not allowed to upload";
                $okay = false;
            }


            if ($okay) {
                if (!dir($target_path)) {
                    mkdir($target_path, 0777, TRUE); // or die("Error local folder:". print_r(error_get_last()));
                }


                $fname1 = preg_replace('/[^A-Za-z0-9_\-]/', '', $fname);
                $filenameEnct = urlencode(base64_encode($fname1));
                $filenameEnct = preg_replace('/[^A-Za-z0-9_\-]/', '', $filenameEnct) . '.' . $extn;
                $fileUpload = move_uploaded_file($source, $target_path . $filenameEnct) or die('File Not Uploaded' . print_r(error_get_last()));
                //die($uploaddir.'/'.$filenameEnct);
                $uploadedFTP = true;
                if ($fileUpload) {


                    $sourcePath[] = $target_path . $filenameEnct;
                    $destinationPath[] = ROOT_FTP_FOLDER.'/'.$uploaddir.$filenameEnct;

                    if ($extn == "pdf") {
                        $noofPages = count_pages($target_path . $filenameEnct);
                        // echo 'okkkk';
                        // exit;
                    } elseif ($extn == "docx") {
                        $noofPages = PageCount_DOCX($target_path . $filenameEnct);
                    } else {
                        $noofPages = 1;
                    }

                    $metavalues = '';
                    mysqli_set_charset($db_con, "utf8");
					// Check is file already exit
                    $check = mysqli_query($db_con, "select doc_id from tbl_document_master where filename='$fname' and doc_name='$slID' and flag_multidelete=1");
                    $rwCheck = mysqli_fetch_assoc($check);
                    if (mysqli_num_rows($check) > 0) {
                        fwrite($logs, '<p class="text-danger">' . $i . '. ' . $date . " : $filename : already exist in $storageName storage.\n</p>");
                        $message[] = "$filename already exist in $storageName storage";
                        $metakey = search_exif($csv, $fname);
                        unset($csv[$metakey]);
                    } else {
						// Check uploaded file is exit in csv or not
                        $metakey = search_exif($csv, $fname);
                        $metavalues = $csv[$metakey];
                        unset($csv[$metakey]);
                        if (!empty($metavalues)) {
                            $querystr = '';
                            foreach ($metavalues as $key => $values) {
                                if (empty($querystr)) {
                                    $querystr = "`$key`='" . mysqli_escape_string($db_con, $values) . "'";
                                } else {
                                    $querystr = "$querystr,`$key`='" . mysqli_escape_string($db_con, $values) . "'";
                                }
                            }
                            if (!array_key_exists('noofpages', $metavalues)) {
								if(!empty($querystr)){
									$querystr = "$querystr,`noofpages`='$noofPages'";
								}else{
									$querystr = "`noofpages`='$noofPages'";
								}
                            }
                            $fileNameExtn = $fname . '.' . $extn;
                            mysqli_set_charset($db_con, "utf8");
                            $docpath = $uploaddir.$filenameEnct;
							// insert document record here
							$query = "insert into tbl_document_master set doc_name='$slID',old_doc_name='$fileNameExtn',doc_extn='$extn',doc_path='$docpath',uploaded_by='$_SESSION[cdes_user_id]',doc_size='$size',dateposted='$date'" . (!empty($querystr) ? ',' . $querystr : '');
                            $fileins = mysqli_query($db_con, $query) or die('Error' . mysqli_error($db_con));
                            $upload = 1;
                            $doc_id = mysqli_insert_id($db_con);


                            $doc_ids[] = $doc_id;
							
                            $newdocname = base64_encode($doc_id);
                            //create thumbnail
                            $uploadedfilename = $target_path . $filenameEnct;
                            if($extn=='jpg' || $extn=='jpeg' || $extn=='png') {
                                createThumbnail2($uploadedfilename,$newdocname);
                            }elseif($extn=='pdf'){
                                changePdfToImage($uploadedfilename,$newdocname);
                            }
							
                            $img_array = array('pdf', 'jpg', 'jpeg', 'png', 'bmp', 'pnm', 'jfif', 'jpeg', 'tiff');

                            $txtpath = $target_path. '/TXT/';
                            if (!is_dir($txtpath)) {
                                mkdir($txtpath, 0777, TRUE) or die(print_r(error_get_last()));
                            }
							// get text from files
                            $extractHereDirfile = $target_path . $filenameEnct;
                            if (strtolower($extn) == "doc") {
                                $docText = read_doc($extractHereDirfile);
                            } elseif (strtolower($extn) == "docx") {
                                $docText = read_docx($extractHereDirfile);
                            } elseif (strtolower($extn) == "xlsx") {
                                $docText = xlsx_to_text($extractHereDirfile);
                            } elseif (strtolower($extn) == "xls") {
                                $docText = xls_to_txt($extractHereDirfile);
                            } elseif (strtolower($extn) == "pptx" || strtolower($extn) == "ppt") {
                                $docText = pptx_to_text($extractHereDirfile);
                            } else if(strtolower($extn) == "txt" || strtolower($extn) == "text"){
                                $docText = txt_to_text($extractHereDirfile);
                            }else if (in_array(strtolower($extn), $img_array)) {
                                $fpathwithname[] = $target_path . $filenameEnct;
                                $fpath[] = $target_path;
                                $fdocid[] = $doc_id;
                                if (!empty($metavalues['noofpages'])) {
                                    $pCount[] = $metavalues['noofpages'];
                                } else {
                                    $pCount[] = $noofPages;
                                }
                            }


                            $message1[] = "$filename uploaded successfully";

                            fwrite($logs, '<p class="text-success">' . $i . '. ' . $date . " : $filename : uploaded successfully.\n</p>");
                            mysqli_set_charset($db_con, "utf8");
							// insert uploaded file log
                            $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,'$slID','Document $filename uploaded in $storageName','$date',null,'$host',null)") or die('error : ' . mysqli_error($db_con));
                        } else {
                            $message[] = "$filename not found in csv.";
                            unlink($target_path . $filenameEnct);
                            fwrite($logs, '<p class="text-danger">' . $i . '. ' . $date . " : $filename : not found in csv.\n</p>");
                        }
                    }
                } else {
                    $message[] = "failed to upload - $filename";
                }
            }
        }
        foreach ($csv as $data) {
            $message[] = $data['filename'] . " found in csv. file not found.";
            fwrite($logs, '<p class="text-danger">' . $i . '. ' . $date . " : " . $data['filename'] . " : found in csv. file not found.\n</p>");
        }
    }

 
    $uploadftp = 0;
    if ($upload) {
		
		// connect file server
		$fileManager->conntFileServer();

        $di = 0;
		foreach ($sourcePath as $key => $value) {
			// 256 encryption of file before transfer to ftp
			// encrypt_my_file($sourcePath[$key]);

			if ($fileManager->uploadFile($sourcePath[$key], $destinationPath[$key], false)) {

                $update_sql = "UPDATE tbl_document_master SET ftp_done = '1' WHERE doc_id = '".$doc_ids[$di]."'";
                // echo '<br>';
                mysqli_query($db_con, $update_sql);


				if(!in_array($sourcePath[$key], $fpathwithname)) {

					// unlink($sourcePath[$key]);
				} else {
					//decrypt_my_file($sourcePath[$key]);
				}
			}
            $di++;
		}
	   
		$uploadftp = 1;
		// send file for OCR
        //getData($fdocid, $fpath, $fpathwithname, $pCount, $ocrUrl);
    }
  
    if ($uploadftp){
		
        if (count($message) >= 1) {

            echo'<p class="text-danger">' . count($message) . ' Errors Found while uploading</P>';
            foreach ($message as $msg) {
                echo '<p class="text-danger">' . $msg . '</p>';
            }
        }
        if (count($message1) >= 1) {

            echo'<p class="text-success">' . count($message1) . ' files uploaded successfully...</P>';
            foreach ($message1 as $msg) {
                echo '<p class="text-success">' . $msg . '</p>';
            }
        }
        $uploadLogs = 'uploadLog';
        echo'<a href="bulk_upload">Upload More..</a><br>';
        echo'<a href=' . $uploadLogs . '>View Logs</a>';
    } else {
        $uploadLogs = 'uploadLog';
        echo'<p class="text-danger">' . count($message) . ' Errors Found while uploading</P>';
        foreach ($message as $msg) {
            echo '<p class="text-danger">' . $msg . '</p>';
        }
        echo'<p class="text-success">' . count($message1) . ' files uploaded successfully...</P>';
        foreach ($message1 as $msg) {
            echo '<p class="text-success">' . $msg . '</p>';
        }
        echo'<a href="bulk_upload">Upload More..</a><br>';
        echo'<a href=' . $uploadLogs . '>View Logs</a>';
    }

    fclose($logs);
    mysqli_close($db_con);
}

function spreadSheetCount() {
    $excel = new PhpExcelReader;
    $excel->read('test.xls');
    $number_of_Sheets = count($excel->sheets);
}

function PageCount_DOCX($file) {
    $pageCount = 0;

    $zip = new ZipArchive();

    if ($zip->open($file) === true) {
        if (($index = $zip->locateName('docProps/app.xml')) !== false) {
            $data = $zip->getFromIndex($index);
            $zip->close();
            $xml = new SimpleXMLElement($data);
            $pageCount = $xml->Pages;
        }
        $zip->close();
    }

    return $pageCount;
}

function count_pages($pdfname) {
    // echo $pdfname;
    // print_r($pdfname);

    // $pdftext = file_get_contents($pdfname);
    // $num = preg_match_all("/\/Page\W/", $pdftext, $dummy);

    // return $num;

    $cmd = "pdfinfo.exe";  // Windows
    // Parse entire output
    // Surround with double quotes if file name has spaces

    exec("$cmd \"$pdfname\"", $output);
    // Iterate through lines

    $pagecount = 0;
    foreach($output as $op) {
        // Extract the number
        if(preg_match("/Pages:\s*(\d+)/i", $op, $matches) === 1) {
            $pagecount = intval($matches[1]);
            break;
        }
    }
    return $pagecount;
}




function search_exif($exif, $field) {
    foreach ($exif as $key => $data) {
        if ($data['filename'] == $field) {
            return $key;
        }
    }
}

function getData($docId, $outputDir, $inputDir, $pCount) {
    /**
     * 
     * @param String $url
     * @param Array $params 
     * done by M.U
     */
    $docId = implode(",", $docId);
    $outputDir = implode(",", $outputDir);
    $inputDir = implode(",", $inputDir);
    $pCount = implode(",", $pCount);
    $url = BASE_URL . 'ocr_bulk.php';
    $params = array('docId' => $docId, 'outputDir' => $outputDir, 'inputDir' => $inputDir, 'pCount' => $pCount);
    //print_r($params);
    // print_r($params);
    foreach ($params as $key => &$val) {
        if (is_array($val))
        $val = implode(',', $val);
        $post_params[] = $key . '=' . urlencode($val);
    }
    $post_string = implode('&', $post_params);
    $parts = parse_url($url); //print_r($parts);die();

    //$fp = fsockopen($parts['host'], isset($parts['port']) ? $parts['port'] : 80, $errno, $errstr, 360000000000);
    $fp = fsockopen('ssl://' . $parts['host'], isset($parts['port']) ? $parts['port'] : 443, $errno, $errstr, 3600000);

    if (!$fp) {
        
    } else {
        $out = "POST " . $parts['path'] . " HTTP/1.1\r\n";
        $out .= "Host: " . $parts['host'] . "\r\n";
        $out .= "Content-Type: application/x-www-form-urlencoded\r\n";
        $out .= "Content-Length: " . strlen($post_string) . "\r\n";
        $out .= "Connection: Close\r\n\r\n";
        if (isset($post_string))
            $out .= $post_string;
        fwrite($fp, $out);
    }
}

?>