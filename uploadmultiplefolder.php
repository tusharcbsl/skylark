<?php
require_once './loginvalidate.php';
require_once './application/pages/function.php';
require_once './classes/fileManager.php';
$fileManager = new fileManager();

$rootFolder = [];
$parentFolder = [];
$fpathwithname = array();
$fpath = array();
$fdocid = array();
$fnoofPages = array();
$metaupload = true;
$destinationPathArray[] = "";
$sourcePathArray[] = "";



if (isset($_POST['uploadfolder'])) {

    $paths = $_POST['paths'];
    $paths = explode("//", $paths);
    $sl_id = $_POST['storage'];
    $files = count($_FILES['zip_upload']['name']);
    $depthLevel = 1;
    if ($_POST['optradio'] == 2) {

        $response = uploadMetaData($db_con, $sl_id, $lang);
        $Successmsg = $response['success'];
        $Errormsg = $response['error'];
    } else {

        if (!is_dir("uploadLogs")) {
            mkdir("uploadLogs", 0777, true);
        }
        $logs = fopen('uploadLogs/' . date('Ymdhis') . '.dat', "a");

        $metaupload = false;
        //$depthArray = [];
        $Successmsg = array();
        $Errormsg = array();

        foreach ($_FILES['files']['name'] as $key => $value) {

            $filename = $_FILES["files"]["name"][$key];
            $source = $_FILES["files"]["tmp_name"][$key];
            $type = $_FILES["files"]["type"][$key];
            $size = $_FILES["files"]["size"][$key];

            $allowed = ALLOWED_EXTN;
            $allowext = implode(", ", $allowed);
            $ext = pathinfo($filename, PATHINFO_EXTENSION);

            if (in_array(strtolower($ext), $allowed)) {

                $extn = substr($filename, strrpos($filename, '.') + 1);
                $fname = substr($filename, 0, strrpos($filename, '.'));
                $pathWithoutFile = substr($paths[$key], 0, strrpos($paths[$key], "/"));
                $fname = preg_replace('/[^\w$\x{0080}-\x{FFFF}!(){}+=~@%&#. -_]+/u', '', $fname);
                //$fname = preg_replace('/[^A-Za-z0-9_\-]/', '', $fname);
                $filenameEnct = urlencode(base64_encode($fname));
                $filenameEnct = preg_replace('/[^A-Za-z0-9_\-]/', '', $filenameEnct) . '.' . $extn;

                $filenameEnct1 = $fname . '.' . $extn; // urlencode(base64_encode($fname)).'.'.$extn;
                $slid = createSubFolder($pathWithoutFile, $sl_id, $db_con, $date, $host);
                if ($slid) {

                    $check = mysqli_query($db_con, "select * from tbl_document_master where old_doc_name='$filenameEnct1' and doc_name='$slid' and flag_multidelete=1");
                    if (mysqli_num_rows($check) > 0) {

                        $Errormsg[] = $lang['filealreadyexist'] . " - " . $filename;
                    } else {

                        $sl = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$slid'");
                        $rwSl = mysqli_fetch_assoc($sl);

                        $updir = getStoragePath($db_con, $rwSl['sl_parent_id'], $rwSl['sl_depth_level']);
                        if (!empty($updir)) {
                            $updir = $updir . '/';
                        } else {
                            $updir = '';
                        }

                        $storageName = $rwSl['sl_name'];
                        $storageName = str_replace(" ", "", $storageName);
                        $storageName = preg_replace('/[^A-Za-z0-9\-]/', '', $storageName);

                        $uploaddir = $updir . $storageName . '/';
                        $target_path = "extract-here/" . $uploaddir . '/';

                        if (!dir($target_path)) {
                            mkdir($target_path, 0777, TRUE); // or die("Error local folder:". print_r(error_get_last()));
                        }
                        //$fname = preg_replace('/[^A-Za-z0-9_\-]/', '', $fname);

                        $fname = preg_replace('/[^\w$\x{0080}-\x{FFFF}!(){}+=~@%&#. -_]+/u', '', $fname);
                        $filenameEnct1 = $fname . '.' . $extn; // urlencode(base64_encode($fname)).'.'.$extn;
                        $filenameEnct = urlencode(base64_encode($fname));
                        $filenameEnct = preg_replace('/[^A-Za-z0-9_\-]/', '', $filenameEnct) . '.' . $extn;
                        $fileUpload = move_uploaded_file($source, $target_path . $filenameEnct) or die('File Not Uploaded' . print_r(error_get_last()));

                        if ($fileUpload) {

                            $destinationPath = $uploaddir . '/' . $filenameEnct;
                            $sourcePath = $target_path . $filenameEnct;

                            $destinationPathArray[] = ROOT_FTP_FOLDER . '/' . $destinationPath;
                            $sourcePathArray[] = $sourcePath;

                            $doc_path = $uploaddir . $filenameEnct;

                            //if (uploadFileInFtpServer($fileserver, $port, $ftpUser, $ftpPwd, $destinationPath, $sourcePath)) {

                            if ($extn == "pdf") {
                                // echo rtrim($target_path, '/');
                                // echo '<br>';
                                // echo $filenameEnct;
                                $noofPages = count_pages(rtrim($target_path, '/') . '/' . $filenameEnct);
                                // echo 'testing';
                                // exit;
                                if ($noofPages > 0) {
                                    $noofPages = $noofPages;
                                } else {
                                    $noofPages = 1;
                                }
                            } elseif ($extn == "docx") {
                                $noofPages = PageCount_DOCX($target_path . $filenameEnct);
                            } else {
                                $noofPages = 1;
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
                                $docText = xls_to_txt($extractHereDirfile);
                            } elseif (strtolower($extn) == "pptx" || strtolower($extn) == "ppt") {
                                $docText = pptx_to_text($extractHereDirfile);
                            } else if (strtolower($extn) == "txt" || strtolower($extn) == "text") {
                                $docText = txt_to_text($extractHereDirfile);
                            } else if (in_array(strtolower($extn), $img_array)) {

                                $fpathwithname[] = $target_path . $filenameEnct;
                                $fpath[] = $target_path;
                                $fdocid[] = $doc_id;
                                $fnoofPages[] = $noofPages;
                            }
                            if ($docText != "") {
                                $fp = fopen($txtpath . $doc_id . ".txt", "wb");
                                fwrite($fp, $docText);
                                fclose($fp);
                            }
                            //if (mysqli_num_rows($check) > 0) {
                            //mysqli_set_charset($db_con, "utf8");
                            //$rwCheck = mysqli_fetch_assoc($check);
                            //$pdfUpdate = mysqli_query($db_con, "update tbl_document_master set doc_extn='$extn', doc_path='$storageName/$filenameEnct',uploaded_by='$_SESSION[cdes_user_id]',doc_size='$size',dateposted='$date',old_doc_name='$fname' where filename='$filename'") or die('Error' . mysqli_error($db_con));
                            //$upload = 1;
                            //$doc_id = $rwCheck['doc_id'];
                            //$dcid = $rwCheck['doc_id'];
                            //$key = array_search($dcid, $csvids);
                            //unset($csvids[$key]); // or die(print_r(error_get_last()));
                            //$uploadFileCount++;
                            //} else {
                            mysqli_set_charset($db_con, "utf8");
                            $fileins = true;
                            $fileins = mysqli_query($db_con, "insert into tbl_document_master(doc_id,doc_name,old_doc_name,doc_extn,doc_path,uploaded_by,noofpages,doc_size,dateposted) values(null,'$slid','$filenameEnct1','$extn','$doc_path','$_SESSION[cdes_user_id]','$noofPages','$size','$date')") or die('Error' . mysqli_error($db_con));
                            if ($fileins) {
                                //die();
                                $doc_id = mysqli_insert_id($db_con);
                                $Successmsg[] = $filename . " " . $lang['Fle_Uplded_Sucsfly'];
                                //create thumbnail
                                if (CREATE_THUMBNAIL) {
                                    $newdocname = base64_encode($doc_id);
                                    $uploadedfilename = $target_path . $filenameEnct;

                                    if ($extn == 'jpg' || $extn == 'jpeg' || $extn == 'png') {
                                        createThumbnail2($uploadedfilename, $newdocname);
                                    } elseif ($extn == 'pdf') {
                                        changePdfToImage($uploadedfilename, $newdocname);
                                    }
                                }
                            } else {
                                //echo 'falied->'.$key;
                            }

                            //}
                            //if ($type == 'application/pdf') {
                            // } else {
                            //     $Errormsg[] = $lang['Op_Fle_upld_fld'] . " - " . $filename;
                            // }
                        } else {
                            $Errormsg[] = $lang['Op_Fle_upld_fld'] . " - " . $filename;
                        }
                    }
                }
            } else {

                $Errormsg[] = "$filename file extention not allowed.";
            }
        }


        mysqli_close($db_con);


        // connect file server
        $fileManager->conntFileServer();
        foreach ($destinationPathArray as $key => $value) {

            // encrypt_my_file($sourcePathArray[$key]);    

            if ($fileManager->uploadFile($sourcePathArray[$key], $destinationPathArray[$key], false)) {

                if (!in_array($sourcePathArray[$key], $fpathwithname)) {
                    unlink($sourcePathArray[$key]);
                } else {
                    //decrypt_my_file($sourcePathArray[$key]);
                }
            }
        }


        if (count($fpath) > 0) {
            //getData($fdocid, $fpath, $fpathwithname, $fnoofPages, $ocrUrl);
        }
    }


    // message for metadata
    if ((isset($Successmsg) && count($Successmsg) > 0 || isset($Errormsg)) && $metaupload == true) {

        echo '<p class="text-success" >' . ((count($Successmsg) > 0) ? count($Successmsg) . ' ' . $lang['meta_insert_success'] : "") . '</p>';
        foreach ($Errormsg as $error) {
            echo '<p class="text-danger" >' . $error . '</p>';

            fwrite($logs, '<p class="text-danger">' . $i . '. ' . $date . ' : ' . $error . '\n</p>');
        }
        // message for file upload
    } else if ((isset($Successmsg) && count($Successmsg) > 0 || isset($Errormsg)) && $metaupload == false) {

        echo '<a href="storage?id=' . base64_encode(urlencode($_POST['storage'])) . '" style="color:green; font-size:15px;" traget="_blank">' . $lang['Click_here_files'] . ' <i class="fa fa-arrow-right"></i></a>';

        echo '<p class="text-success" >' . ((count($Successmsg) > 0) ? count($Successmsg) . ' ' . $lang['Fle_Uplded_Sucsfly'] : "") . '</p>';

        fwrite($logs, '<p class="text-success">' . $i . '. ' . $date . ' : ' . $lang['Fle_Uplded_Sucsfly'] . '\n</p>');

        foreach ($Errormsg as $error) {

            echo '<p class="text-danger" >' . $error . '</p>';

            fwrite($logs, '<p class="text-danger">' . $i . '. ' . $date . ' : ' . $error . '\n</p>');
        }
    }
}

