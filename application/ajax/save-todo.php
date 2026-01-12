<?php
require_once '../../sessionstart.php';
//require_once '../../loginvalidate.php';
if(!isset($_SESSION['cdes_user_id'])){
    header("location:../../logout");
}
if (isset($_SESSION['lang'])) {
    $file = "../../" . $_SESSION['lang'] . ".json";
} else {
    $file = "../../English.json";
}
$data = file_get_contents($file);
$lang = json_decode($data, true);
require './../config/database.php';
     //for user role
$ses_val = $_SESSION; 
$langDetail = mysqli_query($db_con, "select * from tbl_language where lang_name='$_SESSION[lang]'") or die('Error : ' . mysqli_error($db_con));
$langDetail = mysqli_fetch_assoc($langDetail);	

$chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$_SESSION[cdes_user_id]', user_ids) > 0") or die('Error:'. mysqli_error($db_con));
   
   $rwgetRole = mysqli_fetch_assoc($chekUsr);
  
  // echo $rwgetRole['dashboard_mydms']; die;
   /*if($rwgetRole['modify_group'] != '1'){
   header('Location: ../../index');
   }*/
   //todo id if given
   $tdid=intval(base64_decode(urldecode($_POST['tdid'])));

   //assign table name
   $tbl_name='todo_list';
   
   //other required parameter
   $emp_id= xss_clean($_POST['emp_id']);
   $task_date=mysqli_real_escape_string($db_con, $_POST['task_date']);
   
   $task_name = xss_clean($_POST['task_name']);
   $task_name=mysqli_real_escape_string($db_con, $task_name);
   
   $task_time=mysqli_real_escape_string($db_con, $_POST['task_time']);
   
   $task_description = xss_clean($_POST['task_description']);
   $task_description=mysqli_real_escape_string($db_con, $task_description);
   
   $task_notification_frequency=mysqli_real_escape_string($db_con, $_POST['task_notification_frequency']);
   $task_notify_time=mysqli_real_escape_string($db_con, $_POST['task_notify_time']);
   $emp_id=mysqli_real_escape_string($db_con,implode(',',$emp_id));
   $task_date=date('Y-m-d', strtotime($task_date));
   
   if($tdid){
    $sql="update $tbl_name set 
                              emp_id='$emp_id',
                              task_name='$task_name',
                              task_description='$task_description',
                              task_date='$task_date',
                              task_time='$task_time',
                              task_notification_frequency='$task_notification_frequency',
                              task_notify_time='$task_notify_time'
                              where id='$tdid'
                              ";
   
   }
   else{
    $sql="insert into $tbl_name set 
                              emp_id='$emp_id',
                              task_name='$task_name',
                              task_description='$task_description',
                              task_date='$task_date',
                              task_time='$task_time',
                              task_notification_frequency='$task_notification_frequency',
                              task_notify_time='$task_notify_time'
                              ";
   }
 //echo $sql;
 //die;   
 mysqli_set_charset($db_con,"utf8");

$query=mysqli_query($db_con,$sql);
if($query){
$status='success'; $msg=$lang['action_complete_success'];
}
else{
$status='error'; $msg=$lang['oops_something_wrong']; 
}
$res_array=array('status'=>$status,'msg'=>$msg);
echo json_encode($res_array);

   
   
