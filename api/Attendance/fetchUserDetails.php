<?php

header('Content-type : bitmap; charset=utf-8');
require_once 'connection.php';

if(isset($_POST['userid'])&&!empty($_POST['userid'])){

$userid = $_POST['userid'];

	$result = array();

$sql = mysqli_query($con,"Select user_email_id,first_name,last_name,designation,phone_no,profile_picture,user_sign from tbl_user_master where user_id=$userid") or die('Error:' . mysqli_error($con));
	
if($sql){
	
	//base64_encode($rwuser['profile_picture']);
	
	$rwUserDetails = mysqli_fetch_assoc($sql);	
	
	//print_r($rwUserDetails);
	//die;
	$temp = array();
	$temp['firstname']=$rwUserDetails['first_name'];
	$temp['lastname']=$rwUserDetails['last_name'];
    $temp['email']=$rwUserDetails['user_email_id'];
	$temp['designation']=$rwUserDetails['designation'];
	$temp['contact']=$rwUserDetails['phone_no'];
    $temp['sign']=$rwUserDetails['user_sign'];

	
	$temp['profilepic']=base64_encode($rwUserDetails['profile_picture']);
	
    array_push($result,$temp);
	

    echo json_encode($result);  
	
	//echo '<img src="data:image/jpeg;base64,'.base64_encode( $rwUserDetails['profile_picture'] ).'"/>';	//$row=mysqli_fetch_all($sql,MYSQLI_ASSOC);
		
}
	
	else{
		
		$result[error] = 'true';
		$result[message] = 'User details not fetched';
		
		echo json_encode($result);
		
	}



	



}


?>