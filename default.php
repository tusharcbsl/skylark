
<?php
require_once('loginvalidate.php');
require_once('./application/config/database.php');
require_once './application/pages/function.php';
require_once './application/pages/feature-enable-disable.php';

$sameGroupIDs = array();
$group = mysqli_query($db_con, "select group_id from tbl_bridge_grp_to_um where find_in_set('$_SESSION[cdes_user_id]',user_ids)") or die('Error' . mysqli_error($db_con));
while ($rwGroup = mysqli_fetch_assoc($group)) {
    $sameGroupIDs[] = $rwGroup['group_id'];
}
$sameGroupIDs = array_unique($sameGroupIDs);
sort($sameGroupIDs);
$sameGroupIDs = implode(',', $sameGroupIDs);

if ($rwgetRole['app_default'] != '1') {
    header('Location: ./index');
}
$totallabel = count($lang);
$totalang = mysqli_query($db_con, "select * from tbl_language") or die('Error : ' . mysqli_error($db_con));
$totalangCount = mysqli_num_rows($totalang);

$rwInfoPolicy = getPasswordPolicy($db_con);

$waterMarkEnabled = mysqli_query($db_con, "CALL select_data('tbl_watermark_setting', 'is_enable', '', '')");
$db_con->next_result();
$isWatermarkEnabled = mysqli_fetch_assoc($waterMarkEnabled);

