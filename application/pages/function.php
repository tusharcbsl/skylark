<?php

function sheetData($sheet) {
    $re = ''; // starts html table

    $x = 1;
    while ($x <= $sheet['numRows']) {
        $re .= "\n";
        $y = 1;
        while ($y <= $sheet['numCols']) {
            $cell = isset($sheet['cells'][$x][$y]) ? $sheet['cells'][$x][$y] : '';
            $re .= " $cell\n";
            $y++;
        }
        $re .= "\n";
        $x++;
    }

    return $re . ''; // ends and returns the html table
}

function xls_to_txt($file) {

    $excel = new PhpExcelReader;
    $excel->read($file);
    $nr_sheets = count($excel->sheets); // gets the number of worksheets
    $excel_data = ''; // to store the the html tables with data of each sheet
// traverses the number of sheets and sets html table with each sheet data in $excel_data
    for ($i = 0; $i < $nr_sheets; $i++) {
        $excel_data .= sheetData($excel->sheets[$i]);
    }

    return $excel_data;
}

function xlsx_to_text($input_file) {
    $xml_filename = "xl/sharedStrings.xml"; //content file name
    $zip_handle = new ZipArchive;
    $output_text = "";
    if (true === $zip_handle->open($input_file)) {
        if (($xml_index = $zip_handle->locateName($xml_filename)) !== false) {
            $xml_datas = $zip_handle->getFromIndex($xml_index);
            $xml_handle = DOMDocument::loadXML($xml_datas, LIBXML_NOENT | LIBXML_XINCLUDE | LIBXML_NOERROR | LIBXML_NOWARNING);
            $output_text = strip_tags($xml_handle->saveXML());
        } else {
            $output_text .= "";
        }
        $zip_handle->close();
    } else {
        $output_text .= "";
    }
    return $output_text;
}

/* * ***********************power point files**************************** */

function pptx_to_text($input_file) {
    $zip_handle = new ZipArchive;
    $output_text = "";
    if (true === $zip_handle->open($input_file)) {
        $slide_number = 1; //loop through slide files
        while (($xml_index = $zip_handle->locateName("ppt/slides/slide" . $slide_number . ".xml")) !== false) {
            $xml_datas = $zip_handle->getFromIndex($xml_index);
            $xml_handle = DOMDocument::loadXML($xml_datas, LIBXML_NOENT | LIBXML_XINCLUDE | LIBXML_NOERROR | LIBXML_NOWARNING);
            $output_text .= strip_tags($xml_handle->saveXML());
            $slide_number++;
        }
        if ($slide_number == 1) {
            $output_text .= "";
        }
        $zip_handle->close();
    } else {
        $output_text .= "";
    }
    return $output_text;
}

function read_docx($filename) {

    $striped_content = '';
    $content = '';

    $zip = zip_open($filename);

    if (!$zip || is_numeric($zip))
        return false;

    while ($zip_entry = zip_read($zip)) {

        if (zip_entry_open($zip, $zip_entry) == FALSE)
            continue;

        if (zip_entry_name($zip_entry) != "word/document.xml")
            continue;

        $content .= zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));

        zip_entry_close($zip_entry);
    }// end while

    zip_close($zip);

    $content = str_replace('</w:r></w:p></w:tc><w:tc>', " ", $content);
    $content = str_replace('</w:r></w:p>', "\r\n", $content);
    $striped_content = strip_tags($content);

    return $striped_content;
}

function read_doc($filename) {
    $fileHandle = fopen($filename, "r");
    $line = @fread($fileHandle, filesize($filename));
    $lines = explode(chr(0x0D), $line);
    $outtext = "";
    foreach ($lines as $thisline) {
        $pos = strpos($thisline, chr(0x00));
        if (($pos !== FALSE) || (strlen($thisline) == 0)) {
            
        } else {
            $outtext .= $thisline . " ";
        }
    }
    $outtext = preg_replace("/[^a-zA-Z0-9\s\,\.\-\n\r\t@\/\_\(\)]/", "", $outtext);
    return $outtext;
}

function odt_to_text($input_file) {
    $xml_filename = "content.xml"; //content file name
    $zip_handle = new ZipArchive;
    $output_text = "";
    if (true === $zip_handle->open($input_file)) {
        if (($xml_index = $zip_handle->locateName($xml_filename)) !== false) {
            $xml_datas = $zip_handle->getFromIndex($xml_index);
            $xml_handle = DOMDocument::loadXML($xml_datas, LIBXML_NOENT | LIBXML_XINCLUDE | LIBXML_NOERROR | LIBXML_NOWARNING);
            $output_text = strip_tags($xml_handle->saveXML());
        } else {
            $output_text .= "";
        }
        $zip_handle->close();
    } else {
        $output_text .= "";
    }
    return $output_text;
}

//delete storage level
function delStrg($sl_id, $fileserver, $port, $ftpUser, $ftpPwd) {

    global $db_con;


    $getDocPath = mysqli_query($db_con, "select * from tbl_document_master where doc_name='$sl_id'") or die('Error:' . mysqli_error($db_con));
    while ($rwgetDocPath = mysqli_fetch_assoc($getDocPath)) {
        $filePath = "extract-here/" . $rwgetDocPath['doc_path'];
        if (FTP_ENABLED) {
            $ftp = new ftp();
            $ftp->conn("$fileserver", "$port", "$ftpUser", "$ftpPwd");

            $ftp->singleFileDelete(ROOT_FTP_FOLDER . '/' . $rwgetDocPath['doc_path']);
            $arr = $ftp->getLogData();
            if ($arr['error'] != "") {
                echo '<h2>Error:</h2>' . implode('<br />', $arr['error']);
            }
        } else {
            unlink($filePath);
        }
        mysqli_query($db_con, "DELETE FROM tbl_document_master where doc_name='$sl_id' && doc_id='$rwgetDocPath[doc_id]'") or die('Error:' . mysqli_error($db_con));
        $confirm = "yes";
    }
    $sql_child = "select * FROM tbl_storage_level WHERE sl_parent_id = '$sl_id' ";

    $sql_child_run = mysqli_query($db_con, $sql_child) or die('Error:' . mysqli_error($db_con));
    if ($confirm == "yes") {
        // return true;
    }


    if (mysqli_num_rows($sql_child_run) > 0) {

        while ($rwchild = mysqli_fetch_assoc($sql_child_run)) {

            $child = $rwchild['sl_id'];
            delStrg($child, $fileserver, $port, $ftpUser, $ftpPwd);
        }
    }
}

//fun for copystorage
// function copyStorage($toCopyFolderId, $lastCopyToId, $toCopyFolderName, $date, $fileManager, $lang = '') {

//     global $db_con;
    
//     $storageCopyName = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$toCopyFolderId'") or die('Error: 1' . mysqli_error($db_con));

//     $rwstorageCopyName = mysqli_fetch_assoc($storageCopyName);

//     $copyName = $rwstorageCopyName['sl_name'];

//     if ($toCopyFolderName) {
//         $copyName = $toCopyFolderName;
//     }
//     $storageCopyToCheck = mysqli_query($db_con, "select * from tbl_storage_level where sl_parent_id='$lastCopyToId' AND sl_name = '$copyName'") or die('Error:' . mysqli_error($db_con));
	
//     if (mysqli_num_rows($storageCopyToCheck) < 1) {

//         $storageCopyTo = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$lastCopyToId'") or die('Error:' . mysqli_error($db_con));

//         $rwstorageCopyTo = mysqli_fetch_assoc($storageCopyTo);

//         $copyToName = $rwstorageCopyTo['sl_name'];
//         $copyToLevel = $rwstorageCopyTo['sl_depth_level'];
//         $copyToLevel = $copyToLevel + 1;

//         $insertTo = mysqli_query($db_con, "insert into tbl_storage_level (sl_name, sl_parent_id, sl_depth_level) values('$copyName', '$lastCopyToId', '$copyToLevel')") or die('Error ss:' . mysqli_error($db_con));
//         $insertId = mysqli_insert_id($db_con);
		
		
// 		$slname = str_replace(" ", "", $copyName);
// 		$updir = getStoragePath($db_con, $lastCopyToId, $copyToLevel);

// 		if(!empty($updir)){
// 			$updir = $updir . '/';
// 		}else{
// 			$updir = '';
// 		}
// 		$folderpath = $updir.$slname;

//         //insert doc of copy record

//         $storageCopyNameDoc = mysqli_query($db_con, "select * from tbl_document_master where doc_name='$toCopyFolderId'") or die('Error: ' . mysqli_error($db_con));

//         $rowcount = mysqli_num_rows($storageCopyNameDoc);

//         $result = mysqli_fetch_all($storageCopyNameDoc);

//         for ($i = 0; $i < $rowcount; $i++) {
			
// 			$docId = $result[$i][0];
//             unset($result[$i][0]); //Remove ID from array
//             // print_r($result[$i]); 
			
// 			$doc_path = $result[$i][5];
			
//             $qrystr = " INSERT INTO tbl_document_master";

//             $qrystr .= " VALUES (null, '" . implode("', '", array_values($result[$i])) . "')";

//             $insertCopyDoc = mysqli_query($db_con, $qrystr) or die('Error insert: ' . mysqli_error($db_con));

//             $insertCopyDocId = mysqli_insert_id($db_con);
// 			$filname = substr($doc_path, strrpos($doc_path, '/') + 1);
// 			$newDocPath = $folderpath.'/'.$filname;
// 			$fileNewPath = 'extract-here/'.$folderpath.'/'.$filname;
			
// 			$path = substr($doc_path, 0, strrpos($doc_path, '/') + 1);

// 			$pathtxt = 'extract-here/' . $path . 'TXT/' . $docId . '.txt';
			
// 			if (!is_dir('extract-here/'.$folderpath. '/TXT/')) {
// 				mkdir('extract-here/'.$folderpath. '/TXT/', 0777, TRUE) or die(print_r(error_get_last()));
// 			}
			
// 			$newTxtPath = 'extract-here/' . $folderpath . '/TXT/' . $insertCopyDocId . '.txt';

//             $updateDoc = "update tbl_document_master set doc_name = '$insertId', doc_path='$newDocPath', ftp_done='1'  where doc_id = '$insertCopyDocId'";

//             mysqli_query($db_con, $updateDoc) or die('Error' . mysqli_error($db_con));
            
//             $doc_pathArr = explode("/", $doc_path);
//             $doc_Path_copy_to = $copyName . '/' . end($doc_pathArr);
			
// 			if(!file_exists('extract-here/'.$doc_path)){
				
// 				if ($fileManager->downloadFile('DMS/' . ROOT_FTP_FOLDER . '/' . $doc_path, $fileNewPath)) {
					
// 					$uploadfile = $fileManager->uploadFile($fileNewPath, 'DMS/' . ROOT_FTP_FOLDER . '/' . $newDocPath);
						
// 					if ($uploadfile) {
						
// 						if(file_exists($pathtxt)){
// 							copy($pathtxt, $newTxtPath);
// 						}
// 					} 
// 				}
// 			}else{
// 				$oldFilePath = 'extract-here/'.$doc_path;
// 				$uploadfile = $fileManager->uploadFile($oldFilePath, 'DMS/' . ROOT_FTP_FOLDER . '/' . $newDocPath);
// 				if ($uploadfile) {
						
// 					if(file_exists($pathtxt)){
// 						copy($pathtxt, $newTxtPath);
// 					}
// 				} 
// 			}
//         }
//         $sql_child = "select * FROM tbl_storage_level WHERE sl_parent_id = '$toCopyFolderId' ";

//         $sql_child_run = mysqli_query($db_con, $sql_child) or die('Error update:' . mysqli_error($db_con));
//         if (mysqli_num_rows($sql_child_run) > 0) {

//             while ($rwchild = mysqli_fetch_assoc($sql_child_run)) {

//                 $child = $rwchild['sl_id'];

//                 $childCopyname = $rwchild['sl_name'];

//                 copyStorage($child, $insertId, null, $date, $fileManager, $lang);
//             }
//         }
		
//         $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,'$toCopyFolderId','Storage level $copyName copy to $copyToName.','$date',null,'$host','')") or die('error : ' . mysqli_error($db_con));

//         echo'<script>taskSuccess("' . $_SERVER['REQUEST_URI'] . '","' . $lang['scs'] . '");</script>';
//     } else {
//         $storageCopyTo = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$lastCopyToId'") or die('Error:' . mysqli_error($db_con));
//         $rwstorageCopyTo = mysqli_fetch_assoc($storageCopyTo);
//         $copyToName = $rwstorageCopyTo['sl_name'];
//         $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,'$toCopyFolderId','Storage $copyName exist in $copyToName rename to copy .','$date',null,'$host','')") or die('error : ' . mysqli_error($db_con));
//         echo'<script>taskFailed("' . $_SERVER['REQUEST_URI'] . '","' . $lang['Storage_already_exist'] . '");</script>';
//     }
// }

