<?php
//get document which weeding out time is over from set time
require_once './application/config/database.php';
require_once './classes/ftp.php';
require_once './application/pages/feature-enable-disable.php';
require_once './mail.php';
require_once './classes/fileManager.php';
/* feature enable check start*/
$retentionflag="";
$retentiondocflag="";
if ($rwgetInfo['retention_feature_enable'] == '1') {
	

    $mailtousers = $rwgetInfo['retentiondoc_mailsent_users'];
    if ($rwgetInfo['notify_type'] == 'Days') {
        $days = $rwgetInfo['email_time'] . ' ' . $rwgetInfo['notify_type'];
    } else {
        $hours = $rwgetInfo['email_time'];
        $days = $hours . ' ' . 'hours';
    }
    $notifydays_before = date('Y-m-d H:i:s', strtotime('+' . $days, strtotime(date('Y-m-d H:i:s'))));
    $today = date('Y-m-d H:i:s');
    $retentionirytime = array();

    $documentretentiontime = mysqli_query($db_con, "SELECT * FROM `tbl_document_master` WHERE retention_period <='$notifydays_before' AND retention_period IS NOT NULL and retention_period!='' and (flag_multidelete='1' or flag_multidelete='2')");
    while ($rwdocumentretentiontime = mysqli_fetch_assoc($documentretentiontime)) {
        $retentionirytime[] = $rwdocumentretentiontime['retention_period'];
    }
	
	
//print_r($retentionirytime); die;
	$retentionflag = 0;
	$retentiondocflag = 0;
    foreach ($retentionirytime as $docretentiontime) {
		
        $notifydays_ago = date('Y-m-d H:i:s', strtotime('-' . $days, strtotime($docretentiontime)));

        if (strtotime($notifydays_ago) < strtotime($docretentiontime) && strtotime($docretentiontime) > strtotime($today)) {
			
            $mailbodyhtml = RetentionDocumentlistView($db_con, $notifydays_before, $date);
            if ($mailbodyhtml) {
                $retentiondocflag = 2;
            }else{
				
			}
        } else {
			
            $retentiondocmailbody = RetentionDocumentlist($db_con, $date);
            if ($retentiondocmailbody) {
                $retentionflag = 1;
            }else{
				
			}
        }
    }
	

    if ($retentionflag == 1) {
		
		

        $maitousersent = docRetentionFinalMailtoAuthrizedUsers($retentiondocmailbody, $db_con, $mailtousers, $projectName);
        if ($maitousersent) {
			
            $delete = deleteDocumentRetentionPeriodOver($db_con, $date, $host, $fileserver, $port, $ftpUser, $ftpPwd);
        }
    }
    if ($retentiondocflag == 2) {
		
        $retentionperiodover = docRetentionMailtoAuthrizedUsers($mailbodyhtml, $db_con, $mailtousers, $projectName);
    }

/* feature enable check end*/
}
function RetentionDocumentlistView($db_con, $notifydays_before, $date) {
    $getdocument = mysqli_query($db_con, "SELECT * FROM `tbl_document_master` WHERE retention_period <='$notifydays_before' AND retention_period > '$date' AND retention_period IS NOT NULL and retention_period!='' and (flag_multidelete='1' or flag_multidelete='2')");
    if (mysqli_num_rows($getdocument) > 0) {
        $html = '<table border="1" cellpacing="2" cellpadding="8" style="border-collapse : collapse;">';
        $html .= '<tr>';
        $html .= '<th>SNo.</th>';
        $html .= '<th>Storage Name</th>';
        $html .= '<th>Document Name</th>';
        $html .= '<th>Uploaded By</th>';
        $html .= '<th>Retention Period</th>';
        $html .= '</tr>';
        $i = 1;
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
            $html .= '<td>' . $rwuploadedby['first_name'] . ' ' . $rwuploadedby['last_name'] . '</td>';
            $html .= '<td>' . date('d-m-Y H:i:s', strtotime($rwgetdocument['retention_period'])) . '</td>';

            $html .= '</tr>';
            $i++;
        }
        $html .= '</table>';

        return $html;
    } else {

        return false;
    }
}

