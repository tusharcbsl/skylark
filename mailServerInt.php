<?php

error_reporting(E_ALL);

// for mails
//webmail("mail.cbsl-india.com", "143", "", "novalidate-cert", 'web@cbsl-india.com', 'Kdcs@08065');
function webmail($mailServer, $port, $ssl, $validate, $username, $password, $userid, $db_con, $filters) {
    //email connection
    $hostname = "{" . $mailServer . ":" . $port . "/imap" . ((!empty($ssl)) ? ("/$ssl") : ("")) . ((!empty($validate)) ? ("/$validate") : ("")) . "}";
    $connection = imap_open($hostname, $username, $password) or die(print_r(error_get_last()));
    //email connection end
    $mailfolder = imap_list($connection, $hostname, "*"); //mailbox  list
    foreach ($mailfolder as $folder) { //print list
        $shortname = str_replace($hostname, "", $folder); //mailbox name
        //echo '<br>';
        imap_reopen($connection, "$hostname$shortname") or die(implode(", ", imap_errors())); // reconnect 

        mails($connection, $userid, $db_con, $filters); // get mails
        //echo '<br>';
    }
    imap_close($connection); //close connection
}

// function for geting mail list and body
function mails($reconn, $userid, $db_con, $filters) {
    $emails = array();
   
    $emails = imap_search($reconn, $filters);

    if (!empty($emails)) {

        $k = 1;
        foreach ($emails as $e) {
            $overview = imap_fetch_overview($reconn, $e, 0);
            $structure = imap_fetchstructure($reconn, $e);



            $flattenedParts = flattenParts($structure->parts);
            $attached = 0;
            foreach ($flattenedParts as $partNumber => $part) {

                switch ($part->type) {

                    case 0:
                        // the HTML or plain text part of the email
                        $message = getPart($reconn, $e, $partNumber, $part->encoding);
                        // now do something with the message, e.g. render it
                        break;

                    case 1:
                        // multi-part headers, can ignore

                        break;
                    case 2:
                        // attached message headers, can ignore
                        break;

                    case 3: // application
                    case 4: // audio
                    case 5: // image
                    case 6: // video
                    case 7: // other
                        $filename = getFilenameFromPart($part);
                        if ($filename) {
                            $attached = 1;
                            // it's an attachment

                            $attachment = getPart($reconn, $e, $partNumber, $part->encoding, $userid);

                            // now do something with the attachment, e.g. save it somewhere
                            $folder = "extract-here/emailattachment";
                            if (!is_dir($folder)) {

                                mkdir($folder, 0777);
                            }
                            $folder = $folder . '/' . $userid;
                            if (!is_dir($folder)) {
                                mkdir($folder, 0777);
                            }
                            $folder = $folder . '/' . $e;
                            if (!is_dir($folder)) {
                                mkdir($folder, 0777);
                            }
                            $fp = fopen("./" . $folder . "/" . $filename, "w+");
                            fwrite($fp, $attachment);
                            fclose($fp);
                        } else {
                            // don't know what it is
                        }
                        break;
                }
            }
//echo '<br>';
            /* $fl_from=0;$fl_to=0;$fl_cc=0;$fl_bcc=0;$fl_subject=0;$fl_body=0;$fl_messageID=0;$fl_date=0;
              $cols= mysqli_query($db_con, "show columns from tbl_document_master");
              while($rwCols= mysqli_fetch_assoc($cols)){
              if($rwCols['Field']=='from'){ $fl_from=1; }
              if($rwCols['Field']=='to'){ $fl_to=1; }
              if($rwCols['Field']=='cc'){ $fl_cc=1; }
              if($rwCols['Field']=='bcc'){ $fl_bcc=1; }
              if($rwCols['Field']=='subject'){ $fl_subject=1; }
              if($rwCols['Field']=='body_email'){ $fl_body=1; }
              if($rwCols['Field']=='message_id'){ $fl_messageID=1; }
              if($rwCols['Field']=='email_date'){ $fl_date=1; }
              }
              if($fl_from==0){
              $create = mysqli_query($db_con, "insert into tbl_metadata_master (`id`, `field_name`, `data_type`, `length_data`, `mandatory`) values(null,'from','varchar','100','')") or die('Error' . mysqli_error($db_con));
              $metaCreate=mysqli_query($db_con, "ALTER TABLE tbl_document_master ADD `from` varchar(100)  null") or die('Error adding meta ' . mysqli_error($db_con));
              }
              if($fl_to==0){
              $create = mysqli_query($db_con, "insert into tbl_metadata_master (`id`, `field_name`, `data_type`, `length_data`, `mandatory`) values(null,'to','varchar','100','')") or die('Error' . mysqli_error($db_con));
              $metaCreate=mysqli_query($db_con, "ALTER TABLE tbl_document_master ADD `to` varchar(100)  null") or die('Error adding meta ' . mysqli_error($db_con));
              }if($fl_cc==0){
              $create = mysqli_query($db_con, "insert into tbl_metadata_master (`id`, `field_name`, `data_type`, `length_data`, `mandatory`) values(null,'cc','varchar','100','')") or die('Error' . mysqli_error($db_con));
              $metaCreate=mysqli_query($db_con, "ALTER TABLE tbl_document_master ADD `cc` varchar(100)  null") or die('Error adding meta ' . mysqli_error($db_con));
              }if($fl_bcc==0){
              $create = mysqli_query($db_con, "insert into tbl_metadata_master (`id`, `field_name`, `data_type`, `length_data`, `mandatory`) values(null,'bcc','varchar','100','')") or die('Error' . mysqli_error($db_con));
              $metaCreate=mysqli_query($db_con, "ALTER TABLE tbl_document_master ADD `bcc` varchar(100)  null") or die('Error adding meta ' . mysqli_error($db_con));
              }if($fl_subject==0){
              $create = mysqli_query($db_con, "insert into tbl_metadata_master (`id`, `field_name`, `data_type`, `length_data`, `mandatory`) values(null,'subject','varchar','500','')") or die('Error' . mysqli_error($db_con));
              $metaCreate=mysqli_query($db_con, "ALTER TABLE tbl_document_master ADD `subject` varchar(500)  null") or die('Error adding meta ' . mysqli_error($db_con));
              }if($fl_body==0){
              $create = mysqli_query($db_con, "insert into tbl_metadata_master (`id`, `field_name`, `data_type`, `length_data`, `mandatory`) values(null,'body_email','TEXT','','')") or die('Error' . mysqli_error($db_con));
              $metaCreate=mysqli_query($db_con, "ALTER TABLE tbl_document_master ADD `body_email` TEXT  null") or die('Error adding meta ' . mysqli_error($db_con));
              }if($fl_messageID==0){
              $create = mysqli_query($db_con, "insert into tbl_metadata_master (`id`, `field_name`, `data_type`, `length_data`, `mandatory`) values(null,'message_id','varchar','100','')") or die('Error' . mysqli_error($db_con));
              $metaCreate=mysqli_query($db_con, "ALTER TABLE tbl_document_master ADD `message_id` varchar(100)  null") or die('Error adding meta ' . mysqli_error($db_con));
              }if($fl_date==0){
              $create = mysqli_query($db_con, "insert into tbl_metadata_master (`id`, `field_name`, `data_type`, `length_data`, `mandatory`) values(null,'email_date','DATETIME','','')") or die('Error' . mysqli_error($db_con));
              $metaCreate=mysqli_query($db_con, "ALTER TABLE tbl_document_master ADD `email_date` DATETIME  null") or die('Error adding meta ' . mysqli_error($db_con));
              } */

            // the body of the message is in $message
            $details = $overview[0];
            //var_dump($details);
            $cc = "";
            $bcc = "";
            mysqli_set_charset($db_con, "utf8");
            if (array_key_exists("subject", $details)) {
                $subject = $details->subject;

                $subject = utf8_decode(imap_utf8($subject)); //mysqli_real_escape_string($db_con, iconv_mime_decode($subject,0,"UTF-8"));
            }
            mysqli_set_charset($db_con, "utf8");
            if (array_key_exists("to", $details)) {
                $to = $details->to;

                $to = utf8_decode(imap_utf8($to)); // mysqli_real_escape_string($db_con, iconv_mime_decode($to,0,"UTF-8"));
           
                }
            if (array_key_exists("from", $details)) {
                $from = $details->from;
                $from = mysqli_real_escape_string($db_con, $from);
            }
            if (array_key_exists("cc", $details)) {
                $cc = $details->cc;
                $cc = mysqli_real_escape_string($db_con, $cc);
            }
            if (array_key_exists("bcc", $details)) {
                $bcc = $details->bcc;
                $bcc = mysqli_real_escape_string($db_con, $bcc);
            }
            if (array_key_exists("date", $details)) {
                $emailDate = $details->date;
                $emailDate = strtotime($emailDate);
                $emailDate = date('Y-m-d h:i:s', $emailDate);
                $emailDate = mysqli_real_escape_string($db_con, $emailDate);
            }
            if (array_key_exists("uid", $details)) {
                $uid = $details->uid;
                $uid = mysqli_real_escape_string($db_con, $uid);
            }
            // echo '<br>';
            $body = $message;
            $body = mysqli_real_escape_string($db_con, $body);
            //echo '<br>';
            $msg_no = $e;
            $check = mysqli_query($db_con, "select id from tbl_my_mails where user_id='$userid' and uid='$uid' and message_id='$msg_no'");
            if (mysqli_num_rows($check) <= 0) {
                $insertDMS = mysqli_query($db_con, "insert into tbl_my_mails(`user_id`, `from`, `to`, `cc`, `bcc`, `subject`, `body_email`, `message_id`, `email_date`,`attachment`,`uid`) "
                                . "values('$userid','$from','$to','$cc','$bcc','$subject','$body','$msg_no','$emailDate','$attached','$uid')") or die('Error' . mysqli_error($db_con));
//            if($attached==1){
//              $InsertDocumnt = mysqli_query($db_con, "insert into tbl_document_master(`doc_name`, `old_doc_name`, `doc_extn`, `doc_path`, `uploaded_by`, `noofpages`, `dateposted`, `message_id`, `email_date`,`attachment`,`uid`) "
//                        . "values('$userid','$from','$to','$cc','$bcc','$subject','$body','$msg_no','$emailDate','$attached','$uid')"); 
//            }
            } else {
                $update = mysqli_query($db_con, "update tbl_my_mails set `from`='$from',`to`='$to', `cc`='$cc', `bcc`='$bcc', `subject`='$subject', `body_email`='$body' where uid='$uid' and user_id='$userid' and message_id='$msg_no'") or die('Error' . mysqli_error($db_con));
            }

            $k++;
        }
    }
}

