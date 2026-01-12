<!DOCTYPE html>
<html>
   <?php
   require_once './loginvalidate.php';
   require_once './application/config/database.php';
   require_once './application/pages/head.php';
   
   //for user role
   $chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$_SESSION[cdes_user_id]', user_ids) > 0") or die('Error:'. mysqli_error($db_con));
   
   $rwgetRole = mysqli_fetch_assoc($chekUsr);


   if($rwgetRole['create_child_storage'] != '1'){
   header('Location: ./index');
   }
   ?>
        
        <link href="assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
        
    <body class="fixed-left">

        <!-- Begin page -->
        <div id="wrapper">

            <!-- Top Bar Start -->
            <?php require_once './application/pages/topBar.php';?>
            <!-- Top Bar End -->


            <!-- ========== Left Sidebar Start ========== 1001/10556/00959 12/12/2011 14:33:58-->

            <?php require_once './application/pages/sidebar.php';?>
            <!-- Left Sidebar End --> 
          <!-- Start right Content here -->
            <!-- ============================================================== -->                      
            <div class="content-page">
                <!-- Start content -->
                <div class="content">
                    <div class="container">

                        <!-- Page-Title -->
                        <div class="row">
                            
                                 <ol class="breadcrumb">
                                     <li><a href="createNewChild">Storage Management</a></li>
                                    <li class="active">Create New Child</li>
                                </ol>
                              
                        </div>                       
                        <div class="row">
                            <div class="box box-primary">
                                <div class="box-header with-border">
                                        <h4 class="header-title">Add Storage</h4>
                                    </div>
                                <div class="box-body">
                                    
                                <div class="col-lg-12">
                                <div class="card-box">
                                    <div class="row">
                                         <form action="#" data-parsley-validate novalidate method="post" enctype="multipart/form-data">
                                             
                                                 <div class="form-group row">
                                                     <div class="col-md-2">
                                                        <label for="userName">Select Depth*</label>
                                                     </div>
                                                     <div class="col-md-4">
                                                         <select class="form-control" id="depth_level" name="depthLevel" required>
                                                             <option selected disabled>Select Depth</option>
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
                                                        <label for="userName">Select Parent *</label>
                                                     </div>
                                                     <div class="col-md-4">
                                                         <select class="form-control" id="parent" name="parentName" required>
                                                            <option selected disabled>Select Parent</option>
                                                            <?php 
                                                            
                                                            ?>
                                                        </select>
                                                </div>
                                             </div>
                                             <div class="clearfix"></div>
                                             <div class="form-group row">
                                                     <div class="col-md-2">
                                                        <label for="userName">Select Child</label>
                                                     </div>
                                                     <div class="col-md-4">
                                                         <select class="form-control" id="child_level" name="childName">
                                                            <option selected disabled>Select Child</option>
                                                            
                                                        </select>
                                                </div>
                                             </div>
                                                 <div class="form-group row">
                                                     <div class="col-md-2">
                                                        <label for="userName">Sub Child Name*</label>
                                                     </div>
                                                     <div class="col-md-4">
                                                        <input type="text" name="storage" parsley-trigger="change" required placeholder="Enter storage name" class="form-control" id="userName">
                                                </div>
                                             </div>
                                            
                                             <div class="col-md-12">
                                                  <div class="form-group  m-b-0">
                                                   <button class="btn btn-primary waves-effect waves-light" type="submit" name="createChild">
                                                                Submit
                                                        </button>
                                                        <button type="reset" class="btn btn-danger waves-effect waves-light m-l-5">
                                                                Cancel
                                                        </button>
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
        <script src="assets/plugins/bootstrap-filestyle/js/bootstrap-filestyle.min.js" type="text/javascript"></script>
        <script src="assets/plugins/select2/js/select2.min.js" type="text/javascript"></script>
       
        <script type="text/javascript" src="assets/plugins/parsleyjs/parsley.min.js"></script>
        
        
        <script type="text/javascript">
			$(document).ready(function() {
				$('form').parsley();
                                
                        });
                        $(".select2").select2();
                        
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
    $.post("application/ajax/childList.php", {sl_id:slId}, function(result,status){
            if(status=='success'){
                $("#child_level").html(result);
            }
        }); 
});
		</script>


	</body>
</html>
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
        
    if(!empty($childName)){
        $parent= mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$childName'");
        $rwParent= mysqli_fetch_assoc($parent);
        $parentID=$rwParent['sl_id'];
        $level=$rwParent['sl_depth_level']+1;
    }else{
        $parent= mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$parentName'");
        $rwParent= mysqli_fetch_assoc($parent);
        $parentID=$rwParent['sl_id'];
        $level=$rwParent['sl_depth_level']+1;
    }
      $checkLvlName = mysqli_query($db_con, "select * from tbl_storage_level where sl_parent_id='$lbl' AND sl_name = '$create'") or die('Error in checkLvlName:' . mysqli_error($db_con));
        if (mysqli_num_rows($checkLvlName) > 0) {
            echo'<script>taskFailed("storage?id=' . urlencode(base64_encode($sl_id)) . '","Storage of Same Name Already Exist !");</script>';
            }
    $create= mysqli_query($db_con, "insert into tbl_storage_level (`sl_id`, `sl_name`, `sl_parent_id`, `sl_depth_level`) values(null,'$storage','$parentID','$level')") or die('Error'.mysqli_error($db_con));
    if($create){
    $sl_id= mysqli_insert_id($db_con);
    $ChildName = mysqli_query($db_con, "select sl_name from tbl_storage_level where sl_id='$sl_id'");
    $rwchildName = mysqli_fetch_assoc($ChildName);
    $childName = $rwchildName['sl_name'];
    $log=mysqli_query($db_con,"insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,'$sl_id','New Child $childName Created','$date',null,'$host','')") or die('error : '. mysqli_error($db_con));
    echo'<script>taskSuccess("index","New Child created successfully !!");</script>';
    }
     mysqli_close($db_con); 
}

?>