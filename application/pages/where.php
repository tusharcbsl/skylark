<?php

if ($_SESSION['cdes_user_id'] == '1') {
    if (!empty($_GET['taskStats'])) {
        if (empty($where)) {
            
            if($_GET['taskStats']=="Processed"){
               $where = "where (tdawf.task_status = '$_GET[taskStats]' or tdawf.task_status ='Done' or tdawf.task_status ='approved')"; 
            }else{
                $where = "where tdawf.task_status = '$_GET[taskStats]' ";
            
            }
        } else {
            
            if($_GET['taskStats']=="Processed"){
               $where = "and (tdawf.task_status = '$_GET[taskStats]' or tdawf.task_status ='Done' or tdawf.task_status ='approved')"; 
            }else{
                $where = "and tdawf.task_status = '$_GET[taskStats]' ";
            
            }
        }
    }
    if (!empty($_GET['taskPrioty'])) {
        if (!empty($where)) {
            $where .= " and tsm.priority_id = '$_GET[taskPrioty]' ";
        } else {
            $where = "where tsm.priority_id = '$_GET[taskPrioty]' ";
        }
    }
    if (!empty($_GET['asinBy'])) {
        if (!empty($where)) {
            $where .= "and tdawf.assign_by = '$_GET[asinBy]' ";
        } else {
            $where = "where  tdawf.assign_by = '$_GET[asinBy]'";
        }
    }
    if (!empty($_GET['ticketid'])) {
        if (!empty($where)) {
            $where .= "and tsm.ticket_id = '$_GET[ticketid]' ";
        } else {
            $where = "where  tsm.ticket_id = '$_GET[ticketid]'";
        }
    }
    if (!empty($_GET['startDate']) && !empty($_GET['endDate'])) {
        $startDate = $_GET['startDate'];
        $endDate = $_GET['endDate'];
        $startDate = strtotime($startDate);
        $endDate = strtotime($endDate);
        $endDate = $endDate + 23 * 60 * 60 + 59 * 60 + 59;
        $startDate = date("Y-m-d H:i:s", $startDate);
        $endDate = date("Y-m-d H:i:s", $endDate);

        if (!empty($where)) {
            $where .= "and tdawf.start_date between '$startDate' and '$endDate'";
        } else {
            $where = "where tdawf.start_date between '$startDate' and '$endDate'";
        }
    }
    //@dv lockdown25-apr-20 filter by workflow name
    if (!empty($workfid)) {
        if (!empty($where)) {
            $where .= " and tsm.workflow_id='$workfid'";
        } else {
            $where = " where tsm.workflow_id='$workfid'";
        }
    }
} else {
    if (!empty($_GET['taskStats']) && $_GET['taskStats'] != 'Pending') {

        if (empty($where)) {
            if($_GET['taskStats']=="Processed"){
               $where = "where action_by='$_SESSION[cdes_user_id]' and (tdawf.task_status = '$_GET[taskStats]' or tdawf.task_status ='Done' or tdawf.task_status ='approved')"; 
            }else{
                $where = "where action_by='$_SESSION[cdes_user_id]' and tdawf.task_status = '$_GET[taskStats]' ";
            
            }
        } else {
            if($_GET['taskStats']=="Processed"){
                
               $where = "and action_by='$_SESSION[cdes_user_id]' and (tdawf.task_status = '$_GET[taskStats]' or tdawf.task_status ='Done' or tdawf.task_status ='approved')"; 
           
            }else{
                $where = "and action_by='$_SESSION[cdes_user_id]' and tdawf.task_status = '$_GET[taskStats]' ";
            
            }
        }
    } else if (!empty($_GET['taskStats']) && $_GET['taskStats'] == 'Pending') {

        if (empty($where)) {
            $where = "where ((tsm.assign_user='$_SESSION[cdes_user_id]' and tdawf.NextTask='0') or (alternate_user='$_SESSION[cdes_user_id]' and tdawf.NextTask= '3') or (supervisor='$_SESSION[cdes_user_id]' and tdawf.NextTask= '4')) and tdawf.task_status = '$_GET[taskStats]' ";
        } else {
            $where .= "and ((tsm.assign_user='$_SESSION[cdes_user_id]' and tdawf.NextTask='0') or (alternate_user='$_SESSION[cdes_user_id]' and tdawf.NextTask= '3') or (supervisor='$_SESSION[cdes_user_id]' and tdawf.NextTask= '4')) and tdawf.task_status = '$_GET[taskStats]' ";
        }
    } else {
        if (!empty($where)) {
            $where .= "and ((tsm.assign_user='$_SESSION[cdes_user_id]' and tdawf.NextTask='0') or (alternate_user='$_SESSION[cdes_user_id]' and tdawf.NextTask= '3') or (supervisor='$_SESSION[cdes_user_id]' and tdawf.NextTask= '4'))";
        } else {
            $where = "where ((tsm.assign_user='$_SESSION[cdes_user_id]' and tdawf.NextTask='0') or (alternate_user='$_SESSION[cdes_user_id]' and tdawf.NextTask= '3') or (supervisor='$_SESSION[cdes_user_id]' and tdawf.NextTask= '4'))";
        }
    }



    if (!empty($_GET['taskPrioty'])) {
        if (!empty($where)) {
            $where .= " and tsm.priority_id = '$_GET[taskPrioty]' ";
        } else {
            $where = "where tsm.assign_user='$_SESSION[cdes_user_id]'  and  tsm.priority_id = '$_GET[taskPrioty]' ";
        }
    }
    if (!empty($_GET['ticketid'])) {
        if (!empty($where)) {
            $where .= " and tdawf.ticket_id = '$_GET[ticketid]' ";
        } else {
            $where = "where tsm.assign_user='$_SESSION[cdes_user_id]'  and  tdawf.ticket_id = '$_GET[ticketid]' ";
        }
    }

    if (!empty($_GET['asinBy'])) {
        if (!empty($where)) {
            $where .= "and tdawf.assign_by = '$_GET[asinBy]' ";
        } else {
            $where = "where tsm.assign_user='$_SESSION[cdes_user_id]'  and tdawf.assign_by = '$_GET[asinBy]'";
        }
    }

    if (!empty($_GET['startDate']) && !empty($_GET['endDate'])) {
        $startDate = $_GET['startDate'];
        $endDate = $_GET['endDate'];
        $startDate = strtotime($startDate);
        $endDate = strtotime($endDate);
        $endDate = $endDate + 23 * 60 * 60 + 59 * 60 + 59;
        $startDate = date("Y-m-d H:i:s", $startDate);
        $endDate = date("Y-m-d H:i:s", $endDate);

        if (!empty($where)) {
            $where .= "and tdawf.start_date between '$startDate' and '$endDate'";
        } else {
            $where = "where tdawf.start_date between '$startDate' and '$endDate' and (tsm.assign_user='$_SESSION[cdes_user_id]' or tdawf.action_by='$_SESSION[cdes_user_id]')";
        }
    }
    //@dv lockdown25-apr-20 filter by workflow name
    if (!empty($workfid)) {

        if (!empty($where)) {
            $where .= " and tsm.workflow_id='$workfid'";
        } else {
            $where = " where tsm.workflow_id='$workfid'";
        }
    }
}
//if (!empty($_GET['taskStats']) && ($_GET['taskStats'] == 'Approved' || $_GET['taskStats'] == 'Processed' || $_GET['taskStats'] == 'Complete' || $_GET['taskStats'] == 'Done' || $_GET['taskStats'] == 'Aborted' || $_GET['taskStats'] == 'Rejected' )) {
//    $where .= " order by action_time desc";
//} else {
//    $where .= " order by tdawf.id desc";
//}
?>