<!DOCTYPE html>
<html>
<?php
require_once './loginvalidate.php';
require_once './application/config/database.php';
require_once './application/pages/head.php';
?>
<?php
if (isset($_REQUEST['fid']) && !empty($_REQUEST['fid'])) {
    $formid = $_REQUEST['fid'];

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
            $json['name'] = $row['name'];
            $json['placeholder'] = $row['placeholder'];
            $json['subtype'] = $row['subtype'];
            $json['className'] = $row['class'];
            $json['required'] = $row['required'];
            $json['placeholder'] = $row['placeholder'];
            $json['maxlength'] = $row['maxlength'];
            $attrid = $row['aid'];
            $childattr = mysqli_query($db_con, "select * from tbl_form_attribute where dependency_id='$attrid'");
            $content = array();
            if (mysqli_num_rows($childattr) > 0) {
                $json['values'] = array();
                $valueData = array();
                while ($attrrow = mysqli_fetch_assoc($childattr)) {
                    $valueData['label'] = $attrrow['label'];
                    $valueData['value'] = $attrrow['value'];
                    $json['values'][$k] = $valueData;
                    $k++;
                }
            }

            $result[$j] = $json;
            $j++;
        }

        return $result;
    }
}
?>

<head>
    <style>
        .form-wrap.form-builder .frmb .form-elements {
            padding: 10px 60px !important;
        }

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
            padding: 15px !important;
            background-color: #ebeff2 !important;
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
</head>

