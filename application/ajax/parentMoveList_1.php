<?php
require_once '../../sessionstart.php';
if(!isset($_SESSION['cdes_user_id'])){
    header("location:../../logout");
}
if (isset($_SESSION['lang'])){
     $file = "../../".$_SESSION['lang'].".json";
 } else {
     $file = "../../English.json";
 }
 //for user role
$data = file_get_contents($file);
 $lang = json_decode($data, true);
require_once '../config/database.php';

 //for user role
$perm = mysqli_query($db_con, "select sl_id from tbl_storagelevel_to_permission where user_id='$_SESSION[cdes_user_id]' group by sl_id");
$rwPerm = mysqli_fetch_assoc($perm);
$slperm = $rwPerm['sl_id'];
$chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$_SESSION[cdes_user_id]', user_ids) > 0") or die('Error:'. mysqli_error($db_con));
   
   $rwgetRole = mysqli_fetch_assoc($chekUsr);
  
  // echo $rwgetRole['dashboard_mydms']; die;
   if($rwgetRole['move_storage_level'] != '1'){
   header('Location: ../../index');
   }
   
   $parentId = $_POST['parentId']; 
  $level=$_POST['levelDepth'];
  
 $sl_id=$_POST['sl_id'];
$type = $_POST['type'];
if((strtolower($type)=='file')){
  $type='file';  
}else{
  $type='storage';  
} 
 

           echo "<input type='hidden' value='$parentId' name='lastMoveId' />";
           echo "<input type='hidden' value='$level' name='lastMoveIdLevel' />";
if(strtolower($type)!='file'){
  $where=" AND sl_id != '$sl_id'";  
}
//echo "select * from tbl_storage_level where sl_parent_id='$parentId' ".$where; die;
$childName = mysqli_query($db_con,"select * from tbl_storage_level where sl_parent_id='$parentId' ".$where) or die('Error in parent id:'.mysqli_error($db_con));

if(mysqli_num_rows($childName) == 0){} else {
echo '<br>';
//echo "<form method='post'>";
echo' <select class="form-control select22" name="moveToChildId'.$level.'" id="childMoveLevel'.$level.'" style="width:100%">'
. '<option selected disabled>'.$lang['Select_Child'].'</option>';
while($rwchildName= mysqli_fetch_assoc($childName)){
    echo '<option value="'.$rwchildName['sl_id'].'">'.$rwchildName['sl_name'].'</option>';
}
echo '</select>';
//echo "</form>";
?>
<div id="subChild<?php echo $level;?>">
      
            
</div>
   

 <?php } ?>
<link href="assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
<script src="assets/plugins/select2/js/select2.min.js" type="text/javascript"></script>
<script>

$("#childMoveLevel<?php echo $level;?>").change(function(){
    var lbl=$(this).val();
    //alert(lbl);
    $.post("application/ajax/parentMoveList.php", {type:'<?php echo $type;?>',parentId:lbl,levelDepth:<?php echo $level+1;?>,sl_id:<?php echo $sl_id; ?>}, function(result,status){
            if(status=='success'){
                $("#subChild<?php echo $level;?>").html(result);
              // alert($level);
           
            }
        }); 
}); 
$('.select22').select2();
</script>

