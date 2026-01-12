<?php if ($checkincheckout == '0') { ?>
    <div class="middle" style="width:40%; margin: auto; text-align: center; vertical-align: middle;">
        <div id="comment-wrapper" >

            <h4><?php echo $lang['Edit_MetaData']; ?></h4>
            <div class="comment-list">
                <div class="comment-list-container">
                    <form method="post" enctype="multipart/form-data">
                        <?php
                        $getMetaId = mysqli_query($db_con, "select * from tbl_document_master where doc_id = '$docId'") or die('Error:' . mysqli_error($db_con));
                        $meta_row = mysqli_fetch_assoc($getMetaId);
                        $getMetaId = mysqli_query($db_con, "select * from tbl_metadata_to_storagelevel where sl_id = '$meta_row[doc_name]'") or die('Error:' . mysqli_error($db_con));
                        $i = 1;
                        while ($rwgetMetaId = mysqli_fetch_assoc($getMetaId)) {
                            $getMetaName = mysqli_query($db_con, "select * from tbl_metadata_master where id = '$rwgetMetaId[metadata_id]'") or die('Error:' . mysqli_error($db_con));

                            while ($rwgetMetaName = mysqli_fetch_assoc($getMetaName)) {
                                $meta = mysqli_query($db_con, "select `$rwgetMetaName[field_name]` from tbl_document_master where doc_id='$meta_row[doc_id]'");
                                $rwMeta = mysqli_fetch_array($meta);

                                if ($rwgetMetaName['field_name'] == 'noofpages') {
                                    
                                } else {
                                    ?>                           

                                    <div class="form-group">

                                        <div class="col-md-12">
                                            <label for="userName"><?php echo $rwgetMetaName['field_name']; ?> <span style="color:red;"><?php
                                                    if ($rwgetMetaName['mandatory'] == "Yes") {
                                                        echo "*";
                                                    }
                                                    ?></span></label>
                                        </div>
                                        <div class="col-md-12 m-b-15">
                                            <?php if ($rwgetMetaName['data_type'] == 'datetime') {
                                                ?>
                                                <div class="input-group date">
                                                    <input type="text" class="form-control datetimepicker" name="fieldName<?php echo $i; ?>" placeholder="yyyy-mm-dd h:m:s" value="<?php
                                                    if (!empty($rwMeta[$rwgetMetaName['field_name']])) {
                                                        echo $date1 = $rwMeta[$rwgetMetaName['field_name']];
                                                        //echo date('Y-m-d h:mm:ss', $date1);
                                                    }
                                                    ?>" <?php
                                                           if ($rwgetMetaName['mandatory'] == "Yes") {
                                                               echo "required";
                                                           }
                                                           ?> >
                                                    <span class="input-group-addon bg-custom b-0 text-white"><i class="icon-calender"></i></span>
                                                </div>

                                            <?php } else if ($rwgetMetaName['data_type'] == 'varchar' || $rwgetMetaName['data_type'] == 'char') {
                                                ?>
                                                <input type="text" id="metaData<?php echo $i; ?>" class="form-control <?= $rwgetMetaName['data_type'] ?> " name="fieldName<?php echo $i; ?>"  <?= $rwgetMetaName['data_type'] == 'varchar' ? 'pattern="[a-zA-Z0-9@.-\s]+"' : 'pattern="[a-zA-Z\s]+"' ?> value="<?php echo $rwMeta[$rwgetMetaName['field_name']]; ?>" <?php
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
                                                <input class="form-control intvl intLimit" id="metaData<?php echo $i; ?>" name="fieldName<?php echo $i; ?>" maxlength="1" type="text" value="<?php echo $rwMeta[$rwgetMetaName['field_name']]; ?>" placeholder="<?php echo $lang['Entr_0_or_1']; ?>" <?php
                                                if ($rwgetMetaName['mandatory'] == 'Yes') {
                                                    echo'required';
                                                }
                                                ?>>

                                                <?php
                                            } else if ($rwgetMetaName['data_type'] == 'list') {
                                                $label = $rwgetMetaName['label'];
                                                $value = $rwgetMetaName['value'];
                                                $listvalue = explode(',', $rwgetMetaName['value']);
                                                $labellist = explode(',', $label);
                                                ?>
                                                <input type="hidden" class="listval" data-id="<?php echo $i; ?>" id="metaData<?php echo $i; ?>"/>
                                                <select id="listvalue" class="form-control select2" name="fieldName<?php echo $i; ?>" <?php
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
                                            } else if ($rwgetMetaName['data_type'] == 'checklist') {
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
                                                <input class="form-control intvl" id="metaData<?php echo $i; ?>" name="fieldName<?php echo $i; ?>" type="text" value="<?php echo $rwMeta[$rwgetMetaName['field_name']]; ?>"   <?php
                                   if ($rwmeta['mandatory'] == 'Yes') {
                                       echo'required';
                                   }
                                                       ?>>
                                                       <?php
                                                   }
                                                   ?>
                                        </div>

                                    </div> 


                                    <?php
                                }
                            }
                            $i++;
                        }
                        ?>
                        <?php if ($rwgetRole['update_file'] == '1') { ?>
                            <div class="form-group">
                                <div class="col-md-12">
                                    <label class="pull-left"><?php echo $lang['UPDAT_DCUMNT']; ?></label>
                                </div>
                                <div class="col-md-12 m-b-15">
                                    <input class="form-control" id="myImage1" name="fileName" data-buttonname="btn-primary" type="file">
                                    <input type="hidden" id="pCount" name="pageCount">
                                    <input type="hidden" value="<?php echo $docId; ?>" name="docid"/>
                                </div>
                            </div>
                        <?php } ?>
                        <div class="row">
                            <div class="col-md-12 m-l-15">
                                <button type="submit" name="editMetaValue"  class="btn btn-primary pull-left"><?php echo $lang['Submit']; ?></button>
                            </div>

                        </div>
                    </form>
                </div>

            </div>

        </div>
    </div>
    <link href="assets/bootstrap-datetimepicker.css" rel="stylesheet"></script>
    <script src="assets/moment-with-locales.js"></script>
    <script src="assets/bootstrap-datetimepicker.js"></script>
    <script>
        $(document).ready(function (e) {
    //file button validation
            $("#con-close-modal-act").delegate("#myImage1", "change", function (e) {
                var size = document.getElementById("myImage1").files[0].size;
                // alert(size);
                var name = document.getElementById("myImage1").files[0].name;
                //alert(lbl);
                if (name.length < 100)
                {
                    $.post("../application/ajax/valiadate_client_memory.php", {size: size}, function (result, status) {
                        if (status == 'success') {
                            //$("#stp").html(result);
                            var res = JSON.parse(result);
                            if (res.status == "true")
                            {
                                // $("#memoryres").html("<span style=color:green>" + res.msg + "</span>");
                                // $.Notification.autoHideNotify('success', 'top center', 'Success', res.msg)
                                $("#mem_msg").fadeIn().addClass("mem_msg_success").html(res.msg);
                            } else {
                                $("#mem_msg").fadeIn().addClass("mem_msg_fail").html(res.msg);
                                $("#hideOnClick").prop('disabled', true)
                                //$.Notification.autoHideNotify('warning', 'top center', 'Oops', res.msg)
                                //$("#memoryres").html("<span style=color:red>" + res.msg + "</span>");
                            }

                        }
                    });
                } else {
                    var input = $("#myImage1");
                    var fileName = input.val();

                    if (fileName) { // returns true if the string is not empty
                        input.val('');
                    }
                    //$.Notification.autoHideNotify('error', 'top center', 'Error', "File Name Too Long");
                    $("#mem_msg").fadeIn().addClass("mem_msg_fail").html('File Name Too Long');
                }

            });
        })
        $('.datetimepicker').datetimepicker({
            format: "YYYY-MM-DD hh:mm:s",
        });
    </script>
<?php } ?>