<body class="fixed-left">
    <!-- Begin page -->
    <div id="wrapper">
        <!-- Top Bar Start -->
        <?php require_once './application/pages/topBar.php'; ?>
        <!-- Top Bar End -->
        <!-- ========== Left Sidebar Start ========== 1001/10556/00959 12/12/2011 14:33:58-->

        <?php require_once './application/pages/sidebar.php'; ?>
        <link href="assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
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
                            <li><a href="createWorkflow"><?php echo $lang['WORKFLOW_MANAGEMENT']; ?></a></li>
                            <li class="active"><?php echo $lang['Create_Work_Flow']; ?></li>
                            <li> <a href="#" data-toggle="modal" data-target="#help-modal" id="helpview" data="20" title="<?= $lang['help']; ?>"><i class="fa fa-question-circle" style="font-size: 23px"></i> </a></li>
                            <a href="javascript:void(0)" class="btn btn-primary waves-effect waves-light pull-right btn-sm margin-t-9" onclick="goPrevious();" title="<?php echo $lang['Go_to_previous_page']; ?>"><i class="fa fa-arrow-circle-left"></i></a>
                        </ol>
                    </div>
                    <div class="row">
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h4 class="header-title col-md-6"><?php echo $lang['Create_New_Work_Flow']; ?></h4>
                            </div>
                            <div class="box-body">
                                <div class="col-lg-12">
                                    <div class="card-box">
                                        <div class="row">
                                            <div class="form-group row">
                                                <div class="col-md-2">
                                                    <label for="userName"><?php echo $lang['WName']; ?><span style="color: red;">*</span></label>
                                                </div>
                                                <div class="col-md-10">
                                                    <input type="text" class="form-control wfa" name="workflowName" id="workflow_name" placeholder="<?php echo $lang['Workflow_Name']; ?>" required value="<?= isset($_REQUEST['wfname']) && !empty($_REQUEST['wfname']) ? $_REQUEST['wfname'] : '' ?>">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-md-2">
                                                    <label for="userName"><?php echo $lang['wgroup']; ?><span style="color: red;">*</span></label>
                                                </div>
                                                <div class="col-md-10">
                                                    <select class="select42 select2-multiple" name="groupswf[]" multiple="multiple" multiple data-placeholder="<?php echo $lang['Select_Group']; ?>" required parsley-trigger="change" id="groups">
                                                        <?php
                                                        mysqli_set_charset($db_con, "utf8");
                                                        $group_permission = mysqli_query($db_con, "SELECT group_id,user_ids FROM `tbl_bridge_grp_to_um`");
                                                        while ($allGroupRow = mysqli_fetch_array($group_permission)) {
                                                            $user_ids = explode(',', $allGroupRow['user_ids']);
                                                            if (in_array($_SESSION['cdes_user_id'], $user_ids)) {
                                                                $grp = mysqli_query($db_con, "select group_id,group_name from tbl_group_master WHERE group_id='$allGroupRow[group_id]' order by group_name asc") or die('Error' . mysqli_error($db_con));
                                                                while ($rwGrp = mysqli_fetch_assoc($grp)) {
                                                                    if (in_array($rwGrp['group_id'], explode(",", $_REQUEST['groups']))) {
                                                                        echo '<option value="' . $rwGrp['group_id'] . '" selected>' . $rwGrp['group_name'] . '</option>';
                                                                    } else {
                                                                        echo '<option value="' . $rwGrp['group_id'] . '">' . $rwGrp['group_name'] . '</option>';
                                                                    }
                                                                }
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-md-2">
                                                    <label for="userName"><?php echo $lang['freq']; ?></label>
                                                </div>
                                                <div class="col-md-5">
                                                    <div class="checkbox checkbox-primary m-r-15">
                                                        <input class="form_req form_required" type="checkbox" id="chkbox" name="formRequire" value="1" onchange="valueChanged()" <?= $_GET['formreq'] == 1 ? 'checked' : '' ?> />
                                                        <label for="checkbox6"></label>
                                                    </div>
                                                </div>
                                                <div class="col-md-5" id="existing">
                                                    <select class="form-control select42" placeholder="<?php echo $lang['SELECT_EXISTING_WORK_FLOW']; ?>" onchange="existform(this.value)">

                                                        <option value=""><?php echo $lang['SELECT_EXISTING_WORK_FLOW']; ?></option>
                                                        <?php
                                                        $qry = mysqli_query($db_con, "select * from tbl_form_master") or die("ERROR:" . mysqli_error($db_con));
                                                        while ($rows = mysqli_fetch_assoc($qry)) {
                                                            if ($_REQUEST['fid'] == $rows['fid']) {
                                                        ?>

                                                                <option value="<?= $rows['fid'] ?>" selected=""><?= $rows['form_name'] ?></option>
                                                            <?php } else { ?>
                                                                <option value="<?= $rows['fid'] ?>"><?= $rows['form_name'] ?></option>
                                                        <?php
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-md-2">
                                                    <label for="userName"><?php echo $lang['pdfreq']; ?></label>
                                                </div>
                                                <div class="col-md-10">
                                                    <div class="checkbox checkbox-success m-r-15">
                                                        <input class="form_req " type="checkbox" id="pdfreq" name="pdfreq" value="1" <?= $_GET['pdfreq'] == 1 ? 'checked' : '' ?> />
                                                        <label for="checkbox6"></label>
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


                                            <div class="form-group ">
                                                <div class="description" id="desp">

                                                    <!--                                              <textarea class="form-control" rows="5" id="editor" name="area"><?php echo $rwGroup['workflow_description']; ?></textarea>-->

                                                    <!-- <div class="content"  >

                                                        </div>-->
                                                    <!--Form Builder Starts -->
                                                    <div class="content">
                                                        <div id="data"></div>

                                                        <div id="stage1" class="build-wrap"></div>
                                                        <?php if (isset($_REQUEST['fid']) && $_REQUEST['fid']) { ?>
                                                            <form class="render-wrap"></form>
                                                        <?php } ?>

                                                        <div class="render-wrap"></div>
                                                        <div class="action-buttons">
                                                        </div>

                                                    </div>
                                                    <!--Form Builder Ends -->
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="col-md-2">&nbsp;</div>
                                                <div class="col-md-10 workflowbutton">
                                                    <button id="getJSON" type="button" class="btn btn-primary waves-effect waves-light"><?php echo $lang['Create_Workflow']; ?></button>
                                                    <!-- <button id="getdata" type="button" class="btn btn-primary waves-effect waves-light" >Create Workflow</button>-->
                                                    <a href="createWorkflow" class="btn btn-warning waves-effect waves-light m-l-5">
                                                        <?php echo $lang['Reset']; ?>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> <!-- container -->
                <div id="div"></div>
            </div> <!-- content -->
            <?php require_once './application/pages/footer.php'; ?>
            <?php if (isset($_REQUEST['fid'])) { ?>
                <textarea id="formjsondata" style="display: none"><?= json_encode(fetchForm($db_con, $formid)); ?></textarea>
            <?php } ?>
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
    <div id="chkhide">
        <div style=" display: none; background: rgba(0,0,0,0.5); width: 100%; z-index: 2000; position: fixed; top:0;" id="wait">
            <img src="assets/images/proceed.gif" alt="load" style=" margin-left: 48%; margin-top: 250px; width: 100px; height:100px; position: fixed;" />
        </div>
    </div>

    <?php require_once './application/pages/footerForjs.php'; ?>
    <script src="assets/formbulider/js/vendor.js"></script>
    <script src="assets/plugins/bootstrap-filestyle/js/bootstrap-filestyle.min.js" type="text/javascript"></script>
    <script src="assets/plugins/select2/js/select2.min.js" type="text/javascript"></script>
    <script>
        $('#getJSON').click(function() {
            debugger
            var workflowname = document.getElementById("workflow_name").value;
            var group = [];
            $.each($("#groups option:selected"), function() {
                group.push($(this).val());
            });
            //alert( group.join(", "));
            var groups = group.join(", ");
            if (workflowname == "" || groups == "") {

            } else {
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
    </script>

    <script type="text/javascript">
        //sk@241218 : Required field message for creating workflow.
        var pfarf = "<?= $lang['pfarf'] ?>"; //end

        $(".select42").select2();
    </script>
    <!---html textarea editor js code--->
    <script src="assets/formbulider/js/form-builder.min.js?v=<?php time(); ?>"></script>
    <script src="assets/formbulider/js/form-render.min.js?v=<?php time(); ?>"></script>
    <?php if (isset($_REQUEST['fid']) && !empty($_REQUEST['fid'])) { ?>
        <script src="assets/formbulider/js/fetch.js"></script>
    <?php } else { ?>
        <script src="assets/formbulider/js/demo.js"></script>
    <?php } ?>
    <script src="assets/plugins/tinymce/tinymce.min.js?v=<?php time(); ?>"></script>

    <script type="text/javascript">
        $(document).ready(function() {
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
        <?php if (isset($_REQUEST['fid']) && !empty($_REQUEST['fid'])) { ?>
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
        <?php if (isset($_REQUEST['fid']) && !empty($_REQUEST['fid'])) { ?>

            function valueChanged() {
                window.location.href = "createWorkflow";
            }
        <?php } else { ?>

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
                var chkbox, pdfreq, rfi;
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
                window.location.href = "createWorkflow?fid=" + id + "&groups=" + groups + "&formreq=" + chkbox + "&wfname=" + wfname + "&pdfreq=" + pdfreq + "&rfi=" + rfi;
            }

        }
        $(document).ready(function() {
            $('input#workflow_name').on("cut copy paste", function(e) {
                e.preventDefault();
            });
        });
    </script>

</body>

</html>