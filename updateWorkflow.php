<!DOCTYPE html>
<html>
<?php
require_once './loginvalidate.php';
require_once './application/config/database.php';
require_once './application/pages/head.php';
?>
<link href="assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
<?php

if ($rwgetRole['edit_workflow'] != '1') {
    header('Location: index');
}
$id = base64_decode(urldecode($_GET['id']));
$id =  preg_replace("/[^0-9 ]/", "", $id);
$group = mysqli_query($db_con, "select * from tbl_workflow_master where workflow_id='$id'");
$rwGroup = mysqli_fetch_assoc($group);
$workflow_name = $rwGroup['workflow_name'];


?>
<style>
    .form-wrap.form-builder .frmb {
        list-style-type: none;
        min-height: 500px !important;

        margin: 0 6px 0 0;
        padding: 0;
        transition: background-color .5s ease-in-out;
    }

    .form-wrap.form-builder .frmb li {
        position: relative;
        padding: 6px;
        clear: both;
        margin-left: 0;
        margin-bottom: 3px;
        background-color: #d6cece !important;
        transition: background-color .25s ease-in-out, margin-top .4s;
    }

    .input-control-10 {
        display: none !important;
    }

    .input-control-15 {
        display: none !important;
    }

    .input-control-1 {
        display: none !important;
    }

    .input-control-2 {
        display: none !important;
    }

    .input-control-14 {
        display: none !important;
    }
</style>
<?php
if (isset($_REQUEST['id']) && !empty($_REQUEST['id'])) {
    $wid = base64_decode(urldecode($_GET['id']));
    $wid = preg_replace("/[^0-9 ]/", "", $wid);
    $qry = mysqli_query($db_con, "select * from  tbl_bridge_workflow_to_form where workflow_id='$wid'");
    $res = mysqli_fetch_assoc($qry);
    $formid = $res['form_id'];

    function fetchForm($db_con, $formid)
    {
        $j = 0;
        $k = 0;
        $result = array();
        $json = array();
        $sql = mysqli_query($db_con, "select * from tbl_form_attribute where fid='$formid' and dependency_id IS  NULL  order by aid asc");
        while ($row = mysqli_fetch_assoc($sql)) {
            $json['aid'] = $row['aid'];
            $json['type'] = $row['type'];
            $json['label'] = $row['label'];
            $res = explode("_", $row['name']);
            $json['name'] = $res[1];
            $json['placeholder'] = $row['placeholder'];
            $json['subtype'] = $row['subtype'];
            $json['className'] = $row['class'];
            $json['required'] = $row['required'];
            $json['placeholder'] = $row['placeholder'];
            $json['maxlength'] = $row['maxlength'];
            $attrid = $row['aid'];
            //echo $attrid;
            $childattr = mysqli_query($db_con, "select * from tbl_form_attribute where dependency_id='$attrid'");
            $content = array();
            //            if (mysqli_num_rows($childattr) > 0) {
            ////        $k=0;
            ////        $attrrow= mysqli_fetch_assoc($childattr);
            ////        if(!empty($attrrow['dependency_id']))
            ////        {
            //
            //
            //                $json['values'] = array();
            ////        while($attrrow= mysqli_fetch_assoc($childattr))
            ////        {
            //                $json['values'] = mysqli_fetch_all($childattr, MYSQLI_ASSOC);
            //                ;
            ////          $k++;
            //////        }
            ////        }
            //            }

            if (mysqli_num_rows($childattr) > 0) {
                $json['values'] = array();
                $valueData = array();
                //        $attrrow= mysqli_fetch_assoc($childattr);
                //        if(!empty($attrrow['dependency_id']))
                //        {


                while ($attrrow = mysqli_fetch_assoc($childattr)) {

                    $valueData['label'] = $attrrow['label'];
                    $valueData['value'] = $attrrow['value'];
                    $json['values'][$k] = $valueData;
                    $k++;
                }


                //        }
            }

            $result[$j] = $json;
            $j++;
        }

        return $result;
    }
}
?>
<?php if (isset($_REQUEST['id'])) { ?>
    <textarea id="formjsondata" style="display: none"><?= json_encode(fetchForm($db_con, $formid)); ?></textarea>
<?php } ?>

