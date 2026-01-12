<?php
require_once '../../sessionstart.php';
if (!isset($_SESSION['cdes_user_id'])) {
    header("location:../../logout");
}

require './../config/database.php';

//for user role
mysqli_set_charset($db_con, "utf8");
$chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$_SESSION[cdes_user_id]', user_ids) > 0") or die('Error:' . mysqli_error($db_con));

$rwgetRole = mysqli_fetch_assoc($chekUsr);

$id = $_POST['ID'];
$meta = mysqli_query($db_con, "select * from tbl_metadata_master where id='$id'");
$rwMeta = mysqli_fetch_assoc($meta);
if (isset($_SESSION['lang'])) {
    $file = "../../" . $_SESSION['lang'] . ".json";
} else {
    $file = "../../English.json";
}
$ses_val = $_SESSION;
$langDetail = mysqli_query($db_con, "select * from tbl_language where lang_name='$_SESSION[lang]'") or die('Error : ' . mysqli_error($db_con));
$langDetail = mysqli_fetch_assoc($langDetail);
//for user role
$data = file_get_contents($file);
$lang = json_decode($data, true);
?>
<link href="assets/plugins/bootstrap-select/css/bootstrap-select.min.css" rel="stylesheet" />   
<div class="errormsg form-group" style="display:none">
    <span class="label label-danger" style="font-size: 12px;"><?php echo $lang['maximum_minimum_value']; ?></span>
</div>
<div class="form-group row">
    <div class="col-md-4">
        <label for="userName"><?php echo $lang['Ent_Fld_Nm']; ?> <span class="text-alert">*</span></label>
    </div>
    <div class="col-md-8">
        <input type="text" id="metaData1" class="form-control numspecialcharlock" placeholder="<?php echo $lang['Ent_Fld_Nm']; ?>" name="fieldName" value="<?php echo $rwMeta['field_name']; ?>" required maxlength="30">
        <input type="hidden" value="<?php echo $id; ?>" name="metaId"/>
    </div>
</div>
<div class="form-group row">
    <div class="col-md-4">
        <label for="userName"><?php echo $lang['Select_DataType']; ?></label>
    </div>
    <div class="col-md-8">
        <select class="form-control select24" data-live-search="true"  name="dataType" id="selection1" onchange="changeplh()" disabled>
            <option selected disabled value style="background: #808080; color: #121213;"><?= $lang['Select_DataType']; ?></option>

            <option <?php
            if ($rwMeta['data_type'] == 'char') {
                echo 'selected';
            }
            ?>><?php echo $lang['Char']; ?></option>
            <option <?php
            if ($rwMeta['data_type'] == 'datetime') {
                echo 'selected';
            }
            ?>><?php echo $lang['datetime']; ?></option>
            <option <?php
            if ($rwMeta['data_type'] == 'Int') {
                echo 'selected';
            }
            ?>><?php echo $lang['Int']; ?></option>
            <option <?php
            if ($rwMeta['data_type'] == 'BigInt') {
                echo 'selected';
            }
            ?>><?php echo $lang['BigInt']; ?></option>
            <option <?php
            if ($rwMeta['data_type'] == 'float') {
                echo 'selected';
            }
            ?>><?php echo $lang['Float']; ?></option>
            <option <?php
            if ($rwMeta['data_type'] == 'double') {
                echo 'selected';
            }
            ?>><?php echo $lang['Double']; ?></option>
            <option <?php
            if ($rwMeta['data_type'] == 'varchar') {
                echo 'selected';
            }
            ?>><?php echo $lang['Varchar']; ?></option>
            <option <?php
            if ($rwMeta['data_type'] == 'range') {
                echo 'selected';
            }
            ?>><?php echo $lang['range']; ?></option>
            <option <?php
            if ($rwMeta['data_type'] == 'boolean') {
                echo 'selected';
            }
            ?>><?php echo $lang['binary']; ?></option>
            <option <?php
            if ($rwMeta['data_type'] == 'list') {
                echo 'selected';
            }
            ?>><?php echo $lang['list']; ?></option>
            <option <?php
            if ($rwMeta['data_type'] == 'checklist') {
                echo 'selected';
            }
            ?>><?php echo $lang['checklist']; ?></option>
             <option <?php
            if ($rwMeta['data_type'] == 'date') {
                echo 'selected';
            }
            ?>><?php echo $lang['Date']; ?></option>
        </select>
    </div>
    <input type="hidden" value="<?php echo $rwMeta['data_type']; ?>" name="dataTyp"/>
