<!DOCTYPE html>
<html>
    <link href="assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
         <link href="assets/plugins/bootstrap-select/css/bootstrap-select.min.css" rel="stylesheet" />
        <link href="assets/plugins/multiselect/css/multi-select.css"  rel="stylesheet" type="text/css" />
   <?php
   require_once './loginvalidate.php';
   require_once './application/config/database.php';
   require_once './application/pages/head.php';
   
       //for user role
   $chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$_SESSION[cdes_user_id]', user_ids) > 0") or die('Error:'. mysqli_error($db_con));
   
   $rwgetRole = mysqli_fetch_assoc($chekUsr);


   if($rwgetRole['assign_metadata'] != '1'){
   header('Location: ./index');
   }
   ?>
       
 
  ?>      
       
    <body class="fixed-left">

        <!-- Begin page -->
        <div id="wrapper">

            <!-- Top Bar Start -->
            <?php require_once './application/pages/topBar.php';?>
            <!-- Top Bar End -->


            <!-- ========== Left Sidebar Start ========== 1001/10556/00959 12/12/2011 14:33:58-->

            <?php require_once './application/pages/sidebar.php';?>
            <!-- Left Sidebar End --> 



            <!-- ============================================================== -->
            <!-- Start right Content here -->
            <!-- ============================================================== -->                      
            <div class="content-page">
                <!-- Start content -->
                <div class="content">
                    <div class="container">

                        <!-- Page-Title -->
                        <div class="row">
                            
                                 <ol class="breadcrumb">
                                    <li><a href="#"><?php echo $lang['Storage_Management'];?></a></li>
                                    <li class="active"><?php echo $lang['Crt_New_Cld'];?></li>
                                </ol>
                              
                        </div>
                        
                        <div class="row">
                            <div class="box box-primary">
                                <div class="box-header with-border">
                                        <h4 class="header-title"><?php echo $lang['Ad_Strge_Lvl'];?></h4>
                                    </div>
                                <div class="box-body">
                                    
                                <div class="col-lg-12">
                                <div class="card-box">
                                    <div class="row">
                                         <form action="#" data-parsley-validate novalidate method="post" enctype="multipart/form-data">
                                             
                                                 <div class="form-group row">
                                                     <div class="col-md-2">
                                                        <label for="userName"><?php echo $lang['Select_Depth_Level'];?><span style="color:red;">*</span></label>
                                                     </div>
                                                     <div class="col-md-4">
                                                         <select class="form-control" id="depth_level" name="depthLevel" required>
                                                           <option selected disabled style="background: #808080; color: #121213;"><?php echo $lang['Select_Depth_Level'];?></option>  
                                                         <?php 
                                                            $depth=mysqli_query($db_con,"select sl_depth_level from tbl_storage_level group by sl_depth_level");
                                                            while($rwDepth= mysqli_fetch_assoc($depth)){
                                                                 echo '<option>'.$rwDepth['sl_depth_level'].'</option>';
                                                            }
                                                            ?>
                                                        </select>
                                                </div>
                                             </div>
                                             <div class="form-group row">
                                                     <div class="col-md-2">
                                                         <label for="userName"><?php echo $lang['Select_Parent'];?><span style="color:red;">*</span></label>
                                                     </div>
                                                     <div class="col-md-4">
                                                         <select class="form-control" id="parent" name="parentName" required>
                                                            <option selected disabled style="background: #808080; color: #121213;"><?php echo $lang['Select_Parent'];?></option>
                                                           
                                                        </select>
                                                </div>
                                             </div>
                                             <div class="clearfix"></div>
                                             <div id="childWithMeta">
                                             <div class="form-group row">
                                                     <div class="col-md-2">
                                                        <label for="userName"><?php echo $lang['Slt_Node'];?></label>
                                                     </div>
                                                     <div class="col-md-4">
                                                         <select class="form-control" id="child_level" name="childName">
                                                            <option selected disabled style="background: #808080; color: #121213;"><?php echo $lang['Select_Child'];?></option>
                                                        </select>
                                                </div>
                                             </div>
                                             <div class="form-group row" id="multiselect">
                                                     <div class="col-md-2">
                                                        <label for="userName"><?php echo $lang['List_of_Fields'];?></label>
                                                     </div>
                                                     <div class="col-md-4 shiv">
                                                       <span><strong><?php echo $lang['Field_Slt'];?>:</strong></span>
                                                       <label><strong><?php echo $lang['Fld_Asnd'];?>: </strong></label>
                                                        <select multiple="multiple" class="multi-select" id="my_multi_select1" name="my_multi_select1[]" data-plugin="multiselect">
                                                        <?php
                                                        $arrarMeta=array();
                                                        $metas=mysqli_query($db_con,"select * from tbl_metadata_to_storagelevel");
                                                        while($metaval= mysqli_fetch_assoc($metas)){
                                                            array_push($arrarMeta, $metaval['metadataid']);
                                                        }
                                                        $meta= mysqli_query($db_con, "select * from tbl_metadata_master order by field_name asc");
                                                        while($rwMeta= mysqli_fetch_assoc($meta)){
                                                            if(in_array($rwMeta['id'], $arrarMeta)){
                                                                echo '<option value="'.$rwMeta['id'].'" selected>'.$rwMeta['field_name'].'</option>';
                                                            }else{         
                                                               echo '<option value="'.$rwMeta['id'].'">'.$rwMeta['field_name'].'</option>';
                                                            }
                                                        }
                                                        ?>
                                                        </select>
                                                </div>
                                             </div>
                                             </div>
                                             <div class="col-md-12">
                                                  <div class="form-group  m-b-0">
                                                    <button class="btn btn-primary waves-effect waves-light" type="submit" name="createChild">
                                                                <?php echo $lang['Submit']?>
                                                        </button>
                                                        <a href="assignFields" class="btn btn-danger waves-effect waves-light m-l-5">
                                                                <?php echo $lang['Reset']?>
                                                        </a>
                                                </div>
                                             </div>

                                        </form>
                                    </div>
                                </div>
                        </div>
                                </div>
                            </div>				
			</div>
     

            		</div> <!-- container -->
                               
                </div> <!-- content -->

                <?php require_once './application/pages/footer.php';?>

            </div>
            <!-- ============================================================== -->
            <!-- End Right content here -->
            <!-- ============================================================== -->


            <!-- Right Sidebar -->
            <?php require_once './application/pages/rightSidebar.php';?>
            <!-- /Right-bar -->


        </div>
        <!-- END wrapper -->

  <?php require_once './application/pages/footerForjs.php';?>
        <!-- jQuery  -->
        <script type="text/javascript" src="assets/plugins/multiselect/js/jquery.multi-select.js"></script>
        <script src="assets/js/jquery.core.js"></script>
        
        <script type="text/javascript" src="assets/plugins/parsleyjs/parsley.min.js"></script>
        <script type="text/javascript">
			$(document).ready(function() {
				$('form').parsley();
                                
                        });
                       
                        
                        //firstname last name 
                        $("input#userName, input#lastName").keypress(function (e) {
                        //if the letter is not digit then display error and don't type anything
                        if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57) && e.which != 46) {
                            //display error message
                            return true;
                        }else{
                            return false;
                        }
                        str = $(this).val();
                        str = str.split(".").length - 1;
                        if (str > 0 && e.which == 46) {
                            return false;
                        }
                         });
                         $("input#phone").keypress(function (e) {
                        //if the letter is not digit then display error and don't type anything
                        if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57) && e.which != 46) {
                            //display error message
                            return false;
                        }
                        str = $(this).val();
                        str = str.split(".").length - 1;
                        if (str > 0 && e.which == 46) {
                            return false;
                        }
                         });
                         
