<?php


 require_once 'connection.php';
//get control panel data of attendance

if(isset($_POST['groupID'])&&!empty($_POST['groupID'])
  && isset($_POST['userID'])&&!empty($_POST['userID']) 
  && isset($_POST['prjctID'])&&!empty($_POST['prjctID'])  
  && isset($_POST['date'])&&!empty($_POST['date'])){
  
  $userid = $_POST['userID'];
  $dt = $_POST['date'];
  $groupId = $_POST['groupID'];
  $prjctID=$_POST['prjctID'];
  
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

 $response = array();


 $temp = array();

	
  $countP = mysqli_query($con, "select count(attendance_status) as Pstaus from tbl_attendance_master where attendance_status='P' and mark_date ='$dt' and user_id in($grpUserIds) and project_id='$prjctID' and loc_id='$slgroup'");
  $rwcountP = mysqli_fetch_assoc($countP);
  $temp['Pstaus'] = $rwcountP['Pstaus'];
                                                      
  $countA = mysqli_query($con, "select count(attendance_status) as Astaus from tbl_attendance_master where attendance_status='A' and mark_date ='$dt' and user_id in($grpUserIds) and project_id='$prjctID' and loc_id='$slgroup'");
   $rwcountA = mysqli_fetch_assoc($countA);
   $temp['Astaus'] = $rwcountA['Astaus'];
  
    $countACL = mysqli_query($con, "select count(attendance_status) as ACLstaus from tbl_attendance_master where attendance_status='A(CL)' and mark_date ='$dt' and user_id in($grpUserIds) and project_id='$prjctID'  and loc_id='$slgroup'");
  $rwcountACL = mysqli_fetch_assoc($countACL);                                              
   $temp['ACLstaus'] = $rwcountACL['ACLstaus'];
                                                    
   $countAEL = mysqli_query($con, "select count(attendance_status) as AELstaus from tbl_attendance_master where attendance_status='A(EL)' and mark_date ='$dt' and user_id in($grpUserIds) and project_id='$prjctID'  and loc_id='$slgroup'");
    $rwcountAEL = mysqli_fetch_assoc($countAEL);
    $temp['AELstaus'] =  $rwcountAEL['AELstaus']; 
                                                    
  $countAWI = mysqli_query($con, "select count(attendance_status) as AWIstaus from tbl_attendance_master where attendance_status='A(WI)' and mark_date ='$dt' and user_id in($grpUserIds) and project_id='$prjctID' and loc_id='$slgroup'");
  $rwcountAWI = mysqli_fetch_assoc($countAWI);
  $temp['AWIstaus'] =$rwcountAWI['AWIstaus'];
   
  $countACO = mysqli_query($con, "select count(attendance_status) as ACOstaus from tbl_attendance_master where attendance_status='A(CO)' and mark_date ='$dt' and user_id in($grpUserIds) and project_id='$prjctID' and loc_id='$slgroup'");
  $rwcountACO = mysqli_fetch_assoc($countACO);
 $temp['ACOstaus'] =$rwcountACO['ACOstaus'];

 $countAP = mysqli_query($con, "select count(attendance_status) as APstaus from tbl_attendance_master where attendance_status='AP' and mark_date ='$dt' and user_id in($grpUserIds) and project_id='$prjctID' and loc_id='$slgroup'");
 $rwcountAP = mysqli_fetch_assoc($countAP);
 $temp['APstaus'] =$rwcountAP['APstaus'];

 $countPA = mysqli_query($con, "select count(attendance_status) as PAstaus from tbl_attendance_master where attendance_status='PA' and mark_date ='$dt' and user_id in($grpUserIds) and project_id='$prjctID' and loc_id='$slgroup'");
 $rwcountPA = mysqli_fetch_assoc($countPA);
 $temp['PAstaus'] =$rwcountPA['PAstaus'];

 $countDD = mysqli_query($con, "select count(attendance_status) as DDstaus from tbl_attendance_master where attendance_status='DD' and mark_date ='$dt' and user_id in($grpUserIds) and project_id='$prjctID' and loc_id='$slgroup'");
 $rwcountDD = mysqli_fetch_assoc($countDD);
 $temp['DDstaus'] =$rwcountDD['DDstaus'];

 $countOD = mysqli_query($con, "select count(attendance_status) as ODstaus from tbl_attendance_master where attendance_status='OD' and mark_date ='$dt' and user_id in($grpUserIds) and project_id='$prjctID' and loc_id='$slgroup'");
 $rwcountOD = mysqli_fetch_assoc($countOD);
 $temp['ODstaus'] = $rwcountOD['ODstaus'];

 $countSP = mysqli_query($con, "select count(attendance_status) as SPstaus from tbl_attendance_master where attendance_status='SP' and mark_date ='$dt' and user_id in($grpUserIds) and project_id='$prjctID' and loc_id='$slgroup'");
 $rwcountSP = mysqli_fetch_assoc($countSP);
 $temp['SPstaus'] = $rwcountSP['SPstaus'];

 $countS = mysqli_query($con, "select count(attendance_status) as Sstaus from tbl_attendance_master where attendance_status='S' and mark_date ='$dt' and user_id in($grpUserIds) and project_id='$prjctID' and loc_id='$slgroup'");
 $rwcountS = mysqli_fetch_assoc($countS);
 $temp['Sstaus'] = $rwcountS['Sstaus'];

 $countSH = mysqli_query($con, "select count(attendance_status) as SHstaus from tbl_attendance_master where attendance_status='SH' and mark_date ='$dt' and user_id in($grpUserIds) and project_id='$prjctID' and loc_id='$slgroup'");
 $rwcountSH = mysqli_fetch_assoc($countSH);
 $temp['SHstaus'] = $rwcountSH['SHstaus'];

 $countH = mysqli_query($con, "select count(user_id) as Hstaus from tbl_attendance_master where attendance_status='H' and mark_date ='$dt' and user_id in($grpUserIds) and project_id='$prjctID' and loc_id='$slgroup'");
 $rwcountH = mysqli_fetch_assoc($countH);
 $temp['Hstaus'] =  $rwcountH['Hstaus'];

 $countHH = mysqli_query($con, "select count(user_id) as HHstaus from tbl_attendance_master where attendance_status='HH' and mark_date ='$dt' and user_id in($grpUserIds) and project_id='$prjctID' and loc_id='$slgroup'");
 $rwcountHH = mysqli_fetch_assoc($countHH);
 $temp['HHstaus'] =  $rwcountHH['HHstaus'];

 $countHP = mysqli_query($con, "select count(user_id) as HPstaus from tbl_attendance_master where attendance_status='HP' and mark_date ='$dt' and user_id in($grpUserIds) and project_id='$prjctID' and loc_id='$slgroup'");
 $rwcountHP = mysqli_fetch_assoc($countHP);
 $temp['HPstaus'] = $rwcountHP['HPstaus'];

 $countSL = mysqli_query($con, "select count(user_id) as SLstaus from tbl_attendance_master where attendance_status='SL' and mark_date ='$dt' and user_id in($grpUserIds) and project_id='$prjctID' and loc_id='$slgroup'");
 $rwcountSL = mysqli_fetch_assoc($countSL);
 $temp['SLstaus'] =  $rwcountSL['SLstaus'];

 $countHCL = mysqli_query($con, "select count(user_id) as HCLstaus from tbl_attendance_master where attendance_status='H(CL)' and mark_date ='$dt' and user_id in($grpUserIds) and project_id='$prjctID' and loc_id='$slgroup'");
 $rwcountHCL = mysqli_fetch_assoc($countHCL);
 $temp['HCLstaus'] =  $rwcountHCL['HCLstaus'];

 $countPH = mysqli_query($con, "select count(user_id) as PHstaus from tbl_attendance_master where attendance_status='PH' and mark_date ='$dt' and user_id in($grpUserIds) and project_id='$prjctID' and loc_id='$slgroup'");
 $rwcountPH = mysqli_fetch_assoc($countPH);
 $temp['PHstaus'] =  $rwcountPH['PHstaus'];

 $response['msg'] = "Fetched Successfully";
 $response['error'] ="false";
 $response['statusList']= $temp;

 echo json_encode($response);

}

