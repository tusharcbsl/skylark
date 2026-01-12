<?php

require_once 'connection.php';

if(isset($_POST['date']) && !empty($_POST['date'])
&&isset($_POST['userid']) && !empty($_POST['userid'])
){

 $dt = date('Y-m-d', strtotime($_POST['date']));
 $userid = $_POST['userid'];
 $response = array();


 $chekUsr = mysqli_query($con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$userid', user_ids) > 0") or die('Error aa:' . mysqli_error($con));
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
    $grpUsrs = implode(',', $sameGroupIDs);


 $total = 0;
 $totalP = 0;
 $totalA = 0;
 $totalAL = 0;
 $totalAE = 0;
 $totalAC = 0;
 $totalAW = 0;
 $totalS = 0;
 $totalSP = 0;
 $totalH = 0;
 $totalDD = 0;
 $totalOD = 0;
 $totalAP = 0;
 $totalPA = 0;
 $totalSH = 0;
 $totalHP = 0;
 $totalHH = 0;
 $totalSL = 0;
 $totalHCL = 0;
 $totalPH = 0;


/* echo "SELECT DISTINCT(loc_id), project_id FROM `tbl_attendance_master` where user_id in($grpUsrs) and mark_date='$dt' and mark_flag='1'";

 die;*/
   $usrpject = mysqli_query($con, "select project from tbl_user_master where user_id='$userid'");
                                                    $rwuserprject = mysqli_fetch_assoc($usrpject);
                                                    $projectId = $rwuserprject['project'];
 
/* $todayattendance = mysqli_query($con, "SELECT DISTINCT(loc_id), project_id FROM `tbl_attendance_master` where user_id in($grpUsrs) and mark_date='$dt' and mark_flag='1'");*/
  $todayattendance = mysqli_query($con, "SELECT DISTINCT(loc_id), project_id FROM `tbl_attendance_master` where user_id in($grpUsrs) and project_id in($projectId) and mark_date='$dt' and mark_flag='1'");
 if (mysqli_num_rows($todayattendance) > 0) {
 	while ($rwtodayattendance = mysqli_fetch_assoc($todayattendance)) {
 	
 			$grpName = mysqli_query($con, "select group_name from tbl_group_master where group_id='$rwtodayattendance[loc_id]'") or die('Error' . mysqli_error($con));
 			$rwgetgrpName = mysqli_fetch_assoc($grpName);
              
            $temp = array();
               
 		    $temp['loc_name'] = $rwgetgrpName['group_name']; 

       
 			$PjctsName = mysqli_query($con, "SELECT project_name FROM tbl_project_master where project_id='$rwtodayattendance[project_id]'");
 			$rwPjctsName = mysqli_fetch_assoc($PjctsName);
 			
 			$temp['proj_name'] = $rwPjctsName['project_name'];
 			$temp['date'] = date('d-m-Y', strtotime($dt)); 
 		
 			$Dailyrprt = mysqli_query($con, "SELECT count(id) FROM tbl_attendance_master where user_id in($grpUsrs) and project_id='$rwtodayattendance[project_id]' and mark_flag='1' and mark_date='$dt' and loc_id='$rwtodayattendance[loc_id]'");
 			$rwDailyrpt = mysqli_fetch_array($Dailyrprt);
 			 $temp['total_emp'] =$rwDailyrpt[0];
 			$total += $rwDailyrpt[0];
            
            //$total;

 			$attCount = array();
 			$countP = mysqli_query($con, "select count(attendance_status) as Pstaus,attendance_status from tbl_attendance_master where mark_flag='1' and mark_date ='$dt' and user_id in($grpUsrs) and project_id='$rwtodayattendance[project_id]' and loc_id='$rwtodayattendance[loc_id]' group by attendance_status");
 			while ($rwCountp = mysqli_fetch_assoc($countP)) {
 				$attCount[] = $rwCountp;
 			}


     $countpp=0;
 			for ($a = 0; $a < count($attCount); $a++) {
 				$atte = $attCount[$a];
 				if ($atte['attendance_status'] == 'P') {
 					$countpp = $atte['Pstaus'];
 					$totalP += $atte['Pstaus'];
 				}
 			}

            $temp['P'] = $countpp;

 			$countA = 0;
 			for ($a = 0; $a < count($attCount); $a++) {
 				$atte = $attCount[$a];
 				if ($atte['attendance_status'] == 'A') {
 					$countA = $atte['Pstaus'];
 					$totalA += $atte['Pstaus'];
                                                                  
 				}
 			}
 			$temp['A'] = $countA;
 	
 			$countAL = 0;
 			for ($a = 0; $a < count($attCount); $a++) {
 				$atte = $attCount[$a];
 				if ($atte['attendance_status'] == 'A(CL)') {
 					$countAL = $atte['Pstaus'];
 					$totalAL += $atte['Pstaus'];
 				}
 			}
 			
            $temp['AL'] = $countAL;

 			$countAE = 0;
 			for ($a = 0; $a < count($attCount); $a++) {
 				$atte = $attCount[$a];

 				if ($atte['attendance_status'] == 'A(EL)') {
 					$countAE = $atte['Pstaus'];
 					$totalAE += $atte['Pstaus'];
 				}
 			}
 			$temp['AE'] = $countAE;

 		$countAW = 0;
 		for ($a = 0; $a < count($attCount); $a++) {
 			$atte = $attCount[$a];

 			if ($atte['attendance_status'] == 'A(WI)') {
 				$countAW = $atte['Pstaus'];
 				$totalAW += $atte['Pstaus'];
 			}
 		}

 		$temp['AW'] =  $countAW;

 		$countAC = 0;
 		for ($a = 0; $a < count($attCount); $a++) {
 			$atte = $attCount[$a];

 			if ($atte['attendance_status'] == 'A(CO)') {
 				$countAC = $atte['Pstaus'];
 				$totalAC += $atte['Pstaus'];
 			}
 		}
 		$temp['AC'] = $countAC;

 		$countAP = 0;
 		for ($a = 0; $a < count($attCount); $a++) {
 			$atte = $attCount[$a];
 			if ($atte['attendance_status'] == 'AP') {
 				$countpp = $atte['Pstaus'];
 				$totalAP += $atte['Pstaus'];
 			}
 		}
 		$temp['AP'] =  $countAP;

 		$countPA = 0;
 		for ($a = 0; $a < count($attCount); $a++) {
 			$atte = $attCount[$a];
 			if ($atte['attendance_status'] == 'PA') {
 				$countPA = $atte['Pstaus'];
 				$totalPA += $atte['Pstaus'];
 			}
 		}
 		$temp['PA'] =  $countPA;

 		$countDD = 0;
 		for ($a = 0; $a < count($attCount); $a++) {
 			$atte = $attCount[$a];

 			if ($atte['attendance_status'] == 'DD') {
 				$countDD = $atte['Pstaus'];
 				$totalDD += $atte['Pstaus'];
 			}
 		}
 		$temp['DD'] =  $countDD;

 		$countOD = 0;
 		for ($a = 0; $a < count($attCount); $a++) {
 			$atte = $attCount[$a];
 			if ($atte['attendance_status'] == 'OD') {
 				$countOD = $atte['Pstaus'];
 				$totalOD += $atte['Pstaus'];
 			}
 		}
 		$temp['OD'] =  $countOD;

 		$countSP = 0;
 		for ($a = 0; $a < count($attCount); $a++) {
 			$atte = $attCount[$a];
 			if ($atte['attendance_status'] == 'SP') {
 				$countSP = $atte['Pstaus'];
 				$totalSP += $atte['Pstaus'];
 			}
 		}
 		$temp['SP'] =  $countSP;

        $countS = 0;   
 		for ($a = 0; $a < count($attCount); $a++) {
 			$atte = $attCount[$a];

 			if ($atte['attendance_status'] == 'S') {
 				$countS = $atte['Pstaus'];
 				$totalS += $atte['Pstaus'];
 			}
 		}
 		$temp['S'] =  $countS;

 		$countSH = 0;
 		for ($a = 0; $a < count($attCount); $a++) {
 			$atte = $attCount[$a];

 			if ($atte['attendance_status'] == 'SH') {
 				$countSH = $atte['Pstaus'];
 				$totalSH += $atte['Pstaus'];
 			}
 		}
 		$temp['SH'] =  $countSH;

 		$countH = 0;
 		for ($a = 0; $a < count($attCount); $a++) {
 			$atte = $attCount[$a];
 			if ($atte['attendance_status'] == 'H') {
 				$countH = $atte['Pstaus'];
 				$totalH += $atte['Pstaus'];
 			}
 		}
 		$temp['H'] =  $countH;

 		$countHH = 0;
 		for ($a = 0; $a < count($attCount); $a++) {
 			$atte = $attCount[$a];

 			if ($atte['attendance_status'] == 'HH') {
 				$countHH = $atte['Pstaus'];
 				$totalHH += $atte['Pstaus'];
 			}
 		}
 		$temp['HH'] =  $countHH;

 		$countHP = 0;
 		for ($a = 0; $a < count($attCount); $a++) {
 			$atte = $attCount[$a];

 			if ($atte['attendance_status'] == 'HP') {
 				$countHP = $atte['Pstaus'];
 				$totalHP += $atte['Pstaus'];
 			}
 		}
 		$temp['HP'] =  $countHP;

 		$countSL = 0;
 		for ($a = 0; $a < count($attCount); $a++) {
 			$atte = $attCount[$a];

 			if ($atte['attendance_status'] == 'SL') {
 				$countSL = $atte['Pstaus'];
 				$totalSL += $atte['Pstaus'];
 			}
 		}
 		$temp['SL'] =  $countSL;
 		
 		$countHCL = 0;
 		for ($a = 0; $a < count($attCount); $a++) {
 			$atte = $attCount[$a];

 			if ($atte['attendance_status'] == 'H(CL)') {
 				$countHCL = $atte['Pstaus'];
 				$totalHCL += $atte['Pstaus'];
 			}
 		}
 		$temp['HCL'] =  $countHCL;
 	
 		$countPH = 0;
 		for ($a = 0; $a < count($attCount); $a++) {
 			$atte = $attCount[$a];

 			if ($atte['attendance_status'] == 'PH') {
 				$countPH = $atte['Pstaus'];
 				$totalPH += $atte['Pstaus'];
 			}
 		}
 		$temp['PH'] =  $countPH;


        array_push($response, $temp);

}


 $Sum = array();
 $Sum['totalEmp'] = $total;
 $Sum['totalP'] = $totalP;
 $Sum['totalA'] =  $totalA;
  $Sum['totalAL'] = $totalAL;
  $Sum['totalAE'] = $totalAE;
 $Sum['totalAC'] =  $totalAC;
  $Sum['totalAW'] = $totalAW;
 $Sum['totalS'] =  $totalS;
  $Sum['totalSP'] = $totalSP;
  $Sum['totalH'] = $totalH;
  $Sum['totalDD'] = $totalDD;
 $Sum['totalOD'] =  $totalOD;
  $Sum['totalAP'] = $totalAP;
 $Sum['totalPA'] =  $totalPA;
  $Sum['totalSH'] = $totalSH;
 $Sum['totalHP'] =  $totalHP;
  $Sum['totalHH'] = $totalHH;
 $Sum['totalSL'] =  $totalSL;
 $Sum['totalHCL'] =  $totalHCL;
 $Sum['totalPH'] =  $totalPH;
 
  
  $res = array();
  $res['msg'] ="Attendance Found Succesfully";
  $res['error'] = 'false';
  $res['total_data'] = $Sum;
  $res['list'] = $response;
   
  
/* $reponse['total_data'] = $Sum;
 $response['list'] = $temp;*/
 echo json_encode($res);



}

else{

  $res = array();
  $res['msg'] ="No Attendance Found";
  $res['error'] = 'true';
  
  echo json_encode($res);

}



}


?>