function copyStorage($toCopyFolderId, $lastCopyToId, $toCopyFolderName, $date, $host, $fileserver, $port, $ftpUser, $ftpPwd, $lang, $db_con) {

    $storageCopyName = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$toCopyFolderId'") or die('Error: 1' . mysqli_error($db_con));

    $rwstorageCopyName = mysqli_fetch_assoc($storageCopyName);

    $copyName = $rwstorageCopyName['sl_name'];

    if ($toCopyFolderName) {
        $copyName = $toCopyFolderName;
    }
    $storageCopyToCheck = mysqli_query($db_con, "select * from tbl_storage_level where sl_parent_id='$lastCopyToId' AND sl_name = '$copyName'") or die('Error:' . mysqli_error($db_con));

    if (mysqli_num_rows($storageCopyToCheck) < 1) {

        $storageCopyTo = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$lastCopyToId'") or die('Error:' . mysqli_error($db_con));
        $rwstorageCopyTo = mysqli_fetch_assoc($storageCopyTo);
        $copyToName = $rwstorageCopyTo['sl_name'];
        $copyToLevel = $rwstorageCopyTo['sl_depth_level'];
        $copyToLevel = $copyToLevel + 1;
        //insert doc of copy record
        //echo "select * from tbl_document_master where doc_name='$toCopyFolderId'";
        $storageCopyNameDoc = mysqli_query($db_con, "select * from tbl_document_master where doc_name='$toCopyFolderId'") or die('Error: ' . mysqli_error($db_con));
        $rowcount = mysqli_num_rows($storageCopyNameDoc);
        $result = mysqli_fetch_all($storageCopyNameDoc);
//        echo '<pre>';
//        print_r($result);
//        echo '</pre>';
//        die('okkk');
        $insertTo = mysqli_query($db_con, "insert into tbl_storage_level (sl_name, sl_parent_id, sl_depth_level) values('$copyName', '$lastCopyToId', '$copyToLevel')") or die('Error ss:' . mysqli_error($db_con));
        $insertId = mysqli_insert_id($db_con);


        //////////@D////////////////////////
        $updir_from = getStoragePath($db_con, $rwstorageCopyName['sl_parent_id'], $rwstorageCopyName['sl_depth_level']);
        $updir_to = getStoragePath($db_con, $rwstorageCopyTo['sl_parent_id'], $rwstorageCopyTo['sl_depth_level']);
        //echo ($updir_to);
        if (!empty($updir_from)) {
            $updir_from = $updir_from . '/';
        } else {
            $updir_from = '';
        }
        if (!empty($updir_to)) {
            $updir_to = $updir_to . '/';
        } else {
            $updir_to = '';
        }

        $uploaddir_from = $updir_from . $copyName;
        $dir_from = 'extract-here/' . $uploaddir_from;
        $uploaddir_to = $updir_to . $copyToName;
        $folderpath = $updir_to . $copyToName;
        $dir_to = 'extract-here/' . $uploaddir_to . '/' . $copyName;

        if (!is_dir(dirname($dir_to))) {
            mkdir(dirname($dir_to), 0777, true);
        }
        ////////////////////@D////////////////////////
        for ($i = 0; $i < $rowcount; $i++) {
            $docId = $result[$i][0];
            unset($result[$i][0]); //Remove ID from array
            // print_r($result[$i]); 
            $qrystr = " INSERT INTO tbl_document_master";

            $qrystr .= " VALUES (null, '" . implode("', '", array_values($result[$i])) . "')";

            $insertCopyDoc = mysqli_query($db_con, $qrystr) or die('Error insert: ' . mysqli_error($db_con));

            $insertCopyDocId = mysqli_insert_id($db_con);
            $newdocname = base64_encode($insertCopyDocId);

            $updateDoc = "update tbl_document_master set doc_name = '$insertId' where doc_id = '$insertCopyDocId'";
            mysqli_query($db_con, $updateDoc) or die('Error' . mysqli_error($db_con));

            copy('thumbnail/' . base64_encode($docId) . '.jpg', 'thumbnail/' . $newdocname . '.jpg');

            $doc_path = $result[$i][4];
            $doc_pathArr = explode("/", $doc_path);
            $doc_Path_copy_to = $copyName . '/' . end($doc_pathArr);
            $path = substr($doc_path, 0, strrpos($doc_path, '/') + 1);
            $pathtxt = 'extract-here/' . $path . 'TXT/' . $docId . '.txt';
            if (!is_dir('extract-here/' . $folderpath . '/TXT/')) {
                mkdir('extract-here/' . $folderpath . '/TXT/', 0777, TRUE) or die(print_r(error_get_last()));
            }
            $newTxtPath = 'extract-here/' . $folderpath . '/TXT/' . $insertCopyDocId . '.txt';
            if (FTP_ENABLED) {

                $ftp = new ftp();
                $ftp->conn("$fileserver", "$port", "$ftpUser", "$ftpPwd");
                if ($ftp->get('extract-here/' . $doc_Path_copy_to, ROOT_FTP_FOLDER . '/' . $doc_path)) {

                    if ($ftp->put(ROOT_FTP_FOLDER . '/' . $doc_Path_copy_to, 'extract-here/' . $doc_Path_copy_to)) {
                        if (file_exists($pathtxt)) {
                            copy($pathtxt, $newTxtPath);
                        }
                        unlink('extract-here/' . $doc_Path_copy_to);
                    }
                }
                $arr = $ftp->getLogData();
                if ($arr['error'] != "") {
                    //echo '<h2>Error:</h2>' . implode('<br />', $arr['error']);
                }
            } else {
                if (file_exists($pathtxt)) {
                    copy($pathtxt, $newTxtPath);
                }
            }
        }
        $sql_child = "select * FROM tbl_storage_level WHERE sl_parent_id = '$toCopyFolderId' ";

        $sql_child_run = mysqli_query($db_con, $sql_child) or die('Error update:' . mysqli_error($db_con));
        if (mysqli_num_rows($sql_child_run) > 0) {
            //echo "$child"; die;
            while ($rwchild = mysqli_fetch_assoc($sql_child_run)) {

                $child = $rwchild['sl_id'];

                $childCopyname = $rwchild['sl_name'];

                copyStorage($child, $insertId, null, $date, $host, $fileserver, $port, $ftpUser, $ftpPwd, $lang, $db_con);
            }
        }
        $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`, `sl_id`, `action_name`, `start_date`,`system_ip`, `remarks`) values ('" . $_SESSION['cdes_user_id'] . "', '" . $_SESSION['admin_user_name'] . ' ' . $_SESSION['admin_user_last'] . "','$toCopyFolderId','Storage Copied','$date','$host','Folder $copyName copied to $copyToName folder')") or die('error : ' . mysqli_error($db_con));

        echo'<script>taskSuccess("' . $_SERVER['REQUEST_URI'] . '","' . $lang['scs'] . '");</script>';
    } else {
        $storageCopyTo = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$lastCopyToId'") or die('Error:' . mysqli_error($db_con));
        $rwstorageCopyTo = mysqli_fetch_assoc($storageCopyTo);
        $copyToName = $rwstorageCopyTo['sl_name'];
        //$log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`, `sl_id`, `action_name`, `start_date`,`system_ip`, `remarks`) values ('$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]','$toCopyFolderId','Storage Copied','$date','$host','Storage $copyName exist in $copyToName rename to copy .')") or die('error : ' . mysqli_error($db_con));

        echo'<script>taskFailed("' . $_SERVER['REQUEST_URI'] . '","' . $lang['Storage_already_exist'] . '");</script>';
    }
}


//find next task to asssin doc
function nextTaskAsin($nextTaskOrd, $TskWfId, $TskStpId, $docId, $date, $user_id, $db_con, $taskRemark, $ticket) {
    //echo "select * from tbl_task_master where task_order='$nextTaskOrd' and step_id='$TskStpId'";
    $getNextTask = mysqli_query($db_con, "select * from tbl_task_master where task_order='$nextTaskOrd' and step_id='$TskStpId'") or die('Error:' . mysqli_error($db_con));
    $rwgetNextTask = mysqli_fetch_assoc($getNextTask);

    if (mysqli_num_rows($getNextTask) > 0) {

        $NextTaskId = $rwgetNextTask['task_id'];

        $getNextTaskDl = mysqli_query($db_con, "select * from tbl_task_master where task_id='$NextTaskId'") or die('Error:' . mysqli_error($db_con));
        $rwgetNextTaskDl = mysqli_fetch_assoc($getNextTaskDl);

        if ($rwgetNextTaskDl['deadline_type'] == 'Date' || $rwgetNextTaskDl['deadline_type'] == 'Hrs') {

            $endDateNexTsk = date('Y-m-d H:i:s', (strtotime($date) + $rwgetNextTaskDl['deadline'] * 60));
        }
        if ($rwgetNextTaskDl['deadline_type'] == 'Days') {

            $endDateNexTsk = date('Y-m-d H:i:s', (strtotime($date) + $rwgetNextTaskDl['deadline'] * 24 * 60 * 60));
        }
        if (!empty($docId) && $docId != 0) {
            $insertInNextTask = mysqli_query($db_con, "INSERT INTO tbl_doc_assigned_wf(task_id, doc_id, start_date, end_date, task_status, assign_by, NextTask, task_remarks,ticket_id) VALUES ('$NextTaskId', '$docId', '$date', '$endDateNexTsk', 'Pending', '$user_id', 2, '$taskRemark','$ticket')") or die('Erorr: ff' . mysqli_error($db_con));
        } else {
            $insertInNextTask = mysqli_query($db_con, "INSERT INTO tbl_doc_assigned_wf(task_id, start_date, end_date, task_status, assign_by, NextTask, task_remarks,ticket_id) VALUES ('$NextTaskId', '$date', '$endDateNexTsk', 'Pending', '$user_id', 2, '$taskRemark','$ticket')") or die('Erorr: ff' . mysqli_error($db_con));
        }
    } else {
        $getStpOr = mysqli_query($db_con, "select * from tbl_step_master where workflow_id='$TskWfId' and step_id='$TskStpId'") or die('Error:' . mysqli_error($db_con));
        $rwgetStpOr = mysqli_fetch_assoc($getStpOr);
        $getStpOrd = $rwgetStpOr['step_order'];
        $nextStpOrd = $getStpOrd + 1;
        $getNexStp = mysqli_query($db_con, "select * from tbl_step_master where workflow_id='$TskWfId' and step_order='$nextStpOrd'") or die('Error:' . mysqli_error($db_con));
        $rwgetNexStp = mysqli_fetch_assoc($getNexStp);

        if (mysqli_num_rows($getNexStp) > 0) {


            $nextStpId = $rwgetNexStp['step_id'];
            $getNextTask1 = mysqli_query($db_con, "select * from tbl_task_master where step_id = '$nextStpId' ORDER BY task_order ASC LIMIT 1") or die('Error:' . mysqli_error($db_con));
            $rwgetNextTask1 = mysqli_fetch_assoc($getNextTask1);
            $getNexTskId = $rwgetNextTask1['task_id'];
            $getNexTskOrd = $rwgetNextTask1['task_order'];

            nextTaskAsin($getNexTskOrd, $TskWfId, $nextStpId, $docId, $date, $user_id, $db_con, $taskRemark, $ticket);
            // echo 'gg'; die;
            return;
        }
    }
}

//find next task to update user
function nextTaskToUpdate($cTaskOrd, $TskWfId, $TskStpId, $db_con) {
    
    
    $getNextTask = mysqli_query($db_con, "select * from tbl_task_master where step_id='$TskStpId' and task_order > $cTaskOrd limit 1") or die('Error:' . mysqli_error($db_con));

    $rwgetNextTask = mysqli_fetch_assoc($getNextTask);
    if (mysqli_num_rows($getNextTask) > 0) {
        $NextTaskId = $rwgetNextTask['task_id'];
        return $NextTaskId;
    } else {
        //echo "select * from tbl_step_master where workflow_id='$TskWfId' and step_id='$TskStpId'";
        $getStpOr = mysqli_query($db_con, "select * from tbl_step_master where workflow_id='$TskWfId' and step_id='$TskStpId'") or die('Error:' . mysqli_error($db_con));
        $rwgetStpOr = mysqli_fetch_assoc($getStpOr);
        $nextStpOrd = $rwgetStpOr['step_order'];
        $getNexStp = mysqli_query($db_con, "select * from tbl_step_master where workflow_id='$TskWfId' and step_order > $nextStpOrd limit 1") or die('Error Wf:' . mysqli_error($db_con));
        $rwgetNexStp = mysqli_fetch_assoc($getNexStp);

        if (mysqli_num_rows($getNexStp) > 0) {
            $cTaskOrd = 0;
            $TskStpId = $rwgetNexStp['step_id'];
            return nextTaskToUpdate($cTaskOrd, $TskWfId, $TskStpId, $db_con);
            /* $nextStpId = $rwgetNexStp['step_id'];
              $getNextTask1 = mysqli_query($db_con, "select * from tbl_task_master where step_id = '$nextStpId' ORDER BY task_order ASC LIMIT 1") or die('Error:' . mysqli_error($db_con));
              $rwgetNextTask1 = mysqli_fetch_assoc($getNextTask1);
              if(mysqli_num_rows($getNextTask1)){
              $getNexTskId = $rwgetNextTask1['task_id'];
              return $getNexTskId;
              }else{
              nextTaskToUpdate($getNexTskOrd, $TskWfId, $getNexTskId, $db_con);
              } */
        }
    }
}

