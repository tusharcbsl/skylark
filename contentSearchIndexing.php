<!DOCTYPE html>
<html>
    <?php
    require_once './loginvalidate.php';
    require_once './application/config/database.php';
    require_once './application/pages/head.php';
   
    $chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$_SESSION[cdes_user_id]', user_ids) > 0") or die('Error:' . mysqli_error($db_con));

    $rwgetRole = mysqli_fetch_assoc($chekUsr);
    


    // echo $rwgetRole['dashboard_mydms']; die;
    if ($rwgetRole['view_group_list'] != '1') {
        header('Location: ./index');
    }
    ?>
    <link href="assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />

   
    <body class="fixed-left">
 <!-- Begin page -->
        <div id="wrapper">

            <!-- Top Bar Start -->
            <?php require_once './application/pages/topBar.php'; ?>
            <!-- Top Bar End -->


            <!-- ========== Left Sidebar Start ========== -->
            <?php require_once './application/pages/sidebar.php'; ?>
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
                            <div class="col-sm-12">
                                
                            </div>
                        </div>

                        <div class="panel">

                            <div class="panel-body">
                                <div class="card-box">

                                            <form action="#" data-parsley-validate novalidate method="post" enctype="multipart/form-data">
                                                       
                                                <div class="form-group row">
                                                    <div class="col-md-4">
                                                        <label for="userName">Select Storage for Indexing<span style="color:red;">*</span></label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <?php
                                                        $perm = mysqli_query($db_con, "select sl_id from tbl_storagelevel_to_permission where user_id='$_SESSION[cdes_user_id]' group by sl_id");
                                                        $rwPerm = mysqli_fetch_assoc($perm);
                                                        $slperm = $rwPerm['sl_id'];
                                                        $sllevel = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$slperm'");
                                                        $rwSllevel = mysqli_fetch_assoc($sllevel);
                                                        $level = $rwSllevel['sl_depth_level'];
                                                        ?>
                                                        <select class="form-control select2"  name="storage" data-placeholder="select Storage" required>
                                                            <option disabled selected>Select</option>
                                                            <?php
                                                            /*
                                                              $storage= mysqli_query($db_con, "select * from tbl_storage_level") or die('Error:'.mysqli_errno($db_con));

                                                              while($rwStorage= mysqli_fetch_assoc($storage)){
                                                              //echo $rwStorage['sl_id'];


                                                              echo '<option value="'.$rwStorage['sl_id'].'">';
                                                              //checkParent($rwStorage['sl_id'],$rwStorage['sl_id'],$slperm,$db_con,$level);
                                                              echo '</option>';


                                                              }
                                                             * 
                                                             */
                                                            //  echo '<option value="'.$rwStorage['sl_id'].'">';

                                                            findChild($slperm, $level, $slperm);
                                                            // echo '</option>';
                                                            ?>
                                                        </select> 
                                                        <?php

                                                        function findChild($sl_id, $level, $slperm) {

                                                            global $db_con;
                                                            echo '<option value="' . $sl_id . '">';
                                                            parentLevel($sl_id, $db_con, $slperm, $level, '');
                                                            echo '</option>';
                                                            $sql_child = "select * FROM tbl_storage_level WHERE sl_parent_id = '$sl_id' ";

                                                            $sql_child_run = mysqli_query($db_con, $sql_child) or die('Error:' . mysqli_error($db_con));

                                                            if (mysqli_num_rows($sql_child_run) > 0) {

                                                                while ($rwchild = mysqli_fetch_assoc($sql_child_run)) {

                                                                    $child = $rwchild['sl_id'];
                                                                    findChild($child, $level, $slperm);
                                                                }
                                                            }
                                                        }

                                                        function parentLevel($slid, $db_con, $slperm, $level, $value) {

                                                            if ($slperm == $slid) {
                                                                $parent = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$slid' ") or die('Error' . mysqli_error($db_con));
                                                                $rwParent = mysqli_fetch_assoc($parent);

                                                                if ($level < $rwParent['sl_depth_level']) {
                                                                    parentLevel($rwParent['sl_parent_id'], $db_con, $slperm, $level, $rwParent['sl_name']);
                                                                }
                                                            } else {
                                                                $parent = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$slid' and sl_parent_id='$slperm'") or die('Error' . mysqli_error($db_con));
                                                                if (mysqli_num_rows($parent) > 0) {

                                                                    $rwParent = mysqli_fetch_assoc($parent);
                                                                    if ($level < $rwParent['sl_depth_level']) {
                                                                        parentLevel($rwParent['sl_parent_id'], $db_con, $slperm, $level, $rwParent['sl_name']);
                                                                    }
                                                                } else {
                                                                    $parent = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$slid'") or die('Error' . mysqli_error($db_con));
                                                                    $rwParent = mysqli_fetch_assoc($parent);
                                                                    $getparnt = $rwParent['sl_parent_id'];
                                                                    if ($level <= $rwParent['sl_depth_level']) {
                                                                        parentLevel($getparnt, $db_con, $slperm, $level, $rwParent['sl_name']);
                                                                    } else {
                                                                        //header('Location: ./index.php');
                                                                        // header("Location: ./storage?id=".urlencode(base64_encode($slperm)));
                                                                    }
                                                                }
                                                            }

                                                            //echo $value;
                                                            if (!empty($value)) {
                                                                $value = $rwParent['sl_name'] . '<b> > </b>';
                                                            } else {
                                                                $value = $rwParent['sl_name'];
                                                            }
                                                            echo $value;
                                                        }
                                                        ?>

                                                    </div>
                                                </div>

                                                <div class="form-group  m-b-0">
                                                    <button class="btn btn-primary waves-effect waves-light" type="submit" name="sub" >
                                                        Submit
                                                    </button>
                                                    <button type="reset" class="btn btn-danger waves-effect waves-light m-l-5">
                                                        Cancel
                                                    </button>
                                                </div>

                                            </form>
                                        </div>
                                
                            </div>
                            <!-- end: page -->
                        </div> <!-- end Panel -->
                    </div> <!-- container -->

                </div> <!-- content -->


                <!-- /Right-bar -->
              
            </div>
            <!-- END wrapper -->
            <?php require_once './application/pages/footer.php'; ?>

        </div>
        <!-- ============================================================== -->
        <!-- End Right content here -->
        <!-- ============================================================== -->
        <!-- Right Sidebar -->
        <?php require_once 'application/pages/rightSidebar.php'; ?>
        <?php require_once 'application/pages/footerForjs.php'; ?>
