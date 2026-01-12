<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once './application/config/database.php';

  //dms_kundu_1553234480
// Check for Backuptime;
$time_grace=0;
$sql="select * from tbl_db_backup_policy where backup_type='Scheduled' or backup_type='Incremental' limit 0,2";
$query= mysqli_query($db_con, $sql);
if(mysqli_num_rows($query)>0){
    while($res=mysqli_fetch_assoc($query)){
        if($res['backup_type']=='Scheduled'){
            getScheduledBackup($res);
        }
        if($res['backup_type']=='Incremental'){
           // getIncBackup($res);
            $resp=validateBackupTime($res);
if($resp){           
 // For Incremental
$flush_query = mysqli_query($db_con, "flush logs");
if ($flush_query) {
    $status = 'success';
    $new_log_details = getActiveLog($db_con);
    $log_json = json_encode(array('file' => $new_log_details['new_bin_filename'], 'position' => $new_log_details['new_bin_file_position']));
    $action = "Binary Log flushed. And a new bin log file $new_log_details[new_bin_filename] created Successfully.";
    $backup_name = $new_log_details[new_bin_filename];
} else {
    $status = 'Failure';
    $action = "Error in Binary Log flushing and new file creation.";
    $log_json = '';
}
dbBkpLog($db_con, $action, $status, $log_json, $backup_name);

function dbBkpLog($db_con, $action, $status, $log_json, $backup_name) {
    $sql = "insert into tbl_db_backup_log set
                                         backup_name='$backup_name',
                                         action='$action',
                                         backup_type='Incremental',
                                         log_json='$log_json',
                                         status='$status'
                                         ";
    $query = mysqli_query($db_con, $sql);

    $sqlin = "update tbl_db_backup_policy set last_full_backup ='$backup_name' where backup_type='Incremental'";
    $queryin = mysqli_query($db_con, $sqlin);
}

// To get name and position of new created file.
function getActiveLog($db_con) {
    // Define response array
    $arr = array();
    $res = mysqli_fetch_assoc(mysqli_query($db_con, "show MASTER STATUS"));
    if ($res) {
        $new_bin_filename = $res['File'];
        $new_bin_file_postion = $res['Position'];
    }
    $arr = array('new_bin_filename' => $new_bin_filename, 'new_bin_file_position' => $new_bin_file_postion);
    return $arr;
} 

                        
       }     
        }
    }
}

function validateBackupTime($res){
    global $time_grace;
    $day_array=array('Sunday'=>'SUN','Monday'=>'MON','Tuesday'=>'TUE','Wednesday'=>'WED','Thursday'=>'THU','Friday'=>'FRI','Saturday'=>'SAT');
    $current_dayname=date('l'); 
    $current_day=date('d');
    $current_time=date('H:i:00');
    $bktime = $res['backup_time'];
    // Backup time component
    $bktime_component = explode(':',$res['backup_time']);
    $bktime_minute=$bktime_component[1];
    $max_minute=(($bktime_minute + $time_grace)<10 ? '0'.($bktime_minute + $time_grace):$bktime_minute + $time_grace);
    if($max_minute>=60){
        $max_minute=(($max_minute-60)<10 ? '0'.($max_minute-60):$max_minute-60);
        $chk_maxhour=(($bktime_component[0]+1)<10 ? '0'.($bktime_component[0]+1):$bktime_component[0]+1);
        $max_bktime = $chk_maxhour.':'.$max_minute.':'.$bktime_component[2];
    } else {
        $max_bktime = $bktime_component[0].':'.$max_minute.':'.$bktime_component[2];
    }
    
    $min_minute=(($bktime_minute - $time_grace)<10 ? '0'.($bktime_minute - $time_grace):$bktime_minute - $time_grace);
    if($min_minute<0){
      $min_minute=((60+$min_minute)<10 ? '0'.(60+$min_minute):60+$min_minute);
      $chk_minhour=(($bktime_component[0]-1)<10 ? '0'.($bktime_component[0]-1):$bktime_component[0]-1);
      $min_bktime = $chk_minhour.':'.$min_minute.':'.$bktime_component[2];
    } else {
        $min_bktime = $bktime_component[0].':'.$min_minute.':'.$bktime_component[2];
    }
    
    $date=date('Y-m-d');
    $hour=date('H');
    $minute=date('i');
    //echo $minute;
    //$max_minute=(($minute + $time_grace)<10 ? '0'.($minute + $time_grace):$minute + $time_grace);
    //$min_minute= (abs($minute - 2)<10 ? '0'.abs($minute - 2):abs($minute - 2));
    //echo $min_bktime."<br>".$max_bktime;die;
    //echo $max_minute; die;
   // $bktime=$hour.':'.$min_minute.':00';
   // $min_bktime=$hour.':'.$min_minute.':00';
  //  $max_bktime=$hour.':'.$max_minute.':00';
    //print_r($res); die;
    echo $current_time."=>".$min_bktime.'=>'.$max_bktime; 
    switch ($res['backup_frequency']){  
        case "daily":
           if($current_time==$bktime ||($current_time>$min_bktime && $current_time<$max_bktime)){
               return TRUE; 
           } else {
               return FALSE;  
           }
           break;
       case "once":
           echo $res['backup_time'];
           if($res['backup_date']==$date && ($current_time==$bktime ||($current_time>$min_bktime && $current_time < $max_bktime))){
               return TRUE; 
           } else {
               return FALSE;  
           }
           break;
      case "weekly":
           if($res['backup_day']==$day_array[$current_day] && ($current_time==$bktime ||($current_time>$min_bktime && $current_time < $max_bktime))){
               return TRUE; 
           } else {
               return FALSE;  
           }
           break;    
      case "monthly":
           if($res['backup_day']==$current_day && ($current_time==$bktime ||($current_time>$min_bktime && $current_time < $max_bktime))){
               return TRUE; 
           } else {
               return FALSE;  
           }
           break;     
    }
    
}

