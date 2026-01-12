<!DOCTYPE html>
<html>
    <!--Morris Chart CSS -->
    <link rel="stylesheet" href="assets/plugins/chartist/css/chartist.min.css">
    <?php

    require_once './loginvalidate.php';
    require_once './application/pages/head.php';
    require_once './application/pages/function.php';

    
    $perm = mysqli_query($db_con, "select sl_id from tbl_storagelevel_to_permission where user_id='$_SESSION[cdes_user_id]' group by sl_id");
    $slperms = array();
    while ($rwPerm = mysqli_fetch_assoc($perm)) {
        $slperms[] = $rwPerm['sl_id'];
    }

    $sl_perm = implode(',', $slperms);
    $slids = findsubfolder($sl_perm, $db_con);

    $slids = implode(',', $slids);
    ?>
    <style>
        .h4,h4{
            font-size: 15px;
        }
        .h2, h2 {
            font-size: 24px;
        }
        .btn{
            padding: 5px 9px;
        }
        .text-center{
            text-align: left;
            margin-left: 100px;
        }
    </style>

    <!-- full Calendar -->
    <link rel="stylesheet" href="assets/plugins/fullcalendar/css/fullcalendar.min.css">
    <link rel="stylesheet" href="assets/plugins/fullcalendar/css/fullcalendar.print.min.css" media="print">

    <body class="fixed-left">

        <!-- Begin page -->
        <div id="wrapper">

            <!-- Top Bar Start -->
            <?php require_once './application/pages/topBar.php'; ?>
            <!-- Top Bar End -->
            <!-- ========== Left Sidebar Start ========== -->
            <?php require_once './application/pages/sidebar.php'; ?>
            <!-- Left Side bar End -->

            <!-- ============================================================== -->
            <!-- Start right Content here -->
            <!-- ============================================================== -->
            <div class="content-page">
                <!-- Start content -->
                <div class="content">
                    <div class="container">

                        <!-- Page-Title -->
                       
                        
                        <div class="row">
                                <?php
                                
                                $contFile = mysqli_query($db_con, "select sum(doc_size) as total, count(doc_name) as count,sum(noofpages) as numPage from tbl_document_master where doc_name in ($slids)") or die('Error :' . mysqli_error($db_con));
                                $rwcontFile = mysqli_fetch_assoc($contFile);
                                 $totalFileSize = round($rwcontFile['total'] / (1000 * 1000), 2);
                                 $totalsize = (($totalFileSize > 999) ? round($totalFileSize / 1000, 2) : $totalFileSize).(($totalFileSize > 999) ? ' GB' : ' MB');;
                                ?>
                                <div class="col-md-4 col-lg-4 col-sm-4 col-xs-4">
                                    <a href="storage">
                                        <div class="widget-bg-color-icon card-box">
                                            <div class="bg-icon bg-icon-custom pull-left">
                                                <i class="ti-bar-chart text-custom"></i>
                                            </div>
                                            <div class="text-center dsh">
                                                <h5 class="text-dark"><strong><?php echo "Total Files"; ?></strong></h5>
                                                <p class="text-muted text-uppercase"><?php echo 'Total Files : '. $rwcontFile['count']; ?></p>
                                                <p class="text-muted text-uppercase"><?php echo 'Total File Size : '.$totalsize; ?></p>
                                            </div>
                                            <div class="clearfix"></div>
                                        </div>  
                                    </a>
                                </div> 
                           
                             <?php
                                
                                $scontFile = mysqli_query($db_con, "select sum(doc_size) as total, count(doc_name) as count,sum(noofpages) as numPage from tbl_document_master where doc_name in ($slids) and flag_multidelete=1 and (workflow_id IS NULL or workflow_id='')") or die('Error :' . mysqli_error($db_con));
                                $srwcontFile = mysqli_fetch_assoc($scontFile);
                                $stotalFileSize = round($srwcontFile['total'] / (1000 * 1000), 2);
                                $stotalsize = (($stotalFileSize > 999) ? round($stotalFileSize / 1000, 2) : $stotalFileSize).(($stotalFileSize > 999) ? ' GB' : ' MB');;
                            ?>
                               
                            <div class="col-md-4 col-lg-4 col-sm-4 col-xs-4">
                                <a href="storage">
                                     <div class="widget-bg-color-icon card-box">
                                         <div class="bg-icon bg-icon-custom pull-left">
                                             <i class="ti-bar-chart text-custom"></i>
                                         </div>
                                         <div class="text-center dsh">
                                             <h5 class="text-dark"><strong><?php echo "Storage Files"; ?></strong></h5>
                                             <p class="text-muted text-uppercase"><?php echo 'Total Files : '. $srwcontFile['count']; ?></p>
                                             <p class="text-muted text-uppercase"><?php echo 'Total File Size : '.$stotalsize; ?></p>
                                         </div>
                                         <div class="clearfix"></div>
                                     </div>  
                                 </a>
                             </div> 
                             <?php
                                
                                $rcontFile = mysqli_query($db_con, "select sum(doc_size) as total, count(doc_name) as count,sum(noofpages) as numPage from tbl_document_master where doc_name in ($slids) and (flag_multidelete=0 or  flag_multidelete=3) and (workflow_id IS NULL or workflow_id='')") or die('Error :' . mysqli_error($db_con));
                                $rrwcontFile = mysqli_fetch_assoc($rcontFile);
                                $rtotalFileSize = round($rrwcontFile['total'] / (1000 * 1000), 2);
                                $rtotalsize = (($rtotalFileSize > 999) ? round($rtotalFileSize / 1000, 2) : $rtotalFileSize).(($rtotalFileSize > 999) ? ' GB' : ' MB');;
                            ?>
                               
                            <div class="col-md-4 col-lg-4 col-sm-4 col-xs-4">
                                <a href="recycle">
                                    <div class="widget-bg-color-icon card-box">
                                        <div class="bg-icon bg-icon-custom pull-left">
                                            <i class="ti-bar-chart text-custom"></i>
                                        </div>
                                        <div class="text-center dsh">
                                            <h5 class="text-dark"><strong><?php echo "Recycle Bin Files"; ?></strong></h5>
                                            <p class="text-muted text-uppercase"><?php echo 'Total Files : '. $rrwcontFile['count']; ?></p>
                                            <p class="text-muted text-uppercase"><?php echo 'Total File Size : '.$rtotalsize; ?></p>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>  
                                </a>
                            </div> 
                                
                        </div> <!-- end col -->
                        <?php
                                
                                $wcontFile = mysqli_query($db_con, "select sum(doc_size) as total, count(doc_name) as count,sum(noofpages) as numPage from tbl_document_master where doc_name in ($slids) and (workflow_id IS NOT NULL or workflow_id!='')") or die('Error :' . mysqli_error($db_con));
                                $workfCount = mysqli_fetch_assoc($wcontFile);
                                $wtotalFileSize = round($workfCount['total'] / (1000 * 1000), 2);
                                $wtotalsize = (($wtotalFileSize > 999) ? round($wtotalFileSize / 1000, 2) : $wtotalFileSize).(($wtotalFileSize > 999) ? ' GB' : ' MB');;
                            ?>
                        <div class="row">
                            <div class="col-md-4 col-lg-4 col-sm-4 col-xs-4">
         
                                    <div class="widget-bg-color-icon card-box">
                                        <div class="bg-icon bg-icon-custom pull-left">
                                            <i class="ti-bar-chart text-custom"></i>
                                        </div>
                                        <div class="text-center dsh">
                                            <h5 class="text-dark"><strong><?php echo "Workflow Files"; ?></strong></h5>
                                            <p class="text-muted text-uppercase"><?php echo 'Total Files : '. $workfCount['count']; ?></p>
                                            <p class="text-muted text-uppercase"><?php echo 'Total File Size : '.$wtotalsize; ?></p>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>  
                            </div> 
                        </div>
                    </div> <!-- end row -->
                </div>
            </div> <!-- container -->

            <?php require_once './application/pages/footer.php'; ?>
        </div> <!-- content -->
        <!-- END wrapper -->
        <script src="assets/js/jquery.min.js"></script>
        <?php require_once './application/pages/footerForjs.php'; ?>
        <!--Chartist Chart-->
        <script src="assets/plugins/chartist/js/chartist.min.js"></script>
        <script src="assets/plugins/chartist/js/chartist-plugin-tooltip.min.js"></script>
        <!-- Chart JS -->
        <script src="assets/plugins/chart.js/chart.min.js"></script>
       
        <script src="assets/plugins/morris/morris.min.js"></script>
        <script src="assets/plugins/raphael/raphael-min.js"></script>
        <script type="text/javascript">
                                //@sk(20918) Donut chart for task status

        </script>
        <script src="assets/pages/jquery.todo.js"></script>
        <script src="assets/plugins/jquery-sparkline/jquery.sparkline.min.js"></script>
        <!--<script src="assets/pages/jquery.dashboard_3.js"></script>-->
        <link href="assets/plugins/radial/radial.css" rel="stylesheet">
        <script src="assets/pages/jquery.chartjs.init.js"></script>
        <script src="assets/js/jquery.nicescroll.js"></script>

        <!-- full calendar -->
        <script src="assets/plugins/moment/moment.js"></script>
        <script src="assets/plugins/fullcalendar/js/fullcalendar.min.js"></script>
       
        <!-- /full calendar -->
    </body>
</html>
