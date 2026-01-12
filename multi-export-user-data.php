<?php

require_once './loginvalidate.php';
require_once './application/config/database.php';
$sameGroupIDs = array();
mysqli_set_charset($db_con, "utf8");
$group = mysqli_query($db_con, "select * from tbl_bridge_grp_to_um where find_in_set('$_SESSION[cdes_user_id]',user_ids)") or die('Error' . mysqli_error($db_con));
while ($rwGroup = mysqli_fetch_assoc($group)) {
    $sameGroupIDs[] = $rwGroup['user_ids'];
}
$sameGroupIDs = implode(',', $sameGroupIDs);
$sameGroupIDs = explode(',', $sameGroupIDs);
$sameGroupIDs = array_unique($sameGroupIDs);
sort($sameGroupIDs);
$sameGroupIDs = implode(',', $sameGroupIDs);
$getGrpId = trim($_POST['userIds']);
$searchText = trim($_POST['searchtext']);
if (isset($_POST['exportUser'], $_POST['token'])) {
    $selectFormat = trim($_POST['select_Fm']);

    if ($selectFormat == "xlsx") {
        $filterCondition = "";
        if (!empty($searchText)) {
            $filterCondition .= " and (CONCAT(first_name, ' ', last_name) LIKE '%$searchText%' or emp_id LIKE '%$searchText%' or phone_no LIKE '%$searchText%' or user_email_id LIKE '%$searchText%')";
        }
        if (!empty($getGrpId)) {
            $Ugrp = mysqli_query($db_con, "select * from tbl_bridge_grp_to_um where group_id='$getGrpId'");
            while ($rwUgrp = mysqli_fetch_assoc($Ugrp)) {
                $userIds = $rwUgrp['user_ids'];
                $filterCondition .= " and user_id in($userIds)";
            }
        }
        if (!empty($searchText) || !empty($getGrpId)) {
            //echo "select user_id,first_name as FirstName,last_name as LastName,designation as Designation,user_email_id as userEmail,superior_name as SuperiorName,phone_no from tbl_user_master where user_id!='1' and user_id!='$_SESSION[cdes_user_id]' $filterCondition order by first_name, last_name asc"; die;
            $exptUsr = mysqli_query($db_con, "select user_id,first_name as FirstName,last_name as LastName,designation as Designation,user_email_id as userEmail,superior_name as SuperiorName,phone_no from tbl_user_master where user_id!='1' and user_id!='$_SESSION[cdes_user_id]' $filterCondition order by first_name, last_name asc");
        } else {
            $exptUsr = mysqli_query($db_con, "select user_id,first_name as FirstName,last_name as LastName,designation as Designation,user_email_id as userEmail,superior_name as SuperiorName,phone_no from tbl_user_master where user_id in($sameGroupIDs) and user_id!='1' and user_id!='$_SESSION[cdes_user_id]' order by first_name, last_name asc");
        }

        while ($users = mysqli_fetch_field($exptUsr)) {
            if ($users->name != 'user_id')
                $header1 .= $users->name . ",";
        }
        $header1 .= 'User Role';
        while ($row = mysqli_fetch_assoc($exptUsr)) {

            $line = '';
            foreach ($row as $key => $value) {
                if ((!isset($value) ) || ( $value == "" ) || ($value == " ") || ($value == NULL)) {
                    $value = "--";
                }
                if ($key != 'user_id') {

                    if ($key == 'SuperiorName') {
                        $user = mysqli_query($db_con, "select first_name,last_name from tbl_user_master where user_id='$value'");
                        $rwUser = mysqli_fetch_assoc($user);
                        $line .= $rwUser['first_name'] . ' ' . $rwUser['last_name'] . ',';
                    } else {
                        $line .= $value . ',';
                    }
                }
            }
            $row['user_id'];
            $UserRole = mysqli_query($db_con, "select role_id from tbl_bridge_role_to_um where find_in_set($row[user_id],user_ids)");
            $rwUrole = mysqli_fetch_assoc($UserRole);
            $userRid = $rwUrole['role_id'];
            $UserRoleNm = mysqli_query($db_con, "select user_role from tbl_user_roles where role_id= '$userRid'");
            $rwUserRoleNm = mysqli_fetch_assoc($UserRoleNm);
            $roleName = $rwUserRoleNm['user_role'];
            $line .= $roleName;
            $result1 .= trim($line) . "\n";
        }


        //$result1 = str_replace("\r", "", $result1);
        header("Content-Type: application/xls");
        header("Content-Disposition: attachment; filename=userlist.xlsx");
        header("Pragma: no-cache");
        header("Expires: 0");
        echo "\xEF\xBB\xBF";
        print "$header1\n$result1";
    } elseif ($selectFormat == "pdf") {
        require('./wordwrap.php');
        $filterCondition = "";
        if (!empty($searchText)) {
            $filterCondition .= " and (CONCAT(first_name, ' ', last_name) LIKE '%$searchText%' or emp_id LIKE '%$searchText%' or phone_no LIKE '%$searchText%' or user_email_id LIKE '%$searchText%')";
        }
        if (!empty($getGrpId)) {
            $Ugrp = mysqli_query($db_con, "select * from tbl_bridge_grp_to_um where group_id='$getGrpId'");
            while ($rwUgrp = mysqli_fetch_assoc($Ugrp)) {
                $userIds = $rwUgrp['user_ids'];
                $filterCondition .= "and user_id in($userIds)";
            }
        }
        if (!empty($searchText) || !empty($getGrpId)) {
            $exptUsr = mysqli_query($db_con, "select user_id,first_name as FirstName,last_name as LastName,designation as Designation,user_email_id as UserEmail,superior_name as SuperiorName,phone_no as PhoneNumber from tbl_user_master where user_id!='1' and user_id!='$_SESSION[cdes_user_id]' $filterCondition order by first_name, last_name asc");
        } else {
            $exptUsr = mysqli_query($db_con, "select user_id,first_name as FirstName,last_name as LastName,designation as Designation,user_email_id as UserEmail,superior_name as SuperiorName,phone_no as PhoneNumber from tbl_user_master where user_id in($sameGroupIDs) and user_id!='1' and user_id!='$_SESSION[cdes_user_id]' order by first_name, last_name asc");
        }
        $width = 0;
        $widthCell = array();
        $headers = array();
        $headers[] = 'Sr. No.';
        while ($fields = mysqli_fetch_field($exptUsr)) {
            if ($fields->name != 'user_id') {
                $width += 50;
                $headers[] = $fields->name;
                $widthCell[] = 50;
            }
        }
        $width += 50;
        $width += 50;
        $headers[] = 'User Role';
        $widthCell[] = 50;
        $widthCell[] = 50;
        $pdf = new PDF_MC_Table('L', 'mm', array($width + 5, $width));
        $pdf->SetMargins(3.5, 3.5, 5.5);
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        $pdf->AddPage();
        $pdf->SetFont('Times', 'B');

        $pdf->SetWidths($widthCell);
        $pdf->Row($headers);
        $i = 1;
        while ($row = mysqli_fetch_assoc($exptUsr)) {
            $row['user_id'];
            $UserRole = mysqli_query($db_con, "select role_id from tbl_bridge_role_to_um where find_in_set($row[user_id],user_ids)");
            $rwUrole = mysqli_fetch_assoc($UserRole);
            $userRid = $rwUrole['role_id'];

            $UserRoleNm = mysqli_query($db_con, "select user_role from tbl_user_roles where role_id= '$userRid'");
            $rwUserRoleNm = mysqli_fetch_assoc($UserRoleNm);
            $data = array();
            $pdf->SetFont('Times', '', 11);
            $data[] = $i . '.';
            $data[] = $row['FirstName'];
            $data[] = $row['LastName'];
            $data[] = $row['Designation'];
            $data[] = $row['UserEmail'];
            $SuperiorName = $row['SuperiorName'];
            $user = mysqli_query($db_con, "select first_name,last_name from tbl_user_master where user_id='$SuperiorName'");
            $rwUser = mysqli_fetch_assoc($user);
            $data[] = $rwUser['first_name'] . ' ' . $rwUser['last_name'];
            $data[] = $row['PhoneNumber'];
            $data[] = $rwUserRoleNm['user_role'];
            $pdf->Row($data);
            $i++;
        }
        $pdf->Output();

        header("Content-Type: application/pdf charset=utf-8");
        header('Content-type: text/plain; charset=utf-8');
        header("Cache-Control: no-cache");
        header("Accept-Ranges: none");
        header("Content-Disposition: attachment; filename=\"userlist.pdf\"");
    }
}
?>