//add new user to asign task
function addNewTskUsr($cTskId, $TskWfId, $TskStpId, $cTskOrd, $assiUsersAdd, $altrusrAdd, $supvsrAdd, $deadLineAdd, $deadlineType, $date, $db_con) {
    //echo $cTskOrd;
    $tskOrdNxt = $cTskOrd + 1;
    $taskName = 'ExtdTask' . $tskOrdNxt;

    $updateTaskOrder = mysqli_query($db_con, "UPDATE  tbl_task_master  SET task_order = task_order + 1 WHERE step_id = '$TskStpId' and workflow_id = '$TskWfId' and task_order >= '$tskOrdNxt'") or die('Error:' . mysqli_error($db_con));
    //echo $tskOrdNxt;
    $insertTaskOrder = mysqli_query($db_con, "insert into tbl_task_master (task_name, assign_user, alternate_user, supervisor, task_order, step_id, workflow_id, task_created_date, deadline, deadline_type) values('$taskName', '$assiUsersAdd','$altrusrAdd', '$supvsrAdd', '$tskOrdNxt', '$TskStpId', '$TskWfId', '$date', '$deadLineAdd', '$deadlineType')") or die('Error' . mysqli_error($db_con));
    $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,'User Order Assign in $taskName','$date',null,'$host',null)") or die('error : ' . mysqli_error($db_con));
}

// function for list folders in storage
//function storageLevelName($db_con, $slid, $lang) {
//    $store = mysqli_query($db_con, "select * from tbl_storage_level where sl_parent_id='$slid'");
//    while ($rwStore = mysqli_fetch_assoc($store)) {
//        $hasSub = mysqli_query($db_con, "select * from tbl_storage_level where sl_parent_id='$rwStore[sl_id]'");
//        if (mysqli_num_rows($hasSub) > 0) {
//
//
//            $contFile = mysqli_query($db_con, "select sum(doc_size) as total, count(doc_name) as count from tbl_document_master where FIND_IN_SET('$rwStore[sl_id]',doc_name) and flag_multidelete='1'") or die('Error:' . mysqli_error($db_con));
//            $rwcontFile = mysqli_fetch_assoc($contFile);
//            $totalFSize = $rwcontFile['total'];
//            $totalFSize = round($totalFSize / (1024 * 1024), 2);   //convert in kb
//            $numFile = $rwcontFile['count'];
//
//            echo '<a class="col-md-2" href="storage?id=' . urlencode(base64_encode($rwStore['sl_id'])) . '"title="'.$lang['no_of_file'] .' = '. $numFile .' ' .$lang['total_size'] . ' = '. $totalFSize . $lang['MB'].'"><i class="fa fa-folder dv"></i><div style="overflow:hidden; height:25px;" title="' . $rwStore['sl_name'] . '">' . $rwStore['sl_name'] . '</div></a>';
//
//            $string = strip_tags($string);
//
//            if (strlen($string) > 500) {
//
//                // truncate string
//                $stringCut = substr($string, 0, 500);
//
//                // make sure it ends in a word so assassinate doesn't become ass...
//                $string = substr($stringCut, 0, strrpos($stringCut, ' ')) . '... <a href="/this/story">Read More</a>';
//            }
//            echo $string;
//            '</a></span>';
//        } else {
//            $file = mysqli_query($db_con, "SELECT doc_id as total from tbl_document_master where doc_name='$rwStore[sl_id]' and flag_multidelete='1'");
//            if (mysqli_num_rows($file) > 0) {
//
//
//                $contFile = mysqli_query($db_con, "select sum(doc_size) as total, count(doc_name) as count from tbl_document_master where FIND_IN_SET('$rwStore[sl_id]',doc_name) and flag_multidelete='1'") or die('Error:' . mysqli_error($db_con));
//
//                $rwcontFile = mysqli_fetch_assoc($contFile);
//                $totalFSize = $rwcontFile['total'];
//                $totalFSize = round($totalFSize / (1024 * 1024), 2);
//                $numFile = $rwcontFile['count'];
//
//                echo'<a class="col-md-2" href="storage?id=' . urlencode(base64_encode($rwStore['sl_id'])) . '"title="'.$lang['no_of_file'] .' = '. $numFile .' ' .$lang['total_size'] . ' = '. $totalFSize . $lang['MB'].'"><i class="fa fa-folder dv"></i><div style="overflow:hidden; height:25px;" title="' . $rwStore['sl_name'] . '">' . $rwStore['sl_name'] . '</div></a>';
//            } else {
//                $contFile = mysqli_query($db_con, "select sum(doc_size) as total, count(doc_name) as count from tbl_document_master where FIND_IN_SET('$rwStore[sl_id]',doc_name) and flag_multidelete='1'") or die('Error:' . mysqli_error($db_con));
//
//                $rwcontFile = mysqli_fetch_assoc($contFile);
//                $totalFSize = $rwcontFile['total'];
//                $totalFSize = round($totalFSize / (1024 * 1024), 2);
//                $numFile = $rwcontFile['count'];
//                echo'<a class="col-md-2" href="storage?id=' . urlencode(base64_encode($rwStore['sl_id'])) . '" title="'.$lang['no_of_file'] .' = '. $numFile .' ' .$lang['total_size'] . ' = '. $totalFSize . $lang['MB'].'"><i class="fa fa-folder-o dv"></i><div style="overflow:hidden; height:25px;" title="' . $rwStore['sl_name'] . '">' . $rwStore['sl_name'] . '</div></a>';
//            }
//        }
//    }
//}

function storageLevelName($db_con, $slid, $lang) {
    $store = mysqli_query($db_con, "select * from tbl_storage_level where sl_parent_id='$slid' order by sl_name asc");
    while ($rwStore = mysqli_fetch_assoc($store)) {

        $hasSub = mysqli_query($db_con, "select * from tbl_storage_level where sl_parent_id='$rwStore[sl_id]'");
        if (mysqli_num_rows($hasSub) > 0) {


            global $numFile;
            global $totalFS;
            global $totalFl;
            global $numPag;
            $total = array();
            $numFile = 0;
            $totalFS = 0;
            $totalFl = 0;
            $numPag = 0;
            $total = findFolder($rwStore[sl_id]);
            //echo '<a class="col-md-2" href="storage?id=' . urlencode(base64_encode($rwStore['sl_id'])) . '"title="' . $lang['no_of_file'] . ' = ' . $numFile . ' ' . $lang['total_size'] . ' = ' . $totalFSize . $lang['MB'] . '"><i class="fa fa-folder dv"></i><div style="overflow:hidden; height:25px;" title="' . $rwStore['sl_name'] . '">' . $rwStore['sl_name'] . '</div></a>';
            echo '<a class="col-md-2 col-lg-2 col-sm-2 col-xs-2 view1" href="storage?id=' . urlencode(base64_encode($rwStore['sl_id'])) . '" title="' . $lang['no_of_file'] . ' ' . $total['files'] . ' ' . $lang['total_size'] . ' ' . $total['fileSize'] . ' MB"><i class="fa fa-folder dv"></i><div style="overflow:hidden; height:25px;" title="' . $rwStore['sl_name'] . '">' . $rwStore['sl_name'] . '</div></a>';
            echo '<a class="view2" style="display:none" href="storage?id=' . urlencode(base64_encode($rwStore['sl_id'])) . '" title="' . $lang['no_of_file'] . ' ' . $total['files'] . ' ' . $lang['total_size'] . ' ' . $total['fileSize'] . ' MB">' . $i . '. <i class="fa fa-folder "></i> ' . $rwStore['sl_name'] . ' <span class="pull-right">' . ($total['totalFolder'] - 1) . ' ' . $lang['folders'] . ',' . $total['files'] . ' ' . $lang['Files'] . ', ' . $total['numPages'] . ' ' . $lang['pages'] . '</span></a>';
            $string = strip_tags($string);

            if (strlen($string) > 500) {

                // truncate string
                $stringCut = substr($string, 0, 500);

                // make sure it ends in a word so assassinate doesn't become ass...
                $string = substr($stringCut, 0, strrpos($stringCut, ' ')) . '... <a href="/this/story">Read More</a>';
            }
            echo $string;
            '</a></span>';
        } else {
            $file = mysqli_query($db_con, "SELECT doc_id as total from tbl_document_master where doc_name='$rwStore[sl_id]' and flag_multidelete='1'");
            if (mysqli_num_rows($file) > 0) {
                $contFile = mysqli_query($db_con, "select sum(doc_size) as total, count(doc_name) as count from tbl_document_master where FIND_IN_SET('$rwStore[sl_id]',doc_name) and flag_multidelete='1'") or die('Error:' . mysqli_error($db_con));
                $sl_qury = mysqli_query($db_con, "SELECT sl_id FROM `tbl_storage_level`  where sl_parent_id='$rwStore[sl_id]'") or die('Error:' . mysqli_error($db_con));
                $slcount = mysqli_num_rows($sl_qury);
                $rwcontFile = mysqli_fetch_assoc($contFile);
                $totalFSize = $rwcontFile['total'];
                $totalFSize = round($totalFSize / (1024 * 1024), 2);
                $numFile = $rwcontFile['count'];
                $totalPages = $rwcontFile['numPages'];
                if (empty($totalPages)) {
                    $totalPages = 0;
                }
                // echo'<a class="col-md-2" href="storage?id=' . urlencode(base64_encode($rwStore['sl_id'])) . '"title="' . $lang['no_of_file'] . ' = ' . $numFile . ' ' . $lang['total_size'] . ' = ' . $totalFSize . $lang['MB'] . '"><i class="fa fa-folder dv"></i><div style="overflow:hidden; height:25px;" title="' . $rwStore['sl_name'] . '">' . $rwStore['sl_name'] . '</div></a>';
                echo'<a class="col-md-2 col-lg-2 col-sm-2 col-xs-2 view1" href="storage?id=' . urlencode(base64_encode($rwStore['sl_id'])) . '" title="' . $lang['no_of_file'] . ' ' . $numFile . ' ' . $lang['total_size'] . ' ' . $totalFSize . ' MB"><i class="fa fa-folder dv"></i><div style="overflow:hidden; height:25px;" title="' . $rwStore['sl_name'] . '">' . $rwStore['sl_name'] . '</div></a>';
                echo'<a class="view2" style="display:none" href="storage?id=' . urlencode(base64_encode($rwStore['sl_id'])) . '" title="' . $lang['no_of_file'] . ' ' . $numFile . '' . $lang['total_size'] . ' ' . $totalFSize . ' MB">' . $i . '. <i class="fa fa-folder"></i> ' . $rwStore['sl_name'] . ' <span class="pull-right"> ' . $slcount . ' ' . $lang['folders'] . ',' . $numFile . ' ' . $lang['' . $lang['Files'] . ''] . ', ' . $totalPages . ' ' . $lang['pages'] . '</span></a>';
            } else {
                $contFile = mysqli_query($db_con, "select sum(doc_size) as total, count(doc_name) as count, sum(noofpages) as numPages from tbl_document_master where FIND_IN_SET('$rwStore[sl_id]',doc_name) and flag_multidelete='1'") or die('Error:' . mysqli_error($db_con));
                $sl_qury = mysqli_query($db_con, "SELECT sl_id FROM `tbl_storage_level`  where sl_parent_id='$rwStore[sl_id]'") or die('Error:' . mysqli_error($db_con));
                $slcount = mysqli_num_rows($sl_qury);
                $rwcontFile = mysqli_fetch_assoc($contFile);
                $totalFSize = $rwcontFile['total'];
                $totalFSize = round($totalFSize / (1024 * 1024), 2);
                $numFile = $rwcontFile['count'];
                $totalPages = $rwcontFile['numPages'];
                if (empty($totalPages)) {
                    $totalPages = 0;
                }
                echo'<a class="col-md-2 col-lg-2 col-sm-2 col-xs-2 view1" href="storage?id=' . urlencode(base64_encode($rwStore['sl_id'])) . '" title="' . $lang['no_of_file'] . '' . $numFile . ' ' . $lang['total_size'] . ' ' . $totalFSize . ' MB"><i class="fa fa-folder-o dv"></i><div style="overflow:hidden; height:25px;" title="' . $rwStore['sl_name'] . '">' . $rwStore['sl_name'] . '</div></a>';
                echo'<a class="view2" style="display:none" href="storage?id=' . urlencode(base64_encode($rwStore['sl_id'])) . '" title="' . $lang['no_of_file'] . ' ' . $numFile . ' ' . $lang['total_size'] . ' ' . $totalFSize . ' MB">' . $i . '. <i class="fa fa-folder"></i> ' . $rwStore['sl_name'] . '<span class="pull-right">' . $slcount . ' ' . $lang['folders'] . ' ,' . $numFile . ' ' . $lang['Files'] . ', ' . $totalPages . ' ' . $lang['pages'] . '</span></a>';
            }
        }
    }
}

