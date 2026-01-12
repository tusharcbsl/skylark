<!DOCTYPE html>
<html>
    <?php
    require_once './loginvalidate.php';
    require_once './application/config/database.php';
    require_once './application/pages/head.php';

    //for user role
    $chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$_SESSION[cdes_user_id]', user_ids) > 0") or die('Error:' . mysqli_error($db_con));

    $rwgetRole = mysqli_fetch_assoc($chekUsr);

    if ($rwgetRole['view_faq'] != '1') {
        header('Location: ./index');
    }
    $_SESSION['cdes_user_id'] == $rwUser['user_id'];
    ?>
    <!-- Plugin Css-->
    <link rel="stylesheet" href="assets/plugins/magnific-popup/css/magnific-popup.css" />

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
                    <div class="container">

                        <!-- Page-Title -->
                        <div class="row">
                            <div class="col-sm-12">
                                <ol class="breadcrumb">
                                    
                                    <li>
                                        <a href="faq"><?php echo $lang['FAQ_Help']; ?></a>
                                    </li>
                                    <a href="javascript:void(0)" class="btn btn-primary waves-effect waves-light pull-right btn-sm margin-t-9" onclick="goPrevious();" title="<?php echo $lang['Go_to_previous_page']; ?>"><i class="fa fa-arrow-circle-left"></i></a>
                                </ol>
                            </div>
                        </div>

                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <div class="col-sm-10">
                                    <div class="col-sm-10">
                                        <h4 class="header-title"><?php echo $lang['FAQ_Help']; ?></h4>
                                    </div>
                                    <div class="col-sm-2 pull-right" style="margin-right: -150px;">
                                        <?php if ($rwgetRole['add_faq'] == '1') { ?>
                                            <a href="javascript:void(0)" class="btn btn-primary" data-toggle="modal" data-target="#faq-add"> <?= $lang['add_ques'] ?> <i class="fa fa-plus"></i></a>
                                        <?php } ?>
                                    </div>

                                </div>
                            </div>
                            <div class="panel">
                                <div class="panel-body">
                                    <div class="row"> 
                                        <div class="col-lg-12"> 
                                            <?php
                                            $i = 1;
                                            $faqName = mysqli_query($db_con, "SELECT * FROM `tbl_faq_master`") or die('Error in faqName' . mysqli_error($db_con));
                                            while ($rwfaqName = mysqli_fetch_assoc($faqName)) {
                                                ;
                                                ?>
                                                <div class="panel-group" id="accordion-test-2"> 
                                                    <div class="panel panel-default"> 
                                                        <div class="panel-heading"> 
                                                            <h4 class="panel-title"> 
                                                                <a data-toggle="collapse" data-parent="#accordion-test-2" href="#collapse<?php echo $i; ?>" aria-expanded="false" class="collapsed">
                                                                    <?php
                                                                    echo $i . '.';
                                                                    echo $rwfaqName['question'] . '?';
                                                                    ?>

                                                                </a> 
                                                                <div class="faqedit">
                                                                    <?php if ($rwgetRole['edit_faq'] == '1') { ?>
                                                                    <span class="fa fa-edit" data-toggle="modal" data-target="#faq-edit" id="editRow" data="<?php echo $rwfaqName['id']; ?>" title="<?= $lang['Modify_column'] ?>"></span>
                                                                    <?php } if ($rwgetRole['del_faq'] == '1') { ?>
                                                                        <span class="fa fa-trash-o" data-toggle="modal" data-target="#faq-delete" id="delFaq" data="<?php echo $rwfaqName['id']; ?>" title="<?= $lang['Delete'] ?>"></span>
                                                                    <?php } ?>
                                                                </div>
                                                            </h4> 
                                                        </div> 
                                                        <div id="collapse<?php echo $i; ?>" class="panel-collapse collapse"> 
                                                            <div class="panel-body faqtext">
                                                                <?php echo $rwfaqName['answer']; ?>
                                                            </div> 
                                                        </div> 
                                                    </div>

                                                </div>
                                                <?php
                                                $i++;
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div> <!-- end row -->
                        </div>
                        <div id="faq-add" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                            <div class="modal-dialog modal-lg"> 
                                <div class="modal-content"> 

                                    <div class="modal-header"> 
                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                                        <h4 class="modal-title"><?= $lang['Add_New_faq'] ?></h4> 
                                    </div>
                                    <form method="post" >
                                        <div class="modal-body" >
                                            <div class="row">
                                                <div class="form-group">
                                                    <label><?= $lang['ques'] ?></label>
                                                    <input type="text" name="faq" class="form-control" id="faqques" placeholder="<?= $lang['ur_que'] ?>..."required>
                                                </div>
                                                <div class="form-group">
                                                    <label><?= $lang['ans'] ?></label>
                                                    <textarea rows="5" name="faqansss" id="editor"></textarea>
<!--                                                    <input type="text" name="faqans" class="form-control" id="faqans" placeholder="Enter your Answer here..."required>-->
                                                </div>
                                            </div>
                                        </div> 
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal"><?= $lang['Close'] ?></button> 
                                            <button type="submit" name="addfaq" class="btn btn-primary"><?= $lang['Submit'] ?></button> 
                                        </div>
                                    </form>

                                </div> 
                            </div>
                        </div><!-- /.modal -->
                        <div id="faq-edit" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                            <div class="modal-dialog modal-lg"> 
                                <div class="modal-content"> 
                                    <div class="modal-header"> 
                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                                        <h4 class="modal-title"> <?= $lang['edt_qus'] ?></h4> 
                                    </div>
                                    <form method="post">
                                        <div class="modal-body" id="modiFaq">

                                        </div> 
                                        <div class="modal-footer">

                                            <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal"><?= $lang['Close'] ?></button> 
                                            <button type="submit" name="editfaq" class="btn btn-primary"><?= $lang['Save'] ?></button> 
                                        </div>
                                    </form>

                                </div> 
                            </div>
                        </div><!-- /.modal -->
                        <div id="faq-delete" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                            <div class="modal-dialog"> 
                                <div class="panel panel-danger panel-color"> 

                                    <div class="panel-heading"> 
                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                                        <h2 class="panel-title"><?= $lang['Are_u_confirm'] ?></h2> 
                                    </div>
                                    <form method="post">
                                        <div class="panel-body" >
                                            <label class="text-danger"><?= $lang['del_faq'] ?></label>
                                        </div> 
                                        <div class="modal-footer">
                                            <input type="hidden" id="Qid" name="Qid">
                                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"> <?= $lang['Close'] ?></button> 
                                            <button type="submit" name="delfaq" class="btn btn-danger"> <i class="fa fa-trash-o"></i> <?= $lang['Delete'] ?></button> 
                                        </div>
                                    </form>

                                </div> 
                            </div>
                        </div><!-- /.modal -->
                        <?php require_once './application/pages/footer.php'; ?>
                        <!-- Right Sidebar -->
                        <?php //require_once './application/pages/rightSidebar.php'; ?>
                        <!-- /Right-bar -->
                        <?php require_once './application/pages/footerForjs.php'; ?>
                        <script src="assets/plugins/bootstrap-filestyle/js/bootstrap-filestyle.min.js" type="text/javascript"></script>

                        <script type="text/javascript" src="assets/plugins/parsleyjs/parsley.min.js"></script>
                        <!---html textarea editor js code--->
                        <script src="assets/plugins/tinymce/tinymce.min.js"></script>

                        <script type="text/javascript">
                                        $(document).ready(function () {
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
                                        });
                        </script>
                        <script>
                            $("span#editRow").click(function () {
                                var id = $(this).attr('data');
                                // alert(id);
                                $.post("application/ajax/editQueAns.php", {ID: id}, function (result, status) {
                                    // $.post("application/ajax/displayImage.php", {PATH: path}, function (result, status) {
                                    if (status == 'success') {
                                        $("#modiFaq").html(result);
                                        //alert(result);
                                    }
                                });
                            });

                            $("span#delFaq").click(function () {
                                var id = $(this).attr('data');
                                $("#Qid").val(id);
                            });
                          
                        </script>


                        <?php
                        //Add faq  ques. & Ans.
                        if (isset($_POST['addfaq'])) {
                            $faqQues = mysqli_real_escape_string($db_con, $_POST['faq']);
                            $faqAns = mysqli_real_escape_string($db_con, $_POST['faqansss']);
                            $userId = $_SESSION['cdes_user_id'];
                            $date = date('Y-m-d H:i:s');
                            //echo "select * from tbl_faq_master where question='$faqQues'"; die;
                            $checkDub = mysqli_query($db_con, "select * from tbl_faq_master where question='$faqQues'") or die('Error faq' . mysqli_error($db_con));
                            $rwchckDup = mysqli_num_rows($checkDub);
                           if ($rwchckDup < 1) {
                                $insertfaq = "INSERT INTO `tbl_faq_master`(`id`, `question`, `answer`, `user_id`, `dateposted`) VALUES (null, '$faqQues', '$faqAns', '$userId', '$date')" or die('Error in faq insert' . mysqli_error($db_con));
                                $faq = mysqli_query($db_con, $insertfaq) or die('Error faq' . mysqli_error($db_con));
                                if ($faq) {
                                    $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,'Ques. $faqQues Added','$date',null,'$host',null)") or die('error : ' . mysqli_error($db_con));
                                    echo'<script>taskSuccess("faq", "' . $lang['ques_ans_add'] . '");</script>';
                                } else {
                                    echo'<script>taskFailed("faq", "' . $lang['add_failed'] . '");</script>';
                                }
                            } else {
                                echo'<script>taskFailed("faq", "' . $lang['already_exist'] . '");</script>';
                            }
                            mysqli_close($db_con);
                        }
                        //Modify faq  ques. & Ans.
                        if (isset($_POST['editfaq'])) {
                            $faqId = filter_input(INPUT_POST, "faqid");
                            $faqQues = mysqli_real_escape_string($db_con, $_POST['faq']);
                            $faqAns = mysqli_real_escape_string($db_con, $_POST['faqans']);
                            $userId = $_SESSION['cdes_user_id'];
                            $date = date('Y-m-d H:i:s');
                            $oldfaq = mysqli_query($db_con, "SELECT question FROM `tbl_faq_master` WHERE id = '$faqId'") or die('Error in old name' . mysqli_error($db_con));
                            $rwoldfaq = mysqli_fetch_assoc($oldfaq) or die('Error faq old name' . mysqli_error($db_con));
                            $checkDub = mysqli_query($db_con, "select * from tbl_faq_master where question='$faqQues' and id!='$faqId'") or die('Error faq' . mysqli_error($db_con));
                            $rwchckDup = mysqli_num_rows($checkDub);
                           if ($rwchckDup < 1) {
                            $updatefaq = ("UPDATE `tbl_faq_master` SET `question`='$faqQues',`answer`='$faqAns',`user_id`='$userId',`dateposted`='$date' WHERE id = '$faqId'") or die('Error in update' . mysqli_error($db_con));
                            $faqUpdate = mysqli_query($db_con, $updatefaq) or die('Error faq' . mysqli_error($db_con));
                            if ($faqUpdate) {
                                $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,'Ques. $rwoldfaq[question] to $faqQues Updated','$date',null,'$host',null)") or die('error : ' . mysqli_error($db_con));
                                echo'<script>taskSuccess("faq", "' . $lang['ques_ans_edit'] . '");</script>';
                            } else {
                                echo'<script>taskFailed("faq", "' . $lang['edt_fqus'] . '");</script>';
                            }
                            } else {
                                echo'<script>taskFailed("faq", "' . $lang['already_exist'] . '");</script>';
                            }
                            mysqli_close($db_con);
                        }
                        //Delete faq ques. & Ans.
                        if (isset($_POST['delfaq'])) {
                            $QId = mysqli_real_escape_string($db_con, $_POST['Qid']);
                            $Delfaq = mysqli_query($db_con, "SELECT question FROM `tbl_faq_master` WHERE id = '$QId'") or die('Error in faq delete' . mysqli_error($db_con));
                            $rwDelfaq = mysqli_fetch_assoc($Delfaq) or die('Error faq del fetch' . mysqli_error($db_con));
                            $delfaq = "DELETE FROM `tbl_faq_master` WHERE id = '$QId'" or die('Error in faq insert' . mysqli_error($db_con));
                            $Rwdelfaq = mysqli_query($db_con, $delfaq) or die('Error faq' . mysqli_error($db_con));
                            if ($Rwdelfaq) {
                                $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,'Ques. & Ans. $rwDelfaq[question] Deleted','$date',null,'$host',null)") or die('error : ' . mysqli_error($db_con));
                                echo'<script>taskSuccess("faq", "' . $lang['ques_ans_del'] . '");</script>';
                            }
                            mysqli_close($db_con);
                        }
                        ?>