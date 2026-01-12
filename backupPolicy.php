<!DOCTYPE html>
<html>
    <?php
    require_once './loginvalidate.php';
    require_once './application/config/database.php';
    require_once './application/pages/head.php'; 
    require_once './cron.php'; 
    
    //for user role
    $chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$_SESSION[cdes_user_id]', user_ids) > 0") or die('Error:' . mysqli_error($db_con));
    $rwgetRole = mysqli_fetch_assoc($chekUsr);

    if ($rwgetRole['db_backup_policy'] != '1') {
        //header('Location: ./index');
    }
    // Get Record from Backup Policy;
    $db_pol_res = mysqli_fetch_assoc(mysqli_query($db_con, "select * from tbl_db_backup_policy"));
    ?>
    <!-- for searchable select-->
    <link href="assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">
    <link href="assets/plugins/multiselect/css/multi-select.css"  rel="stylesheet" type="text/css" />
    <body class="fixed-left">
        <!-- Begin page -->
        <div id="wrapper">
            <?php require_once './application/pages/topBar.php'; ?>
            <?php require_once './application/pages/sidebar.php'; ?>
            <div class="content-page">
                <!-- Start content -->
                <div class="content">
                    <div class="container">
                        <!-- Page-Title -->
                        <div class="row">
                            <div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">
                                <ol class="breadcrumb">
                                    <li>
                                        <a href="#">Administrative Tool</a>
                                    </li>
                                    <li class="active">
                                        <a href="backupPolicy">Backup Policy</a>
                                    </li>
                                </ol>
                            </div>
                        </div>
                        <section class="content">
                            <div class="box box-primary">
                                <div class="panel">
                                    <div class="panel-body">

                                        <?php
                                        $sql = "Select * from tbl_db_backup_policy";
                                        $runsql = mysqli_query($db_con, $sql);
                                        //$rwsql = mysqli_fetch_array($runsql);
                                        $num = mysqli_num_rows($runsql);
                                        //print_r($rwsql);
                                        if ($num < 2) {
                                        ?>
                                            <button class="btn btn-primary m-b-10" data-toggle="modal" data-target="#addpolicy" name="addpolicy">ADD BACKUP POLICY</button>
                                        <?php } ?>


                                        <div id="bkp_pol_tbl" <?php echo($num == 0 ? 'style="display:none;"' : '') ?>>
                                            <table class="table table-striped table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>S. No.</th>
                                                        <th>BackUp Type</th>
                                                        <th>BackUp Frequency</th>
                                                        <th>BackUp Date</th>
                                                        <th>BackUp Day</th>
                                                        <th>BackUp Time</th>
                                                        <th>Last Modified</th>                                                        
                                                        <th>Action</th>                                                        
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $i = 1;
                                                    while ($bkpol_res = mysqli_fetch_assoc($runsql)) {
                                                        ?>
                                                        <tr class="gradeX">                                                            
                                                            <td><?php echo $i . '.'; ?> </td>
                                                            <td><?php echo ucwords($bkpol_res['backup_type']); ?></td>
                                                            <td><?php echo ucwords($bkpol_res['backup_frequency']); ?></td>
                                                            <td><?php echo $bkpol_res['backup_date']; ?></td>
                                                            <td><?php echo $bkpol_res['backup_day']; ?></td>
                                                            <td><?php echo date('h:i:s a', strtotime($bkpol_res['backup_time'])); ?></td>
                                                            <td><?php echo date('d M Y', strtotime($bkpol_res['last_modified'])); ?></td>
                                                            <td class="actions">
                                                                <!--<a href="#" class="on-default edit-row btn btn-primary" data-toggle="modal" data-target="#policyform" id="edit_bkp_policy" title="Modify" data="<?php echo $bkpol_res['backup_type']; ?>" data1="<?= $bkpol_res['id']; ?>"><i class="fa fa-edit"></i></a>-->
                                                                <a href="#" class="on-default remove-row btn btn-danger" data-toggle="modal" data-target="#dialog" id="removeback" title="Delete" data-msg="Are You sure you want to delete this Scheduled Policy ?" data="<?php echo $bkpol_res['id']; ?>"><i class="fa fa-trash-o"></i></a>
                                                            </td>
                                                        </tr>
                                                        <?php
                                                        $i++;
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                            <form method="post">    
                                        <!-- Add Backup Policy Modal Start-->
                                        <div class="modal fade" id="addpolicy" role="dialog">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4>Add Backup Policy</h4>
                                                    </div>
                                                    <div class="modal-body">

                                                        <div class="row form-group">
                                                            <div class="col-md-2 col-lg-2 col-sm-2 col-xs-2">
                                                                <label for="type">Backup Type <span style="color:red;">*</span></label>
                                                            </div>
                                                            <?php
                                                            $chk_sched= mysqli_num_rows(mysqli_query($db_con,"select * from tbl_db_backup_policy where backup_type='Scheduled'"));
                                                            $chk_inc= mysqli_num_rows(mysqli_query($db_con,"select * from tbl_db_backup_policy where backup_type='Incremental'"));
                                                            ?>
                                                            <div class="col-md-6 col-lg-6 col-sm-6 col-xs-6">
                                                                <select class="select2 select2-hidden-accessible" name="backup_type" name="backup_type" data-placeholder="Select Backup Type" aria-hidden="true" required>
                                                                <option value="">Select Backup Type</option>
                                                                <?php if($chk_sched==0){?><option value="Scheduled">Scheduled</option><?php }?>
                                                                <?php if($chk_inc==0){?><option value="Incremental">Incremental</option><?php }?>
                                                                </select> 
                                                            </div>
                                                        </div>

                                                        <div class="row form-group">                                                            
                                                            <div class="col-md-2 col-lg-2 col-sm-2 col-xs-2">
                                                                <label for="frequency">Backup Frequency<span style="color:red;">*</span></label>
                                                            </div>
                                                            <div class="col-md-6 col-lg-6 col-sm-6 col-xs-6">
                                                                <select class="form-control select2 backup-frequency" parsley-trigger="change" value="" name="backup_frequency" id="backup_frequency" required>
                                                                    <option value="">Backup Frequency</option>
                                                                    <option  value="once" <?php echo($bkpol_res['backup_frequency'] == 'once' ? 'selected' : '') ?>>Once</option>
                                                                    <option  value="daily" <?php echo($bkpol_res['backup_frequency'] == 'daily' ? 'selected' : '') ?>>Daily</option>
                                                                    <option  value="weekly" <?php echo($bkpol_res['backup_frequency'] == 'weekly' ? 'selected' : '') ?>>Weekly</option>
                                                                    <option value="monthly" <?php echo($bkpol_res['backup_frequency'] == 'monthly' ? 'selected' : '') ?>>Monthly</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="row form-group dbpol_child" id="once" <?php echo ($bkpol_res['backup_date'] != '0000-00-00' && !empty($bkpol_res['backup_date']) ? '' : 'style="display: none;"') ?>>
                                                            <div class="col-md-2 col-lg-2 col-sm-2 col-xs-2">
                                                                <label for="frequency">Backup Date<span style="color:red;">*</span></label>
                                                            </div>
                                                            <div class="col-md-6 col-lg-6 col-sm-6 col-xs-6">
                                                                <input type="text" placeholder="Backup Date" class="form-control datepicker" name="backup_date" value="<?php echo ($bkpol_res['backup_date'] != '0000-00-00' && !empty($bkpol_res['backup_date']) ? date('d-m-Y', strtotime($bkpol_res['backup_date'])) : ''); ?>" maxlength="40" required>
                                                            </div>
                                                        </div>
                                                        <div class="row form-group dbpol_child" id="weekly" <?php echo (!empty($bkpol_res['backup_day'] && strtolower($bkpol_res['backup_frequency']) == 'weekly') ? '' : 'style="display: none;"') ?>>
                                                            <div class="col-md-2 col-lg-2 col-sm-2 col-xs-2">
                                                                <label for="frequency">Backup Day<span style="color:red;">*</span></label>
                                                            </div>
                                                            <div class="col-md-6 col-lg-6 col-sm-6 col-xs-6">
                                                                <select class="form-control select2" name="backup_days">
                                                                    <option value="">Select Day</option>
                                                                    <option  value="SUN" <?php echo($bkpol_res['backup_day'] == 'SUN' ? 'selected' : '') ?>>Sunday</option>
                                                                    <option value="MON" <?php echo($bkpol_res['backup_day'] == 'MON' ? 'selected' : '') ?>>Monday</option>
                                                                    <option  value="TUE" <?php echo($bkpol_res['backup_day'] == 'TUE' ? 'selected' : '') ?>>Tuesday</option>
                                                                    <option  value="WED" <?php echo($bkpol_res['backup_day'] == 'WED' ? 'selected' : '') ?>>Wednesday</option>
                                                                    <option  value="THU" <?php echo($bkpol_res['backup_day'] == 'THU' ? 'selected' : '') ?>>Thursday</option>
                                                                    <option  value="FRI" <?php echo($bkpol_res['backup_day'] == 'FRI' ? 'selected' : '') ?>>Friday</option>
                                                                    <option  value="SAT" <?php echo($bkpol_res['backup_day'] == 'SAT' ? 'selected' : '') ?>>Saturday</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="row form-group dbpol_child" id="monthly" <?php echo (!empty($bkpol_res['backup_day'] && strtolower($bkpol_res['backup_frequency']) == 'monthly') ? '' : 'style="display: none;"') ?>>
                                                            <div class="col-md-2 col-lg-2 col-sm-2 col-xs-2">
                                                                <label for="frequency">Backup Date<span style="color:red;">*</span></label>
                                                            </div>
                                                            <div class="col-md-6 col-lg-6 col-sm-6 col-xs-6">
                                                                <select class="form-control select2" name="backup_day">
                                                                    <option value="">Backup Date</option>
                                                                    <?php for ($d = 1; $d <= 31; $d++) { ?>
                                                                        <option value="<?php echo $d; ?>" <?php echo($bkpol_res['backup_day'] == $d ? 'selected' : '') ?>><?php echo $d; ?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                        </div>


                                                        <div class="form-group row">
                                                            <div class="col-sm-2">
                                                                <label for="userName">Backup Time<span style="color:red;">*</span></label>
                                                            </div>

                                                            <div class="col-md-6 col-lg-6 col-sm-6 col-xs-6 form-group bootstrap-timepicker">
                                                                <input type="text" class="form-control timepicker" value="<?php echo $bkpol_res['backup_time']; ?>" name="backup_time"  maxlength="40" required>
                                                            </div>

                                                        </div>
                                                    </div>


                                                    <div class="modal-footer">
                                                        <div class="col-sm-8 m-l-m-10">
                                                            <input type="hidden" id="tskid" name="tskid">
                                                            <input type="hidden" id="type" name="type">
                                                            <input type="submit" name="submit" value="Save" class="btn btn-primary" />
                                                            <a href="backupPolicy" class="btn btn-warning" id="cancel_bkp_policy">Close</a>
                                                        </div>
                                                    </div>

                                                </div>

                                            </div>
                                        </div>
                                        <!-- Add Backup Policy Modal END-->
                                            </form>

                                        <!-- Modify Backup Policy Modal Start-->


                                        <div class="modal fade" id="policyform" role="dialog">
                                            <div class="modal-dialog">

                                                <!-- Modal content-->
                                                <div class="modal-content">
                                                    <form action="" method="post" id="bkp_pol_form">
                                                        <div class="modal-header">
                                                            <h4>Create Backup Policy</h4>
                                                        </div>
                                                        <div class="modal-body">

                                                            <div class="row form-group">
                                                                <div class="col-md-2 col-lg-2 col-sm-2 col-xs-2">
                                                                    <label for="type">Backup Type<span style="color:red;">*</span></label>
                                                                </div>
                                                                <div class="col-md-6 col-lg-6 col-sm-6 col-xs-6">
                                                                    <input type="text" class="form-control" parsley-trigger="change" id="backup" name="backup" value="" readonly="">
                                                                </div>
                                                            </div>

                                                            <div class="row form-group">                                                            
                                                                <div class="col-md-2 col-lg-2 col-sm-2 col-xs-2">
                                                                    <label for="frequency">Backup Frequency<span style="color:red;">*</span></label>
                                                                </div>
                                                                <div class="col-md-6 col-lg-6 col-sm-6 col-xs-6">
                                                                    <select class="form-control select2 backup-frequency"  name="backup_frequency" id="backup_frequency" required>
                                                                        <option value="">Backup Frequency</option>
                                                                        <option  value="once" <?php echo($bkpol_res['backup_frequency'] == 'once' ? 'selected' : '') ?>>Once</option>
                                                                        <option  value="daily" <?php echo($bkpol_res['backup_frequency'] == 'daily' ? 'selected' : '') ?>>Daily</option>
                                                                        <option  value="weekly" <?php echo($bkpol_res['backup_frequency'] == 'weekly' ? 'selected' : '') ?>>Weekly</option>
                                                                        <option value="monthly" <?php echo($bkpol_res['backup_frequency'] == 'monthly' ? 'selected' : '') ?>>Monthly</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="row form-group dbpol_child" id="once_edit" <?php echo ($bkpol_res['backup_date'] != '0000-00-00' && !empty($bkpol_res['backup_date']) ? '' : 'style="display: none;"') ?>>
                                                                <div class="col-md-2 col-lg-2 col-sm-2 col-xs-2">
                                                                    <label for="frequency">Backup Date<span style="color:red;">*</span></label>
                                                                </div>
                                                                <div class="col-md-6 col-lg-6 col-sm-6 col-xs-6">
                                                                    <input type="text" placeholder="Backup Date" class="form-control datepicker" name="backup_date" value="<?php echo ($bkpol_res['backup_date'] != '0000-00-00' && !empty($bkpol_res['backup_date']) ? date('d-m-Y', strtotime($bkpol_res['backup_date'])) : ''); ?>" maxlength="40" required>
                                                                </div>
                                                            </div>
                                                            <div class="row form-group dbpol_child" id="weekly_edit" <?php echo (!empty($bkpol_res['backup_day'] && strtolower($bkpol_res['backup_frequency']) == 'weekly') ? '' : 'style="display: none;"') ?>>
                                                                <div class="col-md-2 col-lg-2 col-sm-2 col-xs-2">
                                                                    <label for="frequency">Backup Day<span style="color:red;">*</span></label>
                                                                </div>
                                                                <div class="col-md-6 col-lg-6 col-sm-6 col-xs-6">
                                                                    <select class="form-control select2" name="backup_days">
                                                                        <option value="">Select Day</option>
                                                                        <option  value="SUN" <?php echo($bkpol_res['backup_day'] == 'SUN' ? 'selected' : '') ?>>Sunday</option>
                                                                        <option value="MON" <?php echo($bkpol_res['backup_day'] == 'MON' ? 'selected' : '') ?>>Monday</option>
                                                                        <option  value="TUE" <?php echo($bkpol_res['backup_day'] == 'TUE' ? 'selected' : '') ?>>Tuesday</option>
                                                                        <option  value="WED" <?php echo($bkpol_res['backup_day'] == 'WED' ? 'selected' : '') ?>>Wednesday</option>
                                                                        <option  value="THU" <?php echo($bkpol_res['backup_day'] == 'THU' ? 'selected' : '') ?>>Thursday</option>
                                                                        <option  value="FRI" <?php echo($bkpol_res['backup_day'] == 'FRI' ? 'selected' : '') ?>>Friday</option>
                                                                        <option  value="SAT" <?php echo($bkpol_res['backup_day'] == 'SAT' ? 'selected' : '') ?>>Saturday</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="row form-group dbpol_child" id="monthly_edit" <?php echo (!empty($bkpol_res['backup_day'] && strtolower($bkpol_res['backup_frequency']) == 'monthly') ? '' : 'style="display: none;"') ?>>
                                                                <div class="col-md-2 col-lg-2 col-sm-2 col-xs-2">
                                                                    <label for="frequency">Backup Date<span style="color:red;">*</span></label>
                                                                </div>
                                                                <div class="col-md-6 col-lg-6 col-sm-6 col-xs-6">
                                                                    <select class="form-control select2" name="backup_day">
                                                                        <option value="">Backup Date</option>
                                                                        <?php for ($d = 1; $d <= 31; $d++) { ?>
                                                                            <option value="<?php echo $d; ?>" <?php echo($bkpol_res['backup_day'] == $d ? 'selected' : '') ?>><?php echo $d; ?></option>
                                                                        <?php } ?>
                                                                    </select>
                                                                </div>
                                                            </div>


                                                            <div class="form-group row">
                                                                <div class="col-sm-2">
                                                                    <label for="userName">Backup Time<span style="color:red;">*</span></label>
                                                                </div>

                                                                <div class="col-md-6 col-lg-6 col-sm-6 col-xs-6 form-group bootstrap-timepicker">
                                                                    <input type="text" class="form-control timepicker" value="<?php echo $bkpol_res['backup_time']; ?>" name="backup_time"  maxlength="40" required>
                                                                </div>

                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <div class="col-sm-8 m-l-m-10">
                                                                <input type="hidden" id="tskid" name="tskid">
                                                                <input type="hidden" id="type" name="type">
                                                                <input type="submit" name="submit" value="Save" class="btn btn-primary" />
                                                                <a href="backupPolicy" class="btn btn-warning" id="cancel_bkp_policy">Close</a>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Modify Backup Policy Modal END-->
                                    </div>
                                </div>
                            </div>

                            <div class="box box-primary">                          

                                <div class="box box-primary">
                                    <div class="panel">

                                        <table class="table table-striped table-bordered">
                                            <thead>
                                                <tr>
                                                    <th  colspan="3" style="text-align: center"><strong>OTHER DATABASE BACKUP OPTIONS</strong></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td style="text-align: center"><a class="btn btn-primary" href="backupandrestore">COMPLETE DATABASE BACKUP</a></td>
                                                    <td style="text-align: center"><button class="btn btn-primary" data-toggle="modal" data-target="#tablewise" name="tablewise">TABLE WISE DATABASE BACKUP</button></td>
                                                    <td style="text-align: center"><a href="incBackup" class="btn btn-primary">INCREMENTAL BACKUP LOG</a></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <div class="modal fade" id="tablewise" role="dialog">
                                            <div class="modal-dialog">
                                                <!-- Modal content-->
                                                <div class="modal-content">
                                                    <form action="dbback_up" method="post">
                                                        <div class="modal-body">
                                                        <div class="form-group row" id="multiselect">
                                                            <div>
                                                                <h4>Select Tables to Backup</h4>
                                                            </div>
                                                            <div class="col-md-4 shiv">
                                                                <span><strong>Select Tables:</strong></span><label style="margin-right: -162px !important"><strong>Selected Tables: </strong></label>
                                                                
                                                                <select multiple="multiple" class="multi-select" id="my_multi_select1" name="table[]" data-plugin="multiselect">
                                                                    <?php
                                                                    $query = "Show Tables where Tables_in_".$dbName." not like '%work%' and Tables_in_".$dbName." not like '%wf%' and Tables_in_".$dbName." not like '%step%' and Tables_in_".$dbName." not like '%task%' and Tables_in_".$dbName." not like '%form%' and Tables_in_".$dbName." not like '%anotation%'";
                                                                    //echo $query;
                                                                   //die;
                                                                    $showTables = mysqli_query($db_con, $query);
                                                                    // print_r($showTables);
                                                                    echo mysqli_num_rows($showTables);


                                                                    if (mysqli_num_rows($showTables) > 0) {
                                                                        while ($rwgetTables = mysqli_fetch_assoc($showTables)) {

                                                                            echo '<option value="' . $rwgetTables['Tables_in_'.$dbName] . '" >' . $rwgetTables['Tables_in_'.$dbName] . '</option>';
                                                                        }
                                                                    }
                                                                    ?>
                                                                </select>
                                                                
                                                            </div>
                                                        </div>
                                                        </div>
                                                         
                                                        <div class="modal-footer">
                                                        <a href="#" id="reset_tbl_wise_bkp" class="btn btn-warning"><i class="fa fa-refresh"></i> <strong>Reset</strong></a>
                                                        <button class="btn btn-primary waves-effect waves-light" type="submit" >Submit</button>
                                                        <button class="btn btn-danger" data-dismiss="modal">Close</button>
                                                        </div>
                                                   </form>
                                                </div>

                                            </div>
                                        </div>



                                    </div>
                                    <!-- end: page -->
                                </div> <!-- end Panel -->
                            </div>

                        </section>
                    </div>
                </div>
            </div>
        </div>
        <div  style="display:none; text-align: center; color: #fff;  background: rgba(0,0,0,0.5); width: 100%; height: 100%; z-index: 2000; position: fixed; top:0;" id="wait">
            <img src="assets/images/load1.gif" alt="load"  style="margin-top: 250px; width: 100px;" />
        </div>
        <!--form here end-->
        <!-- MODAL -->
        <div id="dialog" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog"> 
                <div class="panel panel-color panel-danger"> 
                    <div class="panel-heading"> 
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                        <label><h2 class="panel-title">Are You sure.</h2></label> 
                    </div> 
                    <form method="post">
                        <div class="panel-body">
                            <p style="color: red;" id="del_msg"> Are You sure you want to Delete This Policy ?</p>
                        </div>
                        <div class="modal-footer">
                            <div class="col-md-12 text-right">
                                <input type="hidden" id="bid" name="bid">
                                <button type="submit" name="delete_sch_task" value="delete_sch_task" id="dialogConfirm" class="btn btn-danger waves-effect waves-light"><i class="fa fa-trash-o"></i> Delete</button>
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button> 
                            </div>
                        </div>
                    </form>
                </div> 
            </div>
        </div>
        <!-- end Modal -->
        <?php require_once './application/pages/footer.php'; ?>
        <!-- Right Sidebar -->
        <?php //require_once './application/pages/rightSidebar.php';               ?>
        <!-- /Right-bar -->
        <?php require_once './application/pages/footerForjs.php'; ?>
        <!-- for searchable select-->
        <script src="assets/plugins/select2/js/select2.min.js" type="text/javascript"></script>
        <script src="assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
        <script src="assets/plugins/timepicker/bootstrap-timepicker.js"></script>
        <script type="text/javascript" src="assets/plugins/multiselect/js/jquery.multi-select.js"></script>
        <script src="assets/js/jquery.core.js"></script>
        <!-- for searchable select-->
        <script type="text/javascript">
            $(document).ready(function () {
                $(".select2").select2();
            });
        </script>
        <script>
            // Date Picker
            $('.timepicker').timepicker({
                defaultTIme: false,
                minuteStep: 1,
                //showMeridian:false
            });
            // Date Picker
            $('.datepicker').datepicker({
                autoclose: true,
                todayHighlight: true,
                startDate: '-0m'
            });

            //sk@240119 : Backup policy change event.
            $(document).ready(function (e) {
                $(".backup-frequency").change(function (e) {
                    // hide all Subchild.
                    $(".dbpol_child").hide();
                    // Remove required from all subchild.
                    $(".dbpol_child select,input").prop('required', false);
                    var btype = $(this).val();
                    alert(btype);
                    if ($("#backup").val() != '') {
                        $("#" + btype + '_edit').fadeIn(200);
                    } else {
                        $("#" + btype).fadeIn(200);
                    }

                    // add required to corresponding subchild
                    $("#" + btype + " select").prop('required', true);
                    $("#" + btype + " input").prop('required', true);
                });

                //sk@280119 : Show Policy form for modification
                $("a#edit_bkp_policy").click(function (e) {
                    $("#bkp_pol_form").slideDown();
                    var id = $(this).attr("data1");
                    var type = $(this).attr("data");
                    //alert(id, type);
                    $("#tskid").val(id);
                    $("#backup").val(type);
                });
                $("a#removeback").click(function () {
                    var id = $(this).attr("data");
                    $("#bid").val(id);
                });
                // : cancel Policy
                $("#cancel_bkp_policy").click(function (e) {
                    $("#bkp_pol_form").slideUp();
                });
                
                //sk@20419 : Reset Table wise backup.
                $("#reset_tbl_wise_bkp").click(function(e){
                    $(".ms-selected").click();
                })
            });
        </script>
    </body>
</html>
<?php
class scheduleTask extends Crontab{
    public function getDateComponent($input_date){
       $day_month=date('d',strtotime($input_date));
       $month=date('m',strtotime($input_date));
       $year=date('Y',strtotime($input_date));
       return array("day_month"=>$day_month,"month"=>$month,"year"=>$year);
   }
   public function getTimeComponent($input_time){
       $hour=date('H',strtotime($input_time));
       $minute=date('i',strtotime($input_time));
       return array("minute"=>$minute,"hour"=>$hour);
   }
    
   public function setOnce($input_date,$input_time){
       $date_component=$this->getDateComponent($input_date);
       $time_component=$this->getTimeComponent($input_time);
       $cmd="$time_component[minute] $time_component[hour] $date_component[day_month] $date_component[month] *";
       return $cmd;
   }
   public function setDaily($input_time){
       $time_component=$this->getTimeComponent($input_time);
       $cmd="$time_component[minute] $time_component[hour] * * *";
       return $cmd;
   }
   public function setWeekly($input_day,$input_time){
       $time_component=$this->getTimeComponent($input_time);
       $cmd="$time_component[minute] $time_component[hour] * * $input_day";
       return $cmd;
   }
   public function setMonthly($input_day,$input_time){
       $time_component=$this->getTimeComponent($input_time);
       $cmd="$time_component[minute] $time_component[hour] $input_day * *";
       return $cmd;
   }
    
    

    function taskScheduler($db_con, $task_name, $script, $system, $user, $password, $action) {
        $backup_frequency = strtolower(mysqli_real_escape_string($db_con, $_POST['backup_frequency']));
        
        if ($backup_frequency == 'once') {
            $backup_time = mysqli_real_escape_string($db_con, $_POST['backup_time']);
            $backup_date = mysqli_real_escape_string($db_con, $_POST['backup_date']);
            //$cmd = $this->setOnce($task_name, $script, $backup_date, $backup_time, $system, $user, $password);
            $cmd = $this->setOnce($backup_date,$backup_time);
        } elseif ($backup_frequency == 'daily') {
            $backup_time = mysqli_real_escape_string($db_con, $_POST['backup_time']);
            //$cmd = $this->setDaily($task_name, $script, $backup_time, $system, $user, $password);
            $cmd = $this->setDaily($backup_time);
        } elseif ($backup_frequency == 'weekly') {
            $backup_day = mysqli_real_escape_string($db_con, $_POST['backup_days']);
            $backup_time = mysqli_real_escape_string($db_con, $_POST['backup_time']);
            //$cmd = $this->setWeekly($task_name, $script, $backup_day, $system, $user, $password);
             $cmd = $this->setWeekly($backup_day,$backup_time);
        } elseif ($backup_frequency == 'monthly') {
            $backup_day = mysqli_real_escape_string($db_con, $_POST['backup_day']);
            $backup_time = mysqli_real_escape_string($db_con, $_POST['backup_time']);
            //$cmd = $this->setMonthly($task_name, $script, $backup_day, $backup_time, $system, $user, $password);
            $cmd = $this->setMonthly($backup_day,$backup_time);
        } elseif ($action == 'delete') {
            $del_id = mysqli_real_escape_string($db_con, $_POST['bid']);
           // echo "select * from tbl_db_backup_policy where id='$del_id'";
           // die;
            $res= mysqli_fetch_array(mysqli_query($db_con, "select * from tbl_db_backup_policy where id='$del_id'"));
            $cmd=$res['cmd'];
            //$cmd = $this->delSchTask($task_name, $system, $user, $password);
        }
       // echo $cmd;
        //die($cmd);
        
        // Execute Command
        //$resp = exec("$cmd");
        // check for delete,add,edit case.
        if($action=='delete'){
           $resp= $this->removeJob($cmd);
        }else{
          // $resp= $this->addJob($cmd.' wget -O /dev/null '.$script.' > /dev/null 2>&1'); 
            $resp=0;
        }
        //echo $resp; die;
        //var_dump($out);
        //echo $cmd;
        if ($resp == 0) {
            echo "Success";
            $res_arr = array('backup_frequency' => $backup_frequency, 'backup_date' => ($backup_date ? date('Y-m-d', strtotime($backup_date)) : '0000-00-00'), 'backup_day' => $backup_day, 'backup_time' => date("H:i:s", strtotime($backup_time)), 'action' => $action, 'resp' => $resp,'cmd'=>$cmd);
            return $res_arr;
        } else {
            echo "Failed";
        }
    }

    
    function delSchTask($task_name) {
        // time in 24 hour format
        $backup_time = date("H:i:s", strtotime($backup_time));
        $cmd = 'SCHTASKS /Delete " /TN "' . $task_name . '" /F';

        //return command
        return $cmd;
    }

    function dbBkpPolicyLog($db_con, $log) {
        $date = date("Y-m-d H:i:s");
        // convert log in json format   
        $log = json_encode(array_filter($log));
        $sql = "insert into tbl_db_backup_policy_log set
                                         user_id='$_SESSION[cdes_user_id]',
                                         log='$log'    
                                         ";
        //echo $sql;
        //die;
        $query = mysqli_query($db_con, $sql);
        $status = ($query ? TRUE : FALSE);
        return $status;
    }

    function setDbRecord($db_con, $para_array, $lang, $type) {
        $date = date("Y-m-d H:i:s");
        //message show in sweet alert.  
        $sucess_msg = "Task Scheduler Created Successfully.";
        $error_msg = "Task Scheduler Creation Failed.";
        if ($para_array['action'] == 'create') {
            $sql = "insert into tbl_db_backup_policy set
                                         backup_type='$type',
                                         backup_frequency='$para_array[backup_frequency]',
                                         backup_time='$para_array[backup_time]',
                                         backup_day='$para_array[backup_day]',
                                         backup_date='$para_array[backup_date]',
                                         last_modified='$date',
                                         cmd='$para_array[cmd]',
                                         tstp='$date'    
                                        ";
        } elseif ($para_array['action'] == 'change') {
            $sql = "update tbl_db_backup_policy set
                                         backup_frequency='$para_array[backup_frequency]',
                                         backup_time='$para_array[backup_time]',
                                         backup_day='$para_array[backup_day]',
                                         backup_date='$para_array[backup_date]',
                                         cmd='$para_array[cmd]',    
                                         last_modified='$date' where backup_type='$type'     
                                         ";
        } elseif ($para_array['action'] == 'delete') {
            $sql = "delete from tbl_db_backup_policy where id='$_POST[bid]'";
            //message show in sweet alert.  
            $sucess_msg = "Task Scheduler Deleted Successfully";
            $error_msg = "Task Scheduler Deletion Failed";
        }
        //echo $sql;
        //die;
        $query = mysqli_query($db_con, $sql);
        if ($query) {
            $log_status = $this->dbBkpPolicyLog($db_con, $para_array);
            if ($log_status) {
                echo '<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '","' . $sucess_msg . '");</script>';
            } else {
                echo'<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['log_generation_failed'] . '!");</script>';
            }
        } else {
            echo'<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '","' . $error_msg . '!");</script>';
        }
    }

}





// sk@250119 : Create and update Task in Task Scheduler;
if ($_POST['submit']) {
    $type = $_POST['backup_type'];
    
    $task_name = "";
    if ($type == "Scheduled") {
        $task_name = "SCH";
        //$script = "C:/PHP/php-cgi.exe C:/Apache24/htdocs/ezeefile_bor/trunk/backupandrestore.php";
        $script = "http://192.168.2.104/backupandrestore.php";
    } else {
        $task_name = "INC";
       // $script = "C:/PHP/php-cgi.exe C:/Apache24/htdocs/ezeefile_bor/trunk/dbIncBkpCron.php";
        $script = "http://192.168.2.104/dbIncBkpCron.php";
    }
    //die;

    $action_res = mysqli_fetch_assoc(mysqli_query($db_con, "select count(*) as num from tbl_db_backup_policy where backup_type='$type'"));
    $action = ($action_res['num'] > 0 ? 'change' : 'create');

    ///////////////////
    $tskschd = new scheduleTask();
    /////////////////
    
    $res = $tskschd->taskScheduler($db_con, $task_name, $script, system, user, password, $action);

   // print_r($res);
    //die();
    if ($res['resp'] == 0) {
        $tskschd->setDbRecord($db_con, $res, $lang, $type);
    }
}

// sk@280119 : Delete task from task scheduler.
if ($_POST['delete_sch_task']) {
    $action = 'delete';
    $tskschd = new scheduleTask();
    $res = $tskschd->taskScheduler($db_con, task_name, script, system, user, password, $action);
    if ($res['resp'] == 0) {
        $tskschd->setDbRecord($db_con, $res, $lang,$action);
    }
}
?>