<script src="assets/plugins/select2/js/select2.min.js" type="text/javascript"></script>
<script type="text/javascript">
            $(".select2").select2();

        </script>


<script src="https://npmcdn.com/pdfjs-dist/build/pdf.js"></script>
        <script src="viewer-pdf/getpdftext.js"></script>  
        
        <div  style="display:none; text-align: center; color: #fff;  background: rgba(0,0,0,0.5); width: 100%; height: 100%; z-index: 2000; position: fixed; top:0;" id="wait">
                <img src="assets/images/uploading.gif" alt="load"  style="margin-top: 250px; width: 250px;" />
            </div>
<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
//require_once './loginvalidate.php';
require_once './application/config/database.php';
if(isset($_POST['sub'])){
   echo $slid=$_POST['storage'];
$doc= mysqli_query($db_con, "select * from tbl_document_master where doc_extn='pdf' and doc_name='$slid'");
while($rwDoc=mysqli_fetch_assoc($doc)){
    $docPath='extract-here/'.$rwDoc['doc_path'];
    $uploaddir= substr($docPath, 0, strrpos($docPath, '/'));
    $doc_id=$rwDoc['doc_id'];
    if(file_exists($docPath)){
    gettxtpdf($docPath, $uploaddir, $doc_id);
    }else{
        echo 'ok'; die();
    }
    
}
}
?>
<?php

        function gettxtpdf($filepath, $path, $doc_id) {
            ?>
            <script>
                //debugger;
               $("#wait").html('<img src="assets/images/uploading.gif" alt="load"  style="margin-top: 250px; width: 250px;  "/><br> Indexing <?php //echo $filepath; ?> for content search. please wait.');
                $("#wait").show();
                gettext("<?php echo $filepath; ?>").then(function (text){
                    $.post("textcreator.php", {TEXT: text, PATH: "<?php echo $path; ?>", ID: "<?php echo $doc_id; ?>"}, function (result, status) {
                        if (status == 'success') {
                           $("#wait").hide();
                        }
                    });
                }, function (reason) {
                    console.error(reason);
                });
            </script>            
        <?php sleep(1); } ?>   