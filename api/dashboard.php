<?php
require 'connection.php';
if(isset($_POST['userid'])&& !empty($_POST['userid']))
{
   $user_id=$_POST['userid'];
   $qry= mysqli_query($con, "select * from tbl_storagelevel_to_permission where user_id='$user_id'");
   $storagepermisson= mysqli_fetch_assoc($qry);
   $slid= $storagepermisson['sl_id'];
	
	$qry2 =mysqli_query($con, "SELECT sl_name FROM tbl_storage_level where sl_id ='$slid '"); 
    $storagename= mysqli_fetch_assoc($qry2); 
	$storagealloted =$storagename['sl_name'];
	
  	
   
   function totalfolder($slid)
   {
      global $storagerslt;
      $storagerslt=array();
      global $totalfolder;
      global $con;
      global $noFile;
      global $size;
      $Fileqry = mysqli_query($con, "select sum(doc_size) as totalfile, count(doc_name) as countfile from tbl_document_master where doc_name='$slid' and flag_multidelete=1") or die('SQL Error =' . mysqli_error($con));
      $rowfile = mysqli_fetch_assoc($Fileqry);
      $totalFSize = $rowfile['totalfile'];
      $noFile = $noFile+$rowfile['countfile'];
      $size=$size+$rowfile['totalfile'];
      $storagerslt['file']=$noFile;
      $storagerslt['size']=$size;
      if(!empty($slid))
      {
          $totalfolder=$totalfolder+1;
      }
     $storagerslt['folder']=$totalfolder;
     $folderqry= mysqli_query($con, "select * from  tbl_storage_level where sl_parent_id='$slid'");
      while($result= mysqli_fetch_assoc($folderqry))
      {
          $ch=$result['sl_id'];
          totalfolder($ch);
      }
      return $storagerslt;
      
   }
   
    function formatSizeUnits($bytes)
    {
        if ($bytes >= 1073741824)
        {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        }
        elseif ($bytes >= 1048576)
        {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        }
        elseif ($bytes >= 1024)
        {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        }
        elseif ($bytes > 1)
        {
            $bytes = $bytes . ' MB';
        }
        elseif ($bytes == 1)
        {
            $bytes = $bytes . ' MB';
        }
        else
        {
            $bytes = '0 MB';
        }

        return $bytes;
}

 
 if($user_id == '1'){


 $inTrayQry =  mysqli_query($con,"SELECT tdawf.id,tsm.task_name,tdawf.doc_id,tdawf.task_status,tdawf.task_remarks,tdawf.start_date,tdawf.end_date,tsm.deadline,tsm.deadline_type,tsm.priority_id,tdawf.assign_by,tdawf.NextTask FROM tbl_task_master tsm inner join tbl_doc_assigned_wf tdawf on tsm.task_id=tdawf.task_id");  


}

else{

$inTrayQry = mysqli_query($con, "SELECT tdawf.id,tsm.task_name,tdawf.doc_id,tdawf.task_status,tdawf.task_remarks,tdawf.start_date,tdawf.end_date,tsm.deadline,tsm.deadline_type,tsm.priority_id,tdawf.assign_by,tdawf.NextTask FROM tbl_task_master tsm inner join tbl_doc_assigned_wf tdawf on tsm.task_id=tdawf.task_id where ((tsm.assign_user='$user_id' and tdawf.NextTask='0') or (alternate_user='$user_id' and tdawf.NextTask= '3') or (supervisor='$user_id' and tdawf.NextTask= '4')) order by tdawf.id desc");


}
 
$total = mysqli_num_rows($inTrayQry);




 $dashboard=totalfolder($slid);
   $result=array();
   $result['slid']=$slid;
   $fsize= $dashboard['size'];
   $result['totalfolder']=$dashboard['folder']-1;
   $result['totalfile']=$dashboard['file'];
   $result['size']=formatSizeUnits($fsize);
   $result['storagealloted'] =$storagealloted;
   $result['in_tray'] = $total;
   $json= json_encode($result);
   echo $json;
  
}
?>