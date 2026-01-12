<?php

//Not working 
require_once 'connection.php';


if(isset($_POST['slid'])&&!empty($_POST['slid'])){


  $slid = $_POST['slid'];
  
	
  $foldernameArray= array();	

  
  $slstorageqry=mysqli_query($con,"select * from  tbl_storage_level where sl_id='$slid'");
  $fetchdata=mysqli_fetch_assoc($slstorageqry);
	$storagename=$fetchdata['sl_name']."&&".$slid;
  
  
  $foldernameQuery ="select * from  tbl_storage_level where sl_parent_id='$slid'";
  
  $foldername = mysqli_query($con,$foldernameQuery);
  
	while($rwFoldername = mysqli_fetch_assoc($foldername)){
		
		$temp = array();
		$temp['foldername'] = $rwFoldername['sl_name']."&&".$rwFoldername['sl_id'];
		array_push($foldernameArray,$temp);
		
		
		
		
	}
  
  $result = array();
  $result['storagename']= $storagename;
  $result['foldername'] =$foldernameArray;
  

 
	
	echo json_encode($result);
  
  



}




?>