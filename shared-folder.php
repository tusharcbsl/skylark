<!DOCTYPE html>
<html>
    <?php
    require_once './loginvalidate.php';
    require_once './application/config/database.php';
    require_once './application/pages/head.php';

    if ($rwgetRole['shared_folder'] != '1') {
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
                                        <a href="shared-with-me"><?php echo $lang['list_share_folder_by_you']; ?></a>
                                    </li>
                                     <li> <a href="#" data-toggle="modal" data-target="#help-modal" id="helpview" data="61" title="<?= $lang['help']; ?>"><i class="fa fa-question-circle"></i> </a></li>
                                    <a href="javascript:void(0)" class="btn btn-primary waves-effect waves-light pull-right btn-sm margin-t-9" onclick="goPrevious();" title="<?php echo $lang['Go_to_previous_page']; ?>"><i class="fa fa-arrow-circle-left"></i></a>
                                </ol>
                            </div>
                        </div>

                        <div class="panel">
                            <div class="box box-primary">
                                <div class="panel-body">
                                    <?php
                                   $searchTxt ="";
                                    $loginUser = $_SESSION['cdes_user_id'];
                                    $where = "WHERE fs.share_by='$loginUser'";
                                    if (isset($_GET['shared_doc']) && !empty($_GET['shared_doc'])) {
                                        //$searchTxt = filter_var($_GET['shared_doc'], FILTER_SANITIZE_STRING);
                                        $searchTxt = xss_clean(trim($_GET['shared_doc']));
                                        $where = "WHERE sl.sl_name LIKE '%$searchTxt%'";
                                    }
                                    mysqli_set_charset($db_con, "utf8");
                                    $ShDocId = mysqli_query($db_con, "SELECT sl.sl_id FROM `tbl_folder_share` as fs inner join tbl_storage_level as sl on fs.slId=sl.sl_id left join tbl_user_master as u  on fs.share_with=u.user_id $where"); //or die('Error in share with id fetch' . mysqli_error($db_con));
                                    $foundnum = mysqli_num_rows($ShDocId);
                                    if ($foundnum > 0) {
                                        $limit = preg_replace("/[^0-9 ]/", "", $_GET['limit']);
                                        if (is_numeric($limit)) {
                                            $per_page = $limit;
                                        } else {
                                            $per_page = 10;
                                        }
                                        $start = preg_replace("/[^0-9 ]/", "", $_GET['start']);
                                        $start = isset($start) ? ($start > 0) ? $start : 0 : 0;
                                        $max_pages = ceil($foundnum / $per_page);
                                        if (!$start) {
                                            $start = 0;
                                        }
                                        ?>
                                        <div class="container">

                                            <div class="box-body">
                                                <label><?php echo $lang['Show']; ?> </label>
                                                <select id="limit" class="input-sm m-b-10">
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
                                                </select> <label><?php echo $lang['Documents']; ?></label>
                                                <div class="pull-right record">
                                                    <?php echo $start + 1 ?> <?php echo $lang['To'] ?> <?php
                                                    if (($start + $per_page) > $foundnum) {
                                                        echo $foundnum;
                                                    } else {
                                                        echo ($start + $per_page);
                                                    }
                                                    ?> <span><?php echo $lang['Ttal_Rcrds']; ?> : <?php echo $foundnum; ?></span>
                                                </div>
                                                <table class="table table-striped table-bordered">

                                                    <thead>
                                                        <tr>
                                                            <th class="sort-js-none" ><?php echo $lang['SNO']; ?></th>
                                                            <th class="sort-js-none" ><?php echo $lang['Folder_Name']; ?></th>
                                                            <th class="sort-js-none" ><?php echo $lang['shared_to']; ?></th>
                                                            <th class="sort-js-date" ><?php echo $lang['Shre_Dte']; ?></th>
                                                            <th class="sort-js-none" ><?php echo $lang['Actions']; ?></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $i = 1;
														
														
	mysqli_set_charset($db_con,"utf8");	                                                $ShDocId = mysqli_query($db_con, "SELECT fs.*, sl.sl_name, u.first_name, u.last_name FROM `tbl_folder_share` as fs inner join tbl_storage_level as sl on fs.slId=sl.sl_id left join tbl_user_master as u  on fs.share_with=u.user_id where fs.share_by='$loginUser' order by shared_date desc"); //or die('Error in share id fetch' . mysqli_error($db_con));
                                                        if (mysqli_num_rows($ShDocId) > 0) {
                                                            while ($row = mysqli_fetch_assoc($ShDocId)) {

                                                                $slId = $row['slId'];
                                                                $checkChild = mysqli_query($db_con, "select sl_id from tbl_storage_level where sl_parent_id='$slId'");
                                                                if (mysqli_num_rows($checkChild) > 0) {
                                                                    $storageURL = 'storage?id=' . urlencode(base64_encode($slId));
                                                                } else {
                                                                    $storageURL = 'storageFiles?id=' . urlencode(base64_encode($slId));
                                                                }
                                                                ?>
                                                                <tr>
                                                                    <td><?php echo $i; ?></td>
                                                                    <td><a href="<?php echo $storageURL; ?>"><i class="fa fa-folder" ></i> <?php echo $row['sl_name']; ?></a></td>
                                                                    <td><?php echo $row['first_name'] . ' ' . $row['last_name']; ?></td>
                                                                    <td><?php echo $row['shared_date']; ?></td>
																	 <td class="actions">
                                                                        <a href="#" class="btn btn-danger" data-toggle="modal" data-target="#dialog" id="undoShare" data="<?php echo $row['slId']; ?>"  data1="<?php echo $row['share_with']; ?>" >
                                                                            <i class="fa fa-undo"></i><strong> <?php echo $lang['Undo_Share']; ?></strong></a>
                                                                    </td>
                                                                </tr>
                                                                <?php
                                                                $i++;
                                                            }
                                                            ?>
                                                        </tbody>
                                                        <?php
                                                    } else {
                                                        
                                                    }
                                                    ?>
                                                </table>
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
                                                            echo " <li><a href='?start=$prev&shared_doc=" . $searchTxt . "&limit=" . $per_page . "'>$lang[Prev]</a> </li>";
                                                        else
                                                            echo " <li class='disabled'><a href='javascript:void(0)'>$lang[Prev]</a> </li>";
                                                        //pages 
                                                        if ($max_pages < 7 + ($adjacents * 2)) {   //not enough pages to bother breaking it up
                                                            $i = 0;
                                                            for ($counter = 1; $counter <= $max_pages; $counter++) {
                                                                if ($i == $start) {
                                                                    echo "<li class='active'><a href='?start=$i&limit=$per_page&shared_doc=" . $searchTxt . "'><b>$counter</b></a> </li>";
                                                                } else {
                                                                    echo "<li><a href='?start=$i&limit=$per_page&shared_doc=" . $searchTxt . "'>$counter</a></li> ";
                                                                }
                                                                $i = $i + $per_page;
                                                            }
                                                        } elseif ($max_pages > 5 + ($adjacents * 2)) {    //enough pages to hide some
                                                            //close to beginning; only hide later pages
                                                            if (($start / $per_page) < 1 + ($adjacents * 2)) {
                                                                $i = 0;
                                                                for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++) {
                                                                    if ($i == $start) {
                                                                        echo " <li class='active'><a href='?start=$i&limit=$per_page&shared_doc=" . $searchTxt . "'><b>$counter</b></a></li> ";
                                                                    } else {
                                                                        echo "<li> <a href='?start=$i&limit=$per_page&shared_doc=" . $searchTxt . "'>$counter</a> </li>";
                                                                    }
                                                                    $i = $i + $per_page;
                                                                }
                                                            }
                                                            //in middle; hide some front and some back
                                                            elseif ($max_pages - ($adjacents * 2) > ($start / $per_page) && ($start / $per_page) > ($adjacents * 2)) {
                                                                echo " <li><a href='?start=0&limit=$per_page&shared_doc=" . $searchTxt. "'>1</a></li> ";
                                                                echo "<li><a href='?start=$per_page&limit=$per_page&shared_doc=" . $searchTxt . "'>2</a></li>";
                                                                echo "<li><a href='javascript:void(0)'>...</a></li>";

                                                                $i = $start;
                                                                for ($counter = ($start / $per_page) + 1; $counter < ($start / $per_page) + $adjacents + 2; $counter++) {
                                                                    if ($i == $start) {
                                                                        echo " <li class='active'><a href='?start=$i&limit=$per_page&shared_doc=" . $searchTxt . "'><b>$counter</b></a></li> ";
                                                                    } else {
                                                                        echo " <li><a href='?start=$i&limit=$per_page&shared_doc=" . $searchTxt . "'>$counter</a> </li>";
                                                                    }
                                                                    $i = $i + $per_page;
                                                                }
                                                            }
                                                            //close to end; only hide early pages
                                                            else {
                                                                echo "<li> <a href='?start=0&limit=$per_page&shared_doc=" . $searchTxt . "'>1</a> </li>";
                                                                echo "<li><a href='?start=$per_page&limit=$per_page&shared_doc=" . $searchTxt . "'>2</a></li>";
                                                                echo "<li><a href='javascript:void(0)'>...</a></li>";

                                                                $i = $start;
                                                                for ($counter = ($start / $per_page) + 1; $counter <= $max_pages; $counter++) {
                                                                    if ($i == $start) {
                                                                        echo " <li class='active'><a href='?start=$i&limit=$per_page&shared_doc=" . $searchTxt . "'><b>$counter</b></a></li> ";
                                                                    } else {
                                                                        echo "<li> <a href='?start=$i&limit=$per_page&shared_doc=" . $searchTxt . "'>$counter</a></li> ";
                                                                    }
                                                                    $i = $i + $per_page;
                                                                }
                                                            }
                                                        }
                                                        //next button
                                                        if (!($start >= $foundnum - $per_page))
                                                            echo "<li><a href='?start=$next&shared_doc=" . $searchTxt . "&limit=" . $per_page . "'>$lang[Next]</a></li>";
                                                        else
                                                            echo "<li class='disabled'><a href='javascript:void(0)'>$lang[Next]</a></li>";
                                                        ?>
                                                    </ul>

                                                    <?php
                                                }
                                                echo "</center>";
                                            }
                                            else {
                                                ?>
                                                <table class="table table-striped table-bordered js-sort-table">
                                                    <thead>
                                                        <tr>
                                                            <th><?php echo $lang['SNO']; ?></th>
                                                            <th><?php echo $lang['Folder_Name']; ?></th>
                                                            <th><?php echo $lang['shared_to']; ?></th>
                                                            <th><?php echo $lang['Shre_Dte']; ?></th>
                                                            <th><?php echo $lang['Actions']; ?></th>
                                                        </tr>
                                                    </thead>

                                                    <?php
                                                    if (isset($_GET['shared_doc']) && !empty($_GET['shared_doc'])) {
                                                        echo '<tr><td colspan="5"><label style="font-weight:600; color:red; margin-left: 440px;"><label><strong class="text-danger"><i class="ti-face-sad text-pink"></i> ' . $lang['Who0ps!_No_Records_Found'] . '</strong></label></td></tr>';
                                                    } else {
                                                        echo '<tr><td colspan="5"><label style="font-weight:600; color:red; margin-left: 440px;">' . $lang['Yu_Hv_No_Shred_Folder'] . '</label></td></tr>';
                                                    }
                                                }
                                                ?> 
                                            </table>
                                        </div>
                                    </div>
                                    <!-- end: page -->
                                </div> <!-- end Panel -->
                            </div> <!-- container -->

                        </div> <!-- content -->

                        <?php require_once './application/pages/footer.php'; ?>

                    </div>
                    <!-- ============================================================== -->
                    <!-- End Right content here -->
                    <!-- ============================================================== -->
                    <!-- Right Sidebar -->
                    <?php //require_once './application/pages/rightSidebar.php';    ?>
                    <!-- /Right-bar -->
                </div>
                <!-- END wrapper -->
                <?php require_once './application/pages/footerForjs.php'; ?>
				

                <script src="assets/plugins/select2/js/select2.min.js" type="text/javascript"></script>

                <script type="text/javascript" src="assets/plugins/parsleyjs/parsley.min.js"></script>
				<script>
				//for undo share
                    $("a#undoShare").click(function () {
                        var uid = $(this).attr('data');
                        var userid = $(this).attr('data1');
                        $("#undo").val(uid);
                        $("#userid").val(userid);
                    });
				</script>
				
				<div id="dialog" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
					<div class="modal-dialog"> 
						<div class="panel panel-color panel-danger"> 
							<div class="panel-heading"> 
								<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
								<label><h2 class="panel-title"><?php echo $lang['Are_u_confirm'] ?></h2></label> 
							</div> 
							<form method="post">
								<div class="panel-body">
									<p style="color: red;"><?php echo $lang['Are_you_sure_that_you_want_to_Undo_shared_folder']; ?> ?</p>
								</div>
								<div class="modal-footer">
									<div class="col-md-12 text-right">
										<input type="hidden" id="undo" name="undo">
										<input type="hidden" id="userid" name="userid">
										<button type="submit" name="undoshare" id="dialogConfirm" class="btn btn-danger waves-effect waves-light"><?php echo $lang['confirm'] ?></button>
										<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $lang['Close'] ?></button> 
									</div>
								</div>
							</form>
						</div> 
					</div>
				</div>
                </html>
				
				
	<?php		
	
	if(isset($_POST['undoshare'], $_POST['token'])){
			
		$slId = preg_replace( '/[^0-9]/', '', $_POST['undo']);
		$userid = preg_replace( '/[^0-9]/', '', $_POST['userid']);
		
		if($slId){
			
			$storage = mysqli_query($db_con, "select sl_name from tbl_storage_level where sl_id='$slId'") or die(mysqli_error($db_con));
			$rows = mysqli_fetch_assoc($storage);
			$slname  = $rows['sl_name'];
			
			$userInfo = mysqli_query($db_con, "select first_name, last_name from tbl_user_master where user_id='$userid'") or die(mysqli_error($db_con));
			$rwusername = mysqli_fetch_assoc($userInfo);
			
			$deleteShared = mysqli_query($db_con, "delete from tbl_folder_share where slId='$slId' and share_with='$userid'") or die(mysqli_error($db_con));
			
			if($deleteShared){
				
				$deletePermission = mysqli_query($db_con, "delete from tbl_storagelevel_to_permission where sl_id='$slId' and user_id='$userid'") or die(mysqli_error($db_con));
				$updateStorage = mysqli_query($db_con, "update tbl_storage_level set readonly='0' where sl_id='$slId'") or die(mysqli_error($db_con));
				
				$log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `doc_id`,`action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,'$slId', NULL,'Undo Shared Folder','$date',null,'$host','$slname storage undo shared with $rwusername[first_name] $rwusername[last_name]')") or die(mysqli_error($db_con));
				
				echo'<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '", "' . $lang['Undo_Share_Storage_Successfully'] . ' !!");</script>';
			} else {
				echo '<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['Undo_Share_Storage_failed'] . '")</script>';
			}
			
			
			
		}else{
			
		}
	}