function RetentionDocumentlist($db_con, $date) {
	
    $getdocument = mysqli_query($db_con, "SELECT * FROM `tbl_document_master` WHERE retention_period <='$date' AND retention_period IS NOT NULL and retention_period!='' and (flag_multidelete='1' or flag_multidelete='2')");
    if (mysqli_num_rows($getdocument) > 0) {
        $html = '<table border="1" cellpacing="2" cellpadding="8" style="border-collapse : collapse;">';
        $html .= '<tr>';
        $html .= '<th>SNo.</th>';
        $html .= '<th>Storage Name</th>';
        $html .= '<th>Document Name</th>';
        $html .= '<th>Uploaded By</th>';
        $html .= '<th>Retention Period</th>';
        $html .= '</tr>';
        $i = 1;
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
            $html .= '<td>' . $rwuploadedby['first_name'] . ' ' . $rwuploadedby['last_name'] . '</td>';
            $html .= '<td>' . date('d-m-Y H:i:s', strtotime($rwgetdocument['retention_period'])) . '</td>';

            $html .= '</tr>';
            $i++;
        }
        $html .= '</table>';

        return $html;
    } else {

        return false;
    }
}

function deleteDocumentRetentionPeriodOver($db_con, $date, $host, $fileserver, $port, $ftpUser, $ftpPwd) {
	$fileManager = new fileManager();
	// Connect to file server
	$fileManager->conntFileServer();
    $retentiondoc = mysqli_query($db_con, "SELECT * FROM `tbl_document_master` where retention_period <= '$date' AND retention_period IS NOT NULL AND retention_period!='' AND (flag_multidelete='1' or flag_multidelete='2')");
    while ($rwretentiondoc = mysqli_fetch_assoc($retentiondoc)) {
        $filePath = $rwretentiondoc['doc_path'];
        $delfilename = $rwretentiondoc['old_doc_name'];
        $deldocId = $rwretentiondoc['doc_id'];
        $noofpages = $rwretentiondoc['noofpages'];
        $doc_extn = $rwretentiondoc['doc_extn'];
        $retentiontime = $rwretentiondoc['retention_period'];
        $docpath  = explode("/", $filePath);
        $foldername =  reset($docpath);
        $path = '../extract-here/' . $foldername.'/';
        $pathtxt = $path . 'TXT/' .$deldocId. '/';
       
		$fileManager->deleteFile(ROOT_FTP_FOLDER . '/' . $filePath);
		
        $allowed = array('png', 'jpg', 'jpeg', 'gif', 'tiff', 'odt', 'rtf', 'bmp', 'tif', 'pdf');
        
        if(in_array(strtolower($doc_extn), $allowed)){
            for ($k=0; $k<$noofpages; $k++) {
                if(file_exists($pathtxt.$k.'.txt')){
                    unlink($pathtxt.$k.'.txt');
                }
                
            }
            if (is_dir($pathtxt)) {
                rmdir($pathtxt);
             }
         }
       

        if (isset($retentiontime) && !empty($retentiontime)) {
            $del = mysqli_query($db_con, "DELETE FROM tbl_document_master where retention_period <= '$retentiontime'"); //or die('Error:' . mysqli_error($db_con));
            $delDocShare = mysqli_query($db_con, "DELETE FROM tbl_document_share WHERE doc_ids ='$deldocId'"); //or die('Error:' . mysqli_error($db_con));
            $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`,`doc_id`, `action_name`, `start_date`,`system_ip`) values ('1', 'Action from scheduler','$deldocId', 'Storage Document $delfilename deleted due to retention period over.','$date','$host')"); //or die('error : ' . mysqli_error($db_con));
        }
    }
}

?>