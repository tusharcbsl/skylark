<?php

require_once("imapReader.php");

$box = new ImapReader('mail.cbsl-india.com', '143', 'web@cbsl-india.com', 'kdcs08065');
$box->connect();//->fetchAllHeaders();

echo $box->count() . " emails in mailbox\n";
for ($i = 0; ($i < $box->count()); $i++)
{
    $msg = $box->get($i);
    echo "Reception date : {$msg->date}\n";
    echo "From : {$msg->from}\n";
    echo "To : {$msg->to}\n";
    echo "Reply to : {$msg->from}\n";
    echo "Subject : {$msg->subject}\n";
    $msg = $box->fetch($msg);
    echo "Number of readable contents : " . count($msg->content) . "\n";
    foreach ($msg->content as $key => $content)
    {
        echo "\tContent  " . ($key + 1) . " :\n";
        echo "\t\tContent type : {$content->mime}\n";
        echo "\t\tContent charset : {$content->charset}\n";
        echo "\t\tContent size : {$content->size}\n";
    }
    echo "Number of attachments : " . count($msg->attachments) . "\n";
    foreach ($msg->attachments as $key => $attachment)
    {
        echo "\tAttachment " . ($key + 1) . " :\n";
        echo "\t\tAttachment type : {$attachment->type}\n";
        echo "\t\tContent type : {$attachment->mime}\n";
        echo "\t\tFile name : {$attachment->name}\n";
        echo "\t\tFile size : {$attachment->size}\n";
    }
    echo "\n";
}

echo "Searching '*Bob*' ...\n";
$results = $box->searchBy('*Analytic*', ImapReader::FROM);
foreach ($results as $result)
{
    echo "\tMatched: {$result->from} - {$result->date} - {$result->subject}\n";
}
?>