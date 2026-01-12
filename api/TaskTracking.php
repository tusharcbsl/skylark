<?php

require_once 'connection.php';

if(isset($_POST['ticketid'])&&!empty($_POST['ticketid'])
){



$ticketid =$_POST['ticketid'];


$tasklist = mysqli_query($con, "SELECT * FROM tbl_doc_assigned_wf where ticket_id ='$ticketid'") or die('Error:' . mysqli_error($con));

$response = array();

   /*"id": "43",
        "task_id": "7",
        "doc_id": "5795",
        "start_date": "2018-02-02 16:31:09",
        "end_date": "2018-02-03 16:31:09",
        "task_deadline": null,
        "task_status": "Approved",
        "task_remarks": "",
        "assign_by": "1",
        "action_by": "21",
        "action_time": "2018-02-02 17:26:31",
        "NextTask": "1",
        "ticket_id": "MRF_1_1517569269"*/


 while($list = mysqli_fetch_assoc($tasklist)){

    $actionById = $list['action_by'];
    


 	$actionByName= mysqli_query($con, "select * from tbl_user_master where user_id = '$actionById'") or die('Error'. mysqli_error($con));

 	//echo "select * from tbl_user_master where user_id = '$actionById'";
 	$fetchname = mysqli_fetch_assoc($actionByName);
 	$firstname = $fetchname['first_name'];
	$lastname = $fetchname['last_name'];
 	$profilepic =base64_encode($fetchname['profile_picture']);

 	$taskid = $list['task_id'];
 	$taskidOrder= mysqli_query($con, "SELECT * FROM tbl_task_master where task_id = '$taskid'") or die('Error'. mysqli_error($con));
 	$fetchOrder = mysqli_fetch_assoc($taskidOrder);

    $ticketid = $list['ticket_id'];


//SELECT * FROM tbl_task_comment where tickt_id = 'LFSJ_54_1523600329'and user_id ='17';
    //echo $taskid."\n";
   // echo "SELECT * FROM tbl_task_comment where tickt_id ='$ticketid' and user_id ='$actionById' and task_id = '$taskid'"."\n";
 	$comment = mysqli_query($con, "SELECT * FROM tbl_task_comment where tickt_id ='$ticketid' and user_id ='$actionById' and task_id = '$taskid'") or die('Error'. mysqli_error($con));
 	$fetchComment = mysqli_fetch_assoc($comment);

   $temp = array();
   $temp['task_id'] = $list['task_id'];
   $temp['task_name'] = $fetchOrder['task_name'];
  
   $temp['ticket_id'] = $list['ticket_id'];
   $temp['action_time'] = $list['action_time'];
   $temp['task_status']=$list['task_status'];

   $taskstatus = $list['task_status'];

   //print_r($taskstatus);

   //die;

   if($taskstatus=="Pending"){
    
     $taskid = $list['task_id'];

     //echo $taskid;
     //die; 165

     //SELECT * FROM tbl_task_master where task_id ='165'; 
      $qry = mysqli_query($con, "SELECT * FROM tbl_task_master where task_id ='$taskid'") or die('Error'. mysqli_error($con));
      $fetchid = mysqli_fetch_assoc($qry);
     
      //print_r($fetchid['assign_user']);
     // die;

   // $temp['action_by'] = $fetchid['assign_user'];
    $actionById = $fetchid['assign_user'];

  $actionByName= mysqli_query($con, "select * from tbl_user_master where user_id = '$actionById'") or die('Error'. mysqli_error($con));

  //echo "select * from tbl_user_master where user_id = '$actionById'";
  $fetchname = mysqli_fetch_assoc($actionByName);
  $firstname = $fetchname['first_name'];
  $lastname = $fetchname['last_name'];
     

    $temp['action_by_name'] = $firstname." ".$lastname;

   }

   else{

   $temp['action_by'] = $list['action_by'];
   $temp['action_by_name'] = $firstname." ".$lastname;

   }

  
   $temp['task_order'] = $fetchOrder['task_order'];
   $temp['comment'] = $fetchComment['comment'];
   $temp['profile_pic'] = $profilepic;

   array_push($response, $temp);
 }


// $fetchlist = mysqli_fetch_all($tasklist,MYSQLI_ASSOC);

 echo json_encode($response);

}



?>