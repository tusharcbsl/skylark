<!DOCTYPE html>
<html>

    <?php
    error_reporting(0);
    //die(gethostname());
    //ini_set('display_errors', 1);
    require_once './loginvalidate.php';
    require_once './application/pages/head.php';

    //for user role
    $chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$_SESSION[cdes_user_id]', user_ids) > 0") or die('Error:' . mysqli_error($db_con));

    $rwgetRole = mysqli_fetch_assoc($chekUsr);
    ?>

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
            <?php
            $perm = mysqli_query($db_con, "select sl_id from tbl_storagelevel_to_permission where user_id='$_SESSION[cdes_user_id]' group by sl_id");
            $rwPerm = mysqli_fetch_assoc($perm);
            $slperm = $rwPerm['sl_id'];

            //echo $marray = findTotalFile($slperm);

            function findTotalFile($slperm) {
                global $list;
                $list = array();
                global $db_con;
                global $numFile;
                global $totalFSize;
                global $totalFolder;

                $contFile = mysqli_query($db_con, "select sum(doc_size) as total, count(doc_name) as count from tbl_document_master where FIND_IN_SET('$slperm',doc_name)") or die('Error :' . mysqli_error($db_con));
                $rwcontFile = mysqli_fetch_assoc($contFile);
                $totalFSize1 = $rwcontFile['total'];
                $totalFSize += round($totalFSize1 / (1024 * 1024), 2);
                $numFile += $rwcontFile['count'];
                $list["files"] = $numFile;
                $list["fileSize"] = $totalFSize;
                if (!empty($slperm)) {
                    $totalFolder += 1;
                }
                $list["totalFolder"] = $totalFolder;

                $sql_child = "select * FROM tbl_storage_level WHERE sl_parent_id = '$slperm' ";
                $sql_child_run = mysqli_query($db_con, $sql_child) or die('Error: ' . mysqli_error($db_con));
                if (mysqli_num_rows($sql_child_run) > 0) {

                    while ($rwchild = mysqli_fetch_assoc($sql_child_run)) {

                        $child = $rwchild['sl_id'];
                        $clagain = findTotalFile($child);
                    }
                }
                return $list;
            }

            $totalFiles = findTotalFile($slperm);
            ?>
            <div class="content-page">
                <!-- Start content -->
                <div class="content">
                    <div class="container">

                        <!-- Page-Title -->
                        <div class="row">
                            <div class="col-sm-12">
                                <?php
                                $lastLogin = mysqli_query($db_con, "select last_active_logout from tbl_user_master where user_id = '$_SESSION[cdes_user_id]'") or die('Error :' . mysqli_error($db_con));
                                $rwLastLogin = mysqli_fetch_assoc($lastLogin);
                                ?>
                                <div class="btn-group pull-right m-t-15">
                                    <button type="button" class="btn btn-default"><i class="fa fa-clock-o"></i> <strong>Last Login : </strong> <?php
                                        if (!empty($rwLastLogin['last_active_logout'])) {
                                            echo $rwLastLogin['last_active_logout'];
                                        } else {
                                            echo'00:00:00';
                                        }
                                        ?> </button>
                                </div>
                                <h4 class="page-title">Dashboard</h4>
                                <p class="text-muted page-title-alt">Welcome to <?php echo $projectName; ?>  Dashboard !</p>
                            </div>
                        </div>
                        <div class="row">
                            <?php
                            //for user role
                            if ($rwgetRole['dashboard_mydms'] == '1') {
                                ?>
                                <div class="col-md-6 col-lg-3">
                                    <?php
                                    $perm = mysqli_query($db_con, "select * from tbl_storagelevel_to_permission where user_id='$_SESSION[cdes_user_id]' group by sl_id");
                                    $rwPerm = mysqli_fetch_assoc($perm);
                                    $slperm = $rwPerm['sl_id'];
                                    ?>
                                    <div class="widget-bg-color-icon card-box">
                                        <div class="bg-icon bg-icon-custom pull-left">

                                            <a href="<?php
                                            if (mysqli_num_rows($perm) > 0) {
                                                echo 'storage?id=' . urlencode(base64_encode($slperm));
                                            }
                                            ?>">
                                                <i class="fa fa-dropbox text-custom"></i>
                                            </a>
                                        </div>
                                        <div class="text-center">
                                            <h6 class="text-dark"><strong>MY DMS</strong></h6>
                                            <p class="text-muted">Explore Your DMS</p>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>

                                </div>
                            <?php } ?>
                            <?php if ($rwgetRole['num_of_file'] == '1') { ?>
                                <div class="col-md-6 col-lg-3">
                                    <div class="widget-bg-color-icon card-box">
                                        <div class="bg-icon bg-icon-custom pull-left">
                                            <i class="fa fa-copy text-custom"></i>
                                        </div>
                                        <div class="text-center">
                                            <h6 class="text-dark"><strong>NO. OF FILES</strong></h6>
                                            <h4 class="text-primary text-center"><span data-plugin="counterup"><?php echo $totalFiles["files"]; ?></span></h4>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>        
                                </div> 
                            <?php } ?>
                            <?php if ($rwgetRole['num_of_folder'] == '1') { ?>
                                <div class="col-md-6 col-lg-3">
                                    <div class="widget-bg-color-icon card-box">
                                        <div class="bg-icon bg-icon-custom pull-left">
                                            <i class="fa fa-folder-o text-custom"></i>
                                        </div>
                                        <div class="text-center">
                                            <h6 class="text-dark"><strong>NO. OF FOLDERS</strong></h6>
                                            <h4 class="text-primary text-center"><span data-plugin="counterup"><?php echo $totalFiles["totalFolder"]; ?></span></h4>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>        
                                </div>           
                            <?php } ?>
                            <?php if ($rwgetRole['memory_used'] == '1') { ?>
                                <div class="col-md-6 col-lg-3">
                                    <div class="widget-bg-color-icon card-box" style="max-height: 120px;">
                                        <div class="pull-left" style="margin-top: -10px;">
                                            <div data-label="30%" class="radial-bar radial-bar-30 radial-bar-lg radial-bar-success"></div>
                                            <!--                                                <div data-label="30%" class="radial-bar radial-bar-30 radial-bar-lg radial-bar-warning"></div>
                                                                                            <div data-label="30%" class="radial-bar radial-bar-30 radial-bar-lg radial-bar-danger"></div>-->
                                        </div>

                                        <div class="text-center">
                                            <h6 class="text-dark"><strong>MEMORY USED</strong></h6>
                                            <h4 class="text-primary text-center "><span data-plugin="counterup"><?php echo (($totalFiles['fileSize'] > 999) ? round($totalFiles['fileSize'] / 1024, 2) : $totalFiles['fileSize']); ?></span> <?php echo(($totalFiles['fileSize'] > 999) ? 'GB' : 'MB'); ?> </h4>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>        
                                </div>       


                            <?php } ?>
                            <?php if ($rwgetRole['num_of_folder'] == '1') { ?>
                                <div class="col-md-6 col-lg-4">
                                    <div class="widget-bg-color-icon card-box">
                                        <div class="bg-icon bg-icon-custom pull-left">
                                            <i class="fa fa-folder-o text-custom"></i>
                                        </div>
                                        <div class="text-center">
                                            <h6 class="text-dark"><strong>Storage Management</strong></h6>
                                            <h4 class="text-primary text-center"><span data-plugin="counterup"><?php echo $totalFiles["totalFolder"]; ?></span></h4>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>        
                                </div>           
                            <?php } ?>
                            <?php if ($rwgetRole['num_of_folder'] == '1') { ?>
                                <div class="col-md-6 col-lg-4">
                                    <div class="widget-bg-color-icon card-box">
                                        <div class="bg-icon bg-icon-custom pull-left">
                                            <i class="fa fa-folder-o text-custom"></i>
                                        </div>
                                        <div class="text-center">
                                            <h6 class="text-dark"><strong>User Management</strong></h6>
                                            <h4 class="text-primary text-center"><span data-plugin="counterup"><?php echo $totalFiles["totalFolder"]; ?></span></h4>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>        
                                </div>           
                            <?php } ?>

                        </div>
                    </div>
                    <?php if ($rwgetRole['dashboard_mytask'] == '1') { ?>
                        <!--                                <div class="col-md-6 col-lg-4">
                                                            <div class="widget-bg-color-icon card-box">
                                                                <div class="bg-icon bg-icon-custom pull-left">
                                                                    <a href="myTask">
                                                                        <i class="md md-storage text-custom"></i>
                                                                    </a>
                                                                </div>
                        <?php
                        $where = "";
                        //require_once 'application/pages/where.php';
                        //echo $where;    
                        if ($_SESSION['cdes_user_id'] == 1) {
                            $constructs = "SELECT count(id) as count FROM tbl_task_master tsm inner join tbl_doc_assigned_wf tdawf on tsm.task_id=tdawf.task_id where tdawf.task_status='Pending'";
                        } else {
                            $constructs = "SELECT count(id) as count FROM tbl_task_master tsm inner join tbl_doc_assigned_wf tdawf on tsm.task_id=tdawf.task_id where ((tsm.assign_user='$_SESSION[cdes_user_id]' and tdawf.NextTask='0') or (alternate_user='$_SESSION[cdes_user_id]' and tdawf.NextTask= '3') or (supervisor='$_SESSION[cdes_user_id]' and tdawf.NextTask= '4'))";
                        }
                        //echo $constructs;
                        $run = mysqli_query($db_con, $constructs) or die('Error' . mysqli_error($db_con));
                        $rwRun = mysqli_fetch_assoc($run);
                        $foundnum = $rwRun['count'];
                        ?>
                                                                <div class="text-center">
                                                                    <h4 class="text-dark"><strong>IN TRAY (<?php echo $foundnum; ?>)</strong></h4>
                                                                    <p class="text-muted">View Your Tray</p>
                                                                </div>
                                                                <div class="clearfix"></div>
                                                            </div>
                                                        </div>-->
                    <?php } ?>


                    <?php if ($rwgetRole['dashboard_edit_profile'] == '1') { ?>
                        <!--                                <div class="col-md-6 col-lg-4">
                                                            <div class="widget-bg-color-icon card-box">
                                                                <div class="bg-icon bg-icon-custom pull-left">
                        
                                                                    <a href="profile"  >
                                                                        <i class="fa fa-edit text-custom"></i>
                                                                    </a>
                        
                                                                </div>
                                                                <div class="text-center">
                                                                    <h4 class="text-dark text-custom"><strong>EDIT PROFILE</strong></h4>
                                                                    <p class="text-muted">Update Your Profile</p>
                                                                </div>
                                                                <div class="clearfix"></div>
                                                            </div>
                                                        </div>-->
                    <?php } ?>
                    <!--
                    <?php //if ($rwgetRole['dashboard_query'] == '1') {     ?>
                          <div class="col-md-6 col-lg-3">
                              <div class="widget-bg-color-icon card-box">
                                  <div class="bg-icon bg-icon-custom pull-left">
                                      <a href="#">
                                          <i class="md md-question-answer text-custom"></i>
                                      </a>
                                  </div>
                                  <div class="text-right">
                                      <h4 class="text-dark"><strong>QUERIES</strong></h4>
                                      <p class="text-muted">Frequently Search...</p>
                                  </div>
                                  <div class="clearfix"></div>
                              </div>
                          </div>
                    <?php //}  ?>-->


                    <div class="row">



                        <div class="col-lg-6">
                            <div class="card-box">
                                <h4 class="text-dark header-title m-t-0 m-b-30">Dms Report</h4>
                                <div class="widget-chart text-center">
                                    <div id="sparkline3"></div>
                                    <ul class="list-inline m-t-15">
                                        <li>
                                            <h6 class="text-muted m-t-20">NO OF FOLDER</h6>
                                            <h4 class="m-b-0"><?php echo $totalFiles["totalFolder"]; ?></h4>
                                        </li>
                                        <li>
                                            <h6 class="text-muted m-t-20">NO OF FILE</h6>
                                            <h4 class="m-b-0"><?php echo $totalFiles["files"]; ?></h4>
                                        </li>

                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="card-box">
                                <h4 class="text-dark header-title m-t-0 m-b-30">In Tray Chart</h4>
                                <canvas id="pie"  height="150"></canvas> 

                                <?php
                                $where = "";
                                require_once 'application/pages/where.php';
                                $n = 1;
                                $Approved = 0;
                                $Pending = 0;
                                $Done = 0;
                                $Processed = 0;
                                $Rejected = 0;
                                $Aborted = 0;
                                $Complete = 0;

                                $allot = "SELECT tdawf.id,tdawf.ticket_id,tsm.task_name,tdawf.doc_id,tdawf.task_status,tdawf.task_remarks,tdawf.start_date,tsm.deadline,tsm.deadline_type,tsm.priority_id,tdawf.assign_by,tdawf.NextTask FROM tbl_task_master tsm inner join tbl_doc_assigned_wf tdawf on tsm.task_id=tdawf.task_id $where ";
                                $allot_query = mysqli_query($db_con, $allot) or die("Error: " . mysqli_error($db_con));
                                $allot_row = mysqli_fetch_all($allot_query, MYSQLI_ASSOC);
                                foreach ($allot_row as $key => $value) {
                                    // echo $value[task_status];
                                    if ($value[task_status] == "Approved") {
                                        $Approved += $Approved++;
                                    }
                                    if ($value[task_status] == "Pending") {
                                        $Pending += $Pending++;
                                    }
                                    if ($value[task_status] == "Done") {
                                        $Done += $Done++;
                                    }
                                    if ($value[task_status] == "Processed") {
                                        $Processed += $Processed++;
                                    }
                                    if ($value[task_status] == "Rejected") {
                                        $Rejected += $Rejected++;
                                    }
                                    if ($value[task_status] == "Aborted") {
                                        $Aborted += $Aborted++;
                                    }
                                    if ($value[task_status] == "Complete") {
                                        $Complete += $Complete++;
                                    }
                                }
                                ?>




                                <!--                                        </tbody>
                                                                        
                                                                    </table>-->

                            </div>

                        </div>
                        <?php
                        $year = date("Y");
                        $data = array();
                        for ($month = 1; $month <= 12; $month++) {
                            $mon = sprintf("%'.02d\n", $month);
                            $qry = mysqli_query($db_con, "SELECT COUNT(distinct user_id) as res FROM `tbl_ezeefile_logs` WHERE month(start_date)='$mon' and year(start_date)='$year'");
                            $row = mysqli_fetch_assoc($qry);
                            array_push($data, $row[res]);
                        }
                        //print_r($data);
                        $datas = implode(",", $data);
                        ?>
                        <div class="col-lg-12">
                            <div class="card-box">
                                <h4 class="text-dark header-title m-t-0 m-b-30">User Report</h4>
                                <canvas id="bar"  height="100"></canvas> 






                                <!--                                        </tbody>
                                                                        
                                                                    </table>-->

                            </div>

                        </div>

                    </div> <!-- end col -->
                </div> <!-- end row -->
            </div> <!-- container -->

        </div> <!-- content -->

        <?php require_once './application/pages/footer.php'; ?>
    </div>