//for tree
//for tree
function storageLevelS($level, $db_con, $slid, $parentid, $slperm) {
    //echo "select * from tbl_storage_level where sl_depth_level='$level' and sl_id='$slperm' order by sl_name asc";

    $store = mysqli_query($db_con, "select * from tbl_storage_level where sl_depth_level='$level' and sl_id='$slperm'  and delete_status=0 order by sl_name asc");
    while ($rwStore = mysqli_fetch_assoc($store)) {
        $hasSub = mysqli_query($db_con, "select * from tbl_storage_level where sl_parent_id='$rwStore[sl_id]'  and delete_status=0");
        if (mysqli_num_rows($hasSub) > 0) {
            if ($rwStore['sl_id'] == $slid || $rwStore['sl_id'] == $parentid) {
                //if ($rwStore['sl_id'] == $slid) {
                ?>

                <li data-jstree='{"selected":true,"opened":true}' class="cheekced"><a href="storage?id=<?php
                    echo urlencode(base64_encode($rwStore['sl_id']));
                    ?>" ><i class="md md-storage"></i><?php echo $rwStore['sl_name']; ?></a> 
                    <ul>
                        <?php
                        storageSubLevelS($rwStore['sl_depth_level'] + 1, $rwStore['sl_id'], $db_con, $slid, $parentid);
                        ?>
                    </ul></li>
                <?php
            } else {
                ?>
                <li><a href="storage?id=<?php
                    echo urlencode(base64_encode($rwStore['sl_id']));
                    ?>"><i class="md md-storage"></i><?php echo $rwStore['sl_name']; ?></a> 
                    <ul>
                        <?php
                        storageSubLevelS($rwStore['sl_depth_level'] + 1, $rwStore['sl_id'], $db_con, $slid, $parentid);
                        ?>
                    </ul></li>        
                <?php
            }
        } else {
            if ($rwStore['sl_id'] == $slid) {
                ?>
                <li data-jstree='{"selected":true,"type":"file"}' class="cheekced"><a href="storageFiles?id=<?php
                    echo urlencode(base64_encode($rwStore['sl_id']));
                    ;
                    ?>"><i class="fa fa-folder"></i> <?php echo $rwStore['sl_name']; ?> </a></li>
                                                                                      <?php
                                                                                  } else {
                                                                                      ?>
                <li data-jstree='{"type":"file"}'><a href="storageFiles?id=<?php
                    echo urlencode(base64_encode($rwStore['sl_id']));
                    ;
                    ?>"><i class="fa fa-folder"></i> <?php echo $rwStore['sl_name']; ?> </a></li>
                                                     <?php
                                                 }
                                             }
                                         }
                                     }

                                     function storageSubLevelS($level, $slID, $db_con, $slid, $parentid) {
                                         $store = mysqli_query($db_con, "select * from tbl_storage_level where sl_parent_id='$slID' and delete_status=0 order by sl_name asc");
                                         while ($rwStore = mysqli_fetch_assoc($store)) {
                                             $hasSub = mysqli_query($db_con, "select * from tbl_storage_level where sl_parent_id='$rwStore[sl_id]' and delete_status=0");
                                             if (mysqli_num_rows($hasSub) > 0) {
                                                 if ($rwStore['sl_id'] == $slid || $rwStore['sl_id'] == $parentid) {
                                                     ?>
                <li data-jstree='{"selected":true,"opened":true}' class="cheekced"><a href="storage?id=<?php echo urlencode(base64_encode($rwStore['sl_id'])); ?>"><i class="fa fa-folder"></i><?php echo $rwStore['sl_name']; ?></a> <ul>
                        <?php
                        storageSubLevelS($rwStore['sl_depth_level'] + 1, $rwStore['sl_id'], $db_con, $slid, $parentid);
                        ?>
                    </ul></li>
                <?php
            } else {
                ?>
                <li ><a href="storage?id=<?php echo urlencode(base64_encode($rwStore['sl_id'])); ?>"><i class="fa fa-folder"></i><?php echo $rwStore['sl_name']; ?></a> <ul>
                        <?php
                        storageSubLevelS($rwStore['sl_depth_level'] + 1, $rwStore['sl_id'], $db_con, $slid, $parentid);
                        ?>
                    </ul></li>
                <?php
            }
        } else {
            if ($rwStore['sl_id'] == $slid) {
                ?>
                <li data-jstree='{"selected":true,"type":"file"}' class="cheekced"><a href="storageFiles?id=<?php
                    echo urlencode(base64_encode($rwStore['sl_id']));
                    ;
                    ?>"><i class="fa fa-folder"></i> <?php echo $rwStore['sl_name']; ?> </a></li>
                                                                                      <?php
                                                                                  } else {
                                                                                      ?>
                <li data-jstree='{"type":"file"}'><a href="storageFiles?id=<?php
                    echo urlencode(base64_encode($rwStore['sl_id']));
                    ;
                    ?>"><i class="fa fa-folder"></i> <?php echo $rwStore['sl_name']; ?> </a></li>
                <?php
            }
        }
    }
}

function humanTiming($time) {
    $tokens = array(
        31536000 => 'year',
        2592000 => 'month',
        604800 => 'week',
        86400 => 'day',
        3600 => 'hour',
        60 => 'minute',
        1 => 'second'
    );

    $result = '';
    $counter = 1;
    foreach ($tokens as $unit => $text) {
        if ($time < $unit)
            continue;
        if ($counter > 2)
            break;

        $numberOfUnits = floor($time / $unit);
        $result .= "$numberOfUnits $text ";
        $time -= $numberOfUnits * $unit;
        ++$counter;
    }

    return "{$result}";
}

function copystoragefiles($toCopyFolderId, $lastCopyToId, $toCopyFolderName, $date, $host, $copy_doc_ids) {

    global $db_con;

    $storageCopyName = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$toCopyFolderId'") or die('Error: 1' . mysqli_error($db_con));

    $rwstorageCopyName = mysqli_fetch_assoc($storageCopyName);

    $copyName = $rwstorageCopyName['sl_name'];

    if ($toCopyFolderName) {
        $copyName = $toCopyFolderName;
    }


    $storageCopyToCheck = mysqli_query($db_con, "select * from tbl_storage_level where sl_parent_id='$lastCopyToId' AND sl_name = '$copyName'") or die('Error:' . mysqli_error($db_con));

    // echo mysqli_num_rows($storageCopyToCheck); die;

    if (mysqli_num_rows($storageCopyToCheck) < 1) {

        $storageCopyTo = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$lastCopyToId'") or die('Error:' . mysqli_error($db_con));

        $rwstorageCopyTo = mysqli_fetch_assoc($storageCopyTo);

        $copyToName = $rwstorageCopyTo['sl_name'];
        $copyToLevel = $rwstorageCopyTo['sl_depth_level'];
        $copyToLevel = $copyToLevel + 1;

        $insertTo = mysqli_query($db_con, "insert into tbl_storage_level (sl_name, sl_parent_id, sl_depth_level) values('$copyName', '$lastCopyToId', '$copyToLevel')") or die('Error ss:' . mysqli_error($db_con));
        $insertId = mysqli_insert_id($db_con);

        //insert doc of copy record

        $storageCopyNameDoc = mysqli_query($db_con, "select * from tbl_document_master where doc_name='$toCopyFolderId'") or die('Error: ' . mysqli_error($db_con));

        $rowcount = mysqli_num_rows($storageCopyNameDoc);

        $result = mysqli_fetch_all($storageCopyNameDoc);

        for ($i = 0; $i < $rowcount; $i++) {

            unset($result[$i][0]); //Remove ID from array
            // print_r($result[$i]); 
            $qrystr = " INSERT INTO tbl_document_master";

            $qrystr .= " VALUES (null, '" . implode("', '", array_values($result[$i])) . "')";

            $insertCopyDoc = mysqli_query($db_con, $qrystr) or die('Error insert: ' . mysqli_error($db_con));

            $insertCopyDocId = mysqli_insert_id($db_con);

            $updateDoc = "update tbl_document_master set doc_name = '$insertId' where doc_id = '$insertCopyDocId'";
            mysqli_query($db_con, $updateDoc) or die('Error' . mysqli_error($db_con));
        }
        $sql_child = "select * FROM tbl_storage_level WHERE sl_parent_id = '$toCopyFolderId' ";

        $sql_child_run = mysqli_query($db_con, $sql_child) or die('Error update:' . mysqli_error($db_con));



        if (mysqli_num_rows($sql_child_run) > 0) {

            //echo "$child"; die;

            while ($rwchild = mysqli_fetch_assoc($sql_child_run)) {

                $child = $rwchild['sl_id'];

                $childCopyname = $rwchild['sl_name'];

                copyStorage($child, $insertId, null, $date, $host);
            }
        }



        $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,'$toCopyFolderId','Storage level $copyName copy to $copyToName.','$date',null,'$host','')") or die('error : ' . mysqli_error($db_con));
        echo'<script>taskSuccess("storage","Storage Copy Successfully !");</script>';
    } else {
        $storageCopyTo = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$lastCopyToId'") or die('Error:' . mysqli_error($db_con));
        $rwstorageCopyTo = mysqli_fetch_assoc($storageCopyTo);
        $copyToName = $rwstorageCopyTo['sl_name'];

        $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,'$toCopyFolderId','Storage $copyName exist in $copyToName rename to copy .','$date',null,'$host','')") or die('error : ' . mysqli_error($db_con));
        echo'<script>taskFailed("storage","Storage already exist. please rename storage before copy storage.");</script>';
    }
}

function FindSuperParentId($db_con) {
    $supersl = mysqli_query($db_con, "SELECT * FROM `tbl_storage_level`");
    $i = 0;
    while ($row_sl = mysqli_fetch_array($supersl)) {
        if ($i == 0) {
            $superParentId = $row_sl['sl_id'];
        }
        $i++;
    }
    return $superParentId;
}

function convertIntoBytesMethod($size) {
//        $newSize= str_replace(" ", "-", $size);
//        $type=explode("-",$newSize);
    //print_r($type);
    if ($type[1] == "MB") {
        $bytes = 1000 * 1000 * $size;
    } else {
        $bytes = 1000 * 1000 * 1000 * $size;
    }


    return $bytes;
}

function remaingSizeConvert($bytes) {
    if ($bytes >= 1000000000000) {
        $bytes = number_format($bytes / 1000000000000, 2) . ' TB';
    } elseif ($bytes >= 1000000000) {
        $bytes = number_format($bytes / 1000000000, 2) . ' GB';
    } elseif ($bytes >= 1000000) {
        $bytes = number_format($bytes / 1000000, 2) . ' MB';
    } elseif ($bytes >= 1000) {
        $bytes = number_format($bytes / 1000, 2) . ' KB';
    } elseif ($bytes > 1) {
        $bytes = $bytes . ' bytes';
    } elseif ($bytes == 1) {
        $bytes = $bytes . ' bytes';
    } else {
        $bytes = '0 bytes';
    }

    return $bytes;
}

function decryptLicenseKey($licenseKey) {
    $key = '987654123';
    $c = base64_decode($licenseKey);
    $ivlen = openssl_cipher_iv_length($cipher = "AES-128-CBC");
    $iv = substr($c, 0, $ivlen);
//$hmac = substr($c, $ivlen, $sha2len=32);
    $ciphertext_raw = substr($c, $ivlen/* +$sha2len */);
    $original_plaintext = openssl_decrypt($ciphertext_raw, $cipher, $key, $options = OPENSSL_RAW_DATA, $iv);
    return $original_plaintext;
}

//@dv 23-02-2019 function for tiff viewer single and multi-page tiff
function tiff2pdf($file_tif, $file_pdf) {

    $convertToPdf = exec('convert ' . $file_tif . ' ' . $file_pdf, $error, $exit_code);
    //print_r($error);

    if ($exit_code == 0) {

        return TRUE;
    } else {

        return FALSE;
    }
}

function findFolder($slperm) {
    global $list1;
    //$list1 = array();
    global $db_con;
    global $numFile;
    global $totalFS;
    global $totalFl;
    global $numPag;
    $contFile = mysqli_query($db_con, "select sum(doc_size) as total, count(doc_id) as count,sum(noofpages) as numPage from tbl_document_master where substring_index(doc_name,'_',1) = '$slperm' ") or die('Error :' . mysqli_error($db_con));
    $rwcontFile = mysqli_fetch_assoc($contFile);
    $totalFSize1 = $rwcontFile['total'];
    $totalFS += round($totalFSize1 / (1024 * 1024), 2);
    $numFile += $rwcontFile['count'];
    $numPag += $rwcontFile['numPage'];
    $list1['numPages'] = $numPag;
    $list1["files"] = $numFile;
    $list1["fileSize"] = $totalFS;
    //echo $totalFl.'/';
    if (!empty($slperm)) {
        $totalFl += 1;
    }
    $list1["totalFolder"] = $totalFl;

    $sql_child = "select * FROM tbl_storage_level WHERE sl_parent_id = '$slperm' ";
    $sql_child_run = mysqli_query($db_con, $sql_child) or die('Error: ' . mysqli_error($db_con));
    if (mysqli_num_rows($sql_child_run) > 0) {

        while ($rwchild = mysqli_fetch_assoc($sql_child_run)) {

            $child = $rwchild['sl_id'];
            $clagain = findFolder($child);
        }
    }
    return $list1;
}

