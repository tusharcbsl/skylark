<?php
require_once 'connection.php';
require_once 'classes/function.php';

//version of the file if present

if(isset($_POST['docid'])&&!empty($_POST['docid'])
   &&isset($_POST['slid'])&&!empty($_POST['slid'])
   &&isset($_POST['userid'])&&!empty($_POST['userid']))
{

	

	$slid = $_POST['slid'];
	$docid = $_POST['docid'];
	$userid = $_POST['userid'];
	
	
	$chekUsr = mysqli_query($con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$userid', user_ids) > 0") or die('Error:' . mysqli_error($db_con));
    $rwgetRole = mysqli_fetch_assoc($chekUsr);
	
	$result = array();
	
   $versionView = mysqli_query($con, "SELECT * FROM tbl_document_master where substring_index(doc_name,'_',-1)='$docid' and substring_index(doc_name,'_',1)='$slid' ") or die("Error: test" . mysqli_error($db_con));
   if (mysqli_num_rows($versionView) > 0) {

     $i = 1;
	 $count = 0;  
	   
       while ($rwView = mysqli_fetch_assoc($versionView)) {
		   
       if ($rwgetRole['file_version'] == '1') {
		   $docPath = getDocumentPath($con, $rwView['doc_id'],$rwView['old_doc_name'],$rwView['doc_path'], $rwView['doc_extn'], $rwView['doc_name'], $_POST['userid']);
      if ($i > 0) {
      $val = array(); 
      $val['docVersion'] ='Version :' . $i.".".$count . "\n"."Document name : ".$rwView['old_doc_name'];
	  $val['docpath'] =$docPath;
	  $val['docname'] = $rwView['old_doc_name'];
	  $val['docid'] = $rwView['doc_id'];	  
      array_push($result,$val);      		  
	  $count++; 	  
		  
      }
	
		   

}

	   }}

echo json_encode($result);


}


//getting the list of files

if(isset($_POST['id'])&& !empty($_POST['id']) && 
 isset($_POST['page'])&& !empty($_POST['page']) &&
 isset($_POST['userId'])&& !empty($_POST['userId'])

)
{

$slid=$_POST['id'];
//Getting the page number which is to be displayed  
$page = $_POST['page'];	


//Initially we show the data from 1st row that means the 0th row 
$start = 0; 
	
//Limit is 3 that means we will show 3 items at once
$limit = 25; 

//Counting the total item available in the database 
$total = mysqli_num_rows(mysqli_query($con, "SELECT * FROM  tbl_document_master where doc_name = '$slid' and flag_multidelete=1"));

//echo "total : " .$total;

//We can go atmost to page number total/limit
$page_limit = $total/$limit; 

//echo "page limit : ".$page_limit;

$page_limit = round($page_limit,0);
$page_limit = $page_limit + 1;

//echo "page limit : ".$page_limit;

$start = ($page - 1) * $limit; 
	
$result=array();
$res = array();

if($page<=$page_limit){



//pagination needed
$qry= mysqli_query($con, "SELECT * FROM  tbl_document_master where doc_name = '$slid' and flag_multidelete=1 order by dateposted desc limit $start,$limit");

/*print_r($qry);
die;*/

$count = 1;

while ($data= mysqli_fetch_assoc($qry))	
{
	$docPath = getDocumentPath($con, $data['doc_id'],$data['old_doc_name'],$data['doc_path'], $data['doc_extn'], $data['doc_name'], $_POST['userId']);
	
	 $temp= array();
	
	 $name= array();
	
	 $resu=array();
	
	$version = "";

	
	
	$userid= $data['uploaded_by'];
	
	$uplodedByName =  mysqli_query($con, "SELECT first_name,last_name from tbl_user_master where user_id=$userid");
	while($fullname=mysqli_fetch_assoc($uplodedByName)){
		
		$temp_name = array();
		$temp_name['fullname']=$fullname['first_name']." ".$fullname['last_name'];

		array_push($name,$temp_name);
		
		
		
	}
	
	
	$getMetaId = mysqli_query($con, "select * from tbl_metadata_to_storagelevel where sl_id = '$data[doc_name]'") or die('Error:' . mysqli_error($con));
    while ($rwgetMetaId = mysqli_fetch_assoc($getMetaId)) {
                $getMetaName = mysqli_query($con, "select * from tbl_metadata_master where id = '$rwgetMetaId[metadata_id]'") or die('Error:' . mysqli_error($con));
                while ($rwgetMetaName = mysqli_fetch_assoc($getMetaName)) {

                    $meta = mysqli_query($con, "select `$rwgetMetaName[field_name]` from tbl_document_master where doc_id='$data[doc_id]'");
                    $rwMeta = mysqli_fetch_assoc($meta);
                    $rwMeta[$rwgetMetaName['field_name']];
                    array_push($resu, $rwMeta);

                }
            }
	
	$d =$data['doc_id'];
	
   $versionView = mysqli_query($con, "SELECT count(doc_id) as version FROM tbl_document_master where substring_index(doc_name,'_',-1)='$d' and substring_index(doc_name,'_',1)='$slid' ") or die("Error: test" . mysqli_error($con));
   $rwVersion= mysqli_fetch_assoc($versionView);
	
	$versionPresent = $rwVersion['version'];
	if ($versionPresent== 0) {
     
	   $version="0";
    
   }
	
	else{
	
	
		$version ="1";
	}
	
	
	$getCheckinout = mysqli_query($con, "select checkin_checkout from  tbl_document_master where doc_id=$d") or die('Error:' . mysqli_error($con));
	$checkinStatus =  mysqli_fetch_assoc($getCheckinout);
	$status = $checkinStatus['checkin_checkout'];
	
	//if (preg_match('/^[1-9]+_[1-9]+$/', $str)) {

	         $dname = $data['doc_name']; 
		
		     if (preg_match('/^[1-9]+_/', $dname)) {
				 
				 
				// echo "contains underscore ";
			 
			 
			 
			 
			 }
		
		else{
			
				       	    
	        $temp['doc_id']=$data['doc_id'];
	        $temp['s.no'] = $count;
            $temp['doc_name']=$data['doc_name'];
            $temp['doc_extn']=$data['doc_extn'];
            $temp['doc_path']=$docPath;
            $temp['dateposted']=$data['dateposted'];
	        $temp['old_doc_name']=$data['old_doc_name'];     
            $temp['doc_size']=$data['doc_size'];
            $temp['noofpages']=$data['noofpages'];
            $temp['metadata'] = $resu;
	        $temp['name']=$name;
	        $temp['checkin_status'] = $status;
	        $temp['version_status'] = $version;
	
	 //  echo 'doesnt underscore ';
	
	
 array_push($result,$temp);

 $count++;
			
		
		}


}



$res['pageCount'] = $page_limit;
$res['totalfiles'] = $total;
$res['list'] = $result;


	     
}

else{



}

$fres=json_encode($res);
echo $fres;



}


		   ?>