</div>
<div class="clearfix"></div>
<div class="form-group row">
    <?php if (($rwMeta['data_type'] == 'range') || ($rwMeta['data_type'] == 'list') || ($rwMeta['data_type'] == 'checklist')) { ?>

    <?php } else {
        ?>
        <div class="col-md-4">
            <label for="userName"><?php echo $lang['Enter_Data_Length']; ?></label>
        </div>
        <?php
    }
    if ($rwMeta['data_type'] == 'range') {
        $datalength = explode(',', $rwMeta['length_data']);
        $i = 1;
        foreach ($datalength as $dataminmax) {
            ?>
            <div class="col-md-4">
                <?php if ($i == 1) { ?>
                    <label><?php echo $lang['enter_min_length']; ?> <span class="text-alert">*</span></label>
                <?php } else { ?>
                    <label><?php echo $lang['enter_max_length']; ?> <span class="text-alert">*</span></label>
                <?php } ?>

            </div>
            <div class="col-md-8">
                <div class="form-group">
                    <input type="text" min="1" class="form-control" name="dataLengthrange[]" onkeyup="checkMaximumMinimumRange('1')" placeholder="<?php
                    if ($i == 1) {
                        echo $lang['enter_min_length'];
                    } else {
                        echo $lang['enter_max_length'];
                    }
                    ?>" id="textbox<?php echo $i; ?>" value="<?php
                           if (!empty($dataminmax)) {
                               echo $dataminmax;
                           }
                           ?>">
                </div>
            </div> 
            <?php
            $i++;
        }
    } else {
        if (($rwMeta['data_type'] == 'range') || ($rwMeta['data_type'] == 'list') || ($rwMeta['data_type'] == 'checklist')) {
            ?>

        <?php } else { ?>
            <div class="col-md-8">
                <input type="text" min="1" class="form-control" name="dataLength" id="textbox1" value="<?php
                if (!empty($rwMeta['length_data'])) {
                    echo $rwMeta['length_data'];
                }
                ?>" <?php echo (empty($rwMeta['length_data']) ? "readonly" : ""); ?>>
            </div> 
            <?php
        }
    }
    ?>
</div>
<div class="form-group row">
    <div class="col-md-4">
        <label for="userName"><?php echo $lang['Is_Mandatory']; ?><span style="color:red;">*</span></label>
    </div>
    <div class="col-md-8">
        <select name="mandatory" class="form-control select24" data-live-search="true" required>
            <option disabled selected><?php echo $lang['slt']; ?></option>
            <option value="Yes" <?php
            if ($rwMeta['mandatory'] == 'Yes') {
                echo 'selected';
            }
            ?>><?php echo $lang['Yes']; ?></option>
            <option value="No" <?php
            if ($rwMeta['mandatory'] == 'No') {
                echo 'selected';
            }
            ?>><?php echo $lang['No']; ?></option>
        </select>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div id="list" style="<?php
        if ($rwMeta['data_type'] == 'list') {
            echo "display: block;";
        } else {
            echo "display: none;";
        }
        ?>"">
            <table  style="width:100%;" cellspacing="20" class="table table-bordered">
                <tr>
                    <th> <label><strong><?= $lang['SNO']; ?></strong></label></th>
                    <th> <label><strong><?= $lang['label']; ?></strong><span class="text-alert">*</span></label></th>
                    <th><label><strong><?= $lang['value']; ?></strong><span class="text-alert">*</span></label></th>
                    <th><label><strong><?= $lang['Add']; ?></strong></label></th>
                </tr>
                <tbody id="listdatatype">
                    <?php
                    $listlabel = $rwMeta['label'];
                    $listvalues = explode(',', $rwMeta['value']);
                    $listlabel = explode(',', $listlabel);
                    ?>
                    <?php
                    $i = 1;
                    foreach (array_combine($listvalues, $listlabel) as $listvalues => $names) {
                        ?>
                        <tr id="tablerowlist<?php echo $i; ?>">
                            <td><?php echo $i; ?></td>
                            <td>
                                <input type="text" name="label[]" value="<?php echo $names; ?>" class="form-control numspecialcharlock listtextbox" placeholder="<?= $lang['label']; ?>" maxlength="20">
                            </td>
                            <td>
                                <input type="text" name="value[]" value="<?php echo $listvalues; ?>" class="form-control numspecialcharlock listtextbox" placeholder="<?= $lang['value']; ?>" maxlength="20">
                            </td>

                            <?php if ($i == 1) { ?>
                                <td>
                                    <a href="javascript:void(0);" class="btn btn-primary btn-sm" onclick="addMorelistvalue();" title="<?= $lang['Add_more']; ?>"><i class="fa fa fa-plus"></i></a>
                                <?php } else {
                                    ?>
                                <td><a href="javascript:void(0);" class="btn btn-danger btn-sm" onclick="removelistboxRows(<?php echo $i; ?>);" title="Remove" ><i class="fa fa fa-minus"></i></a></td> 

                            </tr>
                            <?php
                        }
                        $i++;
                    }
                    ?>


                </tbody>

            </table>
        </div>
        <div id="checklist" style="<?php
        if ($rwMeta['data_type'] == 'checklist') {
            echo "display: block;";
        } else {
            echo "display: none;";
        }
        ?>">
            <table  style="width:100%;" cellspacing="20" class="table table-bordered">
                <tr>
                    <th> <label><strong><?= $lang['SNO']; ?></strong></label></th>
                    <th> <label><strong><?= $lang['label']; ?></strong><span class="text-alert">*</span></label></th>
                    <th><label><strong><?= $lang['value']; ?></strong><span class="text-alert">*</span></label></th>
                    <th><label><strong><?= $lang['Add']; ?></strong></label></th>
                </tr>
                <tbody id="checklistdatatype">

                    <?php
                    $label = $rwMeta['label'];
                    $listvalue = explode(',', $rwMeta['value']);
                    $labellist = explode(',', $label);
                    ?>
                    <?php
                    $i = 1;
                    foreach (array_combine($listvalue, $labellist) as $listvalue => $name) {
                        ?>
                        <tr id="tablerow<?php echo $i; ?>">
                            <td><?php echo $i; ?></td> 
                            <td>
                                <input type="text" value="<?php echo $name; ?>" name="checkboxlabel[]" class="form-control numspecialcharlock checkboxlabel" placeholder="<?= $lang['label']; ?>" maxlength="50">
                            </td>
                            <td>
                                <input type="text" name="checkboxvalue[]" value="<?php echo $listvalue; ?>" class="form-control numspecialcharlock checkboxlabel" placeholder="<?= $lang['value']; ?>" maxlength="50">
                            </td>
                            <?php if ($i == 1) { ?>
                                <td>
                                    <a href="javascript:void(0);" class="btn btn-primary btn-sm" onclick="addMoreChecklistBoxvalue();" title="<?= $lang['Add_more']; ?>"><i class="fa fa fa-plus"></i></a>
                                <?php } else {
                                    ?>
                                <td><a href="javascript:void(0);" class="btn btn-danger btn-sm" onclick="removeChecklistboxRows(<?php echo $i; ?>);" title="Remove" ><i class="fa fa fa-minus"></i></a></td> 

                            </tr>
                            <?php
                        }
                        $i++;
                    }
                    ?>

                </tbody>

            </table>
        </div>
    </div>
