<?php
require_once '../../sessionstart.php';
if (!isset($_SESSION['cdes_user_id'])) {
    header("location:../../logout");
}
require './../config/database.php';
//for user role
if (isset($_SESSION['lang'])) {
    $file = "../../" . $_SESSION['lang'] . ".json";
} else {
    $file = "../../English.json";
}
//for user role
$data = file_get_contents($file);
$lang = json_decode($data, true);
mysqli_set_charset($db_con, "utf8");
$chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$_SESSION[cdes_user_id]', user_ids) > 0") or die('Error:' . mysqli_error($db_con));
$ses_val = $_SESSION;
$langDetail = mysqli_query($db_con, "select * from tbl_language where lang_name='$_SESSION[lang]'") or die('Error : ' . mysqli_error($db_con));
$langDetail = mysqli_fetch_assoc($langDetail);
$rwgetRole = mysqli_fetch_assoc($chekUsr);
$id = $_POST['ID'];
$getMetaId = mysqli_query($db_con, "select * from tbl_document_master where doc_id = '$id'") or die('Error:' . mysqli_error($db_con));
$meta_row = mysqli_fetch_assoc($getMetaId);
$getMetaId = mysqli_query($db_con, "select * from tbl_metadata_to_storagelevel where sl_id = '$meta_row[doc_name]'") or die('Error:' . mysqli_error($db_con));
$i = 1;
?>
<link href="assets/plugins/bootstrap-select/css/bootstrap-select.min.css" rel="stylesheet" />
<link href="assets/plugins/sweetalert2/sweetalert2.min.css" rel="stylesheet" type="text/css">
<link href="assets/css/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen">
<?php
while ($rwgetMetaId = mysqli_fetch_assoc($getMetaId)) {
    $getMetaName = mysqli_query($db_con, "select * from tbl_metadata_master where id = '$rwgetMetaId[metadata_id]'") or die('Error:' . mysqli_error($db_con));

    while ($rwgetMetaName = mysqli_fetch_assoc($getMetaName)) {
        $meta = mysqli_query($db_con, "select `$rwgetMetaName[field_name]` from tbl_document_master where doc_id='$meta_row[doc_id]'");
        $rwMeta = mysqli_fetch_array($meta);

        if ($rwgetMetaName['field_name'] == 'noofpages') {
            
        } else {
            ?>     
            <div class="col-md-6">
                <div class="row m-b-10">
                    <div class="form-group">
                        <div class="col-md-12">
                            <label for="userName"><?php echo $rwgetMetaName['field_name']; ?> <span style="color:red;"><?php
                                    if ($rwgetMetaName['mandatory'] == "Yes") {
                                        echo "*";
                                    }
                                    ?></span></label>
                        </div>
                        <div class="col-md-12">
                            <?php if ($rwgetMetaName['data_type'] == 'datetime') {
                                ?>
                                <div class="input-group date datetimepicker" data-link-field="dtp_input1">
                                    <input type="text" id="metaData<?php echo $i; ?>" class="form-control"  name="fieldName<?php echo $i; ?>" placeholder="DD-MM-YYYY HH:MM" value="<?php
                                    if (!empty($rwMeta[$rwgetMetaName['field_name']])) {

                                        $date1 = $rwMeta[$rwgetMetaName['field_name']];
                                        echo date('d-m-Y H:i', strtotime($date1));
                                    }
                                    ?>" <?php
                                           if ($rwgetMetaName['mandatory'] == "Yes") {
                                               echo "required";
                                           }
                                           ?> >

                                    <span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span>
                                </div>

                            <?php } else if ($rwgetMetaName['data_type'] == 'date') {
                                ?>
                                <div class="input-group">
                                    <input type="text" class="form-control datepicker"  id="metaData<?php echo $i; ?>" name="fieldName<?php echo $i; ?>" placeholder="dd-mm-yyyy" <?php
                                    if ($rwgetMetaName['mandatory'] == 'Yes') {
                                        echo'required';
                                    }
                                    ?> value="<?php echo $rwMeta[$rwgetMetaName['field_name']]; ?>">
                                    <span class="input-group-addon bg-custom b-0 text-white"><i class="icon-calender"></i></span>
                                </div> 
                            <?php } else if ($rwgetMetaName['data_type'] == 'varchar' || $rwgetMetaName['data_type'] == 'char') {
                                ?>
                                <input type="text" id="metaData<?php echo $i; ?>" class="form-control <?= $rwgetMetaName['data_type'] ?> " name="fieldName<?php echo $i; ?>"  <?= $rwgetMetaName['data_type'] == 'varchar' ?> value="<?php echo $rwMeta[$rwgetMetaName['field_name']]; ?>" <?php
                                if ($rwgetMetaName['mandatory'] == "Yes") {
                                    echo "required";
                                }
                                ?> placeholder="<?php echo $lang['Data_should_be']; ?> <?php echo $rwgetMetaName['length_data']; ?> <?php echo $lang['characters']; ?>" maxlength="<?php echo $rwgetMetaName['length_data']; ?>">
                                       <?php
                                   } else if ($rwgetMetaName['data_type'] == 'bit') {
                                       ?>
                                <input type="text" id="metaData<?php echo $i; ?>" class="form-control intvl <?= $rwgetMetaName['data_type'] ?>" name="fieldName<?php echo $i; ?>"  placeholder="<?php echo $lang['Data_should_be']; ?> <?php echo $rwgetMetaName['length_data']; ?> <?php echo $lang['only']; ?>" value="<?php echo $rwMeta[$rwgetMetaName['field_name']]; ?>" <?php
                                if ($rwgetMetaName['mandatory'] == "Yes") {
                                    echo "required";
                                }
                                ?>>
                                       <?php
                                   } else if ($rwgetMetaName['data_type'] == 'Int' || $rwgetMetaName['data_type'] == 'float' || $rwgetMetaName['data_type'] == 'BigInt' || $rwgetMetaName['data_type'] == 'bit') {
                                       ?>
                                <input type="text" id="metaData<?php echo $i; ?>" class="form-control intvl <?= $rwgetMetaName['data_type'] ?>" name="fieldName<?php echo $i; ?>"  min="0" placeholder="<?php echo $lang['Data_length_exceed']; ?> <?php echo $rwgetMetaName['length_data']; ?> <?php echo $lang['digits']; ?>" value="<?php echo $rwMeta[$rwgetMetaName['field_name']]; ?>" <?php
                                if ($rwgetMetaName['mandatory'] == "Yes") {
                                    echo "required";
                                }
                                ?> maxlength="<?= isset($rwgetMetaName['length_data']) ? "$rwgetMetaName[length_data]" : '' ?>" >
                                       <?php
                                   } elseif ($rwgetMetaName['data_type'] == 'range') {
                                       $filedrange = explode(',', $rwgetMetaName['length_data']);
                                       ?>
                                <input class="form-control intvl" id="metaData<?php echo $i; ?>" value="<?php echo $rwMeta[$rwgetMetaName['field_name']]; ?>" name="fieldName<?php echo $i; ?>" type="text" minlength="<?= $filedrange[0]; ?>" maxlength="<?= $filedrange[1]; ?>" placeholder="<?php echo $lang['add_enter_range_value']; ?> <?= $filedrange[0] . ' ' . $lang['and'] . ' ' . $lang['enter_max_length'] . ' ' . $filedrange[1] . ' ' . $lang['digits']; ?>" <?php
                                if ($rwgetMetaName['mandatory'] == 'Yes') {
                                    echo'required';
                                }
                                ?>>


                            <?php } elseif ($rwgetMetaName['data_type'] == 'boolean') { ?>
                                <input class="form-control intvl intLimit" id="metaData<?php echo $i; ?>" name="fieldName<?php echo $i; ?>" type="text" value="<?php echo $rwMeta[$rwgetMetaName['field_name']]; ?>" maxlength="1" placeholder="<?php echo $lang['Entr_0_or_1']; ?>" <?php
                                if ($rwgetMetaName['mandatory'] == 'Yes') {
                                    echo'required';
                                }
                                ?>>

                                <?php
                            } elseif ($rwgetMetaName['data_type'] == 'list') {
                                $label = $rwgetMetaName['label'];
                                $value = $rwgetMetaName['value'];
                                $listvalue = explode(',', $rwgetMetaName['value']);
                                $labellist = explode(',', $label);
                                ?>
                                <input type="hidden" class="listval" data-id="<?php echo $i; ?>" id="metaData<?php echo $i; ?>"/>
                                <select id="listvalue" class="form-control select2" data-live-search="true" name="fieldName<?php echo $i; ?>" <?php
                                if ($rwgetMetaName['mandatory'] == 'Yes') {
                                    echo'required';
                                }
                                ?>>
                                    <option value="" selected><?php echo $lang['Select'] . ' ' . $rwgetMetaName['field_name']; ?></option> 
                                    <?php
                                    foreach (array_combine($listvalue, $labellist) as $listvalue => $name) {
                                        if ($rwMeta[$rwgetMetaName['field_name']] == $listvalue) {
                                            ?>
                                            <option value="<?php echo $listvalue; ?>" selected><?php echo $name; ?></option> 
                                        <?php } else { ?>
                                            <option value="<?php echo $listvalue; ?>"><?php echo $name; ?></option> 
                                            <?php
                                        }
                                    }
                                    ?> 
                                </select>

                                <?php
                            } elseif ($rwgetMetaName['data_type'] == 'checklist') {
                                $checklistvalue = explode(',', $rwgetMetaName['value']);
                                $labelchecklist = explode(',', $rwgetMetaName['label']);
                                $keywordvalue = explode(',', $rwMeta[$rwgetMetaName['field_name']]);
                                ?>
                                <input type="hidden" name="fieldName<?php echo $i; ?>" class="form-control <?php echo $rwgetMetaName['field_name']; ?>" value="<?php echo $rwMeta[$rwgetMetaName['field_name']]; ?>" id="metaData<?php echo $i; ?>">
                                <?php
                                $j = 1;
                                foreach (array_combine($checklistvalue, $labelchecklist) as $checklistvalue => $name) {
                                    ?>
                                    <div class="checkbox checkbox-primary m-b-5">
                                        <input id="<?= $name . $j; ?>" type="checkbox" onclick="setCheckboxValue(<?php echo $i; ?>, '<?php echo $rwgetMetaName['field_name']; ?>');" name="checkbox<?php echo $i; ?>[]" value="<?php echo $checklistvalue; ?>" <?php echo ((in_array($checklistvalue, $keywordvalue)) ? "checked" : ""); ?>  <?php
                                        if ($rwgetMetaName['mandatory'] == 'Yes') {
                                            echo'required';
                                        }
                                        ?>>
                                        <label for="<?= $name . $j; ?>"><?php echo $name; ?></label>

                                    </div>
                                    <?php
                                    $j++;
                                }
                            } else {
                                ?>
                                <input type="text" id="metaDatca1" class="form-control" name="fieldName<?php echo $i; ?>" value="<?php echo $rwMeta[$rwgetMetaName['field_name']]; ?>" <?php
                                if ($rwgetMetaName['mandatory'] == "Yes") {
                                    echo "required";
                                }
                                ?>>
                                       <?php
                                   }
                                   ?>
                        </div>
                    </div> 
                </div>
            </div>
        <?php }
        ?>

        <?php
    }
    $i++;
}
?>
<div class="col-md-6">
    <?php if ($rwgetRole['update_file'] == '1') { ?>
        <div class="form-group">
            <div class="col-md-12">
                <label for="myImage1"><?php echo $lang['UPDAT_DCUMNT']; ?></label>
            </div>
            <div class="col-md-12">
                <input class="form-control" id="myImage1" name="fileName" data-buttonname="btn-primary" type="file">
                <input type="hidden" id="pCount" name="pageCount">
                <input type="hidden" value="<?php echo $id; ?>" name="docid"/>
            </div>

        </div>
    <?php } ?>
