<!DOCTYPE html>
<html>
    <?php
    require_once './loginvalidate.php';
    require_once './application/pages/head.php';
    if ($rwgetRole['edit_relog'] != '1') {
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
            <!-- ========== Left Sidebar Start ========== 1001/10556/00959 12/12/2011 14:33:58-->
            <?php require_once './application/pages/sidebar.php'; ?>
            <!-- Left Sidebar End --> 
            <!-- ============================================================== -->
            <!-- Start right Content here -->
            <!-- ============================================================== -->              
            
            <?php
                $sql = "SELECT * FROM `tbl_set_timeout_value` WHERE `id` = 1";
                $query = mysqli_query($db_con, $sql);
                $result = mysqli_fetch_assoc($query);
            ?>

            <div class="content-page">
                <!-- Start content -->
                <div class="content">
                    <div class="container">
                        <!-- Page-Title -->
                        <div class="row">
                            <ol class="breadcrumb">
                                <li><a href="#"><?php echo $lang['Masters']; ?></a></li>
                                <li class="active"><?php echo $lang['set_relog_time']; ?></li>
                                <li> <a href="#" data-toggle="modal" data-target="#help-modal" id="helpview" data="5" title="<?= $lang['help']; ?>"><i class="fa fa-question-circle"></i> </a></li>
                                <a href="javascript:void(0)" class="btn btn-primary waves-effect waves-light pull-right btn-sm margin-t-9" onclick="goPrevious();" title="<?php echo $lang['Go_to_previous_page']; ?>"><i class="fa fa-arrow-circle-left"></i></a>
                            </ol>
                        </div>
                        <div class="row">
                            <div class="box box-primary">
                                <div class="box-header with-border">
                                    <h4 class="header-title col-md-6"><?php echo $lang['edit_Timeout']; ?></h4>
                                </div>
                                <div class="box-body">
                                    <div class="col-sm-6">
                                        <div class="card-box">
                                            <div class="row">
                                                <form method="post">
                                                    <div class="form-group" id="notrange">
                                                        <label><?php echo $lang['enter_timeout_value']; ?> <span class="text-alert">*</span></label>
                                                        <input type="number" class="form-control" name="set-timeout-val" id="textbox" value="<?=$result['timeout_value']?>" placeholder="<?php echo $lang['enter_value_mins']; ?>">
                                                    </div>
                                                            
                                                    <div class="row pull-right">
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <button class="btn btn-primary waves-effect waves-light" id="addrange" type="submit" name="addField"><?php echo $lang['Submit']; ?></button>
                                                                <a href="addFields" class="btn btn-warning waves-effect waves-light m-l-5"><?= $lang['Reset']; ?> </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div> 
                                    </div>
                                </div>				
                            </div>
                        </div> <!-- container -->
                    </div> <!-- content -->
                    <?php require_once './application/pages/footer.php'; ?>
                </div>
            </div>
            <!-- END wrapper -->
            <?php require_once './application/pages/footerForjs.php'; ?>

            <script src="assets/plugins/select2/js/select2.min.js" type="text/javascript"></script>
            <script type="text/javascript" src="assets/plugins/parsleyjs/parsley.min.js"></script>
            

            <?php
                
                if (isset($_POST['set-timeout-val'])) 
                {
                    $type = $_POST['set-timeout-val'];

                    $sql = "SELECT * FROM `tbl_set_timeout_value` WHERE `id` = 1";
                    $query = mysqli_query($db_con, $sql);
                    $result = mysqli_fetch_assoc($query);

                    if(!empty($result)) {
                        $sql = "UPDATE `tbl_set_timeout_value` SET `timeout_value` = '".$type."' WHERE `tbl_set_timeout_value`.`id` = 1";
                    } else {
                        $sql = "INSERT INTO `tbl_set_timeout_value` (`timeout_value`) VALUES ('".$type."');";
                    }
                    $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`, `action_name`, `start_date`, `system_ip`, `remarks`) values ('$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]','Re-Login Time Edited','$date','$host','Re-login Time Modified')");

                    $outcome = mysqli_query($db_con, $sql) or die('Error set timeout value!' . mysqli_error($db_con));
                    
                    if($outcome)
                    {
                        // echo'<script>taskSuccess("addFields","' . $lang['time_updated_success']. '");</script>';
                        // added on 28.01.2022
                        echo '<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '", "' . $lang['time_updated_success'] . '");</script>';
                    } 
                    else 
                    {
                        // echo'<script>taskFailed("addFields","' . $lang['time_updated_failed']. '");</script>';
                        // added on 28.01.2022
                        echo '<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '", "' . $lang['time_updated_failed'] . '");</script>';
                    }
                    mysqli_close($db_con);
                }
            ?>
        
    </body>
</html>