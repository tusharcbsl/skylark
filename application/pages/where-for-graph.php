<?php
if($_SESSION['cdes_user_id']=='1'){   
}else{
    
    if(!empty($where)){
        $where.=" action_by='$_SESSION[cdes_user_id]' or ((tsm.assign_user='$_SESSION[cdes_user_id]' and tdawf.NextTask='0') or (alternate_user='$_SESSION[cdes_user_id]' and tdawf.NextTask= '3') or (supervisor='$_SESSION[cdes_user_id]' and tdawf.NextTask= '4'))";
        }else{
        $where="where action_by='$_SESSION[cdes_user_id]' or ((tsm.assign_user='$_SESSION[cdes_user_id]' and tdawf.NextTask='0') or (alternate_user='$_SESSION[cdes_user_id]' and tdawf.NextTask= '3') or (supervisor='$_SESSION[cdes_user_id]' and tdawf.NextTask= '4'))";
    }
}
if(!empty($_GET['taskStats']) &&($_GET['taskStats']=='Approved' || $_GET['taskStats']=='Processed' || $_GET['taskStats']=='Complete'  || $_GET['taskStats']=='Done'  || $_GET['taskStats']=='Aborted' || $_GET['taskStats']=='Rejected' )){
    $where .=" order by action_time desc";
} else{
    $where .=" order by tdawf.id desc";
}
?>