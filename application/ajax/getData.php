<?php
require_once '../../sessionstart.php';
if(!isset($_SESSION['cdes_user_id'])){
    header("location:../../logout.php");
}
require_once '../config/database.php';
 //$user_id = @$_GET['id']; 
$user_id = preg_replace("/[^0-9 ]/", "", $_POST['id']); 


$checkStorage = "select DISTINCT sl_id from tbl_storagelevel_to_permission where user_id = '$user_id'";

$checkStorage_run = mysqli_query($db_con, $checkStorage) or die('Error'.mysqli_error($db_con));

 $checkStorage_rw = mysqli_num_rows($checkStorage_run);

 if($checkStorage_rw>0){
     
$rwcheckStorage = mysqli_fetch_assoc($checkStorage_run);
$getStorageName = mysqli_query($db_con, "select sl_name from tbl_storage_level where sl_id = $rwcheckStorage[sl_id]") or die('Error:'. mysqli_error($db_con));
$rwgetStorageName = mysqli_fetch_assoc($getStorageName);

echo $rwgetStorageName['sl_name']; 
 }
/*
echo "\n";
  
  echo "<br>Selected Group:- ";

$checkUser = "select DISTINCT group_id from tbl_storagelevel_to_permission where user_id = '$user_id'";

//$checkPerm = "select DISTINCT permission_id from tbl_storagelevel_to_permission where user_id = '$user_id'";

$checkUser_run = mysqli_query($db_con, $checkUser) or die('Error'.mysqli_error($db_con));

$checkUser_rw = mysqli_num_rows($checkUser_run);

 if($checkUser_rw){
     
     while($rwcheckUser = mysqli_fetch_assoc($checkUser_run)){
         
        $grp_id = $rwcheckUser['group_id'];        //echo "\n";
         
      
        
        $fetchGrp = "select DISTINCT group_name from tbl_group_master where group_id = '$grp_id'";
        
        $fetchGrp_run = mysqli_query($db_con, $fetchGrp) or die('Error'.mysqli_error($db_con));
        //global $grp_name;
        
       // $grp_name = array();
        
         while($rwfetchGrp = mysqli_fetch_assoc($fetchGrp_run)){
        
        
         $grp_name = $rwfetchGrp['group_name'];  ///echo "\n";
         
          echo $grp_name; echo ", ";   
          
          //echo json_encode(array("data1"=>$grp_name)); //exit;
          
          // echo array("data1"=>$grp_name); //exit;
     }
  
             
     }
     
 }*/
  /*
  echo "\n";
  
  echo "<br>Selected Action:- ";
 
$checkPerm_run = mysqli_query($db_con, $checkPerm) or die('Error'.mysqli_error($db_con));

$checkPerm_rw = mysqli_num_rows($checkPerm_run);
  if($checkPerm_rw){
      
       while($rwcheckPerm = mysqli_fetch_assoc($checkPerm_run)){
           
             $per_id = $rwcheckPerm['permission_id'];   // echo "\n";
             
        $fetchPer = "select DISTINCT permission_name from tbl_permission_master where permission_id = '$per_id'";
        
        $fetchPer_run = mysqli_query($db_con, $fetchPer) or die('Error'.mysqli_error($db_con));
        
         while($rwfetchPer = mysqli_fetch_assoc($fetchPer_run)){
        
        
             $per_name = $rwfetchPer['permission_name'];  //echo "\n";
             
              echo $per_name;  echo ", "; 
             
           // echo json_encode(array("data2"=>$per_name));
     }
     
              
       }
    
   
     }

 */
?>