</div>
<!--for select all or none---->
<script type="text/javascript" src="assets/js/jquery.min.js"></script>
<script src="assets/plugins/bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>
<script type="text/javascript">
<?php if ($rwMeta['data_type'] == 'checklist') { ?>

                                $("input[name*='checkboxlabel[]']").attr("required", "required");
                                $("input[name*='checkboxvalue[]']").attr("required", "required");
                                $("input[name*='label[]']").removeAttr("required", true);
                                $("input[name*='value[]']").removeAttr("required", true);

<?php } else if ($rwMeta['data_type'] == 'list') { ?>

                                $("input[name*='label[]']").attr("required", "required");
                                $("input[name*='value[]']").attr("required", "required");
                                $("input[name*='checkboxlabel[]']").removeAttr("required", true);
                                $("input[name*='checkboxvalue[]']").removeAttr("required", true);

<?php } ?>
                            function checkMaximumMinimumRange(Id) {
                                if (parseFloat($("#textbox2").val()) < parseFloat($("#textbox1").val()))
                                {
                                    $(".errormsg").css("display", "block").css("color", "red");
                                    $("#addrange").prop('disabled', true);
                                } else {
                                    //var id = parseInt(Id) + parseInt(1);
                                    //var a = $("#textbox2").val();
                                    // $("#textbox2").val(parseInt(a));
                                    $(".errormsg").css("display", "none");
                                    $("#addrange").prop('disabled', false);
                                }
                            }
                            $("input#textbox1,input#textbox2").keypress(function (e) {
                                //if the letter is not digit then display error and don't type anything
                                if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57) && e.which != 46) {
                                    //display error message
                                    return false;
                                }
                                str = $(this).val();
                                str = str.split(".").length - 1;
                                if (str > 0 && e.which == 46) {
                                    return false;
                                }
                            });
                            //for placeholder display
                            //for placeholder display
                        
                            $(".select24").selectpicker();
                            //number special charecterlock
                            $('.numspecialcharlock').bind("keyup change", function ()
                            {
                                var GrpNme = $(this).val();
                                re = /[`~!@#$%^&*()|+\-=?;:'",<>\{\}\[\]\\\/]/gi;
                                var isSplChar = re.test(GrpNme);
                                if (isSplChar)
                                {
                                    var no_spl_char = GrpNme.replace(/[`~!@#$%^&*()|+\-=?;:'",<>\{\}\[\]\\\/]/gi, '');
                                    $(this).val(no_spl_char);
                                }
                            });
                            $('.numspecialcharlock').bind(function () {
                                $(this).val($(this).val().replace(/[<>]/g, ""))
                            });
