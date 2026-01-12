<?php

require_once 'connection.php';

//get Location

if(isset($_POST['userid'])&&!empty($_POST['userid'])){

	$userid = $_POST['userid'];
 
    $chekUsr = mysqli_query($con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$userid', user_ids) > 0") or die('Error:' . mysqli_error($con));
    $rwgetRole = mysqli_fetch_assoc($chekUsr);
    $sameGroupIDs = array();
    $group = mysqli_query($con, "select * from tbl_bridge_grp_to_um where find_in_set('$userid',user_ids)") or die('Error' . mysqli_error($con));
    while ($rwGroup = mysqli_fetch_assoc($group)) {
        $sameGroupIDs[] = $rwGroup['user_ids'];
    }
    $sameGroupIDs = implode(',', $sameGroupIDs);
    $sameGroupIDs = explode(',', $sameGroupIDs);
    $sameGroupIDs = array_unique($sameGroupIDs);
    sort($sameGroupIDs);
    $sameGroupIDs = implode(',', $sameGroupIDs);
   // print_r($sameGroupIDs);

    if ($rwgetRole['mark_attendance'] != '1') {

        //header('Location: ./index');


    }

    $response = array();


   $group_user = mysqli_query($con, "SELECT group_id,user_ids FROM `tbl_bridge_grp_to_um`where find_in_set($userid,user_ids)");
   while ($allGroups = mysqli_fetch_array($group_user)) 

   {
   $user_idsgrp = explode(',', $allGroups['user_ids']);
   
  /* print_r($user_idsgrp);*/
  // print_r($allGroups['group_id']);


  /* die;*/

   if (in_array($userid , $user_idsgrp)) {
    $grp = mysqli_query($con, "select group_id,group_name from tbl_group_master WHERE group_id='$allGroups[group_id]' order by group_name asc") or die('Error' . mysqli_error($con));
    while ($rwGrp = mysqli_fetch_assoc($grp)) {


   $temp = array();
   $temp['locGrpId'] =  $rwGrp['group_id'];
   $temp['locGrpName'] =  $rwGrp['group_name'];
   array_push($response,$temp);  
    
    } 
}

}

echo json_encode($response);

}

//get project 

if(isset($_POST['groupId'])&&!empty($_POST['groupId'])&& isset($_POST['userId'])&&!empty($_POST['userId'])){

 $userid = $_POST['userId'];
 $groupid = $_POST['groupId'];
 $response = array();


 //getting assigned project
     $usql = "select project,modify_atndnce from tbl_user_master where user_id=".$userid."";
    $userdetail = mysqli_query($con,$usql);
     $rowU = mysqli_fetch_assoc($userdetail);
    $assigned_project = $rowU['project'];

 if($userid==1){


 $PjctsNameL = mysqli_query($con, "SELECT * FROM tbl_bridge_project_loc where loc_id='$groupid'");

 while ($row = mysqli_fetch_assoc($PjctsNameL)) {
                                                            
 $location = mysqli_query($con, "SELECT * FROM tbl_project_master where project_id='$row[project_id]'");
                                                            
 while ($loc = mysqli_fetch_assoc($location)) {
  
  //code here 
  $temp = array();
  $temp['project_id']=$loc['project_id'];
  $temp['project_name']=$loc['project_name'];

  array_push($response, $temp);


 }

 }


}

else{
                                                    
$sql ="SELECT pl.project_id, pm.project_name FROM tbl_bridge_project_loc as pl left join tbl_project_master as pm on pl.project_id=pm.project_id where pl.loc_id ='$groupid' and pl.project_id IN($assigned_project) group by pl.project_id";
                                         
   $location = mysqli_query($con, $sql);                                                           
 while ($loc = mysqli_fetch_assoc($location)) {
  

  //code here

  $temp = array();
  $temp['project_id']=$loc['project_id'];
  $temp['project_name']=$loc['project_name'];

  array_push($response, $temp);


}

 }

 echo json_encode($response);

}

//get Shift 

if(isset($_POST['uidShift'])&&!empty($_POST['uidShift'])){

  $userid = $_POST['uidShift'];
  $shiftQry = mysqli_query($con,"SELECT * FROM tbl_shift_master");

 // Fetch all
 $rows = mysqli_fetch_all($shiftQry,MYSQLI_ASSOC);
 
 echo json_encode($rows);
  

}

//get project 

if(isset($_POST['groupId'])&&!empty($_POST['groupId'])&& isset($_POST['userId'])&&!empty($_POST['userId'])){

 $userid = $_POST['userId'];
 $groupid = $_POST['groupId'];
 $response = array();


 //getting assigned project
     $usql = "select project,modify_atndnce from tbl_user_master where user_id=".$userid."";
    $userdetail = mysqli_query($con,$usql);
     $rowU = mysqli_fetch_assoc($userdetail);
    $assigned_project = $rowU['project'];

 if($userid==1){


 $PjctsNameL = mysqli_query($con, "SELECT * FROM tbl_bridge_project_loc where loc_id='$groupid'");

 while ($row = mysqli_fetch_assoc($PjctsNameL)) {
                                                            
 $location = mysqli_query($con, "SELECT * FROM tbl_project_master where project_id='$row[project_id]'");
                                                            
 while ($loc = mysqli_fetch_assoc($location)) {
  
  //code here 
  $temp = array();
  $temp['project_id']=$loc['project_id'];
  $temp['project_name']=$loc['project_name'];

  array_push($response, $temp);


 }

 }


}

else{
                                                    
$sql ="SELECT pl.project_id, pm.project_name FROM tbl_bridge_project_loc as pl left join tbl_project_master as pm on pl.project_id=pm.project_id where pl.loc_id ='$groupid' and pl.project_id IN($assigned_project) group by pl.project_id";
                                         
   $location = mysqli_query($con, $sql);                                                           
 while ($loc = mysqli_fetch_assoc($location)) {
  

  //code here

  $temp = array();
  $temp['project_id']=$loc['project_id'];
  $temp['project_name']=$loc['project_name'];

  array_push($response, $temp);


}

 }

 echo json_encode($response);

}


//mark all present 

if (isset($_POST['groupIdMark'])&&!empty($_POST['groupIdMark'])
  && isset($_POST['userIdMark'])&&!empty($_POST['userIdMark'])
  && isset($_POST['userNameMark'])&&!empty($_POST['userNameMark']) 
  && isset($_POST['prjctIdMark'])&&!empty($_POST['prjctIdMark'])
  && isset($_POST['shiftIdMark'])&&!empty($_POST['shiftIdMark'])
  && isset($_POST['ipMark'])&&!empty($_POST['ipMark'])      
  && isset($_POST['dateMark'])&&!empty($_POST['dateMark'])) {


    $dt = $_POST['dateMark'];
    $userid = $_POST['userIdMark'];
    $username = $_POST['userNameMark'];
     date_default_timezone_set("Asia/Kolkata");
     $date = date("Y-m-d H:i");
     $host = $_POST['ipMark'];

     $markAttnce=null;


    $checkSndy = strtotime($dt);
    $ConverDate = date("l", $checkSndy);
    $DateTomatchSndy = strtolower($ConverDate);
    //$gfids;

    $locationId =$_POST['groupIdMark'];
    $project =$_POST['prjctIdMark'];
    $shiftId = $_POST['shiftIdMark'];

   /* echo $shiftId;
    die;*/


    $chekUsr = mysqli_query($con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$userid', user_ids) > 0") or die('Error 1:' . mysqli_error($con));
    $rwgetRole = mysqli_fetch_assoc($chekUsr);
    $sameGroupIDs = array();
    $group = mysqli_query($con, "select * from tbl_bridge_grp_to_um where find_in_set('$userid',user_ids)") or die('Error' . mysqli_error($con));
    while ($rwGroup = mysqli_fetch_assoc($group)) {
        $sameGroupIDs[] = $rwGroup['user_ids'];
    }
    $sameGroupIDs = implode(',', $sameGroupIDs);
    $sameGroupIDs = explode(',', $sameGroupIDs);
    $sameGroupIDs = array_unique($sameGroupIDs);
    sort($sameGroupIDs);
    $sameGroupIDs = implode(',', $sameGroupIDs);
  

    $slgroup = mysqli_real_escape_string($con, $locationId);
    $grpUserIds = array();
    if (!empty($slgroup)) {

        $matchSamegroupids = explode(',', $sameGroupIDs);
        //echo "select user_ids,group_id from tbl_bridge_grp_to_um WHERE group_id='$slgroup'";
        $getUserID = mysqli_query($con, "select user_ids,group_id from tbl_bridge_grp_to_um WHERE group_id='$slgroup'") or die("Error  2" . mysqli_error($con));
        $RwgetUserID = mysqli_fetch_assoc($getUserID);
        $grpUserIds = explode(',', $RwgetUserID['user_ids']);
        //print_r($grpUserIds);
        ////print_r($matchSamegroupids);
        $grpUserIds = array_intersect($matchSamegroupids, $grpUserIds);
        
        //print_r($grpUserIds);
        //$grpmyidss = implode(",", $grpUserIds);
        //echo $grpmyids;
    }

     //$grpUserIds = implode(',', $grpUserIds);



    $EventGet = mysqli_query($con, "SELECT * FROM tbl_bridge_grp_to_event where FIND_IN_SET('$locationId', location_id) and FIND_IN_SET('$project', project_id)");
    while ($rwEventGet = mysqli_fetch_assoc($EventGet)) {
        $getEventId = $rwEventGet['event_ids'];
    }
/*
   echo  "select date from tbl_events_master where id in($getEventId) and date='$dt'";
   die;
*/
    $checkHldy = mysqli_query($con, "select date from tbl_events_master where id in($getEventId) and date='$dt'");
    $rwcheckHldy = mysqli_fetch_assoc($checkHldy);
    $hdays = $rwcheckHldy['date'];
    $grpUserIds = array_unique($grpUserIds);
    $grpUserIds = implode(',', $grpUserIds);


    $refNo = mysqli_query($con, "select emp_id,user_id,project,doj,userAdmin,location, primary_project,shift_id,tea_amount,convence_amount from tbl_user_master where user_id in($grpUserIds) and FIND_IN_SET($project, project) and shift_id ='$shiftId' and user_id!='1' and user_id!='2' and usr_acvt_dacvt='1'") or die("ErrorDFGGFGD" . mysqli_error($con));
    while ($rwrefNo = mysqli_fetch_assoc($refNo)) {

        //echo $rwrefNo['user_id'].'<br>';

        $prjctID = $_POST['prjctIdMark'];
        $checkusr = mysqli_query($con, "select * from tbl_attendance_master where user_id='$rwrefNo[user_id]' and mark_date='$dt' and project_id='$prjctID' and user_id!='1' and user_id!='2'");
        if (mysqli_num_rows($checkusr) > 0) {
            $markAttnce = 1;
        } else {
            if ($rwrefNo['userAdmin'] == 1) { //for admin user
                if ($locationId == $rwrefNo['location'] && $project == $rwrefNo['primary_project']) {
                    $doj = strtotime($rwrefNo['doj']);
                    $datett = strtotime($dt); //die;
                    if ($doj <= $datett) {
                        if ($DateTomatchSndy == "sunday") {
                            $markAttnce = mysqli_query($con, "insert into tbl_attendance_master(`ref_no`, `marked_by`, `mark_date`, `attendance_status`,`user_id`, `project_id`,`loc_id`,`shift_id`,`tea_amount`,`convence_amount`) values ('$rwrefNo[emp_id]', '$userid', '$dt','S','$rwrefNo[user_id]','$rwrefNo[primary_project]','$locationId','$rwrefNo[shift_id]','$rwrefNo[tea_amount]','$rwrefNo[convence_amount]')") or die('error 3 : ' . mysqli_error($con));
                        } elseif ($dt == $hdays) {
                            $markAttnce = mysqli_query($con, "insert into tbl_attendance_master(`ref_no`, `marked_by`, `mark_date`, `attendance_status`,`user_id`, `project_id`,`loc_id`,`shift_id`,`tea_amount`,`convence_amount`) values ('$rwrefNo[emp_id]', '$userid', '$dt','H','$rwrefNo[user_id]','$rwrefNo[primary_project]','$locationId','$rwrefNo[shift_id]','$rwrefNo[tea_amount]','$rwrefNo[convence_amount]')") or die('error 4 : ' . mysqli_error($con));
                        } else {
                            $markAttnce = mysqli_query($con, "insert into tbl_attendance_master(`ref_no`, `marked_by`, `mark_date`, `attendance_status`,`user_id`, `project_id`,`loc_id`,`shift_id`,`tea_amount`,`convence_amount`) values ('$rwrefNo[emp_id]', '$userid', '$dt','P','$rwrefNo[user_id]','$rwrefNo[primary_project]','$locationId','$rwrefNo[shift_id]','$rwrefNo[tea_amount]','$rwrefNo[convence_amount]')") or die('Error  5: ' . mysqli_error($con));
                        }
                    } else {
                        $markAttnce = mysqli_query($con, "insert into tbl_attendance_master(`ref_no`, `marked_by`, `mark_date`, `attendance_status`,`user_id`, `project_id`,`loc_id`,`shift_id`,`tea_amount`,`convence_amount`) values ('$rwrefNo[emp_id]', '$userid', '$dt','A','$rwrefNo[user_id]','$rwrefNo[primary_project]','$locationId','$rwrefNo[shift_id]','$rwrefNo[tea_amount]','$rwrefNo[convence_amount]')") or die('errors 6 : ' . mysqli_error($con));
                    }
                } else {
                    $markAttnce = 1;
                }
            } else {
                $projectIds = explode(',', $rwrefNo['project']);
                if (count($projectIds) > 1) {
                    //$markAttnce=1;
                } else {

                    $doj = strtotime($rwrefNo['doj']);
                    $datett = strtotime($dt); //die;
                    if ($doj <= $datett) {
                        if ($DateTomatchSndy == "sunday") {
                            $markAttnce = mysqli_query($con, "insert into tbl_attendance_master(`ref_no`, `marked_by`, `mark_date`, `attendance_status`,`user_id`, `project_id`,`loc_id`,`shift_id`,`tea_amount`,`convence_amount`) values ('$rwrefNo[emp_id]', '$userid', '$dt','S','$rwrefNo[user_id]','$rwrefNo[project]','$locationId','$rwrefNo[shift_id]','$rwrefNo[tea_amount]','$rwrefNo[convence_amount]')") or die('error 7: ' . mysqli_error($con));
                        } elseif ($dt == $hdays) {
                            $markAttnce = mysqli_query($con, "insert into tbl_attendance_master(`ref_no`, `marked_by`, `mark_date`, `attendance_status`,`user_id`, `project_id`,`loc_id`,`shift_id`,`tea_amount`,`convence_amount`) values ('$rwrefNo[emp_id]', '$userid', '$dt','H','$rwrefNo[user_id]','$rwrefNo[project]','$locationId','$rwrefNo[shift_id]'),'$rwrefNo[tea_amount]','$rwrefNo[convence_amount]'") or die('error 8: ' . mysqli_error($con));
                        } else {
                            
                            $markAttnce = mysqli_query($con, "insert into tbl_attendance_master(`ref_no`, `marked_by`, `mark_date`, `attendance_status`,`user_id`, `project_id`,`loc_id`,`shift_id`,`tea_amount`,`convence_amount`) values ('$rwrefNo[emp_id]', '$userid', '$dt','P','$rwrefNo[user_id]','$rwrefNo[project]','$locationId','$rwrefNo[shift_id]','$rwrefNo[tea_amount]','$rwrefNo[convence_amount]')") or die('error 9 : DD ' . mysqli_error($con));
                        }
                    } else {
                        
                        $markAttnce = mysqli_query($con, "insert into tbl_attendance_master(`ref_no`, `marked_by`, `mark_date`, `attendance_status`,`user_id`, `project_id`,`loc_id`,`shift_id`,`tea_amount`,`convence_amount`) values ('$rwrefNo[emp_id]', '$userid', '$dt','A','$rwrefNo[user_id]','$rwrefNo[project]','$locationId','$rwrefNo[shift_id]','$rwrefNo[tea_amount]','$rwrefNo[convence_amount]')") or die('error  10: KK' . mysqli_error($con));
                    }
                }
            }
        }
    }
    if ($markAttnce) {

        $loctionName = mysqli_query($con, "select group_name from tbl_group_master where group_id='$locationId'") or die("Eroor 11: " . mysqli_error($con));
        $rwLocationName = mysqli_fetch_assoc($loctionName);
        $projectName = mysqli_query($con, "select project_name from tbl_project_master where project_id='$project'") or die("Eroor 12: " . mysqli_error($con));
        $rwprojectName = mysqli_fetch_assoc($projectName);
        $log = mysqli_query($con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$userid', '$username',null,null,'Attendance of $rwLocationName[group_name], $rwprojectName[project_name] Project marked','$date',null,'$host','')") or die('error : ' . mysqli_error($con));
      

      if($log){

        $temp = array();
        $temp['msg'] = "Attendance Date Selected successfully";
        $temp['error'] = 'false';
        echo json_encode($temp);

      }

      else{

      
        $temp = array();
        $temp['msg'] = "Server Error";
        $temp['error'] = 'true';
        echo json_encode($temp);

      }

    /*   echo '<script>taskSuccess("markAttendance?dt=' . $_GET['dt'] . '&group_id=' . $_GET['group_id'] . '&project=' . $_GET['project'] . '","Attendance Date Selected successfully.");</script>';*/
    }

    else{


        $temp = array();
        $temp['msg'] = "No User found in selected location/project/shift.";
        $temp['error'] = 'true';
        echo json_encode($temp);

    

    }

}
  

//search attendance 
if(isset($_POST['groupID'])&&!empty($_POST['groupID'])
  && isset($_POST['userID'])&&!empty($_POST['userID']) 
  && isset($_POST['prjctID'])&&!empty($_POST['prjctID'])  
  && isset($_POST['date'])&&!empty($_POST['date'])
  && isset($_POST['shiftID'])&&!empty($_POST['shiftID'])
  && isset($_POST['page'])&&!empty($_POST['page'])){


    $groupId = $_POST['groupID'];
    $userid = $_POST['userID'];
    $dt = $_POST['date'];
    $prjctID = $_POST['prjctID'];
    $shiftID = $_POST['shiftID'];


    $showcheckConfirmAttenBtn= "false";
    $showModifyBtn = 'false';
    $modifyAttenPerm = 'false';
    $response = array();



//Getting the page number which is to be displayed  
$page = $_POST['page']; 


//Initially we show the data from 1st row that means the 0th row 
$start = 0; 
  
//Limit is 3 that means we will show 3 items at once
$limit = 50; 


    $chekUsr = mysqli_query($con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$userid', user_ids) > 0") or die('Error:' . mysqli_error($con));
    $rwgetRole = mysqli_fetch_assoc($chekUsr);


    $sameGroupIDs = array();
    $group = mysqli_query($con, "select * from tbl_bridge_grp_to_um where find_in_set('$userid',user_ids)") or die('Error' . mysqli_error($con));
    while ($rwGroup = mysqli_fetch_assoc($group)) {
        $sameGroupIDs[] = $rwGroup['user_ids'];
    }
    $sameGroupIDs = implode(',', $sameGroupIDs);
    $sameGroupIDs = explode(',', $sameGroupIDs);
    $sameGroupIDs = array_unique($sameGroupIDs);
    sort($sameGroupIDs);
    $sameGroupIDs = implode(',', $sameGroupIDs);
  

    $slgroup = mysqli_real_escape_string($con, $groupId);
    $grpUserIds = array();
    if (!empty($slgroup)) {

        $matchSamegroupids = explode(',', $sameGroupIDs);
        //echo "select user_ids,group_id from tbl_bridge_grp_to_um WHERE group_id='$slgroup'";
        $getUserID = mysqli_query($con, "select user_ids,group_id from tbl_bridge_grp_to_um WHERE group_id='$slgroup'") or die("Error " . mysqli_error($con));
        $RwgetUserID = mysqli_fetch_assoc($getUserID);
        $grpUserIds = explode(',', $RwgetUserID['user_ids']);
        //print_r($grpUserIds);
        ////print_r($matchSamegroupids);
        $grpUserIds = array_intersect($matchSamegroupids, $grpUserIds);
        
        //print_r($grpUserIds);
        //$grpmyidss = implode(",", $grpUserIds);
        //echo $grpmyids;
    }

     $grpUserIds = implode(',', $grpUserIds);

    /*  echo $grpUserIds;
      die;  */  
 
     if (!empty($slgroup)) {

      /* echo  "SELECT * FROM tbl_user_master tum INNER JOIN tbl_attendance_master tam on tum.user_id = tam.user_id where tum.user_id in($grpUserIds) and tam.mark_date='$dt' and tam.project_id='$prjctID' and tum.user_id!='1' and tum.user_id!='2' and loc_id='$slgroup' order by first_name, last_name asc";

       die;*/

       $retval = mysqli_query($con, "SELECT * FROM tbl_user_master tum INNER JOIN tbl_attendance_master tam on tum.user_id = tam.user_id where tum.user_id in($grpUserIds) and tam.mark_date='$dt' and tam.project_id='$prjctID' and tam.shift_id ='$shiftID'  and tum.user_id!='1' and tum.user_id!='2' and loc_id='$slgroup' order by first_name, last_name asc");


       } else {


/*echo "SELECT * FROM tbl_user_master tum INNER JOIN tbl_attendance_master tam on tum.user_id = tam.user_id where tum.user_id in($sameGroupIDs) and tam.mark_date='$dt' and tam.project_id='$prjctID' and tum.user_id!='1' and tum.user_id!='2' and loc_id='$slgroup' order by first_name, last_name asc";
die;*/
  

      $retval = mysqli_query($con, "SELECT * FROM tbl_user_master tum INNER JOIN tbl_attendance_master tam on tum.user_id = tam.user_id where tum.user_id in($sameGroupIDs) and tam.mark_date='$dt' and tam.project_id='$prjctID' and tam.shift_id ='$shiftID' and tum.user_id!='1' and tum.user_id!='2' and loc_id='$slgroup' order by first_name, last_name asc");
       }


    
//Counting the total item available in the database 
$total = mysqli_num_rows($retval);
$limit =  mysqli_num_rows($retval);




//echo "total : " .$total;

//We can go atmost to page number total/limit

if($limit == 0){

  $limit = 1;

}
$page_limit = $total/$limit; 

//echo "page limit : ".$page_limit;

$page_limit = round($page_limit,0);
$page_limit = $page_limit + 1;

if($page<=$page_limit){

$count = 1;
//Calculating start for every given page number 
$start = ($page - 1) * $limit; 



 $conf = mysqli_query($con, "select attendance_status from tbl_attendance_master where mark_flag=0 and mark_date ='$dt' and user_id in($grpUserIds) and project_id='$prjctID' and loc_id='$slgroup' and shift_id = $shiftID");
  //echo mysqli_num_rows($conf);
  if (mysqli_num_rows($conf) > 0) {
 
    $showcheckConfirmAttenBtn = "true";

  }

  else{

   $showcheckConfirmAttenBtn = "false";

  }


/*  if ($rwgetRole['modify_attendance'] == 1 && $rwUser['mark_flag'] == 1) 
  if ($modify_atndnce == 1 && $row['usr_acvt_dacvt'] == 1) 
                                                                 */           



          
      /*    echo "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$userid', user_ids) > 0";

          die;  */
            
$chekUsr = mysqli_query($con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$userid', user_ids) > 0") or die('Error:' . mysqli_error($con));         
$rwgetRole =  mysqli_fetch_assoc($chekUsr);

$result = mysqli_query($con, "SELECT modify_atndnce,usr_acvt_dacvt FROM tbl_user_master where user_id='$userid'");
   $row = mysqli_fetch_assoc($result);


 $checkAtten = mysqli_query($con, "select * from tbl_attendance_master where mark_date='$dt' and project_id='$prjctID' and user_id in ($grpUserIds) and loc_id='$slgroup'") or die('Error : ' . mysqli_error($con));


  $clist =  mysqli_fetch_assoc($checkAtten);



   $modify_atndnce = $row['modify_atndnce'];
   $active_deactivate = $row['usr_acvt_dacvt'];       
   $mARFlag = $rwgetRole['modify_attendance'];
   $markAttendanceRight = $rwgetRole['mark_attendance'];
   $mark_flag = $clist['mark_flag']; 

   $role = $rwgetRole['role_id'];





 if($mark_flag == 0){

     $showModifyBtn = 'true';


   }


 else if(($mARFlag == 1 && $mark_flag == 1) ||  ($modify_atndnce == 1 && $active_deactivate == 1)){


     

     $showModifyBtn = 'true';
     

 }

 else{

$showModifyBtn = 'false';


 }  

 if (!empty($slgroup)) {

    
       $retval = mysqli_query($con, "SELECT * FROM tbl_user_master tum INNER JOIN tbl_attendance_master tam on tum.user_id = tam.user_id where tum.user_id in($grpUserIds) and tam.mark_date='$dt' and tam.project_id='$prjctID' and tam.shift_id ='$shiftID' and tum.user_id!='1' and tum.user_id!='2' and loc_id='$slgroup' order by first_name, last_name asc limit $start,$limit");
       } else {

  

      $retval = mysqli_query($con, "SELECT * FROM tbl_user_master tum INNER JOIN tbl_attendance_master tam on tum.user_id = tam.user_id where tum.user_id in($sameGroupIDs) and tam.mark_date='$dt' and tam.project_id='$prjctID' and tam.shift_id ='$shiftID' and tum.user_id!='1' and tum.user_id!='2' and loc_id='$slgroup' order by first_name, last_name asc limit $start,$limit");
       }


     while($r =mysqli_fetch_assoc($retval)){

      $slgroup = $r['loc_id'];
      $loctnName = mysqli_query($con, "select group_name from tbl_group_master where group_id='$slgroup'");
      $rwloctnName = mysqli_fetch_assoc($loctnName);

    $prjctID = $r['project_id'];
    $prjctName = mysqli_query($con, "select project_name from tbl_project_master where project_id='$prjctID'");
    $rwprjctName = mysqli_fetch_assoc($prjctName);
    $prjctName = $rwprjctName['project_name'];

      /* <th>S.No.</th>
                                                            <th>Ref. No.</th>
                                                            <th>User Name</th>
                                                            <th>Designation</th>
                                                            <th>Location</th>
                                                            <th>Project</th>
                                                            <th>Role</th>
                                                            <th>Attendance Date</th>*/
     
      $temp = array();
      $temp['s.no'] = $count;
      $temp['ref.no'] = $r['ref_no'];
      $temp['userid'] = $r['user_id'];
      $temp['username'] = $r['first_name']." ".$r['last_name'];
      $temp['designation'] = $r['designation'];
      $temp['location'] = $rwloctnName['group_name'];
      $temp['project'] = $prjctName;
      $temp['role'] = $r['role_onoff'];
      $temp['attendance_status'] = $r['attendance_status'];
      $temp['attendance_date'] = $r['mark_date'];

      array_push($response,$temp);


     $count++;

     } 

    $res = array();
    $res['pageCount'] = $page_limit;
    $res['totalRecord'] = $total;
    $res['show_confirm_atten'] = $showcheckConfirmAttenBtn;
    $res['show_modify_atten'] = $showModifyBtn;
    $res['list'] = $response; 

  
  echo json_encode($res);

}

}








?>