$("#depth_level").change(function(){
    var lbl=$(this).val();
    //alert(lbl);
    $.post("application/ajax/parentList.php", {level:lbl}, function(result,status){
            if(status=='success'){
                $("#parent").html(result);
            }
        }); 
}); 
$("#parent").change(function(){
    var slId=$(this).val();
   // alert(slId);
    $.post("application/ajax/childListwithasignfield.php", {sl_id:slId}, function(result,status){
            if(status=='success'){
                $("#childWithMeta").html(result);
            }
        }); 
});
/*
$("#child_level").change(function(){
    var slId=$(this).val();
 //alert(slId);
    $.post("application/ajax/metaList.php", {sl_id:slId}, function(result,status){
            if(status=='success'){
                $("#multiselect").html(result);
            }
            //alert(result);
        }); 
});
       */
        </script>

<?php 
if(isset($_POST['createChild'])){
    $depthLevel=$_POST['depthLevel'];
    $depthLevel= mysqli_real_escape_string($db_con,$depthLevel);
    $parentName=$_POST['parentName'];
    $parentName= mysqli_real_escape_string($db_con,$parentName);
    $childName=$_POST['childName'];
    $childName= mysqli_real_escape_string($db_con,$childName);
    $storage= filter_input(INPUT_POST, "storage");
    $storage= mysqli_real_escape_string($db_con,$storage);
    $fields=$_POST['my_multi_select1'];
        $flag=0;
        if(!empty($childName)){
            $reset= mysqli_query($db_con, "delete from tbl_metadata_to_storagelevel where sl_id='$childName'");
        }else{
            $reset= mysqli_query($db_con, "delete from tbl_metadata_to_storagelevel where sl_id='$parentName'");
        }
    foreach ($fields as $field){
        $field= preg_replace("/[^0-9]/", "", $field);
        if(!empty($childName)){
            //check meta data assigned or not
            $match = mysqli_query($db_con,"select * from tbl_metadata_to_storagelevel where sl_id='$childName' and metadata_id='$field'") or die('Error:'. mysqli_error($db_con));
            if(mysqli_num_rows($match)<=0){
                //assign meta data
            $create= mysqli_query($db_con, "insert into tbl_metadata_to_storagelevel (`metadata_id`, `sl_id`) values('$field','$childName')") or die('Error'.mysqli_error($db_con));
            // find meta data details
            $metan= mysqli_query($db_con, "select * from tbl_metadata_master where id='$field'");
            $rwMetan= mysqli_fetch_assoc($metan);
            //check meta data in table tbl_document_master
            $checkDoc= mysqli_query($db_con, "SHOW COLUMNS FROM tbl_document_master LIKE '$rwMetan[field_name]'");
            if(mysqli_num_rows($checkDoc)<=0){ //if not
                $metaCreateDoc= mysqli_query($db_con, "ALTER TABLE tbl_document_master ADD `$rwMetan[field_name]` $rwMetan[data_type]($rwMetan[length_data])  null");
            }
            $flag=1;
            $sl_id=$childName;
            }else{
                $sl_id=$childName;
            }
        }else{
            //check meta data assigned or not
            $match = mysqli_query($db_con,"select * from tbl_metadata_to_storagelevel where sl_id='$parentName' and metadata_id='$field'") or die('Error:'. mysqli_error($db_con));
            if(mysqli_num_rows($match)<=0){
                //assign meta data
            $create= mysqli_query($db_con, "insert into tbl_metadata_to_storagelevel (`metadata_id`, `sl_id`) values('$field','$parentName')") or die('Error'.mysqli_error($db_con));
            // find meta data details
            $metan= mysqli_query($db_con, "select * from tbl_metadata_master where id='$field'");
            $rwMetan= mysqli_fetch_assoc($metan);
            //check meta data in table tbl_document_master
            $checkDoc= mysqli_query($db_con, "SHOW COLUMNS FROM tbl_document_master LIKE '$rwMetan[field_name]'");
            if(mysqli_num_rows($checkDoc)<=0){ //if not
                $metaCreateDoc= mysqli_query($db_con, "ALTER TABLE tbl_document_master ADD `$rwMetan[field_name]` $rwMetan[data_type]($rwMetan[length_data])  null");
            }
            $flag=1;
            $sl_id=$parentName;
            }else{
                $sl_id=$parentName;
            }
        }
 }
if($flag==1){
    $log=mysqli_query($db_con,"insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,'$sl_id','new metadata assigned to $sl_id','$date',null,'$host','')") or die('error on suc: '.$sl_id. mysqli_error($db_con));
    echo '<script>metasuccess("assignFields");</script>';
     }else{
         //create log
         $log=mysqli_query($db_con,"insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,'$sl_id','failed to assigned metadata to $sl_id','$date',null,'$host','')") or die('error on failed: '.$sl_id. mysqli_error($db_con));
         echo '<script>metafailed("assignFields");</script>';
     }
       
     mysqli_close($db_con);        
}

?>
 </body>
</html>