// Scheduled Backup
function getScheduledBackup($res){
  $msg='';
  global $dbHost,$db_con,$dbUser,$dbPwd,$dbName;  
  $resp=validateBackupTime($res);
  //var_dump($resp); die;
  $date = date("Ymdhis");  
  $backup_name = $dbName.$date.".sql";
  $filepath = "db_file/" . $backup_name;
  //echo "mysqldump -h $dbHost --user=$dbUser --password=$dbPwd $dbName >$filepath"; 
  exec("mysqldump -h $dbHost --user=$dbUser --password=$dbPwd $dbName >$filepath");
  //exec("mysqldump -u $dbUser -p$dbPwd $dbName >$filepath");
  if (file_exists($filepath)) {
    $status = "Success";
    $action = "Backup <strong>" . $backup_name . "</strong> created Successfully.";
    $log_json = json_encode(array('backupname' => $backup_name, 'time' => $date, 'status' => $status));
    } else {
        $status = "Failure";
        $action = "Backup Creation Failed.";
        $log_json = json_encode(array('backupname' => $backup_name, 'time' => $date, 'status' => $status));
    }
   $sql = "insert into tbl_db_backup_log set backup_name='$backup_name', action='$action',log_json='$log_json', backup_type='Scheduled',status='$status'";
   $query = mysqli_query($db_con, $sql); 
   if(!$query){
       $msg="Log Insertion Failed";
   }   
   $sqlin = "update tbl_db_backup_policy set last_full_backup ='$backup_name' where backup_type='Scheduled'";
   $queryin = mysqli_query($db_con, $sqlin);
   if(!$queryin){
       $msg.=", Backup Policy last full backup updation failed.";
   }
   if(!empty($msg)){
      $file=fopen("db_file/dbbackuplog.txt","a+");
      $msg = date('d-M-Y H:i:s').' '.$msg;
      fwrite($file,$msg);
      fclose($file);
   }
  // Download the backup to client.  
    if (file_exists($filepath)) {
     header('Content-Description: File Transfer');
     header('Content-Type: application/octet-stream');
     header('Content-Disposition: attachment; filename="' . basename($filepath) . '"');
     header('Expires: 0');
     header('Cache-Control: must-revalidate');
     header('Pragma: public');
     header('Content-Length: ' . filesize($filepath));
     flush(); // Flush system output buffer
     readfile($filepath);
     //header('location:backup');
     exit;
   }  
}
exit(0);

// For Incremental
$flush_query = mysqli_query($db_con, "flush logs");
if ($flush_query) {
    $status = 'success';
    $new_log_details = getActiveLog($db_con);
    $log_json = json_encode(array('file' => $new_log_details['new_bin_filename'], 'position' => $new_log_details['new_bin_file_position']));
    $action = "Binary Log flushed. And a new bin log file $new_log_details[new_bin_filename] created Successfully.";
    $backup_name = $new_log_details[new_bin_filename];
} else {
    $status = 'Failure';
    $action = "Error in Binary Log flushing and new file creation.";
    $log_json = '';
}
dbBkpLog($db_con, $action, $status, $log_json, $backup_name);

function dbBkpLog($db_con, $action, $status, $log_json, $backup_name) {
    $sql = "insert into tbl_db_backup_log set
                                         backup_name='$backup_name',
                                         action='$action',
                                         backup_type='Incremental',
                                         log_json='$log_json',
                                         status='$status'
                                         ";
    $query = mysqli_query($db_con, $sql);

    $sqlin = "update tbl_db_backup_policy set last_full_backup ='$backup_name' where backup_type='Incremental'";
    $queryin = mysqli_query($db_con, $sqlin);
}

// To get name and position of new created file.
function getActiveLog($db_con) {
    // Define response array
    $arr = array();
    $res = mysqli_fetch_assoc(mysqli_query($db_con, "show MASTER STATUS"));
    if ($res) {
        $new_bin_filename = $res['File'];
        $new_bin_file_postion = $res['Position'];
    }
    $arr = array('new_bin_filename' => $new_bin_filename, 'new_bin_file_position' => $new_bin_file_postion);
    return $arr;
}



?>