<?php
require'connection.php';

if(isset($_POST['userid'])&&!empty($_POST['userid'])){
	
	$userid = $_POST['userid'];
	
	$result = array();
	
	$getRoleIdQry= mysqli_query($con, "select * from tbl_bridge_role_to_um where find_in_set('$userid',user_ids)") or die('Error'. mysqli_error($con));
	
	$sqlresult = mysqli_fetch_assoc($getRoleIdQry);
	$rwsqlresult= $sqlresult['role_id'];
	   
	$getRolePrivQry= mysqli_query($con, "SELECT * FROM tbl_user_roles where role_id ='$rwsqlresult';") or die('Error'. mysqli_error($con));
	
	$row=mysqli_fetch_all($getRolePrivQry,MYSQLI_ASSOC);
    echo json_encode($row);    
}

?>