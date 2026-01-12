<?php
require_once 'connection.php';
if(isset($_POST['name']) && isset($_POST['pwd']) && !empty($_POST['name']) && !empty($_POST['pwd'])
   && isset($_POST['ip']) && !empty($_POST['ip']) 
  )
{
$name=$_POST['name'];
$pwd=$_POST['pwd'];
$remoteHost =$_POST['ip']; 
$qry= mysqli_query($con, "select * from tbl_user_master where user_email_id='$name' and password=sha1('$pwd')") or die('Error'. mysqli_error($con));
if(mysqli_num_rows($qry)>=1)
{
   
    $fetch= mysqli_fetch_assoc($qry);
     date_default_timezone_set("Asia/Kolkata");
     $date = date("Y-m-d H:i");
    $id=$fetch['user_id'];
    $update= mysqli_query($con,"update tbl_user_master set current_login_status='1',last_active_login='$date' where user_id='$id'");
	
	
    $result=array();
    $result["status"]='True';//"status"=>"true","Login_id"=>$id
    $result['userID']=$fetch['user_id'];
    $result['Email']=$fetch['user_email_id'];
    $result['FirstName']=$fetch['first_name'];
    $result['LastName']=$fetch['last_name'];
    $result['Phone']=$fetch['phone_no'];
	  $result['Designation']=$fetch['designation'];
	
	 $userid = $fetch['user_id'];
	 $username =$fetch['first_name']." ".$fetch['last_name'];
    

    
    $log = mysqli_query($con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$userid','$username',null,null,'Login/Logout','$date',null,'$remoteHost','')") or die('error : ' . mysqli_error($con));

    if($log){
        
    $json= json_encode($result);
    echo $json;
    
    }
    
	
}
 else 
     {
     
    $result=array("status"=>"False","Login_id"=>"0");
    $json= json_encode($result);
    echo $json;
 }
}
else 
     {
   
    $result=array("status"=>"False","Login_id"=>"0");
    $json= json_encode($result);
    echo $json;
 }





?>