/*else {

$response =array();
$response['msg'] = "Not Fetched Successfully";
$response['error'] ="true";

$temp = array();
$response['statusList'] = $temp;
  

echo json_encode($response);



}*/

//getComoff data
if(isset($_POST['uIdCompOff'])&& !empty($_POST['uIdCompOff'])){

   $usrid = $_POST['uIdCompOff'];

   $response = array();
   $list= array();

   $compoffSp = mysqli_query($con, "SELECT * FROM tbl_attendance_master where attendance_status= 'SP' AND comoff_status='1' AND user_id='$usrid'") or die("Error:" . mysqli_error($con));
            if (mysqli_num_rows($compoffSp) > 0) {
                while ($rwcompoffSp = mysqli_fetch_assoc($compoffSp)) {
                  
           

                  $temp = array();
                  $temp['attendance_status'] =  $rwcompoffSp['attendance_status'];
                  $temp['mark_date'] =   $rwcompoffSp['mark_date'];

                  array_push($list,$temp);
                 
                   /* <option value="<?php echo ($rwcompoffSp['attendance_status']) . '(' . ($rwcompoffSp['mark_date']) . ')'; ?>"><?php echo ($rwcompoffSp['attendance_status']) . ' (' . ($rwcompoffSp['mark_date']) . ')'; ?></option>  
                    <?php*/
                }

                  $response['error'] = 'false';
                  $response['msg'] ='Data found Successfully';
                  $response['list'] =$list;
      

            }

            else{

               
                  $temp = array();
                 
                  $temp['attendance_status'] ='null';
                  $temp['mark_date'] = 'null';
                    

                  array_push($list,$temp);
               

                  
                  $response['error'] = 'true';
                  $response['msg'] ='No data found';
                  $response['list'] =$list;

            }

             echo json_encode($response);
  

}