</div>
<script src="assets/moment-with-locales.js"></script>
<script type="text/javascript" src="assets/js/bootstrap-datetimepicker.js" charset="UTF-8"></script>
<script src="assets/plugins/bootstrap-filestyle/js/bootstrap-filestyle.min.js" type="text/javascript"></script>
<script src="assets/plugins/bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>
<script type="text/javascript">
                        $(".select2").selectpicker();
</script>
<script>
    $(document).ready(function () {
        $('.datetimepicker').datetimepicker({
            //language:  'fr',
            weekStart: 1,
            todayBtn: 1,
            autoclose: 1,
            todayHighlight: 1,
            startView: 2,
            forceParse: 0,
            showMeridian: 1,
            startDate: '+0d',
            format: 'dd-mm-yyyy hh:ii'
        });
        $('.datepicker').datetimepicker({
            minView: 2,
            autoclose: 1,
            format: "dd-mm-yyyy"
        });
        $(".datetimepicker").keydown(function (e) {
            e.preventDefault();

        });
        $(".datepicker").keydown(function (e) {
            e.preventDefault();

        });
    });
    function setCheckboxValue(row, fieldname) {
        var metadatavalues = $("input[name='checkbox" + row + "[]']:checked").map(function () {
            return this.value;
        }).get().join(",");
        $("." + fieldname).val(metadatavalues);

    }
    // for binary metadata value
    (function ($) {
        $.fn.inputFilter = function (inputFilter) {
            return this.on("input keydown keyup mousedown mouseup select contextmenu drop", function () {
                if (inputFilter(this.value)) {
                    this.oldValue = this.value;
                    this.oldSelectionStart = this.selectionStart;
                    this.oldSelectionEnd = this.selectionEnd;
                } else {
                    this.value = "";
                }
            });
        };
    }(jQuery));
    $(".intLimit").inputFilter(function (value) {
        return /^\d*$/.test(value) && (value === "" || parseInt(value) <= 1);
    });
</script>
<script>
    // for page count
    $("#myImage1").change(function () {
        if (this.files[0].type == 'application/pdf') {
            var reader = new FileReader();
            reader.readAsBinaryString(this.files[0]);
            reader.onloadend = function () {
                var count = reader.result.match(/\/Type[\s]*\/Page[^s]/g).length;
                $("#pCount").val(count);
                // console.log('Number of Pages:',count );
            }
        } else {
            $("#pCount").val('1');
        }
    });
</script>

<script type="text/javascript">
    google.load("elements", "1", {
        packages: "transliteration"
    });
    function onLoad12() {
        var langcode = '<?php echo $langDetail['lang_code']; ?>';
        var options = {
            sourceLanguage: 'en',
            destinationLanguage: [langcode],
            shortcutKey: 'ctrl+g',
            transliterationEnabled: true
        };
        var control =
                new google.elements.transliteration.TransliterationControl(options);
        var ids = ["metaDatca1"];
        control.makeTransliteratable(ids);
    }
    $.getScript('test.js', function () {
        // Call custom function defined in script
        onLoad12();
    });
    google.setOnLoadCallback(onLoad12);

</script> 