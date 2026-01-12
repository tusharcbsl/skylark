<?php

$sameGroupIDs = array();
$group = mysqli_query($db_con, "select * from tbl_bridge_grp_to_um where find_in_set('$_SESSION[cdes_user_id]',user_ids)") or die('Error' . mysqli_error($db_con));
while ($rwGroup = mysqli_fetch_assoc($group)) {
    $sameGroupIDs[] = $rwGroup['user_ids'];
}
$sameGroupIDs = implode(',', $sameGroupIDs);
$sameGroupIDs = explode(",", $sameGroupIDs);
$sameGroupIDs = array_unique($sameGroupIDs);
sort($sameGroupIDs);
$sameGroupIDs = implode(',', $sameGroupIDs);