function createSubFolder($folderfullpath, $sl_id, $db_con, $date, $host)
{
    //global $depthArray;
    $array = explode("/", $folderfullpath);
    foreach ($array as $key => $value) {
        $parentfolder = $array[$key];
        $parentfolder = mysqli_real_escape_string($db_con, $parentfolder);
        $resultParent = mysqli_query($db_con, "select sl_id, sl_depth_level from tbl_storage_level where sl_parent_id='$sl_id' AND sl_name='$parentfolder' and delete_status=0 order by sl_id desc limit 1") or die('Error in checkLvlName:' . mysqli_error($db_con));
        if (mysqli_num_rows($resultParent) > 0) {
            $storage = mysqli_fetch_assoc($resultParent);
            $sl_id = $storage['sl_id'];
        } else {

            $parent = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$sl_id'") or die('Error:' . mysqli_error($db_con));
            $rwParent = mysqli_fetch_assoc($parent);
            $level = $rwParent['sl_depth_level'] + 1;

            $sql = "insert into tbl_storage_level(sl_name, sl_parent_id, sl_depth_level)VALUES ('$parentfolder', '$sl_id', '$level')";
            $sql_run = mysqli_query($db_con, $sql) or die("error1:" . mysqli_error($db_con));
            if ($sql_run) {
                $sl_id = mysqli_insert_id($db_con);
            }
        }
    }

    return $sl_id;
}

