<!DOCTYPE html>
<html>
    <?php
    require_once './loginvalidate.php';
    require_once './application/config/database.php';
    require_once './application/pages/head.php';
    require_once 'logo-project.php';
    //for user role
    $chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$_SESSION[cdes_user_id]', user_ids) > 0") or die('Error:' . mysqli_error($db_con));

    $rwgetRole = mysqli_fetch_assoc($chekUsr);

    if ($rwgetRole['feedback_msg'] != '1') {
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
                                        <a href="feedback"><?php echo $lang['Fdbk_Mgmt']; ?></a>
                                    </li>
                                    <li class="active">
                                        <?php echo $lang['Lve_ur_Fdbk']; ?>
                                    </li>
                                      <a href="javascript:void(0)" class="btn btn-primary waves-effect waves-light pull-right btn-sm margin-t-9" onclick="goPrevious();" title="<?php echo $lang['Go_to_previous_page']; ?>"><i class="fa fa-arrow-circle-left"></i></a>
                                </ol>
                            </div>
                        </div>

                        <div class="panel">
                            <div class="box box-primary">
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="form-group">
                                            <label style="font-weight:600;"><?php echo $lang['Lve_ur_Fdbk_Hre']; ?></label>
                                        </div>
                                        <form method="post" onsubmit="return validateForm()" name="myform">
                                            <div class="form-group">
                                                <textarea   rows="5" name="f_message" id="editor"  ></textarea>
                                                <p style="color: red" id="error"></p></div>
                                            <div class="form-group pull-right">
                                                <button class="btn btn-default" type="reset"><?php echo $lang['Reset']; ?></button>
                                                <input type="submit" class="btn btn-primary" name="feedback" value="<?php echo $lang['Leave_Feedback']; ?>">
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <!-- end: page -->
                        </div> <!-- end Panel -->
                    </div> <!-- container -->

                </div> <!-- content -->

                <?php require_once './application/pages/footer.php'; ?>

            </div>

        </div>
        <!-- END wrapper -->

        <?php require_once './application/pages/footerForjs.php'; ?>

        <!---html textarea editor js code--->
        <script src="assets/plugins/tinymce/tinymce.min.js"></script>
        <script type="text/javascript" src="assets/plugins/parsleyjs/parsley.min.js"></script>
        <script type="text/javascript">
                                            function validateForm() {
                                                var x = document.forms["myform"]["f_message"].value;
                                                if (x == "") {
                                                    document.getElementById("error").innerHTML = "This value is required.";
                                                    return false;
                                                }
                                            }
                                            $(document).ready(function () {
                                                //$form= $('form').parsley();
                                                if ($("#editor").length > 0) {
                                                    tinymce.init({
                                                        selector: "textarea#editor",
                                                        theme: "modern",
                                                        height: 200,
                                                        plugins: [
                                                            "advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker",
                                                            "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
                                                            "save table contextmenu directionality emoticons template paste textcolor"
                                                        ],
                                                        toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | forecolor backcolor emoticons",
                                                        style_formats: [
                                                            {title: 'Bold text', inline: 'b'},
                                                            {title: 'Red text', inline: 'span', styles: {color: '#ff0000'}},
                                                            {title: 'Red header', block: 'h1', styles: {color: '#ff0000'}},
                                                        ]
                                                    });
                                                }
                                                //$('form').parsely();
                                            });
        </script>
        <?php
        if (isset($_POST['feedback'])) {
            $fbackMsg = mysqli_real_escape_string($db_con, $_POST['f_message']);
            //$to = 'ezeefileadmin@cbsl-india.com';
            $getUser = mysqli_query($db_con, "select * from tbl_user_master where user_id ='$_SESSION[cdes_user_id]'")or die("error: in get name" . mysqli_error($db_con));
            $rwGetUser = mysqli_fetch_assoc($getUser);
            $UserName = $rwGetUser['first_name'] . ' ' . $rwGetUser['last_name'];
            $from = $rwGetUser['user_email_id'];
            $des = $rwGetUser['designation'];
            include './mail.php';
            $fbackMail = feedbackMail($from, $fbackMsg, $UserName, $des, $projectName);
            if ($fbackMail) {
                echo '<script> taskSuccess("feedback", "Thank you !! For your feedback" );</script>';
            } else {
                echo '<script> taskFailed("feedback", "Sorry unable to sent your feedback " );</script>';
            }
        }
        ?>
    </body>
</html>