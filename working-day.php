<!DOCTYPE html>
<html>
    <?php
    require_once './loginvalidate.php';
    require_once './application/config/database.php';
    require_once './application/pages/head.php';
    require_once 'calendar.php';
    
    if ($rwgetRole['view_holiday'] != '1') {
        header('Location: ./index');
    }
    ?>
    <link href="assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/plugins/bootstrap-select/css/bootstrap-select.min.css" rel="stylesheet" />
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
                        <div class="row">
                            <ol class="breadcrumb">
                                <li><a href="working-day"><?php echo $lang['holiday_manager']; ?></a></li>
                                <li class="active"><?php echo $lang['holiday_view']; ?></li>
                                <a href="javascript:void(0)" class="btn btn-primary waves-effect waves-light pull-right btn-sm margin-t-9" onclick="goPrevious();" title="<?php echo $lang['Go_to_previous_page']; ?>"><i class="fa fa-arrow-circle-left"></i></a>
                            </ol>
                        </div>
                        <!-- Page-Title -->
                        <div class="row">
                            <div class="box box-primary">
                                <!-- /.box-header -->
                                <div class="box-body">
                                    <div class="row">
                                        <!-- Left col -->
                                        <section class="col-lg-12">
                                            <!-- Custom tabs (Charts with tabs)-->
                                            <div class="row nav-tabs-custom p-b-30">
                                                <!-- Tabs within a box -->
                                                <div class="col-md-7" style="margin-left: 170px;">
                                                    <div id="calendar_div">
                                                        <?php
                                                        mysqli_set_charset($db_con, "utf8");
                                                        echo getCalender('', '', $db_con, $lang);
                                                        ?>
                                                    </div>
                                                </div>
                                                <div class="col-md-5">
                                                    <div class="tab-content ">
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- /.nav-tabs-custom -->
                                        </section>
                                    </div>
                                </div>

                                <!-- /.box-body -->
                            </div>
                        </div> <!-- container -->
                    </div> <!-- content -->
                    <?php require_once './application/pages/footer.php'; ?>
                </div>          
            </div>
        </div>
        <!-- END wrapper -->
        <?php require_once './application/pages/footerForjs.php'; ?>
        <script src="assets/plugins/select2/js/select2.min.js" type="text/javascript"></script>

        <script>

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

        </script>
     
    </body>
</html>
