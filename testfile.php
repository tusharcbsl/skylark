<?php

//echo 'this is code';
require_once 'sessionstart.php';
require_once './application/config/database.php';

$hour = "today";
//echo date("Y-m-d h:i:sa",1530713096);
echo strtotime(date("Y-m-d", strtotime("+" . $hour))) . "<br>";
echo date("Y-m-d h:i:s A", 1536517800);
//    $role_fieldName=array();
//            $data_role_col= mysqli_query($db_con, "show COLUMNS FROM `tbl_user_roles`")or die(mysqli_error($db_con));
//            while ($row = mysqli_fetch_assoc($data_role_col)) {
//                $role_fieldName[]=$row['Field'];
//                
//            }
//            unset($role_fieldName[0]);// remove role id becoz it is autoincrement
//            $data_cols_role= implode(",", $role_fieldName);
//            $result_cols=array();
//            $selected_roles_data= mysqli_query($db_con, "select $data_cols_role from `tbl_user_roles` where role_id='2'");
//            $newdata= mysqli_fetch_all($selected_roles_data);
//            $new_imploded_data= "'".implode("'".","."'", $newdata[0])."'";
//            echo  "insert into `tbl_user_roles`($data_cols_role)values($new_imploded_data)";
//            //$insert_role= mysqli_query($db_con, "insert into `tbl_user_roles`($data_cols_role)values($new_imploded_data)") or die(mysqli_error($db_con));
//      




//function fileSize
//    $size=$_SESSION['total_memory'];
//    function formatSizeUnits($size)
//    {
//        $newSize= str_replace(" ", "-", $size);
//        $type=explode("-",$newSize);
//        //print_r($type);
//        if ($type[1] == "MB")
//        {
//            $bytes = 1024*1024*$type[0];
//        }
//        else
//        {
//           $bytes = 1024*1024*1024*$type[0];
//        }
//   
//
//        return $bytes;
//}
//
//    $total= mysqli_query($db_con, "select sum(doc_size) as totals from `tbl_document_master`");
//    $total_fsize= mysqli_fetch_assoc($total);
//    $total_memory_consume= $total_fsize['totals'];
//
// function remaingSizeConvert($bytes)
//    {
//        if ($bytes >= 1073741824)
//        {
//            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
//        }
//        elseif ($bytes >= 1048576)
//        {
//            $bytes = number_format($bytes / 1048576, 2) . ' MB';
//        }
//        elseif ($bytes >= 1024)
//        {
//            $bytes = number_format($bytes / 1024, 2) . ' KB';
//        }
//        elseif ($bytes > 1)
//        {
//            $bytes = $bytes . ' MB';
//        }
//        elseif ($bytes == 1)
//        {
//            $bytes = $bytes . ' MB';
//        }
//        else
//        {
//            $bytes = '0 MB';
//        }
//
//        return $bytes;
//}
//
// $total_memory_alot=formatSizeUnits($size);
//
//  $free_memory=$total_memory_alot-$total_memory_consume;
//  if($total_memory_alot<$total_memory_consume)
//  {
//      echo "No More Memory ";
//  }else{
//  echo remaingSizeConvert($free_memory);
//  }
// 