function getPasswordPolicy($db_con) {
    $pwdPolicy = mysqli_query($db_con, "select * from tbl_pass_policy") or die("Error : " . mysqli_error($db_con));
    $rwpwdPolicy = mysqli_fetch_assoc($pwdPolicy);
    $rwpwdPolicy['passExpiry'] = $rwpwdPolicy['edate'];
    $rwpwdPolicy['passExpdays'] = $rwpwdPolicy['edate'] * 24 * 60 * 60;
    return $rwpwdPolicy;
}

function findsubfolder($SlIds, $db_con) {
    global $folderperms;
    $sllevel = mysqli_query($db_con, "select * from tbl_storage_level where sl_id in($SlIds) and delete_status=0 order by sl_name asc");
    while ($rwfolderperm = mysqli_fetch_assoc($sllevel)) {
        $folderperms[] = $rwfolderperm['sl_id'];

        $sllevel1 = mysqli_query($db_con, "select * from tbl_storage_level where sl_parent_id='" . $rwfolderperm['sl_id'] . "' and delete_status=0 order by sl_name asc");

        if (mysqli_num_rows($sllevel1) > 0) {
            $childarray = array();
            while ($rowCh = mysqli_fetch_assoc($sllevel1)) {
                $childarray[] = $rowCh['sl_id'];
            }
            $childIds = implode(",", $childarray);
            findsubfolder($childIds, $db_con);
        }
    }

    return $folderperms;
}
function change_pdf_version($fileNameWithPath) {
    // echo $fileNameWithPath;
    // exit;
    // $fileNameWithPath;
    $mypdf = fopen($fileNameWithPath, "r");
    // exit;
    $first_line = fgets($mypdf);
    fclose($mypdf);
    $open_path = explode('.', $fileNameWithPath);
    $newFileNameWithPath = '..' . $open_path[2] . '1.' . $open_path[3];
    // exit;
    // extract number such as 1.4,1.5 from first read line of pdf file
    preg_match_all('!\d+!', $first_line, $matches);
    // save that number in a variable
    $pdfversion = implode('.', $matches[0]);
    //echo$pdfversion;
    if ($pdfversion > "1.4" || $pdfversion < "1.4") {
        //for both windows & linux
        // shell_exec('"' . CHANGE_PDF_VERSION . '" -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dNOPAUSE -dQUIET -dBATCH -sOutputFile="' . $newFileNameWithPath . '" "' . $fileNameWithPath . '"');

        //for windows only
        shell_exec('"C:\gs\bin\gswin64c.exe" -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dNOPAUSE -dQUIET -dBATCH -sOutputFile="' . $newFileNameWithPath . '" "' . $fileNameWithPath . '"');
        //for linux only      
        //shell_exec('gs -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dNOPAUSE -dQUIET -dBATCH -sOutputFile="' . $newFileNameWithPath . '" "' . $fileNameWithPath . '"');

        // unlink($fileNameWithPath);
        return $newFileNameWithPath;
        // echo $newFileNameWithPath;
    }
    else {
        return $fileNameWithPath;
        // echo $fileNameWithPath;
    }
}
function changePdfVersion1($fileNameWithPath) {

    $fileNameWithPath;
    $mypdf = fopen($fileNameWithPath, "r");
    $first_line = fgets($mypdf);
    fclose($mypdf);
    $open_path = explode('.', $fileNameWithPath);
    // require_once '././extract-here/test.php';
    // die("rrrrr");
    $newFileNameWithPath = '././' . $open_path[0] . '1.' . $open_path[1];
    // extract number such as 1.4,1.5 from first read line of pdf file
    preg_match_all('!\d+!', $first_line, $matches);
    // save that number in a variable
    $pdfversion = implode('.', $matches[0]);
   
    
    if ($pdfversion > "1.4" || $pdfversion < "1.4") {
        //for both windows & linux
        // error_reporting(E_ALL);
        ini_set('max_execution_time', 300);
        print_r(shell_exec('"' . CHANGE_PDF_VERSION . '" -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dNOPAUSE -dQUIET -dBATCH -sOutputFile="' . $newFileNameWithPath . '" "' . $fileNameWithPath . '"'));

        //for windows only
        //shell_exec('"C:\gs\bin\gswin64c.exe" -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dNOPAUSE -dQUIET -dBATCH -sOutputFile="' . $newFileNameWithPath . '" "' . $fileNameWithPath . '"');
        //for linux only      
        //shell_exec('gs -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dNOPAUSE -dQUIET -dBATCH -sOutputFile="' . $newFileNameWithPath . '" "' . $fileNameWithPath . '"');
       
        // unlink($fileNameWithPath);
        return array("status"=>"new","file"=>$newFileNameWithPath);
    } else {

        return array("status"=>"old","file"=>$fileNameWithPath);
    }
}
function documentSharenotificationtoUsers($db_con, $shareDocId) {
    $html = '<table border="1" cellpacing="2" cellpadding="8" style="border-collapse : collapse;">';
    $html .= '<tr>';
    $html .= '<th>SNo.</th>';
    $html .= '<th>Storage Name</th>';
    $html .= '<th>Document Name</th>';
    $html .= '<th>Shared By</th>';
    $html .= '<th>Shared Datetime</th>';
    $html .= '</tr>';
    $i = 1;
    $getdocument = mysqli_query($db_con, "SELECT * FROM `tbl_document_master` WHERE doc_id in($shareDocId)");
    while ($rwgetdocument = mysqli_fetch_assoc($getdocument)) {

        $slid = $rwgetdocument['doc_name'];
        $storageName = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$slid'");
        $rwstorageName = mysqli_fetch_assoc($storageName);
        mysqli_set_charset($db_con, "utf8");
        $uploadedby = mysqli_query($db_con, "select first_name,last_name from tbl_user_master where user_id='" . $rwgetdocument['uploaded_by'] . "'");
        $rwuploadedby = mysqli_fetch_assoc($uploadedby);

        $html .= '<tr>';
        $html .= '<td>' . $i . '.' . '</td>';
        $html .= '<td>' . $rwstorageName['sl_name'] . '</td>';
        $html .= '<td>' . $rwgetdocument['old_doc_name'] . '</td>';
        $html .= '<td>' . $_SESSION['admin_user_name'] . ' ' . $_SESSION['admin_user_last'] . '</td>';
        $html .= '<td>' . date('d-m-Y H:i:s') . '</td>';

        $html .= '</tr>';
        $i++;
    }
    $html .= '</table>';
    return $html;
}

// function tiffTopdf($file_tif, $file_pdf) {

// echo "convert  $file_tif  $file_pdf 2>&1";
//     $convertToPdf = exec("convert  $file_tif  $file_pdf 2>&1", $error, $exit_code);
//     //print_r($error);
//     if ($exit_code == 0) {
//         return TRUE;
//     } else {
//         return FALSE;
//     }
// }

function tiffToPdf($file_tif, $file_pdf) {
    // Create a new Imagick object
    $imagick = new Imagick();
    
    try {
        // Read the TIFF file
        $imagick->readImage($file_tif);

        // Set compression and quality options for PDF
        $imagick->setImageFormat('pdf');
        $imagick->setCompressionQuality(100);

        // Write the PDF file
        $imagick->writeImages($file_pdf, true);
        
        // Free up resources
        $imagick->clear();
        $imagick->destroy();

        return true; // Conversion successful
    } catch (ImagickException $e) {
        // Handle any exceptions (e.g., invalid file format, permission issues)
        error_log("Error converting TIFF to PDF: " . $e->getMessage());
        return false; // Conversion failed
    }
}

function loginWithoutOTP($db_con, $data, $host, $date) {
    if (empty($data['last_active_login'])) {
        header("location:pwd-reset");
        $_SESSION['temp_user_id'] = $data['user_id'];
        $_SESSION['admin_user_name'] = $data['first_name'];
        $_SESSION['admin_user_last'] = $data['last_name'];
        $_SESSION['designation'] = $data['designation'];
        $_SESSION['lastLogin'] = $data['last_active_login'];
        $_SESSION['adminMail'] = $data['user_email_id'];
        $_SESSION['lang'] = $data['lang'];
    } else {
        $_SESSION['cdes_user_id'] = $data['user_id'];
        $_SESSION['admin_user_name'] = $data['first_name'];
        $_SESSION['admin_user_last'] = $data['last_name'];
        $_SESSION['designation'] = $data['designation'];
        $_SESSION['lastLogin'] = $data['last_active_login'];
        $_SESSION['adminMail'] = $data['user_email_id'];
        $_SESSION['lang'] = $data['lang'];
        //user profile type
        $privileges = array();
        $priv = mysqli_query($db_con, "select * from tbl_bridge_role_to_um where find_in_set('" . $data['user_id'] . "',user_ids)"); //or die('Error' . mysqli_error($db_con));
        while ($rwPriv = mysqli_fetch_assoc($priv)) {
            array_push($privileges, $rwPriv['role_id']);
        }
        $privileges = array_filter($privileges, function($value) {
            return $value !== '';
        });
        //print_r($privileges);
        $_SESSION['admin_privileges'] = array_unique($privileges);
        //print_r($_SESSION['admin_privileges']);
        $_SESSION['notified'] = array();
        $_SESSION['notified1'] = array();
        $_SESSION['notified2'] = array();
        $remoteHost = $host;
        $remoteHost = mysqli_real_escape_string($db_con, $remoteHost);
        if (isset($_SESSION['lang']) && !empty($_SESSION['lang'])) {
            $UpdateLang = mysqli_query($db_con, "UPDATE tbl_user_master SET lang='" . $_SESSION['lang'] . "' WHERE user_id='" . $_SESSION['cdes_user_id'] . "'");
        } else {
            $_SESSION['lang'] = "English";
        }
        $lastlogin = mysqli_query($db_con, "select start_date from tbl_ezeefile_logs where id in (select max(id) from tbl_ezeefile_logs where user_id = '$data[user_id]' and action_name = 'Login/Logout')"); //or die('Error' . mysqli_error($db_con));
        $rwlastlogin = mysqli_fetch_assoc($lastlogin);
        //update usermaster current logout
        $update = mysqli_query($db_con, "update tbl_user_master set current_login_status='1',system_ip='$remoteHost', last_active_login='$rwlastlogin[start_date]'  where user_id='$data[user_id]'"); //or die('Error : ' . mysqli_error($db_con));
        //update log
        //sk@120219:restrict up to two ip.
        $lip = "$remoteHost/$ip";
        $ipos = strpos($lip, '/', strpos($lip, '/') + 1);
        $login_logout_ip = ($ipos ? substr($lip, 0, $ipos) : $lip);
        
        $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$data[user_id]', '$data[first_name] $data[last_name]',null,null,'Login/Logout','$date',null,'$login_logout_ip','')") or die('error : ' . mysqli_error($db_con));

        unset($_SESSION['data']);
        if (isset($_GET['ref'])) {
            $ref = $_GET['ref'];
            $ref = base64_decode(urldecode($ref));
            if ($ref == "") {

                header("location:index");
            } else {
                header("location:$ref");
            }
        } else {
            header("location:index");
        }
    }
}

function deleteSubFolders($db_con, $slpid, $fileserver, $port, $ftpUser, $ftpPwd, $checkdelete) {

    $result = mysqli_query($db_con, "select * from tbl_storage_level where sl_parent_id='$slpid'");

    if (mysqli_num_rows($result) > 0) {

        while ($rows = mysqli_fetch_assoc($result)) {

            $sl_id = $rows['sl_id'];

            mysqli_set_charset($db_con, "utf8");
            $deletStorageName = $rows['sl_name'];
            $dirPath = "extract-here/" . str_replace(" ", "", $deletStorageName);
            if ($checkdelete == 'yes') {

                mysqli_query($db_con, "DELETE FROM tbl_storage_level WHERE sl_id='$sl_id'");

                deleteDocument($db_con, $sl_id, $dirPath, $fileserver, $port, $ftpUser, $ftpPwd);

                deleteSubFolders($db_con, $sl_id, $fileserver, $port, $ftpUser, $ftpPwd, $checkdelete);
            } else {

                mysqli_query($db_con, "UPDATE tbl_storage_level set delete_status=1 WHERE sl_id='$sl_id'");

                moveFilesInRecycleBin($db_con, $sl_id, 2);
            }
        }
    } else {
        
    }
}

function deleteDocument($db_con, $slid, $folderpath, $fileserver, $port, $ftpUser, $ftpPwd) {

    $getDocPath = mysqli_query($db_con, "select * from tbl_document_master where substring_index(doc_name, '_', 2)='$slid'") or die('Error:' . mysqli_error($db_con));

    while ($rowd = mysqli_fetch_assoc($getDocPath)) {

        $id = $rowd['doc_id'];

        $noofpages = $rowd['noofpages'];

        $path = substr($rowd['doc_path'], 0, strrpos($rowd['doc_path'], '/') + 1);

        $pathtxt = 'extract-here/' . $path . 'TXT/' . $id . '.txt';

        $filePath = $rowd['doc_path'];

        $del = mysqli_query($db_con, "DELETE FROM tbl_document_master WHERE doc_id ='$id'") or die('Error:' . mysqli_error($db_con));

        $delDocShare = mysqli_query($db_con, "DELETE FROM tbl_document_share WHERE doc_ids ='$id'") or die('Error:' . mysqli_error($db_con));
        if ($del) {
            if (FTP_ENABLED) {
                $ftp = new ftp();
                $ftp->conn("$fileserver", "$port", "$ftpUser", "$ftpPwd");
                $ftp->singleFileDelete('DMS/' . ROOT_FTP_FOLDER . '/' . $filePath);
                unlink('DMS/' . ROOT_FTP_FOLDER . '/' . $filePath);
                $arr = $ftp->getLogData();
                if ($arr['error'] != "") {
                    echo '<h2>Error:</h2>' . implode('<br />', $arr['error']);
                }
                unlink($path);
            } else {

                if (file_exists('extract-here/' . $filepath)) {

                    unlink('extract-here/' . $filePath);
                }
            }

            if (file_exists('extract-here/' . $filepath)) {

                unlink('extract-here/' . $filePath);
            }
            if ($noofpages > 0) {

                for ($i = 0; $i < $noofpages; $i++) {

                    $pathtxt = 'extract-here/' . $path . 'TXT/' . $id . '/' . $i . 'txt';

                    if (file_exists($pathtxt)) {

                        unlink($pathtxt);
                    }
                }
            }
        }
    }


    rmdir($folderpath);
}



