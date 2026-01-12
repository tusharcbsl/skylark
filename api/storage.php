<?php
require_once 'connection.php';
if(isset($_POST['userid'])&& !empty($_POST['userid'])&&isset($_POST['slid'])&&!empty($_POST['slid']))
{
   
    $slid= $_POST['slid'];
    $foldername['fname']=array();
    $foldername['fid']=array();
    $slstorageqry=mysqli_query($con,"select * from  tbl_storage_level where sl_id='$slid'");
    $fetchdata=mysqli_fetch_assoc($slstorageqry);
	$storagename=$fetchdata['sl_name'];
 
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
          $totalfolder+=1;
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
  
      function folderName()
   {
      global $foldername;
      global $totalfolder;
      global $con;
      global $noFile;
      global $size;
      $slid=$_POST['slid'];
      $folderqry= mysqli_query($con, "select * from  tbl_storage_level where sl_parent_id='$slid'");
     
       
       
      while($result= mysqli_fetch_assoc($folderqry))
      {
          $b = $result['sl_id'];
          
          $checkblank= mysqli_query($con, "select * from  tbl_storage_level where sl_parent_id='$b'");
          $r = mysqli_fetch_assoc($checkblank);
          $fileqry= mysqli_query($con,"select count(doc_id) as doc from tbl_document_master where doc_name='$slid' and flag_multidelete=1");
          $totalfiles= mysqli_fetch_assoc($fileqry);
          $t=$totalfiles['doc'];
          
          $childcount ="";
          
          
          
          if(count($r)>0 || count($t)>0){
              
              $childcount ="1";
             // echo "storage ".count($r)." ";
             // echo "files ".count($t)." ";
          
          }
          
      
          
          else{
          
                 $childcount ="0";
                // echo "storage ".count($r)." ";
                 //echo "files ".count($t)." ";
              
          }
         /* $fBlank = array();
          
          if( empty($fBlank['slid'])){
          
                $fBlank['slid'] = $result['sl_id'];
          }
          
          else{
          
               $fBlank['slid'] = "null";
          }
        
            if( empty($fBlank['storagename'])){
          
            $fBlank['storagename']=$result['sl_name'];
                
          }
          
          else{
          
               $fBlank['storagename']="null";
          }
          
          
         // $fBlank['storagename']=$result['sl_name'];
          $fBlank['folderBlank']=$childcount;*/
          
     // $ch=$result['sl_name']."&&".$ch1=$result['sl_id']."&&".$childcount ;
      $ch=$result['sl_name']."&&".$ch1=$result['sl_id'] ;
          
          
          array_push($foldername['fname'], $ch);
         
         
      }
      return $foldername;
      
   }
   
    function filesInFolder()
    {
        global $con;
        global $file;
        $slid=$_POST['slid'];
        $fileqry= mysqli_query($con,"select count(doc_id) as doc from tbl_document_master where doc_name='$slid' and flag_multidelete=1");
        $totalfiles= mysqli_fetch_assoc($fileqry);
        $result=$totalfiles['doc'];
        return $result;
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

 $dashboard=totalfolder($slid);
  $folder=folderName($slid);
   $result=array();
   $fsize= $dashboard['size'];
	$result['storagename']=$storagename;
   $result['foldername']=$folder['fname'];
   $result['totalfolder']=$dashboard['folder']-1;
   $result['currentstoragefile']=filesInFolder();
   $result['totalfile']=$dashboard['file'];
   $result['size']=formatSizeUnits($fsize);
   $json= json_encode($result);
   echo $json;
  
}
