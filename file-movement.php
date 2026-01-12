<?php
require_once 'file-action-html.php';
//require_once './loginvalidate.php';

$slid = ((!empty($slid) ? $slid : $_GET['id']));
?>
<link href="assets/css/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen">


<!-- for export metadata-->
<div id="export" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="panel panel-color panel-primary">
            <div class="panel-heading">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h2 class="panel-title"><?php echo $lang['Export_CSV']; ?></h2>
            </div>
            <form method="post" action="export">
                <div class="panel-body">
                    <div class="col-md-12">
                        <div class="radio radio-success radio-inline">
                            <input type="radio" name="radExp" id="inlineRadio1" value="all" checked="checked">
                            <label for="inlineRadio1"><?php echo $lang['Al_Files_in_slt_fld']; ?></label>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <input value="<?php echo $slid; ?>" name="slid" type="hidden">
                    <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button>
                    <button type="submit" name="startExport" class="btn btn-primary"><i class="fa fa-download"></i> <?php echo $lang['Strt_xprt']; ?></button>
                </div>

            </form>
        </div>
    </div>
</div>

<!-- move selected files---->
<div id="move-selected-files" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="panel panel-color panel-danger">
            <div class="panel-heading">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h2 class="panel-title" id="unseMove"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i><?php echo $lang['Hre_msge']; ?></h2>
                <h2 class="panel-title" style="display:none;" id="mov"><?php echo $lang['Move_Slt_Files']; ?></h2>
            </div>
            <div id="unselected" style="display:none;">
                <div class="panel-body">
                    <h5 class="text-alert"><?php echo $lang['Pls_slct_Fles_fr_mve']; ?></h5>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button>
                </div>
            </div>
            <div id="selected">
                <?php
                $moveFolderName = mysqli_query($db_con, "select * from tbl_storage_level where sl_id = $slid"); //or die('Error in move folder name: ' . mysqli_error($db_con));
                $rwmoveFolderName = mysqli_fetch_assoc($moveFolderName);
                ?>
                <form method="post">
                    <div class="panel-body">
                        <input type="hidden" name="doc_id_smove_multi" id="doc_id_smove_multi" value="">
                        <input type="hidden" name="sl_id_move_multi" id="sl_id_move_multi">

                        <div class="row">
                            <div class="form-group col-sm-12">
                                <label><?php echo $lang['Move_Fld_File']; ?> <?php echo '(' . $rwmoveFolderName['sl_name'] . ') ' . $lang['folders']; ?></label>
                            </div>
                            <div class="col-md-12">
                                <label> <?php echo $lang['Move_To']; ?><span class="text-alert">*</span></label>
                                <select class="form-control select2" data-type="file" name="lastMoveId" required="">
                                    <option selected disabled><?php echo $lang['Sel_Strg_Lvl']; ?></option>
                                    <?php

                                    mysqli_set_charset($db_con, "utf8");
                                $perm = mysqli_query($db_con, "select sl_id from tbl_storagelevel_to_permission where user_id='$_SESSION[cdes_user_id]' group by sl_id");
                                $slperms = array();
                                while ($rwPerm = mysqli_fetch_assoc($perm)) {
                                    $slperms[] = $rwPerm['sl_id'];
                                }
                                $permcount = count($slperms);
                                $sl_perm = implode(',', $slperms);

                                    $sllevel = mysqli_query($db_con, "select * from tbl_storage_level where sl_id in($slpermIdes) AND delete_status='0' order by sl_name asc");

                            $all_data = mysqli_query($db_con, "select * from tbl_storage_level where  delete_status='0' order by sl_name asc");
                                   $new_arr=mysqli_fetch_all($all_data,MYSQLI_ASSOC);
                                    $parent_id_arr=array();
                            $sl_id_arr=array();
                            foreach($new_arr as $v)
                            {
                                $parent_id_arr[$v['sl_parent_id']][]=$v;
                                $sl_id_arr[$v['sl_id']]=$v;
                                $sl_parent_id_slid[$v['sl_parent_id']][$v['sl_id']]=$v;

                            }

                                    while ($rwSllevel = mysqli_fetch_assoc($sllevel)) {
                                        $level = $rwSllevel['sl_depth_level'];
                                        $SlId = $rwSllevel['sl_id'];
                                        //findChild($SlId, $level, $SlId);
                                        findchild1($SlId, $level, $SlId, $slid,$parent_id_arr,$sl_id_arr,$sl_parent_id_slid);
                                    }
                                    ?>
                                </select>
                                <br>
                                <div class="row">
                                    <div class="col-md-12" id="child1">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close'] ?></button>
                        <input type="submit" name="movemulti" class="btn btn-primary" value="<?php echo $lang['Mve_fles'] ?>">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!--copy selected files--->
