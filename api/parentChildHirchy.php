<?php
require_once 'connection.php';

if(isset($_POST['userid'])&&!empty($_POST['userid'])){
   

	$res = array();
    $userid=$_POST['userid'];
	
	 $perm = mysqli_query($con, "select sl_id from tbl_storagelevel_to_permission where user_id='$userid' group by sl_id");
                                                    $rwPerm = mysqli_fetch_assoc($perm);
                                                    $slperm = $rwPerm['sl_id'];
                                                    $sllevel = mysqli_query($con, "select * from tbl_storage_level where sl_id='$slperm'");
                                                    $rwSllevel = mysqli_fetch_assoc($sllevel);
                                                    $level = $rwSllevel['sl_depth_level'];
	
    $slstorageqry=mysqli_query($con,"select * from  tbl_storage_level where sl_id='$slperm'");
    $fetchdata=mysqli_fetch_assoc($slstorageqry);
	$storagename=$fetchdata['sl_name'];
 
	 findChild($slperm, $level, $slperm, $slperm);
    
    //echo json_encode($res);
	


}



  function findChild($sl_id, $level, $slperm, $parentId) {

                                                        global $con;
	                                                    global $res;

                                                        if ($sl_id == $parentId) {
                                                           // echo '<option value="' . $sl_id . '"  selected>';
                                                            $value = parentLevel($sl_id, $con, $slperm, $level, '');
                                                           // echo '</option>';
														   echo $a =  $value."&&".$sl_id. "\n";
														
															
															//array_push($res,$a);
                                                        } else {
                                                           // echo '<option value="' . $sl_id . '" >';
                                                            $value = parentLevel($sl_id, $con, $slperm, $level, '');
                                                            //echo '</option>';
														  echo $a =  $value."&&".$sl_id. "\n";
														
															
                                                        }

                                                        $sql_child = "select * FROM tbl_storage_level WHERE sl_parent_id = '$sl_id' ";

                                                        $sql_child_run = mysqli_query($con, $sql_child) or die('Error:' . mysqli_error($con));

                                                        if (mysqli_num_rows($sql_child_run) > 0) {

                                                            while ($rwchild = mysqli_fetch_assoc($sql_child_run)) {

                                                                $child = $rwchild['sl_id'];
                                                                findChild($child, $level, $slperm, $parentId);
                                                            }
                                                        }
                                                    }

                                                    function parentLevel($slid, $con, $slperm, $level, $value) {
														
													  global $con;
	                                                    global $res;

                                                        if ($slperm == $slid) {
                                                            $parent = mysqli_query($con, "select * from tbl_storage_level where sl_id='$slid' ") or die('Error' . mysqli_error($con));
                                                            $rwParent = mysqli_fetch_assoc($parent);

                                                            if ($level < $rwParent['sl_depth_level']) {
                                                                parentLevel($rwParent['sl_parent_id'], $con, $slperm, $level, $rwParent['sl_name']);
                                                            }
                                                        } else {
                                                            $parent = mysqli_query($con, "select * from tbl_storage_level where sl_id='$slid' and sl_parent_id='$slperm'") or die('Error' . mysqli_error($con));
                                                            if (mysqli_num_rows($parent) > 0) {

                                                                $rwParent = mysqli_fetch_assoc($parent);
                                                                if ($level < $rwParent['sl_depth_level']) {
                                                                    parentLevel($rwParent['sl_parent_id'], $con, $slperm, $level, $rwParent['sl_name']);
                                                                }
                                                            } else {
                                                                $parent = mysqli_query($con, "select * from tbl_storage_level where sl_id='$slid'") or die('Error' . mysqli_error($con));
                                                                $rwParent = mysqli_fetch_assoc($parent);
                                                                $getparnt = $rwParent['sl_parent_id'];
                                                                if ($level <= $rwParent['sl_depth_level']) {
                                                                    parentLevel($getparnt, $con, $slperm, $level, $rwParent['sl_name']);
                                                                } else {
                                                                    //header('Location: ./index.php');
                                                                    // header("Location: ./storage?id=".urlencode(base64_encode($slperm)));
                                                                }
                                                            }
                                                        }

                                                        //echo $value;
                                                        if (!empty($value)) {
                                                            $value = $rwParent['sl_name'] . ' > ';
													    //array_push($res,$value);
														//print_r($value);
															
                                                        } else {
                                                            $value = $rwParent['sl_name'];
														//print_r($value);
													
															
                                                        }
                                                        //array_push($res,$value);
													echo $value;
													
													
                                                    }


         
         
      
      // folderName($storagename,$result['sl_id']);
       
       
     
      //return $foldername;
      
   





//global variable

/*
$aa = array();


if (isset($_POST['slid'])&&!empty($_POST['slid'])&&isset($_POST['text'])&&!empty($_POST['text'])) {
    $sl_id = $_POST['slid'];
	$text=$_POST['text'];
    searchAllDb($text,$sl_id,$con);



}


function searchAllDb($search,$slid,$con){

    $table = "tbl_document_master";

    //$out .= $table.";";
    $sql_search = "select doc_extn,doc_name,doc_id,doc_path,old_doc_name,doc_size,noofpages from " . $table . " where ";
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

    if(mysqli_num_rows($rs3)>0){

        while($data =mysqli_fetch_assoc($rs3)){



            $temp = array();
            $resu=array();


            $strgName = mysqli_query($con, "select * from tbl_storage_level where sl_id = '$data[doc_name]'") or die('Error:' . mysqli_error($con));
            $rwstrgName = mysqli_fetch_assoc($strgName);

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


            $temp['doc_id']=$data['doc_id'];
            $temp['doc_name']=$data['doc_name'];
            $temp['doc_extn']=$data['doc_extn'];
            $temp['old_doc_name']=$data['old_doc_name'];
            $temp['doc_size']=$data['doc_size'];
            $temp['noofpages']=$data['noofpages'];
            $temp['storagename'] =$rwstrgName['sl_name'];
            $temp['metadata'] = $resu;

            array_push($Data,$temp);

        }

        /*    $strgName = mysqli_query($con, "select * from tbl_storage_level where sl_id = '$data[doc_name]'") or die('Error:' . mysqli_error($con));
            $rwstrgName = mysqli_fetch_assoc($strgName);
            echo $rwstrgName['sl_name'];

        echo json_encode($Data);

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
}*/



?>
