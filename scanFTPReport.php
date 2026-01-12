<!DOCTYPE html>
<html>

    <?php
    require_once './loginvalidate.php';
    require_once './application/pages/head.php';
    error_reporting(0);
    //for user role
    mysqli_set_charset($db_con, "utf8");
    $chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$_SESSION[cdes_user_id]', user_ids) > 0") or die('Error:' . mysqli_error($db_con));

    $rwgetRole = mysqli_fetch_assoc($chekUsr);
    ?>
    <link rel="stylesheet" href="assets/plugins/jquery-datatables-editable/datatables.css" />
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
                            <div class="col-sm-12">
                                <ol class="breadcrumb">
                                    <li>
                                        <a href="index"> Dashboard </a>
                                    </li>
                                    <li class="active">
                                        Client Wise File Report
                                    </li>
                                    <a href="javascript:void(0)" class="btn btn-primary waves-effect waves-light pull-right btn-sm margin-t-9" onclick="goPrevious();" title="<?php echo $lang['Go_to_previous_page']; ?>"><i class="fa fa-arrow-circle-left"></i></a>
                                </ol>
                            </div>
                        </div>
                        <div class="row">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th><?php echo $lang['Sr_No']; ?></th>
                                        <th><?php echo "Company"; ?></th>
                                        <th><?php echo "Domain"; ?></th>
                                        <th><?php echo "Total Files"; ?></th>
                                        <th><?php echo "Files on FTP"; ?></th>
                                        <th><?php echo "Missing Files"; ?></th>
                                        <th><?php echo "Action"; ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $time = time();
                                    $getClient = mysqli_query($db_con, "select client_id,company,db_name,subdomain from tbl_client_master where valid_upto > '$time' and db_name not in ('DMS_C_N_Patel___Company_1578301300','DMS_Casa__Stays_Pvt_Ltd_1590995478')");
                                    $rwClient = mysqli_fetch_assoc($getClient);
                                    $i = 1;
                                    while ($rwClient = mysqli_fetch_assoc($getClient)) {
                                        $domain = $rwClient['subdomain'];
                                        ?>
                                        <tr class="gradeX">
                                            <td><?php echo $i . '.'; ?></td>
                                            <td><?php echo $rwClient['company']; ?></td>
                                            <td><?php echo $domain; ?></td>

                                            <?php
                                            $dbName = $rwClient['db_name'];
                                            $connect = mysqli_connect($dbHost, $dbUser, $dbPwd, $dbName) or die("connection error" . mysqli_connect_error());
                                            ?>
                                            <th><?php
                                                $total = mysqli_query($connect, "select count(*) as total from tbl_document_master where flag_multidelete=1");
                                                $rwTotal = mysqli_fetch_assoc($total);
                                                echo $rwTotal['total'];
                                                ?></th>
                                            <th><?php
                                                $totalFTP = mysqli_query($connect, "select count(*) as total from tbl_document_master where flag_multidelete=1 and ftp_exists=1");
                                                $rwTotalFTP = mysqli_fetch_assoc($totalFTP);
                                                echo $rwTotalFTP['total'];
                                                ?></th>
                                            <th><?php
                                                $totalNFTP = mysqli_query($connect, "select count(*) as total from tbl_document_master where flag_multidelete=1 and ftp_exists=0");
                                                $rwTotalNFTP = mysqli_fetch_assoc($totalNFTP);
                                                echo $rwTotalNFTP['total'];
                                                ?></th>
                                            <th><a href="fileSummaryClient?id=<?php echo $rwClient['client_id']; ?>"><i class="fa fa-eye"></i></a></th>

                                            <?php
                                            $i++;
                                        }
                                        mysqli_close($connect);
                                        ?>
                                </tbody>
                            </table>
                        </div> <!-- end col -->
                    </div> <!-- end row -->
                </div>
            </div> <!-- container -->

            <?php require_once './application/pages/footer.php'; ?>
        </div> <!-- content -->
        <!-- END wrapper -->
        <script src="assets/js/jquery.min.js"></script>
        <?php require_once './application/pages/footerForjs.php'; ?>
        <script src="assets/plugins/bootstrap-filestyle/js/bootstrap-filestyle.min.js" type="text/javascript"></script>
        <script src="assets/plugins/select2/js/select2.min.js" type="text/javascript"></script>
        <script type="text/javascript" src="assets/plugins/parsleyjs/parsley.min.js"></script>

        <script src="assets/js/gs_sortable.js"></script>

    </body>
</html>
