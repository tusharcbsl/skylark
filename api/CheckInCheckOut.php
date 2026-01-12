<?php

require_once 'connection.php';


if(isset($_POST['checkin'])&&!empty($_POST['checkin'])){

	$response = array();
 
$checkinId = $_POST['checkin'];



$checkin = mysqli_query($con, "UPDATE tbl_document_master set checkin_checkout=1 WHERE doc_id='$checkinId'");
if($checkin){
	
	$res= array();
	$res['message'] = 'Checkin in successful';
	$res['error'] = 'false';
	array_push($response,$res);
	
	echo json_encode($response);
	
	
}
	else{
		
			
	$res= array();
	$res['message'] = 'Checkin not successful';
	$res['error'] = 'true';
	array_push($response,$res);
		echo json_encode($response);
		
	}
	

}



if(isset($_POST['checkout'])&&!empty($_POST['checkout'])){

	$response = array();
 

$checkoutId = $_POST['checkout'];

$checkout = mysqli_query($con, "UPDATE tbl_document_master set checkin_checkout=0 WHERE doc_id='$checkoutId'");
if($checkout){
	
	$res= array();
	$res['message'] = 'Check Out successful';
	$res['error'] = 'false';
	array_push($response,$res);
	
		echo json_encode($response);
	
	
}
	else{
		
			
	$res= array();
	$res['message'] = 'Check Out not successful';
	$res['error'] = 'true';
	array_push($response,$res);
	
		echo json_encode($response);
		
		
	}
	

}









?>