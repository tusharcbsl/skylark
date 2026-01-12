<?php
require_once '../../sessionstart.php';
if (!isset($_SESSION['cdes_user_id'])) {
    header("location:../../logout");
}
if (isset($_SESSION['lang'])) {
    $file = "../../" . $_SESSION['lang'] . ".json";
} else {
    $file = "../../English.json";
}
//for user role
$data = file_get_contents($file);
$lang = json_decode($data, true);

require './../config/database.php';

//for user role

$chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$_SESSION[cdes_user_id]', user_ids) > 0") or die('Error:' . mysqli_error($db_con));

$rwgetRole = mysqli_fetch_assoc($chekUsr);

// echo $rwgetRole['dashboard_mydms']; die;
if ($rwgetRole['edit_workflow'] != '1') {
    header('Location: ../../index');
}

/* if (!isset($_POST['ID'], $_POST['token'])) {
    echo "Unauthorised access !";
    exit;
} */

$id = preg_replace("/[^0-9 ]/", "", $_POST['ID']);
$group = mysqli_query($db_con, "select * from tbl_workflow_master where workflow_id='$id'");
$rwGroup = mysqli_fetch_assoc($group);
?>
</head-->
<body>
    <!--form validation init-->
    <script src="assets/plugins/tinymce/tinymce.min.js"></script>
    <script src="assets/plugins/bootstrap-filestyle/js/bootstrap-filestyle.min.js" type="text/javascript"></script>
    <link href="assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
    <script src="assets/plugins/select2/js/select2.min.js" type="text/javascript"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            if ($("#editor").length > 0) {
                tinymce.init({
                    selector: "textarea#editor",
                    theme: "modern",
                    height: 150,
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


    <div class="row"> 
        <div class="col-md-12"> 
            <div class="form-group">
                <label><?php echo $lang['Workflow_Name']; ?><span style="color: red;">*</span></label>
                <input type="text" name="workflowName" autocomplete="off" required class="form-control" id="wfname" value="<?php echo $rwGroup['workflow_name']; ?>" placeholder="<?= $lang['Workflow_Name'] ?>">
            </div>
            <div class="form-group row">
                <div class="col-md-2">
                    <label for="userName"><?php echo $lang['Slt_Grp']; ?><span style="color: red;">*</span></label>
                    <?php
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
                    <select class="select3 select2-multiple" name="groupswf[]" multiple="multiple" multiple data-placeholder="<?php echo $lang['Slt_Grp']; ?>" required parsley-trigger="change">
                        <?php
                        $grp = mysqli_query($db_con, "select * from tbl_group_master  where group_id IN($groupAllow)") or die('error' . mysqli_error($db_con));
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
            <div class="form-group row">
                <div class="col-md-2">
                    <label for="userName"><?php echo $lang['Form_Required']; ?>:</label>
                </div>
                <div class="col-md-10">
                    <input class="form_req" type="checkbox" name="formRequire" value="1" onchange="valueChanged()" <?php
                    if ($rwGroup['form_req'] == '1') {
                        echo 'checked';
                    }
                    ?>>
                </div>
            </div>
            <div class="form-group description" <?php
            if ($rwGroup['form_req'] == '1') {
                echo 'style=display:block';
            } else {
                echo 'style=display:none';
            };
            ?> >
                <textarea class="form-control" rows="5" id="editor" name="area"><?php echo $rwGroup['workflow_description']; ?></textarea>
            </div>
        </div> 

    </div> 

    <input type="hidden" name="wid" value="<?php echo $rwGroup['workflow_id']; ?>">

    <script type="text/javascript">
        function valueChanged()
        {
            if ($('.form_req').is(":checked"))
                $(".description").show();
            else
                $(".description").hide();
        }
    </script>
    <script>

        $('#wfname').keypress(function (e) {
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

        $(document).ready(function () {
            $('form').parsley();
        });
        $(".select3").select2();

        $(document).ready(function () {
            $('input#wfname').on("cut copy paste", function (e) {
                e.preventDefault();
            });
        });
    </script>
</body>