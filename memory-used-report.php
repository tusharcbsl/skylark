<!DOCTYPE html>
<html>
    <?php
    require_once './loginvalidate.php';
    require_once './application/config/database.php';
    require_once './application/pages/head.php';
    require_once './application/pages/function.php';
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
    ?>

    <link href="assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />

    <!-- Plugin Css-->
    <link rel="stylesheet" href="assets/plugins/magnific-popup/css/magnific-popup.css" />
    <link rel="stylesheet" href="assets/plugins/jquery-datatables-editable/datatables.css" />

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
                                <ol class="breadcrumb">
                                    <li>
                                        <a href="memory-used-report">Client List</a>
                                    </li>
                                    <li class="active">
                                        Client List
                                    </li>
                                </ol>
                            </div>
                        </div>
                        <div class="panel">
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <form method="get">
                                            <div class="col-sm-4">
                                                <input type="text" class="form-control" name="cname" value="<?php echo $_GET['cname'] ?>" parsley-trigger="change"  data-parsley-required-message="Enter Company Name, Client Name for Search"placeholder="Enter Company Name, Client Name for Search"  />
                                            </div>
                                            <div class="col-sm-6">
                                                <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i>Search</button>
                                                <a href="memory-used-report" class="btn btn-warning"><i class="fa fa-refresh"></i> Reset</a>
												<a href="#" class="btn btn-primary btn-sm" id="export4"  data-toggle="modal"  data-target="#multi-csv-export-model"><i data-toggle="tooltip" title="Export Data" class="fa fa-download"></i> Export</a>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <div class="box-body">

                                    <?php
                                    if (!empty($_GET['cname'])) {
                                        $company = $_GET['cname'];
                                        $condition .= "and (concat(fname, ' ', lname) LIKE '%$company%' OR concat(lname, ' ', fname) LIKE '%$company%' OR  company like '%$company%')";
                                        //$condition = " where company like '%$company%'";
                                    }
									$currentsdate=strtotime(date("Y-m-d"));
                                    $sql = "SELECT * FROM  tbl_client_master where valid_upto>='$currentsdate' $condition";
                                    $retval = mysqli_query($db_con, $sql) or die('Could not get data: ' . mysqli_error($db_con));
                                    $foundnum = mysqli_num_rows($retval);
                                    if ($foundnum > 0) {
                                        if (is_numeric($_GET['limit'])) {
                                            $per_page = preg_replace("/[^0-9]/", "", $_GET['limit']);
                                        } else {
                                            $per_page = 10;
                                        }
                                        $start = isset($_GET['start']) ? $_GET['start'] : '';
                                        $max_pages = ceil($foundnum / $per_page);
                                        if (!$start) {
                                            $start = 0;
                                        }
                                        ?>

                                        Show <select id="limit">
                                            <option value="10" <?php
                                            if ($_GET['limit'] == 10) {
                                                echo 'selected';
                                            }
                                            ?>>10</option>
                                            <option value="25" <?php
                                            if ($_GET['limit'] == 25) {
                                                echo 'selected';
                                            }
                                            ?>>25</option>
                                            <option value="50" <?php
                                            if ($_GET['limit'] == 50) {
                                                echo 'selected';
                                            }
                                            ?>>50</option>
                                            <option value="250" <?php
                                            if ($_GET['limit'] == 250) {
                                                echo 'selected';
                                            }
                                            ?>>250</option>
                                            <option value="500" <?php
                                            if ($_GET['limit'] == 500) {
                                                echo 'selected';
                                            }
                                            ?>>500</option>
                                        </select> Client
                                        <div class="pull-right record">
                                            <?php echo $start + 1 ?> to <?php
                                            if (($start + 10) > $foundnum) {
                                                echo $foundnum;
                                            } else {
                                                echo ($start + 10);
                                            }
                                            ?> Out Of <span>Total Records: <?php echo $foundnum; ?></span>

                                        </div>
                                        <?php
                                        $where = '';
                                        $users = mysqli_query($db_con, "select * from tbl_client_master where valid_upto>='$currentsdate'  $condition order by company asc LIMIT $start, $per_page") or die('Error:' . mysqli_error($db_con));
                                        showData($users, $rwgetRole, $db_con,$dbHost,$dbUser,$dbPwd, $slpermIdes);
                                        ?>
                                        <?php
                                        echo "<center>";
                                        $prev = $start - $per_page;
                                        $next = $start + $per_page;

                                        $adjacents = 3;
                                        $last = $max_pages - 1;
                                        if ($max_pages > 1) {
                                            ?>

                                            <ul class='pagination strgePage'>
                                                <?php
                                                //previous button
                                                if (!($start <= 0))
                                                    echo " <li><a href='?start=" . $prev . "&limit=" . $_GET['limit'] . "'>Prev</a> </li>";
                                                else
                                                    echo " <li class='disabled'><a href='javascript:void(0)'>Prev</a> </li>";
                                                //pages 
                                                if ($max_pages < 7 + ($adjacents * 2)) {   //not enough pages to bother breaking it up
                                                    $i = 0;
                                                    for ($counter = 1; $counter <= $max_pages; $counter++) {
                                                        if ($i == $start) {
                                                            echo "<li class='active'><a href='?start=" . $i . "&limit=" . $_GET['limit'] . "'><b>$counter</b></a> </li>";
                                                        } else {
                                                            echo "<li><a href='?start=" . $i . "&limit=" . $_GET['limit'] . "'>$counter</a></li> ";
                                                        }
                                                        $i = $i + $per_page;
                                                    }
                                                } elseif ($max_pages > 5 + ($adjacents * 2)) {    //enough pages to hide some
                                                    //close to beginning; only hide later pages
                                                    if (($start / $per_page) < 1 + ($adjacents * 2)) {
                                                        $i = 0;
                                                        for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++) {
                                                            if ($i == $start) {
                                                                echo " <li class='active'><a href='?start=" . $i . "&limit=" . $_GET['limit'] . "'><b>$counter</b></a></li> ";
                                                            } else {
                                                                echo "<li> <a href='?start=" . $i . "&limit=" . $_GET['limit'] . "'>$counter</a> </li>";
                                                            }
                                                            $i = $i + $per_page;
                                                        }
                                                    }
                                                    //in middle; hide some front and some back
                                                    elseif ($max_pages - ($adjacents * 2) > ($start / $per_page) && ($start / $per_page) > ($adjacents * 2)) {
                                                        echo " <li><a href='?start=0&limit=" . $_GET['limit'] . "'>1</a></li> ";
                                                        echo "<li><a href='?start=" . $per_page . "&limit=" . $_GET['limit'] . "'>2</a></li>";
                                                        echo "<li><a href='javascript:void(0)'>...</a></li>";

                                                        $i = $start;
                                                        for ($counter = ($start / $per_page) + 1; $counter < ($start / $per_page) + $adjacents + 2; $counter++) {
                                                            if ($i == $start) {
                                                                echo " <li class='active'><a href='?start=" . $i . "&limit=" . $_GET['limit'] . "'><b>$counter</b></a></li> ";
                                                            } else {
                                                                echo " <li><a href='?start=" . $i . "&limit=" . $_GET['limit'] . "'>$counter</a> </li>";
                                                            }
                                                            $i = $i + $per_page;
                                                        }
                                                    }
                                                    //close to end; only hide early pages
                                                    else {
                                                        echo "<li> <a href='?start=0'>1</a> </li>";
                                                        echo "<li><a href='?start=$per_page'>2</a></li>";
                                                        echo "<li><a href='javascript:void(0)'>...</a></li>";

                                                        $i = $start;
                                                        for ($counter = ($start / $per_page) + 1; $counter <= $max_pages; $counter++) {
                                                            if ($i == $start) {
                                                                echo " <li class='active'><a href='?start=" . $i . "&limit=" . $_GET['limit'] . "'><b>$counter</b></a></li> ";
                                                            } else {
                                                                echo "<li> <a href='?start=" . $i . "&limit=" . $_GET['limit'] . "'>$counter</a></li> ";
                                                            }
                                                            $i = $i + $per_page;
                                                        }
                                                    }
                                                }
                                                //next button
                                                if (!($start >= $foundnum - $per_page))
                                                    echo "<li><a href='?start=" . $next . "&limit=" . $_GET['limit'] . "'>Next</a></li>";
                                                else
                                                    echo "<li class='disabled'><a href='javascript:void(0)'>Next</a></li>";
                                                ?>
                                            </ul>
                                            <?php
                                        }
                                        echo "</center>";
                                    } else {
                                        ?>
                                        <div class="form-group no-records-found"><label><i>No Client Found !!</i></label></div>
                                    <?php }
                                    ?>	
                                </div>
                            </div>
                            <!-- end: page -->
                        </div> <!-- end Panel -->
                        <!-- end: page -->
                    </div> <!-- end Panel -->
                </div> <!-- container -->

            </div> <!-- content -->
	
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
						 <input type="hidden" name="cname" value="<?php echo $_GET['cname'] ?>">
						 <input type="hidden" name="limit" value="<?php echo $_GET['limit'] ?>">
						 <input type="hidden" name="start" value="<?php echo $_GET['start'] ?>">
						 <input type="hidden" name="report_type" value="memoryusedreport">
						<button type="button" class="btn btn-danger waves-effect" data-dismiss="modal">Close</button>
						<button type="submit" name="export" value="export" title="Export Report" class="btn btn-primary pull-right"> <i class="fa fa-download"></i> Export</button> 
					</div>
				</form>
			</div> 
		</div>
	</div> 
    <?php require_once './application/pages/footer.php'; ?>

