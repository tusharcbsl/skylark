<!DOCTYPE html>
<html>
    <?php
		require_once './loginvalidate.php';
		require_once './application/config/database.php';
		require_once './application/pages/head.php';
		require_once './application/pages/function.php';
		require_once './application/pages/sendSms.php';
		require_once './classes/ftp.php';
		require_once './classes/fileManager.php';
		$fileManager = new fileManager();
	   
		if ($rwgetRole['upload_doc_storage'] != '1') {
			header('Location: ./index');
		}
		$metaDataFiledsIds = "";
		mysqli_set_charset($db_con, "utf8");
		$slid = base64_decode(urldecode(xss_clean($_GET['id'])));
		$slid = preg_replace("/[^0-9]/", "", $slid);
    ?>

    <link href="assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />

    <!--for searchable select-->
    <link href="assets/plugins/bootstrap-select/css/bootstrap-select.min.css" rel="stylesheet" />
    <link href="assets/plugins/jstree/style.css" rel="stylesheet" type="text/css" />
    <body class="fixed-left">

        <!-- Begin page -->
        <div id="wrapper">

            <!-- Top Bar Start -->
            <?php require_once './application/pages/topBar.php'; ?>
            <!-- Top Bar End -->
            <?php require_once './application/pages/sidebar.php'; ?>
            <!-- Left Sidebar End --> 
            <div class="content-page">
                <!-- Start content -->
                <div class="content">
                    <div class="container">

                        <!-- Page-Title -->
                        <div class="row">
                            <?php
								$perm = mysqli_query($db_con, "select * from tbl_storagelevel_to_permission where user_id='$_SESSION[cdes_user_id]' group by sl_id");
								$rwPerm = mysqli_fetch_assoc($perm);
								$slperm = $rwPerm['sl_id'];
								$sllevel = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$slperm'");
								$rwSllevel = mysqli_fetch_assoc($sllevel);
								$level = $rwSllevel['sl_depth_level'];
                            ?>
                            <ol class="breadcrumb">
                                <li><a href="storage?id=<?php echo urlencode(base64_encode($slperm)); ?>"><?php echo $lang['Storage_Management']; ?></a></li>
                                

                                <li class="active"><?php echo $lang['Upld_Docmnt']; ?></li>
                                  <li> <a href="#" data-toggle="modal" data-target="#help-modal" id="helpview" data="45" title="<?= $lang['help']; ?>"><i class="fa fa-question-circle"></i> </a></li>
                                <a href="javascript:void(0)" class="btn btn-primary waves-effect waves-light pull-right btn-sm margin-t-9" onclick="goPrevious();" title="<?php echo $lang['Go_to_previous_page']; ?>"><i class="fa fa-arrow-circle-left"></i></a>
                            </ol>
                        </div>
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <span class="header-title"><?php echo $lang['Slt_Folder']; ?> : </span>
                                <?php
									parentLevel($slid, $db_con, $slpermIdes, $level, $lang, '');
									function parentLevel($slid, $db_con, $slperm, $level, $lang, $value) {

									   // echo $slid.'-->'.$slperm.'-->'.$level.'-->'.$value;
									   // die();
										$flag = 0;
										$slPermIds = explode(',', $slperm);
										if (in_array($slid, $slperm)) {
											$parent = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$slid'") or die('Error' . mysqli_error($db_con));
											$rwParent = mysqli_fetch_assoc($parent);

											if ($level < $rwParent['sl_depth_level']) {
												parentLevel($rwParent['sl_parent_id'], $db_con, $slperm, $level, $lang, $rwParent['sl_name']);
											}
											$flag = 1;
										} 
										else {
											$parent = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$slid' and sl_parent_id='$slperm'") or die('Error' . mysqli_error($db_con));
											if (mysqli_num_rows($parent) > 0) {

												$rwParent = mysqli_fetch_assoc($parent);
												if ($level < $rwParent['sl_depth_level']) {
													parentLevel($rwParent['sl_parent_id'], $db_con, $slperm, $level, $lang, $rwParent['sl_name']);
												}
												$flag = 1;
											} 
											else {
												$parent = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$slid'") or die('Error' . mysqli_error($db_con));
												$rwParent = mysqli_fetch_assoc($parent);
												$getparnt = $rwParent['sl_parent_id'];
												if ($level <= $rwParent['sl_depth_level']) {
													parentLevel($getparnt, $db_con, $slperm, $level, $lang, $rwParent['sl_name']);
													$flag = 1;
												} else {
													$flag = 0;
													//header("Location: ./storage_test?id=" . urlencode(base64_encode($slperm)));
												}
											}
										}
										if ($flag == 1) {
											?>
											<span class="header-title"> <?php
												if (!empty($value)) {
													echo $value = $rwParent['sl_name'] . ' > ';
												} else {
												   echo $value = $rwParent['sl_name'];
												}
											   
												?></span>
										<?php
										}
									}
                                ?>

                            </div>

                            <div class="card-box">

                                <div class="stepwizard">
                                    <div class="stepwizard-row setup-panel">
                                        <div class="stepwizard-step">
                                            <a href="#step-1" type="button" class="btn btn-primary btn-circle">1</a>
                                            <h4><?php echo $lang['DESCRIBES']; ?></h4>
                                        </div>
                                        <div class="stepwizard-step">
                                            <a href="#step-2" type="button" class="btn btn-default btn-circle" disabled="disabled">2</a>
                                            <h4><?php echo $lang['Upload']; ?></h4>
                                        </div>
                                        <div class="stepwizard-step">
                                            <a href="#step-3" type="button" class="btn btn-default btn-circle" disabled="disabled">3</a>
                                            <h4><?php echo $lang['VFY_COMP']; ?></h4>
                                        </div>
                                        <?php if ($rwgetRole['workflow_initiate_file'] == '1' || $rwgetRole['initiate_file'] == '1') { ?>
                                            <div class="stepwizard-step">
                                                <a href="#step-4" type="button" class="btn btn-default btn-circle" disabled="disabled">4</a>
                                                <h4><?php echo $lang['UP_IN_Wf']; ?></h4>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                                <form method="post" enctype="multipart/form-data">
                                    <div class="row setup-content" id="step-1">
                                        <div class="col-xs-12 well wel-done">
                                            <div class="col-md-12 m-t-20">
                                                <?php
                                                $id = preg_replace("/[^A-Za-z0-9, ]/", "", base64_decode(urldecode(@$_GET['id'])));
                                                $id = mysqli_escape_string($db_con, $id);
                                                $mata = "SELECT tmm.field_name,data_type,length_data,mandatory, label, value FROM tbl_metadata_to_storagelevel tms INNER JOIN tbl_metadata_master tmm  ON tms.metadata_id = tmm.id where tms.sl_id='$id'";
                                                $meta_run = mysqli_query($db_con, $mata);
                                                $i = 1;
                                                $k = 1;
                                                while ($rwmeta = mysqli_fetch_assoc($meta_run)) {
                                                    ?>
                                                    <div class="form-group clearfix dv">
                                                        <label class="col-lg-2 control-label " for="metaData<?php echo $i; ?>"><?php echo $rwmeta['field_name']; ?> <?php
                                                            if ($rwmeta['mandatory'] == "Yes") {
                                                                echo '<span style="color:red;">*</span>';
                                                            }
                                                            ?></label>

                                                        <div class="col-lg-10 dev">
                                                            <?php if ($rwmeta['data_type'] == 'datetime') { ?>
                                                                <div class="input-group">
                                                                    <input type="text" class="form-control datetimepicker"  id="metaData<?php echo $i; ?>" name="metaName[]" placeholder="DD-MM-YYYY HH:MM" onchange="datetimeValueChange(<?php echo $i; ?>)" <?php
                                                                    if ($rwmeta['mandatory'] == 'Yes') {
                                                                        echo'required';
                                                                    }
                                                                    ?> value="<?php //echo date('Y-m-d h:m:s'); ?>">
                                                                    <span class="input-group-addon bg-custom b-0 text-white"><i class="icon-calender"></i></span>
                                                                </div>

                                                            <?php } else if ($rwmeta['data_type'] == 'date') { ?>
                                                                <div class="input-group date datepicker" data-link-field="dtp_input1">
                                                                    <input type="text" class="form-control" value="" id="metaData<?php echo $i; ?>" name="metaName[]" placeholder="DD-MM-YYYY" onchange="datetimeValueChange(<?php echo $i; ?>)" <?php
                                                                    if ($rwmeta['mandatory'] == 'Yes') {
                                                                        echo'required';
                                                                    }
                                                                    ?>>

                                                                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                                                                    <span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span>
                                                                </div> 

                                                            <?php } elseif ($rwmeta['data_type'] == 'varchar' || $rwmeta['data_type'] == 'char') { ?>
                                                                <input class="translatetext form-control specialchaecterlock <?= $rwmeta['data_type'] ?> " id="metaData<?php echo $i; ?>" name="metaName[]" type="text"  placeholder="<?php echo $lang['Data_length_exceed']; ?> <?php echo $rwmeta['length_data']; ?> characters." onblur="metaDataChange(<?php echo $i; ?>)" <?php
                                                                if ($rwmeta['mandatory'] == 'Yes') {
                                                                    echo'required';
                                                                }
                                                                ?> maxlength="<?php echo $rwmeta['length_data']; ?>">
                                                                <span id="<?= $rwmeta['data_type'] ?>1" style="color:red"></span>
                                                            <?php } elseif ($rwmeta['data_type'] == 'bit') {
                                                                ?>
                                                                <input class="form-control intvl <?= $rwmeta['data_type'] ?>" id="metaData<?php echo $i; ?>" name="metaName[]" type="text" placeholder="<?php echo $lang['Data_should_be']; ?> <?php echo $rwmeta['length_data']; ?> only." onblur="metaDataChange(<?php echo $i; ?>)" <?php
                                                                if ($rwmeta['mandatory'] == 'Yes') {
                                                                    echo'required';
                                                                }
                                                                ?>> <span id="errormsg" style="color:red"></span>
                                                                   <?php } elseif ($rwmeta['data_type'] == 'Int' || $rwmeta['data_type'] == 'float' || $rwmeta['data_type'] == 'BigInt' || $rwmeta['data_type'] == 'bit') { ?>
                                                                <input class="form-control intvl" id="metaData<?php echo $i; ?>" name="metaName[]" type="text" min="0" placeholder="<?php echo $lang['Data_length_exceed']; ?> <?php echo $rwmeta['length_data']; ?> digits." onblur="metaDataChange(<?php echo $i; ?>)" <?php
                                                                if ($rwmeta['mandatory'] == 'Yes') {
                                                                    echo'required';
                                                                }
                                                                ?> maxlength="<?= isset($rwmeta['length_data']) ? "$rwmeta[length_data]" : '' ?>">

                                                                  <?php } else if ($rwmeta['data_type'] == 'range') {
                                                                       $filedrange = explode(',', $rwmeta['length_data']);
                                                                       ?>
                                                                <input class="form-control intvl" id="metaData<?php echo $i; ?>" name="metaName[]" type="text" minlength="<?= $filedrange[0]; ?>" maxlength="<?= $filedrange[1]; ?>" placeholder="<?php echo $lang['add_enter_range_value']; ?> <?= $filedrange[0] . ' ' . $lang['and'] . ' ' . $lang['enter_max_length'] . ' ' . $filedrange[1] . ' ' . $lang['digits']; ?>" onblur="metaDataChange(<?php echo $i; ?>)" <?php
                                                                if ($rwmeta['mandatory'] == 'Yes') {
                                                                    echo'required';
                                                                }
                                                                ?>>


                                                            <?php } else if ($rwmeta['data_type'] == 'boolean') { ?>
                                                                <input class="form-control intvl intLimit" id="metaData<?php echo $i; ?>" name="metaName[]" type="text"  placeholder="<?php echo $lang['Entr_0_or_1']; ?>" onblur="metaDataChange(<?php echo $i; ?>)" <?php
                                                                if ($rwmeta['mandatory'] == 'Yes') {
                                                                    echo'required';
                                                                }
                                                                ?> maxlength="1">

                                                                <?php
                                                            } else if ($rwmeta['data_type'] == 'list') {
                                                                 $label = $rwmeta['label'];
                                                                $value = $rwmeta['value'];
                                                                $listvalue = explode(',', $rwmeta['value']);
                                                                $labellist = explode(',', $label);
                                                                ?>
                                                                <input type="hidden" class="listval" data-id="<?php echo $i; ?>" id="metaData<?php echo $i; ?>"/>
                                                                <select id="listvalue<?php echo $i; ?>" class="form-control select2" parsley-trigger="change" name="metaName[]" <?php
                                                                if ($rwmeta['mandatory'] == 'Yes') {
                                                                    echo'required';
                                                                }
                                                                ?> onchange="getListvalue('<?php echo $i; ?>')">
                                                                    <option value="" selected><?php echo $lang['Select'] . ' ' . $rwmeta['field_name']; ?></option> 
                                                                    <?php foreach (array_combine($listvalue, $labellist) as $listvalue => $name) { ?>

                                                                        <option value="<?php echo $listvalue; ?>"><?php echo $name; ?></option> 
                                                                    <?php }
                                                                    ?> 
                                                                </select>

                                                                <?php
                                                            } else if ($rwmeta['data_type'] == 'checklist') {
                                                                $checklistvalue = explode(',', $rwmeta['value']);
                                                                $labelchecklist = explode(',', $rwmeta['label']);
                                                                ?>
                                                                <input type="hidden" name="metaName[]" class="form-control <?php echo $rwmeta['field_name']; ?>" id="metaData<?php echo $i; ?>" onblur="metaDataChange(<?php echo $i; ?>)">
                                                                <?php
                                                                $j = 1;
                                                                foreach (array_combine($checklistvalue, $labelchecklist) as $checklistvalue => $name) {
                                                                    ?>
                                                                    <div class="checkbox checkbox-primary m-b-5">
                                                                        <input id="<?= $name . $j; ?>" type="checkbox" onclick="setCheckboxValue(<?php echo $i; ?>, '<?php echo $rwmeta['field_name']; ?>');" name="checkbox<?php echo $i; ?>[]" value="<?php echo $checklistvalue; ?>" onblur="metaDataChange(<?php echo $i; ?>)" <?php
                                                                        if ($rwmeta['mandatory'] == 'Yes') {
                                                                            echo'required';
                                                                        }
                                                                        ?>>
                                                                        <label for="<?= $name . $j; ?>"><?php echo $name; ?></label>

                                                                    </div>
                                                                    <?php
                                                                    $j++;
                                                                }
                                                            } else { ?>

                                                                <input class="translatetext form-control intvl" id="metaData<?php echo $i; ?>" name="metaName[]" type="text" onblur="metaDataChange(<?php echo $i; ?>)" <?php
                                                                if ($rwmeta['mandatory'] == 'Yes') {
                                                                    echo'required';
                                                                }
                                                                ?>>
                                                           <?php } ?>
                                                        </div>
                                                    </div>
                                                    <?php
                                                    $i++;
                                                }
                                                ?>
                                            </div>
                                            <button class="btn btn-primary nextBtn pull-right" type="button" ><?php echo $lang['Next']; ?></button>
                                        </div>
                                    </div>
                                    <div class="row setup-content" id="step-2">
                                        <div class="col-xs-12 mrt well">
                                            <div class="col-md-6 m-t-20">
                                                <input class="filestyle" id="myImage" name="fileName" data-buttonname="btn-primary" id="filestyle-4" style="position: absolute; clip: rect(0px, 0px, 0px, 0px);" tabindex="-1" type="file" required="required">
                                                <input type="hidden" id="pCount" name="pageCount">
                                            </div>
                                            <!--<div class="col-md-6 form-group m-t-20">
                                               <label style="font-weight: 600; font-size: 20px;">(pdf, jpg, png, gif, tif/tiff, mp3, mp4 )</label>
                                           </div>-->
                                        </div>
                                        <button class="btn btn-primary nextBtn pull-right" type="button" id="verify-comp"><?php echo $lang['Next']; ?></button>
                                    </div>
                                    <div class="row setup-content" id="step-3">
                                        <div class="col-xs-12">
                                            <div class="form-group well" style="overflow: auto;">
                                                <table class="table table-bordered  dataTable" cellspacing="0" rules="all" border="1" id="ContentPlaceHolder1_grid" style="border-collapse:collapse;"> 
                                                    <tr>
                                                        <?php
                                                        $id = preg_replace("/[^A-Za-z0-9, ]/", "", base64_decode(urldecode(@$_GET['id'])));
                                                        $id = mysqli_escape_string($db_con, $id);
                                                        $mata = "SELECT tmm.field_name FROM tbl_metadata_to_storagelevel tms INNER JOIN tbl_metadata_master tmm  ON tms.metadata_id = tmm.id where tms.sl_id='$id'";
                                                        $meta_run = mysqli_query($db_con, $mata);

                                                        $i = 1;
                                                        while ($rwmeta = mysqli_fetch_assoc($meta_run)) {
                                                            ?>
                                                            <th scope="col"><div id="bold"><?php echo $rwmeta['field_name']; ?></div> </th>
                                                            <td> <div id="metaVal<?php echo $i; ?>"></div></td>
                                                            <?php
                                                            $i++;
                                                        }
                                                        ?>
                                                    </tr>
                                                </table>
                                            </div>
                                            <div class="form-group well" >
                                                <table class="table table-bordered  dataTable" cellspacing="0" rules="all" border="1" id="ContentPlaceHolder1_grid" style="border-collapse:collapse;">
                                                    <tbody>
                                                        <tr>
                                                            <th scope="col"><?php echo $lang['S_byte']; ?></th><th scope="col"><?php echo $lang['File_Format']; ?></th><th scope="col"><?php echo $lang['File']; ?></th><th scope="col"><?php echo $lang['No_Of_Pages']; ?></th>
                                                        </tr>
                                                        <tr>
                                                            <td> <div id="fileSize"></div></td><td><div id="fileType"></div></td><td><div id="fileName"></div></td><td><div id="pageCount"></div></td>

                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="col-lg-12">
                                                <div class="checkbox checkbox-primary">
                                                    <input id="myCheck" type="checkbox" data-parsley-multiple="groups" data-parsley-mincheck="2" required>
                                                    <label for="myCheck"><?php echo $lang['I_hrby_vrify_te_abve_Docs_nd_prced_to_upld']; ?></label>
                                                </div>
                                                <div id="accept_term" class="form-group" style="display:none;">
                                                    <p class="text-danger">This value is required.</p>
                                                </div>
                                            </div>
                                        </div>
                                        <?php if ($rwgetRole['workflow_initiate_file'] == '1' || $rwgetRole['initiate_file'] == '1') { ?>
                                            <button class="btn btn-primary nextBtn pull-right" type="button" onclick="chk_term()" id="ufw" ><?php echo $lang['UPLD_FIL_IN_workFlow']; ?></button>
                                        <?php } ?>
                                        <input type="submit" style="display: none" id="inufw">
                                        <button class="btn btn-primary pull-right" type="submit" id="btn_primary" name="sub" value="UPLOAD FILE !" onclick="" style="margin-right: 7px;"><?php echo $lang['UPLD_FIL_IN_STRG']; ?> !</button>
                                    </div>
                                    <div class="row setup-content" id="step-4">

                                        <div class="col-xs-12 well">

                                            <div class="col-md-4 m-t-20">

                                                <div class="form-group">

                                                    <label><?php echo $lang['Slt_Wrkflw']; ?> :</label>
                                                    <select class="select2" id="wfid" data-style="btn-white" style="" name="wfid">
                                                        <option selected disabled style="background: #808080; color: #121213;"><?php echo $lang['Slt_Wrkflw']; ?></option>
                                                        <?php
                                                        $getWorkflw = mysqli_query($db_con, "select * from tbl_workflow_master") or die('Error in getWorkflw upload:' . mysqli_error($db_con));
                                                        while ($rwgetWorkflw = mysqli_fetch_assoc($getWorkflw)) {
                                                            ?> 
                                                            <option value="<?php echo $rwgetWorkflw['workflow_id']; ?>"><?php echo $rwgetWorkflw['workflow_name']; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>

                                            </div>
                                            <div id="stp">

                                            </div>

                                        </div>

                                    </div>
                                </form>
                            </div>
                        </div>

                    </div>
                </div> <!-- container -->

            </div> <!-- content -->

            <?php require_once './application/pages/footer.php'; ?>
            <div  style="display:none; text-align: center; color: #fff;  background: rgba(0,0,0,0.5); width: 100%; height: 100%; z-index: 2000; position: fixed; top:0;" id="wait">
                <img src="assets/images/uploading.gif" alt="load"  style="margin-top: 250px; width: 50%;" />
            </div>
        </div>
        <!-- Right Sidebar -->
    </div>
    <?php require_once './application/pages/rightSidebar.php'; ?>
    <!-- /Right-bar -->
    <?php require_once './application/pages/footerForjs.php'; ?>
    <script src="assets/plugins/bootstrap-filestyle/js/bootstrap-filestyle.min.js" type="text/javascript"></script>
    <script src="assets/plugins/select2/js/select2.min.js" type="text/javascript"></script>
    <script type="text/javascript" src="assets/plugins/parsleyjs/parsley.min.js"></script>

    <!--for searchable select -->
    <script type="text/javascript" src="assets/plugins/jquery-quicksearch/jquery.quicksearch.js"></script>
    <script src="assets/plugins/select2/js/select2.min.js" type="text/javascript"></script>
    <script src="assets/plugins/bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>
    <!--Form Wizard-->
    <script src="assets/plugins/jquery.steps/js/jquery.steps.min.js" type="text/javascript"></script>
    <script src="assets/pages/jquery.wizard-init.js" type="text/javascript"></script>
    <script src="assets/plugins/jstree/jstree.min.js"></script>
    <script src="assets/pages/jquery.tree.js"></script>
    <script src="assets/jsCustom/wizard.js"></script>
    <link href="assets/css/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen">
    <script src="assets/moment-with-locales.js"></script>
    <script src="assets/js/bootstrap-datetimepicker.js"></script>

    <script>
$(document).ready(function () {
	$('.datetimepicker').datetimepicker({
		//language:  'fr',
		weekStart: 1,
		todayBtn: 1,
		autoclose: 1,
		todayHighlight: 1,
		startView: 2,
		forceParse: 0,
		showMeridian: 1,
		startDate: '+0d',
		format: 'dd-mm-yyyy hh:ii'
	});
	$('.datepicker').datetimepicker({
		minView: 2,
		autoclose: 1,
		format: "dd-mm-yyyy"
	});
	$(".datetimepicker").keydown(function (e) {
		e.preventDefault();

	});
	$(".datepicker").keydown(function (e) {
		e.preventDefault();

	});
});
    </script>

    <!--text from pdf-->
    <script src="viewer-pdf/build/pdf.js"></script>
    <script src="viewer-pdf/getpdftext.js"></script>    
    <script>

        function getListvalue(id) {
            var x = document.getElementById("listvalue" + id).value;
            $("#metaData" + id).val(x);
            metaDataChange(id);
        }
        function setCheckboxValue(row, fieldname) {
            var metadatavalues = $("input[name='checkbox" + row + "[]']:checked").map(function () {
                return this.value;
            }).get().join(",");
            $("." + fieldname).val(metadatavalues);

        }
		function metaDataChange(val)
		{
			var valInput = $("#metaData" + val).val();
			//alert(valInput);
			$("#metaVal" + val).html(valInput);
			$("#metaValu" + val).html(valInput);
		}

		function datetimeValueChange(Id) {
			var datetimeValue = $("#metaData" + Id).val();
			//alert(datetimeValue);
			$("#metaVal" + Id).html(datetimeValue);
		}
		//image detail              
		$('#myImage').bind('change', function () {
			//this.files[0].size gets the size of your file.
			$("#fileSize").html(this.files[0].size);
			$("#fileName").html(this.files[0].name);
			if (this.files[0].type != '') {
				$("#fileType").html(this.files[0].type);
			} else {
				//sk@131218 : use extension when file type is empty
				var extension = this.files[0].name.replace(/^.*\./, '');
				$("#fileType").html(extension);
			}//end
			//var input = document.getElementById("#myImage");
			if (this.files[0].type == 'application/pdf') {

				var file_data = $('#myImage').prop('files')[0];   
				var form_data = new FormData();                  
				form_data.append('file', file_data);
				form_data.append('action', 'countPdfPage');
				// alert(form_data);                             
				$.ajax({
					url: 'commonController.php', // <-- point to server-side PHP script 
					dataType: 'text',  // <-- what to expect back from the PHP script, if anything
					cache: false,
					contentType: false,
					processData: false,
					data: form_data,                         
					type: 'post',
					success: function(response){
						//console.log(response); // <-- display response from the PHP script, if any
						$("#pageCount").html(response);
						$("#pCount").val(response);
					}
				});

				// previus code

				// var reader = new FileReader();
				// reader.readAsBinaryString(this.files[0]);
				// reader.onloadend = function () {
				//     var count = reader.result.match(/\/Type[\s]*\/Page[^s]/g).length;
				//     $("#pageCount").html(count);
				//     $("#pCount").val(count);
				//     // console.log('Number of Pages:',count );
					
				// }
			} else {
				$("#pageCount").html('1');
				$("#pCount").val('1');
			}
		});

		//script for validate field
		$(".bit").keyup(function () {
			var bitVal = $(this).val();
			if (bitVal == 0 || bitVal == 1)
			{

				$(".nextBtn").removeAttr("disabled", "disabled");
				$("#errormsg").html("");
			} else {
				$(".nextBtn").attr("disabled", "disabled");
				$("#errormsg").html("Invalid Value!Value should be 0 or 1");
			}
		})
		$('.char').keyup(function ()
		{
			var GrpNme = $(this).val();
			re = /[`12345679890~!@#$%^&*()_|+\-=?;:'",.<>\{\}\[\]\\\/]/gi;
			var isSplChar = re.test(GrpNme);
			if (isSplChar)
			{
				var no_spl_char = GrpNme.replace(/[`~!@#$%^&*()_|0-9+\-=?;:'",.<>\{\}\[\]\\\/]/gi, '');
				$(this).val(no_spl_char);
			}
		});
		$('.char').bind(function () {
			$(this).val($(this).val().replace(/[<>]/g, ""))
		});
		//file button validation
		$("#myImage").change(function () {
			var size = document.getElementById("myImage").files[0].size;
			// alert(size);
			var name = document.getElementById("myImage").files[0].name;
			//alert(lbl);
			if (name.length < 100)
			{
				$.post("application/ajax/valiadate_client_memory.php", {size: size}, function (result, status) {
					if (status == 'success') {
						//$("#stp").html(result);
						var res = JSON.parse(result);
						if (res.status == "true")
						{
							// $("#memoryres").html("<span style=color:green>" + res.msg + "</span>");
							$.Notification.autoHideNotify('success', 'top center', 'Success', res.msg)
						} else {
							$.Notification.autoHideNotify('warning', 'top center', 'Oops', res.msg)
							//$("#memoryres").html("<span style=color:red>" + res.msg + "</span>");
						}

					}
				});
			} else {
				var input = $("#myImage");
				var fileName = input.val();

				if (fileName) { // returns true if the string is not empty
					input.val('');
				}
				$.Notification.autoHideNotify('error', 'top center', 'Error', "File Name Too Long");
			}
		});


		(function ($) {
			$.fn.inputFilter = function (inputFilter) {
				return this.on("input keydown keyup mousedown mouseup select contextmenu drop", function () {
					if (inputFilter(this.value)) {
						this.oldValue = this.value;
						this.oldSelectionStart = this.selectionStart;
						this.oldSelectionEnd = this.selectionEnd;
					} else {
						this.value = "";
					}
				});
			};
		}(jQuery));
		$(".intLimit").inputFilter(function (value) {
			return /^\d*$/.test(value) && (value === "" || parseInt(value) <= 1);
		});

    </script>
    <script>

        

        $("#wfid").change(function () {
            var wfId = $(this).val();

            //alert(lbl);
            $.post("application/ajax/workFlstp.php", {wid: wfId}, function (result, status) {
                if (status == 'success') {
                    $("#stp").html(result);
                }
            });
        });
        $("#ufw,#verify-comp").click(function (event) {
            if ($("input#myCheck").is(":checked")) {
                //alert('ok');
                // $("#accept_term").hide();
            } else {
                //$("#accept_term").show();
                //document.querySelector('#inufw').click();
            }
        });

        $("#myCheck").change(function (e)
        {
            chk_term();
        });
        function chk_term() {
            if ($("input#myCheck").is(":checked")) {
                $("#accept_term").hide();
            } else {
                $("#accept_term").show();
            }
        }
        $("input.intvl").keypress(function (e) {
            //if the letter is not digit then display error and don't type anything
            if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57) && e.which != 46) {
                //display error message
                return false;
            }
            str = $(this).val();
            str = str.split(".").length - 1;
            if (str > 0 && e.which == 46) {
                return false;
            }
        });
        $('.select2').select2();

        //sk@242019: disable submit button after one click.
        $(document).ready(function (e) {
            $("button[name='uploaddWfd']").click(function (e) {
                alert('okk');
                $("button[name='uploaddWfd']").prop('disabled', true);
            });
        })



    </script>
