<!DOCTYPE html>
<html>
    <?php
    require_once './loginvalidate.php';
    require_once './application/config/database.php';
    require_once './application/pages/head.php';
    require_once './application/pages/function.php';

    if ($_SESSION['cdes_user_id'] != '1') {
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
            <!-- ========== Left Sidebar Start ========== -->
            <?php require_once './application/pages/sidebar.php'; ?>
            <!-- Left Sidebar End -->
            <!-- ============================================================== -->
            <!-- Start right Content here -->
            <!-- ============================================================== -->
            <div class="content-page">
                <!-- Start content -->
                <div class="content">
                    <!-- Page-Title -->
                    <div class="row">
                        <div class="col-sm-12">
                            <ol class="breadcrumb">
                                <li>
                                    <a href="#"><?php echo $lang['admin_tool']; ?></a>
                                </li>
                                <li class="active">
                                    <a href="sending-email-credential"><?php echo $lang['manage_fileserver_credentials']; ?></a>
                                </li>

                             <!--li> <a href="#" data-toggle="modal" data-target="#help-modal" id="helpview" data="1" title="<?= $lang['help']; ?>"><i class="fa fa-question-circle"></i> </a></li-->
                                <a href="javascript:void(0)" class="btn btn-primary waves-effect waves-light pull-right btn-sm margin-t-9" onclick="goPrevious();" title="<?php echo $lang['Go_to_previous_page']; ?>"><i class="fa fa-arrow-circle-left"></i></a>
                            </ol>
                        </div>
                    </div>
                    <?php
                    $retval = mysqli_query($db_con, "SELECT id FROM  `tbl_file_server_details`") or die('Could not get data: ' . mysqli_error($db_con));
                    $foundnum = mysqli_num_rows($retval);
                    ?>
                    <div class="box box-primary">
                        <div class="panel">
                            <div class="panel-body">
                                <div class="container">
                                    <div class="row">
                                        <?php //if ($rwgetRole['add_email_credential'] == '1' && $foundnum == '0') { ?>
                                            <div class="col-sm-12">
                                                <a href="javascript:void(0)" class="btn btn-primary waves-effect waves-light pull-right" data-toggle="modal" data-target="#email-add"><?php echo $lang['add_fileserver_credentails']; ?> <i class="fa fa-plus"></i></a>
                                            </div>
                                        <?php //} ?>

                                    </div>
                                    <div class="row">
                                        <?php
                                        mysqli_set_charset($db_con, "utf8");
                                        if ($foundnum > 0) {
                                            $StartPoint = preg_replace("/[^0-9]/", "", $_GET['limit']); //filter limit from all special chars
                                            if (is_numeric($StartPoint)) {
                                                $per_page = $StartPoint;
                                            } else {
                                                $per_page = 10;
                                            }
                                            //$start = preg_replace("/[^0-9]/", "", $_GET['start']); //filter start variable
                                            $start = isset($_GET['start']) ? ($_GET['start'] > 0) ? $_GET['start'] : 0 : 0;
                                            $max_pages = ceil($foundnum / $per_page);
                                            if (!$start) {
                                                $start = 0;
                                            }
                                            $limit = $_GET['limit'];
                                            ?>
                                            <div class="box-body">
                                                <label><?php echo $lang['show_lst']; ?> </label> 
                                                <select id="limit" class="input-sm">
                                                    <option value="10" <?php
                                                    if ($limit == 10) {
                                                        echo 'selected';
                                                    }
                                                    ?>>10</option>
                                                    <option value="25" <?php
                                                    if ($limit == 25) {
                                                        echo 'selected';
                                                    }
                                                    ?>>25</option>
                                                    <option value="50" <?php
                                                    if ($limit == 50) {
                                                        echo 'selected';
                                                    }
                                                    ?>>50</option>
                                                    <option value="250" <?php
                                                    if ($limit == 250) {
                                                        echo 'selected';
                                                    }
                                                    ?>>250</option>
                                                    <option value="500" <?php
                                                    if ($limit == 500) {
                                                        echo 'selected';
                                                    }
                                                    ?>>500</option>
                                                </select>   
                                                <label><?php echo $lang['entries_lst']; ?></label>

                                                <div class="pull-right record m-b-15">
                                                    <label><?php echo $start + 1 ?> <?php echo $lang['To']; ?> <?php
                                                        if ($start + $per_page > $foundnum) {
                                                            echo $foundnum;
                                                        } else {
                                                            echo ($start + $per_page);
                                                        }
                                                        ?> <span><?php echo $lang['ttl_recrds']; ?>: <?php echo $foundnum; ?></span></label>
                                                </div>
                                                <?php
                                                mysqli_set_charset($db_con, "utf8");
                                                $emailcre = mysqli_query($db_con, "SELECT * FROM  `tbl_file_server_details` LIMIT $start, $per_page") or die('Error sss:' . mysqli_error($db_con));
                                                showData($emailcre, $rwgetRole, $db_con, $start, $lang);
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
                                                            echo " <li><a href='?start=$prev'>$lang[Prev]</a> </li>";
                                                        else
                                                            echo " <li class='disabled'><a href='javascript:void(0)'>$lang[Prev]</a> </li>";
                                                        //pages 
                                                        if ($max_pages < 7 + ($adjacents * 2)) {   //not enough pages to bother breaking it up
                                                            $i = 0;
                                                            for ($counter = 1; $counter <= $max_pages; $counter++) {
                                                                if ($i == $start) {
                                                                    echo "<li class='active'><a href='?start=$i&limit=$per_page&grpname=" . $_GET['grpname'] . "''><b>$counter</b></a> </li>";
                                                                } else {
                                                                    echo "<li><a href='?start=$i&limit=$per_page&grpname=" . $_GET['grpname'] . "''>$counter</a></li> ";
                                                                }
                                                                $i = $i + $per_page;
                                                            }
                                                        } elseif ($max_pages > 5 + ($adjacents * 2)) {    //enough pages to hide some
                                                            //close to beginning; only hide later pages
                                                            if (($start / $per_page) < 1 + ($adjacents * 2)) {
                                                                $i = 0;
                                                                for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++) {
                                                                    if ($i == $start) {
                                                                        echo " <li class='active'><a href='?start=$i&limit=$per_page&grpname=" . $_GET['grpname'] . "''><b>$counter</b></a></li> ";
                                                                    } else {
                                                                        echo "<li> <a href='?start=$i&limit=$per_page&grpname=" . $_GET['grpname'] . "''>$counter</a> </li>";
                                                                    }
                                                                    $i = $i + $per_page;
                                                                }
                                                            }
                                                            //in middle; hide some front and some back
                                                            elseif ($max_pages - ($adjacents * 2) > ($start / $per_page) && ($start / $per_page) > ($adjacents * 2)) {
                                                                echo " <li><a href='?start=0'>1</a></li> ";
                                                                echo "<li><a href='?start=$per_page'>2</a></li>";
                                                                echo "<li><a href='javascript:void(0)'>...</a></li>";

                                                                $i = $start;
                                                                for ($counter = ($start / $per_page) + 1; $counter < ($start / $per_page) + $adjacents + 2; $counter++) {
                                                                    if ($i == $start) {
                                                                        echo " <li class='active'><a href='?start=$i&limit=$per_page&grpname=" . $_GET['grpname'] . "''><b>$counter</b></a></li> ";
                                                                    } else {
                                                                        echo " <li><a href='?start=$i&limit=$per_page&grpname=" . $_GET['grpname'] . "''>$counter</a> </li>";
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
                                                                        echo " <li class='active'><a href='?start=$i&limit=$per_page&grpname=" . $_GET['grpname'] . "''><b>$counter</b></a></li> ";
                                                                    } else {
                                                                        echo "<li> <a href='?start=$i&limit=$per_page&grpname=" . $_GET['grpname'] . "''>$counter</a></li> ";
                                                                    }
                                                                    $i = $i + $per_page;
                                                                }
                                                            }
                                                        }
                                                        //next button
                                                        if (!($start >= $foundnum - $per_page))
                                                            echo "<li><a href='?start=$next'>$lang[Next]</a></li>";
                                                        else
                                                            echo "<li class='disabled'><a href='javascript:void(0)'>$lang[Next]</a></li>";
                                                        ?>
                                                    </ul>
                                                    <?php
                                                }
                                                echo "</center>";
                                            } else {
                                                ?>
                                                <table class="table table-striped table-bordered m-t-15">
                                                    <thead>
                                                        <tr>
                                                            <th><?php echo $lang['Sr_No']; ?></th>
                                                            <th><?php echo $lang['host']; ?></th>
                                                            <th><?php echo $lang['port_number']; ?></th>
                                                            <th><?php echo $lang['username']; ?></th>
                                                            <th><?php echo $lang['Password']; ?></th>
                                                            <th><?php echo $lang['bucketname']; ?></th>
                                                            <th><?php echo $lang['aws_access_key']; ?></th>
                                                            <th><?php echo $lang['aws_secret_access_key']; ?></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr class="text-center"><td colspan="8"><label><strong class="text-danger"><i class="ti-face-sad text-pink"></i> <?php echo $lang['Who0ps!_No_Records_Found']; ?></strong></label></td></tr>
                                                    </tbody>
                                                </table>
                                            <?php }
                                            ?>	
                                        </div>
                                    </div>
                                    <!-- end: page -->
                                </div> <!-- end Panel -->
                            </div> <!-- container -->

                        </div> <!-- content -->
                    </div>
                    <!-- /Right-bar -->

                    <div id="con-close-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                        <div class="modal-dialog"> 
                            <div class="modal-content"> 

                                <div class="modal-header"> 
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                                    <h4 class="modal-title"><?php echo $lang['edit_fileserver_credentails']; ?></h4> 
                                </div>
                                <form method="post" >
                                    <div class="modal-body" id="editcredential">
                                        <img src="assets/images/load.gif" alt="load" class="img-responsive center-block" style="height: 60px;"/>
                                    </div> 
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-danger" data-dismiss="modal"><?php echo $lang['Close']; ?></button> 
                                        <button type="submit" name="editmail" class="btn btn-primary"><?php echo $lang['Save']; ?></button> 
                                    </div>
                                </form>

                            </div> 
                        </div>
                    </div>
                    <!-- /.modal -->
                    <div id="email-add" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                        <div class="modal-dialog "> 
                            <div class="modal-content"> 
                                <form method="post" >
                                    <div class="modal-header"> 
                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                                        <h4 class="modal-title"><?php echo $lang['add_fileserver_credentails']; ?></h4> 
                                    </div>

                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-sm-12">
												<div class="form-group">
                                                    <label class="text-weight"> <?= $lang['fileserver_type']; ?><span class="text-alert">*</span></label>
                                                    <select class="form-control select2" name="servertype" id="servertype" onchange="checkServerType(this.value);" >
														<option value="">Select File Server Type</option>
														<option value="ftp">FTP Server</option>
														<option value="S3">AWS S3</option>
														<option value="same">Same Server</option>
													</select>
                                                </div>
												<div id="ftpdetails">
                                                <div class="form-group">
                                                    <label class="text-weight"> <?= $lang['host']; ?><span class="text-alert">*</span></label>
                                                    <input type="text" class="form-control" placeholder="<?= $lang['host_name']; ?>" name="hostname" required="" maxlength="30"/>
                                                </div>
                                                <div class="form-group">
                                                    <label class="text-weight"> <?= $lang['port_number']; ?><span class="text-alert">*</span></label>
                                                    <input type="text" class="form-control" placeholder="<?= $lang['port_number']; ?>" name="portnumber" required="" maxlength="10"/>
                                                </div>
                                                <div class="form-group">
                                                    <label class="text-weight"> <?= $lang['username']; ?><span class="text-alert">*</span></label>
                                                    <input type="text" class="form-control" placeholder="<?= $lang['username']; ?>" name="username" required="" maxlength="50"/>
                                                </div>
                                                <div class="form-group">
                                                    <label class="text-weight"> <?= $lang['Password']; ?><span class="text-alert">*</span></label>
                                                    <input type="password" class="form-control" placeholder="<?= $lang['Password']; ?>" name="pwd" required="" maxlength="40"/>
                                                </div>
												</div>
												<div id="s3details" style="display:none;">
													<div class="form-group ">
														<label class="text-weight"> <?= $lang['bucketname']; ?><span class="text-alert">*</span></label>
														<input type="text" class="form-control" placeholder="<?= $lang['bucketname']; ?>" name="bucketname" required="" maxlength="50"/>
													</div>
													<div class="form-group">
														<label class="text-weight"> <?= $lang['aws_access_key']; ?><span class="text-alert">*</span></label>
														<input type="password" class="form-control" placeholder="<?= $lang['aws_access_key']; ?>" name="aws_access_key" required="" maxlength="30"/>
													</div>
													<div class="form-group">
														<label class="text-weight"> <?= $lang['aws_secret_access_key']; ?><span class="text-alert">*</span></label>
														<input type="password" class="form-control" placeholder="<?= $lang['aws_secret_access_key']; ?>" name="aws_secret" required="" maxlength="40"/>
													</div>
												</div>
                                            </div>
                                        </div>

                                    </div> 
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button>
                                        <button type="submit" name="setcredential" class="btn btn-primary waves-effect waves-light"><?php echo $lang['Submit']; ?></button>
                                    </div>
                                </form>

                            </div> 
                        </div>
                    </div><!-- /.modal -->
					
					
			<div id="activate" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
				<div class="modal-dialog"> 
					<div class="panel panel-color panel-danger"> 
						<div class="panel-heading"> 
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
							<label><h2 class="panel-title"><?= $lang['Are_u_confirm'] ?>?</h2></label> 
						</div> 
						<form method="post">
							<div class="panel-body">
								<p class="text-danger"><?php echo $lang['Are_you_sure_that_you_want_to_disable_this_detail'] ?> ? </p>
							</div>
							<div class="modal-footer">
								<div class="col-md-12 text-right">
									<input type="hidden" id="dectiveId" name="recoredId">
									<button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close'] ?></button> 
									<button type="submit" name="disable" id="dialogConfirm" class="btn btn-danger waves-effect waves-light"><?= $lang['confirm'] ?></button>
								</div>
							</div>
						</form>
					</div> 
				</div>
			</div>

            <div id="deactivate" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                <div class="modal-dialog"> 
                    <div class="panel panel-color panel-success"> 
                        <div class="panel-heading"> 
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                            <label><h2 class="panel-title"><?= $lang['Are_u_confirm'] ?>?</h2></label> 
                        </div> 
                        <form method="post" >
                            <div class="panel-body">
                                <p class="text-danger"><?= $lang['Are_you_sure_that_you_want_to_enable_this_detail'] ?> ? </p>
                            </div>
                            <div class="modal-footer">
                                <div class="col-md-12 text-right">
                                    <input type="hidden" id="activateId" name="recoredId" value="">
                                    <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal"><?= $lang['Close'] ?></button> 
                                    <button type="submit" name="enable" id="dialogConfirm" class="btn btn-success waves-effect waves-light"><?= $lang['confirm'] ?></button>

                                </div>
                            </div>
                        </form>
                    </div> 
                </div>
            </div>
			
			
                    <!-- /.modal -->
                    <!-- END wrapper -->
                    <?php require_once './application/pages/footer.php'; ?>

                </div>
                <!-- ============================================================== -->
                <!-- End Right content here -->
                <!-- ============================================================== -->
                <!-- Right Sidebar -->
                <?php //require_once './application/pages/rightSidebar.php';   ?>
                <?php require_once './application/pages/footerForjs.php'; ?>

                <script src="assets/plugins/select2/js/select2.min.js" type="text/javascript"></script>

                <script type="text/javascript" src="assets/plugins/parsleyjs/parsley.min.js"></script>
                <script type="text/javascript">
                                    $(document).ready(function () {
                                        $('form').parsley();
                                        $('#groupName').on("cut copy paste", function (e) {
                                            e.preventDefault();
                                        });
                                    });
                                    $(".select2").select2();
                </script>
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

                    $("a#editRow").click(function () {
                        var id = $(this).attr('data');
                        var token = $("input[name='token']").val();
                        $.post("application/ajax/edit-server-details.php", {ID: id, token: token}, function (result, status) {
                            if (status == 'success') {
                                $("#editcredential").html(result);
								$(".select3").select2();
								editCheckServerType();
                                getToken();
                            }
                        });

                    });
					
					function checkServerType(serverType){
						if(serverType=="S3"){
							$("#ftpdetails").hide();
							$("#s3details").show();
							$("#ftpdetails input").attr("required", false);
							$("#s3details input").attr("required", true);
						}else{
							$("#s3details").hide();
							$("#ftpdetails").show();
							$("#ftpdetails input").attr("required", true);
							$("#s3details input").attr("required", false);
							
						}
					}
					
					 $("a#active").click(function () {
						var id = $(this).attr('data');
						$("#activateId").val(id);

					});
					$("a#dective").click(function () {
						var id = $(this).attr('data');
						$("#dectiveId").val(id);
					});
					
					
                </script>  

                <script type="text/javascript">

                    $(document).ready(function () {
                        $('form').parsley();
                    });
                </script>

				
				
                <?php

                function showData($email, $rwgetRole, $db_con, $start, $lang) {
                    ?>

                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th><?php echo $lang['Sr_No']; ?></th>
                                <th><?php echo $lang['fileserver_type']; ?></th>
                                <th><?php echo $lang['ftp_credentials']; ?></th>
                                <th><?php echo $lang['aws_s3_credentails']; ?></th>
                                <th><?php echo $lang['Actions']; ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 1;
                            $i += $start;
                            while ($rwEmail = mysqli_fetch_assoc($email)) {
								
								 $password = ezeefile_crypt($rwEmail['password'], 'd');
								 $access_key = ezeefile_crypt($rwEmail['access_key'], 'd');
								
								$secret_access_key = explode(",", $rwEmail['secret_access_key']);
								$secret_access_key = ezeefile_crypt($secret_access_key[0], 'd').ezeefile_crypt($secret_access_key[1], 'd');
								 
                                ?>
                                <tr class="gradeX">
                                    <td><?php echo $i . '.'; ?></td>
                                    <td><?php echo ucfirst($rwEmail['servertype']); ?></td>
                                    <td>
										<?php 
										if($rwEmail['servertype']=="ftp"){
											echo $lang['host'].': '. $rwEmail['host'].'<br>'; 
											echo $lang['Port'].': '. $rwEmail['port'].'<br>'; 
											echo $lang['username'].': '.  ezeefile_crypt($rwEmail['username'], 'd').'<br>'; 
											echo $lang['Password'].': '.  substr($password, 0, 2).str_repeat('*', strlen($password) - 2).substr($password, strlen($password) - 2, 2); 
										}
										?>
									</td>
                                   
                                    <td>
									<?php 
									if($rwEmail['servertype']!="ftp"){
										echo $lang['bucketname'].': ' . $rwEmail['bucket_name'].'<br>';
										echo $lang['aws_access_key'].': ' . substr($access_key, 0, 2).str_repeat('*', strlen($access_key) - 2).substr($access_key, strlen($access_key) - 2, 2).'<br>'; 
										echo $lang['aws_secret_access_key'].': ' . substr($secret_access_key, 0, 2).str_repeat('*', strlen($secret_access_key) - 2).substr($secret_access_key, strlen($secret_access_key) - 2, 2); 
									}
									?>
									</td>
                                    <td class="actions">
                                        <?php if ($rwgetRole['edit_email_credential'] == '1') { ?>
                                            <a href="#" class="on-default edit-row btn btn-primary" data-toggle="modal" data-target="#con-close-modal" id="editRow" data="<?php echo $rwEmail['id']; ?>" title="<?php echo $lang['Modify_column']; ?>"><i class="fa fa-edit"></i> </a> &nbsp;&nbsp;
                                        <?php } ?>
										
										

										 <?php if ($rwEmail['status'] == 1) { ?>
											<a href="#" class="on-default edit-row btn btn-success" data-toggle="modal" id="dective" data-target="#activate" data="<?php echo $rwEmail['id']; ?>" title="<?php echo $lang['disable']; ?>"><i class="fa fa-toggle-on"></i></a>
										<?php } else { ?>
											<a href="#" class="on-default edit-row btn btn-danger" data-toggle="modal" data-target="#deactivate" id="active" data="<?php echo $rwEmail['id']; ?>" title="<?php echo $lang['disable']; ?>"><i class="fa fa-toggle-off"></i></a>
											<?php
										} ?>

                                    </td>
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
				
				
				<?php
                if (isset($_POST['setcredential'], $_POST['token'])) {
					
					$servertype = preg_replace("/[^a-zA-Z0-9]/", "", $_POST['servertype']);
                    $hostname = preg_replace("/[^0-9.]/", "", $_POST['hostname']);
                    $portnumber = preg_replace("/[^0-9]/", "", $_POST['portnumber']);
					$username = "";
					$password ="";
					if($_POST['servertype']=="ftp"){
						$username = ezeefile_crypt(preg_replace("/[^a-zA-Z0-9_@.-]/", "", $_POST['username']));
						$password = ezeefile_crypt($_POST['pwd']);
					}
					
					$bucketname = xss_clean($_POST['bucketname']);
					
					$aws_access_key = ezeefile_crypt(xss_clean($_POST['aws_access_key']));
					
					$aws_secret_access_key = xss_clean($_POST['aws_secret']);
					if($aws_secret_access_key!=""){
						
						$aws_secret_access_key  = ezeefile_crypt(substr($aws_secret_access_key, 0, 20)).','. ezeefile_crypt(substr($aws_secret_access_key, 20, 40));
					}
					
                    $insert = mysqli_query($db_con, "INSERT INTO `tbl_file_server_details` (`host`, `port`, `username`, `password`, `bucket_name`, `access_key`, `secret_access_key`, `servertype`) VALUES ('$hostname','$portnumber','$username','$password','$bucketname', '$aws_access_key', '$aws_secret_access_key', '$servertype')") or die('error : while insert file server details' . mysqli_error($db_con));
                    if ($insert) {
                        $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`,`action_name`, `start_date`,`system_ip`, `remarks`) values ('$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]','File server credential added','$date','$host', 'File server credential added')") or die('error : ' . mysqli_error($db_con));
                        echo'<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['details_added_successfully'] . '");</script>';
                    } else {
                        echo'<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['failed_to_add_details'] . '");</script>';
                    }
                }
                ?>
                <?php
                if (isset($_POST['editmail'], $_POST['token'])) {
					
                    $id = preg_replace("/[^0-9]/", "", $_POST['id']);
					
                    $servertype = preg_replace("/[^a-zA-Z0-9]/", "", $_POST['servertype']);
                    $hostname = preg_replace("/[^0-9.]/", "", $_POST['hostname']);
                    $portnumber = preg_replace("/[^0-9]/", "", $_POST['portnumber']);
					$username = "";
					$password ="";
					if($_POST['servertype']=="ftp"){
						$username = ezeefile_crypt(preg_replace("/[^a-zA-Z0-9_@.-]/", "", $_POST['username']));
						$password = ezeefile_crypt($_POST['pwd']);
					}
					
					$bucketname = xss_clean($_POST['bucketname']);
					
					$aws_access_key = ezeefile_crypt(xss_clean($_POST['aws_access_key']));
					
					$aws_secret_access_key = xss_clean($_POST['aws_secret']);
					
					if($aws_secret_access_key!=""){
						
						$aws_secret_access_key  = ezeefile_crypt(substr($aws_secret_access_key, 0, 20)).','. ezeefile_crypt(substr($aws_secret_access_key, 20, 40));
					}
				   
                    $updated = mysqli_query($db_con, "UPDATE `tbl_file_server_details` SET `host`='$hostname', `port`='$portnumber', `username`='$username', `password`='$password', `bucket_name`='$bucketname', `access_key`='$aws_access_key', `secret_access_key`='$aws_secret_access_key', servertype='$servertype'  WHERE id='$id'") or die('error : set user email password ' . mysqli_error($db_con));
                    if ($updated) {
                        $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`,`action_name`, `start_date`,`system_ip`, `remarks`) values ('$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]','credential updated','$date','$host', 'File server credential updated')") or die('error : ' . mysqli_error($db_con));
                       echo'<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['details_updated_successfully'] . '");</script>';
                    } else {
                        echo'<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['failed_to_update_details'] . '");</script>';
                    }
                }
				
				
				
				
				if (isset($_POST['enable'], $_POST['token'])) {
					$id = mysqli_escape_string($db_con, $_POST['recoredId']);
					$id = preg_replace("/[^0-9]/", "", $id);
					$enabled = mysqli_query($db_con, "update tbl_file_server_details set status='1' where id = '$id'"); //or die('Error:' . mysqli_error($db_con));
					if ($enabled) {
						$enabled = mysqli_query($db_con, "update tbl_file_server_details set status='0' where id != '$id'"); //or die('Error:' . mysqli_error($db_con));

						 $ress = mysqli_query($db_con, "select servertype from tbl_file_server_details where id='$id'");
						 $rl_row = mysqli_fetch_array($ress);
						 $servertype = $rl_row['servertype'];

						$log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`,`action_name`, `start_date`, `system_ip`, `remarks`) values ('$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]','Enabled','$date','$host','$servertype file server details enabled.')") or die('error1 : ' . mysqli_error($db_con));


						echo '<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['details_enabled_succesufully'] . '");</script>';
					}
					mysqli_close($db_con);
				}

				if (isset($_POST['disable'], $_POST['token'])) {
					$id = mysqli_escape_string($db_con, $_POST['recoredId']);
					$id = preg_replace("/[^0-9]/", "", $id);
					$disabled = mysqli_query($db_con, "update tbl_file_server_details set status='0' where id = '$id'"); //or die('Error:' . mysqli_error($db_con));
					if ($disabled) {
						
						

						$ress = mysqli_query($db_con, "select servertype from tbl_file_server_details where id='$id'");
						 $rl_row = mysqli_fetch_array($ress);
						 $servertype = $rl_row['servertype'];

						$log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`,`action_name`, `start_date`, `system_ip`, `remarks`) values ('$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]','Disabled','$date','$host','$servertype file server details disabled.')") or die('error1 : ' . mysqli_error($db_con));

						echo '<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['details_disabled_succesufully'] . '");</script>';
					}

					mysqli_close($db_con);
				}


                ?>
				
				

                </body>
                </html>