</div>
<!-- ============================================================== -->
<!-- End Right content here -->
<!-- ============================================================== -->
<!-- Right Sidebar -->
<?php require_once './application/pages/rightSidebar.php'; ?>
<?php require_once './application/pages/footerForjs.php'; ?>

<script src="assets/plugins/select2/js/select2.min.js" type="text/javascript"></script>

<script type="text/javascript" src="assets/plugins/parsleyjs/parsley.min.js"></script>

<script>

//limit filter
    var url = window.location.href + "?";
    function removeParam(key, sourceURL) {
        sourceURL = String(sourceURL).replace("#/", "");
        var rtn = sourceURL.split("?")[0],
                param,
                params_arr = [],
                queryString = (sourceURL.indexOf("?") !== -1) ? sourceURL.split("?")[1] : "";
        if (queryString !== "") {
            params_arr = queryString.split("&");
            for (var i = params_arr.length - 1; i >= 0; i -= 1) {
                param = params_arr[i].split("=")[0];
                if (param === key) {
                    params_arr.splice(i, 1);
                }
            }
            rtn = rtn + "?" + params_arr.join("&");
        } else {
            rtn = rtn + '?';
        }
        return rtn;
    }
    jQuery(document).ready(function ($) {
        $("#limit").change(function () {
            lval = $(this).val();
            url = removeParam("limit", url);
            url = url + "&limit=" + lval;
            window.open(url, "_parent");
        });
    });