</script>
<script type="text/javascript">

//    google.load("elements", "1", {
//        packages: "transliteration"
//    });
//    function onLoad12() {
//        var langcode = '<?php echo $langDetail['lang_code']; ?>';
//        var options = {
//            sourceLanguage: 'en',
//            destinationLanguage: [langcode],
//            shortcutKey: 'ctrl+g',
//            transliterationEnabled: true
//        };
//        var control =
//                new google.elements.transliteration.TransliterationControl(options);
//
//        var ids = ["metaData1"];
//        control.makeTransliteratable(ids);
//    }
//    $.getScript('test.js', function () {
//        console.log("hfbfdbfd ");
//        // Call custom function defined in script
//        onLoad12();
//    });
//    google.setOnLoadCallback(onLoad12);
//    console.log("test 12");

</script> 
<script type="text/javascript">
    function addMorelistvalue()
    {
        var count = $('#listdatatype').children('tr').length;
        count++;
        $('#listdatatype').append('<tr id="addlistRow' + count + '"><td>' + count + '.' + '</td><td> <input type="text" name="label[]" class="form-control numspecialcharlock listtextbox" placeholder="<?= $lang['label']; ?>" maxlength="20" required></td><td> <input type="text" name="value[]" class="form-control listtextbox numspecialcharlock" placeholder="<?php echo $lang['value']; ?>" maxlength="20" required></td><td><a href="javascript:void(0);" class="btn btn-danger btn-sm" onclick="removelistLastRow(' + count + ');" title="Remove" ><i class="fa fa fa-minus"></i></a></td></tr>');

        //for avoid special charecter
        $('.numspecialcharlock').bind("keyup change", function ()
        {
            var GrpNme = $(this).val();
            re = /[`~!@#$%^&*()_|+\=?;,:'"<>\{\}\[\]\\\/]/gi;
            var isSplChar = re.test(GrpNme);
            if (isSplChar)
            {
                var no_spl_char = GrpNme.replace(/[`~!@#$%^&*()_|+\=?;,:'"<>\{\}\[\]\\\/]/gi, '');
                $(this).val(no_spl_char);
            }
        });
        $('.numspecialcharlock').bind(function () {
            $(this).val($(this).val().replace(/[<>]/g, ""))
        });


    }
    function removelistLastRow(rmvId) {
        $('#addlistRow' + rmvId).remove();
    }
    function addMoreChecklistBoxvalue()
    {
        var count = $('#checklistdatatype').children('tr').length;
        count++;
        $('#checklistdatatype').append('<tr id="addchecklistRow' + count + '"><td>' + count + '.' + '</td><td> <input type="text" name="checkboxlabel[]" class="form-control numspecialcharlock checkboxlabel" placeholder="<?= $lang['label']; ?>" maxlength="50" required></td><td> <input type="text" name="checkboxvalue[]" class="form-control checkboxlabel numspecialcharlock" placeholder="<?php echo $lang['value']; ?>" maxlength="50" required></td><td><a href="javascript:void(0);" class="btn btn-danger btn-sm" onclick="removelistboxLastRow(' + count + ');" title="Remove" ><i class="fa fa fa-minus"></i></a></td></tr>');

        //for avoid special charecter
        $('.numspecialcharlock').bind("keyup change", function ()
        {
            var GrpNme = $(this).val();
            re = /[`~!@#$%^&*()_|+\=?;,:'"<>\{\}\[\]\\\/]/gi;
            var isSplChar = re.test(GrpNme);
            if (isSplChar)
            {
                var no_spl_char = GrpNme.replace(/[`~!@#$%^&*()_|+\=?;,:'"<>\{\}\[\]\\\/]/gi, '');
                $(this).val(no_spl_char);
            }
        });
        $('.numspecialcharlock').bind(function () {
            $(this).val($(this).val().replace(/[<>]/g, ""))
        });

    }
    function removelistboxLastRow(rmvId) {
        $('#addchecklistRow' + rmvId).remove();
    }
    function removeChecklistboxRows(rmvId) {
        $('#tablerow' + rmvId).remove();
    }
    function removelistboxRows(rmvId) {
        $('#tablerowlist' + rmvId).remove();
    }
</script>