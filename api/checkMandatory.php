<?php

require_once 'connection.php';

if(isset($_POST['slid'])&&!empty($_POST['slid'])){

     $sl_id = $_POST['slid'];//parent slid
   
	 $response = array();
	 $result = mysqli_query($con, "SELECT tmm.field_name,data_type,length_data,mandatory FROM tbl_metadata_to_storagelevel tms INNER JOIN tbl_metadata_master tmm  ON tms.metadata_id = tmm.id where tms.sl_id=$sl_id") or die('Error in checkLvlName:' . mysqli_error($con));
	
	$row=mysqli_fetch_all($result,MYSQLI_ASSOC);
   echo json_encode($row);           	
	
}

if(isset($_POST['slidCheckin']) && !empty($_POST['slidCheckin']) && isset($_POST['docidCheckin']) && !empty($_POST['docidCheckin'])){


	$slid = $_POST['slidCheckin'];
	$docid = $_POST['docidCheckin'];
	
	$res = array();

   $result = mysqli_query($con, "SELECT tmm.field_name,data_type,length_data,mandatory FROM tbl_metadata_to_storagelevel tms INNER JOIN tbl_metadata_master tmm  ON tms.metadata_id = tmm.id where tms.sl_id=$slid") or die('Error in checkLvlName:' . mysqli_error($con));
   while($r= mysqli_fetch_assoc($result)){
   
       $fieldname = $r['field_name'];
       $length = $r['length_data'];
       $mandatory = $r['mandatory'];
	   $datatype = $r['data_type'];
	   
      // echo "select `$fieldname` as fieldvalue from tbl_document_master where doc_id =$docid";
       //die;

	   $qry =mysqli_query($con,"select `$fieldname` as fieldvalue from tbl_document_master where doc_id ='$docid'");
	   $f =  mysqli_fetch_assoc($qry);
	   
	   $fieldvalue = $f['fieldvalue'];
	   
	  $temp = array();
	  $temp['fieldname'] = $fieldname;
	  $temp['fieldvalue'] = $fieldvalue;
	  $temp['mandatory'] = $mandatory;
      $temp['length'] =$length;
	  $temp['data_type']= $datatype;
	   
	  array_push($res,$temp);
	   
	   
	   
	  
   
   }	



echo json_encode($res);




}



?>