<div id="copy-selected-files" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="panel panel-color panel-danger">
            <div class="panel-heading">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h2 class="panel-title" id="cop"> <i class="fa fa-exclamation-triangle" aria-hidden="true"></i> <?php echo $lang['Hre_msge']; ?></h2>
                <h2 class="panel-title" style="display:none;" id="ctitle"><?php echo $lang['Cpy_Slt_Files_in_Storage']; ?></h2>
            </div>
            <div id="unselected1" style="display:none;">
                <div class="panel-body">
                    <h5 class="text-alert"><?php echo $lang['Pls_slct_Fles_fr_Cpy']; ?></h5>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button>
                </div>
            </div>
            <div id="selected1">
                <form method="post">
                    <div class="panel-body" id="csf">
                        <div class="row">
                            <label><?php echo $lang['Copy_files']; ?> </label>
                            <input type="text" readonly id="tocopyfolder" name="toCopyFolder" class="form-control" value="<?php echo $tocpyFolder = $rwFolder['sl_name']; ?>">
                            <div class="col-md-12">
                                <p class="text-danger" id="error"></p>
                            </div>
                            <input type="hidden" name="doc_ids" id="doc_ids">
                            <input type="hidden" name="sl_id4" id="sl_id4">
                            <label> <?php echo $lang['Cpy_To']; ?> <span class="text-alert">*</span></label>
                            <select class="form-control select2" name="lastMoveId" required="">
                                <option selected disabled=""><?php echo $lang['Sel_Strg_Lvl']; ?></option>
                                <?php
                                 mysqli_set_charset($db_con, "utf8");
                            $perm = mysqli_query($db_con, "select sl_id from tbl_storagelevel_to_permission where user_id='$_SESSION[cdes_user_id]' group by sl_id");
                            $slperms = array();
                            while ($rwPerm = mysqli_fetch_assoc($perm)) {
                                $slperms[] = $rwPerm['sl_id'];
                            }
                            $permcount = count($slperms);
                            $sl_perm = implode(',', $slperms);
                                // $storeName = mysqli_query($db_con, "select * from tbl_storage_level where sl_id in($slpermIdes)") or die('Error in move store: ' . mysqli_error($db_con));
                                                    $sllevel = mysqli_query($db_con, "select * from tbl_storage_level where sl_id in($sl_perm) AND delete_status='0' order by sl_name asc");

                                $all_data = mysqli_query($db_con, "select * from tbl_storage_level where  delete_status='0' order by sl_name asc");
                            $new_arr=mysqli_fetch_all($all_data,MYSQLI_ASSOC);
                            $parent_id_arr=array();
                            $sl_id_arr=array();
                            foreach($new_arr as $v)
                            {
                                $parent_id_arr[$v['sl_parent_id']][]=$v;
                                $sl_id_arr[$v['sl_id']]=$v;
                                $sl_parent_id_slid[$v['sl_parent_id']][$v['sl_id']]=$v;

                            }

                                
                                while ($rwSllevel = mysqli_fetch_assoc($sllevel)) {
                                    $level = $rwSllevel['sl_depth_level'];
                                    $SlId = $rwSllevel['sl_id'];
                                    //findChild($SlId, $level, $SlId);
                                    findchild1($SlId, $level, $SlId, $slid,$parent_id_arr,$sl_id_arr,$sl_parent_id_slid);

                                }
                                ?>
                            </select>
                            <div class="col-md-12" id="child2"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input value="<?php echo $rwFolder['sl_id']; ?>" name="modi" type="hidden">
                        <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close'] ?></button>
                        <input type="submit" name="copyFiles" class="btn btn-primary" value="<?php echo $lang['Copy_files'] ?>">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div id="multi-csv-export-model" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="panel panel-color panel-danger">
            <div class="panel-heading">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h2 class="panel-title" id="unexportitle"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> <?php echo $lang['Hre_msge']; ?></h2>
                <h2 class="panel-title" id="export_title"><?php echo $lang['xprt_Slt_Dta']; ?></h2>
            </div>
            <form action="multi_data_export" method="post" enctype="multipart/form-data">
                <div class="panel-body">
                    <div class="row">
                        <label id="export_unselected" style="display:none;">
                            <h5 class="text-alert"> <?php echo $lang['Pls_slt_Files_for_xpt_dta']; ?></h5>
                        </label>
                        <div id="export_selected">
                            <label><?php echo $lang['Slct_Fles_fr_xpt_Frmt']; ?></label>
                            <select class="form-control select2" name="select_Fm">
                                <option value="csv"><?php echo $lang['Csv']; ?></option>
                                <option value="excel"><?php echo $lang['Excel']; ?></option>
                                <option value="pdf"><?php echo $lang['Pdf']; ?></option>
                                <option value="word"><?php echo $lang['Word']; ?></option>
                            </select>
                        </div>
                    </div>
                    <input type="hidden" name="export_doc_ids" id="export_doc_ids" value="">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button>
                    <input type="hidden" value="<?php echo $slid; ?>" name="id">
                    <button class="btn btn-primary waves-effect waves-light" type="submit" name="exportData" id="hidexp"> <i class="fa fa-download"></i> <?php echo $lang['Export']; ?></button>
                </div>
            </form>

        </div>
    </div>
