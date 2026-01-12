<?php

/*

This api is for push notification 

$tokenid = token id of firebase of specific user.
$userid = userid of the specific user
$id = here id can be anything ticketid,taskid etc customize according to logic
$taskname = name of the task
$title = title of the push notication means the header 
$message = message for the push notification



*/

require_once 'connection.php';




function sendPushNotification($tokenid,$userid,$id,$taskname,$actionby,$title,$message){
 
 $userid = $userid;
 $registrationIds = array($tokenid);



 define( 'API_ACCESS_KEY', 'AAAAjURrpKU:APA91bEBlnzsIq2tw0N3eN-sObLGS491dffii-QB85b4H9xk-z99cJPpxYwZsLcs6L8mzjs7MhdhvGLiAuRVGkWY6pEudBl7cIz8NaOEkFVk803CMgoxwRwGo2E4FdSJGfT7dLM580Gn');

 //echo "access key : ".API_ACCESS_KEY;
 //$url = 'https://fcm.googleapis.com/fcm/send';

   date_default_timezone_set("Asia/Kolkata");
   $date = date("Y-m-d H:i");

$msg = array
(
  'message'   => $message,
  'title'   => $title,
  'id'   => $id,
  'taskname'   => $taskname,
  'actionBy' => $actionby,
  'datetime' => $date
  //'subtitle'  => 'This is a subtitle. subtitle',
  //'tickerText'  => 'Ticker text here...Ticker text here...Ticker text here',
  //'vibrate' => 1,
  //'sound'   => 1,
  //'largeIcon' => 'large_icon',
  //'smallIcon' => 'small_icon'
);



$fields = array
(
  'registration_ids'  => $registrationIds,
   'data'  => $msg
);

$headers = array
(
  'Authorization: key=' . API_ACCESS_KEY,
  'Content-Type: application/json'
);

$ch = curl_init();
curl_setopt( $ch,CURLOPT_URL, 'fcm.googleapis.com/fcm/send' );
curl_setopt( $ch,CURLOPT_POST, true );
curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
$result = curl_exec($ch );
curl_close( $ch );
//echo $result;

}


?>
 