function getPart($connection, $messageNumber, $partNumber, $encoding) {

    $data = imap_fetchbody($connection, $messageNumber, $partNumber);
    switch ($encoding) {
        case 0: return $data; // 7BIT
        case 1: return $data; // 8BIT
        case 2: return $data; // BINARY
        case 3: return base64_decode($data); // BASE64
        case 4: return quoted_printable_decode($data); // QUOTED_PRINTABLE
        case 5: return $data; // OTHER
    }
}

function getFilenameFromPart($part) {

    $filename = '';

    if ($part->ifdparameters) {
        foreach ($part->dparameters as $object) {
            if (strtolower($object->attribute) == 'filename') {
                $filename = $object->value;
            }
        }
    }

    if (!$filename && $part->ifparameters) {
        foreach ($part->parameters as $object) {
            if (strtolower($object->attribute) == 'name') {
                $filename = $object->value;
            }
        }
    }

    return $filename;
}

function flattenParts($messageParts, $flattenedParts = array(), $prefix = '', $index = 1, $fullPrefix = true) {

    foreach ($messageParts as $part) {
        $flattenedParts[$prefix . $index] = $part;
        if (isset($part->parts)) {
            if ($part->type == 2) {
                $flattenedParts = flattenParts($part->parts, $flattenedParts, $prefix . $index . '.', 0, false);
            } elseif ($fullPrefix) {
                $flattenedParts = flattenParts($part->parts, $flattenedParts, $prefix . $index . '.');
            } else {
                $flattenedParts = flattenParts($part->parts, $flattenedParts, $prefix);
            }
            unset($flattenedParts[$prefix . $index]->parts);
        }
        $index++;
    }

    return $flattenedParts;
}

//connectionCheck("mail.cbsl-india.com", "143", "", "novalidate-cert", "web@cbsl-india.com", "Kdcs@08065");
// connection check return true or false
function connectionCheck($mailServer, $port, $ssl, $validate, $username, $password) {

    $hostname = "{" . $mailServer . ":" . $port . "/imap" . ((!empty($ssl)) ? ("/$ssl") : ("")) . ((!empty($validate)) ? ("/$validate") : ("")) . "}";
    $connection = imap_open($hostname, $username, $password); //or die(print_r(error_get_last()));
    if ($connection) {

        return TRUE;
    } else {

        return FALSE;
    }
}

?>
