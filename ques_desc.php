<!DOCTYPE html>
<html>
    <?php
    require_once './loginvalidate.php';
    // require_once './application/config/database.php';
    require_once './application/pages/head.php';

    //for user role
    $chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$_SESSION[cdes_user_id]', user_ids) > 0") or die('Error:' . mysqli_error($db_con));

    $rwgetRole = mysqli_fetch_assoc($chekUsr);

    if ($rwgetRole['view_faq'] != '1') {
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
                    <div class="container">

                        <!-- Page-Title -->
                        <div class="row">
                            <div class="col-sm-12">
                                <ol class="breadcrumb">

                                    <li>
                                        <a href="ques_desc"><?php echo $lang['ques']; ?></a>
                                    </li>
                                    <a href="index" class="btn btn-primary waves-effect waves-light pull-right btn-sm margin-t-9" onclick="goPrevious();" title="<?php echo $lang['Go_to_previous_page']; ?>"><i class="fa fa-arrow-circle-left"></i></a>
                                </ol>
                            </div>
                        </div>

                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <div class="col-sm-10">
                                    <div class="col-sm-10">
                                        <h4 class="header-title"><?php echo $lang['ques']; ?></h4>
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
                                        <!-- Question Fetch query start here -->

                                        <div class="col-lg-12"> 
                                            <?php
                                            $i = 1;
                                            $descName = mysqli_query($db_con, "SELECT * FROM `tbl_desc_answ`") or die('Error in descName' . mysqli_error($db_con));
                                            while ($rwdescName = mysqli_fetch_assoc($descName)) {
                                                ?>
                                                <div class="panel-group" id="accordion-test-2"> 
                                                    <div class="panel panel-default"> 
                                                        <div class="panel-heading"> 
                                                            <h4 class="panel-title"> 
                                                                <a data-toggle="collapse" data-parent="#accordion-test-2" href="#collapse<?php echo $i; ?>" aria-expanded="false" class="collapsed">
                                                                    <?php
                                                                    echo $i . '.';
                                                                    echo $rwdescName['question'] . '?';
                                                                    ?>

                                                                </a> 
                                                                <div class="faqedit">
                                                                    <?php if ($rwgetRole['edit_faq'] == '1') { ?>
                                                                        <span class="fa fa-edit" data-toggle="modal" data-target="#desc-edit" id="editRow" data="<?php echo $rwdescName['id']; ?>" title="<?= $lang['Modify_column'] ?>"></span>
                                                                    <?php } if ($rwgetRole['del_faq'] == '1' && $_SESSION['cdes_user_id'] == '1') { ?>
                                                                        <span class="fa fa-trash-o" data-toggle="modal" data-target="#faq-delete" id="delFaq" data="<?php echo $rwdescName['id']; ?>" title="<?= $lang['Delete'] ?>"></span>
                                                                    <?php } ?>
                                                                </div>
                                                            </h4> 
                                                        </div> 
                                                        <div id="collapse<?php echo $i; ?>" class="panel-collapse collapse"> 
                                                            <div class="panel-body faqtext">
                                                                <?php echo $rwdescName['answer']; ?>
                                                            </div> 
                                                        </div> 
                                                    </div>

                                                </div>
                                                <?php
                                                $i++;
                                            }
                                            ?>
                                        </div>
                                        <!-- /Question Fetch query start here -->
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
                                                    <label><?= $lang['ques'] ?><span class="text-alert">*</span></label>
                                                    <input type="text" name="desc" class="form-control" id="" placeholder="<?= $lang['ur_que'] ?>..."required>
                                                </div>
                                                <div class="form-group">
                                                    <label><?= $lang['ans'] ?><span class="text-alert">*</span></label>
                                                    <textarea class="form-control" rows="5"  name="descansss" id="descansss"></textarea>
<!--                                                    <input type="text" name="faqans" class="form-control" id="faqans" placeholder="Enter your Answer here..."required>-->
                                                </div>
                                            </div>
                                        </div> 
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal"><?= $lang['Close'] ?></button> 
                                            <button type="submit" name="adddesc" class="btn btn-primary"><?= $lang['Submit'] ?></button> 
                                        </div>
                                    </form>

                                </div> 
                            </div>
                        </div><!-- /.modal -->

                        <div id="desc-edit" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                            <div class="modal-dialog modal-lg"> 
                                <div class="modal-content"> 
                                    <form method="post">
                                        <div class="modal-header"> 
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                                            <h4 class="modal-title"> <?= $lang['edt_qus'] ?></h4> 
                                        </div>

                                        <div class="modal-body" id="modiFaq">

                                        </div> 
                                        <div class="modal-footer">

                                            <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal"><?= $lang['Close'] ?></button> 
                                            <button type="submit" name="editdescc" class="btn btn-primary"><?= $lang['Save'] ?></button> 
                                        </div>
                                    </form><!-- /.modal -->

                                </div> 
                            </div>
                        </div>
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
                                //alert(id);
                                $.post("application/ajax/editQueAnsDesc.php", {ID: id}, function (result, status) {
                                    // $.post("application/ajax/displayImage.php", {PATH: path}, function (result, status) {
                                    if (status == 'success') {
                                        $("#modiFaq").html(result);
                                        //        alert(result);
                                    }
                                });
                            });

                            $("span#delFaq").click(function () {
                                var id = $(this).attr('data');
                                $("#Qid").val(id);
                            });

                        </script>


                        <!-- Modal Table insert update query start here -->
                        <?php
                        //Add faq  ques. & Ans.

                        if (isset($_POST['adddesc'], $_POST['token'])) {
                            $faqQues = mysqli_real_escape_string($db_con,  preg_replace("/[^\w$\x{0080}-\x{FFFF}!(){}+=~@%&#.: -]+/u", "", $_POST['desc']));
                            $faqAns = mysqli_real_escape_string($db_con,  preg_replace("/[^\w$\x{0080}-\x{FFFF}!(){}+=~@%&#.: -]+/u", "", $_POST['descansss']));
                            $userId = $_SESSION['cdes_user_id'];
                            $date = date('Y-m-d H:i:s');
                            //echo "select * from tbl_desc_answ where question='$faqQues'"; die;
                            $checkDub = mysqli_query($db_con, "select * from tbl_desc_answ where question='$faqQues'") or die('Error faq' . mysqli_error($db_con));
                            $rwchckDup = mysqli_num_rows($checkDub);
                            if ($rwchckDup < 1) {
                                $insertfaq = "INSERT INTO `tbl_desc_answ`(`id`, `question`, `answer`, `user_id`, `dateposted`) VALUES (null, '$faqQues', '$faqAns', '$userId', '$date')" or die('Error in faq insert' . mysqli_error($db_con));
                                $faq = mysqli_query($db_con, $insertfaq) or die('Error faq' . mysqli_error($db_con));
                                if ($faq) {
                                    $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`, `action_name`, `start_date`, `system_ip`, `remarks`) values ('$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]','Help Added','$date','$host','Ques. $faqQues Added')") or die('error : ' . mysqli_error($db_con));
                                    echo'<script>taskSuccess("ques_desc", "' . $lang['ques_ans_add'] . '");</script>';
                                } else {
                                    echo'<script>taskFailed("ques_desc", "' . $lang['add_failed'] . '");</script>';
                                }
                            } else {
                                echo'<script>taskFailed("ques_desc", "' . $lang['already_exist'] . '");</script>';
                            }
                            mysqli_close($db_con);
                        }

                        if (isset($_POST['editdescc'], $_POST['token'])) {

                            $descid = $_POST['descid'];
                            $updatedesc = mysqli_real_escape_string($db_con,  preg_replace("/[^\w$\x{0080}-\x{FFFF}!(){}+=~@%&#.: -]+/u", "", $_POST['updateques']));
                            $updateans = mysqli_real_escape_string($db_con,  preg_replace("/[^\w$\x{0080}-\x{FFFF}!(){}+=~@%&#.: -]+/u", "", $_POST['updateans']));
                            $userId = $_SESSION['cdes_user_id'];
                            $date = date('Y-m-d H:i:s');

                            $olddesc = mysqli_query($db_con, "SELECT question FROM `tbl_desc_answ` WHERE id ='$descid'") or die('Error in old name' . mysqli_error($db_con));
                            $rwolddesc = mysqli_fetch_assoc($olddesc) or die('Error faq old name' . mysqli_error($db_con));
                            $checkDub = mysqli_query($db_con, "select * from tbl_desc_answ where question='$updatedesc' and id!='$descid'") or die('Error faq' . mysqli_error($db_con));
                            $rwchckDup = mysqli_num_rows($checkDub);
                            if ($rwchckDup < 1) {

                                $Update = mysqli_query($db_con, "UPDATE `tbl_desc_answ` SET `question`='$updatedesc',`answer`='$updateans',`user_id`='$userId',`dateposted`='$date' WHERE id='$descid'"); // or die('Error faq update' . mysqli_error($db_con));
                                if ($Update) {
                                    $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`,`action_name`, `start_date`,`system_ip`, `remarks`) values ('$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]','Help Edited','$date','$host','Ques. $rwolddesc[question] to $updatedesc Updated')") or die('error : ' . mysqli_error($db_con));
                                    echo'<script>taskSuccess("ques_desc", "' . $lang['ques_ans_edit'] . '");</script>';
                                } else {
                                    echo'<script>taskFailed("ques_desc", "' . $lang['edt_fqus'] . '");</script>';
                                }
                            } else {
                                echo'<script>taskFailed("ques_desc", "' . $lang['already_exist'] . '");</script>';
                            }
                            mysqli_close($db_con);
                        }
                        //Delete faq ques. & Ans.
                        if (isset($_POST['delfaq'], $_POST['token'])) {
                            $QId = mysqli_real_escape_string($db_con, $_POST['Qid']);
                            $Delfaq = mysqli_query($db_con, "SELECT question FROM `tbl_desc_answ` WHERE id = '$QId'") or die('Error in faq delete' . mysqli_error($db_con));
                            $rwDelfaq = mysqli_fetch_assoc($Delfaq) or die('Error faq del fetch' . mysqli_error($db_con));
                            $delfaq = "DELETE FROM `tbl_desc_answ` WHERE id = '$QId'" or die('Error in faq insert' . mysqli_error($db_con));
                            $Rwdelfaq = mysqli_query($db_con, $delfaq) or die('Error faq' . mysqli_error($db_con));
                            if ($Rwdelfaq) {
                                $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`, `action_name`, `start_date`, `system_ip`, `remarks`) values ('$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]','Help Deleted','$date','$host','Ques. & Ans. $rwDelfaq[question] Deleted')") or die('error : ' . mysqli_error($db_con));
                                echo'<script>taskSuccess("ques_desc", "' . $lang['ques_ans_del'] . '");</script>';
                            }
                            mysqli_close($db_con);
                        }
                        ?>


                        <!-- /Modal Table insert update query start here -->