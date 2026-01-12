<!DOCTYPE html>
<html>
    <link href="assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
    <?php
    require_once './loginvalidate.php';
    require_once './application/config/database.php';
    require_once './application/pages/head.php';
    $sameGroupIDs = array();
    $group = mysqli_query($db_con, "select group_id from tbl_bridge_grp_to_um where find_in_set('$_SESSION[cdes_user_id]',user_ids)") or die('Error' . mysqli_error($db_con));
    while ($rwGroup = mysqli_fetch_assoc($group)) {
        $sameGroupIDs[] = $rwGroup['group_id'];
    }
    $sameGroupIDs = array_unique($sameGroupIDs);
    sort($sameGroupIDs);
    $sameGroupIDs = implode(',', $sameGroupIDs);
    //for user role
    $chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$_SESSION[cdes_user_id]', user_ids) > 0") or die('Error:' . mysqli_error($db_con));
    $rwgetRole = mysqli_fetch_assoc($chekUsr);
    if ($rwgetRole['mis_upload_download_report'] != '1') {
        header('Location: ./index');
    }

    $dbname = base64_decode(urldecode($_GET['db']));
    $clientId = base64_decode(urldecode($_GET['cid']));
	$users = mysqli_query($db_con, "select company from tbl_client_master where client_id='$clientId'");
	$rsuser=mysqli_fetch_array($users);
    //db con 
    $newdbconn = @mysqli_connect($dbHost,$dbUser,$dbPwd, $dbname) OR die('could not connect:' . mysqli_connect_error());
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
			<style>
			.dataTables_filter
			{
				float:right !important;
			}
			#datatable_paginate
			{
				float:right !important;
			}
			</style>
            <div class="content-page">
                <!-- Start content -->
                <div class="content">
                    <div class="container">
                        <!-- Page-Title -->
                        <div class="row">
                            <div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">
                                <ol class="breadcrumb">
                                    <li><a href="#">MIS Upload Report</a></li>									
                                    <li class="active"><?php echo $rsuser['company'];?></li>
                                    <a href="javascript:void(0)" class="btn btn-primary waves-effect waves-light pull-right btn-sm margin-t-9" onclick="goPrevious();" title="<?php echo $lang['Go_to_previous_page']; ?>"><i class="fa fa-arrow-circle-left"></i></a>
                                </ol>
                            </div>
                        </div>
                        <div class="panel">
                            <div class="box box-primary">
                                <br>
                                <div class="row">
                                    <div class="col-sm-8">
                                        <form method="get">					
                                            <input type="hidden" name="db" value="<?php echo $_GET['db']; ?>">
                                            <input type="hidden" name="cid" value="<?php echo $_GET['cid']; ?>">
                                            <div class="col-sm-5">
                                                <div class="input-daterange input-group"  id="date-range">
                                                    <input type="text" class="form-control" name="startdt" value="<?php echo $_GET['startdt']; ?>" placeholder="Start Date" autocomplete="off"/>
                                                    <span class="input-group-addon bg-custom b-0 text-white">to</span>
                                                    <input type="text" class="form-control" name="enddt" value="<?php echo $_GET['enddt']; ?>"  placeholder="End Date" autocomplete="off"/>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i>Search</button>
                                                <a href="upload-report?db=<?php echo $_GET['db']; ?>&cid=<?php echo $_GET['cid']; ?>" class="btn btn-warning"><i class="fa fa-refresh"></i> Reset</a>
												<a href="#" class="btn btn-primary btn-sm" id="export4"  data-toggle="modal"  data-target="#multi-csv-export-model"><i data-toggle="tooltip" title="Export Data" class="fa fa-download"></i> Export</a>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <div class="panel-body">
                                    <div id="">
                                        <div id="">
                                            <table class="table table-striped table-bordered" id="datatable">
                                                <thead>
                                                    <tr>
                                                        <th>SNO</th>
                                                        <th>Storage</th>
                                                        <th>No Of Files</th>
                                                        <th>No of Pages</th>
                                                        <th>Storage Size(MB)</th>
                                                        <th>Uploaded Date</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $date = date("Y-m-d");
                                                    $where = " where sl_name!='' OR sl_name is not NULL";
                                                    if (isset($_GET['enddt']) && !empty($_GET['enddt']) && isset($_GET['startdt']) && !empty($_GET['startdt'])) {
                                                        $end_date = date('Y-m-d', strtotime($_GET['enddt']));
                                                        $start_date = date('Y-m-d', strtotime($_GET['startdt']));
                                                        $where = " and  DATE(dateposted) BETWEEN '$start_date' AND '$end_date'";
                                                    }
                                                    $uploadrept = mysqli_query($newdbconn, "SELECT sl_name,count(doc_id) as no_of_file,doc_name as sl_id,sum(noofpages) as no_of_pages,dateposted,sum(doc_size) as file_size FROM tbl_document_master as tdm  join tbl_storage_level as tsl on tsl.sl_id=tdm.doc_name $where group by YEAR(dateposted),month(dateposted), day(dateposted),doc_name order by sl_name asc, DATE(dateposted) desc") or die("ERROR" . mysqli_error($newdbconn));

                                                    $num = mysqli_num_rows($uploadrept);
                                                    $totalFiles = 0;
                                                    $totalPages = 0;
                                                    $totaldocSize = 0;
                                                    if (isset($start) && $start != 0) {
                                                        $i = $start + 1;
                                                    } else {
                                                        $i = 1;
                                                    }
                                                    if ($num > 0) {

                                                        while ($rwuploadrpt = mysqli_fetch_assoc($uploadrept)) {
                                                            $totalFiles += $rwuploadrpt['no_of_file'];
                                                            $totalPages += $rwuploadrpt['no_of_pages'];
                                                            $totaldocSize += $rwuploadrpt['file_size'];
                                                            $child = mysqli_query($newdbconn, "select sl_id from tbl_storage_level where sl_parent_id='$rwuploadrpt[sl_id]'");
                                                            ?>
                                                            <tr>
                                                                <td><?php echo $i; ?></td>
                                                                <td><?php echo $rwuploadrpt['sl_name']; ?></td>
                                                                <td><?php
                                                                    $sslid = explode('_', $rwuploadrpt['sl_id']);

                                                                    if (mysqli_num_rows($child) > 0) {

                                                                        echo $rwuploadrpt['no_of_file'];
                                                                    } else {
                                                                        echo $rwuploadrpt['no_of_file'];
                                                                    }
                                                                    ?></td>
                                                                <td><?php echo $rwuploadrpt['no_of_pages']; ?></td>
                                                                <td><?php echo round($rwuploadrpt['file_size'] / (1024 * 1024), 2); ?> </td>
                                                                <td><?php
                                                                    if (!empty($rwuploadrpt['dateposted'])) {
                                                                        echo date("d-m-Y", strtotime($rwuploadrpt['dateposted']));
                                                                    }
                                                                    ?></td>
                                                            </tr>
                                                            <?php
                                                            $i++;
                                                        }
                                                        $totaldocSize = $totaldocSize / 1024;
                                                        if ($totaldocSize >= 1024) {
                                                            $totaldocSize = $totaldocSize / 1024;
                                                            if ($totaldocSize >= 1024) {
                                                                $totaldocSize = $totaldocSize / 1024;
                                                                if ($totaldocSize >= 1024) {
                                                                    $totaldocSize = $totaldocSize / 1024;
                                                                    if ($totaldocSize >= 1024) {
                                                                        
                                                                    } else {
                                                                        $totaldocSize = round($totaldocSize, 2) . ' TB';
                                                                    }
                                                                } else {
                                                                    $totaldocSize = round($totaldocSize, 2) . ' GB';
                                                                }
                                                            } else {
                                                                $totaldocSize = round($totaldocSize, 2) . ' MB';
                                                            }
                                                        } else {
                                                            $totaldocSize = round($totaldocSize, 2) . ' KB';
                                                        }
                                                        ?>
                                                    <tfoot>
                                                        <tr>
                                                            <th></th><th><strong>Total</strong></th><th><?php echo $totalFiles ?> </th><th><?php echo $totalPages ?> </th><th colspan="2"><?php echo $totaldocSize; ?></th>
                                                        </tr>
                                                    </tfoot>
                                                    <?php
                                                } else {
                                                    ?>
                                                    <tfoot>
                                                        <tr>
                                                            <th colspan="6" style="text-align: center;color: red">No File Uploaded!</th>
                                                        </tr>
                                                    </tfoot>
                                                    <?php
                                                }
                                                ?>
                                                </tbody>
                                            </table>
                                        </div>


                                    </div>

                                </div>

									<div id="multi-csv-export-model" class="modal fade"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
										<div class="modal-dialog"> 
											<div class="panel panel-color panel-danger"> 
												<div class="panel-heading"> 
													<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
													<h2 class="panel-title" id="export_title"> Export Selected Data</h2>
												</div>
												<form action="exportUploadReportmis"  method="post">
													<div class="panel-body">
														<div class="row">
															<label id="export_unselected" style="display:none;"><h5 class="text-danger"> Please Select File for Export</h5></label>
															<div id="export_selected">
																<label>Select File for Export<span class="text-danger">*</span></label>
																<select class="form-control select2" name="select_Fm" required="">
																	<option value="EXCEL">EXCEL</option>
																</select>
															</div>
														</div>
													</div>
													<div class="modal-footer"> 
														 <input type="hidden" name="enddt" value="<?php echo $_GET['enddt'] ?>">
														 <input type="hidden" name="startdt" value="<?php echo $_GET['startdt'] ?>">
														 <input type="hidden" name="db" value="<?php echo $_GET['db'] ?>">
														 <input type="hidden" name="cid" value="<?php echo $_GET['cid'] ?>">
														 <input type="hidden" name="report_type" value="uploadreport">
														<button type="button" class="btn btn-danger waves-effect" data-dismiss="modal">Close</button>
														<button type="submit" name="export" value="export" title="Export Report" class="btn btn-primary pull-right"> <i class="fa fa-download"></i> Export</button> 
													</div>
												</form>
											</div> 
										</div>
									</div> 
                            </div>
                        </div>
                    </div>
                </div>
            </div>           
        </div> <!-- container -->

        <!-- content -->

        <?php require_once './application/pages/footer.php'; ?>

        <!-- ============================================================== -->
        <!-- End Right content here -->
        <!-- ============================================================== -->
        <script src="assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
        <script src="assets/plugins/timepicker/bootstrap-timepicker.js"></script>
        <script src="assets/plugins/bootstrap-daterangepicker/daterangepicker.js"></script>
        <script src="assets/plugins/select2/js/select2.min.js" type="text/javascript"></script>
        <?php require_once './application/pages/footerForjs.php'; ?>
        <script>
                                        $(".select2").select2();
										
										$('#datatable').dataTable();
                                        jQuery('#date-range').datepicker({
                                            toggleActive: true
                                        });
        </script>
    </body>
</html>