<?php
    if (isset($_POST['sub'], $_POST['token'])) {
        require_once './application/config/validate_client_db.php';
        $id = preg_replace("/[^0-9]/", "", base64_decode(urldecode(@$_GET['id'])));
        $id = mysqli_escape_string($db_con, $id);
        $errors = array();
        
        $file_name = $_FILES['fileName']['name'];
        $file_size = $_FILES['fileName']['size'];
        $file_type = $_FILES['fileName']['type'];
        $file_tmp = $_FILES['fileName']['tmp_name'];

        if (isset($_FILES['fileName']['name']) && !empty($_FILES['fileName']['name'])) {

            $allowed = ALLOWED_EXTN;
            $allowext = implode(", ", $allowed);
            $ext = pathinfo($file_name, PATHINFO_EXTENSION);
            if (!in_array(strtolower($ext), $allowed)) {

                echo '<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '", "' . str_replace("ext", $allowext, $lang['document_allowed']) . '")</script>';
                exit();
            }
        }
        $query_validate = mysqli_query($db_con, "select * from `tbl_document_master` where doc_name='$id' and old_doc_name='$file_name' and flag_multidelete=1");
        if (mysqli_num_rows($query_validate) > 0) {
            echo '<script>taskFailed("storage?id=' . $_GET['id'] . '", "Opps!! File already exist")</script>';
        } 
        else {
            $pageCount = preg_replace("/[^A-Za-z0-9,-_@ ]/", "", $_POST['pageCount']);
            $pageCount = mysqli_escape_string($db_con, $pageCount);

            $extn = substr($file_name, strrpos($file_name, '.') + 1);
            $fname = substr($file_name, 0, strrpos($file_name, '.'));

            $metavals = '';
            $columns = '';
            //mu
            // $free_memory = $total_memory_alot - $total_memory_consume + $file_size;
            //MU
            $decKey = decryptLicenseKey($clientKey);
            $decKey = explode("%", $decKey);
            /*
             * validate right user at right time
             
            if ($_SESSION['clientid'] != $decKey[1]) {
                header('Location: ./index');
                exit();
            }
            */

            $check_validity_qry = mysqli_query($db_valid_con, "select * from  tbl_client_master where client_id='$decKey[1]'  and license_key='$clientKey'"); //Query get validity of particular company user
            $validity_date = mysqli_fetch_assoc($check_validity_qry); //fetch validity timestamp from client table
            //$plantype_qry = mysqli_query($db_valid_con, "select * from tbl_plantype where plantype='$validity_date[plan_type]'");
            //$total_memory_allot = mysqli_fetch_assoc($plantype_qry);
            //$size= $total_memory_allot['memory_size'];//total user allow 
            $size = $validity_date['total_memory']; //total user allow 
            $total_memory_alot = convertIntoBytesMethod($size); //total memory converted to bytes
            $total = mysqli_query($db_con, "select sum(doc_size) as totals from `tbl_document_master`");
            $total_fsize = mysqli_fetch_assoc($total);
            $total_memory_consume = $total_fsize['totals']; //total sizes of all files
            //MU
            $free_space_memory = $total_memory_alot - $total_memory_consume;
            $eng_memory = remaingSizeConvert($free_space_memory);
            //if ($total_memory_alot >= ($total_memory_consume + $file_size)) {

            if ($total_memory_alot <= ($total_memory_consume + $file_size)) {

                $uploaded_file_size = remaingSizeConvert($file_size);
                echo '<script>taskFailed("storage?id=' . $_GET['id'] . '", "Opps!! File Size(' . $uploaded_file_size . ') Larger Than Available Storage' . $eng_memory . '")</script>';
            }
            else {
                if(isset($_POST['metaName'])) {
                    if ($total_memory_alot <= ($total_memory_consume + $file_size)) {
                        $uploaded_file_size = remaingSizeConvert($file_size);
                        echo '<script>taskFailed("storage?id=' . $_GET['id'] . '", "Opps!! File Size(' . $uploaded_file_size . ') Larger Than Available Storage' . $eng_memory . '")</script>';
                    }

                    $metaValues = $_POST['metaName'];
                    //foreach ($metaValues as $val){
                    for ($i = 0; $i < count($metaValues); $i++) {

                        $val = $metaValues[$i]; //filter metadata remove special char and space;

                        if (!empty($metavals)) {
                            $metavals = $metavals . ",'" . $val . "'";
                        } else {
                            $metavals = ",'$val'";
                        }
                    }
                    $mata = "SELECT tmm.field_name,tmm.data_type FROM tbl_metadata_to_storagelevel tms INNER JOIN tbl_metadata_master tmm  ON tms.metadata_id = tmm.id where tms.sl_id='$id'";
                    mysqli_set_charset($db_con, "utf8");
                    $meta_run = mysqli_query($db_con, $mata);
                    $i = 1;
                    while ($rwmeta = mysqli_fetch_assoc($meta_run)) {
                        if (!empty($columns)) {
                            $columns = $columns . ',`' . $rwmeta['field_name'] . '`';
                        } else {
                            $columns = ',`' . $rwmeta['field_name'] . '`';
                        }
                        //$colval.$i=$_POST[''];
                        $i++;
                    }
                }

                //$docs_name =  $rwslname['sl_name'];
                $user_id = $_SESSION['cdes_user_id'];
                $name = substr($file_name, 0, strrpos($file_name, '.'));
                $encryptName = urlencode(base64_encode($name));
                $fileExtn = substr($file_name, strrpos($file_name, '.') + 1);


                $strgName = mysqli_query($db_con, "select * from tbl_storage_level where sl_id = '$id'"); //or die('Error:' . mysqli_error($db_con));
                $rwstrgName = mysqli_fetch_assoc($strgName);
                $storageName = $rwstrgName['sl_name'];
                //$storageName = str_replace(" ", "", $storageName);
                $storageName = preg_replace('/[^A-Za-z0-9\-_ ]/', '', $storageName);
                
                
                $updir = getStoragePath($db_con, $rwstrgName['sl_parent_id'], $rwstrgName['sl_depth_level']);
                if(!empty($updir)) {
                    $updir = $updir . '/';
                }
                else{
                    $updir = '';
                }
                $uploaddir = 'extract-here/'.$updir.$storageName.'/';
                if (!is_dir($uploaddir)) {
                    mkdir($uploaddir, 0777, TRUE) or die(print_r(error_get_last()));
                }
                $fname = preg_replace('/[^A-Za-z0-9_\-@]/', '', $fname);
                // $filenameEnct=$fname.'.'.$extn;// urlencode(base64_encode($fname)).'.'.$extn;
                $filenameEnct = urlencode(base64_encode($fname));
                $filenameEnct = preg_replace('/[^A-Za-z0-9_\-@&]/', '', $filenameEnct);
                $filenameEnct = $filenameEnct . '.' . $extn;
                $filenameEnct = time() . $filenameEnct;
                // $upload = move_uploaded_file($file_tmp, $image_path) or die(print_r(error_get_last()));
                $upload = move_uploaded_file($file_tmp, $uploaddir . $filenameEnct); //or die('Error' . print_r(error_get_last()));
                //encrypt_my_file($uploaddir . $filenameEnct);

                $uploadInToFTP = false;

                if($upload) {
                    if (FTP_ENABLED === (boolean) TRUE) {
                        $fileManager->conntFileServer();
                        $uploadInToFTP = $fileManager->uploadFile($uploaddir . $filenameEnct, ROOT_FTP_FOLDER . '/' .$updir. $storageName . '/' . $filenameEnct, false);
                        // $uploadInToFTP = true;
                        /*require_once './classes/ftp.php';
                        $ftp = new ftp();
                        $ftp->conn("$fileserver", "$port", "$ftpUser", "$ftpPwd");
                        $uploadfile = $ftp->put(ROOT_FTP_FOLDER . '/' .$updir. $storageName . '/' . $filenameEnct, $uploaddir . $filenameEnct);
                        $arr = $ftp->getLogData();
                        if ($uploadfile) {
                            $uploadInToFTP = true;
                        } else {
                            $uploadInToFTP = false;
                            echo '<h2>Error:</h2>' . implode('<br />', $arr['error']);
                        }*/
                    } 
                    else {
                        $uploadInToFTP = true;
                    }
                }

                if ($uploadInToFTP) {
                    // Decrypt file
                    decrypt_my_file($uploaddir . $filenameEnct);
                    $doc_path = $updir.$storageName.'/'.$filenameEnct;
                    $query = "INSERT INTO tbl_document_master(doc_name, old_doc_name, doc_extn, doc_path, uploaded_by, doc_size, noofpages $columns , dateposted) VALUES ('$id', '$file_name', '$fileExtn', '$doc_path', '$user_id', '$file_size', '$pageCount' $metavals, '$date')";
                    $exe = mysqli_query($db_con, $query) or die(mysqli_error($db_con));
                    $doc_id = mysqli_insert_id($db_con);
                    if(CREATE_THUMBNAIL){
                        $newdocname = base64_encode($doc_id);
                        //create thumbnail
                        $uploadedfilename = $uploaddir . $filenameEnct;
                        if($extn=='jpg' || $extn=='jpeg' || $extn=='png'){
                            createThumbnail2($uploadedfilename,$newdocname);
                        }
                        elseif($extn=='pdf'){
                            changePdfToImage($uploadedfilename,$newdocname);
                        }
                    }
                    $txtpath = $uploaddir . '/TXT/';
                    if (!is_dir($txtpath)) {
                        mkdir($txtpath, 0777, TRUE) or die(print_r(error_get_last()));
                    }
                    $extractHereDirfile = $uploaddir . $filenameEnct;
                    if (strtolower($extn) == "doc") {
                        $docText = read_doc($extractHereDirfile);
                    } elseif (strtolower($extn) == "docx") {
                        $docText = read_docx($extractHereDirfile);
                    } elseif (strtolower($extn) == "xlsx") {
                        $docText = xlsx_to_text($extractHereDirfile);
                    } elseif (strtolower($extn) == "xls") {
                        //$docText = xls_to_txt($extractHereDirfile);
                    } elseif (strtolower($extn) == "pptx" || strtolower($extn) == "ppt") {
                        $docText = pptx_to_text($extractHereDirfile);
                    } else if(strtolower($extn) == "txt" || strtolower($extn) == "text"){
                        $docText = txt_to_text($extractHereDirfile);
                    }
                    if($docText!=""){
                        $fp = fopen($txtpath . $doc_id . ".txt", "wb");
                        fwrite($fp, $docText);
                        fclose($fp);
                    }
                    if ($exe) {
                        $host = $host . '/' . $_SESSION['custom_ip'];
                        $lip = $host;
                        $ipos = strpos($lip, '/', strpos($lip, '/') + 1);
                        $host = ($ipos ? substr($lip, 0, $ipos) : $lip);
                        
                        $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `doc_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,'$id','$doc_id','Document $file_name Uploaded in $storageName','$date',null,'$host',null)"); //or die('error : ' . mysqli_error($db_con));
                        $img_array = array('jpg', 'jpeg', 'png', 'bmp', 'pnm', 'jfif', 'jpeg', 'tiff');
                        if (strtolower($extn) == 'pdf' || in_array(strtolower($extn), $img_array)) {
                            //getData($doc_id, $uploaddir, $uploaddir . $filenameEnct, $ocrUrl);
                            //gettxtpdf($uploaddir . $filenameEnct, $uploaddir, $doc_id);
                            echo '<script>uploadSuccess("storage?id=' . $_GET['id'] . '", "File Uploaded Successfully!!");</script>';
                        } 
                        else {
                            if (FTP_ENABLED) {
                            // unlink($uploaddir . $filenameEnct);
                            }
                            echo '<script>uploadSuccess("storage?id=' . $_GET['id'] . '", "' . $lang['Fle_Uplded_Sucsfly'] . '");</script>';
                        }
                    } 
                    else {
                        echo '<script>taskFailed("storage?id=' . $_GET['id'] . '", "' . $lang['Op_Fle_upld_fld'] . '")</script>';
                    }
                } 
                else {
                    echo '<script>taskFailed("storage?id=' . $_GET['id'] . '", "' . $lang['Op_Fle_upld_fld'] . '")</script>';
                }
            }
        }
    }
?>

    <?php
    if (isset($_POST['uploaddWfd'], $_POST['token'])) {
        require_once './application/config/validate_client_db.php';
        $wTaskId = $_POST['wtsk'];
        $wTaskId = preg_replace("/[^A-Za-z0-9 ]/", "", $wTaskId);
        $wTaskId = mysqli_real_escape_string($db_con, $wTaskId);
        $wStpId = $_POST['wstp'];
        $wStpId = preg_replace("/[^A-Za-z0-9 ]/", "", $wStpId);
        $wStpId = mysqli_real_escape_string($db_con, $wStpId);
        $wfid = $_POST['wfid'];
        $wfid = preg_replace("/[^A-Za-z0-9 ]/", "", $wfid);
        $wfid = mysqli_real_escape_string($db_con, $wfid);
        $id = base64_decode(urldecode(@$_GET['id']));
        $id = preg_replace("/[^A-Za-z0-9 ]/", "", $id);
        $id = $id . '_' . $wfid;
        $wfd = mysqli_query($db_con, "select * from tbl_workflow_master where workflow_id='$wfid'");
        $rwWfd = mysqli_fetch_assoc($wfd);
        $workFlowName = $rwWfd['workflow_name'];
        $workFlowArray = explode(" ", $workFlowName);
        $ticket = '';
        for ($w = 0; $w < count($workFlowArray); $w++) {
            $name = $workFlowArray[$w];
            $ticket = $ticket . substr($name, 0, 1);
        }
        $taskRemark = mysqli_real_escape_string($db_con, $_POST['taskRemark']);

        $user_id = $_SESSION['cdes_user_id'];
        $ticket = $ticket . '_' . $user_id . '_' . strtotime($date);
        if (isset($_FILES['fileName']['name']) && !empty($_FILES['fileName']['name'])) {
            $allowed = ALLOWED_EXTN;
            $allowext = implode(", ", $allowed);
            $ext = pathinfo($_FILES['fileName']['name'], PATHINFO_EXTENSION);
            if (!in_array(strtolower($ext), $allowed)) {
                echo '<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '", "' . str_replace("ext", $allowext, $lang['document_allowed']) . '")</script>';
                exit();
            }
        }
        $errors = array();
        $file_name = $_FILES['fileName']['name'];
        $file_size = $_FILES['fileName']['size'];
        $file_type = $_FILES['fileName']['type'];
        $file_tmp = $_FILES['fileName']['tmp_name'];
        $pageCount = $_POST['pageCount'];
        $metavals = '';
        $columns = '';
        $decKey = decryptLicenseKey($clientKey);
        $decKey = explode("%", $decKey);
        /*
         * validate right user at right time
         */
        if ($_SESSION['clientid'] != $decKey[1]) {
            header('Location: ./index');
            exit();
        }
        $check_validity_qry = mysqli_query($db_valid_con, "select * from  tbl_client_master where client_id='$decKey[1]'  and license_key='$clientKey'"); //Query get validity of particular company user
        $validity_date = mysqli_fetch_assoc($check_validity_qry); //fetch validity timestamp from client table
        $size = $validity_date['total_memory']; //total user allow 
        $total_memory_alot = convertIntoBytesMethod($size); //total memory converted to bytes
        $total = mysqli_query($db_con, "select sum(doc_size) as totals from `tbl_document_master`");
        $total_fsize = mysqli_fetch_assoc($total);
        $total_memory_consume = $total_fsize['totals']; //total sizes of all files
        //MU
        $free_space_memory = $total_memory_alot - $total_memory_consume;
        $eng_memory = remaingSizeConvert($free_space_memory);
         //if ($total_memory_alot >= ($total_memory_consume + $file_size)) {
        if ($total_memory_alot <= ($total_memory_consume + $file_size)) {
            echo '<script>taskFailed("storage?id=' . $_GET['id'] . '", "Opps!! File Size Large Available Memory Is ' . $eng_memory . '")</script>';
        }else {
            if (isset($_POST['metaName'])) {

                $metaValues = $_POST['metaName'];
                //foreach ($metaValues as $val){
                for ($i = 0; $i < count($metaValues); $i++) {
                    $val = preg_replace("/[^A-Za-z0-9 ]/", "", $metaValues[$i]);
                    if (!empty($metavals)) {
                        $metavals = $metavals . ",'" . $val . "'";
                    } else {
                        $metavals = ",'$val'";
                    }
                }
                $mata = "SELECT tmm.field_name FROM tbl_metadata_to_storagelevel tms INNER JOIN tbl_metadata_master tmm  ON tms.metadata_id = tmm.id where tms.sl_id='$id'";
                mysqli_set_charset($db_con, "utf8");
                $meta_run = mysqli_query($db_con, $mata);
                $i = 1;
                while ($rwmeta = mysqli_fetch_assoc($meta_run)) {
                    if (!empty($columns)) {
                        $columns = $columns . ',`' . $rwmeta['field_name'] . '`';
                    } else {
                        $columns = ',`' . $rwmeta['field_name'] . '`';
                    }
                    //$colval.$i=$_POST[''];
                    $i++;
                }
            }

            //$docs_name =  $rwslname['sl_name'];
            //$user_id = $_SESSION['cdes_user_id'];
            $name = explode(".", $file_name);
            $encryptName = urlencode(base64_encode($name[0]));
            $fileExtn = $name[1];
            $storage = mysqli_query($db_con, "select sl_name from tbl_storage_level where sl_id='$id'"); //or die("Error: ". mysqli_errot($db_con));
            $rwstorage = mysqli_fetch_assoc($storage);
            $slname = str_replace(" ", "", $rwstorage['sl_name']);
            //$image_path = "extract-here/" . $slname . '/' . $file_name;
            //$uploaddir = "extract-here/" . $slname;
            //$filePath = $slname . '/' . $file_name;
            
            $updir = getStoragePath($db_con, $rwstorage['sl_parent_id'], $rwstorage['sl_depth_level']);

            if(!empty($updir)){
                $updir = $updir . '/';
            }else{
                $updir = '';
            }
            $uploaddir = 'extract-here/'.$updir.$slname.'/';
            $image_path = $uploaddir.$file_name;
            $filePath = $updir . '/' . $slname . '/' . $file_name;
            
            if (!is_dir($uploaddir)) {
                mkdir($uploaddir, 0777, TRUE) or die(print_r(error_get_last()));
            }
            $upload = move_uploaded_file($file_tmp, $image_path) or die(print_r(error_get_last()));
           
            // encypt file
           // encrypt_my_file($image_path);

            $uploadInToFTP = false;
            if ($upload){
				/* $fileManager->conntFileServer();
				    $uploadInToFTP = $fileManager->uploadFile($image_path, ROOT_FTP_FOLDER . '/' . $filePath);
			    */
					 
				$uploadInToFTP = true;
            }


            if ($uploadInToFTP) {
                //decrypt file
                //decrypt_my_file($image_path);

                $chkrw = mysqli_query($db_con, "select * from tbl_task_master where workflow_id = '$wfid'"); //or die('Error: ' . mysqli_error($db_con));

                if (mysqli_num_rows($chkrw) > 0) {
                    $query = "INSERT INTO tbl_document_master(doc_name, old_doc_name, doc_extn, doc_path, uploaded_by, doc_size, noofpages $columns , dateposted, workflow_id) VALUES ('$id', '$file_name', '$fileExtn', '$filePath', '$user_id', '$file_size', '$pageCount' $metavals, '$date', '$wfid')";
                    $exe = mysqli_query($db_con, $query); //or die('Error n query failed' . mysqli_error($db_con));
                    $docId = mysqli_insert_id($db_con);
					
					if(CREATE_THUMBNAIL){
                    $newdocname = base64_encode($docId);
                    //create thumbnail
                    if($extn=='jpg' || $extn=='jpeg' || $extn=='png'){
                        createThumbnail2($image_path,$newdocname);
                    }elseif($extn=='pdf'){
                        changePdfToImage($image_path,$newdocname);
                    }
					}
                    if (!empty($wTaskId)) {

                        $getTaskDl = mysqli_query($db_con, "select * from tbl_task_master where task_id='$wTaskId'"); //or die('Error:' . mysqli_error($db_con));
                        $rwgetTaskDl = mysqli_fetch_assoc($getTaskDl);

                        if ($rwgetTaskDl['deadline_type'] == 'Date' || $rwgetTaskDl['deadline_type'] == 'Hrs') {

                            $endDate = date('Y-m-d H:i:s', (strtotime($date) + $rwgetTaskDl['deadline'] * 60 * 60));
                        }
                        if ($rwgetTaskDl['deadline_type'] == 'Days') {

                            $endDate = date('Y-m-d H:i:s', (strtotime($date) + $rwgetTaskDl['deadline'] * 24 * 60 * 60));
                        }

                        $insertInTask = mysqli_query($db_con, "INSERT INTO tbl_doc_assigned_wf(task_id, doc_id, start_date, end_date, task_status, assign_by, ticket_id) VALUES ('$wTaskId', '$docId', '$date', '$endDate', 'Pending', '$user_id', '$ticket')") or die('Erorr:' . mysqli_error($db_con));
                        $idins = mysqli_insert_id($db_con);
                        $getTask = mysqli_query($db_con, "select * from tbl_task_master where task_id = '$wTaskId'"); //or die('Error:' . mysqli_error($db_con));
                        $rwgetTask = mysqli_fetch_assoc($getTask);
                        $TskStpId = $rwgetTask['step_id'];
                        $TskWfId = $rwgetTask['workflow_id'];
                        $TskOrd = $rwgetTask['task_order'];
                        $nextTaskOrd = $TskOrd + 1;
                        nextTaskAsin($nextTaskOrd, $TskWfId, $TskStpId, $docId, $date, $user_id, $db_con, '', $ticket);

                        if ($insertInTask) {
                            require_once './mail.php';
                            $mail = assignTask($ticket, $idins, $db_con, $projectName);

                            if ($mail || $insertInTask) {
								
                                echo '<script>uploadSuccess("storage?id=' . $_GET['id'] . '", "File Uploaded Successfully!!");</script>';
                            }
                        } else {
                            echo '<script>taskFailed("storage?id=' . $_GET['id'] . '", "Opps!! File upload failed")</script>';
                        }
                    } else if (!empty($wStpId)) {

                        $getTask = mysqli_query($db_con, "select * from tbl_task_master where step_id = '$wStpId' ORDER BY task_order ASC LIMIT 1"); //or die('Error:' . mysqli_error($db_con));

                        if (mysqli_num_rows($getTask) > 0) {


                            $getTaskId = mysqli_fetch_assoc($getTask);


                            $tskId = $getTaskId['task_id'];

                            $getTaskDl = mysqli_query($db_con, "select * from tbl_task_master where task_id='$tskId'"); //or die('Error:' . mysqli_error($db_con));
                            $rwgetTaskDl = mysqli_fetch_assoc($getTaskDl);

                            if ($rwgetTaskDl['deadline_type'] == 'Date' || $rwgetTaskDl['deadline_type'] == 'Hrs') {

                                $endDate = date('Y-m-d H:i:s', (strtotime($date) + $rwgetTaskDl['deadline'] * 60 * 60));
                            }
                            if ($rwgetTaskDl['deadline_type'] == 'Days') {

                                $endDate = date('Y-m-d H:i:s', (strtotime($date) + $rwgetTaskDl['deadline'] * 24 * 60 * 60));
                            }

                            $insertInTask = mysqli_query($db_con, "INSERT INTO tbl_doc_assigned_wf(task_id, doc_id, start_date, end_date, task_status, assign_by, ticket_id) VALUES ('$tskId', '$docId', '$date', '$endDate', 'Pending', '$user_id', '$ticket')"); //or die('Erorr:' . mysqli_error($db_con));
                            $idins = mysqli_insert_id($db_con);
                            $getTask = mysqli_query($db_con, "select * from tbl_task_master where task_id = '$tskId'"); //or die('Error:' . mysqli_error($db_con));
                            $rwgetTask = mysqli_fetch_assoc($getTask);
                            $TskStpId = $rwgetTask['step_id'];
                            $TskWfId = $rwgetTask['workflow_id'];
                            $TskOrd = $rwgetTask['task_order'];
                            $nextTaskOrd = $TskOrd + 1;

                            nextTaskAsin($nextTaskOrd, $TskWfId, $TskStpId, $docId, $date, $user_id, $db_con, '', $ticket);
                            if ($insertInTask) {
                                require_once './mail.php';
                                $mail = assignTask($ticket, $idins, $db_con, $projectName);
                                if ($mail || $insertInTask) {

                                    $getTskName = mysqli_query($db_con, "select * from tbl_task_master where task_id = '$tskId' "); //or die('Error' . mysqli_error($db_con));
                                    $rwgetTskName = mysqli_fetch_assoc($getTskName);

                                    echo '<script>uploadSuccess("storage?id=' . $_GET['id'] . '", "File Uploaded Successfully!!");</script>';
                                }
                            } else {
                                echo '<script>taskFailed("storage?id=' . $_GET['id'] . '", "Opps!! File upload failed")</script>';
                            }
                        } else {
                            echo '<script>taskFailed("storage?id=' . $_GET['id'] . '", "There is no task in this step !")</script>';
                        }
                    } else {
                        $getStep = mysqli_query($db_con, "select * from tbl_step_master where workflow_id = '$wfid' ORDER BY step_order ASC LIMIT 1"); //or die('Error:' . mysqli_error($db_con));
                        $getStpId = mysqli_fetch_assoc($getStep);
                        $stpId = $getStpId['step_id'];

                        $getTask = mysqli_query($db_con, "select * from tbl_task_master where step_id = '$stpId' ORDER BY task_order ASC LIMIT 1"); //or die('Error:' . mysqli_error($db_con));
                        $getTaskId = mysqli_fetch_assoc($getTask);
                        $tskId = $getTaskId['task_id'];

                        $getTaskDl = mysqli_query($db_con, "select * from tbl_task_master where task_id='$tskId'"); //or die('Error:' . mysqli_error($db_con));
                        $rwgetTaskDl = mysqli_fetch_assoc($getTaskDl);

                        if ($rwgetTaskDl['deadline_type'] == 'Date' || $rwgetTaskDl['deadline_type'] == 'Hrs') {

                            $endDate = date('Y-m-d H:i:s', (strtotime($date) + $rwgetTaskDl['deadline'] * 60 * 60));
                        }
                        if ($rwgetTaskDl['deadline_type'] == 'Days') {

                            $endDate = date('Y-m-d H:i:s', (strtotime($date) + $rwgetTaskDl['deadline'] * 24 * 60 * 60));
                        }

                        $insertInTask = mysqli_query($db_con, "INSERT INTO tbl_doc_assigned_wf(task_id, doc_id, start_date, end_date, task_status, assign_by, ticket_id) VALUES ('$tskId', '$docId', '$date', '$endDate', 'Pending', '$user_id', '$ticket')"); //or die('Erorr:' . mysqli_error($db_con));
                        $idins = mysqli_insert_id($db_con);
                        $getTask = mysqli_query($db_con, "select * from tbl_task_master where task_id = '$tskId'"); //or die('Error:' . mysqli_error($db_con));
                        $rwgetTask = mysqli_fetch_assoc($getTask);
                        $TskStpId = $rwgetTask['step_id'];
                        $TskWfId = $rwgetTask['workflow_id'];
                        $TskOrd = $rwgetTask['task_order'];
                        $nextTaskOrd = $TskOrd + 1;
                        $chk = nextTaskAsin($nextTaskOrd, $TskWfId, $TskStpId, $docId, $date, $user_id, $db_con, '', $ticket);

                        if ($insertInTask) {
							//die("OKK");
                            require_once './mail.php';
                            $mail = assignTask($ticket, $idins, $db_con, $projectName);
                            if ($mail || $insertInTask) {
                                
                                echo '<script>uploadSuccess("storage?id=' . $_GET['id'] . '", "File Uploaded Successfully!!");</script>';
                            }
                        } else {
                            echo '<script>taskFailed("storage?id=' . $_GET['id'] . '", "Opps!! File upload failed")</script>';
                        }
                    }
                } else {
                    echo '<script>taskFailed("storage?id=' . $_GET['id'] . '", "There is no Task in this Workflow ")</script>';
                }
            }
        }
    }
    ?>  

    <?php

        function getData($docId, $outputDir, $inputDir, $ocrUrl) {
            // echo "docid is".$docId.PHP_EOL;
            // echo "outputDir is".$outputDir.PHP_EOL;
            // echo "inputDir is".$inputDir.PHP_EOL;
            // echo "ocrUrl is".$ocrUrl;
            // exit;
            /**
             * 
             * @param String $url
             * @param Array $params 
             * done by M.U
            */
            $url = BASE_URL . 'ocr_bulk.php';
            //echo $url;
            //echo "Testing ocr";
            //exit;

            $params = array('docId' => $docId, 'outputDir' => $outputDir, 'inputDir' => $inputDir);
            foreach ($params as $key => &$val) {
                if (is_array($val))
                    $val = implode(',', $val);
                $post_params[] = $key . '=' . urlencode($val);
            }
            $post_string = implode('&', $post_params);
            $parts = parse_url($url);
			
			//print_r($parts);die();
            // echo $parts['port'];
            // $fp = fsockopen($parts['host'], isset($parts['port']) ? $parts['port'] : 8080, $errno, $errstr, 3600000000000);
            // $fp = fsockopen("http://localhost:8080/ezeefile_core_application/ocr_bulk.php", 8080, $errno, $errstr, 3600000000000);

            if (isset($_SERVER['HTTPS'])) {
				//echo 'if block';
                $fp = fsockopen('ssl://' . $parts['host'], isset($parts['port']) ? $parts['port'] : 443, $errno, $errstr, 3600);
            } else {
                $fp = fsockopen($parts['host'], isset($parts['port']) ? $parts['port'] : 80, $errno, $errstr, 3600);
				//echo 'else block';
			}
			
			//print_r($parts);
			//echo 'opppppps';
			//var_dump($fp);
			//exit;
            
            // $fp = fsockopen('ssl://' . $parts['host'], isset($parts['port']) ? $parts['port'] : 443, $errno, $errstr, 360000);

            if (!$fp) {
                echo 'socket error';
                echo $errno.'->'.$errstr;
            } 
            else {
                $out = "POST " . $parts['path'] . " HTTP/1.1\r\n";
                $out .= "Host: " . $parts['host'] . "\r\n";
                $out .= "Content-Type: application/x-www-form-urlencoded\r\n";
                $out .= "Content-Length: " . strlen($post_string) . "\r\n";
                $out .= "Connection: Close\r\n\r\n";
                if (isset($post_string))
                    $out .= $post_string;
                //echo $out;
                fwrite($fp, $out);
            }
        }
    ?>
</body>
</html>