</div>
<!-- END wrapper --
<?php require_once './application/pages/footerForjs.php'; ?>
<!-- Chart JS -->
<script src="assets/plugins/chart.js/chart.min.js"></script>
<script src="assets/pages/jquery.chartjs.init.js"></script> 

<!-- Counterup  -->
<script>

    var ctx = document.getElementById("pie");
    var ctx = document.getElementById("pie").getContext("2d");
    var myChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ["Approved", "Pending", "Rejected"],
            datasets: [{
                    label: '# of Votes',
                    data: [<?= $Approved; ?>, <?= $Pending ?>,<?= $Rejected ?>],
                    backgroundColor: [
                        '#6bb152',
                        '#3c8dbc',
                        '#f05050'

                    ],
                    borderColor: [
                        '#6bb152',
                        '#3c8dbc',
                        '#f05050'

                    ],
                    borderWidth: 0
                }]
        }

    });
</script>
<script>
    var ctx = document.getElementById("bar");
    var myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ["January", "February ", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"],
            datasets: [{
                    label: 'Active User Analytics <?= $year; ?>',
                    fill: false,
                    lineTension: 0.1,

                    borderCapStyle: 'butt',
                    borderDash: [],
                    borderDashOffset: 0.0,
                    borderJoinStyle: 'miter',
                    pointBorderColor: "#a1c8df",
                    pointBackgroundColor: "#fff",
                    pointBorderWidth: 1,
                    pointHoverRadius: 5,
                    pointHoverBackgroundColor: "#a1c8df",
                    pointHoverBorderColor: "#a1c8df",
                    pointHoverBorderWidth: 2,
                    pointRadius: 1,
                    pointHitRadius: 10,

                    data: [<?= $datas; ?>],
                    backgroundColor: [
                        '#a1c8df'

                    ],
                    borderColor: [
                        '#3c8dbc'
                    ],
                    borderWidth: 2
                }]
        },
        options: {
            scales: {
                yAxes: [{
                        ticks: {

                            stepSize: 5
                        },
                        scaleLabel: {
                            display: true,
                            labelString: 'Number Of User'
                        }
                    }],
                xAxes: [{
                        ticks: {

                            stepSize: 1
                        },
                        scaleLabel: {
                            display: true,
                            labelString: 'Months'
                        }
                    }]
            }
        }
    });
</script>

<script src="assets/pages/jquery.todo.js"></script>
<script src="assets/plugins/jquery-sparkline/jquery.sparkline.min.js"></script>
<!--<script src="assets/pages/jquery.dashboard_3.js"></script>-->
<link href="assets/plugins/radial/radial.css" rel="stylesheet">
<script>
    $('#sparkline3').sparkline([<?php echo $totalFiles["totalFolder"]; ?>, <?php echo $totalFiles["files"]; ?>], {
        type: 'pie',
        width: '150',
        height: '150',
        sliceColors: ['#7e57c2', '#34d3eb', ]
    });
</script>
</body>
</html>