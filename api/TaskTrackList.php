<?php

require_once 'connection.php';
require_once 'classes/function.php';

if(isset($_POST['userid'])&&!empty($_POST['userid']) &&isset($_POST['page'])&&!empty($_POST['page']))
{


$userid = $_POST['userid'];
//Getting the page number which is to be displayed  
$page = $_POST['page']; 


//Initially we show the data from 1st row that means the 0th row 
$start = 0; 
  
//Limit is 3 that means we will show 3 items at once
$limit = 10; 

if ($userid == 1) {

  //$taskTrack = "SELECT * FROM  tbl_doc_assigned_wf group by ticket_id order by id desc limit $start,$perPage";
  $taskTrack = "SELECT * FROM  tbl_doc_assigned_wf group by ticket_id order by id desc";

} else {

  //$taskTrack = "SELECT * FROM  tbl_doc_assigned_wf where assign_by = '$userid' group by ticket_id order by id desc limit $start,$perPage";
  $taskTrack = "SELECT * FROM  tbl_doc_assigned_wf where assign_by = '$userid' group by ticket_id order by id desc";
}


//Counting the total item available in the database 
$total = mysqli_num_rows(mysqli_query($con, $taskTrack));
//echo $total;



//echo "total : " .$total;

//We can go atmost to page number total/limit
$page_limit = $total/$limit; 


$page_limit = round($page_limit,0);
$page_limit = $page_limit + 1;

/*echo "page limit : ".$page_limit;
die;*/  
$result = array();

$start = ($page - 1) * $limit; 

if($page<=$page_limit){



if ($userid == 1) {

  //$taskTrack = "SELECT * FROM  tbl_doc_assigned_wf group by ticket_id order by id desc limit $start,$perPage";
  $taskTrack = "SELECT * FROM  tbl_doc_assigned_wf group by ticket_id order by id desc limit $start,$limit";

} else {

  //$taskTrack = "SELECT * FROM  tbl_doc_assigned_wf where assign_by = '$userid' group by ticket_id order by id desc limit $start,$perPage";
  $taskTrack = "SELECT * FROM  tbl_doc_assigned_wf where assign_by = '$userid' group by ticket_id order by id desc limit $start,$limit";
}

 //echo $taskTrack;

 //die;
$run = mysqli_query($con, $taskTrack) or die('Error' . mysqli_error($con));

 while ($tasklist = mysqli_fetch_assoc($run)){
    
   
   
     $taskId = $tasklist['ticket_id'];
     $taskstatus = $tasklist['task_status'];

     $checkRj = mysqli_query($con, "select * from tbl_doc_assigned_wf where ticket_id='$taskId' and task_status='Rejected'");
     $num = mysqli_num_rows($checkRj);

     if($num==1){

        $taskstatus ='Rejected'; 
     

     }

     else{


     $checkRj = mysqli_query($con, "select * from tbl_doc_assigned_wf where ticket_id='$taskId' order by id desc");
     $rwCheckrj = mysqli_fetch_assoc($checkRj);
     if ($rwCheckrj['task_status'] == 'Aborted') {
       $taskstatus = $rwCheckrj['task_status'];
      } else if ($rwCheckrj['task_status'] == 'Pending') {
    $taskstatus = $rwCheckrj['task_status'];
      } else {
     $taskstatus = $rwCheckrj['task_status'];
         }

     }
    
      

   $temp = array();
   $temp['id'] = $tasklist['id'];
   $temp['task_id'] = $tasklist['task_id'];
   $temp['doc_id'] = $tasklist['doc_id'];
   $temp['assign_by'] = $tasklist['assign_by'];
   $temp['ticket_id'] = $tasklist['ticket_id'];
   $temp['start_date'] = $tasklist['start_date'];

   //here editing
     //$temp['task_status'] = $tasklist['task_status'];
   $temp['task_status'] = $taskstatus;

   array_push($result,$temp);

    }

    $res = array();
    $res['pageCount'] = $page_limit;
    $res['totalfiles'] = $total;
    $res['list'] = $result;  
      
     echo json_encode($res);
  

} 

else{

  $res = array();
  $res['msg'] = 'No Document Found';
  $res['error'] = 'true';

  echo json_encode($res);

}

 }




// $result = mysqli_fetch_all($run,MYSQLI_ASSOC);





if(isset($_POST['docid'])&&!empty($_POST['docid'])
  &&isset($_POST['userId'])&&!empty($_POST['userId'])

 
){
    

    $doc_list = array();
    $docid = $_POST['docid'];

    //echo $docid;
    //echo "SELECT * FROM tbl_document_master where doc_name ='$docid' order by dateposted desc";
     
    // die;

    //for the pdf doc
    $DocQry = "SELECT * FROM tbl_document_master where doc_id ='$docid' order by dateposted desc";
    $Doc = mysqli_query($con, $DocQry) or die('Error' . mysqli_error($con));

    while($pdf = mysqli_fetch_assoc($Doc)){
		
		$docPath = getDocumentPath($con, $pdf['doc_id'],$pdf['old_doc_name'],$pdf['doc_path'], $pdf['doc_extn'], $pdf['doc_name'], $_POST['userId']);
    	 $temp =array();
    	 $temp['old_doc_name'] = $pdf['old_doc_name'];
    	 $temp['doc_id'] = $pdf['doc_id'];
    	 $temp['doc_path'] = $docPath;
    	 $temp['doc_extn'] = $pdf['doc_extn'];
       $temp['msg'] = 'Document Found';
       $temp['error'] = 'false';

         array_push($doc_list,$temp);

     $docid = $pdf['doc_id'];

   //  echo  "SELECT * FROM tbl_document_master where doc_name  LIKE CONCAT('%', '$docid', '%') order by dateposted desc";

    // die;
         
    $getDocQry = "SELECT * FROM tbl_document_master where doc_name  LIKE CONCAT('%', '$docid', '%') order by dateposted desc";
    $getDoc = mysqli_query($con, $getDocQry) or die('Error' . mysqli_error($con));

    while($doclist = mysqli_fetch_assoc($getDoc)){

        $docPath = getDocumentPath($con, $doclist['doc_id'],$doclist['old_doc_name'],$doclist['doc_path'], $doclist['doc_extn'], $doclist['doc_name'], $_POST['userId']); 
       $t =array();
    	 $t['old_doc_name'] = $doclist['old_doc_name'];
    	 $t['doc_id'] = $doclist['doc_id'];
    	 $t['doc_path'] = $docPath;
    	 $t['doc_extn'] = $doclist['doc_extn'];
      


      array_push($doc_list,$t);

    
    }

    }
  
  echo json_encode($doc_list);  
 
}
else{

   $response = array();
  echo json_encode($response);

}

//sakshi.bhatia@ams-espl.com


?>