//mark attendance 

if (isset($_POST['useridMark'])&&!empty($_POST['useridMark'])
   &&isset($_POST['attStatus'])&&!empty($_POST['attStatus'])
   &&isset($_POST['date'])&&!empty($_POST['date'])
) {
        $id = $_POST['useridMark'];
        //$id  = explode(",",$id);

        $dt = $_POST['date'];
        $MarkStatus = $_POST['attStatus'];
        $leveType = $_POST['leveType'];
        $sndyprst = $_POST['sndyprst'];



        if (!empty($_POST['leveType'] == 'CL') || !empty($_POST['leveType'] == 'EL') || !empty($_POST['leveType'] == 'WI') || !empty($_POST['leveType'] == 'CO')) {

            //this is the case of absent 
            
           /* echo "update tbl_attendance_master set attendance_status='$MarkStatus($leveType)', compoff_date='$sndyprst' where user_id='$id' and mark_date='$dt'";

            die;*/

            $markAttnce = mysqli_query($con, "update tbl_attendance_master set attendance_status='$MarkStatus($leveType)', compoff_date='$sndyprst' where user_id='$id' and mark_date='$dt'");
            if ($markAttnce) {
                //echo "update tbl_attendance_master set comoff_status='0' where user_id='$id' and compoff_date='$sndyprst'";
                $markAttnce = mysqli_query($con, "update tbl_attendance_master set comoff_status='0' where user_id='$id' and compoff_date='$sndyprst'") or die('Error:' . mysqli_error($con));
            }
        } elseif (!empty($_POST['attStatus'] == 'H')) {

         /*   echo "update tbl_attendance_master set attendance_status='$MarkStatus' where user_id IN ($id) and mark_date='$dt'";
            die;
*/
            $markAttnce = mysqli_query($con, "update tbl_attendance_master set attendance_status='$MarkStatus' where mark_date='$dt' and  user_id IN ($id)");


        } elseif (!empty($_POST['attStatus'] == 'SP')) {

           /*echo "update ezee_att_testing.tbl_attendance_master set attendance_status='$MarkStatus', comoff_status='1', compoff_date='$MarkStatus($dt)' where user_id IN ($id) and mark_date='$dt'";

           die;*/
    /*        $update = mysqli_query($db_con, "update tbl_attendance_master set mark_flag='1' where mark_date='$dt' and project_id='$prjctID' and user_id in ($grpUserIds) and loc_id='$slgroup'")*/

            $markAttnce = mysqli_query($con, "update tbl_attendance_master set attendance_status='$MarkStatus', comoff_status='1', compoff_date='$MarkStatus($dt)' where user_id IN ($id) and mark_date='$dt'"); //or die('Error:' . mysqli_error($con));
        } else {

          /*  echo  "update tbl_attendance_master set attendance_status='$MarkStatus' where user_id IN ($id) and mark_date='$dt'";
            die;*/
           

            $markAttnce = mysqli_query($con, "update tbl_attendance_master set attendance_status='$MarkStatus' where user_id IN ($id) and mark_date='$dt'");
        }
        if ($markAttnce) {

        /*    echo '<script>taskSuccess("markAttendance?start=' . $_GET['start'] . '&dt=' . $_GET['dt'] . '&group_id=' . $_GET['group_id'] . '&project=' . $_GET['project'] . '&limit=' . $_GET['limit'] . '","Attendance Mark successfully.");</script>';*/
           
            $temp = array();
            $temp['msg']  = 'Attendance Mark successfully';
            $temp['error'] = 'false';
            echo json_encode($temp);

           

           

        } else {

          /*  echo '<script>taskFailed("markAttendance?start=' . $_GET['start'] . '&dt=' . $_GET['dt'] . '&group_id=' . $_GET['group_id'] . '&project=' . $_GET['project'] . '&limit=' . $_GET['limit'] . '","Attendance Mark Failed.");</script>';*/

            $temp = array();
            $temp['msg']  = 'Attendance Mark Failed';
            $temp['error'] = 'true';
            echo json_encode($temp);
        }
      
    }


 //check ShortLeave
 
 if(isset($_POST['chkSrtUserid']) && isset($_POST['chkSrtUserid']) &&
    !empty($_POST['chkSrtDate']) && !empty($_POST['chkSrtDate']))
   {


$userid = $_POST['chkSrtUserid'];
$attdate = $_POST['chkSrtDate'];

/*echo $attdate;

die;*/

$attdate=strtotime($attdate);
$month=date("m",$attdate);
 $year=date("Y",$attdate);
/*echo "select attendance_status,mark_date from tbl_attendance_master where user_id='$userid' and MONTH(mark_date)='$month' and YEAR(mark_date)='$year' and attendance_status='SL'";
die;*/
$checkSl = mysqli_query($con, "select attendance_status,mark_date from tbl_attendance_master where user_id='$userid' and MONTH(mark_date)='$month' and YEAR(mark_date)='$year' and attendance_status='SL'");

          if(mysqli_num_rows($checkSl)>0){
              
              $temp = array();
              $temp['msg'] = "Short Leave Not Available";
              $temp['error'] = "true";

              echo json_encode($temp);

        
            }else{

              $temp = array();
              $temp['msg'] = "Short Leave Available";
              $temp['error'] = "false";

              echo json_encode($temp);
            }
 

 }


   /*  if (isset($_POST['confAtt'])) {
        $attDate = $_POST['attDate'];
        $slgrp = $_POST['slgrp'];
        $project = $_POST['project'];
        $update = mysqli_query($db_con, "update tbl_attendance_master set mark_flag='1' where mark_date='$dt' and project_id='$prjctID' and user_id in ($grpUserIds) and loc_id='$slgroup'") or die('Error : ' . mysqli_error($db_con));
        if ($update) {
            echo'<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '","Confirmation Saved Successfully !!");</script>';
        } else {
            echo'<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '","Failed to Saved Attendance Confirmation !!");</script>';
        }
    }*/


   if(isset($_POST['conftAttDate'])&&!empty($_POST['conftAttDate'])
      &&isset($_POST['conftAttProjectId']) && !empty($_POST['conftAttProjectId'])
      &&isset($_POST['conftAttLocId']) && !empty($_POST['conftAttLocId'])
      &&isset($_POST['conftAttUserId']) && !empty($_POST['conftAttUserId'])
 ){

      $dt = $_POST['conftAttDate']; 
      $prjctID = $_POST['conftAttProjectId'];
      $slgroup = $_POST['conftAttLocId'];
      $userid = $_POST['conftAttUserId'];


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
 
    
     $update = mysqli_query($con, "update tbl_attendance_master set mark_flag='1' where mark_date='$dt' and project_id='$prjctID' and user_id in ($grpUserIds) and loc_id='$slgroup'") or die('Error : ' . mysqli_error($con));
        if ($update) {
          //  echo'<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '","Confirmation Saved Successfully !!");</script>';

              $temp = array();
            $temp['msg']  = 'Attendance Confirmed Successfully';
            $temp['error'] = 'false';
            echo json_encode($temp);

         

        } else {
           // echo'<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '","Failed to Saved Attendance Confirmation !!");</script>';


            $temp = array();
            $temp['msg']  = 'Failed to confirm attendance';
            $temp['error'] = 'true';
            echo json_encode($temp);

          

        }


   }



?>