<?php

	require_once 'connection.php';

	if(isset($_POST['userid'])&&!empty($_POST['userid'])){
    
	    $privileges = array();
	    $response = array();
	    
	    $userid = $_POST['userid'];



	    $priv = mysqli_query($con, "SELECT group_id FROM tbl_bridge_grp_to_um where find_in_set('$userid',user_ids)") or die('Error' . mysqli_error($con));
	      while ($rwPriv = mysqli_fetch_assoc($priv)) {
	                                        array_push($privileges, $rwPriv['group_id']);
	                                    }
	                                    $privileges = array_filter($privileges, function($value) {
	                                        return $value !== '';
	                                    });
	                                    $groups = array_unique($privileges);
	                                    //print_r($groups);
	                                    $wfids = array();
	                                    foreach ($groups as $group) {
	                                        $work = mysqli_query($con, "SELECT * FROM `tbl_workflow_to_group` WHERE find_in_set('$group',group_id)");

	                                        while ($rwWorkflow = mysqli_fetch_assoc($work)) {
	                                            array_push($wfids, $rwWorkflow['workflow_id']);
	                                        }
	                                    }
	                                    $wfids = array_filter($wfids, function($value) {
	                                        return $value !== '';
	                                    });
	                                    $wfids = implode("','", $wfids);
	                                    //print_r($wfids);
	                                    // echo "select * from tbl_workflow_master where workflow_id in('$wfids')";
	                                   // echo '<ul style="overflow-y:auto;max-height:200px;">';
	                                    $getWorkflw = mysqli_query($con, "select * from tbl_workflow_master where workflow_id in('$wfids') and form_tbl_name IS NOT NULL ") or die('Error in getWorkflw upload:' . mysqli_error($con));

	                                    while ($rwgetWorkflw = mysqli_fetch_assoc($getWorkflw)) {
                                       
                                        $temp = array();
                                        $temp['workflow_id'] = $rwgetWorkflw['workflow_id'];
                                        $temp['workflow_name'] = $rwgetWorkflw['workflow_name'];
                                       // $temp['error'] ='false';
                                        array_push($response, $temp);

                                    }

                                    
                                    echo json_encode($response);


}

                               else{

                                        $temp = array();
                                        $temp['message'] = 'No workflow found';
                                        $temp['error'] ='true';
                                        echo json_encode($temp);


}
                      


	?>