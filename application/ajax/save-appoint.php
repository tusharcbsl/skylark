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
   $aid=intval(base64_decode(urldecode($_POST['aid'])));

   //assign table name
   $tbl_name='appointments';
   
   //other required parameter
   $app_date=mysqli_real_escape_string($db_con, $_POST['app_date']);
   //$title=mysqli_real_escape_string($db_con, $_POST['title']);
   $title= xss_clean(trim($_POST['title']));
   $notify_time=mysqli_real_escape_string($db_con, $_POST['notify_time']);
   //$agenda=mysqli_real_escape_string($db_con, $_POST['agenda']);
   $agenda= xss_clean(trim($_POST['agenda']));
   $notify_frequency=mysqli_real_escape_string($db_con, $_POST['notify_frequency']);
   $task_notify_time=mysqli_real_escape_string($db_con, $_POST['task_notify_time']);
   $user_id=mysqli_real_escape_string($db_con,$_SESSION[cdes_user_id]);
   $app_date=date('Y-m-d', strtotime($app_date));
   //$contact=mysqli_real_escape_string($db_con, $_POST['contact']);
   $contact= xss_clean(trim($_POST['contact']));
   $contact_email=mysqli_real_escape_string($db_con, $_POST['contact_email']);
   //$app_notes=mysqli_real_escape_string($db_con, $_POST['notes']);
   $app_notes= xss_clean($_POST['notes']);
   $app_time=mysqli_real_escape_string($db_con, $_POST['app_time']);
   mysqli_set_charset($db_con,"utf8");    
   
   if($aid){
    $sql="update $tbl_name set 
                              user_id='$user_id',
                              title='$title',
                              contact='$contact',
                              contact_email='$contact_email',
                              agenda='$agenda',
                              notify_frequency='$notify_frequency',
                              notify_time='$notify_time',
                              app_date='$app_date',
                              app_time='$app_time',
                              app_notes='$app_notes'
                              where id='$aid'
                              ";
   
   }
   else{
    $sql="insert into $tbl_name set 
                              user_id='$user_id',
                              title='$title',
                              contact='$contact',
                              contact_email='$contact_email',
                              agenda='$agenda',
                              notify_frequency='$notify_frequency',
                              notify_time='$notify_time',
                              app_date='$app_date',
                              app_time='$app_time',
                              app_notes='$app_notes'
                              ";
   }
 //echo $sql;
// die;   
$query=mysqli_query($db_con,$sql);

if($query){
$status='success'; $msg=$lang['action_complete_success'];
}
else{
$status='error'; $msg=$lang['oops_something_wrong']; 
}
$res_array=array('status'=>$status,'msg'=>$msg);

echo json_encode($res_array);

   
   
