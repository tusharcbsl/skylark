<?php

require_once 'connection.php';

if(isset($_POST['slid'])&&!empty($_POST['slid'])){


          $slid = $_POST['slid'];

          $checkblank= mysqli_query($con, "select count(*) as count from  tbl_storage_level where sl_parent_id='$slid'");
	
	   
	
          $r = mysqli_fetch_assoc($checkblank);
	      $fCount = $r['count'];
	
	 //print_r($r);
	
//	die;
	      
	     
          $fileqry= mysqli_query($con,"select count(doc_id) as doc from tbl_document_master where doc_name='$slid' and flag_multidelete=1");
          $totalfiles= mysqli_fetch_assoc($fileqry);
          $t=$totalfiles['doc'];
          
          $childcount ="";
          
          
          
          if($fCount>0 ||$t>0){
              
              $childcount ="1";
             // echo "storage ".count($r)." ";
             // echo "files ".count($t)." ";
          
          }
          
      
          
          else{
          
                 $childcount ="0";
                // echo "storage ".count($r)." ";
                //echo "files ".count($t)." ";
              
          }
		  
		  
		  $result = array();
		  $result['blank'] = $childcount;
		  $result ['error'] ='false';
	
	 echo json_encode($result);
		  

}










?>