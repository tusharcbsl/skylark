<?php
require'connection.php';

if(isset($_POST['userid'])&&!empty($_POST['userid'])){
	
	$userid = $_POST['userid'];
	
	$response = array();
	$roleids = array();
        
        $grp_by_rl_ids = mysqli_query($con, "SELECT group_id,user_ids,roleids FROM `tbl_bridge_grp_to_um` where find_in_set($userid,user_ids)");
        while ($rwGrp = mysqli_fetch_array($grp_by_rl_ids)) {
            if (!empty($rwGrp['roleids'])) {
                $roleids[] = $rwGrp['roleids'];
            }
        }
        $roleids = implode(',', $roleids);
        $roleids = explode(',', $roleids);
        $roleids = array_unique($roleids);
        $roleids = implode(',', $roleids);
        
        
        if (!empty($roleids)) {
           
            $rol = mysqli_query($con, "select role_id,user_role from tbl_user_roles where role_id in($roleids)order by user_role asc") or die('Error' . mysqli_error($db_con));
            while ($rwRole = mysqli_fetch_assoc($rol)) {
                if ($rwRole['role_id'] != 1) {
                    $response['role_id'] = $rwRole['role_id'];
                    $response['role_name'] = $rwRole['user_role'];
                }
            }
            
            echo json_encode($response);
        }else{
            
                $json_response['status']="false";
                $json_response['message']="No Role Found";
                $json= json_encode($json_response);
                echo $json;
        }
}

?>