<body class="fixed-left">
    <p id="data"></p>
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
                            <li><a href="updateWorkflow"><?php echo $lang['dWORKFLOW_MANAGEMENT']; ?></a></li>
                            <li class="active"><?php echo $lang['Updt_Wrkflw']; ?></li>
                            <li class="active"><?php echo $workflow_name; ?></li>
                            <a href="javascript:void(0)" class="btn btn-primary waves-effect waves-light pull-right btn-sm margin-t-9" onclick="goPrevious();" title="<?php echo $lang['Go_to_previous_page']; ?>"><i class="fa fa-arrow-circle-left"></i></a>
                        </ol>
                    </div>
                    <div class="row">
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h4 class="header-title"><?php echo $lang['Updt_Wrkflw']; ?> </h4>
                            </div>
                            <div class="box-body">
                                <div class="col-lg-12">
                                    <div class="row">
                                        <div class="col-md-12">

                                            <div class="form-group">
                                                <label><?php echo $lang['Workflow_Name']; ?><span style="color: red;">*</span></label>
                                                <input type="text" name="workflowName" autocomplete="off" required class="form-control" id="workflow_name" value="<?php echo $_REQUEST['wfname'] ? $_REQUEST['wfname'] : $rwGroup['workflow_name']; ?>" placeholder="<?= $lang['Workflow_Name'] ?>">
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-md-2">
                                                    <label for="userName"><?php echo $lang['Select_Group']; ?><span style="color: red;">*</span></label>
                                                    <?php
                                                    mysqli_set_charset($db_con, "utf8");
                                                    $workflGroupMap = array();
                                                    $grpbrg = mysqli_query($db_con, "select group_id from tbl_workflow_to_group where workflow_id like '%$rwGroup[workflow_id]%'") or die('Error : ' . mysqli_error($db_con));
                                                    $rwrkfltogrp = mysqli_fetch_assoc($grpbrg);
                                                    $workflGroupMap = explode(",", $rwrkfltogrp['group_id']);
                                                    $groupAllow = array();

                                                    $group_permission = mysqli_query($db_con, "SELECT group_id,user_ids FROM `tbl_bridge_grp_to_um` where  FIND_IN_SET(" . $_SESSION['cdes_user_id'] . ", user_ids)");
                                                    while ($allGroupRow = mysqli_fetch_array($group_permission)) {
                                                        $groupAllow[] = $allGroupRow['group_id'];
                                                    }

                                                    $groupAllow = implode(',', $groupAllow);
                                                    ?>
                                                </div>
                                                <div class="col-md-12">
                                                    <select class="select2 select2-multiple" name="groupswf[]" multiple="multiple" multiple data-placeholder="<?php echo $lang['Select_Group']; ?>" required parsley-trigger="change" id="groups">
                                                        <?php
                                                        if (isset($_REQUEST['groups']) && !empty($_REQUEST['groups'])) {
                                                            $workflGroupMap = explode(",", $_REQUEST['groups']);
                                                        }

                                                        $grp = mysqli_query($db_con, "select * from tbl_group_master where group_id IN($groupAllow)") or die('error' . mysqli_error($db_con));
                                                        while ($rwGrp = mysqli_fetch_assoc($grp)) {
                                                            if (in_array($rwGrp['group_id'], $workflGroupMap)) {
                                                                echo '<option value="' . $rwGrp['group_id'] . '" selected>' . $rwGrp['group_name'] . '</option>';
                                                            } else {
                                                                echo '<option value="' . $rwGrp['group_id'] . '" >' . $rwGrp['group_name'] . '</option>';
                                                            }
                                                        }
                                                        ?>

                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-md-2">
                                                    <label for="userName"><?php echo $lang['freq']; ?></label>
                                                </div>
                                                <div class="col-md-5">
                                                    <div class="checkbox checkbox-primary">
                                                        <input class="form_req form_required" id="chkbox" type="checkbox" name="formRequire" value="1" onchange="valueChanged()" <?php
                                                                                                                                                                                    if ($rwGroup['form_req'] == '1') {
                                                                                                                                                                                        echo 'checked';
                                                                                                                                                                                    }
                                                                                                                                                                                    ?>>
                                                        <label for="checkbox6"></label>
                                                    </div>
                                                </div>

                                            </div>
                                            <div class="form-group">
                                                <div class="col-md-2">
                                                    <label for="userName"><?php echo $lang['pdfreq']; ?></label>
                                                </div>
                                                <div class="col-md-10">
                                                    <div class="checkbox checkbox-primary">
                                                        <input class="form_req" type="checkbox" name="pdfreq" id="pdfreq" value="1" <?php
                                                                                                                                    if ($rwGroup['pdf_req'] == '1' || $_REQUEST['pdf_req'] == 1) {
                                                                                                                                        echo 'checked';
                                                                                                                                    }
                                                                                                                                    ?>>
                                                        <label for="checkbox61"></label>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <div class="col-md-2">
                                                    <label for="rfi"><?php echo 'RFI'; ?></label>
                                                </div>
                                                <div class="col-md-10">
                                                    <div class="checkbox checkbox-success m-r-15">
                                                        <input class="form_req " type="checkbox" id="rfi" name="rfi" value="1" <?= $_GET['rfi'] == 1 ? 'checked' : '' ?> />
                                                        <label for="checkbox6"></label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group description" <?php
                                                                                if ($rwGroup['form_req'] == '1' || $_REQUEST['formreq'] == 1) {
                                                                                    echo 'style=display:block';
                                                                                } else {
                                                                                    echo 'style=display:none';
                                                                                };
                                                                                ?>>
                                                <?php
                                                $qry = mysqli_query($db_con, "select * from tbl_bridge_workflow_to_form where workflow_id='$id'") or die(mysqli_error($db_con));
                                                if (mysqli_num_rows($qry) < 1 && $rwGroup['form_req'] == 1) {
                                                ?>
                                                    <textarea class="form-control" rows="5" id="chkeditor" name="area"><?php echo $rwGroup['workflow_description']; ?></textarea>
                                                <?php } else { ?>
                                                    <div class="content">

                                                        <div id="data"></div>

                                                        <div id="stage1" class="build-wrap"></div>
                                                        <?php if (isset($_REQUEST['id']) && $_REQUEST['id']) { ?>
                                                            <form class="render-wrap"></form>
                                                        <?php } ?>

                                                        <div class="render-wrap"></div>
                                                        <div class="action-buttons">
                                                        </div>

                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="col-md-2">&nbsp;</div>
                                            <div class="col-md-10 workflowbutton">
                                                <button id="getJSON" class="btn btn-primary waves-effect waves-light" name="editWrkFlow"><?php echo $lang['Updt_Wrkflw']; ?></button>
                                                <a href="<?php echo basename($_SERVER['REQUEST_URI']); ?>" class="btn btn-warning waves-effect waves-light m-l-5"><?php echo $lang['Reset']; ?></a>
                                            </div>
                                        </div>
                                        <input type="hidden" name="wid" value="<?php echo $formid; ?>" id="formid">
                                        <input type="hidden" name="wid" value="<?php echo $rwGroup['workflow_id']; ?>" id="workid">
                                        <input type="hidden" name="widf" value="<?php echo $id; ?>" id="wid">
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
                <?php //require_once './application/pages/rightSidebar.php';  
                ?>
                <!-- /Right-bar -->
            </div>
            <!-- END wrapper -->
            <?php require_once './application/pages/footerForjs.php'; ?>
            <script src="assets/plugins/bootstrap-filestyle/js/bootstrap-filestyle.min.js" type="text/javascript"></script>
            <script src="assets/plugins/select2/js/select2.min.js" type="text/javascript"></script>
            <script type="text/javascript" src="assets/plugins/parsleyjs/parsley.min.js"></script>

            <script type="text/javascript">
                $(document).ready(function() {
                    $('form').parsley();

                });
                $(".select2").select2();
            </script>
            <!---html textarea editor js code--->
            <script src="assets/plugins/tinymce/tinymce.min.js"></script>
            <script type="text/javascript">
                $(document).ready(function() {
                    if ($("#chkeditor").length > 0) {
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
                            style_formats: [{
                                    title: 'Bold text',
                                    inline: 'b'
                                },
                                {
                                    title: 'Red text',
                                    inline: 'span',
                                    styles: {
                                        color: '#ff0000'
                                    }
                                },
                                {
                                    title: 'Red header',
                                    block: 'h1',
                                    styles: {
                                        color: '#ff0000'
                                    }
                                },
                            ]
                        });
                    }
                });
            </script>

            <script type="text/javascript">
                function valueChanged() {
                    if ($('.form_required').is(":checked"))
                        $(".description").show();
                    else
                        $(".description").hide();
                }
                $(document).ready(function() {
                    ("#hidden").hide();
                });
            </script>
            <script src="assets/formbulider/js/vendor.js"></script>
            <script src="assets/formbulider/js/form-builder.min.js"></script>
            <script src="assets/formbulider/js/form-render.min.js"></script>
            <?php if (isset($_REQUEST['id']) && !empty($_REQUEST['id'])) { ?>
                <script src="assets/formbulider/js/updatefetch.js"></script>
            <?php } else { ?>
                <script src="assets/formbulider/js/updatedemo.js"></script>
            <?php } ?>
            <script>
                //   for wait gif display after submit

                $('#getJSON').click(function() {
                    var workflowname = document.getElementById("workflow_name").value;
                    var group = [];
                    $.each($("#groups option:selected"), function() {
                        group.push($(this).val());
                    });
                    //alert( group.join(", "));
                    var groups = group.join(", ");

                    if (workflowname == "" || group == "") {} else {
                        var heiht = $(document).height();
                        //alert(heiht);
                        $('#wait').css('height', heiht);

                        $('#getJSON').attr("disabled", "disabled");
                        $('#wait').show();
                        //$('#wait').css('height',heiht);

                        $('#afterClickHide').hide();
                        return true;
                    }
                });
            </script>
            <script>
                $('#workflow_name').keypress(function(e) {
                    var regex = new RegExp("^[a-zA-Z_ ]+$");
                    var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
                    if (regex.test(str)) {
                        return true;
                    }

                    e.preventDefault();
                    return false;
                })
                $(document).ready(function() {
                    $('input#workflow_name').on("cut copy paste", function(e) {
                        e.preventDefault();
                    });
                });
            </script>
            <script type="text/javascript">
                <?php if (isset($_REQUEST['id']) && !empty($_REQUEST['id'])) { ?>
                    $(document).ready(function() {
                        $(".description").show();
                        $("#existing").show();
                    })
                <?php } else { ?>
                    $(document).ready(function() {
                        $("#desp").hide();
                        $("#existing").hide();
                    })
                <?php } ?>
                <?php if (isset($_REQUEST['id']) && !empty($_REQUEST['id'])) { ?>

                    function valueChanged() {
                        if ($('.form_required').is(":checked")) {
                            $(".description").show();
                            $("#existing").show();
                        } else {

                            $(".description").hide();
                            $("#existing").hide();

                        }
                    }
                <?php } ?>

                function existform(id) {

                    if (id !== "") {
                        var chkbox, , rfi;
                        var wfname = document.getElementById("workflow_name").value;
                        var group = [];
                        $.each($("#groups option:selected"), function() {
                            group.push($(this).val());
                        });
                        //alert( group.join(", "));
                        var groups = group.join(", ");

                        if (document.getElementById("chkbox").checked) {
                            chkbox = 1;
                        } else {
                            chkbox = 0;
                        }
                        if (document.getElementById("pdfreq").checked) {
                            pdfreq = 1;
                        } else {
                            pdfreq = 0;
                        }
                        if (document.getElementById("rfi").checked) {
                            rfi = 1;
                        } else {
                            rfi = 0;
                        }
                        var wid = document.getElementById("wid").value;
                        window.location.href = "updateWorkflow?id=" + id + "&groups=" + groups + "&formreq=" + chkbox + "&wfname=" + wfname + "&pdfreq=" + pdfreq + "&rfi=" + rfi + "&id=" + wid;


                    }

                }
            </script>

</body>

</html>