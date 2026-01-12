<!DOCTYPE html>
<html>
    <?php
    require_once './loginvalidate.php';
    require_once './application/config/database.php';
    require_once './application/pages/head.php';
    require_once './application/pages/function.php';

    if ($rwgetRole['workflow_task_track'] != '1') {
        header('Location: ./index');
    }

    $Uid = $_SESSION['cdes_user_id'];
	 
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
                                    <li><a href="#"><?php echo $lang['WORKFLOW_MANAGEMENT']; ?></a></li>
                                    <li>
                                        <a href="taskTrack"><?php echo $lang['trck_yr_tsk_status']; ?></a>
                                    </li>
                                    <a href="javascript:void(0)" class="btn btn-primary waves-effect waves-light pull-right btn-sm margin-t-9" onclick="goPrevious();" title="<?php echo $lang['Go_to_previous_page']; ?>"><i class="fa fa-arrow-circle-left"></i></a>
                                </ol>
                            </div>
                        </div>
                        <div class="box box-primary">
                            <div class="panel">
                                <div class="panel-body">
                                    <div class="col-sm-12">
                                        <form method="get">
                                            <div class="col-sm-3">
                                                <input type="text" class="form-control" name="tktid" value="<?php echo xss_clean($_GET['tktid']); ?>" parsley-trigger="change" placeholder="<?php echo $lang['Ticket_Id'] . " " . $lang['seerchsingle']; ?>"  />
                                            </div>
                                            <div class="col-sm-3">
                                                <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> <?php echo $lang['Search']; ?></button>
                                                <a href="taskTrack" class="btn btn-warning"><i class="fa fa-refresh"></i> <?php echo $lang['Reset']; ?></a>
                                            </div>

                                        </form>
                                    </div>
                                    <div class="container">
                                        <?php
                                        $where = "";
                                        if (in_array("1", $privileges) && $_SESSION['cdes_user_id'] == 1) {
                                            if (isset($_GET['tktid']) && !empty($_GET['tktid'])) {
                                                $ticketId = xss_clean($_GET['tktid']);
                                                $where .= "where ticket_id like '%$ticketId%'";
                                            }
                                        } else {
                                            if (isset($_GET['tktid']) && !empty($_GET['tktid'])) {
                                                $ticketId = xss_clean($_GET['tktid']);
                                                $where .= "and ticket_id like '%$ticketId%'";
                                            }
                                        }
                                        if (in_array("1", $privileges) && $_SESSION['cdes_user_id'] == 1) {

                                            $taskTrack = "SELECT * FROM  tbl_doc_assigned_wf $where group by ticket_id order by id desc";
                                        } else {
                                            $taskTrack = "SELECT * FROM  tbl_doc_assigned_wf where assign_by = '$Uid' $where group by ticket_id order by id desc";
                                        }
                                        $run = mysqli_query($db_con, $taskTrack) or die('Error' . mysqli_error($db_con));

                                        $foundnum = mysqli_num_rows($run);
                                        if ($foundnum > 0) {
                                            if (is_numeric($_GET['limit'])) {
                                                $per_page = preg_replace("/[^0-9]/", "", $_GET['limit']);
                                            } else {
                                                $per_page = 10;
                                            }
                                            $start = isset($_GET['start']) ? ($_GET['start'] > 0) ? $_GET['start'] : 0 : 0;
                                            $max_pages = ceil($foundnum / $per_page);
                                            if (!$start) {
                                                $start = 0;
                                            }
                                            $limit = $_GET['limit'];
                                            ?>
                                            <div class="box-body">
                                                <label><?php echo $lang['show_lst']; ?></label>
                                                <select id="limit" class="input-sm m-t-10 m-b-10">
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
                                                <label><?php echo $lang['Task_Track_Status']; ?></label>
                                                <div class="pull-right record m-t-10">
                                                    <?php echo $start + 1 ?> <?php echo $lang['To'] ?> <?php
                                                    if ($start + $per_page > $foundnum) {
                                                        echo $foundnum;
                                                    } else {
                                                        echo ($start + $per_page);
                                                    }
                                                    ?> <span><?php echo $lang['ttl_recrds']; ?> : <?php echo $foundnum; ?></span>
                                                </div>
                                                <?php
                                                if (in_array("1", $privileges) && $_SESSION['cdes_user_id'] == 1) {

                                                    $taskTrack = "SELECT * FROM  tbl_doc_assigned_wf $where group by ticket_id order by id desc LIMIT $start, $per_page";
                                                } else {
                                                    $taskTrack = "SELECT * FROM  tbl_doc_assigned_wf where assign_by = '$Uid' $where group by ticket_id order by id desc LIMIT $start, $per_page";
                                                }
                                                $run = mysqli_query($db_con, $taskTrack) or die('Error' . mysqli_error($db_con));
                                                ?>
                                                <table class="table table-striped table-bordered js-sort-table">
                                                    <thead>
                                                        <tr>
                                                            <th class="sort-js-none" ><?php echo $lang['SNO']; ?></th>
                                                            <th><?php echo $lang['Actions']; ?></th>
                                                            <th><?php echo $lang['Ticket_Id']; ?></th>
                                                            <th><?php echo $lang['Document_description']; ?></th>
                                                            <th ><?php echo $lang['Task_Track_Status']; ?></th>
                                                            <th class="sort-js-date" ><?php echo $lang['Ticket_Date_Time']; ?></th>
                                                            <th class="sort-js-none" ><?php echo $lang['Actions']; ?></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody >
                                                        <?php
                                                        $i = 1;
                                                        $i += $start;
                                                        while ($rwTaskDoc = mysqli_fetch_assoc($run)) {
                                                            ?>
                                                            <tr class="gradeX" style="vertical-align: middle;">
                                                                <td><?php echo $i . '.'; ?></td>
                                                                <td><a data-toggle="modal" id="tracktsk" data-target="#trackTask" data="<?php echo $rwTaskDoc['ticket_id']; ?>"> <i class="glyphicon glyphicon-new-window task" title="<?= $lang['task_track'] ?>"></i></a></td>
                                                                <td style="color:#797979;;"><?php echo $rwTaskDoc['ticket_id']; ?></td>
                                                                <td>

                                                                    <?php if(file_exists('thumbnail/'.base64_encode($rwTaskDoc['doc_id']).'.jpg')){ ?>
                                                                            <div> <img class="thumb-image" src="thumbnail/<?=base64_encode($rwTaskDoc['doc_id'])?>.jpg"> </div>
                                                                        <?php } ?>


                                                                    <?php
                                                                    if (!empty($rwTaskDoc['doc_id'])) {
                                                                        $doc = mysqli_query($db_con, "select * from tbl_document_master where doc_id='$rwTaskDoc[doc_id]'") or die('Error' . mysqli_error($db_con));
                                                                        $rwDoc = mysqli_fetch_assoc($doc);
                                                                        ?>
                                                                        <?php
                                                                        if (!empty($rwDoc['doc_path'])) {
                                                                            //@sk(221118): include view handler to handle different file formats
                                                                            echo substr($rwDoc['old_doc_name'], 0, 20);
                                                                            $file_row = $rwDoc;
                                                                            require 'view-handler.php';
                                                                        }
                                                                        ?>
                                                                        <?php
                                                                        $docName = $file_row['doc_name'];
                                                                        $docName = explode("_", $docName);
                                                                        $updateDocName = $docName[0] . '_' . $file_row['doc_id'] . ((!empty($docName[1])) ? '_' . $docName[1] : '');
                                                                        $fileVersion = mysqli_query($db_con, "SELECT * FROM `tbl_document_master` WHERE doc_name='$updateDocName' ") or die('Error:' . mysqli_error($db_con));
                                                                        while ($rwfileVersion = mysqli_fetch_assoc($fileVersion)) {
                                                                            ?>
                                                                            <div>

                                                                                 <?php if(file_exists('thumbnail/'.base64_encode($rwfileVersion['doc_id']).'.jpg')){ ?>
                                                                            <div> <img class="thumb-image" src="thumbnail/<?=base64_encode($rwfileVersion['doc_id'])?>.jpg"> </div>
                                                                        <?php } ?>

                                                                        
                                                                                <?php
                                                                                //versioning view start here
                                                                                echo substr($rwfileVersion['old_doc_name'], 0, 20);
                                                                                $file_row = $rwDoc;
                                                                                $file_row = $rwfileVersion;
                                                                                require 'view-handler.php';
                                                                                
                                                                            }
                                                                            ?>
                                                                        </div>
                                                                        <?php
                                                                    } else {
                                                                        if (!empty($rwTaskDoc['task_remarks'])) {
                                                                            ?>
                                                                            <a href="#" data-toggle="modal" data-target="#taskdescription" id="ViewTsk" data="<?php echo $rwTaskDoc['id']; ?>" title="View Task Description"> Task Description <i class="fa fa-eye"></i></a>
                                                                            <div style="display: none" id="<?php echo $rwTaskDoc['id']; ?>"><?php echo $rwTaskDoc['task_remarks']; ?></div>
                                                                            <?php
                                                                        } else {
                                                                            echo'<span class="label label-primary">Task have No Document / Description</span>';
                                                                        }
                                                                    }
                                                                    ?> 
                                                                </td>

                                                                <?php
                                                                $checkRj = mysqli_query($db_con, "select * from tbl_doc_assigned_wf where ticket_id='$rwTaskDoc[ticket_id]' and task_status='Rejected'");
                                                                $num = mysqli_num_rows($checkRj);
                                                                if ($num == 1) {
                                                                    ?>
                                                                    <td><span class="label label-danger">Rejected</span></td> 
                                                                    <?php
                                                                } else {
                                                                    $checkRj = mysqli_query($db_con, "select * from tbl_doc_assigned_wf where ticket_id='$rwTaskDoc[ticket_id]' order by id desc");
                                                                    $rwCheckrj = mysqli_fetch_assoc($checkRj);
                                                                    if ($rwCheckrj['task_status'] == 'Aborted') {
                                                                        echo '<td><span class="label label-danger">' . $rwCheckrj['task_status'] . '</span></td> ';
                                                                    } else if ($rwCheckrj['task_status'] == 'Pending') {
                                                                        echo '<td><span class="label label-warning">' . $rwCheckrj['task_status'] . '</span></td> ';
                                                                    } else {
                                                                        echo '<td><span class="label label-success">' . $rwCheckrj['task_status'] . '</span></td> ';
                                                                    }
                                                                }
                                                                ?>
                                                                <td><?php echo $rwTaskDoc['start_date']; ?></td>
                                                                <td>
                                                                    <?php
                                                                    if ($num == 1) {
                                                                        ?>
                                                                        <a href="RecreateWork?tkt=<?php echo base64_encode(urlencode($rwTaskDoc['ticket_id'])); ?>" class="btn btn-primary"> <i class="fa fa-edit"></i> <?php echo $lang['re-initiate']; ?></a>
                                                                        <?php
                                                                    } else {
                                                                        ?>

                                                                        <?php
                                                                    }
                                                                    ?>
                                                                </td>
                                                            </tr>
                                                            <?php
                                                            $i++;
                                                        }
                                                        ?>


                                                    </tbody>
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
                                                            echo " <li><a href='?start=$prev&tktid=$_GET[tktid]&limit=$per_page'> $lang[Prev] </a> </li>";
                                                        else
                                                            echo " <li class='disabled'><a href='javascript:void(0)'>$lang[Prev]</a> </li>";
                                                        //pages 
                                                        if ($max_pages < 7 + ($adjacents * 2)) {   //not enough pages to bother breaking it up
                                                            $i = 0;
                                                            for ($counter = 1; $counter <= $max_pages; $counter++) {
                                                                if ($i == $start) {
                                                                    echo "<li class='active'><a href='?start=$i&limit=$per_page&tktid=" . $_GET['tktid'] . "'><b>$counter</b></a> </li>";
                                                                } else {
                                                                    echo "<li><a href='?start=$i&limit=$per_page&tktid=" . $_GET['tktid'] . "'>$counter</a></li> ";
                                                                }
                                                                $i = $i + $per_page;
                                                            }
                                                        } elseif ($max_pages > 5 + ($adjacents * 2)) {    //enough pages to hide some
                                                            //close to beginning; only hide later pages
                                                            if (($start / $per_page) < 1 + ($adjacents * 2)) {
                                                                $i = 0;
                                                                for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++) {
                                                                    if ($i == $start) {
                                                                        echo " <li class='active'><a href='?start=$i&limit=$per_page&tktid=" . $_GET['tktid'] . "'><b>$counter</b></a></li> ";
                                                                    } else {
                                                                        echo "<li> <a href='?start=$i&limit=$per_page&tktid=" . $_GET['tktid'] . "'>$counter</a> </li>";
                                                                    }
                                                                    $i = $i + $per_page;
                                                                }
                                                            }
                                                            //in middle; hide some front and some back
                                                            elseif ($max_pages - ($adjacents * 2) > ($start / $per_page) && ($start / $per_page) > ($adjacents * 2)) {
                                                                echo " <li><a href='?start=0&limit=$per_page&tktid=$_GET[tktid]'>1</a></li> ";
                                                                echo "<li><a href='?start=$per_page&limit=$per_page&tktid=$_GET[tktid]'>2</a></li>";
                                                                echo "<li><a href='javascript:void(0)'>...</a></li>";

                                                                $i = $start;
                                                                for ($counter = ($start / $per_page) + 1; $counter < ($start / $per_page) + $adjacents + 2; $counter++) {
                                                                    if ($i == $start) {
                                                                        echo " <li class='active'><a href='?start=$i&limit=$per_page&tktid=" . $_GET['tktid'] . "'><b>$counter</b></a></li> ";
                                                                    } else {
                                                                        echo " <li><a href='?start=$i&limit=$per_page&tktid=" . $_GET['tktid'] . "'>$counter</a> </li>";
                                                                    }
                                                                    $i = $i + $per_page;
                                                                }
                                                            }
                                                            //close to end; only hide early pages
                                                            else {
                                                                echo "<li> <a href='?start=0&limit=$per_page&tktid=$_GET[tktid]'>1</a> </li>";
                                                                echo "<li><a href='?start=$per_page&limit=$per_page&tktid=$_GET[tktid]'>2</a></li>";
                                                                echo "<li><a href='javascript:void(0)'>...</a></li>";

                                                                $i = $start;
                                                                for ($counter = ($start / $per_page) + 1; $counter <= $max_pages; $counter++) {
                                                                    if ($i == $start) {
                                                                        echo " <li class='active'><a href='?start=$i&limit=$per_page&tktid=" . $_GET['tktid'] . "'><b>$counter</b></a></li> ";
                                                                    } else {
                                                                        echo "<li> <a href='?start=$i&limit=$per_page&tktid=" . $_GET['tktid'] . "'>$counter</a></li> ";
                                                                    }
                                                                    $i = $i + $per_page;
                                                                }
                                                            }
                                                        }
                                                        //next button
                                                        if (!($start >= $foundnum - $per_page))
                                                            echo "<li><a href='?start=$next&tktid=$_GET[tktid]&limit=$per_page'>$lang[Next]</a></li>";
                                                        else
                                                            echo "<li class='disabled'><a href='javascript:void(0)'>$lang[Next]</a></li>";
                                                        ?>
                                                    </ul>
                                                    <?php
                                                }
                                                echo "</center>";
                                            }else {
                                                ?>
                                                <div class="form-group form-group no-records-found m-t-60"><label><strong class="text-danger"><i class="ti-face-sad text-danger"></i> <?php echo $lang['Who0ps!_No_Records_Found']; ?></strong></label></div>
                                                <?php
                                            }
                                            ?>
                                        </div>

                                        <?php
                                        if ($Uid == $rwDoc['assign_by']) {
                                            echo '<p class="text-center"><label class="text-danger">Please Create Task</label></p>';
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> <!-- container -->

            <?php require_once './application/pages/footer.php'; ?>
        </div>
        <?php require_once './application/pages/footerForjs.php'; ?>
        <!--Form Wizard-->
        <script src="assets/plugins/jquery.steps/js/jquery.steps.min.js" type="text/javascript"></script>
        <script src="assets/pages/jquery.wizard-init.js" type="text/javascript"></script>
        <script src="assets/jscustom/wizard.js"></script>
        <script>
                                        $("a#tracktsk").click(function () {
                                            var tcket = $(this).attr('data');
                                            // alert(id);
                                           var token = $("input[name='token']").val();
                                            $.post("application/ajax/trackTasklist.php", {ID: tcket, token:token}, function (result, status) {
                                                if (status == 'success') {
                                                    getToken();
                                                    $("#ticket").html(result);
                                                    // alert(result);
                                                }
                                            });
                                        });
                                        //for limit change
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
                                                console.log(url);
                                                window.open(url, "_parent");
                                            });
                                        });
        </script> 
        <script src="assets/plugins/bootstrap-filestyle/js/bootstrap-filestyle.min.js" type="text/javascript"></script>
        <script src="assets/plugins/select2/js/select2.min.js" type="text/javascript"></script>
        <script type="text/javascript" src="assets/plugins/parsleyjs/parsley.min.js"></script>

        <div class="modal fade bs-example-modal-lg" id="trackTask" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" 
             aria-hidden="true" style="display: none;">
            <div class="modal-dialog modal-full">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="myLargeModalLabel"><?php echo $lang['TSK_TKING']; ?></h4>
                    </div>
                    <div class="modal-body" id="ticket" style="overflow:auto">
                        <img src="assets/images/load.gif" alt="load" class="img-responsive center-block" width="50px"/>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button>
                    </div>
                </div>
            </div>
        </div>
        <div id="taskdescription" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog modal-lg"> 
                <div class="modal-content"> 
                    <div class="modal-header"> 
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 

                        <h4 class="modal-title"><?php echo $lang['Task_Description']; ?></h4> 
                    </div>
                    <div class="modal-body" id="taskRemrk">
                        <img src="assets/images/load.gif" alt="load" class="img-responsive center-block" />

                    </div> 
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button> 
                    </div>
                </div> 
            </div>
        </div><!-- /.modal -->
        <!-- for audio model-->
        <div id="modal-audio" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="myModalLabel">Play/Download Audio</h4>
                    </div>
                    <div id="foraudio">

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>

                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
        <!--for video model-->
        <div id="modal-video" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="myModalLabel">Play/Download video</h4>
                    </div>
                    <div  id="videofor">


                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>

                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->

        <script>

		//for video file
		$("a#video").click(function () {
			var id = $(this).attr('data');

			$.post("application/ajax/videoformat.php", {vid: id}, function (result, status) {
				if (status == 'success') {
					$("#videofor").html(result);
					//alert(result);

				}
			});
		});
		//for audio file
		$("a#audio").click(function () {
			var id = $(this).attr('data');

			$.post("application/ajax/audioformat.php", {aid: id}, function (result, status) {
				if (status == 'success') {
					$("#foraudio").html(result);
					//alert(result);

				}
			});
		});
		
		//for viewing task Description
		$("a#ViewTsk").click(function () {
			var id = $(this).attr('data');
			var result = $("#" + id).html();

			$("#taskRemrk").html(result);

		});
        </script>

    </body>
</html>