</div>
<!--ends assign-meta-data modal -->
<!--share files with users-->
<div id="mail-selected-files" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="panel panel-color panel-danger">
            <div class="panel-heading">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h2 class="panel-title" id="mailf"> <i class="fa fa-exclamation-triangle" aria-hidden="true"></i> <?php echo $lang['Hre_msge']; ?></h2>
                <h2 class="panel-title" style="display:none;" id="mtitle"> <?php echo $lang['mail_document']; ?></h2>
            </div>
            <div id="unmail">
                <div class="panel-body">
                    <h5 class="text-alert"><?php echo $lang['Pls_slct_Fles_for_mail']; ?></h5>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button>
                </div>
            </div>
            <div id="selected3">
                <form method="post">
                    <div class="panel-body">
                        <div id="addemailbox">
                            <div class="row" id="emailremove1">
                                <div class="form-group">
                                    <label><?php echo $lang['Email']; ?><span class="text-alert">*</span></label>
                                    <div class="input-group">
                                        <input type="email" name="mailto[]" id="mailto" parsley-type="email" class="form-control emaillock" required="" placeholder="<?php echo $lang['Enter_Email_Id']; ?>">
                                        <span class="input-group-btn add-on">
                                            <a class="btn btn-primary btn-md" href="javascript:void(0);" onclick="addMoreRows('1');" title="Add More">
                                                <i class="fa fa-plus"></i>
                                            </a>
                                        </span>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group">
                                <label><?php echo $lang['subject']; ?><span class="text-alert">*</span></label>
                                <input type="text" name="subject" id="subject" class="form-control specialchaecterlock" placeholder="<?php echo $lang['enter_subject']; ?>" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group">
                                <label><?php echo $lang['description']; ?><span class="text-alert">*</span></label>
                                <textarea name="mailbody" id="mailbody" class="form-control specialchaecterlock" placeholder="<?php echo $lang['enter_description']; ?>" required></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" id="mail_docids" name="mailFile">
                        <input type="hidden" value="<?php echo $slid; ?>" name="storagemailFile">
                        <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close'] ?></button>
                        <button type="submit" name="mailFiles" class="btn btn-primary"><i class="fa fa-send-o"></i> <?php echo $lang['Send'] ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div><!-- /.modal -->

