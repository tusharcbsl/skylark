<?php
error_reporting(E_ALL);
//echo 'ok';  
 /*$hostname = "{imap.gmail.com:993/imap/ssl/novalidate-cert}";
$mailbox = imap_open($hostname, 'kuldeep446@gmail.com', 'kdcs@@08065');
$emails = imap_search($mailbox,  'SINCE "27-Nov-2017"' );// or die(print_r(error_get_last()));
//echo 'count'.count($emails);
//print_r($emails);
foreach($emails as $e){
    $overview = imap_fetch_overview($mailbox,$e,0);
    $message = imap_fetchbody($mailbox,$e,2);
	//$header=imap_header($mailbox,$e);
	//print_r($header);
	//echo $header->subject;
	//echo $header->toaddress;
	echo '<br>';
    // the body of the message is in $message
   $details = $overview[0];
   echo $details->subject;
   echo $details->to;
   echo '<br>';
  var_dump($details);
  //print_r($details);
  //echo $message;
  /*foreach($details as $detail){
	  echo $detail;
  }*/
	// you can do a var_dump($details) to see which parts you need
	//then do whatever to insert them into your DB
//} 
//echo 'ok';
//print_r($emails);


// for mails
$hostname = "{mail.cbsl-india.com:143/imap/novalidate-cert}";
 $username = 'web@cbsl-india.com';
$password = 'kdcs08065';  //die(); echo 'ok';

$connection = imap_open($hostname,$username,$password) or die(print_r(error_get_last()));
$emails = imap_search($connection,  'SINCE "01-Dec-2017"' );// or die(print_r(error_get_last()));
//echo 'count'.count($emails);
//print_r($emails);
$mailfolder=imap_list($connection,$hostname,"*");
foreach($mailfolder as $folder){
	echo $shortname= str_replace($hostname,"",$folder); 
	echo '<br>';
	imap_reopen($connection, "$hostname$shortname") or die(implode(", ", imap_errors()));
	
	mails($connection);
	echo '<br>';
}
function mails($reconn){
	$emails=array();
	$emails = imap_search($reconn,  'SINCE "01-Dec-2017"' );
	
	if(!empty($emails)){
foreach($emails as $e){
    $overview = imap_fetch_overview($reconn,$e,0);
    $message = imap_fetchbody($reconn,$e,2);
	//$header=imap_header($mailbox,$e);
	//print_r($header);
	//echo $header->subject;
	//echo $header->toaddress;
	echo '<br>';
    // the body of the message is in $message
   $details = $overview[0];
   echo $details->subject;
   echo $details->to;
   echo '<br>';
  var_dump($details);
  //print_r($details);
  //echo $message;
  /*foreach($details as $detail){
	  echo $detail;
  }*/
	// you can do a var_dump($details) to see which parts you need
	//then do whatever to insert them into your DB
}
} 
}
imap_close($connection);
?>