// function deleteSubFolders($db_con, $slpid, $fileManager, $checkdelete){

//     $result = mysqli_query($db_con, "select * from tbl_storage_level where sl_parent_id='$slpid'");

//     if(mysqli_num_rows($result)>0){

//         while($rows = mysqli_fetch_assoc($result)){
			
// 			$slname = str_replace(" ", "", $rows['sl_name']);
			
// 			$updir = getStoragePath($db_con, $rows['sl_parent_id'], $rows['sl_depth_level']);

// 			if(!empty($updir)){
// 				$updir = $updir . '/';
// 			}else{
// 				$updir = '';
// 			}

//             $sl_id = $rows['sl_id'];

//             //delStrg($sl_id, $fileserver, $port, $ftpUser, $ftpPwd);

//             mysqli_set_charset($db_con, "utf8");
//             $deletStorageName = $rows['sl_name'];
//             $dirPath = "extract-here/" .$updir.$slname;
			
//              if($checkdelete=='yes'){

//                 mysqli_query($db_con, "DELETE FROM tbl_storage_level WHERE sl_id='$sl_id'");

//                 deleteDocument($db_con, $sl_id, $dirPath, $fileManager);

//                 deleteSubFolders($db_con, $sl_id, $fileManager, $checkdelete);

//             }else{

//                 mysqli_query($db_con, "UPDATE tbl_storage_level set delete_status=1 WHERE sl_id='$sl_id'");

//                 moveFilesInRecycleBin($db_con, $sl_id, 3);

//             }

//         }

//     }else{


//     }
// }


// function deleteDocument($db_con, $slid, $folderpath, $fileManager){
	
//     $getDocPath = mysqli_query($db_con, "select * from tbl_document_master where substring_index(doc_name, '_', 2)='$slid'") or die('Error:' . mysqli_error($db_con));
 
//     while ($rowd = mysqli_fetch_assoc($getDocPath)) {

//         $id = $rowd['doc_id'];

//         $noofpages = $rowd['noofpages'];

//         $path = substr($rowd['doc_path'], 0, strrpos($rowd['doc_path'], '/') + 1);

//         $pathtxt = 'extract-here/' . $path . 'TXT/' . $id . '.txt';

//         $filePath = $rowd['doc_path'];

//         $del = mysqli_query($db_con, "DELETE FROM tbl_document_master WHERE doc_id ='$id'") or die('Error:' . mysqli_error($db_con));

//         $delDocShare = mysqli_query($db_con, "DELETE FROM tbl_document_share WHERE doc_ids ='$id'") or die('Error:' . mysqli_error($db_con));
// 		$del= true;
//         if ($del) {
			
			
			
//             if (file_exists('extract-here/' . $filepath)) {
				
//                 unlink('extract-here/' . $filePath);
				
//             }else{
// 				// delete file from file server
// 				$fileManager->deleteFile(ROOT_FTP_FOLDER . '/' . $filePath);
// 			}
//             /* if ($noofpages > 0) {

//                 for ($i = 0; $i < $noofpages; $i++) {

//                     $pathtxt =   'extract-here/' . $path . 'TXT/' . $id . '/' . $i . 'txt'; */

//                     if (file_exists($pathtxt)) {

//                         unlink($pathtxt);
//                     }
//                // }
//             //}           
//         }
//     }
	
//     rmdir($folderpath);
// }

function txt_to_text($input_file) {
    $result = @file_get_contents($input_file);
    return $result;
}
function change_Pdf_Version11($fileNameWithPath)
{
    //  error_reporting(E_ALL);
    $fileNameWithPath;
    $mypdf = fopen($fileNameWithPath, "r");
    $first_line = fgets($mypdf);
    fclose($mypdf);
    $open_path = explode('.', $fileNameWithPath);

   
    $newFileNameWithPath = '../'.$open_path[2].'1.'.$open_path[3];
    // extract number such as 1.4,1.5 from first read line of pdf file
    preg_match_all('!\d+!', $first_line, $matches);
    // save that number in a variable
    $pdfversion = implode('.', $matches[0]);
   
    if ($pdfversion > "1.4" || $pdfversion < "1.4" || $pdfversion == "1.4") {
        shell_exec('"' . CHANGE_PDF_VERSION . '" -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dNOPAUSE -dQUIET -dBATCH -sOutputFile="' . $newFileNameWithPath . '" "' . $fileNameWithPath . '"');
        // unlink($fileNameWithPath);
        return $newFileNameWithPath;
    }
    else {
        return $fileNameWithPath;
    }
//     if($pdfversion > "1.4" || $pdfversion < "1.4" || $pdfversion == "1.4"){
		
// 		//echo '"C:\gs\gs9.5\bin\gswin64c.exe" -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dNOPAUSE -dQUIET -dBATCH -sOutputFile="'.$newFileNameWithPath.'" "'.$fileNameWithPath.'"';
		
// 		//die();
		
		
//         // shell_exec('"C:\gs\gs9.5\bin\gswin64c.exe" -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dNOPAUSE -dQUIET -dBATCH -sOutputFile="'.$newFileNameWithPath.'" "'.$fileNameWithPath.'"'); 

//         // echo 'gs -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dNOPAUSE -dQUIET -dBATCH -sOutputFile="'.$newFileNameWithPath.'" "'.$fileNameWithPath.'"';
        
//         shell_exec('gs -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dNOPAUSE -dQUIET -dBATCH -sOutputFile="'.$newFileNameWithPath.'" "'.$fileNameWithPath.'"');
// //echo 'gs -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dNOPAUSE -dQUIET -dBATCH -sOutputFile="'.$newFileNameWithPath.'" "'.$fileNameWithPath.'"';
// 		if(file_exists($newFileNameWithPath)){
// 			 unlink($fileNameWithPath);
// 		}
       
//         return $newFileNameWithPath;
//     }else{
//         return $fileNameWithPath;
//     }
}
function changePdfVersion($fileNameWithPath)
{
    $fileNameWithPath;
    $mypdf = fopen($fileNameWithPath, "r");
    $first_line = fgets($mypdf);
    fclose($mypdf);
    $open_path = explode('.', $fileNameWithPath);
    $newFileNameWithPath = '../'.$open_path[2].'1.'.$open_path[3];
    // extract number such as 1.4,1.5 from first read line of pdf file
    preg_match_all('!\d+!', $first_line, $matches);
    // save that number in a variable
    $pdfversion = implode('.', $matches[0]);
    //echo$pdfversion;
    if($pdfversion > "1.4" || $pdfversion < "1.4"){
		
		//echo '"C:\gs\gs9.5\bin\gswin64c.exe" -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dNOPAUSE -dQUIET -dBATCH -sOutputFile="'.$newFileNameWithPath.'" "'.$fileNameWithPath.'"';
		
		//die();
		
		
        //shell_exec('"C:\gs\gs9.5\bin\gswin64c.exe" -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dNOPAUSE -dQUIET -dBATCH -sOutputFile="'.$newFileNameWithPath.'" "'.$fileNameWithPath.'"'); 

        // echo 'gs -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dNOPAUSE -dQUIET -dBATCH -sOutputFile="'.$newFileNameWithPath.'" "'.$fileNameWithPath.'"';
        
        shell_exec('gs -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dNOPAUSE -dQUIET -dBATCH -sOutputFile="'.$newFileNameWithPath.'" "'.$fileNameWithPath.'"');
//echo 'gs -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dNOPAUSE -dQUIET -dBATCH -sOutputFile="'.$newFileNameWithPath.'" "'.$fileNameWithPath.'"';
		if(file_exists($newFileNameWithPath)){
			 unlink($fileNameWithPath);
		}
       
        return $newFileNameWithPath;
    }else{
        return $fileNameWithPath;
    }
}

function moveFilesInRecycleBin($db_con, $slid, $status){

    $getDocPath = mysqli_query($db_con, "UPDATE tbl_document_master SET flag_multidelete='$status' where substring_index(doc_name, '_', 2)='$slid'") or die('Error:' . mysqli_error($db_con));
 
    
}


function reStoreFolders($db_con, $slpid){

    $result = mysqli_query($db_con, "select * from tbl_storage_level where sl_parent_id='$slpid'");

    if(mysqli_num_rows($result)>0){

        while($rows = mysqli_fetch_assoc($result)){

            $sl_id = $rows['sl_id'];

            mysqli_query($db_con, "UPDATE tbl_storage_level set delete_status=0 WHERE sl_id='$sl_id'");

            moveFilesInRecycleBin($db_con, $sl_id, 1);

            reStoreFolders($db_con, $sl_id);

        }

    }else{

    }
	
    return true;
}

function moveStorageInRecycleBin($db_con, $slpid){

    $result = mysqli_query($db_con, "select * from tbl_storage_level where sl_parent_id='$slpid'");

    if(mysqli_num_rows($result)>0){

        while($rows = mysqli_fetch_assoc($result)){

            $sl_id = $rows['sl_id'];

            mysqli_query($db_con, "UPDATE tbl_storage_level set delete_status=1 WHERE sl_id='$sl_id'");

            moveStorageInRecycleBin($db_con, $sl_id);

        }

    }else{



    }


    return true;
}