<!--share files with users-->
<div id="share-selected-files" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="panel panel-color panel-danger">
            <div class="panel-heading">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h2 class="panel-title" id="shr"> <i class="fa fa-exclamation-triangle" aria-hidden="true"></i> <?php echo $lang['Hre_msge']; ?></h2>
                <h2 class="panel-title" style="display:none;" id="stitle"> <?php echo $lang['Shre_Docs_Wth']; ?></h2>
            </div>
            <div id="unseshare">
                <div class="panel-body">
                    <p class="text-alert"><?php echo $lang['Pls_slct_Fles_for_Sre']; ?></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close']; ?></button>
                </div>
            </div>
            <div id="selected2">
                <form method="post">
                    <div class="panel-body">


                        <?php if ($rwgetRole['doc_share_time'] == '1' && $rwdocshare['docshare_enable_disable'] == '1') { ?>
                            <div class="form-group">
                                <div class="checkbox checkbox-primary checkbox-single">
                                    <input type="checkbox" id="extdate" value="1" name="extenddate" />
                                    <label for="extdate"><?= $lang['document_valid_msg']; ?></label>
                                </div>
                            </div>
                            <div class="row m-b-15">
                                <div class="form-group" id="extend">
                                    <div class="col-md-12">
                                        <label><?= $lang['select_doc_share_time']; ?><span class="text-alert">*</span></label>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="input-group date datetimepicker" data-date-format="dd MM yyyy HH:ii p" data-link-field="dtp_input1">
                                            <input class="form-control" name="docsharetime" id="docsharetime" type="text" placeholder="<?php echo $lang['select_doc_share_time']; ?>" value="" readonly>
                                            <span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span>
                                            <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                        <div class="form-group">
                            <label class="text-primary"><?php echo $lang['Select_User']; ?> <span class="text-alert">*</span></label>
                            <select class="select2 select2-multiple" multiple data-placeholder="<?php echo $lang['Select_User']; ?>" name="userid[]" required>
                                <?php
                                $sameGroupIDs = array();
                                $group = mysqli_query($db_con, "select * from tbl_bridge_grp_to_um where find_in_set('$_SESSION[cdes_user_id]',user_ids)") or die('Error' . mysqli_error($db_con));
                                while ($rwGroup = mysqli_fetch_assoc($group)) {
                                    $sameGroupIDs[] = $rwGroup['user_ids'];
                                }
                                $sameGroupIDs = array_unique($sameGroupIDs);
                                sort($sameGroupIDs);
                                $sameGroupIDs = implode(',', $sameGroupIDs);

                                $user = mysqli_query($db_con, "select * from tbl_user_master where user_id in($sameGroupIDs) order by first_name,last_name asc");
                                while ($rwUser = mysqli_fetch_assoc($user)) {
                                    if ($rwUser['user_id'] != 1 && $rwUser['user_id'] != $_SESSION['cdes_user_id']) {
                                        echo '<option value="' . $rwUser['user_id'] . '">' . $rwUser['first_name'] . ' ' . $rwUser['last_name'] . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <input type="hidden" id="share_docids" name="shareFile">
                        <button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?php echo $lang['Close'] ?></button>
                        <button type="submit" name="shareFiles" class="btn btn-primary"> <i class="fa fa-share-alt"></i> <?php echo $lang['Share'] ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div><!-- /.modal -->
<!--share files with users-->



<!-- for add and search metaData--->
<script>
    $(document).ready(function() {

        $('input#create_child,input#workflow_name').on("cut copy paste", function(e) {
            e.preventDefault();
        });
    });
</script>
<!---end add and search metadata-->
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

    $(".select2").select2();
</script>

<script>
    $("#parentCopyLevel").change(function() {
        var lbl = $(this).val();
        var copyf = $("#tocopyfolder").val();
        var sfolder = $(this).find(":selected").text();
        //alert(lbl);
        $.post("application/ajax/parentCopyList.php", {
            parentId: lbl,
            levelDepth: 0,
            sl_id: <?php echo $slid; ?>,
            folder: copyf,
            sfolder: sfolder
        }, function(result, status) {
            if (status == 'success') {
                $("#FilesCopy").html(result);
                //alert(result);
                $.post("application/ajax/checkDuplicate.php", {
                    parentId: lbl,
                    levelDepth: 0,
                    folder: copyf
                }, function(result, status) {
                    if (status == 'success') {
                        if (result == 0) {
                            $("#tocopyfolder").attr("readonly", "readonly");
                            $("#tocopyfolder").attr("readonly");
                        } else {
                            $("#error").html(copyf + " is already exist in " + sfolder + ".Please rename storage name.");
                            $("#tocopyfolder").removeAttr("readonly");
                        }
                    }
                });
            }
        });
    });
    $(document).ready(function() {
        $('form').parsley();
    });
</script>
<script>
    $("#moveToParentId").change(function() {
        var lbl = $(this).val();
        var atype = $(this).data('type');
        //alert(lbl);
        $.post("application/ajax/parentMoveList_1.php", {
            type: atype,
            parentId: lbl,
            levelDepth: 0,
            sl_id: <?php echo $slid; ?>
        }, function(result, status) {
            if (status == 'success') {
                $("#child1").html(result);
                //alert(result);
            }
        });
    });
</script>
<script type="text/javascript">
    $(document).ready(function() {
        $("#select_all").change(function() {
            $(".emp_checkbox").prop("checked", $(this).prop("checked"));
        });
    });

    //Extraxt CSV 

    $(document).ready(function() {

        function exportTableToCSV($table, filename) {

            var $rows = $table.find('tr:has(td),tr:has(th)'),
                //var $rows = $table.filter('tr:has(:checkbox:checked)').find('tr:has(td),tr:has(th)'),

                tmpColDelim = String.fromCharCode(11),
                tmpRowDelim = String.fromCharCode(0),
                colDelim = '","',
                rowDelim = '"\r\n"',
                csv = '"' + $rows.map(function(i, row) {
                    var $row = $(row),
                        $cols = $row.find('td,th');

                    return $cols.map(function(j, col) {
                        var $col = $(col),
                            text = $col.text();

                        return text.replace(/"/g, '""');
                    }).get().join(tmpColDelim);

                }).get().join(tmpRowDelim)
                .split(tmpRowDelim).join(rowDelim)
                .split(tmpColDelim).join(colDelim) + '"',
                csvData = 'data:application/csv;charset=utf-8,' + encodeURIComponent(csv);

            console.log(csv);

            if (window.navigator.msSaveBlob) {
                window.navigator.msSaveOrOpenBlob(new Blob([csv], {
                    type: "text/plain;charset=utf-8;"
                }), "csvname.csv")
            } else {
                $(this).attr({
                    'download': filename,
                    'href': csvData,
                    'target': '_blank'
                });
            }
        }

        $("#down").on('click', function(event) {

            exportTableToCSV.apply(this, [$('#home-table'), 'data.csv']);

        });
    });
</script>
<script>
    $("#copyToParentId").change(function() {
        var lbl = $(this).val();
        //alert(lbl);
        $.post("application/ajax/parentMoveList_2.php", {
            type: 'file',
            parentId: lbl,
            levelDepth: 0,
            sl_id: <?php echo $slid; ?>
        }, function(result, status) {
            if (status == 'success') {
                $("#child2").html(result);
                //alert(result);
            }
        });
    });
    $("#con-close-modal-history").delegate("a#deleteVersionDocument", "click", function() {
        var id = $(this).attr("data");
        //alert(id);
        $("#docidversion").val(id);
    });
</script>

<?php require_once 'file-action-php.php'; ?>

<?php
if (isset($_POST['shareFiles'], $_POST['token'])) {

    $fromUser = $_SESSION['cdes_user_id'];
    $ToUser = $_POST['userid'];
    $date = date('Y-m-d H:i:s');
    if (!empty($_POST['extenddate'])) {
        $docvalidupto = date('Y-m-d H:i:s', strtotime($_POST['docsharetime']));
    } else {
        $docvalidupto = NULL;
    }
    $ToUser = implode(",", $ToUser);
    $ToUser = preg_replace("/[^A-Za-z0-9, ]/", "", $ToUser);
    $shareDocId = $_POST['shareFile'];
    $shareDocIds = explode(',', $shareDocId);
    $myuser = explode(',', $ToUser);
    $doc_path = array();
    $filename = array();
    foreach ($shareDocIds as $shareId) {
        foreach ($myuser as $myuserid) {
            $chkDocId = mysqli_query($db_con, "select * from tbl_document_share where doc_ids='$shareId' and to_ids ='$myuserid'") or die('Error in check' . mysqli_error($db_con));
            if (mysqli_num_rows($chkDocId) > 0) {
                echo '<script>taskFailed("' . basename($_SERVER['REQUEST_URI'])  . '","' . $lang['Doc_Alrdy_Shared'] . '");</script>';

                exit();
            } else {

                $shareFiles = mysqli_query($db_con, "INSERT INTO `tbl_document_share`(`from_id`, `to_ids`, `doc_ids`, `share_date`,`doc_share_valid_upto`) VALUES ('$fromUser','$myuserid','$shareId', '$date','$docvalidupto')") or die('Error in insert share document' . mysqli_error($db_con));
                $shareDocNm = mysqli_query($db_con, "select old_doc_name from tbl_document_master where doc_id = '$shareId'") or die('Error :' . mysqli_error($db_con));
                while ($rwshareDocNm = mysqli_fetch_assoc($shareDocNm)) {
                    $filename[] = $rwshareDocNm['old_doc_name'];
                    if ($shareFiles) {
                        $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `doc_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null, '$shareDocIds', 'Storage Document $rwshareDocNm[old_doc_name] Shared','$date',null,'$host',null)") or die('error1212 : ' . mysqli_error($db_con));
                        if ($log) {
                            $message = "Y";
                        }
                    }
                }
            }
        }
    }

    if ($message == "Y") {
        $doclist = documentSharenotificationtoUsers($db_con, $shareDocId);
        $subject = $projectName . " document shared alert.";
        require_once './mail.php';

        //mail for subscribe document
        $subdocId = $shareDocId;
        $filenamed = implode(',', $filename);
        $userId = array();
        $checksubs = mysqli_query($db_con, "SELECT * FROM tbl_document_subscriber WHERE subscribe_docid in($subdocId) and find_in_set('5',action_id)");
        while ($rwcheckSubs = mysqli_fetch_assoc($checksubs)) {
            $userId[] = $rwcheckSubs['subscriber_userid'];
        }
        $userIds = implode(',', $userId);
        $mailto = array();
        $k = 1;
        $touser = mysqli_query($db_con, "SELECT user_email_id,first_name FROM tbl_user_master WHERE user_id in($userIds)");
        while ($rwtouser = mysqli_fetch_assoc($touser)) {
            $mailto[$k]['user_email_id'] = $rwtouser['user_email_id'];
            $mailto[$k]['first_name'] = $rwtouser['first_name'];
            $k++;
        }
        $fileaction = "Your subscribed document $filenamed shared.";
        foreach ($mailto as $to) {
            $email = $to['user_email_id'];
            $name = $to['first_name'];
            if (MAIL_BY_SOCKET) {
                $paramsArray = array(
                    'email' => $email,
                    'filenamed' => $filenamed,
                    'action' => 'filesubscribe',
                    'projectName' => $projectName,
                    'fileaction' => $fileaction,
                    'name' => $name
                );
                mailBySocket($paramsArray);
            } else {
                $mailsent = mailsenttoDocumentSubscribeUsers($email, $filenamed, $projectName, $fileaction, $name);
            }
        }
        if (MAIL_BY_SOCKET) {
            $paramsArray = array(
                'ToUser' => $ToUser,
                'doclist' => $doclist,
                'action' => 'sharedocument',
                'projectName' => $projectName,
                'subject' => $subject
            );
            mailBySocket($paramsArray);
        } else {
            sharedDocumentsMail($projectName, $subject, $ToUser, $doclist, $db_con);
        }

        echo '<script>taskSuccess("' . basename($_SERVER['REQUEST_URI'])  . '","' . $lang['Doc_shared_Sfly'] . '");</script>';
    } else {
        echo '<script>taskFailed("' . basename($_SERVER['REQUEST_URI'])  . '","' . $lang['Doc_nt_shared'] . '");</script>';
    }


    mysqli_close($db_con);
}

if (isset($_POST['copyFiles'], $_POST['token'])) {

    error_reporting(0);
    mysqli_autocommit($db_con, FALSE);
    $to = preg_replace("/[^A-Za-z0-9, ]/", "", $_POST['lastMoveId']);
    $to = mysqli_real_escape_string($db_con, $to);

    $doc_ids = preg_replace("/[^A-Za-z0-9, ]/", "", $_POST['doc_ids']);
    $doc_ids = mysqli_real_escape_string($db_con, $doc_ids);

    $sl_id4 = preg_replace("/[^A-Za-z0-9, ]/", "", $_POST['sl_id4']);
    $sl_id4 = mysqli_real_escape_string($db_con, $sl_id4);
    //echo 'hertererere'."select * from tbl_metadata_to_storagelevel where sl_id='$sl_id4'"; die;
    $meta = mysqli_query($db_con, "select * from tbl_metadata_to_storagelevel where sl_id='$sl_id4'"); //?
    //echo "select * from tbl_metadata_to_storagelevel where sl_id='$sl_id4'";
    $fetchresult = mysqli_query($db_con, "select * from tbl_document_master where doc_id in($doc_ids) and doc_name='$sl_id4'");
    $copyLaststrg = mysqli_query($db_con, "select sl_name, sl_parent_id, sl_depth_level  from tbl_storage_level where sl_id = '$to'") or die('Error :' . mysqli_error($db_con));
    $rwcopyLaststrg = mysqli_fetch_assoc($copyLaststrg);
    $slname = rtrim($rwcopyLaststrg['sl_name']);
    $updir = getStoragePath($db_con, $rwcopyLaststrg['sl_parent_id'], $rwcopyLaststrg['sl_depth_level']);

    if (!empty($updir)) {
        $updir = $updir . '/';
    } else {
        $updir = '';
    }

    $copyfromstrg = mysqli_query($db_con, "select sl_name from tbl_storage_level where sl_id = '$sl_id4'") or die('Error :' . mysqli_error($db_con));
    $rwcopyFromstrg = mysqli_fetch_assoc($copyfromstrg);
    $rowcount = mysqli_num_rows($fetchresult);

    $rowmultifield = mysqli_fetch_field($fetchresult);
    $olddocname = array();
    // Connect to file server
    $fileManager->conntFileServer();
    while ($rowmulticopy = mysqli_fetch_array($fetchresult)) {
        $doc_extn = $rowmulticopy['doc_extn'];
        $old_doc_name = $rowmulticopy['old_doc_name'];
        $olddocname[] = $rowmulticopy['old_doc_name'];
        $doc_path = "extract-here/" . $rowmulticopy['doc_path'];
        $uploaded_by = $rowmulticopy['uploaded_by'];
        $doc_size = $rowmulticopy['doc_size'];

        $doc_EncryptFile = explode('/', $doc_path);
        $doc_Encrypt_nm = end($doc_EncryptFile);
        //$dir_to = "extract-here/" . $rwcopyLaststrg['sl_name'];
        $dir_to = "extract-here/" . $updir . $slname;

        if (!is_dir($dir_to)) {
            mkdir($dir_to);
        }
        //die;
        //$dir = "extract-here/" . $rwcopyLaststrg['sl_name'];

        $doc_Path_copy_to = $dir_to . "/" . $doc_Encrypt_nm;
        $pathArray = explode('/', $doc_Path_copy_to);

        array_shift($pathArray);

        $db_copy_Path_to = implode('/', $pathArray);

        copy($doc_path, $doc_Path_copy_to);

        $uploadInToFTP = false;

        $fromdir = "extract-here/" . substr($rowmulticopy['doc_path'], 0, strrpos($rowmulticopy['doc_path'], "/"));

        if (!file_exists($doc_path)) {
            if (FTP_ENABLED) {
                if ($fileManager->downloadFile(FTP_FOLDER.'/' . ROOT_FTP_FOLDER . '/' . $rowmulticopy['doc_path'], $doc_path)) {
                    $destinationPath = $updir . $slname . '/' . $doc_Encrypt_nm;
                    $uploadfile = $fileManager->uploadFile($doc_path,ROOT_FTP_FOLDER . '/' . $destinationPath);

                    if ($uploadfile) {
                        $uploadInToFTP = true;
                        //unlink($doc_path);
                    }
                }
            } else {
                $uploadInToFTP = true;
            }
        } else {
            $destinationPath = $updir . $slname . '/' . $doc_Encrypt_nm;
            $uploadfile = $fileManager->uploadFile($doc_path,ROOT_FTP_FOLDER . '/' . $destinationPath, false);

            if ($uploadfile) {
                $uploadInToFTP = true;
                // unlink($doc_path);
            } else {
                $uploadInToFTP = true;
            }
        }

        if ($uploadInToFTP) {
            $checkdubDocument = mysqli_query($db_con, "select old_doc_name from tbl_document_master where doc_name='$to' and old_doc_name='$old_doc_name' and flag_multidelete='1'") or die('Error : ' . mysqli_error($db_con));
            if (mysqli_num_rows($checkdubDocument) < 1) {

                $sql2 = "INSERT INTO tbl_document_master SET";
                $sql2 .= " doc_name='$to',old_doc_name='$old_doc_name',doc_extn='$doc_extn',doc_path='$destinationPath',uploaded_by='$uploaded_by',doc_size='$doc_size',dateposted='$rowmulticopy[dateposted]',noofpages='$rowmulticopy[noofpages]', ftp_done='1'";
                while ($rwMeta = mysqli_fetch_assoc($meta)) {
                    $metan = mysqli_query($db_con, "select field_name from tbl_metadata_master where id='$rwMeta[metadata_id]'");
                    $rwMetan = mysqli_fetch_assoc($metan);
                    $field = $rwMetan['field_name'];
                    $value = $rowmulticopy[$field];
                    $sql2 .= ",`$field`='$value'";
                }
                $multicopyinsert = mysqli_query($db_con, $sql2) or die("Error copy" . mysqli_error($db_Con));

                $insert_id = mysqli_insert_id($db_con);
                $newdocname = base64_encode($insert_id);

                //create thumbnail
                if (CREATE_THUMBNAIL) {
                    copy('thumbnail/' . base64_encode($rowmulticopy['doc_id']) . '.jpg', 'thumbnail/' . $newdocname . '.jpg');
                }
                if ($multicopyinsert) {
                    // copy text file if exist.
                    $ftxtdir = $fromdir . '/TXT/' . $rowmulticopy['doc_id'] . '.txt';
                    if (file_exists($ftxtdir)) {

                        $txtnewpath = 'extract-here/' . $updir . $slname . '/TXT';
                        if (!is_dir($txtnewpath)) {
                            mkdir($txtnewpath, 0777, TRUE) or die(print_r(error_get_last()));
                        }
                        $totxtdir = $txtnewpath . '/' . $insert_id . '.txt';
                        copy($ftxtdir, $totxtdir);
                    }

                    $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `doc_id`,`action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,'$to', '$rowmulticopy[doc_id]','Storage document $old_doc_name copy to Storage $rwcopyLaststrg[sl_name].','$date',null,'$host','')") or die('Error DBHOST: ' . mysqli_error($db_con));
                    if ($log) {
                        $message = "yes";
                    }
                }
            } else {
                echo '<script>taskFailed("' . basename($_SERVER['REQUEST_URI'])  . '","' . $lang['uploaded_already'] . '");</script>';
            }
        } else {
            $message = "no";
        }
    }
    if ($message == "yes") {
        //for document alert to subscribe user
        $subdocId = $doc_ids;
        $filenamed = implode(',', $olddocname);
        $userId = array();
        $checksubs = mysqli_query($db_con, "SELECT * FROM tbl_document_subscriber WHERE subscribe_docid in($subdocId) and find_in_set('7',action_id)");
        while ($rwcheckSubs = mysqli_fetch_assoc($checksubs)) {
            $userId[] = $rwcheckSubs['subscriber_userid'];
        }
        $userIds = implode(',', $userId);
        $mailto = array();
        $k = 1;
        $touser = mysqli_query($db_con, "SELECT user_email_id,first_name FROM tbl_user_master WHERE user_id in($userIds)");
        while ($rwtouser = mysqli_fetch_assoc($touser)) {
            $mailto[$k]['user_email_id'] = $rwtouser['user_email_id'];
            $mailto[$k]['first_name'] = $rwtouser['first_name'];
            $k++;
        }
        $toslname = $rwcopyLaststrg['sl_name'];
        $fromslname = $rwcopyFromstrg['sl_name'];
        $fileaction = "$filenamed copied from $fromslname to $toslname storage.";
        require_once './mail.php';
        foreach ($mailto as $to) {
            $email = $to['user_email_id'];
            $name = $to['first_name'];
            $mailsent = mailsenttoDocumentSubscribeUsers($email, $filenamed, $projectName, $fileaction, $name);
        }
        mysqli_autocommit($db_con, TRUE);
        echo '<script>taskSuccess("' . basename($_SERVER['REQUEST_URI'])  . '","' . $lang['Doc_Cpy_Sfly'] . '");</script>';
    } else {
        echo '<script>taskFailed("' . basename($_SERVER['REQUEST_URI'])  . '","' . $lang['Document_not_copied'] . '");</script>';
    }

    mysqli_close($db_con);
}

//sent document in mail
if (isset($_POST['mailFiles'], $_POST['token'])) {
    $mailto = $_POST['mailto'];
    $tousers = implode(',', $mailto);
    $subject = xss_clean($_POST['subject']);
    $mailbody = xss_clean($_POST['mailbody']);
    $doc_ids = xss_clean($_POST['mailFile']);

    $storagemailFile = $_POST['storagemailFile'];

    //$slid = base64_decode(urldecode($_GET['id']));
    $doc_path = array();
    $docIds = explode(',', $doc_ids);

    $username = 'User';

    //for document alert to subscribe user
    $subdocId = $doc_ids;
    $userId = array();
    $checksubs = mysqli_query($db_con, "SELECT * FROM tbl_document_subscriber WHERE subscribe_docid in($subdocId) and find_in_set('8',action_id)");
    while ($rwcheckSubs = mysqli_fetch_assoc($checksubs)) {
        $userId[] = $rwcheckSubs['subscriber_userid'];
    }
    $userIds = implode(',', $userId);
    $emailto = array();
    $k = 1;
    $touser = mysqli_query($db_con, "SELECT user_email_id,first_name FROM tbl_user_master WHERE user_id in($userIds)"); // or die('Error453' . mysqli_error($db_con));
    while ($rwtouser = mysqli_fetch_assoc($touser)) {
        $emailto[$k]['user_email_id'] = $rwtouser['user_email_id'];
        $emailto[$k]['first_name'] = $rwtouser['first_name'];
        $k++;
    }
    $mailstrgeNm = mysqli_query($db_con, "select sl_name from tbl_storage_level where sl_id ='$storagemailFile'") or die('Error' . mysqli_error($db_con));
    $rwmailstrgeNm = mysqli_fetch_assoc($mailstrgeNm);
    $filenamed = array();
    $maildocname = mysqli_query($db_con, "SELECT old_doc_name, doc_name FROM tbl_document_master WHERE doc_id in($subdocId) and flag_multidelete='1'");
    while ($rwmaildocname = mysqli_fetch_assoc($maildocname)) {

        if (isFolderReadable($db_con, $rwmaildocname['doc_name'])) {

            $filenamed[] = $rwmaildocname['old_doc_name'];
        } else {

            echo '<script>taskFailed("' . basename($_SERVER['REQUEST_URI'])  . '","' . $lang['you_are_not_allowed_to_access_one_of_these_files'] . '");</script>';
            exit;
        }
    }
    $documentname = implode(',', $filenamed);
    $emailslname = $rwmailstrgeNm['sl_name'];
    $fileaction = "$documentname shared with $tousers through email from $emailslname storage.";
    require_once './mail.php';
    foreach ($emailto as $to) {
        $email = $to['user_email_id'];
        $name = $to['first_name'];
        if (MAIL_BY_SOCKET) {
            $paramsArray = array(
                'email' => $email,
                'filenamed' => $filenamed,
                'action' => 'filesubscribe',
                'projectName' => $projectName,
                'fileaction' => $fileaction,
                'name' => $name
            );
            mailBySocket($paramsArray);
        } else {
            $mailsent = mailsenttoDocumentSubscribeUsers($email, $filenamed, $projectName, $fileaction, $name);
        }
    }
    require_once './mail.php';
    foreach ($mailto as $to) {

        if (MAIL_BY_SOCKET) {

            $paramsArray = array(
                'subject' => $subject,
                'mailbody' => $mailbody,
                'action' => 'maildocumentoutside',
                'projectName' => $projectName,
                'username' => $username,
                'to' => $to,
                'docIds' => implode(",", $docIds)
            );
            $sent = mailBySocket($paramsArray);
        } else {
            $emailsent = mailDocuments($projectName, $subject, $mailbody, $username, $to, implode(",", $docIds));
        }
    }


    //if ($sent || $emailsent) {
    foreach ($mailto as $to) {
        $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`, `doc_id`,`action_name`, `start_date`,`system_ip`, `remarks`) values ('$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]', '$doc_ids','Document Mailed','$date','$host','Document mailed with ($to).')") or die('error : ' . mysqli_error($db_con));
        $flag = 1;
    }
    if ($flag == '1') {
        echo '<script>taskSuccess("' . $_SERVER['RESQUEST_URI'] . '","' . $lang['document_send'] . '");</script>';
    }
}
?>
<script src="https://www.google.com/jsapi" type="text/javascript"></script>

<script type="text/javascript">
    $(document).ready(function() {
        $('#extend').hide();
    });
    $('#extdate').click(function() {
        if ($("input[name='extenddate']:checked").val()) {
            $('#extend').show();
            $("#docsharetime").prop('required', true);
        } else {
            $('#extend').hide();
            $("#docsharetime").removeAttr('required', true);
        }
    });
    // Load the Google Transliterate API
    google.load("elements", "1", {
        packages: "transliteration"
    });

    function onLoad() {

        var langcode = '<?php echo $langDetail['lang_code']; ?>';

        var options = {
            sourceLanguage: 'en',
            destinationLanguage: [langcode],
            shortcutKey: 'ctrl+g',
            transliterationEnabled: true
        };
        // Create an instance on TransliterationControl with the required

        var control =
            new google.elements.transliteration.TransliterationControl(options);

        // Enable transliteration in the text fields with the given ids.
        var ids = ["subject", "mailbody"];
        control.makeTransliteratable(ids);

    }
    google.setOnLoadCallback(onLoad);
</script>

<script type="text/javascript" src="./assets/js/bootstrap-datetimepicker.js" charset="UTF-8"></script>
<script type="text/javascript" src="./assets/js/bootstrap-datetimepicker.fr.js" charset="UTF-8"></script>
<script type="text/javascript">
    $('.datetimepicker').datetimepicker({
        //language:  'fr',
        weekStart: 1,
        todayBtn: 1,
        autoclose: 1,
        todayHighlight: 1,
        startView: 2,
        forceParse: 0,
        showMeridian: 1,
    });
</script>
<script>
    var id = 1;

    function addMoreRows(Id) {
        id++;
        $("#addemailbox").append('<div class="row m-b-10" id="emailremove' + id + '"><div class="input-group"><input type="email" name="mailto[]" id="mailto" parsley-type="email" class="form-control emaillock" required="" placeholder="<?php echo $lang['Enter_Email_Id']; ?>"><span class="input-group-btn add-on"><a href="javascript:void(0);" class="btn btn-danger btn-md" onclick="removeLastRow(' + id + ');" title="Remove"><i class="fa fa-minus"></i></button></span></div></div>');
    }

    function removeLastRow(Id) {

        $('#emailremove' + Id).remove();
    }
</script>