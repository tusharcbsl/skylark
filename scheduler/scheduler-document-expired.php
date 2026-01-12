<?php
//Expired document list sechdular
require_once './application/config/database.php';
require_once './application/pages/feature-enable-disable.php';
require_once './mail.php';
$expflag = "";
$flag = "";
if ($rwgetexpInfo['exp_feature_enable'] == '1') {
//check email notification time before
    $mailto = $rwgetexpInfo['expdoc_mailsent_users'];
    if ($rwgetexpInfo['notify_type'] == 'Days') {
        $days = $rwgetexpInfo['email_sent_time'] . ' ' . $rwgetexpInfo['notify_type'];
    } else {
        $hours = $rwgetexpInfo['email_sent_time'];
        $days = $hours . ' ' . 'hours';
    }
    $notifydays_before = date('Y-m-d H:i:s', strtotime('+' . $days, strtotime(date('Y-m-d H:i:s'))));
    $today = date('Y-m-d H:i:s');
    $expirytime = array();
    $documentexptime = mysqli_query($db_con, "SELECT * FROM `tbl_document_master` WHERE doc_expiry_period <='$notifydays_before' AND doc_expiry_period IS NOT NULL and doc_expiry_period!='' and flag_multidelete='1'");
    while ($rwdocumentexptime = mysqli_fetch_assoc($documentexptime)) {
        $expirytime[] = $rwdocumentexptime['doc_expiry_period'];
    }
      //print_r($expirytime); die;
    foreach ($expirytime as $docexptime) {
        $notifydays_ago = date('Y-m-d H:i:s', strtotime('-' . $days, strtotime($docexptime)));
        if (strtotime($notifydays_ago) < strtotime($docexptime) && strtotime($docexptime) > strtotime($today)) {
			
			
            $mailbodyhtml = expiryDocumentlistView($db_con, $notifydays_before);
            if ($mailbodyhtml) {

                $flag = 2;
				
				echo "notify";
            }
        } else {
            $Expdocmailbody = expiryDocumentlist($db_con);
            if ($Expdocmailbody) {
                $expflag = 1;
				
				echo "expired";
            }
        }
    }
    if ($expflag == 1) {
        
        $maitsent = docExpFinalMailtoAuthrizedUsers($Expdocmailbody, $db_con, $mailto, $projectName);
        $schedularlog = mysqli_query($db_con, "select old_doc_name,doc_name,doc_id from tbl_document_master  WHERE doc_expiry_period <= NOW() and flag_multidelete='1'");
        //$updatedocstatus = mysqli_query($db_con, "UPDATE `tbl_document_master` SET flag_multidelete='2' WHERE doc_expiry_period <= NOW() and flag_multidelete='1'");
        while ($rwschedularlog = mysqli_fetch_assoc($schedularlog)) {
            
            echo "UPDATE `tbl_document_master` SET flag_multidelete='2' WHERE doc_id= '".$rwschedularlog['doc_id']."' and flag_multidelete='1'";
            $updatedocstatus = mysqli_query($db_con, "UPDATE `tbl_document_master` SET flag_multidelete='2' WHERE doc_id= '".$rwschedularlog['doc_id']."' and flag_multidelete='1'") or die('errorexpired : ' . mysqli_error($db_con));
            if($updatedocstatus){
                    $sl_id = explode('_', $rwschedularlog['doc_name']);	
                    $slid = $sl_id[0];

                    $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`,`doc_id`,`sl_id`, `action_name`, `start_date`,`system_ip`) values ('1', 'Action from scheduler','$rwschedularlog[doc_id]', '$slid', 'Document/Certificate name : $rwschedularlog[old_doc_name] has been expired.','$date','$host')") or die('error12111 : ' . mysqli_error($db_con));
            }
            
        }
    }
    if ($flag == 2) {
        $afterexpiredmailsent = docExpMailtoAuthrizedUsers($mailbodyhtml, $db_con, $mailto, $projectName);
    }
}

function expiryDocumentlistView($db_con, $notifydays_before) {

    $getdocument = mysqli_query($db_con, "SELECT * FROM `tbl_document_master` WHERE doc_expiry_period <='$notifydays_before' AND doc_expiry_period > NOW() AND doc_expiry_period IS NOT NULL and doc_expiry_period!='' and flag_multidelete='1'");
    if (mysqli_num_rows($getdocument) > 0) {
        $html = '<table border="1" cellpacing="2" cellpadding="8" style="border-collapse : collapse;">';
        $html .= '<tr>';
        $html .= '<th>SNo.</th>';
        $html .= '<th>Storage Name</th>';
        $html .= '<th>Document Name</th>';
        $html .= '<th>Uploaded By</th>';
        $html .= '<th>Expiry Date</th>';
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
            $html .= '<td>' . date('d-m-Y H:i:s', strtotime($rwgetdocument['doc_expiry_period'])) . '</td>';

            $html .= '</tr>';
            $i++;
        }
        $html .= '</table>';

        return $html;
    } else {

        return false;
    }
}

function expiryDocumentlist($db_con) {

    $getdocument = mysqli_query($db_con, "SELECT * FROM `tbl_document_master` WHERE doc_expiry_period <=NOW() AND doc_expiry_period IS NOT NULL and doc_expiry_period!='' and flag_multidelete='1'");
    if (mysqli_num_rows($getdocument) > 0) {
        $html = '<table border="1" cellpacing="2" cellpadding="8" style="border-collapse : collapse;">';
        $html .= '<tr>';
        $html .= '<th>SNo.</th>';
        $html .= '<th>Storage Name</th>';
        $html .= '<th>Document Name</th>';
        $html .= '<th>Uploaded By</th>';
        $html .= '<th>Expiry Date</th>';
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
            $html .= '<td>' . date('d-m-Y H:i:s', strtotime($rwgetdocument['doc_expiry_period'])) . '</td>';

            $html .= '</tr>';
            $i++;
        }
        $html .= '</table>';

        return $html;
    } else {

        return false;
    }
}

?>