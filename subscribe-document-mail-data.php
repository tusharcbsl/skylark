<?php

$userId = array();
$checksubs = mysqli_query($db_con, "SELECT * FROM tbl_document_subscriber WHERE subscribe_docid='$subdocId' and find_in_set('1',action_id)");
while ($rwcheckSubs = mysqli_fetch_assoc($checksubs)) {
    $userId[] = $rwcheckSubs['subscriber_userid'];
}
$userIds = implode(',', $userId);
$mailto = array();
$k = 1;
$touser = mysqli_query($db_con, "SELECT user_email_id,first_name FROM tbl_user_master WHERE user_id in($userIds)");
while ($rwtouser = mysqli_fetch_assoc($touser)) {
    $mailto[$k]['user_email_id'] = $rwtouser['user_email_id'];
    $mailto[$k]['first_name'] = $rwtouser['first_name'];
    $k++;
}

$documentName = $dname;
require_once './mail.php';
foreach ($mailto as $to) {
    $email = $to['user_email_id'];
    $name = $to['first_name'];
    if (SOCKET_ENABLED) {
        $paramsArray = array(
            'email' => $email,
            'filenamed' => $filenamed,
            'action' => 'filesubscribe',
            'projectName' => $projectName,
            'fileaction' => $fileaction,
            'name' => $name
        );
        mailBySocket($paramsArray);
    } else {
        $mailsent = mailsenttoDocumentSubscribeUsers($email, $filenamed, $projectName, $fileaction, $name);
    }
}