function storageLevelNameByslid($db_con, $lang,$childInString, $searchText) {
    $store = mysqli_query($db_con, "SELECT * FROM tbl_storage_level WHERE sl_name LIKE '%".$searchText."%' and sl_id IN ('$childInString')");
    if(mysqli_num_rows($store) > 0){
        while ($rwStore = mysqli_fetch_assoc($store)) {

            $hasSub = mysqli_query($db_con, "select * from tbl_storage_level where sl_parent_id='$rwStore[sl_id]' and delete_status=0");
            if (mysqli_num_rows($hasSub) > 0) {


                global $numFile;
                global $totalFS;
                global $totalFl;
                global $numPag;
                $total = array();
                $numFile = 0;
                $totalFS = 0;
                $totalFl = 0;
                $numPag = 0;
                $total = findFolder($rwStore[sl_id]);
                //echo '<a class="col-md-2" href="storage?id=' . urlencode(base64_encode($rwStore['sl_id'])) . '"title="' . $lang['no_of_file'] . ' = ' . $numFile . ' ' . $lang['total_size'] . ' = ' . $totalFSize . $lang['MB'] . '"><i class="fa fa-folder dv"></i><div style="overflow:hidden; height:25px;" title="' . $rwStore['sl_name'] . '">' . $rwStore['sl_name'] . '</div></a>';
                echo '<a class="col-md-2 col-lg-2 col-sm-2 col-xs-2 view1" href="storage?id=' . urlencode(base64_encode($rwStore['sl_id'])) . '" title="' . $lang['no_of_file'] . ' ' . $total['files'] . ' ' . $lang['total_size'] . ' ' . $total['fileSize'] . ' MB"><i class="fa fa-folder dv"></i><div style="overflow:hidden; height:25px;" title="' . $rwStore['sl_name'] . '">' . $rwStore['sl_name'] . '</div></a>';
                echo '<a class="view2" style="display:none" href="storage?id=' . urlencode(base64_encode($rwStore['sl_id'])) . '" title="' . $lang['no_of_file'] . ' ' . $total['files'] . ' ' . $lang['total_size'] . ' ' . $total['fileSize'] . ' MB">' . $i . '. <i class="fa fa-folder "></i> ' . $rwStore['sl_name'] . ' <span class="pull-right">' . ($total['totalFolder'] - 1) . ' ' . $lang['folders'] . ',' . $total['files'] . ' ' . $lang['Files'] . ', ' . $total['numPages'] . ' ' . $lang['pages'] . '</span></a>';
                $string = strip_tags($string);

                if (strlen($string) > 500) {

                    // truncate string
                    $stringCut = substr($string, 0, 500);

                    // make sure it ends in a word so assassinate doesn't become ass...
                    $string = substr($stringCut, 0, strrpos($stringCut, ' ')) . '... <a href="/this/story">Read More</a>';
                }
                echo $string;
                '</a></span>';
            } else {
                $file = mysqli_query($db_con, "SELECT doc_id as total from tbl_document_master where doc_name='$rwStore[sl_id]' and flag_multidelete='1'");
                if (mysqli_num_rows($file) > 0) {
                    $contFile = mysqli_query($db_con, "select sum(doc_size) as total, count(doc_name) as count from tbl_document_master where FIND_IN_SET('$rwStore[sl_id]',doc_name) and flag_multidelete='1'") or die('Error:' . mysqli_error($db_con));
                    $sl_qury = mysqli_query($db_con, "SELECT sl_id FROM `tbl_storage_level`  where sl_parent_id='$rwStore[sl_id]'") or die('Error:' . mysqli_error($db_con));
                    $slcount = mysqli_num_rows($sl_qury);
                    $rwcontFile = mysqli_fetch_assoc($contFile);
                    $totalFSize = $rwcontFile['total'];
                    $totalFSize = round($totalFSize / (1024 * 1024), 2);
                    $numFile = $rwcontFile['count'];
                    $totalPages = $rwcontFile['numPages'];
                    if (empty($totalPages)) {
                        $totalPages = 0;
                    }
                    // echo'<a class="col-md-2" href="storage?id=' . urlencode(base64_encode($rwStore['sl_id'])) . '"title="' . $lang['no_of_file'] . ' = ' . $numFile . ' ' . $lang['total_size'] . ' = ' . $totalFSize . $lang['MB'] . '"><i class="fa fa-folder dv"></i><div style="overflow:hidden; height:25px;" title="' . $rwStore['sl_name'] . '">' . $rwStore['sl_name'] . '</div></a>';
                    echo'<a class="col-md-2 col-lg-2 col-sm-2 col-xs-2 view1" href="storage?id=' . urlencode(base64_encode($rwStore['sl_id'])) . '" title="' . $lang['no_of_file'] . ' ' . $numFile . ' ' . $lang['total_size'] . ' ' . $totalFSize . ' MB"><i class="fa fa-folder dv"></i><div style="overflow:hidden; height:25px;" title="' . $rwStore['sl_name'] . '">' . $rwStore['sl_name'] . '</div></a>';
                    echo'<a class="view2" style="display:none" href="storage?id=' . urlencode(base64_encode($rwStore['sl_id'])) . '" title="' . $lang['no_of_file'] . ' ' . $numFile . '' . $lang['total_size'] . ' ' . $totalFSize . ' MB">' . $i . '. <i class="fa fa-folder"></i> ' . $rwStore['sl_name'] . ' <span class="pull-right"> ' . $slcount . ' ' . $lang['folders'] . ',' . $numFile . ' ' . $lang['' . $lang['Files'] . ''] . ', ' . $totalPages . ' ' . $lang['pages'] . '</span></a>';
                } else {
                    $contFile = mysqli_query($db_con, "select sum(doc_size) as total, count(doc_name) as count, sum(noofpages) as numPages from tbl_document_master where FIND_IN_SET('$rwStore[sl_id]',doc_name) and flag_multidelete='1'") or die('Error:' . mysqli_error($db_con));
                    $sl_qury = mysqli_query($db_con, "SELECT sl_id FROM `tbl_storage_level`  where sl_parent_id='$rwStore[sl_id]' and delete_status=0") or die('Error:' . mysqli_error($db_con));
                    $slcount = mysqli_num_rows($sl_qury);
                    $rwcontFile = mysqli_fetch_assoc($contFile);
                    $totalFSize = $rwcontFile['total'];
                    $totalFSize = round($totalFSize / (1024 * 1024), 2);
                    $numFile = $rwcontFile['count'];
                    $totalPages = $rwcontFile['numPages'];
                    if (empty($totalPages)) {
                        $totalPages = 0;
                    }
                    echo'<a class="col-md-2 col-lg-2 col-sm-2 col-xs-2 view1" href="storage?id=' . urlencode(base64_encode($rwStore['sl_id'])) . '" title="' . $lang['no_of_file'] . '' . $numFile . ' ' . $lang['total_size'] . ' ' . $totalFSize . ' MB"><i class="fa fa-folder-o dv"></i><div style="overflow:hidden; height:25px;" title="' . $rwStore['sl_name'] . '">' . $rwStore['sl_name'] . '</div></a>';
                    echo'<a class="view2" style="display:none" href="storage?id=' . urlencode(base64_encode($rwStore['sl_id'])) . '" title="' . $lang['no_of_file'] . ' ' . $numFile . ' ' . $lang['total_size'] . ' ' . $totalFSize . ' MB">' . $i . '. <i class="fa fa-folder"></i> ' . $rwStore['sl_name'] . '<span class="pull-right">' . $slcount . ' ' . $lang['folders'] . ' ,' . $numFile . ' ' . $lang['Files'] . ', ' . $totalPages . ' ' . $lang['pages'] . '</span></a>';
                }
            }
        }
    }else{
        return '0';
    }
}

function createThumbnail($sTempFileName)
{
    // read photo
    $oTempFile = fopen($sTempFileName, "r");
    $sBinaryPhoto = fread($oTempFile, fileSize($sTempFileName));
    // Try to read image
    $nOldErrorReporting = error_reporting(E_ALL & ~(E_WARNING)); // ingore warnings
    $oSourceImage = imagecreatefromstring($sBinaryPhoto); // try to create image
    error_reporting($nOldErrorReporting);

    $nWidth = imagesx($oSourceImage); // get original source image width
    $nHeight = imagesy($oSourceImage); // and height
    // create small thumbnail
    $nDestinationWidth = 100;
    $nDestinationHeight = 75;
    $oDestinationImage = imagecreate($nDestinationWidth, $nDestinationHeight);

    imagecopyresized($oDestinationImage, $oSourceImage,0, 0, 0, 0,$nDestinationWidth, $nDestinationHeight,$nWidth, $nHeight); // resize the image

    // return $oDestinationImage;
    ob_start(); // Start capturing stdout.
    imageJPEG($oDestinationImage); // As though output to browser.
    $thumbnail = ob_get_contents(); // the raw jpeg image data.
    ob_end_clean(); // Dump the stdout so it does not screw other output.
    return base64_encode($thumbnail);
}

function createThumbnail2($filenamewithPath,$newdocname, $reviewFile="")
{
	if(CREATE_THUMBNAIL){
		
		$thumb = realpath('./');
		$fname = realpath($filenamewithPath);

		$os = checkOperatingSystem();

		if($os == 'win'){
			if($reviewFile){
				// for windows server
				$newfilename = $thumb.'\\thumbnail\\review\\'.$newdocname.'.jpg';
			}else{
				// for windows server
				$newfilename = $thumb.'\\thumbnail\\'.$newdocname.'.jpg';
			}
		}else{
			if($reviewFile){
				// for linux server
				$newfilename = $thumb.'/thumbnail/review/'.$newdocname.'.jpg';
			}else{
				// for linux server
				$newfilename = $thumb.'/thumbnail/'.$newdocname.'.jpg';
			}
		}
		
		$im = new imagick($fname);

		$imageprops = $im->getImageGeometry();
		$width = $imageprops['width'];
		$height = $imageprops['height'];
		if($width > $height){
			$newHeight = 100;
			$newWidth = (100 / $height) * $width;
		}else{
			$newWidth = 100;
			$newHeight = (100 / $width) * $height;
		}
		$im->resizeImage($newWidth,$newHeight, imagick::FILTER_LANCZOS, 0.9, true);
		$im->cropImage ($newWidth,$newHeight,0,0);
		$im->writeImage($newfilename);
	}
}

function changePdfToImage($uploadedfilename,$newdocname, $reviewFile="")
{
	if(CREATE_THUMBNAIL){
		
		$pathWithoutExt = explode('.', $uploadedfilename);
		$newimage = $pathWithoutExt[0].'.jpeg';
		// shell_exec('"C:\Program Files\gs\gs9.52\bin\gswin64c.exe" -dNOPAUSE -sDEVICE=jpeg -r144 -sOutputFile="'.$newimage.'" "'.$uploadedfilename.'"');
		shell_exec('gs -dNOPAUSE -sDEVICE=jpeg -r144 -sOutputFile="'.$newimage.'"  "'.$uploadedfilename.'"');
		$newimage1 = createThumbnail2($newimage,$newdocname, $reviewFile);
		unlink($newimage);
	}
}

function checkOperatingSystem(){
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        return 'win';
    } else {
        return 'lin';
    }
}


function encrypt_my_file($extractHereDirfile){
    //different key for different user
    $encryption_key_256bit = base64_encode(openssl_random_pseudo_bytes(32));
    //for now we can take it static
    $key = 'bRuD5WYw5wd0rdHR9yLlM6wt2vteuiniQBqE70nAuhU=';
    $file_string_data = file_get_contents($extractHereDirfile);
    $encrypted_data = encrypt_file($file_string_data,$key);
    $file = fopen($extractHereDirfile, 'wb');
    fwrite($file, $encrypted_data);
    fclose($file);
}

function decrypt_my_file($fileDirectoryWithName){
    //dynamic key
    $encryption_key_256bit = base64_encode(openssl_random_pseudo_bytes(32));
    // for now take static key
    $key = 'bRuD5WYw5wd0rdHR9yLlM6wt2vteuiniQBqE70nAuhU=';
    $file_string_data = file_get_contents($fileDirectoryWithName);
    $msg_decrypted = decrypt_file($file_string_data,$key);

    if($msg_decrypted!='0'){
        $file = fopen($fileDirectoryWithName,'wb');
        fwrite($file, $msg_decrypted);
        fclose($file);
    }
}


function encrypt_file($data, $key)
{
    // Remove the base64 encoding from our key
    $encryption_key = base64_decode($key);
    // Generate an initialization vector
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
    // Encrypt the data using AES 256 encryption in CBC mode using our encryption key and initialization vector.
    $encrypted = openssl_encrypt($data, 'aes-256-cbc', $encryption_key, 0, $iv);
    // The $iv is just as important as the key for decrypting, so save it with our encrypted data using a unique separator (::)
    return base64_encode($encrypted . '::' . $iv);
}

function decrypt_file($data, $key)
{
    // Remove the base64 encoding from our key
    $encryption_key = base64_decode($key);
    // To decrypt, split the encrypted data from our IV - our unique separator used was "::"
    list($encrypted_data, $iv) = explode('::', base64_decode($data), 2);
    return openssl_decrypt($encrypted_data, 'aes-256-cbc', $encryption_key, 0, $iv);
}

