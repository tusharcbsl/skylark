<!DOCTYPE html>
<html>

    <link href="assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />

    <?php
    require_once './loginvalidate.php';
    require_once './application/pages/head.php';
    require_once './application/pages/function.php';

    $slids = findsubfolder($slpermIdes, $db_con);

    $slids = implode(',', $slids);

    $userRole = $rwgetRole['user_role'];
    if ($rwgetRole['mis_report'] != 1) {
        header('location:index.php');
    }

   
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
            <div class="content-page">
                <!-- Start content -->
                <div class="content">
                    <div class="container">
                        <!-- Page-Title -->
                        <div class="row">
                            <div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">
                                <ol class="breadcrumb">
                                    <li class="active"><a href="#"><?php echo $lang['MIS_upload_report']; ?></a></li>
                                    <a href="javascript:void(0)" class="btn btn-primary waves-effect waves-light pull-right btn-sm margin-t-9" onclick="goPrevious();" title="<?php echo $lang['Go_to_previous_page']; ?>"><i class="fa fa-arrow-circle-left"></i></a>
                                </ol>
                            </div>
                        </div>
                        <div class="panel">
                            <div class="box box-primary">
                                <div class="box-header with-border">
                                    <div class="col-sm-10">
                                        <h4 class="header-title"><?php echo $lang['digital_record']; ?> (<?php echo date('d-m-Y'); ?>)</h4>
                                    </div>
                                    
									
									<div class="row">
                                            <div class="col-sm-12">
                                                <form method="get">
                                                    <div class="col-sm-3">
                                                        <input type="text" class="form-control translatetext" name="sname" id="sname" parsley-trigger="change" placeholder="Please enter storage name" value="<?php echo $_GET['sname']; ?>" >
                                                    </div>
													
													<div class="col-sm-4">
														<div class="form-group">
															<div class="input-group">
																<div class="input-daterange input-group" id="date-range">
																	<input type="text" class="form-control" name="fromDate" id="fromDate" value="<?php echo $_GET['fromDate']; ?>" placeholder="<?= $lang['dd_mm_yyyy']; ?>" title="<?= $lang['dd_mm_yyyy']; ?>" autocomplete="off" />
																	<span class="input-group-addon bg-custom b-0 text-white"><?php echo $lang['to']; ?></span>
																	<input type="text" class="form-control" name="toDate" id="toDate" value="<?php echo $_GET['toDate']; ?>"   placeholder="<?= $lang['dd_mm_yyyy']; ?>" title="<?= $lang['dd_mm_yyyy']; ?>" autocomplete="off" />
																</div>
															</div>
														</div>
													</div>
													
                                                    <div class="col-sm-3">
                                                        <button type="submit" class="btn btn-primary">Search <i class="fa fa-search"></i> </button>
                                                        <a href="misUploadReport" class="btn btn-warning"> Reset</a>
                                                    </div>
													
													<div class="col-sm-2">
														<a href="javascript:void(0)" class="btn btn-primary btn-sm" id="export4"  data-toggle="modal"  data-target="#multi-csv-export-model"><i data-toggle="tooltip" title="<?php echo $lang['Export_Data'] ?>" class="fa fa-download"></i> <?php echo $lang['Export']; ?></a>

													</div>
                                                <input type="hidden" value="b0ZuF/2K+RKu1zApCwz7m+OVI1fwVf5UGHLj5UatjPE=" name="token">
												</form>

                                            </div>
                                        </div>
                                </div>
								
								
                                <div class="panel-body">
								
                                    <div id="Upload_Report">
                                        <!-- <div id="all_upld"> -->
                                            <table class="table table-striped table-bordered" id="UpreportTable">
                                                <thead>
                                                    <tr>
                                                        <th class="js-sort-none" ><?php echo $lang['SNO']; ?></th>
                                                        <th class="js-sort-none" ><?php echo $lang['strg']; ?></th>
                                                        <th class="js-sort-none" ><?php echo $lang['nooffiles']; ?></th>
                                                        <th class="js-sort-none" ><?php echo $lang['No_Of_Pages']; ?></th>
                                                        <th class="js-sort-none" ><?php echo $lang['Storage_Size']; ?>(MB)</th>
                                                        <th class="arrow" >
														
														<?php if(isset($_GET['sort']) && $_GET['sort']=="up"){ ?>
														<a  href="misUploadReport?sort=d&sname=<?php echo $_GET['sname']; ?>&fromDate=<?php echo $_GET['fromDate']; ?>&toDate=<?php echo $_GET['toDate']; ?>" > <?php echo $lang['Uploaded_On']; ?> <i class="fa fa-arrow-down"></i></a></th>
														<?php }else{ ?>
														<a href="misUploadReport?sort=up&sname=<?php echo $_GET['sname']; ?>&fromDate=<?php echo $_GET['fromDate']; ?>&toDate=<?php echo $_GET['toDate']; ?>"  > <?php echo $lang['Uploaded_On']; ?> <i class="fa fa-arrow-up"></i></a></th>
														<?php } ?>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
													$where ="";
													if(isset($_GET['sname']) && !empty($_GET['sname'])){
														$slname = xss_clean($_GET['sname']);
														$where = " and sl_name like '%$slname%'";
													}
													
													if((isset($_GET['fromDate']) && !empty($_GET['fromDate'])) && (isset($_GET['toDate']) && !empty($_GET['toDate']))){
														
														$fromDate = xss_clean($_GET['fromDate']);
														$toDate = xss_clean($_GET['toDate']);
														
														$fromDate = date('Y-m-d', strtotime($fromDate));
														$toDate = date('Y-m-d', strtotime($toDate));
												
														$where .= " and date(dateposted)>='$fromDate' and date(dateposted)<='$toDate'";
														
													}
													
													if(isset($_GET['sort']) && !empty($_GET['sort']))
													{
														$sort =($_GET['sort']=="up")?"asc":"desc";
														
													}else{
														$sort = "desc";
													}
                                                    $date = date("Y-m-d");
													
													//echo "SELECT sl_name,count(doc_id) as no_of_file,doc_name as sl_id,sum(noofpages) as no_of_pages,dateposted,sum(doc_size) as file_size FROM tbl_document_master as tdm  join tbl_storage_level as tsl on tsl.sl_id=tdm.doc_name where tsl.sl_id in($slids) and flag_multidelete=1 $where group by YEAR(dateposted),month(dateposted), day(dateposted),doc_name order by dateposted $sort";
                                                    if(!empty($slids)){
													$uploadrept = mysqli_query($db_con, "SELECT sl_name,count(doc_id) as no_of_file,doc_name as sl_id,sum(noofpages) as no_of_pages,dateposted,sum(doc_size) as file_size FROM tbl_document_master as tdm  join tbl_storage_level as tsl on tsl.sl_id=tdm.doc_name where tsl.sl_id in($slids) and flag_multidelete=1 $where group by YEAR(dateposted),month(dateposted), day(dateposted),doc_name order by dateposted $sort") or die("ERROR" . mysqli_error($db_con));
                                                    $num = mysqli_num_rows($uploadrept);
													}
                                                    $totalFiles = 0;
                                                    $totalPages = 0;
                                                    $totaldocSize = 0;
                                                    if (isset($start) && $start != 0) {
                                                        $i = $start + 1;
                                                    } else {
                                                        $i = 1;
                                                    }
                                                    if ($num > 0) {

                                                        while($rwuploadrpt = mysqli_fetch_assoc($uploadrept)) {
															
                                                            $totalFiles += $rwuploadrpt['no_of_file'];
                                                            $totalPages += $rwuploadrpt['no_of_pages'];
                                                            $totaldocSize += $rwuploadrpt['file_size'];
                                                            $child = mysqli_query($db_con, "select sl_id from tbl_storage_level where sl_parent_id='$rwuploadrpt[sl_id]'");
                                                            ?>
                                                            <tr>
                                                                <td><?php echo $i; ?></td>
                                                                <td><?php echo $rwuploadrpt['sl_name']; ?></td>
                                                                <td><?php
                                                                    $sslid = explode('_', $rwuploadrpt['sl_id']);

                                                                    if (mysqli_num_rows($child) > 0) {

                                                                        echo '<a href="storage?id=' . urlencode(base64_encode($sslid[0])) . '">' . $rwuploadrpt['no_of_file'] . '</a>';
                                                                    } else {
                                                                        echo '<a href="storageFiles?id=' . urlencode(base64_encode($sslid[0])) . '">' . $rwuploadrpt['no_of_file'] . '</a>';
                                                                    }
                                                                    ?>
                                                                </td>
                                                                <td><?php echo $rwuploadrpt['no_of_pages']; ?></td>
                                                                <td><?php echo formatSizeUnits($rwuploadrpt['file_size']); ?></td>
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
                                                                        $totaldocSize = round($totaldocSize, 2) . 'TB';
                                                                    }
                                                                } else {
                                                                    $totaldocSize = round($totaldocSize, 2) . 'GB';
                                                                }
                                                            } else {
                                                                $totaldocSize = round($totaldocSize, 2) . 'MB';
                                                            }
                                                        } else {
                                                            $totaldocSize = round($totaldocSize, 2) . 'KB';
                                                        }
                                                        ?>
                                                    <tfoot>
													
														<tr>
                                                            <th></th><th style="text-align: center;"><strong><?php echo $lang['total']; ?>  </strong></th><th style="text-align: center;"><?php echo $totalFiles ?> </th><th style="text-align: center;"><?php echo $totalPages ?> </th><th colspan="2"><?php echo $totaldocSize; ?></th>
                                                        </tr>
														
                                                        <!--tr>
                                                            <th></th><th style="text-align: center;"><strong><?php echo $lang['total']; ?>  </strong></th><th style="text-align: center;"><?php echo $files ?> </th><th style="text-align: center;"><?php echo $numPages ?> </th><th colspan="2"><?php echo $fileSize; ?></th>
                                                        </tr-->
                                                    </tfoot>
                                                    <?php
                                                } else {
                                                    ?>
                                                    <tfoot>
                                                        <tr>
                                                            <th colspan="6" style="text-align: center;color: red"><?php echo $lang['No_Files_Uploaded_Today']; ?></th>
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
                                                <h2 class="panel-title" id="export_title"><?php echo $lang['Export_Slt_Data']; ?></h2>
                                            </div>
                                            <form action="exportUploadReport_mis"  method="post">
                                                <div class="panel-body">
                                                    <div class="row">
                                                        <label id="export_unselected" style="display:none;"><h5 class="text-danger"> <?php echo $lang['Pls_slt_Files_for_xpt_dta']; ?></h5></label>
                                                        <div id="export_selected">
                                                            <label><?php echo $lang['Select_Files_for_Export_Format']; ?><span class="text-danger">*</span></label>
                                                            <select class="form-control select2" name="select_Fm" required="">
                                                                <option value="" selected="" disabled=""><?php echo $lang['Select_Files_for_Export_Format']; ?></option>
                                                                <option value="CSV"><?php echo $lang['Csv']; ?></option>
                                                                <option value="EXCEL"><?php echo $lang['Excel']; ?></option>
                                                                <option value="PDF"><?php echo $lang['Pdf']; ?></option> 
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer"> 
												<input type="hidden" class="form-control" name="sname" value="<?php echo $_GET['sname']; ?>" >
												<input type="hidden" class="form-control" name="fromDate" value="<?php echo $_GET['fromDate']; ?>" >
												<input type="hidden" class="form-control" name="toDate" value="<?php echo $_GET['toDate']; ?>" >
												
                                                    <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button>
                                                    <button type="submit" name="export" value="export" title="Export Report" class="btn btn-primary pull-right"> <i class="fa fa-download"></i> <?php echo $lang['Export']; ?></button> 

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
		 <script src="assets/js/sort-table.js"></script>
            <script src="assets/js/sort-table.min.js"></script>
        <?php require_once './application/pages/footerForjs.php'; ?>
        <script>
		jQuery('#date-range').datepicker({
			toggleActive: true
		});
		$(".select2").select2();
        </script>
    </body>
</html>