?>
<!DOCTYPE html>
<html>
    <?php require_once './application/pages/head.php';
    ?>

    <link href="assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css" rel="stylesheet">
    <link href="assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
    <script src="https://www.google.com/jsapi" type="text/javascript">
    </script>  
    <script type="text/javascript">
        // Load the Google Transliterate API
        google.load("elements", "1", {
            packages: "transliteration"
        });
        function onLoad() {
            console.log("onload");
            var langcode = '<?php echo $rwgetRole['langCode']; ?>';
            var options = {
                sourceLanguage: 'en',
                destinationLanguage: [langcode],
                shortcutKey: 'ctrl+g',
                transliterationEnabled: true
            };
            // Create an instance on TransliterationControl with the required
            // options.

<?php
$result = array();
$keyarray = array_keys($lang);
$keyarray = implode('","', $keyarray);
?>
            var control = new google.elements.transliteration.TransliterationControl(options);
// var array = str.split(",	", 100);
            var ids = ["def_page", "<?php echo $keyarray ?>"];
            control.makeTransliteratable(ids);
        }
        google.setOnLoadCallback(onLoad);
    </script>
    <?php
    $slids = findsubfolder($slpermIdes, $db_con);
    $slids = implode(',', $slids);
    $retention = mysqli_query($db_con, "SELECT * FROM `tbl_document_master` where retention_period IS NOT NULL and flag_multidelete='1' and doc_name in($slids)");
    if ($rwretention = mysqli_num_rows($retention) < 1) {
        $rwretention = 0;
    } else {
        $rwretention = mysqli_num_rows($retention);
    }
    $expiry = mysqli_query($db_con, "SELECT * FROM tbl_document_master WHERE flag_multidelete='2' and doc_name in($slids)");
    if ($rwexpiry = mysqli_num_rows($expiry) < 1) {
        $rwexpiry = 0;
    } else {
        $rwexpiry = mysqli_num_rows($expiry);
    }

    //default language 
    $chklang = mysqli_query($db_con, "SELECT lang_name FROM `tbl_language` where default_language='1'");
    $rwchklang = mysqli_fetch_assoc($chklang);
    ?>
    <body class="fixed-left">
        <!-- Begin page -->
        <div id="wrapper">
            <!-- Top Bar Start -->
            <?php require_once './application/pages/topBar.php'; ?>
            <!-- Top Bar End -->
            <!-- ========== Left Sidebar Start ========== -->
            <?php require_once './application/pages/sidebar.php'; ?>
            <link href="assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />                    
            <div class="content-page">
                <!-- Start content -->
                <div class="content">
                    <div class="wraper container">
                        <!-- Page-Title -->
                        <div class="row">
                            <div class="col-sm-12">
                                <ol class="breadcrumb">
                                    <li class="active"><?php echo $lang['App_default']; ?></li>
                                    <li> <a href="#" data-toggle="modal" data-target="#help-modal" id="helpview" data="37" title="<?= $lang['help']; ?>"><i class="fa fa-question-circle"></i> </a></li>

                                    <a href="javascript:void(0)" class="btn btn-primary waves-effect waves-light pull-right btn-sm margin-t-9" onclick="goPrevious();" title="<?php echo $lang['Go_to_previous_page']; ?>"><i class="fa fa-arrow-circle-left"></i></a>
                                </ol>
                            </div>
                        </div>
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <div class="col-sm-12">
                                    <h4 class="header-title"> <?php echo $lang['set_default_lang']; ?></h4>
                                </div>
                            </div>
                            <div class="panel">
                                <div class="panel-body">
                                    <div class="box-body">
                                        <div class="col-lg-12"> 
                                            <div class="row">
                                                <?php if ($rwgetRole['customize_label'] == '1') { ?>
                                                    <div class="col-lg-4">
                                                        <div class="card-box">
                                                            <div class="bar-widget">
                                                                <div class="table-box">
                                                                    <div class="table-detail">
                                                                        <a href="#" data-toggle="modal" data-target="#custom-width-modal">
                                                                            <div class="iconbox bg-info">
                                                                                <i class="ti-marker-alt"></i>
                                                                            </div>
                                                                        </a>
                                                                    </div>
                                                                    <div class="table-detail">
                                                                        <h4 class="m-t-0 m-b-5"><b><?= $totallabel; ?></b></h4>
                                                                        <p class="text-primary m-b-0 m-t-0"><?php echo $lang['edit_label'] ?></p>
                                                                    </div>

                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php } if ($rwgetRole['default_lang_setting'] == '1') { ?>
                                                    <div class="col-lg-4">
                                                        <div class="card-box">
                                                            <div class="bar-widget">
                                                                <div class="table-box">
                                                                    <div class="table-detail">
                                                                        <a href="#" data-toggle="modal" data-target="#default-language">
                                                                            <div class="iconbox bg-custom">
                                                                                <i class="icon-layers"></i>
                                                                            </div>
                                                                        </a>
                                                                    </div>
                                                                    <div class="table-detail">
                                                                        <h5 class="m-t-0 m-b-5"><strong><?php echo $rwchklang['lang_name']; ?></strong></h5>
                                                                        <p class="text-primary m-b-0 m-t-0"><?= $lang['Default_Language']; ?></p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php } if ($rwgetRole['doc_exp_setting'] == '1') { ?>
                                                    <div class="col-lg-4">
                                                        <div class="card-box">
                                                            <div class="bar-widget">
                                                                <div class="table-box">
                                                                    <div class="table-detail">
                                                                        <a href="#" data-toggle="modal" data-target="#expdocument">
                                                                            <div class="iconbox bg-warning">
                                                                                <i class="fa fa-history"></i>
                                                                            </div>
                                                                        </a>
                                                                    </div>
                                                                    <div class="table-detail">
                                                                        <b class="text-primary" style="font-size: 15px;"><?= $rwexpiry; ?></b><?php echo (($rwgetexpInfo['exp_feature_enable'] == '1') ? " (<b class='text-success'>" . $lang['enable'] . ")</b>" : " (<b class='text-danger'>" . $lang['disable'] . ")</b>"); ?>
                                                                        <p class="text-primary m-b-0 m-t-0"><?= $lang['expiry_document']; ?></p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php }if ($rwgetRole['doc_retention_setting'] == '1') { ?>
                                                    <div class="col-lg-4">
                                                        <div class="card-box">
                                                            <div class="bar-widget">
                                                                <div class="table-box">
                                                                    <div class="table-detail">
                                                                        <a href="#" data-toggle="modal" data-target="#retention-file">
                                                                            <div class="iconbox bg-danger">
                                                                                <i class="fa fa-hourglass-half"></i>
                                                                            </div>
                                                                        </a>
                                                                    </div>
                                                                    <div class="table-detail">
                                                                        <b class="text-primary" style="font-size: 15px;"><?= $rwretention; ?></b><?php echo (($rwgetInfo['retention_feature_enable'] == '1') ? " (<b class='text-success'>" . $lang['enable'] . ")</b>" : " (<b class='text-danger'>" . $lang['disable'] . ")</b>"); ?>
                                                                        <p class="text-primary m-b-0 m-t-0"><?= $lang['Retention_document']; ?></p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php }if ($rwgetRole['doc_share_setting'] == '1') { ?>
                                                    <div class="col-lg-4">
                                                        <div class="card-box">
                                                            <div class="bar-widget">
                                                                <div class="table-box">
                                                                    <div class="table-detail">
                                                                        <a href="#" data-toggle="modal" data-target="#share-document">
                                                                            <div class="iconbox bg-primary">
                                                                                <i class="fa fa-share-square"></i>
                                                                            </div>
                                                                        </a>
                                                                    </div>
                                                                    <div class="table-detail">
                                                                        <?php echo (($rwdocshare['docshare_enable_disable'] == '1') ? "<b class='text-success'>" . $lang['enable'] . "</b>" : "<b class='text-danger'>" . $lang['disable'] . "</b>") ?>
                                                                        <p class="text-primary m-b-0 m-t-0"><?= $lang['Share_docs_with_time']; ?></p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php }if ($rwgetRole['password_policy'] == '1') { ?>
                                                    <div class="col-lg-4">
                                                        <div class="card-box">
                                                            <div class="bar-widget">
                                                                <div class="table-box">
                                                                    <div class="table-detail">
                                                                        <a href="#" data-toggle="modal" data-target="#pass-policy">
                                                                            <div class="iconbox bg-primary">
                                                                                <i class="fa fa-lock"></i>
                                                                            </div>
                                                                        </a>
                                                                    </div>
                                                                    <div class="table-detail">
                                                                        <?php echo (($rwInfoPolicy['feature_enable_disable'] == '1') ? "<b class='text-success'>" . $lang['enable'] . "</b>" : "<b class='text-danger'>" . $lang['disable'] . "</b>") ?>
                                                                        <p class="text-primary m-b-0 m-t-0"><?= $lang['Password_Policy']; ?></p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                               <?php } if ($rwgetRole['login_otp'] == '1') { ?>
                                                    <div class="col-lg-4">
                                                        <div class="card-box">
                                                            <div class="bar-widget">
                                                                <div class="table-box">
                                                                    <div class="table-detail">
                                                                        <a href="#" data-toggle="modal" data-target="#loginotpenabale">
                                                                            <div class="iconbox bg-warning">
                                                                                <i class="fa fa-envelope"></i>
                                                                            </div>
                                                                        </a>
                                                                    </div>
                                                                    <div class="table-detail">
                                                                        <?php echo (($rwInfoPolicy['loginotp_enable_disable'] == '1') ? "<b class='text-success'>" . $lang['enable'] . "</b>" : "<b class='text-danger'>" . $lang['disable'] . "</b>") ?>
                                                                        <p class="text-primary m-b-0 m-t-0"><?= $lang['login_with_otp']; ?></p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
													
													<?php }if ($rwgetRole['login_otp_mobile'] == '1') {
                                                    ?>
													
													<div class="col-lg-4">
                                                        <div class="card-box">
                                                            <div class="bar-widget">
                                                                <div class="table-box">
                                                                    <div class="table-detail">
                                                                        <a href="#" data-toggle="modal" data-target="#mobileloginotpenabale">
                                                                            <div class="iconbox bg-warning">
                                                                                <i class="fa fa-mobile"></i>
                                                                            </div>
                                                                        </a>
                                                                    </div>
                                                                    <div class="table-detail">
                                                                        <?php echo (($rwInfoPolicy['mobile_loginotp_enable_disable'] == '1') ? "<b class='text-success '>" . $lang['enable'] . "</b>" : "<b class='text-danger'>" . $lang['disable'] . "</b>") ?>
                                                                        <p class="text-primary m-b-0 m-t-0"><?= $lang['login_with_otp_mobile']; ?></p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
													
                                                    
                                                 <?php } if ($rwgetRole['login_captcha'] == '1') { ?>
                                                    <div class="col-lg-4">
                                                        <div class="card-box">
                                                            <div class="bar-widget">
                                                                <div class="table-box">
                                                                    <div class="table-detail">
                                                                        <a href="#" data-toggle="modal" data-target="#logincaptchaenabale">
                                                                            <div class="iconbox bg-warning">
                                                                                <i class="fa fa-cog"></i>
                                                                            </div>
                                                                        </a>
                                                                    </div>
                                                                    <div class="table-detail">
                                                                        <?php echo (($rwInfoPolicy['captcha_enabled'] == '1') ? "<b class='text-success'>" . $lang['enable'] . "</b>" : "<b class='text-danger'>" . $lang['disable'] . "</b>") ?>
                                                                        <p class="text-primary m-b-0 m-t-0"><?= $lang['login_with_captcha']; ?></p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php } ?>
												
												<?php if ($rwgetRole['set_watermark'] == '1') { ?>
                                                    <div class="col-lg-4">
                                                        <div class="card-box">
                                                            <div class="bar-widget">
                                                                <div class="table-box">
                                                                    <div class="table-detail">
                                                                        <a href="#" data-toggle="modal" data-target="#watermarkenable">
                                                                            <div class="iconbox bg-primary">
                                                                                <i class="fa fa-file-pdf-o"></i>
                                                                            </div>
                                                                        </a>
                                                                    </div>
                                                                    <div class="table-detail">
                                                                        <?php echo (($isWatermarkEnabled['is_enable'] == '1') ? "<b class='text-success'>" . $lang['enable'] . "</b>" : "<b class='text-danger'>" . $lang['disable'] . "</b>") ?>
                                                                        <p class="text-primary m-b-0 m-t-0"><?= $lang['show_watermark']; ?></p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                               
												
												
													<?php 
													if ($isWatermarkEnabled['is_enable'] == '1') {

													$getWatermarkDet = mysqli_query($db_con, "CALL select_data('tbl_watermark_master','*','','')");
													$db_con->next_result();
													$query_row = mysqli_fetch_assoc($getWatermarkDet);

													$getWatermarkSetting = mysqli_query($db_con, "CALL select_data('tbl_watermark_setting', '*', '', '')");
													$db_con->next_result();
													$wsetting = mysqli_fetch_assoc($getWatermarkSetting);

													?>
                                                    <div class="col-lg-4">
                                                        <div class="card-box">
                                                            <div class="bar-widget">
                                                                <div class="table-box">
                                                                    <div class="table-detail">
                                                                        <a href="#" data-toggle="modal" data-target="#setwatermark">
                                                                            <div class="iconbox bg-primary">
                                                                                <i class="fa fa-pencil"></i>
                                                                            </div>
                                                                        </a>
                                                                    </div>
                                                                    <div class="table-detail">
                                                                        <?php echo (($wsetting['is_customized'] == '1') ? "<b class='text-success'>" . $lang['enable'] . "</b>" : "<b class='text-danger'>" . $lang['disable'] . "</b>") ?>
                                                                        <p class="text-primary m-b-0 m-t-0"><?= $lang['update_custom_watermark']; ?></p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php } ?>
											 <?php } ?>
												
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                            <div id="loginotpenabale" class="modal fade" role="dialog" aria-labelledby="custom-width-modalLabel" aria-hidden="true" style="display: none;">
                            <div class="modal-dialog">
                                <div class="panel panel-color panel-danger">
                                    <div class="panel-heading">
                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                        <h2 class="panel-title" id="custom-width-modalLabel"><?php echo $lang['ARE_YOU_SURE']; ?></h2>
                                    </div>
                                    <form method="post" enctype="multipart/form-data">
                                        <div class="modal-body">
                                            <?php if ($rwInfoPolicy['loginotp_enable_disable'] != '1') { ?>
                                                <h5><?php echo $lang['loginotp_disable'] ?></h5>
                                            <?php } else { ?>
                                                <h5><?php echo $lang['loginotp_enable'] ?></h5>
                                            <?php } ?>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button>
                                            <?php if ($rwInfoPolicy['loginotp_enable_disable'] != '1') { ?>
                                                <button type="submit" name="loginotpenable" class="btn btn-success waves-effect waves-light"><?php echo $lang['enable']; ?></button>
                                            <?php } else { ?>
                                                <button type="submit" name="loginotpdisable" class="btn btn-danger waves-effect waves-light"><?php echo $lang['disable']; ?></button>
                                            <?php } ?>
                                        </div>
                                    </form>

                                </div><!-- /.modal-content -->
                            </div><!-- /.modal-dialog -->
                        </div><!-- /.modal -->

                        <div id="logincaptchaenabale" class="modal fade" role="dialog" aria-labelledby="custom-width-modalLabel" aria-hidden="true" style="display: none;">
                            <div class="modal-dialog">
                                <div class="panel panel-color panel-danger">
                                    <div class="panel-heading">
                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                        <h2 class="panel-title" id="custom-width-modalLabel"><?php echo $lang['ARE_YOU_SURE']; ?></h2>
                                    </div>
                                    <form method="post" enctype="multipart/form-data">
                                        <div class="modal-body">
                                            <?php if ($rwInfoPolicy['captcha_enabled'] != '1') { ?>
                                                <h5><?php echo $lang['login_captcha_disable'] ?></h5>
                                            <?php } else { ?>
                                                <h5><?php echo $lang['login_captcha_enable'] ?></h5>
                                            <?php } ?>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button>
                                            <?php if ($rwInfoPolicy['captcha_enabled'] != '1') { ?>
                                                <button type="submit" name="captchaenable" class="btn btn-success waves-effect waves-light"><?php echo $lang['enable']; ?></button>
                                            <?php } else { ?>
                                                <button type="submit" name="captchadisable" class="btn btn-danger waves-effect waves-light"><?php echo $lang['disable']; ?></button>
                                            <?php } ?>
                                        </div>
                                    </form>

                                </div><!-- /.modal-content -->
                            </div><!-- /.modal-dialog -->
                        </div><!-- /.modal -->

                        <div id="share-document" class="modal fade" role="dialog" aria-labelledby="custom-width-modalLabel" aria-hidden="true" style="display: none;">
                            <div class="modal-dialog">
                                <div class="panel panel-color panel-danger">
                                    <div class="panel-heading">
                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                        <h2 class="panel-title" id="custom-width-modalLabel"><?php echo $lang['ARE_YOU_SURE']; ?></h2>
                                    </div>
                                    <form method="post" enctype="multipart/form-data">
                                        <div class="modal-body">
                                            <?php if ($rwdocshare['docshare_enable_disable'] != '1') { ?>
                                                <h5><?php echo $lang['enable_share_doc_feature'] ?></h5>
                                            <?php } else { ?>
                                                <h5><?php echo $lang['disable_share_doc_feature'] ?></h5>
                                            <?php } ?>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button>
                                            <?php if ($rwdocshare['docshare_enable_disable'] != '1') { ?>
                                                <button type="submit" name="sharedocenable" class="btn btn-success waves-effect waves-light"><?php echo $lang['enable']; ?></button>
                                            <?php } else { ?>
                                                <button type="submit" name="sharedocdisable" class="btn btn-danger waves-effect waves-light"><?php echo $lang['disable']; ?></button>
                                            <?php } ?>
                                        </div>
                                    </form>

                                </div><!-- /.modal-content -->
                            </div><!-- /.modal-dialog -->
                        </div><!-- /.modal -->
                        <div id="pass-policy" class="modal fade" role="dialog" aria-labelledby="custom-width-modalLabel" aria-hidden="true" style="display: none;">
                            <div class="modal-dialog">
                                <div class="panel panel-color panel-danger">
                                    <div class="panel-heading">
                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                        <h2 class="panel-title" id="custom-width-modalLabel"><?php echo $lang['ARE_YOU_SURE']; ?></h2>
                                    </div>
                                    <form method="post" enctype="multipart/form-data">
                                        <div class="modal-body">
                                            <?php if ($rwInfoPolicy['feature_enable_disable'] != '1') { ?>
                                                <h5><?php echo $lang['enable_pass_policy_feature'] ?></h5>
                                            <?php } else { ?>
                                                <h5><?php echo $lang['disable_pass_policy_feature'] ?></h5>
                                            <?php } ?>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button>
                                            <?php if ($rwInfoPolicy['feature_enable_disable'] != '1') { ?>
                                                <button type="submit" name="passpolicyenable" class="btn btn-success waves-effect waves-light"><?php echo $lang['enable']; ?></button>
                                            <?php } else { ?>
                                                <button type="submit" name="passpolicydisable" class="btn btn-danger waves-effect waves-light"><?php echo $lang['disable']; ?></button>
                                            <?php } ?>
                                        </div>
                                    </form>

                                </div><!-- /.modal-content -->
                            </div><!-- /.modal-dialog -->
                        </div><!-- /.modal -->
                        <div id="custom-width-modal" class="modal fade" role="dialog" aria-labelledby="custom-width-modalLabel" aria-hidden="true" style="display: none;">
                            <div class="modal-dialog modal-full">
                                <div class="modal-content">
                                    <form method="post" enctype="multipart/form-data">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                            <h4 class="modal-title" id="custom-width-modalLabel"><?php echo $lang['edit_label']; ?></h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <?php
                                                foreach ($lang as $key => $langua) {
                                                    echo '<div class="col-sm-4 m-t-10">';
                                                    echo '<span style="color:#193860;">' . $key . '</span>';
                                                    echo'<input type="text" id=' . $key . ' value="' . $langua . '" name="' . $key . '" class="form-control translatetext specialchaecterlocklabel" required >';
                                                    echo '</div>';
                                                }
                                                ?>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <input type="hidden" name="id" value="<?php echo $rwUser['user_id']; ?>">
                                            <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button>
                                            <button type="submit" name="changelabel" class="btn btn-primary waves-effect waves-light"><?php echo $lang['Save_changes']; ?></button>
                                        </div>
                                    </form>

                                </div><!-- /.modal-content -->
                            </div><!-- /.modal-dialog -->
                        </div><!-- /.modal -->
						
						<div id="mobileloginotpenabale" class="modal fade" role="dialog" aria-labelledby="custom-width-modalLabel" aria-hidden="true" style="display: none;">
                            <div class="modal-dialog">
                                <div class="panel panel-color panel-danger">
                                    <div class="panel-heading">
                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                        <h2 class="panel-title" id="custom-width-modalLabel"><?php echo $lang['ARE_YOU_SURE']; ?></h2>
                                    </div>
                                    <form method="post" enctype="multipart/form-data">
                                        <div class="modal-body">
                                            <?php if ($rwInfoPolicy['mobile_loginotp_enable_disable'] != '1') { ?>
                                                <h5><?php echo $lang['loginotp_disable_mobile'] ?></h5>
                                            <?php } else { ?>
                                                <h5><?php echo $lang['loginotp_enable_mobile'] ?></h5>
                                            <?php } ?>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button>
                                            <?php if ($rwInfoPolicy['mobile_loginotp_enable_disable'] != '1') { ?>
                                                <button type="submit" name="loginotpenablemobile" class="btn btn-success waves-effect waves-light"><?php echo $lang['enable']; ?></button>
                                            <?php } else { ?>
                                                <button type="submit" name="loginotpdisablemobile" class="btn btn-danger waves-effect waves-light"><?php echo $lang['disable']; ?></button>
                                            <?php } ?>
                                        </div>
                                    </form>

                                </div><!-- /.modal-content -->
                            </div><!-- /.modal-dialog -->
                        </div><!-- /.modal -->
						
						
                        <div id="default-language" class="modal fade" role="dialog" aria-labelledby="custom-width-modalLabel" aria-hidden="true" style="display: none;">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form method="post" enctype="multipart/form-data">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                            <h4 class="modal-title" id="custom-width-modalLabel"><?= $lang['Default_Language']; ?></h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-sm-12">

                                                    <div class="form-group">
                                                        <label class="text-weight"> <?= $lang['Default_Language']; ?><span class="text-alert">*</span></label>

                                                        <select name="lang"  id="lang" class="form-control select12">
                                                            <option disabled selected=""><?php echo $lang['Ch_lang']; ?></option>
                                                            <?php
                                                            while ($rwlanguage = mysqli_fetch_assoc($totalang)) {
                                                                if ($rwchklang['lang_name'] == $rwlanguage['lang_name']) {
                                                                    ?>
                                                                    <option  value="<?php echo $rwlanguage['lang_name']; ?>" selected="selected"> <?php echo $rwlanguage['lang_name']; ?> </option>
                                                                <?php } else { ?>
                                                                    <option  value="<?php echo $rwlanguage['lang_name']; ?>"> <?php echo $rwlanguage['lang_name']; ?> </option>
                                                                    <?php
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button>
                                            <button type="submit" name="changedefaultlang" class="btn btn-primary waves-effect waves-light"><?php echo $lang['Save_changes']; ?></button>
                                        </div>
                                    </form>

                                </div><!-- /.modal-content -->
                            </div><!-- /.modal-dialog -->
                        </div><!-- /.modal -->
                        <div id="expdocument" class="modal fade" role="dialog" aria-labelledby="custom-width-modalLabel" aria-hidden="true" style="display: none;">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form method="post" enctype="multipart/form-data">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                            <h4 class="modal-title" id="custom-width-modalLabel"><?php echo $lang['exp_feature_enable_disabled']; ?></h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <?php
                                                $emailnotifyusers = explode(',', $rwgetexpInfo['expdoc_mailsent_users']);
                                                $rowexpCount = mysqli_num_rows($getexpInfo);
                                                ?>
                                                <div class="form-group">
                                                    <div class="checkbox checkbox-primary">
                                                        <input id="expenabled" name="enabled" value="1" type="checkbox" data-parsley-multiple="groups" <?php
                                                        if ($rwgetexpInfo['exp_feature_enable'] == 1) {
                                                            echo "checked";
                                                        } else {
                                                            echo "";
                                                        }
                                                        ?>>
                                                        <label for="expenabled"><?php echo $lang['do_you_want_to_enabled']; ?></label>
                                                    </div>    
                                                </div>
                                                <div class="form-group">
                                                    <table class="table table-bordered">

                                                        <tr>
                                                            <td> <label><?= $lang['Notification_before'] ?><span class="astrick">*</span></label></td>
                                                            <td>
                                                                <div class="radio radio-primary">
                                                                    <input type="radio" name="radio" id="radioexp1" onclick="radiocheck()" value="Days" <?php
                                                                    if ($rwgetexpInfo['notify_type'] == 'Days') {
                                                                        echo'checked';
                                                                    }
                                                                    ?>>
                                                                    <label for="radioexp1"><?= $lang['Days']; ?></label>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="radio radio-primary">
                                                                    <input type="radio" name="radio" id="radioexp2" onclick="radiocheck()" value="Hrs" <?php
                                                                    if ($rwgetexpInfo['notify_type'] == 'Hrs') {
                                                                        echo'checked';
                                                                    }
                                                                    ?>>
                                                                    <label for="radioexp2"><?= $lang['Hrs']; ?></label>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </div>
                                                <?php
                                                $displayday = "display:none";
                                                $displaydays = "display:none";
                                                if ($rwgetexpInfo['notify_type'] == 'Days') {

                                                    $displayday = "";
                                                    $displaydays = "display:none";
                                                    $value1 = $rwgetexpInfo['email_sent_time'];
                                                } else if ($rwgetInfo['notify_type'] == 'Hrs') {

                                                    $displayday = "display:none";
                                                    $displaydays = "";
                                                    $value2 = $rwgetexpInfo['email_sent_time'];
                                                }
                                                ?>
                                                <div class="form-group">
                                                    <input type="text" class="form-control alphalock" name="days" value="<?php echo $value1; ?>" onkeyup="radiocheckinput(this.value);" id="days" style="<?php echo $displayday; ?>" placeholder="Days"/>
                                                    <input type="text" class="form-control alphalock" name="hrs" value="<?php echo $value2; ?>" onkeyup="radiocheckinput(this.value);" id="hrs" style="<?php echo $displaydays; ?>" placeholder="Hrs"/>
                                                </div>
                                                <div class="form-group">
                                                    <label><?php echo $lang['notification_users_retention']; ?><span class="astrick">*</span></label>
                                                    <select class="select12 select2-multiple" multiple data-placeholder="<?php echo $lang['Slt_Usrs']; ?>" name="emailusers[]" required="required">
                                                        <?php
                                                        $sameGroupuserIDs = array();
                                                        $group = mysqli_query($db_con, "select * from tbl_bridge_grp_to_um where find_in_set('$_SESSION[cdes_user_id]',user_ids)") or die('Error' . mysqli_error($db_con));
                                                        while ($rwGroup = mysqli_fetch_assoc($group)) {
                                                            $sameGroupuserIDs[] = $rwGroup['user_ids'];
                                                        }
                                                        $sameGroupuserIDs = array_unique($sameGroupuserIDs);
                                                        sort($sameGroupuserIDs);
                                                        $sameGroupuserIDs = implode(',', $sameGroupuserIDs);

                                                        $user = mysqli_query($db_con, "select * from tbl_user_master where user_id in($sameGroupuserIDs) and user_id !='" . $_SESSION['cdes_user_id'] . "' and user_id !='1' order by first_name,last_name asc");
                                                        while ($rwUser = mysqli_fetch_assoc($user)) {
                                                            if (in_array($rwUser['user_id'], $emailnotifyusers)) {
                                                                echo '<option value="' . $rwUser['user_id'] . '" selected>' . $rwUser['first_name'] . ' ' . $rwUser['last_name'] . '</option>';
                                                            } else {
                                                                echo '<option value="' . $rwUser['user_id'] . '">' . $rwUser['first_name'] . ' ' . $rwUser['last_name'] . '</option>';
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button>
                                            <?php if ($rowexpCount == 0) { ?>
                                                <button type="submit" name="expsetting" class="btn btn-primary waves-effect waves-light"><?php echo $lang['Submit']; ?></button>
                                            <?php } else { ?>
                                                <button type="submit" name="editexpsetting" class="btn btn-primary waves-effect waves-light"><?php echo $lang['Save_changes']; ?></button>
                                            <?php } ?>
                                        </div>
                                    </form>
                                </div><!-- /.modal-content -->
                            </div><!-- /.modal-dialog -->
                        </div><!-- /.modal -->
                        <div id="retention-file" class="modal fade" role="dialog" aria-labelledby="custom-width-modalLabel" aria-hidden="true" style="display: none;">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form method="post" enctype="multipart/form-data">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                            <h4 class="modal-title" id="custom-width-modalLabel"><?php echo $lang['Retention_feature_enable_disabled']; ?></h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <?php
                                                $emailusers = explode(',', $rwgetInfo['retentiondoc_mailsent_users']);
                                                $rowCount = mysqli_num_rows($getInfo);
                                                ?>
                                                <div class="form-group">
                                                    <div class="checkbox checkbox-primary">
                                                        <input id="enabled" name="enabled" value="1" type="checkbox" data-parsley-multiple="groups" <?php
                                                        if ($rwgetInfo['retention_feature_enable'] == 1) {
                                                            echo "checked";
                                                        } else {
                                                            echo "";
                                                        }
                                                        ?>>
                                                        <label for="enabled"><?php echo $lang['do_you_want_to_enabled']; ?></label>
                                                    </div>    
                                                </div>
                                                <div class="form-group">
                                                    <table class="table table-bordered">

                                                        <tr>
                                                            <td> <label><?= $lang['Notification_before'] ?><span class="astrick">*</span></label></td>
                                                            <td>
                                                                <div class="radio radio-primary">
                                                                    <input type="radio" name="retentionradio" id="radio1" onclick="radiocheck()" value="Days" <?php
                                                                    if ($rwgetInfo['notify_type'] == 'Days') {
                                                                        echo'checked';
                                                                    }
                                                                    ?>>
                                                                    <label for="radio1"><?= $lang['Days']; ?></label>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="radio radio-primary">
                                                                    <input type="radio" name="retentionradio" id="radio2" onclick="radiocheck()" value="Hrs" <?php
                                                                    if ($rwgetInfo['notify_type'] == 'Hrs') {
                                                                        echo'checked';
                                                                    }
                                                                    ?>>
                                                                    <label for="radio2"><?= $lang['Hrs']; ?></label>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </div>
                                                <?php
                                                $displayreday = "display:none";
                                                $displayreday2 = "display:none";
                                                if ($rwgetInfo['notify_type'] == 'Days') {

                                                    $displayreday = "";
                                                    $displayreday2 = "display:none";
                                                    $valemailday = $rwgetInfo['email_time'];
                                                } else if ($rwgetInfo['notify_type'] == 'Hrs') {

                                                    $displayreday = "display:none";
                                                    $displayreday2 = "";
                                                    $valemailtime = $rwgetInfo['email_time'];
                                                }
                                                ?>
                                                <div class="form-group">
                                                    <input type="text" class="form-control alphalock" name="days" value="<?php echo $valemailday; ?>" onkeyup="radiocheckinput(this.value);" id="days1" style="<?php echo $displayreday; ?>" placeholder="Days"/>
                                                    <input type="text" class="form-control alphalock" name="hrs" value="<?php echo $valemailtime; ?>" onkeyup="radiocheckinput(this.value);" id="hrs1" style="<?php echo $displayreday2; ?>" placeholder="Hrs"/>
                                                </div>
                                                <div class="form-group">
                                                    <label><?php echo $lang['notification_users_retention']; ?><span class="astrick">*</span></label>
                                                    <select class="select12 select2-multiple" multiple data-placeholder="<?php echo $lang['Slt_Usrs']; ?>" name="emailusers[]" required="required">
                                                        <?php
                                                        $sameGroupuserIDs = array();
                                                        $group = mysqli_query($db_con, "select * from tbl_bridge_grp_to_um where find_in_set('$_SESSION[cdes_user_id]',user_ids)") or die('Error' . mysqli_error($db_con));
                                                        while ($rwGroup = mysqli_fetch_assoc($group)) {
                                                            $sameGroupuserIDs[] = $rwGroup['user_ids'];
                                                        }
                                                        $sameGroupuserIDs = array_unique($sameGroupuserIDs);
                                                        sort($sameGroupuserIDs);
                                                        $sameGroupuserIDs = implode(',', $sameGroupuserIDs);

                                                        $user = mysqli_query($db_con, "select * from tbl_user_master where user_id in($sameGroupuserIDs) and user_id !='" . $_SESSION['cdes_user_id'] . "' and user_id !='1' order by first_name,last_name asc");
                                                        while ($rwUser = mysqli_fetch_assoc($user)) {
                                                            if (in_array($rwUser['user_id'], $emailusers)) {
                                                                echo '<option value="' . $rwUser['user_id'] . '" selected>' . $rwUser['first_name'] . ' ' . $rwUser['last_name'] . '</option>';
                                                            } else {
                                                                echo '<option value="' . $rwUser['user_id'] . '">' . $rwUser['first_name'] . ' ' . $rwUser['last_name'] . '</option>';
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button>
                                            <?php if ($rowCount == 0) { ?>
                                                <button type="submit" name="retentionsetting" class="btn btn-primary waves-effect waves-light"><?php echo $lang['Submit']; ?></button>
                                            <?php } else { ?>
                                                <button type="submit" name="editretentionsetting" class="btn btn-primary waves-effect waves-light"><?php echo $lang['Save_changes']; ?></button>
                                            <?php } ?>
                                        </div>
                                    </form>
                                </div><!-- /.modal-content -->
                            </div><!-- /.modal-dialog -->
                        </div><!-- /.modal -->
						
						
						<div id="watermarkenable" class="modal fade" role="dialog" aria-labelledby="custom-width-modalLabel" aria-hidden="true" style="display: none;">
                            <div class="modal-dialog">
                                <div class="panel panel-color panel-danger">
                                    <div class="panel-heading">
                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                        <h2 class="panel-title" id="custom-width-modalLabel"><?php echo $lang['ARE_YOU_SURE']; ?></h2>
                                    </div>
                                    <form method="post" enctype="multipart/form-data">
                                        <div class="modal-body">
                                            <?php if ($isWatermarkEnabled['is_enable'] != '1') { ?>
                                                <h5><?php echo $lang['watermark_disable'] ?></h5>
                                            <?php } else { ?>
                                                <h5><?php echo $lang['watermark_enable'] ?></h5>
                                            <?php } ?>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button>
                                            <?php if ($isWatermarkEnabled['is_enable'] != '1') { ?>
                                                <button type="submit" name="watermarkenable" class="btn btn-success waves-effect waves-light"><?php echo $lang['enable']; ?></button>
                                            <?php } else { ?>
                                                <button type="submit" name="watermarkdisable" class="btn btn-danger waves-effect waves-light"><?php echo $lang['disable']; ?></button>
                                            <?php } ?>
                                        </div>
                                    </form>

                                </div><!-- /.modal-content -->
                            </div><!-- /.modal-dialog -->
                        </div><!-- /.modal -->

                        <div id="setwatermark" class="modal fade" role="dialog" aria-labelledby="custom-width-modalLabel" aria-hidden="true" style="display: none;" >

                            <div class="modal-dialog">
                                <div class="panel panel-color panel-danger">
                                    <div class="panel-heading">
                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                        <h2 class="panel-title" id="custom-width-modalLabel"><?php echo $lang['ARE_YOU_SURE']; ?></h2>
                                    </div>
                                    <form method="post" enctype="multipart/form-data">
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label>Watermark Type<span class="text-danger">*</span></label>
                                                <select class="form-control" name="wtype" onchange="updateDiv(this.value)" required>
                                                    <option value="0" <?php if ($wsetting['is_customized'] == '0') { ?> selected <?php } ?>>Default</option>
                                                    <option value="1" <?php if ($wsetting['is_customized'] == '1') { ?> selected <?php } ?>>Customized</option>
                                                </select>
                                            </div>
                                            <div id="wmform" <?php if ($wsetting['is_customized'] == '0') { ?> style="display:none;" <?php } ?> >
                                                <div class="form-group">
                                                    <label>Enter Customized Watermark<span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" value="<?php echo $query_row['watermark']; ?>" placeholder="Enter Customized Watermark" name="watermark" id="watermark"/>
                                                </div>
                                                <div class="form-group">
                                                    <label>Enter Font Size in px<span class="text-danger">*</span></label>
                                                    <input type="number" class="form-control" value="<?php echo $query_row['size']; ?>" placeholder="Enter Font Size in px" name="fsize" id="fsize" min="1" max="150"/>
                                                </div>
                                                <div class="form-group">
                                                    <label>Font Color (Default : 66, 66, 66)<span class="text-danger">*</span></label>
                                                    <input type="color" rgba class="form-control" value="<?php echo $query_row['fcolor']; ?>" name="fclr" id="fclr"/>
                                                </div>
                                                <div class="form-group">
                                                    <label>Watermark Position<span class="text-danger">*</span></label>
                                                    <select class="form-control" name="wposition" required>
                                                        <option value="0" <?php if ($query_row['w_position'] == '0') { ?> selected <?php } ?>>Top-Left</option>
                                                        <option value="1" <?php if ($query_row['w_position'] == '1') { ?> selected <?php } ?>>Top-Center</option>
                                                        <option value="2" <?php if ($query_row['w_position'] == '2') { ?> selected <?php } ?>>Top-Right</option>
                                                        <option value="3" <?php if ($query_row['w_position'] == '3') { ?> selected <?php } ?>>Middle-Left</option>
                                                        <option value="4" <?php if ($query_row['w_position'] == '4') { ?> selected <?php } ?>>Middle-Center</option>
                                                        <option value="5" <?php if ($query_row['w_position'] == '5') { ?> selected <?php } ?>>Middle-Right</option>
                                                        <option value="6" <?php if ($query_row['w_position'] == '6') { ?> selected <?php } ?>>Bottom-Left</option>
                                                        <option value="7" <?php if ($query_row['w_position'] == '7') { ?> selected <?php } ?>>Bottom-Center</option>
                                                        <option value="8" <?php if ($query_row['w_position'] == '8') { ?> selected <?php } ?>>Bottom-Right</option>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label>Watermark Rotation<span class="text-danger">*</span></label>
                                                    <select class="form-control" name="wrotation" required>
                                                        <option value="0" <?php if ($query_row['w_rotation'] == '0') { ?> selected <?php } ?>>Disabled</option>
                                                        <option value="1" <?php if ($query_row['w_rotation'] == '1') { ?> selected <?php } ?>>Enabled</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button>
                                            <button type="submit" name="updateWatermark" class="btn btn-success waves-effect waves-light"><?php echo $lang['Update']; ?></button>
                                        </div>
                                    </form>

                                </div><!-- /.modal-content -->
                            </div><!-- /.modal-dialog -->
                        </div><!-- /.modal -->
                    </div>
                </div> <!-- container -->

            </div> <!-- content -->
        </div>
        <?php require_once './application/pages/footer.php'; ?>
        <!-- Right Sidebar -->
        <?php //require_once './application/pages/rightSidebar.php';               ?>
        <!-- /Right-bar -->
    </div>
    <!-- END wrapper -->
    <?php require_once './application/pages/footerForjs.php'; ?>
    <script src="assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
    <script src="assets/plugins/select2/js/select2.min.js" type="text/javascript"></script>

    <script>
                                                        $(".select12").select2();
                                                        $(document).ready(function () {
                                                            var d1 = new Date();
                                                            d1 = d1.setDate(d1.getDate() - 30);
                                                            var d = new Date(d1);
                                                            var month = d.getMonth() + 1;
                                                            var day = d.getDate();
                                                            var output = d.getFullYear() + '-' +
                                                                    (('' + month).length < 2 ? '0' : '') + month + '-' +
                                                                    (('' + day).length < 2 ? '0' : '') + day;
                                                            //alert(output);
                                                            $('.datepicker').datepicker({
                                                                format: "dd-mm-yyyy",
                                                                startDate: output
                                                            });
                                                            radiocheck();



                                                            $('.specialchaecterlocklabel').keyup(function ()
                                                            {
                                                                var groupName = $(this).val();
                                                                re = /[`1234567890~!@#$%^*()|+\=?;:'".<>\{\}\[\]\\\/]/gi;
                                                                var isSplChar = re.test(groupName);
                                                                if (isSplChar)
                                                                {
                                                                    var no_spl_char = groupName.replace(/[`~!@#$%^*()|+\=?;:'".<>\{\}\[\]\\\/]/gi, '');
                                                                    $(this).val(no_spl_char);
                                                                }
                                                            });
                                                        });


    </script>
    <script>
        function radiocheck()
        {
            var val = $("input[name='radio']:checked").val();
            var value = $("input[name='retentionradio']:checked").val();
            if (val == 'Days') {
                //$("#dateRange").css("display", "none");
                $("#hrs").removeAttr("required", true);
                $("#days").prop("required", true);
                $("#days").css("display", "block");
                $("#hrs").css("display", "none");
                $("#hrs").val("");
            }
            if (val == 'Hrs') {
                //$("#dateRange").css("display", "none");
                $("#hrs").prop("required", true);
                $("#days").removeAttr("required", true);
                $("#days").css("display", "none");
                $("#hrs").css("display", "block");
                $("#days").val("");
            }
            if (value == 'Days') {
                //$("#dateRange").css("display", "none");
                $("#hrs1").removeAttr("required", true);
                $("#days1").prop("required", true);
                $("#days1").css("display", "block");
                $("#hrs1").css("display", "none");
                $("#hrs1").val("");
            }
            if (value == 'Hrs') {
                //$("#dateRange").css("display", "none");
                $("#hrs1").prop("required", true);
                $("#days1").removeAttr("required", true);
                $("#days1").css("display", "none");
                $("#hrs1").css("display", "block");
                $("#days1").val("");
            }
        }
        function radiocheckinput(id) {
            if (id == '') {
                radiocheck();
            }
        }
		
		//ab@190621 
        function updateDiv(wtype) {
            if (wtype == 0) {
                $("#wmform").hide();
                $("#watermark").attr("required", "false")
            } else {
                $("#wmform").show();
                $("#watermark").attr("required", "true")
            }
        }
    </script>
     <?php
    if (isset($_POST['loginotpenable'], $_POST['token'])) {
        $enable = mysqli_query($db_con, "UPDATE `tbl_pass_policy` SET `loginotp_enable_disable`='1'");
        if ($enable) {
            $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`,`action_name`, `start_date`,`system_ip`, `remarks`) values ('$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]','Login with OTP enabled','$date','$host', 'Login with email OTP feature enabled.')") or die('error : ' . mysqli_error($db_con));
            echo'<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['otplogin_setting_enable_successful'] . '");</script>';
        } else {
            echo'<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['Retention_setting_failed'] . '");</script>';
        }
    }
  
    if (isset($_POST['loginotpdisable'], $_POST['token'])) {

        $disable = mysqli_query($db_con, "UPDATE `tbl_pass_policy` SET `loginotp_enable_disable`='0'") or die('error : disabled password ' . mysqli_error($db_con));
        if ($disable) {
            $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`,`action_name`, `start_date`,`system_ip`, `remarks`) values ('$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]','Login with OTP disabled','$date','$host', 'Login with email OTP feature disabled.')") or die('error : ' . mysqli_error($db_con));
            echo'<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['otplogin_setting_disable_successful'] . '");</script>';
        } else {
            echo'<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['Retention_setting_failed'] . '");</script>';
        }
    }

    if (isset($_POST['passpolicyenable'], $_POST['token'])) {
        $enable = mysqli_query($db_con, "UPDATE `tbl_pass_policy` SET `feature_enable_disable`='1'");
        if ($enable) {
            $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`,`action_name`, `start_date`,`system_ip`) values ('$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]','Password policy feature enable in $projectName','$date','$host')") or die('error : ' . mysqli_error($db_con));
            echo'<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['enabled_setting_successful'] . '");</script>';
        } else {
            echo'<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['Retention_setting_failed'] . '");</script>';
        }
    }

    if (isset($_POST['passpolicydisable'], $_POST['token'])) {

        $disable = mysqli_query($db_con, "UPDATE `tbl_pass_policy` SET `minlen`='8',`maxlen`='8',`lowercase`='1',`uppercase`='1',`numbers`='1',`s_char`='1',`edate`='0',`feature_enable_disable`='0'") or die('error : disabled password ' . mysqli_error($db_con));
        if ($disable) {
            $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`,`action_name`, `start_date`,`system_ip`) values ('$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]','Password policy feature disabled and default password policy applied in $projectName','$date','$host')") or die('error : ' . mysqli_error($db_con));
            echo'<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['disabled_setting_successful'] . '");</script>';
        } else {
            echo'<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['Retention_setting_failed'] . '");</script>';
        }
    }

    if (isset($_POST['sharedocenable'], $_POST['token'])) {
        $checkrow = mysqli_query($db_con, "SELECT * FROM `tbl_default_docshare_setting`");
        if ($rwcheckrow = mysqli_num_rows($checkrow) < 1) {
            $insert = mysqli_query($db_con, "INSERT INTO `tbl_default_docshare_setting`(`docshare_enable_disable`, `action_time`) VALUES ('1',NOW())");
            if ($insert) {
                $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`,`action_name`, `start_date`,`system_ip`) values ('$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]','Document share with time feature enabled in $projectName','$date','$host')") or die('error : ' . mysqli_error($db_con));
                echo'<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['enabled_setting_successful'] . '");</script>';
            } else {
                echo'<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['Retention_setting_failed'] . '");</script>';
            }
        } else {
            $update = mysqli_query($db_con, "UPDATE `tbl_default_docshare_setting` set `docshare_enable_disable`='1', `action_time`=NOW()");
            if ($update) {
                $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`,`action_name`, `start_date`,`system_ip`) values ('$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]','Document share with time feature enabled in $projectName','$date','$host')") or die('error : ' . mysqli_error($db_con));
                echo'<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['enabled_setting_successful'] . '");</script>';
            } else {
                echo'<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['Retention_setting_failed'] . '");</script>';
            }
        }
    }

    if (isset($_POST['sharedocdisable'], $_POST['token'])) {

        $disable = mysqli_query($db_con, "UPDATE `tbl_default_docshare_setting` set `docshare_enable_disable`='0', `action_time`=NOW()") or die('error : dd ' . mysqli_error($db_con));
        if ($disable) {
            $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`,`action_name`, `start_date`,`system_ip`) values ('$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]','Document share with time feature disabled in $projectName','$date','$host')") or die('error : ' . mysqli_error($db_con));
            echo'<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['disabled_setting_successful'] . '");</script>';
        } else {
            echo'<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['Retention_setting_failed'] . '");</script>';
        }
    }

    if (isset($_POST['changedefaultlang'], $_POST['token'])) {
        $langName = $_POST['lang'];
        $chkdefaultLang = mysqli_query($db_con, "SELECT lang_name FROM tbl_language WHERE default_language='1'");
        $rwchkdefault = mysqli_fetch_assoc($chkdefaultLang);
        $removebeforelang = mysqli_query($db_con, "UPDATE tbl_language set default_language='0' where default_language='1'");
        if ($removebeforelang) {
            $setDefaultLang = mysqli_query($db_con, "UPDATE tbl_language set default_language='1' where lang_name='$langName'");
            if ($setDefaultLang) {
                $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`,`action_name`, `start_date`,`system_ip`) values ('$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]','Default language $rwchkdefault[lang_name] to $langName changed in $projectName','$date','$host')") or die('error : ' . mysqli_error($db_con));
                echo'<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['enabled_setting_successful'] . '");</script>';
            } else {
                echo'<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['Retention_setting_failed'] . '");</script>';
            }
        } else {
            echo'<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['unable_to_change_default_lang'] . '");</script>';
        }
    }
   
    if (isset($_POST['retentionsetting'], $_POST['token'])) {
        $enable = ((!empty($_POST['enabled'])) ? 1 : 0);
        $notifyType = $_POST['retentionradio'];
        if ($notifyType == 'Days') {
            $notifytime = $_POST['days'];
        } else if ($notifyType == 'Hrs') {
            $notifytime = $_POST['hrs'];
        }
        $emailusers = $_POST['emailusers'];
        $emailusers = implode(',', $emailusers);
        $retention = mysqli_query($db_con, "INSERT INTO `tbl_retention_default_setting`(`email_time`, `notify_type`, `retention_feature_enable`, `retentiondoc_mailsent_users`,`action_time`) VALUES ('$notifytime','$notifyType','$enable','$emailusers',NOW())") or die("Error : chage seeting retention" . mysqli_error($db_con));
        if ($retention) {
            $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`,`action_name`, `start_date`,`system_ip`) values ('$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]','Retention feature enabled in $projectName','$date','$host')") or die('error : ' . mysqli_error($db_con));
            echo'<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['enabled_setting_successful'] . '");</script>';
        } else {
            echo'<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['Retention_setting_failed'] . '");</script>';
        }
    }
    
    if (isset($_POST['expsetting'], $_POST['token'])) {
        $enable = ((!empty($_POST['enabled'])) ? 1 : 0);
        $notifyType = $_POST['radio'];
        if ($notifyType == 'Days') {
            $notifytime = $_POST['days'];
        } else if ($notifyType == 'Hrs') {
            $notifytime = $_POST['hrs'];
        }
        $emainotilusers = $_POST['emailusers'];
        $emainotilusers = implode(',', $emainotilusers);

        $exprysetting = mysqli_query($db_con, "INSERT INTO `tbl_expiry_default_setting` (`email_sent_time`, `notify_type`, `exp_feature_enable`, `expdoc_mailsent_users`,`action_time`) VALUES ('$notifytime','$notifyType','$enable','$emainotilusers',NOW())") or die("Error : chage seeting retention" . mysqli_error($db_con));
        if ($exprysetting) {
            $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`,`action_name`, `start_date`,`system_ip`) values ('$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]','Document Expiry feature enabled in $projectName','$date','$host')") or die('error : ' . mysqli_error($db_con));
            echo'<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['enabled_setting_successful'] . '");</script>';
        } else {
            echo'<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['Retention_setting_failed'] . '");</script>';
        }
    }
    
    if (isset($_POST['editexpsetting'], $_POST['token'])) {
        $enable = ((!empty($_POST['enabled'])) ? 1 : 0);
        $notifyType = $_POST['radio'];
        if ($notifyType == 'Days') {
            $notifytime = $_POST['days'];
        } else if ($notifyType == 'Hrs') {
            $notifytime = $_POST['hrs'];
        }
        $emailnotifyusers = $_POST['emailusers'];
        $emailnotifyusers = implode(',', $emailnotifyusers);

        $editExpsetting = mysqli_query($db_con, "UPDATE `tbl_expiry_default_setting` SET `email_sent_time`='$notifytime',`notify_type`='$notifyType',`exp_feature_enable`='$enable',`expdoc_mailsent_users`='$emailnotifyusers',`action_time`=NOW()") or die("Error : chage seeting retention" . mysqli_error($db_con));
        if ($editExpsetting) {
            $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`,`action_name`, `start_date`,`system_ip`) values ('$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]','Document expiry feature modify in $projectName','$date','$host')") or die('error : ' . mysqli_error($db_con));
            if ($enable == 1) {
                echo'<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['enabled_setting_successful'] . '");</script>';
            } else {
                echo'<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['disabled_setting_successful'] . '");</script>';
            }
        } else {
            echo'<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['Retention_setting_failed'] . '");</script>';
        }
    }
    
    if (isset($_POST['editretentionsetting'], $_POST['token'])) {
        $enable = ((!empty($_POST['enabled'])) ? 1 : 0);
        $notifyType = $_POST['retentionradio'];
        if ($notifyType == 'Days') {
            $notifytime = $_POST['days'];
        } else if ($notifyType == 'Hrs') {
            $notifytime = $_POST['hrs'];
        }
        $emailusers = $_POST['emailusers'];
        $emailusers = implode(',', $emailusers);

        $retention = mysqli_query($db_con, "UPDATE `tbl_retention_default_setting` SET `email_time`='$notifytime',`notify_type`='$notifyType',`retention_feature_enable`='$enable',`retentiondoc_mailsent_users`='$emailusers',`action_time`=NOW()") or die("Error : change setting retention" . mysqli_error($db_con));
        if ($retention) {
            $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`,`action_name`, `start_date`,`system_ip`) values ('$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]','Retention feature modify in $projectName','$date','$host')") or die('error : ' . mysqli_error($db_con));
            echo'<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['Retention_setting_Usuccessful'] . '");</script>';
        } else {
            echo'<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['Retention_setting_Ufailed'] . '");</script>';
        }
    }
    
    if (isset($_POST['changelabel'], $_POST['token'])) {
        //include('sessionstart.php');
        //echo "-------------------------------------------------------";print_r($_SESSION['lang']);echo "-------------------";die;
        foreach ($lang as $key => $langu) {
            $language = xss_clean($_POST[$key]);
            //$language = preg_replace('/[^\w$\x{0080}-\x{FFFF}@#$&!%()_ <>]+/u', "", $language);
            $lang[$key] = $language;
        }
        $file = $_SESSION['lang'] . ".json";

        $newJsonString = json_encode($lang);
        $fileedit = fopen("$file", "w+");

        if (!empty($fileedit)) {
            if (fwrite($fileedit, $newJsonString)) {
                fclose($fileedit);
                echo'<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['label_updated_successfully'] . '");</script>';
            } else {
                echo'<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['label_updated_failed'] . '");</script>';
            }
        } else {
            echo'<script>taskfailed("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['label_updated_failed'] . '");</script>';
        }
    }

    if (isset($_POST['captchaenable'], $_POST['token'])) {
        $enable = mysqli_query($db_con, "UPDATE `tbl_pass_policy` SET `captcha_enabled`='1'");
        if ($enable) {
            $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`,`action_name`, `start_date`,`system_ip`, `remarks`) values ('$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]','Login with OTP enabled','$date','$host', 'Login with captcha feature enabled.')") or die('error : ' . mysqli_error($db_con));
            echo'<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['captcha_setting_enable_successful'] . '");</script>';
        } else {
            echo'<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['Retention_setting_failed'] . '");</script>';
        }
    }

    if (isset($_POST['captchadisable'], $_POST['token'])) {

        $disable = mysqli_query($db_con, "UPDATE `tbl_pass_policy` SET `captcha_enabled`='0'") or die('error : disabled captcha ' . mysqli_error($db_con));
        if ($disable) {
            $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`,`action_name`, `start_date`,`system_ip`, `remarks`) values ('$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]','Login with OTP disabled','$date','$host', 'Login with captcha feature disabled.')") or die('error : ' . mysqli_error($db_con));
            echo'<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['captcha_setting_disable_successful'] . '");</script>';
        } else {
            echo'<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['Retention_setting_failed'] . '");</script>';
        }
    }
	
	
	if (isset($_POST['loginotpenablemobile'], $_POST['token'])) {
        $enable = mysqli_query($db_con, "UPDATE `tbl_pass_policy` SET `mobile_loginotp_enable_disable`='1'");
        if ($enable) {
            $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`,`action_name`, `start_date`,`system_ip`, `remarks`) values ('$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]','Login with OTP enabled','$date','$host', 'Login with email OTP feature enabled.')") or die('error : ' . mysqli_error($db_con));
            echo'<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['otplogin_setting_enable_successful_mobile'] . '");</script>';
        } else {
            echo'<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['Retention_setting_failed'] . '");</script>';
        }
    }
	
    if (isset($_POST['loginotpdisablemobile'], $_POST['token'])) {

        $disable = mysqli_query($db_con, "UPDATE `tbl_pass_policy` SET `mobile_loginotp_enable_disable`='0'") or die('error : disabled password ' . mysqli_error($db_con));
        if ($disable) {
            $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`,`action_name`, `start_date`,`system_ip`, `remarks`) values ('$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]','Login with OTP disabled','$date','$host', 'Login with email OTP feature disabled.')") or die('error : ' . mysqli_error($db_con));
            echo'<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['otplogin_setting_disable_successful_mobile'] . '");</script>';
        } else {
            echo'<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['Retention_setting_failed'] . '");</script>';
        }
    }
	
	
	 //watermark ab@190621
    if (isset($_POST['watermarkenable'], $_POST['token'])) {
        $enable = mysqli_query($db_con, "UPDATE `tbl_watermark_setting` SET `is_enable`='1'");
        if ($enable) {
            $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`,`action_name`, `start_date`,`system_ip`, `remarks`) values ('$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]','Watermark enabled','$date','$host', 'Watermark feature enabled.')") or die('error : ' . mysqli_error($db_con));
            echo'<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['watermark_setting_enable_successful'] . '");</script>';
        } else {
            echo'<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['Retention_setting_failed'] . '");</script>';
        }
    }

    if (isset($_POST['watermarkdisable'], $_POST['token'])) {

        $disable = mysqli_query($db_con, "UPDATE `tbl_watermark_setting` SET `is_enable`='0'") or die('error : disabled watermark ' . mysqli_error($db_con));
        if ($disable) {
            $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`,`action_name`, `start_date`,`system_ip`, `remarks`) values ('$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]','Watermark disabled','$date','$host', 'Watermark feature disabled.')") or die('error : ' . mysqli_error($db_con));
            echo'<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['watermark_setting_disable_successful'] . '");</script>';
        } else {
            echo'<script>taskFailed("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['Retention_setting_failed'] . '");</script>';
        }
    }

    if (isset($_POST['updateWatermark'])) {
        $wtype = $_POST['wtype'];
        if (isset($wtype) and $wtype == '0') {
            //disable customized 
            $updateDefaultWatermark = mysqli_query($db_con, "UPDATE tbl_watermark_setting SET is_customized='0'");
            $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`,`action_name`, `start_date`,`system_ip`, `remarks`) values ('$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]','Set Default Watermark','$date','$host', 'Set Default watermark by $_SESSION[cdes_user_id]')") or die('error : ' . mysqli_error($db_con));
            echo'<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['default_watermark'] . '");</script>';
        } else if (isset($wtype) and $wtype == '1') {
            $watermark = $_POST['watermark'];
            $fsize = $_POST['fsize'];
            $fclr = $_POST['fclr'];
            $wposition = $_POST['wposition'];
            $wrotation = $_POST['wrotation'];

            $updateCustomizedWatermark = mysqli_query($db_con, "UPDATE tbl_watermark_setting SET is_customized='1' where id='1'");
            $updatedWatermarkconf = mysqli_query($db_con, "UPDATE tbl_watermark_master SET watermark='$watermark', size='$fsize', fcolor='$fclr', w_position='$wposition', w_rotation='$wrotation', createdby='$_SESSION[cdes_user_id]' ");
            $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`,`action_name`, `start_date`,`system_ip`, `remarks`) values ('$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]','Set Customized Watermark','$date','$host', 'Set Customized watermark $watermark')") or die('error : ' . mysqli_error($db_con));
            echo'<script>taskSuccess("' . basename($_SERVER['REQUEST_URI']) . '","' . $lang['customized_watermark'] . '");</script>';
        }
    }
	
    ?>
</body>
</html>