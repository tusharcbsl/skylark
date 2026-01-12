<?php
require_once 'connection.php';
require_once 'classes/function.php';

//global variable
$aa = array();


if (isset($_POST['slid']) && 
    !empty($_POST['slid'])&& 
        isset($_POST['text'])&&
          !empty($_POST['text'])&&
             isset($_POST['userid'])&&
               !empty($_POST['userid'])

     ) {
    

    $sl_id = $_POST['slid'];
	$text=$_POST['text'];
    $userid = $_POST['userid'];
    searchAllDb($text,$sl_id,$userid,$con);



}


function searchAllDb($search,$slid,$userid,$con){

    $table = "tbl_document_master";

    //$out .= $table.";";
    $sql_search = "select doc_extn,doc_name,doc_id,doc_path,old_doc_name,doc_size,noofpages from " . $table . " where";
    // findChild($slperm);
    $sql_search_fields = Array();
    $fields = array();
    $sql2 = "SHOW COLUMNS FROM " . $table;
    $rs2 = mysqli_query($con, $sql2);
    if (mysqli_num_rows($rs2) > 0) {

        while ($r2 = mysqli_fetch_array($rs2)) {
            $colum = $r2[0];

            $sql_search_fields[] = 'CONVERT(`' . $colum . "` USING utf8) like('%" . $search . "%')";
            $fields[] = $colum;
        }

        //mysqli_close($rs2);
    }

    $marray = findChild($slid);
    $sql_search .= "( doc_name=";
    $sql_search .= implode(" OR doc_name=", $marray);
    //$sql_search .= ' and '.findChild($slperm);
    $sql_search .= ") and (";

    $sql_search .= implode(" OR ", $sql_search_fields);
    $sql_search .= ")";

    $rs3 = mysqli_query($con, $sql_search);
    
    $Data = array();
    
    if(!empty($rs3)){
   
   //echo mysqli_num_rows($rs3);
 //die;
   
    if(mysqli_num_rows($rs3)>0){

        while($data =mysqli_fetch_assoc($rs3))

        {

            $temp = array();
            $resu=array();
            $strgName = mysqli_query($con, "select * from tbl_storage_level where sl_id = '$data[doc_name]'") or die('Error:' . mysqli_error($con));
            $rwstrgName = mysqli_fetch_assoc($strgName);

            $getMetaId = mysqli_query($con, "select * from tbl_metadata_to_storagelevel where sl_id = '$data[doc_name]'") or die('Error:' . mysqli_error($con));

           $docPath = getDocumentPath($con, $data['doc_id'],$data['old_doc_name'],$data['doc_path'], $data['doc_extn'], $data['doc_name'], $userid);
            
            $dname = $data['doc_name']; 
        
            if (preg_match('/^[1-9]+_/', $dname)) {
                    
                // echo "contains underscore ";
                                       
             }

             else

         {

            $temp['doc_id']=$data['doc_id'];
            $temp['doc_name']=$data['doc_name'];
            $temp['doc_extn']=$data['doc_extn'];
            $temp['doc_path']=$docPath;
            //$temp['dateposted']=$data['dateposted'];
            $temp['old_doc_name']=$data['old_doc_name'];
            $temp['doc_size']=$data['doc_size'];
            $temp['noofpages']=$data['noofpages'];
            $temp['storagename'] =$rwstrgName['sl_name'];
           // $temp['metadata'] = $resu;
            

            array_push($Data,$temp);

             }


          
        }

        /*    $strgName = mysqli_query($con, "select * from tbl_storage_level where sl_id = '$data[doc_name]'") or die('Error:' . mysqli_error($con));
            $rwstrgName = mysqli_fetch_assoc($strgName);
            echo $rwstrgName['sl_name'];*/
      
             
         //echo count($Data);
        echo json_encode($Data);
    
    }
    
    else{

         $Data = array();
         echo json_encode($Data);
        
    
    }

   

    }




}


function findChild($slperm)
{

    global $con;
    global $aa;
    $aa[] = $slperm;
    $sql_child = "select * FROM tbl_storage_level WHERE sl_parent_id = '$slperm' ";
    $sql_child_run = mysqli_query($con, $sql_child) or die('Error:' . mysqli_error($con));//
    if (mysqli_num_rows($sql_child_run) > 0) {

        while ($rwchild = mysqli_fetch_assoc($sql_child_run)) {

            $child = $rwchild['sl_id'];
            findChild($child);
        }
    }
    return $aa;
}



?>
