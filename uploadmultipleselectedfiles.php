<?php

require_once './loginvalidate.php';
require_once './classes/ftp.php';
require_once './application/pages/function.php';

if (isset($_POST['bulkUpload'], $_POST['token'])) {

    $flag = 1;
    $message = array();
    $message1 = array();
    $fpathwithname = array();
    $fpath = array();
    $fdocid = array();
    $csvids = array();
    $sourcePath = [];
    $destinationPath = [];
    $slID = mysqli_real_escape_string($db_con, $_POST['storage']);
    mysqli_set_charset($db_con, "utf8");
    $sl = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$slID'");
    $rwSl = mysqli_fetch_assoc($sl);
    $storageName = $rwSl['sl_name'];
    $storageName = str_replace(" ", "", $storageName);
    $storageName = preg_replace('/[^A-Za-z0-9\-]/', '', $storageName);
    $upload = 0;
    $uploadFileCount = 0;

    $updir = getStoragePath($db_con, $rwSl['sl_parent_id'], $rwSl['sl_depth_level']);
    if (!empty($updir)) {
        $updir = $updir . '/';
    } else {
        $updir = '';
    }
    $uploaddir = $updir . $storageName.'/';
    $target_path = 'extract-here/' . $uploaddir;
    if (!is_dir("uploadLogs")) {
        mkdir("uploadLogs", 0777, true);
    }


    $logs = fopen('uploadLogs/' . date('Ymdhis') . '.dat', "a");

    $countf = count($_FILES["zip_upload"]["name"]);
    if ($flag == 1) {
        for ($i = 0; $i < $countf; $i++) {
            $filename = $_FILES["zip_upload"]["name"][$i];
            $source = $_FILES["zip_upload"]["tmp_name"][$i];
            $type = $_FILES["zip_upload"]["type"][$i];
            $size = $_FILES["zip_upload"]["size"][$i];
            $extn = substr($filename, strrpos($filename, '.') + 1);
            $fname = substr($filename, 0, strrpos($filename, '.'));
            // echo 'ok'; die;   
            $name = explode(".", $filename);
            //$accepted_types = array('application/zip', 'application/x-zip-compressed', 'multipart/x-zip', 'application/x-compressed', 'application/pdf', 'image/*', 'audio/mp3', 'video/mp4');
            $okay = true;
            $accepted_types = ALLOWED_EXTN;
            $ext = pathinfo($filename, PATHINFO_EXTENSION);
            $ext = strtolower($ext);
            if (!in_array($ext, $accepted_types)) {
                $message[] = "$filename file extention not allowed to upload";
                $okay = false;
            }
            if ($okay) {
                if (!dir($target_path)) {
                    mkdir($target_path, 0777, TRUE); // or die("Error local folder:". print_r(error_get_last()));
                }
                $fname1 = preg_replace('/[^A-Za-z0-9_\-]/', '', $fname);
                // $filenameEnct=$fname.'.'.$extn;// urlencode(base64_encode($fname)).'.'.$extn;
                $filenameEnct = urlencode(base64_encode($fname1));
                $filenameEnct = preg_replace('/[^A-Za-z0-9_\-]/', '', $filenameEnct) . '.' . $extn;
                $fileUpload = move_uploaded_file($source, $target_path . $filenameEnct) or die('File Not Uploaded' . print_r(error_get_last()));

                $uploadedFTP = true;
                if ($fileUpload) {


                    $sourcePath[] = $target_path . $filenameEnct;
                    $destinationPath[] = 'DMS/' . ROOT_FTP_FOLDER . '/' . $uploaddir . $filenameEnct;

                    if ($extn == "pdf") {
                        $noofPages = count_pages($target_path . $filenameEnct);
                    } elseif ($extn == "docx") {
                        $noofPages = PageCount_DOCX($target_path . $filenameEnct);
                    } else {
                        $noofPages = 1;
                    }

                    $fileNameExtn = $fname . '.' . $extn;
                    mysqli_set_charset($db_con, "utf8");

                    $check = mysqli_query($db_con, "select doc_id from tbl_document_master where old_doc_name='$fileNameExtn' and doc_name='$slID' and flag_multidelete=1");

                    if (mysqli_num_rows($check) > 0) {
                        fwrite($logs, '<p class="text-danger">' . $i . '. ' . $date . " : $fileNameExtn : already exist in $storageName folder.\n</p>");
                        $message[] = "$fileNameExtn already exist in $storageName folder";
                    } else {
                        mysqli_set_charset($db_con, "utf8");
                        $docpath = $uploaddir . $filenameEnct;
                        $query = "insert into tbl_document_master set doc_name='$slID',old_doc_name='$fileNameExtn',doc_extn='$extn',doc_path='$docpath',uploaded_by='$_SESSION[cdes_user_id]',doc_size='$size',dateposted='$date', noofpages='$noofPages', filename='$fname'";
                        $fileins = mysqli_query($db_con, $query) or die('Error' . mysqli_error($db_con));
                        $upload = 1;
                        $doc_id = mysqli_insert_id($db_con);
                        if (CREATE_THUMBNAIL) {
                            $newdocname = base64_encode($doc_id);
                            //create thumbnail
                            $uploadedfilename = $target_path . $filenameEnct;
                            if ($extn == 'jpg' || $extn == 'jpeg' || $extn == 'png') {
                                //createThumbnail2($uploadedfilename, $newdocname);
                            } elseif ($extn == 'pdf') {
                                //changePdfToImage($uploadedfilename, $newdocname);
                            }
                        }
                        $img_array = array('pdf', 'jpg', 'jpeg', 'png', 'bmp', 'pnm', 'jfif', 'jpeg', 'tiff');

                        $txtpath = $target_path . '/TXT/';
                        if (!is_dir($txtpath)) {
                            mkdir($txtpath, 0777, TRUE) or die(print_r(error_get_last()));
                        }
                        $extractHereDirfile = $target_path . $filenameEnct;
                        if (strtolower($extn) == "doc") {
                            $docText = read_doc($extractHereDirfile);
                        } elseif (strtolower($extn) == "docx") {
                            $docText = read_docx($extractHereDirfile);
                        } elseif (strtolower($extn) == "xlsx") {
                            $docText = xlsx_to_text($extractHereDirfile);
                        } elseif (strtolower($extn) == "xls") {
                            //$docText = xls_to_txt($extractHereDirfile);
                        } elseif (strtolower($extn) == "pptx" || strtolower($extn) == "ppt") {
                            $docText = pptx_to_text($extractHereDirfile);
                        } else if (strtolower($extn) == "txt" || strtolower($extn) == "text") {
                            $docText = txt_to_text($extractHereDirfile);
                        } else if (in_array(strtolower($extn), $img_array)) {
                            $fpathwithname[] = $target_path . $filenameEnct;
                            $fpath[] = $target_path;
                            $fdocid[] = $doc_id;
                            $pCount[] = $noofPages;
                        }
						// echo $docText;
						// die('hghe');
                        if (!empty($docText)) {
                            $fp = fopen($txtpath . $doc_id . ".txt", "wb");
                            fwrite($fp, $docText);
                            fclose($fp);
                        }
                        $message1[] = "$fileNameExtn uploaded successfully";

                        fwrite($logs, '<p class="text-success">' . $i . '. ' . $date . " : $fileNameExtn : uploaded successfully.\n</p>");
                        mysqli_set_charset($db_con, "utf8");
                       $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`, `sl_id`, `action_name`, `start_date`, `system_ip`, `remarks`) values ('$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]','$slID','Document Uploaded','$date','$host','Document $filename uploaded in $storageName')") or die('error : ' . mysqli_error($db_con));
                    }
                } else {
                    $message[] = "failed to upload - $fileNameExtn";
                }
            }
        }
    }

    $uploadftp = 0;
    if ($fileUpload) {
    if (FTP_ENABLED) {

            $ftp = new ftp();
            $ftp->conn("$fileserver", "$port", "$ftpUser", "$ftpPwd");
           
            //             print_r($sourcePath);      
            //  print_r($destinationPath);
            //  die("rrrr");

            foreach ($sourcePath as $key => $value) {

                //encrypt_my_file($sourcePath[$key]);
               
                if ($ftp->put($destinationPath[$key], $sourcePath[$key])) {
                    unlink($sourcePath[$key]);
                    // if (!in_array($sourcePath[$key], $fpathwithname)) {

                    //     unlink($sourcePath[$key]);
                    // } else {
                    //     //decrypt_my_file($sourcePath[$key]);
                    // }
                }
            }
            $uploadftp = 1;
            $ftp->closeConn();
        } else {
            
        }


        //getData($fdocid, $fpath, $fpathwithname, $pCount, $ocrUrl);
    }
    // start upload in FTP
    if ($uploadftp) {
        if (count($message) >= 1) {


            echo'<p class="text-danger">' . count($message) . ' Errors Found while uploading</P>';
            foreach ($message as $msg) {
                echo '<p class="text-danger">' . $msg . '</p>';
            }
        }


        if (count($message1) > 0) {

            echo'<p class="text-success">' . count($message1) . ' files uploaded successfully...</P>';
            foreach ($message1 as $msg) {
                echo '<p class="text-success">' . $msg . '</p>';
            }
        }

        $uploadLogs = 'uploadLog';
        echo'<a href="upload-multiple-files">Upload More..</a><br>';
        echo'<a href=' . $uploadLogs . '>View Logs</a>';
    } else {

        if (FTP_ENABLED) {

            echo'<p class="text-danger">' . count($message) . ' Errors found while uploading on ftp server</P>';
        }

        if (count($message) >= 1) {


            echo'<p class="text-danger">' . count($message) . ' Errors Found while uploading</P>';
            foreach ($message as $msg) {
                echo '<p class="text-danger">' . $msg . '</p>';
            }
        }


        if (count($message1) > 0) {

            echo'<p class="text-success">' . count($message1) . ' files uploaded successfully...</P>';
            foreach ($message1 as $msg) {
                echo '<p class="text-success">' . $msg . '</p>';
            }
        }

        $uploadLogs = 'uploadLog';
        echo'<a href="upload-multiple-files">Upload More..</a><br>';
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

/*function count_pages($pdfname) {

    $pdftext = file_get_contents($pdfname);
    $num = preg_match_all("/\/Page\W/", $pdftext, $dummy);

    return $num;
}*/

function count_pages($pdfname) {

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


function getData($docId, $outputDir, $inputDir, $pCount, $ocrUrl) {
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
    if (isset($_SERVER['HTTPS'])) {
        $fp = fsockopen('ssl://' . $parts['host'], isset($parts['port']) ? $parts['port'] : 443, $errno, $errstr, 3600);
    }else{
        $fp = fsockopen($parts['host'], isset($parts['port']) ? $parts['port'] : 80, $errno, $errstr, 3600);
    }
   // $fp = fsockopen('ssl://' . $parts['host'], isset($parts['port']) ? $parts['port'] : 443, $errno, $errstr, 3600000);

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