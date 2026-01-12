<?php
//Client memory Check api
require_once 'connection.php';
if(isset($_POST['apikey']))
{ 
$api=decryptLicenseKey($_POST['apikey']);
$api= explode("%", $api);

if(isset($_POST['clientid']) && isset($_POST['size'])){
if($api[1]==$_POST['clientid']){
  $clientid = $_POST['clientid'];

  require_once 'client_validate.php';
 $check_validity_qry= mysqli_query($db_con, "select * from  tbl_client_master where client_id='$clientid'");//Query get validity of particular company user
 $validity_date= mysqli_fetch_assoc($check_validity_qry);//fetch validity timestamp from client table

   $size= $validity_date['total_memory'];//total user allow 
    $selected_file=$_POST['size'];
   // $size=$_SESSION['total_memory'];
    
    

    $total= mysqli_query($con, "select sum(doc_size) as totals from `tbl_document_master`");
    $total_fsize= mysqli_fetch_assoc($total);
    echo $total_memory_consume= $total_fsize['totals'];

    //print_r(number_format($total_memory_consume / 1000000, 2) . ' MB');

    //die;

 

 $total_memory_alot=formatSizeUnits($size);

  $free_memory=$total_memory_alot-$total_memory_consume;
  if($total_memory_alot<=($total_memory_consume+$selected_file))
  {
     
     $final= json_encode(array("status"=>"false","msg"=> "File Size (".remaingSizeConvert($selected_file).") is larger than available storage (".remaingSizeConvert($free_memory).")"));
  }else{
        $free_memory=$total_memory_alot-($total_memory_consume+$selected_file);
        $final= json_encode(array("status"=>"true","msg"=> "Remaining storage (".remaingSizeConvert($free_memory).") after uploading this file"));

  }


 echo $final;
}else{
    echo json_encode(array("Error"=>"Client Id is not matching with API Key"));
}
}else{
    echo json_encode(array("Error"=>"Clientid is missing"));
}
}

    function formatSizeUnits($size)
    {
        if ($type[1] == "MB")
        {
            $bytes = 1000*1000*$size;
        }
        else
        {
           $bytes = 1000*1000*1000*$size;
        }
        return $bytes;
    }
    function remaingSizeConvert($bytes)
    {
      if ($bytes >= 1000000000000)
        {
            $bytes = number_format($bytes / 1000000000000, 2) . ' TB';
        }
        elseif ($bytes >= 1000000000)
        {
            $bytes = number_format($bytes / 1000000000, 2) . ' GB';
        }
        elseif ($bytes >= 1000000)
        {
            $bytes = number_format($bytes / 1000000, 2) . ' MB';
        }
        elseif ($bytes >= 1000)
        {
            $bytes = number_format($bytes / 1000, 2) . ' KB';
        }
        elseif ($bytes > 1)
        {
            $bytes = $bytes . ' bytes';
        }
        elseif ($bytes == 1)
        {
            $bytes = $bytes . ' bytes';
        }
        else
        {
            $bytes = '0 bytes';
        }

        return $bytes;
}
?>