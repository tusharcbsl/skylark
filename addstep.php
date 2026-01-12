<!DOCTYPE html>
<html>
    <?php
    require_once './loginvalidate.php';
    require_once './application/config/database.php';
    require_once './application/pages/head.php';
    ?>
    <?php
    if (isset($_SESSION['lang'])) {
        $file = "../../" . $_SESSION['lang'] . ".json";
    } else {
        $file = "../../English.json";
    }
    $langDetail = mysqli_query($db_con, "select * from tbl_language where lang_name='" . $_SESSION['lang'] . "'") or die('Error : ' . mysqli_error($db_con));
    $langDetail = mysqli_fetch_assoc($langDetail);
    ?>
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
                                <li><a href="#"><?php echo $lang['Workflow_management']; ?></a></li>
                                <li class="active"><?php echo $lang['Workflow_Stp_Crt']; ?></li>
                            </ol>

                        </div>

                        <div class="row">
                            <div class="box box-primary">
                                <div class="box-header with-border">
                                    <h4 class="header-title"><?php echo $lang['Workflow_Stp_Crt']; ?> <a href="workFlowStep?idwork=<?php echo $_GET['idwork']; ?>" class="btn btn-primary btn-xs pull-right" style="margin-right: 10px;">Back</a></h4>

                                </div>


                                <div class="box-body">

                                    <div class="col-lg-12">
                                        <div class="card-box">
                                            <div class="row">

                                                <?php
                                                mysqli_set_charset($db_con, "utf8");
                                                $getStep = mysqli_query($db_con, "select * from tbl_step_master where step_id='$_GET[idstp]'") or die('Error in stepfetch:' . mysqli_error($db_con));
                                                $rwgetStep = mysqli_fetch_assoc($getStep)
                                                ?>


                                                <form action=""  method="post">

                                                    <div class="form-group row">
                                                        <div class="col-md-2">
                                                            <label for=""><?php echo $lang['Stp_Nme']; ?>:<span style="color: red;">*</span></label>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <input type="text" class="form-control " name="workflowStep" value="<?php echo $rwgetStep['step_name']; ?>"required>
                                                        </div>
                                                    </div>

                                                    <div class="form-group row">
                                                        <div class="col-md-2">
                                                            <label for="userName"><?php echo $lang['Stp_Ordr']; ?><span style="color: red;">*</span></label>
                                                        </div>
                                                        <div class="col-md-4">

                                                            <input type="text" class="form-control" name="workStepOrd" value="<?php echo $rwgetStep['step_order']; ?>" required/>                                          
                                                        </div>
                                                    </div>

                                                    <div class="form-group row">
                                                        <div class="col-md-2">
                                                            <label for="userName"><?php echo $lang['Des']; ?>:</label>
                                                        </div>
                                                        <div class="col-md-4">

                                                            <textarea class="form-control translatetext" rows="5" name="workStepDesc"><?php echo $rwgetStep[step_description]; ?></textarea>

                                                        </div>
                                                    </div>


                                                    <div class="col-md-12">
                                                        <div class="col-md-2">&nbsp;</div>
                                                        <div class="col-md-10">
                                                            <div class="form-group  m-b-0">

                                                                <?php
                                                                if (isset($_GET['idstp']) && !empty($_GET['idstp'])) {
                                                                    echo '
                                                                     <button class="btn btn-primary waves-effect waves-light" type="submit" name="upstep">
                                                                     Update Step
                                                                     </button>
                                                                    
                                                                 ';
                                                                } else {
                                                                    echo '
                                                                  <button class="btn btn-primary waves-effect waves-light" type="submit" name="adstep">
                                                                  Add Step
                                                                  </button>
                                                                  <button type="reset" class="btn btn-warning waves-effect waves-light m-l-5">
                                                                Reset
                                                               </button>
                                                                  ';
                                                                }
                                                                ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>				
                        </div>
                    </div> <!-- container -->

                </div> <!-- content -->

                <?php require_once './application/pages/footer.php'; ?>

            </div>
            <!-- ============================================================== -->
            <!-- End Right content here -->
            <!-- ============================================================== -->
            <!-- Right Sidebar -->
            <?php require_once './application/pages/rightSidebar.php'; ?>
            <!-- /Right-bar -->
        </div>
        <!-- END wrapper -->
        <?php require_once './application/pages/footerForjs.php'; ?>
        <script src="assets/plugins/bootstrap-filestyle/js/bootstrap-filestyle.min.js" type="text/javascript"></script>
        <script src="assets/plugins/select2/js/select2.min.js" type="text/javascript"></script>
        <script type="text/javascript" src="assets/plugins/parsleyjs/parsley.min.js"></script>       
        <script type="text/javascript">
            $(document).ready(function () {
                $('form').parsley();

            });
            $(".select2").select2();

            //firstname last name 
            $("input#userName, input#lastName").keypress(function (e) {
                //if the letter is not digit then display error and don't type anything
                if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57) && e.which != 46) {
                    //display error message
                    return true;
                } else {
                    return false;
                }
                str = $(this).val();
                str = str.split(".").length - 1;
                if (str > 0 && e.which == 46) {
                    return false;
                }
            });
            $("input#datalength").keypress(function (e) {
                //if the letter is not digit then display error and don't type anything
                if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57) && e.which != 46) {
                    //display error message
                    return false;
                }
                str = $(this).val();
                str = str.split(".").length;
                if (str > 0 && e.which == 46) {
                    return false;
                }
            });

            $("#depth_level").change(function () {
                var lbl = $(this).val();
                //alert(lbl);
                $.post("application/ajax/parentList.php", {level: lbl}, function (result, status) {
                    if (status == 'success') {
                        $("#parent").html(result);
                    }
                });
            });
            $("#parent").change(function () {
                var slId = $(this).val();
                // alert(slId);
                $.post("application/ajax/childList.php", {sl_id: slId}, function (result, status) {
                    if (status == 'success') {
                        $("#child_level").html(result);
                    }
                });
            });    //for placeholder display
            function changeplh() {
                debugger;
                var sel = document.getElementById("selection");
                // var textbx = document.getElementById("textbox");
                var indexe = sel.selectedIndex;

                if (indexe == 1) {
                    $("#textbox").attr("placeholder", "Enter 0 or 1");

                }
                if (indexe == 2) {
                    $("#textbox").attr("placeholder", "Enter max. 255 characters");
                }

                if (indexe == 3) {

                    $("#textbox").attr("placeholder", "");


                }
                if (indexe == 4) {
                    $("#textbox").attr("placeholder", "Enter max. 255 digits");
                }


                if (indexe == 5) {
                    $("#textbox").attr("placeholder", "Enter max. 255 digits");
                }
                if (indexe == 6) {
                    $("#textbox").attr("placeholder", "Enter max. 53 digits");
                }
                if (indexe == 7) {
                    $("#textbox").attr("placeholder", "");
                }
                if (indexe == 8) {
                    $("#textbox").attr("placeholder", "Enter max. 255 characters");
                }

            }

            $(document).on('change', '#selection', function () {
                $('#textbox').attr('disabled', $(this).val() == 'datetime' || $(this).val() == 'double');
            });
        </script>
        <script type="text/javascript">
            google.load("elements", "1", {
                packages: "transliteration"
            });

            function onLoad() {
                var langcode = '<?php echo $langDetail['lang_code']; ?>';
                var options = {
                    sourceLanguage: 'en',
                    destinationLanguage: [langcode],
                    shortcutKey: 'ctrl+g',
                    transliterationEnabled: true
                };

                var control =
                        new google.elements.transliteration.TransliterationControl(options);
                //var ids = ["groupName12"];
                var elements = document.getElementsByClassName('translatetext');
                control.makeTransliteratable(elements);
            }
            $.getScript('assets/js/test.js', function () {
                // Call custom function defined in script
                onLoad();
            });
            google.setOnLoadCallback(onLoad);
        </script>
    </body>
