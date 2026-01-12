<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
//this files start session and having db credentials
//require_once './sessionstart.php';
////This file contains the database access information. It will included on every file requiring database access.
//require_once './application/config/database.php';
////this files contains mails
//require_once './mail.php';
//error_reporting(0);
// move task from user to alternate
$tasks = mysqli_query($db_con, "select * from tbl_doc_assigned_wf tdwf inner join tbl_task_master tm on tm.task_id=tdwf.task_id where tdwf.task_status='Pending' and NextTask='0'") or die('Error' . mysqli_error($db_con));
while ($rwTasks = mysqli_fetch_assoc($tasks)) {
//   $workinghrs= explode("To", $rwTasks['working_hour']);
//    $time1 = strtotime($workinghrs[0]);
//    $time2 = strtotime($workinghrs[1]);
//    $difference = round(abs($time2 - $time1) / 3600,2);
    $startdate = $rwTasks['start_date'];
    $deadLine = $rwTasks['deadline'];
    if ($rwTasks['deadline_type'] == 'Date') {
        $endDate = strtotime($startdate) + ($deadLine * 60);
    } else if ($rwTasks['deadline_type'] == 'Days') {
        $endDate = strtotime($startdate) + ($deadLine * 24 * 60 * 60);
    } else {
        $endDate = strtotime($startdate) + ($deadLine * 60);
    }
    $currentDate = strtotime($date);
    //echo date('d-m-Y H:i:s',$currentDate+900).'=';
    //echo date('d-m-Y H:i:s',$endDate).'<br>';
    if ($currentDate>= $endDate) {
        if (!empty($rwTasks['alternate_user']) && $rwTasks['alternate_user'] != 'Select Aletrnate User') {
            $update = mysqli_query($db_con, "update tbl_doc_assigned_wf set NextTask='3', start_date='$date' where id='$rwTasks[id]'") or die('Error' . mysqli_error($db_con));
            //$update=true;
            if ($update) {
                assignTaskAlternate($rwTasks['id'], $db_con, $projectName);
            }
        }else if (empty($rwTasks['alternate_user']) || $rwTasks['alternate_user'] == 'Select Aletrnate User'){ 
            $update = mysqli_query($db_con, "update tbl_doc_assigned_wf set NextTask='4', start_date='$date' where id='$rwTasks[id]'") or die('Error' . mysqli_error($db_con));
            if ($update) {
                assignTaskAlternate($rwTasks['id'], $db_con, $projectName);
            }
        }
    }
}
// move task from alternate to superuser
$tasks = mysqli_query($db_con, "select * from tbl_doc_assigned_wf tdwf inner join tbl_task_master tm on tm.task_id=tdwf.task_id where tdwf.task_status='Pending' and NextTask='3'") or die('Error' . mysqli_error($db_con));
//echo"select * from tbl_doc_assigned_wf tdwf inner join tbl_task_master tm on tm.task_id=tdwf.task_id where tdwf.task_status='Pending' and NextTask=0";
while ($rwTasks = mysqli_fetch_assoc($tasks)) {
    $startdate = $rwTasks['start_date'];
    $deadLine = $rwTasks['deadline'];
    if ($rwTasks['deadline_type'] == 'Date') {
        $endDate = strtotime($startdate) + ($deadLine*60);
    } else if ($rwTasks['deadline_type'] == 'Days') {
        $endDate = strtotime($startdate) + ($deadLine * 24 * 60 * 60);
    } else {
        $endDate = strtotime($startdate) + ($deadLine * 60);
    }
    $currentDate = strtotime($date);
    //echo date('Y-m-d h:i:s', $endDate).' '.$rwTasks['id'].'<br>';
    if ($currentDate > $endDate) {
        if (empty($rwTasks['alternate_user']) || $rwTasks['alternate_user'] == 'Select Aletrnate User'){
            $update = mysqli_query($db_con, "update tbl_doc_assigned_wf set NextTask='4', start_date='$date' where id='$rwTasks[id]'") or die('Error' . mysqli_error($db_con));
            if ($update) {
                assignTaskSupervisor($rwTasks['id'], $db_con, $projectName);
            }
        }else{
            $update = mysqli_query($db_con, "update tbl_doc_assigned_wf set NextTask='4', start_date='$date' where id='$rwTasks[id]'") or die('Error' . mysqli_error($db_con));
            if ($update) {
                assignTaskSupervisor($rwTasks['id'],$db_con);
            }  
        }
    }
}
   
?>