</script>

<?php

function showData($user, $rwgetRole, $db_con,$dbHost,$dbUser,$dbPwd, $slpermIdes) {
    ?>
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>S.No</th>
                <th>Contact Name</th>
                <th>Company Name</th>
                <th>Uploaded Memory</th>
                <th>Downloaded Memory</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 1;
            $i += $_GET['start'];
            while ($rwUser = mysqli_fetch_assoc($user)) {
                $dbname = $rwUser['db_name'];
                //db con 
                $totaldocSize = 0;
                $newdb_con = @mysqli_connect($dbHost,$dbUser,$dbPwd, $dbname);
                if (!$newdb_con) {
                    continue;
                }
                //echo "SELECT sum(doc_size) as res FROM `tbl_document_master` WHERE doc_name in ($slpermIdes) and flag_multidelete='1'";
                $qry = mysqli_query($newdb_con, "SELECT sum(doc_size) as res FROM `tbl_document_master` WHERE flag_multidelete='1'");
                $rows = mysqli_fetch_assoc($qry);
                $uploadMemory = round($rows[res] / (1000 * 1000), 2);
                $dmemory = array();
                //echo "select doc_id from tbl_ezeefile_logs WHERE  action_name LIKE '%printed%' OR action_name LIKE '%Downloaded%' and doc_id is NOT NULL and doc_id!='Array' and doc_id!=''";
                $download_qry = mysqli_query($newdb_con, "select doc_id from tbl_ezeefile_logs WHERE  action_name LIKE '%printed%' OR action_name LIKE '%Downloaded%' OR action_name LIKE '%download%' OR action_name LIKE '%Download%' OR action_name LIKE '%view%'  OR action_name LIKE '%viewed%' and doc_id is NOT NULL and doc_id!='Array' and doc_id!=''");
                while ($rowdownload_qry = mysqli_fetch_assoc($download_qry)) {
                    //array_push($dmemory, $rowdownload_qry[doc_id]);
                    $child = mysqli_query($newdb_con, "select sum(doc_size) as file_size FROM tbl_document_master where FIND_IN_SET(doc_id,'$rowdownload_qry[doc_id]')");
                    $rwuploadrpt = mysqli_fetch_assoc($child);
                    //print_r($rwuploadrpt);
                    $totaldocSize += $rwuploadrpt['file_size'];
                }
                /*  $d_memory = implode(",", $dmemory);
                  //echo "SELECT sum(doc_size) as res FROM `tbl_document_master` WHERE doc_id IN($d_memory) and flag_multidelete='1'";
                  $dqry = mysqli_query($newdb_con, "SELECT sum(doc_size) as res FROM `tbl_document_master` WHERE doc_id IN($d_memory) and flag_multidelete='1'");
                  $rowsd = mysqli_fetch_assoc($dqry);

                  $downloadMemory = round($rowsd['res'] / (1000 * 1000), 2); */
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
                <tr class="gradeX">
                    <td><?php echo $i; ?></td>
                    <td><?php echo $rwUser['fname'] . " " . $rwUser['lname']; ?> </td>
                    <td><?php echo $rwUser['company']; ?> </td>
                    <td> <a href="upload-report?db='<?php echo urlencode(base64_encode($dbname)); ?>'&cid='<?php echo urlencode(base64_encode($rwUser['client_id'])); ?>'" class="btn btn-primary btn-xs" title="Total Uploaded Memory"><i class="fa fa-cloud-upload"></i> <?php echo (($uploadMemory > 999) ? round($uploadMemory / 1024, 2) : $uploadMemory) . (($uploadMemory > 999) ? ' GB' : ' MB') ?></a> </td>
                    <td><a href="download-report?db='<?php echo urlencode(base64_encode($dbname)); ?>'&cid='<?php echo urlencode(base64_encode($rwUser['client_id'])); ?>'" class="btn btn-primary btn-xs" title="Total Downloded Memory"> <i class="fa fa-cloud-download"></i> <?php echo $totaldocSize; ?> </a> </td>
                </tr>
                <?php
                $i++;
            }
            ?>
        </tbody>
    </table>
    <?php
}
?>
</body>
</html>
