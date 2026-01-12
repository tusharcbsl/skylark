<?php
require'connection.php';
//$data=json_decode($_POST['metadata'],true);
	//print_r($data);

//get metadata of specific file
if(isset($_POST['docname'])&&!empty($_POST['docname'])&&isset($_POST['docid'])&&!empty($_POST['docid'])){
	
	$docname = $_POST['docname'];
	$docid = $_POST['docid'];
	
	$resu = array();
	
    $temp = array();
	
	$getMetaId = mysqli_query($con, "select * from tbl_metadata_to_storagelevel where sl_id = '$docname'") or die('Error:' . mysqli_error($con));
    while ($rwgetMetaId = mysqli_fetch_assoc($getMetaId)) {
                $getMetaName = mysqli_query($con, "select * from tbl_metadata_master where id = '$rwgetMetaId[metadata_id]'") or die('Error:' . mysqli_error($con));
                while ($rwgetMetaName = mysqli_fetch_assoc($getMetaName)) {

                    //echo "select `$rwgetMetaName[field_name]` from tbl_document_master where doc_id='$docid'";
                   // echo $rwgetMetaName['field_name'];
                   
                   $meta = mysqli_query($con, "select `$rwgetMetaName[field_name]` from tbl_document_master where doc_id='$docid'");
                    $rwMeta = mysqli_fetch_assoc($meta);
                    $rwMeta[$rwgetMetaName['field_name']];
                    array_push($resu, $rwMeta);
				
				
				}
		
	}
		
	    
		echo json_encode($resu);
	



}

//multimetadata
if(isset($_POST['metadata'])&&!empty($_POST['metadata'])&&isset($_POST['slid'])&& !empty($_POST['slid']))
{
	$data=json_decode($_POST['metadata'],true);
	//print_r($data);
	
    $slid=$_POST['slid'];
  //$slid = base64_decode(urldecode($_GET['slid']));
	
	 $table = "tbl_document_master";
    $sql_search = "select * from " . $table . " where flag_multidelete=1 and doc_name=$slid";
    $sql_search_fields = array();
	$data=$data['multiMetaSearch'];
	  for ($i = 0; $i < count($data); $i++) {
      if ( $data[$i]['cond'] == 'Like') {
        $sql_search_fields[] = 'CONVERT(`' . $data[$i]['metadata'] . "` USING utf8) like('%" .  $data[$i]['searchText'] . "%')";
      } else if ( $data[$i]['cond'] == 'Not Like') {
     $sql_search_fields[] = 'CONVERT(`' .  $data[$i]['metadata'] . "` USING utf8) not like('%" .  $data[$i]['searchText'] . "%')";
      } else if ( $data[$i]['cond'] == 'Contains') {
     $sql_search_fields[] = 'CONVERT(`' .  $data[$i]['metadata'] . "` USING utf8) contains('%" .  $data[$i]['searchText'] . "%')";
     } else if ( $data[$i]['cond'] == 'Equal') {
       $sql_search_fields[] = "`" .  $data[$i]['metadata']. "` ='" .  $data[$i]['searchText'] . "'";
                                                    }
                                                }
if(!empty($sql_search_fields)){
               $sql_search .= ' and (';
               $sql_search .= implode(" and ", $sql_search_fields);
               $sql_search .= ')';
}
//echo $sql_search;
                $rs3 = mysqli_query($con, $sql_search);
	            if(!$rs3){
					
				 $result[] = array('doc_id'=>"null",'error'=>"true");
			     echo json_encode($result);			
				
				}
	
	else
	{while(
		$rwData=mysqli_fetch_assoc($rs3)){
             			$datas[]=$rwData;
		          
		                
		
            					}
	if(!empty($datas))
	{  	
		 
         $result=json_encode($datas);
         echo $result;
	}
	
	
	else{	
		         $result[] = array('doc_id'=>"null",'error'=>"true");
			     echo json_encode($result);				
		}
	
	}
	
	     
                                         
	
}



