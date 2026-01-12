<!DOCTYPE html>
<html>
<?php


ini_set('memory_limit', '-1');
set_time_limit(0);
require_once './loginvalidate.php';
if (!$_SESSION['cdes_user_id']) {
    header('Location:index');
}
require_once './application/config/database.php';
require_once './application/pages/head.php';
require_once './application/pages/function.php';
require_once './excel-viewer/excel_reader.php';

$queryString = $_SERVER["QUERY_STRING"];
// echo $rwgetRole['dashboard_mydms']; die;
if ($rwgetRole['metadata_quick_search'] != '1') {
    header('Location: ./index');
}

?>
<link href="assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
<!-- Plugin Css-->
<link rel="stylesheet" href="assets/plugins/magnific-popup/css/magnific-popup.css" />
<link rel="stylesheet" href="assets/plugins/jquery-datatables-editable/datatables.css" />
<link href="assets/plugins/datatables/dataTables.bootstrap.min.css" rel="stylesheet" type="text/css" />

<style>
    .south_railway {
        height: 85px;
    }

    .butoon_save {
        margin-top: 25px;
    }

    table.table.table-bordered.upper {
        margin-top: -11px;
    }

    .center_content {
        text-align: center;
    }

    .top-center {
        vertical-align: top;
        text-align: center;
    }

    a.btn.btn-primary.add-btn.true_false {
        margin-top: 24px;
    }

    .remove-btn {
        margin-top: 24px;
    }

    hr {
        margin-top: 20px;
        margin-bottom: 20px;
        border: 0;
        border-top: 4px solid #193860;
    }
</style>

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
                        <ol class="breadcrumb">
                            <li><a href="item_contract"><?php echo $lang['Masters']; ?></a></li>
                            <li class="active">Railway Item Contract</li>
                            <li>
                                <a href="#" data-toggle="modal" data-target="#help-modal" id="helpview" data="3" title="<?= $lang['help']; ?>">
                                    <i class="fa fa-question-circle"></i>
                                </a>
                            </li>
                        </ol>
                    </div>

                    <div class="card-box">
                        <div class="row">
                            <form method="post" action="#" enctype="multipart/form-data" id="railwayForm">
                                <input type="hidden" name="action" value="save">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label">Item No as per contract (for payment)</label>
                                            <input type="text" class="form-control" id="railway_item_no" placeholder="Enter Item No." name="railway_item_no" required>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="submit" class="btn btn-primary butoon_save" name="save" id="save" value="Save" />
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Table to display saved Railway Item Numbers -->
                    <div class="card-box">
                        <div class="container">
                            <div id="order_table">
                                <table class="table table-bordered upper" id="example">
                                    <thead class="alert-info">
                                        <tr>
                                            <th>Item No </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php

                                        $sql = "SELECT * FROM tbl_railway_item_no ORDER BY id DESC";
                                        $query = mysqli_query($db_con, $sql);
                                        if (mysqli_num_rows($query) > 0) {
                                            while ($data = mysqli_fetch_assoc($query)) {
                                        ?>
                                                <tr>
                                                    <td><?php echo $data['railway_item_no']; ?></td>

                                                </tr>
                                            <?php
                                            }
                                        } else {
                                            ?>
                                            <tr>
                                                <td colspan="2" style="text-align:center; color:red;">No Record found</td>
                                            </tr>
                                        <?php
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <?php require_once './application/pages/footer.php'; ?>
        </div>

    </div>
    <!-- END wrapper -->
    <?php require_once './application/pages/footerForjs.php'; ?>
    <script src="assets/plugins/bootstrap-filestyle/js/bootstrap-filestyle.min.js" type="text/javascript"></script>
    <script src="assets/plugins/select2/js/select2.min.js" type="text/javascript"></script>
    <script type="text/javascript" src="assets/plugins/parsleyjs/parsley.min.js"></script>
    <script src="assets/multi_function_script.js"></script>
    <script src="assets/js/gs_sortable.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <script>
        $(document).ready(function() {
            $('#railwayForm').on('submit', function(e) {
                e.preventDefault();
                var railway_item_no = $('#railway_item_no').val();

                $.ajax({
                    url: 'save_railway.php',
                    type: 'POST',
                    data: {
                        action: 'save',
                        railway_item_no: railway_item_no
                    },
                    success: function(response) {
                        $('body').append(response);
                    },
                    error: function() {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Failed to save Railway item No.',
                            icon: 'error'
                        });
                    }
                });
            });
        });
    </script>

</body>

</html>