<!DOCTYPE html>
<html>
    <?php
    require_once './loginvalidate.php';
    require_once './application/config/database.php';
    require_once './application/pages/head.php';


    if ($rwgetRole['upload_logs'] != '1') {
        header('Location: ./index');
    }
    ?>
    <link rel="stylesheet" href="assets/plugins/jquery-datatables-editable/datatables.css" />
    <link href="assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />

    <body class="fixed-left">
        <!-- Begin page -->
        <div id="wrapper">
            <!-- Top Bar Start -->
            <?php require_once './application/pages/topBar.php'; ?>
            <!-- Top Bar End -->
            <!-- ========== Left Sidebar Start ========== 1001/10556/00959 12/12/2011 14:33:58-->
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

                            <ol class="breadcrumb">
                                <li><a href="#"><?php echo $lang['Audit_Trail']; ?></a></li>
                                <li class="active"><?php echo $lang['upload_logs']; ?></li>
                                <li> <a href="#" data-toggle="modal" data-target="#help-modal" id="helpview" data="29" title="<?= $lang['help']; ?>"><i class="fa fa-question-circle" style="font-size: 23px"></i> </a></li>

                                <a href="javascript:void(0)" class="btn btn-primary waves-effect waves-light pull-right btn-sm margin-t-9" onclick="goPrevious();" title="<?php echo $lang['Go_to_previous_page']; ?>"><i class="fa fa-arrow-circle-left"></i></a>
                            </ol>
                        </div>
                        <div class="row">
                            <div class="box box-primary">
                                <div class="box-header with-border">
                                    <h4 class="header-title"><?php echo $lang['upload_logs']; ?></h4>
                                </div>
                                <div class="box-body">
                                    <div class="col-lg-12">
                                        <?php
                                        $target_path = 'uploadLogs';
                                        $files = scandir($target_path) or die('Error' . $target_path . print_r(error_get_last()));
                                        //$files = array_reverse($files);
                                        //print_r($files);
                                        ?>
                                        <table class="table table-striped table-bordered" id="datatable">
                                            <thead>
                                                <tr>
                                                    <th><?php echo $lang['upload_logs']; ?></th>
                                                    <th><?php echo $lang['log_time']; ?></th>
                                                    <th><?php echo $lang['Actions']; ?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                for ($kk = 2; $kk < count($files); $kk++) {
                                                    if ($files[$kk] != 'index.php') {
                                                        ?>
                                                        <tr>
                                                            <td><?php echo $files[$kk]; ?></td>
                                                            <td><?php echo '<label class="label label-primary">' . date('d-m-Y h:i:s', strtotime(str_replace(".dat", "", $files[$kk]))) . '</label>'; ?></td>
                                                            <td><a href="javascript:void(0)" data="<?php echo $files[$kk]; ?>" data-time="<?php echo date('d-m-Y h:i:s', strtotime(str_replace(".dat", "", $files[$kk]))); ?>" data-toggle="modal" data-target="#dialog" id="viewLog"><i class="fa fa-eye"></i></a></td>
                                                        </tr>
                                                        <?php
                                                    }
                                                }
                                                ?>

                                            </tbody>
                                        </table>

                                    </div>
                                </div>
                            </div>				
                        </div>


                        <?php //if($message) echo "<p>$message</p>";   ?>
                    </div> <!-- container -->

                </div> <!-- content -->

                <?php require_once './application/pages/footer.php'; ?>

            </div>
        </div>
        <!-- END wrapper -->
        <div id="dialog" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog modal-lg"> 
                <div class="modal-content"> 
                    <div class="modal-header"> 
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                        <label><h2 class="panel-title" id="title"></h2></label> 
                    </div> 
                    <div class="modal-body" id="bodymodal">
                    </div>
                    <div class="modal-footer">
                        <div class="col-md-12 text-right">
                            <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $lang['Close']; ?></button> 
                        </div>
                    </div>

                </div> 
            </div>
        </div>
        <?php require_once './application/pages/footerForjs.php'; ?>
        <script src="assets/plugins/bootstrap-filestyle/js/bootstrap-filestyle.min.js" type="text/javascript"></script>
        <script src="assets/plugins/select2/js/select2.min.js" type="text/javascript"></script>
        <script type="text/javascript" src="assets/plugins/parsleyjs/parsley.min.js"></script>
        <style>
            .modal-body p{
                font-size: 12px;
                line-height: 11px;
                margin: 1px;
            }
        </style>
        <script type="text/javascript">
                                    //$(".select2").select2();
                                    $("a#viewLog").click(function () {
                                    var path = $(this).attr('data');
                                    var logtime = $(this).attr('data-time');
                                    $("#title").html("<?php echo $lang['upload_logs']; ?> (" + logtime + ")");
                                    $.post("application/ajax/uploadLogs.php", {path: path}, function (result, status) {
                                    if (status == 'success') {
                                    $("#bodymodal").html(result);
                                    }
                                    });
                                    });
                                    $(document).ready(function () {
                                    $('form').parsley();
                                    $('#datatable').dataTable({
<?= (!empty($pageLength) ? '"pageLength":' . $pageLength . ',' : '') ?>
                                    "language": {
                                    "paginate": {
                                    "previous": "<?= $lang['Prev'] ?>",
                                            "next": "<?= $lang['Next'] ?>",
                                    },
                                            "emptyTable": "<?= $lang['No_Rcrds_Fnd'] ?>",
                                            "sEmptyTable": "<?= $lang['No_Rcrds_Fnd'] ?>",
                                            "sInfo": "<?= $lang['sInfo'] ?>",
                                            "sInfoEmpty": "<?= $lang['sInfoEmpty'] ?>",
                                            "sSearch": "<?= $lang['Search'] ?>",
                                            "sLengthMenu": "<?= $lang['sLengthMenu'] ?>",
                                            "sInfoFiltered": "<?= $lang['sInfoFiltered'] ?>",
                                            "sZeroRecords": "<?= $lang['sZeroRecords'] ?>",
                                    }
                                    });
                                    });
        </script>
    </body>
</html>