function getStoragePath($db_con, $parentid, $depthLevel){
	
    for($i=1;$i<=$depthLevel;$i++){
		
        $sql = mysqli_query($db_con, "SELECT sl_name, sl_parent_id FROM tbl_storage_level WHERE sl_id='$parentid'");
        $sql_result = mysqli_fetch_assoc($sql);
        $path[] = $sql_result['sl_name'];
        $parentid = $sql_result['sl_parent_id'];
    }
    return implode('/', array_reverse($path));
}


    function formatSizeUnits($bytes)
    {
			if ($bytes >= 1073741824)
			{
				$bytes = number_format($bytes / 1073741824, 2) . ' GB';
			}
			elseif ($bytes >= 1048576)
			{
				$bytes = number_format($bytes / 1048576, 2) . ' MB';
			}
			elseif ($bytes >= 1024)
			{
				$bytes = number_format($bytes / 1024, 2) . ' KB';
			}
			elseif ($bytes > 1)
			{
				$bytes = $bytes . ' bytes';
			}
			elseif ($bytes == 1)
			{
				$bytes = $bytes . ' byte';
			}
			else
			{
				$bytes = '0 bytes';
			}

			return $bytes;
	}
	
	function ezeefile_crypt($string, $action = 'e') {
		// you may change these values to your own
		$secret_key = 'cbsldms_key';
		$secret_iv = 'cbsldms_iv';
		$output = false;
		$encrypt_method = "aes-128-cbc";
		$key = substr(hash('sha1', $secret_key), 0, 32);
		$iv = substr(hash('sha1', $secret_iv), 0, 32);

		if ($action == 'e') {
			$output = base64_encode(openssl_encrypt($string, $encrypt_method, $key, 0, $iv));
			$filteroutput1 = str_replace("=", "ezee", $output);
		} else if ($action == 'd') {
			$output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
			$filteroutput1 = str_replace("ezee", "=", $output);
		}

		return $filteroutput1;
	}
	
	function moveStorage($db_con, $slid, $storageLevel, $fileManager){
		
		$moveStorage = mysqli_query($db_con, "select * from tbl_storage_level where sl_id = '$slid'"); //or die('Error in checkDublteStorage:' . mysqli_error($db_con));
		
		$rows = mysqli_fetch_assoc($moveStorage);
			
		$storageId = $rows['sl_id'];
		$storageLevel = $storageLevel+1;
		$storageMove = mysqli_query($db_con, "update tbl_storage_level set sl_depth_level = '$storageLevel' where sl_id = '$storageId'") or die('Error:' . mysqli_error($db_con));
		
		$slname = str_replace(" ", "", $rows['sl_name']);
		
		$updir = getStoragePath($db_con, $rows['sl_parent_id'], $storageLevel);

		if(!empty($updir)){
			$updir = $updir . '/';
		}else{
			$updir = '';
		}
		$folderpath = $updir.$slname;
		
		if (!is_dir($folderpath)) {
			mkdir($folderpath, 0777, TRUE) or die(print_r(error_get_last()));
		}
		
		if(moveStorageFiles($db_con, $storageId, $folderpath, $fileManager)){
			
			$checkSubStorage = mysqli_query($db_con, "select sl_id from tbl_storage_level where sl_parent_id = '$slid'");
			while($rowc = mysqli_fetch_assoc($checkSubStorage)){
				$storageLevel = $storageLevel+1;
				$slid = $rowc['sl_id'];
				moveStorage($db_con, $slid, $storageLevel, $fileManager);
			}
		}
		
	}
	
	function moveStorageFiles($db_con, $slid, $folderpath, $fileManager){
		
		$getDocument = mysqli_query($db_con, "select * from tbl_document_master where substring_index(doc_name, '_', 2)='$slid'") or die('Error:' . mysqli_error($db_con));
 
		while ($rowd = mysqli_fetch_assoc($getDocument)) {

			$id = $rowd['doc_id'];

			$noofpages = $rowd['noofpages'];

			$path = substr($rowd['doc_path'], 0, strrpos($rowd['doc_path'], '/') + 1);

			$pathtxt = 'extract-here/' . $path . 'TXT/' . $id . '.txt';

			$filePath = $rowd['doc_path'];
			$filname = substr($filePath, strrpos($filePath, '/') + 1);
			$newDocPath = $folderpath.'/'.$filname;
			$fileNewPath = 'extract-here/' .$newDocPath;
		
			if (!is_dir('extract-here/'.$folderpath. '/TXT')) {
				mkdir('extract-here/'.$folderpath. '/TXT', 0777, TRUE) or die(print_r(error_get_last()));
			}
			$newTxtPath =  'extract-here/'.$folderpath. '/TXT/' . $id . '.txt';
			
			$filemoved = mysqli_query($db_con, "update tbl_document_master set doc_path='$newDocPath', ftp_done='1' where doc_id = '$id'") or die('Error:' . mysqli_error($db_con));
			if ($filemoved) {
				if(!file_exists('extract-here/'.$filePath)){
					
					if ($fileManager->downloadFile( 'DMS/' . ROOT_FTP_FOLDER . '/' . $rowd['doc_path'], $fileNewPath)) {
						
						$uploadfile = $fileManager->uploadFile($fileNewPath,  'DMS/' . ROOT_FTP_FOLDER . '/' . $newDocPath);
							
						if ($uploadfile) {
							
							$fileManager->deleteFile( 'DMS/' . ROOT_FTP_FOLDER . '/' . $rowd['doc_path']); // delete file from file server
							unlink('DMS/' . ROOT_FTP_FOLDER . '/' . $rowd['doc_path']);
							
							
							if(file_exists($pathtxt)){
								
								if(copy($pathtxt, $newTxtPath)){
									unlink($pathtxt);
								}
							}
							 unlink($fileNewPath);
							return true;
						} 
					}
				}else{
					
					$oldfilePath ='extract-here/'.$filePath;
					$uploadfile = $fileManager->uploadFile($oldfilePath,  ROOT_FTP_FOLDER . '/' . $newDocPath);
							
					if ($uploadfile) {
						
						if(file_exists($pathtxt)){
							if(copy($pathtxt, $newTxtPath)){
								unlink($pathtxt);
							}
						}
						
						unlink($oldfilePath);
						
						return true;
					}
				}
				           
			}
		}
	}
	
	function directoryDepth()
	{
		
		if(isset($_SERVER["PHP_SELF"]) && !empty($_SERVER["PHP_SELF"])){
			
			if(!isset($_SESSION['root_depth'])){
				
				$_SESSION['root_depth'] = substr_count($_SERVER["PHP_SELF"], "/");
			}
			
			$folder_depth = substr_count($_SERVER["PHP_SELF"] , "/");
			
			$pieces = explode(".", $_SERVER['HTTP_HOST']);
			
			if($folder_depth == false){
				$folder_depth = 1;
			}
			
			if(!in_array(end($pieces), ['com', 'in'])){
	
				$minusroot = $_SESSION['root_depth']-1;
				$folder_depth = $folder_depth-$minusroot;
			}
			
			return str_repeat("../", $folder_depth - 1);
		}else{
			return str_replace('\application\pages', '', __DIR__) . DIRECTORY_SEPARATOR;
		}
	}
	
	function findParentfolder($slpermIdes, $slid, $db_con) {
		global $folderperms1;
		global $openIds;

		$sllevel = mysqli_query($db_con, "select * from tbl_storage_level where sl_id in($slid)  and delete_status=0 order by sl_name asc");
		while ($rwfolderperm = mysqli_fetch_assoc($sllevel)) {
			$folderperms1[] = $rwfolderperm['sl_id'];
			$openIds[] = $rwfolderperm['sl_parent_id'];

			$sllevel1 = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='" . $rwfolderperm['sl_parent_id'] . "' and delete_status=0 order by sl_name asc");
			
			 $sllevel2 = mysqli_query($db_con, "select * from tbl_storage_level where sl_parent_id='" . $rwfolderperm['sl_parent_id'] . "' and sl_id!='".$rwfolderperm['sl_id']."' and delete_status=0 order by sl_name asc");
			 if (mysqli_num_rows($sllevel2) > 0) {
				while ($rowChh = mysqli_fetch_assoc($sllevel2)) {
				
					$folderperms1[] = $rowChh['sl_id'];
				}
			 }
			if (mysqli_num_rows($sllevel1) > 0) {
				
				$childarray = array();
				while ($rowCh = mysqli_fetch_assoc($sllevel1)) {
					
				
					$childarray[] = $rowCh['sl_id'];
				}
				$childIds = implode(",", $childarray);
				findParentfolder($slpermIdes, $childIds, $db_con);
			}
		}

		return array('parentIds' =>$folderperms1, 'openIds' => $openIds);
	}
	
	function findChild($sl_id, $level, $slperm, $selectedslId="") {

		global $db_con;
		$notshow="";
		if($selectedslId!=""){ // condition for do not show selected folder in the list
			
			if($selectedslId!=$sl_id){
				echo '<option value="' . $sl_id . '">';
				parentLevels($sl_id, $db_con, $slperm, $level, '');
				echo '</option>';
			}
			
			$notshow = " and sl_parent_id!='$selectedslId'";
		}else{
			echo '<option value="' . $sl_id . '">';
			parentLevels($sl_id, $db_con, $slperm, $level, '');
			echo '</option>';
		}
		
		$sql_child = "select * FROM tbl_storage_level WHERE sl_parent_id = '$sl_id'  AND delete_status='0' $notshow order by sl_name asc";

		$sql_child_run = mysqli_query($db_con, $sql_child) or die('Error:' . mysqli_error($db_con));

		if (mysqli_num_rows($sql_child_run) > 0) {

			while ($rwchild = mysqli_fetch_assoc($sql_child_run)) {

				$child = $rwchild['sl_id'];
				findChild($child, $level, $slperm, $selectedslId);
			}
		}
	}
	
	function parentLevels($slid, $db_con, $slperm, $level, $value) {

    if ($slperm == $slid) {
        $parent = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$slid' AND delete_status='0'") or die('Error' . mysqli_error($db_con));
        $rwParent = mysqli_fetch_assoc($parent);

        if ($level < $rwParent['sl_depth_level']) {
            parentLevels($rwParent['sl_parent_id'], $db_con, $slperm, $level, $rwParent['sl_name']);
        }
    } else {
        $parent = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$slid' and sl_parent_id='$slperm' AND delete_status='0'") or die('Error' . mysqli_error($db_con));
        if (mysqli_num_rows($parent) > 0) {

            $rwParent = mysqli_fetch_assoc($parent);
            if ($level < $rwParent['sl_depth_level']) {
                parentLevels($rwParent['sl_parent_id'], $db_con, $slperm, $level, $rwParent['sl_name']);
            }
        } else {
            $parent = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$slid' AND delete_status='0'") or die('Error' . mysqli_error($db_con));
            $rwParent = mysqli_fetch_assoc($parent);
            $getparnt = $rwParent['sl_parent_id'];
            if ($level <= $rwParent['sl_depth_level']) {
                parentLevels($getparnt, $db_con, $slperm, $level, $rwParent['sl_name']);
            } else {
                
            }
        }
    }
    if (!empty($value)) {
        $value = $rwParent['sl_name'] . '<b> > </b>';
    } else {
        $value = $rwParent['sl_name'];
    }
    echo $value;
}

function findchild1($sl_id, $level, $slperm, $selectedslId = "",$parent_id_arr,$sl_id_arr,$sl_parent_id_slid) {

    global $db_con;
    $notshow = "";
    if ($selectedslId != "") { // condition for do not show selected folder in the list
        if ($selectedslId != $sl_id) {
            echo '<option value="' . $sl_id . '">';
            parentLevels1($sl_id, $db_con, $slperm, $level, '',$sl_id_arr,$sl_parent_id_slid);
            echo '</option>';
        }

        $notshow = " and sl_parent_id!='$selectedslId'";
    } else {
        echo '<option value="' . $sl_id . '">';
        parentLevels1($sl_id, $db_con, $slperm, $level, '',$sl_id_arr,$sl_parent_id_slid);
        echo '</option>';
    }

    // $sql_child = "select * FROM tbl_storage_level WHERE sl_parent_id = '$sl_id'  AND delete_status='0' $notshow order by sl_name asc";

    // $sql_child_run = mysqli_query($db_con, $sql_child) or die('Error:' . mysqli_error($db_con));

    // if (mysqli_num_rows($sql_child_run) > 0) {

        foreach ($parent_id_arr[$sl_id] as $rwchild) {

            $child = $rwchild['sl_id'];
            findchild1($child, $level, $slperm, $selectedslId,$parent_id_arr,$sl_id_arr,$sl_parent_id_slid);
        }
    // }
}

function parentLevels1($slid, $db_con, $slperm, $level, $value,$sl_id_arr,$sl_parent_id_slid) {

    if ($slperm == $slid) {
        // $parent = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$slid' AND delete_status='0'") or die('Error' . mysqli_error($db_con));
        // $rwParent = mysqli_fetch_assoc($parent);
        $rwParent=$sl_id_arr[$slid];
        if ($level < $rwParent['sl_depth_level']) {
            parentLevels1($rwParent['sl_parent_id'], $db_con, $slperm, $level, $rwParent['sl_name'],$sl_id_arr,$sl_parent_id_slid);
        }
    } else {
        // $parent = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$slid' and sl_parent_id='$slperm' AND delete_status='0'") or die('Error' . mysqli_error($db_con));
        $rwParent=$sl_parent_id_slid[$slperm][$slid];
        if (!empty($rwParent)) {

            // $rwParent = mysqli_fetch_assoc($parent);
            if ($level < $rwParent['sl_depth_level']) {
                parentLevels1($rwParent['sl_parent_id'], $db_con, $slperm, $level, $rwParent['sl_name'],$sl_id_arr,$sl_parent_id_slid);
            }
        } else {
            // $parent = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$slid' AND delete_status='0'") or die('Error' . mysqli_error($db_con));
            // $rwParent = mysqli_fetch_assoc($parent);
            $rwParent=$sl_id_arr[$slid];
            $getparnt = $rwParent['sl_parent_id'];
            if ($level <= $rwParent['sl_depth_level']) {
                parentLevels1($getparnt, $db_con, $slperm, $level, $rwParent['sl_name'],$sl_id_arr,$sl_parent_id_slid);
            } else {
                
            }
        }
    }
    if (!empty($value)) {
        $value = $rwParent['sl_name'] . '<b> > </b>';
    } else {
        $value = $rwParent['sl_name'];
    }
    echo $value;
}


//ab@210621 convert hex color into rgb
function hexToRgb($hex, $alpha = false) {
    $hex = str_replace('#', '', $hex);
    $length = strlen($hex);
    $rgb['r'] = hexdec($length == 6 ? substr($hex, 0, 2) : ($length == 3 ? str_repeat(substr($hex, 0, 1), 2) : 0));
    $rgb['g'] = hexdec($length == 6 ? substr($hex, 2, 2) : ($length == 3 ? str_repeat(substr($hex, 1, 1), 2) : 0));
    $rgb['b'] = hexdec($length == 6 ? substr($hex, 4, 2) : ($length == 3 ? str_repeat(substr($hex, 2, 1), 2) : 0));
    if ($alpha) {
        $rgb['a'] = $alpha;
    }
    return $rgb;
}

function isFolderReadable($db_con, $slid){

	$sllevel = mysqli_query($db_con, "select s.sl_parent_id, sp.shared, sp.readonly, sp.user_id  from tbl_storage_level as s left join tbl_storagelevel_to_permission as sp on s.sl_id=sp.sl_id where s.sl_id ='$slid' and s.sl_parent_id IS NOT NULL");
	if(mysqli_num_rows($sllevel)>0){
		$rowp = mysqli_fetch_assoc($sllevel);
		$parentId = $rowp['sl_parent_id'];
		if($parentId){
			if($rowp['readonly']==1 && $_SESSION['cdes_user_id']==$rowp['user_id']){
				return false;
			}else{
				if($rowp['user_id']==""){ // check permission more than this level
					
					return isFolderReadable($db_con, $parentId);
				}else{
					return true;
				}
			}
		}else{
			return true;
		}
	}else{
		return true;
	}
}



?>