function uploadMetaData($db_con, $sl_id, $lang)
{

    $filename = $_FILES["csvfile"]["name"];
    $source = $_FILES["csvfile"]["tmp_name"];
    $type = $_FILES["csvfile"]["type"];
    $size = $_FILES["csvfile"]["size"];
    $extn = substr($filename, strrpos($filename, '.') + 1);

    $Errormsg = array();
    $Successmsg = array();

    if (strtolower($extn) == "csv") {
        mysqli_set_charset($db_con, "utf8");
        $columns = mysqli_query($db_con, "SHOW COLUMNS FROM `tbl_document_master`");
        $col = array();
        while ($rwCol = mysqli_fetch_array($columns)) {
            $col[] = $rwCol['Field'];
        }

        $row = 1;
        if (($handle = fopen($source, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                // $data = array_map("utf8_encode", $data);
                $num = count($data);
                if ($row == 1) {
                    $cols = '';
                    $colums = array();
                    for ($c = 0; $c < $num; $c++) {
                        $metaName = $data[$c]; //str_replace("dc.", "", $data[$c]) . "<br />\n";
                        //print_r($metaName);
                        if (in_array($metaName, $col)) {
                            $flag = 1;
                            if (!empty($cols)) {

                                $colums[] = $metaName;
                            } else {

                                $colums[] = $metaName;
                            }
                        } else {

                            $flag = 0;
                            $msg = str_replace('column', $metaName, $lang['metadata_notfound']);

                            $Errormsg[] = $msg;
                            // break;
                            //die('ok');
                        }
                    }
                } else {
                    if ($flag == 1) {
                        //echo $cols.'<br>';
                        $values = array();
                        for ($c = 0; $c < $num; $c++) {
                            if (!empty($values)) {
                                //$values = $values . ",'" . $data[$c] . "'";
                                $values[] = $data[$c];
                            } else {
                                //$values = "'" . $data[$c] . "'";
                                $values[] = $data[$c];
                            }
                        }
                        if ($data[1] != "") {
                            // echo "select * from tbl_document_master where old_doc_name='$data[0]' and doc_name='$sl_id'";
                            $check = mysqli_query($db_con, "select * from tbl_document_master where old_doc_name='$data[0]' and doc_name='$sl_id'");
                            if (mysqli_num_rows($check) > 0) {

                                $rowP = mysqli_fetch_assoc($check);
                                $preDocId = $rowP['doc_id'];
                                $query = "doc_name='" . $sl_id . "'";

                                //print_r($colums);
                                foreach ($colums as $key => $value) {

                                    $query .= "," . $colums[$key] . "='" . $values[$key] . "'";
                                }
                                // $insert = mysqli_query($db_con, "insert into tbl_document_master(doc_name,$cols)values('$sl_id',$values)") or die('Error' . mysqli_error($db_con));
                                $update = mysqli_query($db_con, "update tbl_document_master set $query where doc_id='$preDocId'") or die('Error' . mysqli_error($db_con));

                                if ($update) {
                                    $Successmsg[] = "File $data[0] metadata inserted successfully";
                                } else {
                                    $Errormsg[] = "Failed to insert metadata - $data[0]";
                                }
                            } else {
                                $Errormsg[] = str_replace('FILE', $data[0], $lang['fileexits']);
                            }
                        } else {
                            $Errormsg[] = str_replace('column', '', $lang['metadata_notfound']);
                            break;
                        }
                    }
                }

                // echo $cols;

                $row++;
            }

            fclose($handle);
        }
    } else {
        $Errormsg[] = "Please upload only csv file";
    }

    return array('error' => $Errormsg, 'success' => $Successmsg);
}


function spreadSheetCount()
{
    $excel = new PhpExcelReader;
    $excel->read('test.xls');
    $number_of_Sheets = count($excel->sheets);
}

function PageCount_DOCX($file)
{
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

function count_pages($pdfname)
{

    // $pdftext = file_get_contents($pdfname);

    // $num = preg_match_all("/\/Page\W/", $pdftext, $dummy);

    // return $num;

    $cmd = "pdfinfo.exe";  // Windows
    // Parse entire output
    // Surround with double quotes if file name has spaces

    exec("$cmd \"$pdfname\"", $output);
    // Iterate through lines

    $pagecount = 0;
    foreach ($output as $op) {
        // Extract the number
        if (preg_match("/Pages:\s*(\d+)/i", $op, $matches) === 1) {
            $pagecount = intval($matches[1]);
            break;
        }
    }
    return $pagecount;
}

function getData($docId, $outputDir, $inputDir, $pCount, $ocrUrl)
{

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
    $fp = fsockopen($parts['host'], isset($parts['port']) ? $parts['port'] : 80, $errno, $errstr, 3600);
    //$fp = fsockopen('ssl://'.$parts['host'], isset($parts['port']) ? $parts['port'] : 443, $errno, $errstr, 36000000);
    if (!$fp) {
        //echo 'Socket is not active';
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
