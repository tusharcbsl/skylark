<?php

require_once 'connection.php';

//getting the assigned metadata list 
if(isset($_POST['slid'])&&!empty($_POST['slid'])){

$slid = $_POST['slid'];

  $metas = mysqli_query($con, "select * from tbl_metadata_to_storagelevel where sl_id='$slid'") or die('Error: metaData assign' . mysqli_error($con));
  
  //$rwMetas = mysqli_fetch_all($metas,MYSQLI_ASSOC);
  
  $metadatalist = array();
  
  
  while($rwMetas = mysqli_fetch_assoc($metas)){
    
     $temp = array(); 
     $temp = $rwMetas['metadata_id'];   
     array_push( $metadatalist,$temp);
     
  }
  
  $metadatalist = implode(",",$metadatalist);
  
  //print_r($metadatalist);
  
  
  
  // die;
  
   
  
   $metaname = mysqli_query($con," select * from tbl_metadata_master where id in ($metadatalist)") or die('Error: metaData assign' . mysqli_error($con));
  
   
   
   
  
  
  $rwMetas = mysqli_fetch_all($metaname,MYSQLI_ASSOC);
  
  echo json_encode($rwMetas);

//$metadatalist = mysqli_query($con, "select * from tbl_metadata_master order by field_name asc");





}


//getting all metadata list 
if(isset($_POST['metadata'])&&!empty($_POST['metadata'])){

   
  
  $metadatalist = mysqli_query($con, "select * from tbl_metadata_master order by field_name asc");
  
   
   
   
  
  
  $rwMetas = mysqli_fetch_all( $metadatalist ,MYSQLI_ASSOC);
  
  echo json_encode($rwMetas);

//$metadatalist = mysqli_query($con, "select * from tbl_metadata_master order by field_name asc");





}

//update metadalist of the specific folder

  if(isset($_POST['assignUserid'])&&!empty($_POST['assignUserid'])
	  &&isset($_POST['assignUsername'])&&!empty($_POST['assignUsername'])
	   &&isset($_POST['assignIp'])&&!empty($_POST['assignIp'])
	    &&isset($_POST['assignMetaList'])&&!empty($_POST['assignMetaList']) 
   &&isset($_POST['folderId'])&&!empty($_POST['folderId']) )

	  {
            $childName = $_POST['folderId'];
      
      
			  
            $childName = mysqli_real_escape_string($con, $childName);
            $userid = $_POST['assignUserid'];
			   $username = $_POST['assignUsername'];
			    
         $fields = json_decode($_POST['assignMetaList'],true);
      
        // print_r($fields);
 
			   $host = $_POST['assignIp'];
      date_default_timezone_set("Asia/Kolkata");
					$date = date("Y-m-d H:i");
      
			   

            $flag = 0;
            if (!empty($childName)) {
                $reset = mysqli_query($con, "delete from tbl_metadata_to_storagelevel where sl_id='$childName'");
            }
            $metaDataNames = array();
            foreach ($fields as $field) {
                if (!empty($childName)) {
                    //check meta data assigned or not
                    $match = mysqli_query($con, "select * from tbl_metadata_to_storagelevel where sl_id='$childName' and metadata_id='$field'") or die('Error:' . mysqli_error($con));
                    if (mysqli_num_rows($match) <= 0) {
                        //assign meta data
                        $create = mysqli_query($con, "insert into tbl_metadata_to_storagelevel (`metadata_id`, `sl_id`) values('$field','$childName')") or die('Error' . mysqli_error($con));
                        // find meta data details
                        $metan = mysqli_query($con, "select * from tbl_metadata_master where id='$field'");
                        $rwMetan = mysqli_fetch_assoc($metan);
                        $metaDataNames[] = $rwMetan['field_name'];
                        //check meta data in table tbl_document_master
                        $checkDoc = mysqli_query($con, "SHOW COLUMNS FROM tbl_document_master LIKE '$rwMetan[field_name]'");
                        if (mysqli_num_rows($checkDoc) <= 0) { //if not
                            $metaCreateDoc = mysqli_query($con, "ALTER TABLE tbl_document_master ADD `$rwMetan[field_name]` $rwMetan[data_type]($rwMetan[length_data])  null");
                        }
                        $flag = 1;
                        $sl_id = $childName;
                    } else {
                        $sl_id = $childName;
                    }
                }
            }
            if ($flag == 1) {
                $metaDataNames = implode(",", $metaDataNames);
                $strgeName = mysqli_query($con, "select sl_name from tbl_storage_level where sl_id = '$sl_id'");
                $rwstrgeName = mysqli_fetch_assoc($strgeName);
                $storageName = $rwstrgeName['sl_name'];
                $log = mysqli_query($con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$userid', '$username',null, '$sl_id','MetaData($metaDataNames)  Assigned on storage $storageName','$date',null,'$host', null)") or die('error : ' . mysqli_error($con));
					 
					 $res = array();
					 $res['error'] ='false';
					 $res ['message'] = 'Metadata Update Succesfully';
					 echo json_encode($res);
                //echo '<script>metasuccess("storage?id=' . $_GET['id'] . '");</script>';
            } else {
               // echo '<script>metafailed("storage?id=' . $_GET['id'] . '");</script>';
					 $res = array();
					 $res['error'] ='true';
					 $res ['message'] = 'Metadata Updation Failed';
					 echo json_encode($res);
            }
            mysqli_close($con);
        }
       





?>