<!DOCTYPE html>
<html>
    <?php
    require_once './loginvalidate.php';
    require_once './application/config/database.php';
    require_once './application/pages/head.php';
    ?>

    <link href="assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />

    <body class="fixed-left">

        <!-- Begin page -->
        <div id="wrapper">

            <!-- Top Bar Start -->
            <?php require_once './application/pages/topBar.php'; ?>
            <!-- Top Bar End -->


            <!-- ========== Left Sidebar Start ========== 1001/10556/00959 12/12/2011 14:33:58-->

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

                            <ol class="breadcrumb">
                                <li><a href="createStorage"><?php echo $lang['Storage_Management'];?></a></li>
                                <li class="active"><?php echo $lang['Create Storage'];?></li>
                            </ol>

                        </div>

                        <div class="row">
                            <div class="box box-primary">
                                <div class="box-header with-border">
                                    <h4 class="header-title"><?php echo $lang['Rqred_flds_re_mked_wth_a'];?></h4>
                                </div>
                                <div class="box-body">

                                    <div class="col-lg-6">
                                        <div class="card-box">

                                            <form action="#" data-parsley-validate novalidate method="post" enctype="multipart/form-data">
                                                <div class="form-group">
                                                    <label for="userName"><?php echo $lang['Storage_Name'];?>*</label>
                                                    <input type="text" name="storage" parsley-trigger="change" required placeholder="Enter storage name" class="form-control" id="userName">
                                                </div>

                                                <div class="form-group  m-b-0">
                                                    <button class="btn btn-primary waves-effect waves-light" type="submit" name="createstorage">
                                                        <?php echo $lang['Submit'];?>
                                                    </button>
                                                    <button type="reset" class="btn btn-danger waves-effect waves-light m-l-5">
                                                        
                                                        <?php echo $lang['Cancel'];?>
                                                    </button>
                                                </div>

                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>				
                        </div>

                        <?php
                        if (isset($_POST['createstorage'])) {
                            $storage = filter_input(INPUT_POST, "storage");
                            $storage = mysqli_real_escape_string($db_con, $storage);
                            $str = mysqli_query($db_con, "select * from tbl_storage_level where sl_depth_level=0");
                            if (mysqli_num_rows($str) <= 0) {
                                $create = mysqli_query($db_con, "insert into tbl_storage_level (`sl_id`, `sl_name`, `sl_parent_id`, `sl_depth_level`) values(null,'$storage',null,'0')") or die('Error' . mysqli_error($db_con));
                                $slid= mysqli_insert_id($db_con);
                                if ($create) {
                                    $slPerm= mysqli_query($db_con, "insert into tbl_storagelevel_to_permission(user_id,sl_id) values('1','$slid')");
                                    $sl_id = mysqli_insert_id($db_con);
                                    $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,'$sl_id','Storage Created','$date',null,'$host','')") or die('error : ' . mysqli_error($db_con));
                                    echo'<script>alert("storage created successfully."); window.location.href="index";</script>';
                                    
                                }
                            } else {
                                echo'<script>alert("oops!!! you can create only one root folder.");</script>';
                            }
                             mysqli_close($db_con); 
                        }
                        ?>
                    </div> <!-- container -->

                </div> <!-- content -->

                <?php require_once './application/pages/footer.php'; ?>

            </div>
            <!-- ============================================================== -->
            <!-- End Right content here -->
            <!-- ============================================================== -->


            <!-- Right Sidebar -->
            <?php require_once './application/pages/rightSidebar.php'; ?>
            <!-- /Right-bar -->


        </div>
        <!-- END wrapper -->

        <?php require_once './application/pages/footerForjs.php'; ?>
        <script src="assets/plugins/bootstrap-filestyle/js/bootstrap-filestyle.min.js" type="text/javascript"></script>
        <script src="assets/plugins/select2/js/select2.min.js" type="text/javascript"></script>

        <script type="text/javascript" src="assets/plugins/parsleyjs/parsley.min.js"></script>


        <script type="text/javascript">
            $(document).ready(function () {
                $('form').parsley();

            });
            $(".select2").select2();

            //firstname last name 
            $("input#userName, input#lastName").keypress(function (e) {
                //if the letter is not digit then display error and don't type anything
                if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57) && e.which != 46) {
                    //display error message
                    return true;
                } else {
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
        </script>


    </body>
</html>