</html>
<?php
if (isset($_POST['upstep'])) {

    if (!empty($_GET['idwork'])) {

        $workFlowId = $_GET['idwork'];
    }

    if (!empty($_POST['workflowStep'])) {

        $workStepName = mysqli_real_escape_string($db_con, $_POST['workflowStep']);
    }


    if (!empty($_POST['workStepOrd'])) {

        $workStepOrd = mysqli_real_escape_string($db_con, $_POST['workStepOrd']);
    }

    if (!empty($_POST['workStepDesc'])) {

        $workStepDesc = mysqli_real_escape_string($db_con, $_POST['workStepDesc']);
    }

    $idstp = mysqli_real_escape_string($db_con, $_GET['idstp']);
    mysqli_set_charset($db_con, "utf8");
    $adStep = mysqli_query($db_con, "update tbl_step_master set step_name='$workStepName', step_description='$workStepDesc', workflow_id='$workFlowId', step_order='$workStepOrd' where step_id='$idstp'") or die('Error in upstp:' . mysqli_error($db_con));

    if ($adStep) {


        echo '<script>taskSuccess("workFlowStep?idwork=' . $_GET[idwork] . '","' . $lang['Stp_Updatd_Sucesfly'] . '");</script>';
    } else {
        echo '<script>taskFailed("workFlowStep?idwork=' . $_GET[idwork] . '","' . $lang['Stp_nt_Updtd_Crtd'] . '");</script>';
    }
    mysqli_close($db_con);
}
?>