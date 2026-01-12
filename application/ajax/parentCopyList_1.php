<?php
require_once '../../sessionstart.php';
if(!isset($_SESSION['cdes_user_id'])){
    header("location:../../logout");
}

require_once '../config/database.php';
     //for user role

$chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$_SESSION[cdes_user_id]', user_ids) > 0") or die('Error:'. mysqli_error($db_con));
   
   $rwgetRole = mysqli_fetch_assoc($chekUsr);
  
  // echo $rwgetRole['dashboard_mydms']; die;
   if($rwgetRole['move_storage_level'] != '1'){
   header('Location: ../../index');
   }


   $parentId = $_POST['parentId']; 
  $level=$_POST['levelDepth'];
  
   $sl_id=$_POST['sl_id']; 

 echo "<input type='hidden' value='$parentId' name='lastCopyToId' />";
     //echo "<input type='hidden' value='$level' name='lastCopyIdLevel' />";
   
 
$childName = mysqli_query($db_con,"select * from tbl_storage_level where sl_parent_id='$parentId' AND sl_id != '$sl_id'") or die('Error in parent id:'.mysqli_error($db_con));

if(mysqli_num_rows($childName) == 0){} else {
echo '<br>';
//echo "<form method='post'>";
echo' <select class="form-control" name="moveToChildId'.$level.'" id="childMoveLevel'.$level.'" style="width:100%;">'
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
<script>

$("#childMoveLevel<?php echo $level;?>").change(function(){
    var copyf=$("#tocopyfolder").val();
    var lbl=$(this).val();
    var sfolder=$(this).find(":selected").text();
    //alert(lbl);
    $.post("application/ajax/parentCopyList.php", {parentId:lbl,levelDepth:<?php echo $level+1;?>,sl_id:<?php echo $sl_id; ?>}, function(result,status){
            if(status=='success'){
                $("#subChild<?php echo $level;?>").html(result);
              // alert($level);
             //alert(copyf+lbl);
           $.post("application/ajax/checkDuplicate.php", {parentId: lbl, levelDepth: <?php echo $level+1;?>, folder:copyf}, function (result, status) {
                        if (status == 'success') {
                            
                            if(result==1){  
                               $("#error").html(copyf+" is already exist in "+sfolder+". Please rename storage name.");
                               $("#tocopyfolder").removeAttr("readonly");
                           }else{
                              $("#error").html("");
                               $("#tocopyfolder").attr("readonly","readonly");
                                
                           }
                        }
                    });
            }
        }); 
}); 

</script>

