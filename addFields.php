<!DOCTYPE html>
<html>
    <?php
    require_once './loginvalidate.php';
    //require_once './application/config/database.php';
    require_once './application/pages/head.php';
    
    if ($rwgetRole['add_metadata'] != '1') {
        header('Location: ./index');
    }
    $ses_val = $_SESSION;
    $langDetail = mysqli_query($db_con, "select * from tbl_language where lang_name='$_SESSION[lang]'") or die('Error : ' . mysqli_error($db_con));
    $langDetail = mysqli_fetch_assoc($langDetail);
    ?>
    <link href="assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
    <script src="https://www.google.com/jsapi" type="text/javascript">
    </script>  

    <script type="text/javascript">

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
            // options.
            var control =
                    new google.elements.transliteration.TransliterationControl(options);

            // Enable transliteration in the text fields with the given ids.
//            var ids = ["metaData", "langchange1","langchange2", "langchange3", "langchange4"];
//            control.makeTransliteratable(ids);
            var elements = document.getElementsByClassName('.langchange');
            control.makeTransliteratable(elements);

        }
        google.setOnLoadCallback(onLoad);

    </script> 
    <body class="fixed-left">
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
                                <li><a href="#"><?php echo $lang['Masters']; ?></a></li>
                                <li class="active"><?php echo $lang['Add_New_Field']; ?></li>
                                <li> <a href="#" data-toggle="modal" data-target="#help-modal" id="helpview" data="5" title="<?= $lang['help']; ?>"><i class="fa fa-question-circle"></i> </a></li>
                                <a href="javascript:void(0)" class="btn btn-primary waves-effect waves-light pull-right btn-sm margin-t-9" onclick="goPrevious();" title="<?php echo $lang['Go_to_previous_page']; ?>"><i class="fa fa-arrow-circle-left"></i></a>
                            </ol>
                        </div>
                        <div class="row">
                            <div class="box box-primary">
                                <div class="box-header with-border">
                                    <h4 class="header-title col-md-6"><?php echo $lang['Add_New_Field']; ?></h4>
                                </div>
                                <div class="box-body">
                                    <div class="col-sm-6">
                                        <div class="card-box">
                                            <div class="row">
                                                <form method="post">
                                                    <div class="errormsg form-group" style="display:none">
                                                        <span class="label label-danger" style="font-size: 12px;"><?php echo $lang['maximum_minimum_value']; ?></span>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label><?php echo $lang['Ent_Fld_Nm']; ?> <span style="color:red;">*</span></label>
                                                                <input type="text" id="metaData" class="form-control" name="fieldName" placeholder="<?php echo $lang['Ent_Fld_Nm']; ?>" required maxlength="30">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label><?php echo $lang['Select_DataType']; ?><span style="color:red;">*</span></label>
                                                                <select class="form-control select23" name="dataType" id="selection" onchange="changeplh()" required>
                                                                    <option selected disabled><?php echo $lang['Select']; ?></option>
                                                                    <option value="char"><?php echo $lang['Char']; ?></option>
                                                                    <option value="datetime"><?php echo $lang['datetime']; ?></option>
                                                                    <option value="Int"><?php echo $lang['Int']; ?></option>
                                                                    <option value="BigInt"><?php echo $lang['BigInt']; ?></option>
                                                                    <option value="float"><?php echo $lang['Float']; ?></option>
                                                                    <option value="double"><?php echo $lang['Double']; ?></option>
                                                                    <option value="varchar"><?php echo $lang['Varchar']; ?></option>
                                                                    <option value="range"><?php echo $lang['range']; ?></option>
                                                                    <option value="boolean"><?php echo $lang['binary']; ?></option>
                                                                    <option value="list"><?php echo $lang['list']; ?></option>
                                                                    <option value="checklist"><?php echo $lang['checklist']; ?></option>
                                                                    <option value="date"><?php echo $lang['Date']; ?></option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div id="list" style="display: none;">
                                                                <table  style="width:100%;" cellspacing="20" class="table table-bordered">
                                                                    <tr>
                                                                        <th> <label><strong><?= $lang['SNO']; ?></strong></label></th>
                                                                        <th> <label><strong><?= $lang['label']; ?></strong><span class="text-alert">*</span></label></th>
                                                                        <th><label><strong><?= $lang['value']; ?></strong><span class="text-alert">*</span></label></th>
                                                                        <th><label><strong><?= $lang['Add']; ?></strong></label></th>
                                                                    </tr>
                                                                    <tbody id="listdatatype">
                                                                        <tr>
                                                                            <td>1.</td>
                                                                            <td>
                                                                                <input type="text" name="label[]" class="form-control numspecialcharlock listtextbox langchange" id="langchange1" placeholder="<?= $lang['label']; ?>" maxlength="20">
                                                                            </td>
                                                                            <td>
                                                                                <input type="text" name="value[]" class="form-control numspecialcharlock listtextbox langchange" id="langchange2" placeholder="<?= $lang['value']; ?>" maxlength="20">
                                                                            </td>

                                                                            <td>
                                                                                <a href="javascript:void(0);" class="btn btn-primary btn-sm" onclick="addMorelistvalue();" title="<?= $lang['Add_more']; ?>"><i class="fa fa fa-plus"></i></a>
                                                                            </td>
                                                                        </tr>


                                                                    </tbody>

                                                                </table>
                                                            </div>
                                                            <div id="checklist" style="display: none;">
                                                                <table  style="width:100%;" cellspacing="20" class="table table-bordered">
                                                                    <tr>
                                                                        <th> <label><strong><?= $lang['SNO']; ?></strong></label></th>
                                                                        <th> <label><strong><?= $lang['label']; ?></strong><span class="text-alert">*</span></label></th>
                                                                        <th><label><strong><?= $lang['value']; ?></strong><span class="text-alert">*</span></label></th>
                                                                        <th><label><strong><?= $lang['Add']; ?></strong></label></th>
                                                                    </tr>
                                                                    <tbody id="checklistdatatype">
                                                                        <tr>
                                                                            <td>1.</td>
                                                                            <td>
                                                                                <input type="text" name="checkboxlabel[]" class="form-control numspecialcharlock checkboxlabel langchange" id="langchange3" placeholder="<?= $lang['label']; ?>" maxlength="50">
                                                                            </td>
                                                                            <td>
                                                                                <input type="text" name="checkboxvalue[]" class="form-control numspecialcharlock checkboxlabel langchange" id="langchange4" placeholder="<?= $lang['value']; ?>" maxlength="50">
                                                                            </td>

                                                                            <td>
                                                                                <a href="javascript:void(0);" class="btn btn-primary btn-sm" onclick="addMoreChecklistBoxtvalue();" title="<?= $lang['Add_more']; ?>"><i class="fa fa fa-plus"></i></a>
                                                                            </td>
                                                                        </tr>


                                                                    </tbody>

                                                                </table>
                                                            </div>
                                                            <div class="form-group" id="notrange">
                                                                <label><?php echo $lang['Enter_Data_Length']; ?> <span class="text-alert">*</span></label>
                                                                <input type="text" min="0" class="form-control" name="dataLength" id="textbox" value="" placeholder="<?php echo $lang['Enter_Data_Length']; ?>">
                                                            </div>
                                                            <div id="range" style="display: none;">
                                                                <div class="form-group">
                                                                    <label><?php echo $lang['enter_min_length']; ?> <span class="text-alert">*</span></label>
                                                                    <input type="text" min="1" class="form-control textboxminmax" name="dataLengthrange[]" id="textbox1" value="" onkeyup="checkMaximumMinimumRange('1')" placeholder="<?php echo $lang['enter_min_length']; ?>" maxlength="9">
                                                                </div>
                                                                <div class="form-group">
                                                                    <label><?php echo $lang['enter_max_length']; ?> <span class="text-alert">*</span></label>
                                                                    <input type="text" min="1" class="form-control textboxminmax" name="dataLengthrange[]" id="textbox2" value="" onkeyup="checkMaximumMinimumRange('1')" placeholder="<?php echo $lang['enter_max_length']; ?>" maxlength="9">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row"> 
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label><?php echo $lang['Is_Mandatory']; ?> ? <span style="color:red;">*</span></label>
                                                                <select name="mandatory" class="form-control select23" required>
                                                                    <option disabled selected><?php echo $lang['Select']; ?></option>
                                                                    <option value="Yes"><?php echo $lang['Yes']; ?></option>
                                                                    <option value="No"><?php echo $lang['No']; ?></option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row pull-right">
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <button class="btn btn-primary waves-effect waves-light" id="addrange" type="submit" name="addField"><?php echo $lang['Submit']; ?></button>
                                                                <a href="addFields" class="btn btn-warning waves-effect waves-light m-l-5"><?= $lang['Reset']; ?> </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div> 
                                    </div>
                                </div>				
                            </div>
                        </div> <!-- container -->
                    </div> <!-- content -->
                    <?php require_once './application/pages/footer.php'; ?>
                </div>
            </div>
            <!-- END wrapper -->
            <?php require_once './application/pages/footerForjs.php'; ?>

            <script src="assets/plugins/select2/js/select2.min.js" type="text/javascript"></script>
            <script type="text/javascript" src="assets/plugins/parsleyjs/parsley.min.js"></script>
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
                                                                        function addMoreChecklistBoxtvalue()
                                                                        {
                                                                            var count = $('#checklistdatatype').children('tr').length;
                                                                            count++;
                                                                            $('#checklistdatatype').append('<tr id="addchecklistRow' + count + '"><td>' + count + '.' + '</td><td> <input type="text" name="checkboxlabel[]" class="form-control numspecialcharlock checkboxlabel langchange" placeholder="<?= $lang['label']; ?>" maxlength="50" required></td><td> <input type="text" name="checkboxvalue[]" class="form-control checkboxlabel numspecialcharlock langchange" placeholder="<?php echo $lang['value']; ?>" maxlength="50" required></td><td><a href="javascript:void(0);" class="btn btn-danger btn-sm" onclick="removelistboxLastRow(' + count + ');" title="Remove" ><i class="fa fa fa-minus"></i></a></td></tr>');

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
            </script>
            <script type="text/javascript">
                function checkMaximumMinimumRange(Id) {
                    if (parseFloat($("#textbox2").val()) < parseFloat($("#textbox1").val()))
                    {
                        $(".errormsg").css("display", "block").css("color", "red");
                        $("#addrange").prop('disabled', true);
                    } else {
                        //var id = parseInt(Id) + parseInt(1);
                        //var a = $("#textbox1").val();
                        //alert(a);
                        //$("#textbox2").val(parseInt(a));
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
                $(document).ready(function () {
                    $('form').parsley();
                });
                $(".select23").select2();

                //for placeholder display
                function changeplh() {
                    //debugger;
                    var sel = document.getElementById("selection");
                    // var textbx = document.getElementById("textbox");
                    var indexe = sel.selectedIndex;
                    //alert(indexe);
                    if (indexe == 1) {
                        $("#range").hide();
                        $(".textboxminmax").removeAttr("required", true);
                        $(".textboxminmax").val("");
                        $("#notrange").show();
                        $("#list").hide();
                        $("#list").each(function () {
                            $(".listtextbox").removeAttr("required", true);
                        });
                        $("#checklist").hide();
                        $("#checklist").each(function () {
                            $(".checkboxlabel").removeAttr('required', true);
                        });
                        $("#textbox").attr("placeholder", "<?= $lang['Enter_max_characters']; ?>");
                        $("#textbox").attr("required", "required");
                        $("#textbox").val("");
                        $("#errormsg").html("");
                        document.getElementById("textbox").style.borderColor = "grey";
                        $("#textbox").keyup(function () {
                            var valu = $("#textbox").val();
                            console.log(valu);
                            if (valu <= 255)
                            {
                                $(".submit-btn").removeAttr("disabled");
                                $("#errormsg").html("");
                                document.getElementById("textbox").style.borderColor = "grey";
                            } else {
                                $(".submit-btn").attr("disabled", "disabled");
                                $("#errormsg").html("Value should be less or equal to 255");
                                document.getElementById("textbox").style.borderColor = "red";
                            }
                        })
                        $("#textbox").change(function () {
                            var valu = $("#textbox").val();
                            if (valu <= 255)
                            {
                                $(".submit-btn").removeAttr("disabled");
                                $("#errormsg").html("");
                                document.getElementById("textbox").style.borderColor = "grey";
                            } else {
                                $(".submit-btn").attr("disabled", "disabled");
                                $("#errormsg").html("Value should be less or equal to 255");
                                document.getElementById("textbox").style.borderColor = "red";
                            }
                        })
                    }
                    if (indexe == 2) {
                        $("#textbox").removeAttr('required');
                        $("#textbox").val("");
                        $("#list").hide();
                        $("#list").each(function () {
                            $(".listtextbox").removeAttr("required", true);
                        });
                        $("#checklist").hide();
                        $("#checklist").each(function () {
                            $(".checkboxlabel").removeAttr('required', true);
                        });
                    }
                    if (indexe == 3) {
                        $("#range").hide();
                        $(".textboxminmax").removeAttr("required", true);
                        $(".textboxminmax").val("");
                        $("#notrange").show();
                        $("#list").hide();
                        $("#list").each(function () {
                            $(".listtextbox").removeAttr("required", true);
                        });
                        $("#checklist").hide();
                        $("#checklist").each(function () {
                            $(".checkboxlabel").removeAttr('required', true);
                        });
                        $("#textbox").attr("placeholder", "<?= $lang['Enter_max_int_length']; ?>");
                        $("#textbox").attr("required", "required");
                        $(this).keyup(function () {
                            var valu = $("#textbox").val();
                            console.log(valu);
                            if (valu <= 255)
                            {
                                $(".submit-btn").removeAttr("disabled");
                                $("#errormsg").html("");
                                document.getElementById("textbox").style.borderColor = "grey";
                            } else {
                                $(".submit-btn").attr("disabled", "disabled");
                                $("#errormsg").html("Value should be less or equal to 9");
                                document.getElementById("textbox").style.borderColor = "red";
                            }
                        })
                        $(this).change(function () {
                            var valu = $("#textbox").val();
                            if (valu <= 255)
                            {
                                $(".submit-btn").removeAttr("disabled");
                                $("#errormsg").html("");
                                document.getElementById("textbox").style.borderColor = "grey";
                            } else {

                                $(".submit-btn").attr("disabled", "disabled");
                                $("#errormsg").html("Value should be less or equal to 9");
                                document.getElementById("textbox").style.borderColor = "red";
                            }
                        })
                    }

                    if (indexe == 4) {
                        $("#range").hide();
                        $(".textboxminmax").removeAttr("required", true);
                        $(".textboxminmax").val("");
                        $("#notrange").show();
                        $("#list").hide();
                        $("#list").each(function () {
                            $(".listtextbox").removeAttr("required", true);
                        });
                        $("#checklist").hide();
                        $("#checklist").each(function () {
                            $(".checkboxlabel").removeAttr('required', true);
                        });
                        $("#textbox").attr("placeholder", "<?= $lang['Enter_max_bigint_length']; ?>");
                        $("#textbox").attr("required", "required");
                        $(this).keyup(function () {
                            var valu = $("#textbox").val();
                            console.log(valu);
                            if (valu <= 255)
                            {
                                $(".submit-btn").removeAttr("disabled");
                                $("#errormsg").html("");
                                document.getElementById("textbox").style.borderColor = "grey";
                            } else {
                                $(".submit-btn").attr("disabled", "disabled");
                                $("#errormsg").html("Value should be less or equal to 255");
                                document.getElementById("textbox").style.borderColor = "red";
                            }
                        })
                        $(this).change(function () {
                            var valu = $("#textbox").val();
                            if (valu <= 255)
                            {

                                $(".submit-btn").removeAttr("disabled");
                                $("#errormsg").html("");
                                document.getElementById("textbox").style.borderColor = "grey";
                            } else {

                                $(".submit-btn").attr("disabled", "disabled");
                                $("#errormsg").html("Value should be less or equal to 255");
                                document.getElementById("textbox").style.borderColor = "red";
                            }
                        })
                    }
                    if (indexe == 5) {
                        $("#range").hide();
                        $(".textboxminmax").removeAttr("required", true);
                        $(".textboxminmax").val("");
                        $("#notrange").show();
                        $("#list").hide();
                        $("#list").each(function () {
                            $(".listtextbox").removeAttr("required", true);
                        });
                        $("#checklist").hide();
                        $("#checklist").each(function () {
                            $(".checkboxlabel").removeAttr('required', true);
                        });
                        $("#textbox").attr("placeholder", "<?= $lang['Enter_max_float_length']; ?>");
                        $("#textbox").attr("required", "required");
                        $(this).keyup(function () {
                            var valu = $("#textbox").val();
                            console.log(valu);
                            if (valu <= 255)
                            {

                                $(".submit-btn").removeAttr("disabled");
                                $("#errormsg").html("");
                                document.getElementById("textbox").style.borderColor = "grey";
                            } else {

                                $(".submit-btn").attr("disabled", "disabled");
                                $("#errormsg").html("Value should be less or equal to 255");
                                document.getElementById("textbox").style.borderColor = "red";
                            }
                        })
                        $(this).change(function () {
                            var valu = $("#textbox").val();
                            if (valu <= 255)
                            {
                                $(".submit-btn").removeAttr("disabled");
                                $("#errormsg").html("");
                                document.getElementById("textbox").style.borderColor = "grey";
                            } else {
                                $(".submit-btn").attr("disabled", "disabled");
                                $("#errormsg").html("Value should be less or equal to 255");
                                document.getElementById("textbox").style.borderColor = "red";
                            }
                        })
                    }

                    if (indexe == 6) {
                        $("#range").hide();
                        $(".textboxminmax").removeAttr("required", true);
                        $(".textboxminmax").val("");
                        $("#checklist").hide();
                        $("#checklist").each(function () {
                            $(".checkboxlabel").removeAttr('required', true);
                        });
                        $("#list").hide();
                        $("#list").each(function () {
                            $(".listtextbox").removeAttr("required", true);
                        });
                        $("#notrange").show();
                        $("#textbox").removeAttr('required');
                        $("#textbox").val("");
                    }
                    if (indexe == 7) {
                        $("#range").hide();
                        $(".textboxminmax").removeAttr("required", true);
                        $(".textboxminmax").val("");
                        $("#notrange").show();
                        $("#checklist").hide();
                        $("#checklist").each(function () {
                            $(".checkboxlabel").removeAttr('required', true);
                        });
                        $("#list").hide();
                        $("#list").each(function () {
                            $(".listtextbox").removeAttr("required", true);
                        });
                        $("#textbox").attr("placeholder", "<?= $lang['Enter_max_characters']; ?>");
                        $("#textbox").attr("required", "required");
                        $(this).keyup(function () {
                            var valu = $("#textbox").val();
                            console.log(valu);
                            if (valu <= 255)
                            {
                                $(".submit-btn").removeAttr("disabled");
                                $("#errormsg").html("");
                                document.getElementById("textbox").style.borderColor = "grey";
                            } else {

                                $(".submit-btn").attr("disabled", "disabled");
                                $("#errormsg").html("Value should be less or equal to 255");
                                document.getElementById("textbox").style.borderColor = "red";
                            }
                        })
                        $(this).change(function () {
                            var valu = $("#textbox").val();
                            if (valu <= 255)
                            {

                                $(".submit-btn").removeAttr("disabled");
                                $("#errormsg").html("");
                                document.getElementById("textbox").style.borderColor = "grey";
                            } else {

                                $(".submit-btn").attr("disabled", "disabled");
                                $("#errormsg").html("Value should be less or equal to 255");
                                document.getElementById("textbox").style.borderColor = "red";
                            }
                        })
                    }
                    if (indexe == 8) {
                        $("#range").show();
                        $("#notrange").hide();
                        $("#checklist").hide();
                        $("#checklist").each(function () {
                            $(".checkboxlabel").removeAttr('required', true);
                        });
                        $("#list").hide();
                        $("#list").each(function () {
                            $(".listtextbox").removeAttr("required", true);
                        });
                        $(".textboxminmax").attr("required", "required");
                        $("#textbox").removeAttr("required", true);
                    }
                    if (indexe == 9) {
                        $("#textbox").removeAttr('required');
                        $("#textbox").val("");
                        $("#range").hide();
                        $("#notrange").show();
                        $("#checklist").hide();
                        $("#checklist").each(function () {
                            $(".checkboxlabel").removeAttr('required', true);
                        });
                        $("#list").hide();
                        $("#list").each(function () {
                            $(".listtextbox").removeAttr("required", true);
                        });
                        $(".textboxminmax").removeAttr("required", "required");
                        $("#textbox").removeAttr("required", true);
                    }

                    if (indexe == 10) {
                        $("#range").hide();
                        $("#notrange").hide();
                        $("#checklist").hide();
                        $("#checklist").each(function () {
                            $(".checkboxlabel").removeAttr('required', true);
                        });
                        $("#list").show();
                        $("#list").each(function () {
                            $(".listtextbox").prop('required', true);
                        });
                        $(".textboxminmax").removeAttr("required", true);
                        $("#textbox").removeAttr("required", true);
                    }
                    if (indexe == 11) {
                        $("#range").hide();
                        $("#notrange").hide();
                        $("#list").hide();
                        $("#checklist").show();
                        $("#checklist").each(function () {
                            $(".checkboxlabel").prop('required', true);
                        });
                        $(".textboxminmax").removeAttr("required", true);
                        $("#textbox").removeAttr("required", true);
                    }
                    if (indexe == 12) {
                        $("#textbox").removeAttr('required');
                        $("#textbox").val("");
                        $("#list").hide();
                        $("#list").each(function () {
                            $(".listtextbox").removeAttr("required", true);
                        });
                        $("#checklist").hide();
                        $("#checklist").each(function () {
                            $(".checkboxlabel").removeAttr('required', true);
                        });
                    }
                    $(document).on('change', '#selection', function () {
                        $('#textbox').attr('disabled', $(this).val() == 'datetime' || $(this).val() == 'double' || $(this).val() == 'boolean' || $(this).val() == 'date');
                    });
                }


            </script>
            <script>

                $('#metaData').bind("keyup change", function ()
                {
                    var GrpNme = $(this).val();
                    re = /[`~!@#$%^&*()_|+\-=?;:'",<>\{\}\[\]\\\/0-9]/gi;
                    var isSplChar = re.test(GrpNme);
                    if (isSplChar)
                    {
                        var no_spl_char = GrpNme.replace(/[`~!@#$%^&*()_|+\-=?;:'",<>\{\}\[\]\\\/0-9]/gi, '');
                        $(this).val(no_spl_char);
                    }
                });
                $('#metaData').bind(function () {
                    $(this).val($(this).val().replace(/[<>]/g, ""))
                });

            </script>

            <?php
            if (isset($_POST['addField'], $_POST['token'])) {
                $fieldName = trim(strtolower($_POST['fieldName']));
                $fieldName = str_replace(" ", "_", $fieldName);
                $fieldName = preg_replace('/[^\w$\x{0080}-\x{FFFF}]+/u', "", $fieldName); //filter name
                $fieldName = trim($fieldName);
                $dataType = $_POST['dataType'];
                $dataType = preg_replace("/[^a-zA-Z]_/", "", $dataType); //filter name
                $dataType = mysqli_real_escape_string($db_con, $dataType);
                $dataLength = $_POST['dataLength'];
                if (!empty($dataLength) && $dataType != 'range') {
                    $dataLength = preg_replace("/[^0-9]/", "", $dataLength); //filter name
                    $dataLength = mysqli_real_escape_string($db_con, $dataLength);
                } else if ($dataType == 'range') {
                    $dataLength = implode(',', $_POST['dataLengthrange']);
                }
                if ($dataType == 'list' || $dataType == 'checklist') {
                    if ($dataType == 'list') {
                        $label = preg_replace('/[^\w$\x{0080}-\x{FFFF} ]+/u', "", $_POST['label']);
                        $label = implode(',', $label);
                        $value = preg_replace('/[^\w$\x{0080}-\x{FFFF} ]+/u', "", $_POST['value']);
                        $value = implode(',', $value);
                    } else {
                        $label = preg_replace('/[^\w$\x{0080}-\x{FFFF} ]+/u', "", $_POST['checkboxlabel']);
                        $label = implode(',', $label);
                        $value = preg_replace('/[^\w$\x{0080}-\x{FFFF} ]+/u', "", $_POST['checkboxvalue']);
                        $value = implode(',', $value);
                    }
                }

                $mandatory = filter_input(INPUT_POST, "mandatory");
                $mandatory = preg_replace("/[^a-zA-Z]/", "", $mandatory); //filter name
                $mandatory = mysqli_real_escape_string($db_con, $mandatory);

                $flag = 0;
                mysqli_set_charset($db_con, "utf8");
                $metaDataCheck = mysqli_query($db_con, "select * from tbl_metadata_master where field_name='$fieldName'")or die('error : ' . mysqli_error($db_con));
                if (mysqli_num_rows($metaDataCheck) < 1) {
                    $checkDoc = mysqli_query($db_con, "SHOW COLUMNS FROM tbl_document_master LIKE '$fieldName'");
                    if (mysqli_num_rows($checkDoc) < 1) { //if not
                        if ((($dataType == 'char') || ($dataType == 'varchar') AND ( $dataLength <= 255 )) || (( $dataType == 'BigInt') AND ( $dataLength <= 20 )) || (( $dataType == 'Int') AND ( $dataLength <= 9 )) || (($dataType == 'float') AND ( $dataLength <= 4 )) || ($dataType == 'datetime') || ($dataType == 'double') || ( $dataType == 'range') || ($dataType == 'boolean') || ($dataType == 'list') || ($dataType == 'checklist') || ($dataType == 'date')) {
                            mysqli_set_charset($db_con, "utf8");

                            // echo "insert into tbl_metadata_master (`field_name`, `data_type`, `length_data`, `mandatory`, `label`, `value`) values('$fieldName','$dataType','$dataLength','$mandatory','$label','$value')"; die;
                            $create = mysqli_query($db_con, "insert into tbl_metadata_master (`field_name`, `data_type`, `length_data`, `mandatory`, `label`, `value`) values('$fieldName','$dataType','$dataLength','$mandatory','$label','$value')") or die('Error' . mysqli_error($db_con));
                            $mid = mysqli_insert_id($db_con);

                            if ($dataType == 'datetime' || $dataType == 'BigInt' || $dataType == 'double' || $dataType == 'float' || $dataType == 'Int' || $dataType == 'range' || $dataType == 'list' || $dataType == 'checklist' || $dataType == 'date') {
                                $dataType = 'varchar';
                                $dataLength = 255;
                            }
                            if ($dataLength == '') {
                                if ($dataType == 'Int' || $dataType == 'BigInt' || $dataType == 'float' || $dataType == 'double' || $dataType == 'range') {
                                    $metaCreateDoc = mysqli_query($db_con, "ALTER TABLE tbl_document_master ADD `$fieldName` $dataType DEFAULT 0") or die('Error adding meta2 ' . mysqli_error($db_con));
                                } else {
                                    $metaCreateDoc = mysqli_query($db_con, "ALTER TABLE tbl_document_master ADD `$fieldName` $dataType NULL") or die('Error adding meta3 ' . mysqli_error($db_con));
                                }
                                if ($metaCreateDoc) {
                                    $flag = 1;
                                } else {
                                    $flag = 0;
                                    mysqli_set_charset($db_con, "utf8");
                                    $del = mysqli_query($db_con, "delete from tbl_metadata_master where id='$mid'");
                                }
                            } else {
                                if ($dataType == 'Int' || $dataType == 'BigInt' || $dataType == 'float' || $dataType == 'double' || $dataType == 'datetime' || $dataType == 'range' || $dataType == 'date') {
                                    $metaCreateDoc = mysqli_query($db_con, "ALTER TABLE tbl_document_master ADD `$fieldName` $dataType($dataLength) DEFAULT 0") or die('Error adding meta value ' . mysqli_error($db_con));
                                } else {
                                    $metaCreateDoc = mysqli_query($db_con, "ALTER TABLE tbl_document_master ADD `$fieldName` $dataType($dataLength) NULL") or die('Error adding meta value ' . mysqli_error($db_con));
                                }
                                if ($metaCreateDoc) {
                                    $flag = 1;
                                } else {
                                    $flag = 0;
                                    mysqli_set_charset($db_con, "utf8");
                                    $del = mysqli_query($db_con, "delete from tbl_metadata_master where id='$mid'");
                                }
                            }

                            if ($flag) {
                                mysqli_set_charset($db_con, "utf8");
                                $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`, `action_name`, `start_date`, `system_ip`, `remarks`) values ('$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]','Keyword Created','$date','$host','New keyword $fieldName Created.')") or die('error : ' . mysqli_error($db_con));
                                echo'<script>taskSuccess("addFields","' . $lang['Mtdta_crtd_sucesfly'] . '");</script>';
                            } else {
                                echo'<script>taskFailed("addFields","' . $lang['Fld_to_crte_mtadta'] . '");</script>';
                            }
                        } else {
                            echo'<script>taskFailed("addFields","' . $lang['Fld_Lngth_Excd'] . '");</script>';
                        }
                    } else {
                        echo'<script>taskFailed("addFields","' . $lang['mtadta_alrady_exst'] . '");</script>';
                    }
                } else {
                    echo'<script>taskFailed("addFields","' . $lang['mtadta_alrady_exst'] . '");</script>';
                }
                mysqli_close($db_con);
            }
            ?>

    </body>
</html>