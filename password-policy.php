<!DOCTYPE html>
<html>
    <?php
    require_once 'loginvalidate.php';
    require_once 'application/pages/head.php';
    
    $getque = mysqli_query($db_con, "select * from `tbl_pass_policy`") or die('Error' . mysqli_error($db_con));
    $rwque = mysqli_fetch_assoc($getque);
    if ($rwgetRole['password_policy'] != '1' || $rwque['feature_enable_disable'] != '1') {
        header('Location: ./index');
    }
    ?>
    <?php
   
    ?>

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
                                <li><a href="#"><?= $lang['Administrative_tool']; ?></a></li>
                                <li class="active"><?= $lang['Password_Policy']; ?></li>
                                <li> <a href="#" data-toggle="modal" data-target="#help-modal" id="helpview" data="19" title="<?= $lang['help']; ?>"><i class="fa fa-question-circle" style="font-size: 23px"></i> </a></li>
                                <a href="javascript:void(0)" class="btn btn-primary waves-effect waves-light pull-right btn-sm margin-t-9" onclick="goPrevious();" title="<?php echo $lang['Go_to_previous_page']; ?>"><i class="fa fa-arrow-circle-left"></i></a>
                            </ol>
                        </div>
                        <div class="row">
                            <div class="box box-primary">
                                <div class="box-header with-border">
                                    <div class="col-sm-12">
                                        <h4 class="header-title"><?php echo $lang['password_policy_title']; ?></h4>
                                    </div>
                                </div>
                                <div class="card-box">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="table-responsive">
                                                <form method="post">
                                                    <table class="table table-hover mails table-striped table-bordered">
                                                        <tbody>
                                                            <tr class="unread">
                                                                <td class="mail-select">
                                                                    <div class="error form-group" style="display:none">
                                                                        <p> <?php echo $lang['password_length']; ?></p>
                                                                    </div>

                                                                    <div class="form-group">
                                                                        <label><?= $lang['Maximum_Password_Length']; ?> : <span class="text-alert">*</span></label>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <input type="text" class="form-control alphalock" name="maxlen" min="0" id="maxId" data-parsley-pattern="^[0-9]" value="<?php echo ((!empty($rwque['maxlen'])) ? $rwque['maxlen'] : ''); ?>" required >
                                                                    </div>

                                                                </td>
                                                                <td class="mail-select">

                                                                    <div class="form-group">
                                                                        <label><?= $lang['Minimum_Password_Length']; ?> : <span class="text-alert">*</span></label>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <input type="text" class="form-control alphalock" name="minlen" id="minId" min="0" value="<?php echo ((!empty($rwque['minlen'])) ? $rwque['minlen'] : ''); ?>" required>
                                                                    </div>

                                                                </td>
                                                            </tr>

                                                            <tr class="unread">
                                                                <td class="mail-select">
                                                                    <div class="checkbox checkbox-primary">
                                                                        <input type="checkbox" id="uppercase" name="uppercase" value="1"<?php
                                                                        echo (($rwque['uppercase'] == '1') ? "checked" : "");
                                                                        ?>>                                            
                                                                        <label for="uppercase"><?= $lang['uppercase_letter']; ?></label>
                                                                    </div>
                                                                </td>
                                                                <td class="mail-select">
                                                                    <div class="checkbox checkbox-primary">
                                                                        <input type="checkbox" id="lowercase" name="lowercase"value="1" <?php
                                                                        echo (($rwque['lowercase'] == '1') ? "checked" : "");
                                                                        ?>>
                                                                        <label for="lowercase"><?= $lang['lowercase_letter']; ?></label>
                                                                    </div>
                                                                </td>

                                                            </tr>
                                                            <tr class="unread">
                                                                <td class="mail-select">
                                                                    <div class="checkbox checkbox-primary">
                                                                        <input type="checkbox" id="numbers" name="numbers" value="1" <?php
                                                                        echo (($rwque['numbers'] == '1') ? "checked" : "");
                                                                        ?>>
                                                                        <label for="numbers"><?= $lang['least_one_number']; ?></label>
                                                                    </div>
                                                                </td>
                                                                <td class="mail-select">
                                                                    <div class="checkbox checkbox-primary">
                                                                        <input type="checkbox" id="s_char" name="s_char" value="1" <?php
                                                                        echo (($rwque['s_char'] == '1') ? "checked" : "");
                                                                        ?>>
                                                                        <label for="s_char"><?= $lang['non-alphanumeric_character']; ?></label>
                                                                    </div>
                                                                </td>

                                                            </tr>

                                                            <tr class="unread">
                                                                <td class="mail-select" colspan="2">
                                                                    <div class="checkbox checkbox-primary">
                                                                        <input type="checkbox" id="edate" name="edate" value="1"<?php
                                                                        echo (($rwque['edate'] != '0') ? "checked" : "");
                                                                        ?>>
                                                                        <label for="edate"><?= $lang['Password_Expiration']; ?></label>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr class="unread">
                                                                <td class="mail-select" id="expiry" style="display:none; width: 10px;">
                                                                    <label><?= $lang['Password_expiration_period']; ?><span class="text-alert">*</span></label>
                                                                    <input type="text" class="form-control alphalock" name="edate" id="day" min="0" value="<?php echo (!empty($rwque['edate']) ? $rwque['edate'] : ""); ?>" maxlength="9">
                                                                </td>
                                                                <td class="mail-select">
                                                                    <button class="btn btn-primary m-t-20" id="applyButton" name="changePolicy" type="submit"><?= $lang['Change_Password_Policy']; ?></button>
                                                                </td>
                                                            </tr>

                                                        </tbody>

                                                    </table>
                                                </form>
                                            </div>

                                        </div>                                      
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php require_once './application/pages/footer.php'; ?>
            <?php require_once './application/pages/footerForjs.php'; ?>
        </div>
        <script>
            $("#minId, #maxId").focusout(function () {
                if (parseFloat($("#maxId").val()) < parseFloat($("#minId").val()))
                {
                    $(".error").css("display", "block").css("color", "red");
                    $("#applyButton").prop('disabled', true);
                } else {
                    $(".error").css("display", "none");
                    $("#applyButton").prop('disabled', false);
                }

            });
            $(function () {

                $('#edate').click(function () {
                    if ($("input[name='edate']:checked").val()) {
                        $('#expiry').show();
                        $("#day").prop('required', true);
                    } else {
                        $('#expiry').hide();
                        $("#day").prop('required', false);
                        $("#day").val("");
                    }
                });
            });
            $('.alphalock').keyup(function (e)
            {
                if (/\D/g.test(this.value))
                {
                    // Filter non-digits from input value.
                    this.value = this.value.replace(/\D/g, '');
                }
            });
        </script>
        <script>
              $(document).ready(function () {
            if ($("input[name='edate']:checked").val()) {
                $('#expiry').show();
                $("#day").prop('required', true);
            } else {
                $('#expiry').hide();
                $("#day").prop('required', false);
                $("#day").val("");
            }
              });
        </script>
       
        <?php
        if (isset($_POST['changePolicy'], $_POST['token'])) {
            $minlen = $_POST['minlen'];
            $minlen = mysqli_real_escape_string($db_con, $minlen);
            $maxlen = $_POST['maxlen'];
            $maxlen = mysqli_real_escape_string($db_con, $maxlen);
            $lowercase = $_POST['lowercase'];
            if ($lowercase == 1) {
                $lowercase = 1;
            } else {
                $lowercase = 0;
            }
            $uppercase = $_POST['uppercase'];
            if ($uppercase == 1) {
                $uppercase = 1;
            } else {
                $uppercase = 0;
            }
            $numbers = $_POST['numbers'];
            if ($numbers == 1) {
                $numbers = 1;
            } else {
                $numbers = 0;
            }
            $s_char = $_POST['s_char'];
            if ($s_char == 1) {
                $s_char = 1;
            } else {
                $s_char = 0;
            }

            if (!empty($_POST['edate'])) {
                $edate = $_POST['edate'];
            } else {
                $edate = '0';
            }
            $changePolicy = mysqli_query($db_con, "update tbl_pass_policy set `minlen`='$minlen',`maxlen`='$maxlen',`lowercase`='$lowercase',`uppercase`='$uppercase',`numbers`='$numbers',`s_char`='$s_char',`edate`='$edate'"); //or die('Error ::' . mysqli_error($db_con));

            if ($changePolicy) {
                echo '<script>taskSuccess("' . $_SERVER['REQUEST_URI'] . '","' . $lang['password_rule_set'] . '");</script>';
            } else {
                echo '<script>taskFailed("' . $_SERVER['REQUEST_URI'] . '","' . $lang['Unable to set password rule'] . '");</script>';
            }
        }
        ?>
    </body>
</html>