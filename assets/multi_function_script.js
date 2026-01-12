
$(document).ready(function () {

    $('#select_all').on('click', function (e) {
        if ($(this).is(':checked', true)) {
            $(".emp_checkbox").prop('checked', true);
        } else {
            $(".emp_checkbox").prop('checked', false);
        }
// set all checked checkbox count
        $("#select_count").html($("input.emp_checkbox:checked").length + " ");
    });
    $(".emp_checkbox").on('click',function(){
        var all = $('.emp_checkbox');
        //get all checkbox count
        var allLen = all.length;
        //get check checkbox count
        var filterLen = all.filter(':checked').length;
        if (allLen == filterLen) {
            $("#select_all").prop("checked", true);
        } else {
            $("#select_all").prop("checked", false);
        }
    });
// set particular checked checkbox count
    $(".emp_checkbox").on('click', function (e) {
        $("#select_count").html($("input.emp_checkbox:checked").length + " ");
    });
    // delete selected records
    $('#del_file').on('click', function (e) {
        var storage_id = $('#sty').val();
        var file = [];
        $(".emp_checkbox:checked").each(function () {
            file.push($(this).data('doc-id'));
        });
        if (file.length <= 0) {
            //var text3 = "";
            //$("#errmessage").val(text3);
            $("#errmessage").show();
            $("#hide").hide();
            $("#delyes").hide();
            $("#delno").hide();
            $("#confirm").hide();
            $("#hid").show();

        } else {
            $("#errmessage").hide();
            $("#hide").show();
            $("#delyes").show();
            $("#delno").show();
            $("#confirm").show();
            $("#hid").hide();

            var selected_values = file.join(",");
            $("#reDel").val(selected_values);
            $("#sl_id1").val(storage_id);

        }
    });
    // for export multi selected files Csv
    $('#export4').on('click', function (e) {
        //var slid = $('#slid').val();

        var file = [];
        $(".emp_checkbox:checked").each(function () {
            file.push($(this).data('doc-id'));
        });
        if (file.length <= 0) {
            $("#export_unselected").show();
            $("#export_selected").hide();
            $("#unexport").show();
            $("#unexportitle").show();
            $("#close").show();
            $("#export_title").hide();
            $("#hidexp").hide();
        } else {
            $("#close").hide();
            $("#export_unselected").hide();
            $("#export_selected").show();
            $("#unexport").hide();
            $("#unexportitle").hide();
            $("#export_title").show();
            $("#hidexp").show();
            var selected_values = file.join(",");
            $('#export_doc_ids').val(selected_values);
            //window.location.href = "checkexportfile?doc_id=" + selected_values + "&&slid=" + slid;
        }
    });

    //copy multi selected files in storage

    $("button#copyFiles").click(function () {
        var slid = $('#slid').val();
        var storage_id = $('#sty').val();
        var file = [];
        $(".emp_checkbox:checked").each(function () {
            file.push($(this).data('doc-id'));
        });
        if (file.length <= 0) {
            $("#unselected1").show();
            $("#selected1").hide();
            $("#ctitle").hide();
        } else {
            $("#unselected1").hide();
            $("#selected1").show();
            $("#cop").hide();
            $("#ctitle").show();

            var selected_values = file.join(",");
            $('#doc_ids').val(selected_values);
            $('#sl_id4').val(slid);
        }

    });

    //move multi selected files in storage 

    $("#move_multi").click(function () {
        var slid = $('#slid').val();
        var storage_id = $('#sty').val();
        var file = [];
        $(".emp_checkbox:checked").each(function () {
            file.push($(this).data('doc-id'));
        });
        if (file.length <= 0) {
            $("#unselected").show();
            $("#selected").hide();

        } else {
            var selected_values = file.join(",");
            //alert(selected_values);
            $('#doc_id_smove_multi').val(selected_values);
            $('#sl_id_move_multi').val(slid);
            $("#unselected").hide();
            $("#selected").show();
            $("#mov").show();
            $("#unseMove").hide();

        }



    });

    //for share multi selected files
    $('#shareFiles').on('click', function (e) {

        var file = [];
        $(".emp_checkbox:checked").each(function () {
            file.push($(this).data('doc-id'));
        });
        if (file.length <= 0) {
            $("#unseshare").show();
            $("#selected2").hide();
            $("#shr").show();
            $("#stitle").hide();
        } else {
            $("#unseshare").hide();
            $("#selected2").show();
            $("#shr").hide();
            $("#selected2").show();
            $("#stitle").show();

            var selected_values = file.join(",");
            $('#share_docids').val(selected_values);


        }
    });


    //for share multi selected files
    $('#shareFiles').on('click', function (e) {

        var file = [];
        $(".emp_checkbox:checked").each(function () {
            file.push($(this).data('doc-id'));
        });
        if (file.length <= 0) {
            $("#unseshare").show();
            $("#selected2").hide();
            $("#shr").show();
            $("#stitle").hide();
        } else {
            $("#unseshare").hide();
            $("#selected2").show();
            $("#shr").hide();
            $("#selected2").show();
            $("#stitle").show();

            var selected_values = file.join(",");
            $('#share_docids').val(selected_values);


        }
    });
    //multi file delete from rcyle bin 
    $('#del_file_recylebin').on('click', function (e) {

        var file = [];
        $(".emp_checkbox:checked").each(function () {
            file.push($(this).data('doc-id'));
        });

        if (file.length <= 0) {
            //var text3 = "";
            //$("#errmessage").val(text3);
            $("#recycle_errmessage").show();
            $("#recycle_hide").hide();

            $("#recycle_confirm").hide();
            $("#hid").show();
            $("#mulDel").hide();
        } else {

            $("#recycle_errmessage").hide();
            $("#recycle_hide").show();

            $("#recycle_confirm").show();
            $("#hid").hide();
            $("#mulDel").show();
            var selected_values = file.join(",");

            $("#reDel_recyle").val(selected_values);


        }
    });

    //for Recycle multi-selected files 
    $('#multi_restore_files').on('click', function (e) {
        var file = [];
        $(".emp_checkbox:checked").each(function () {
            file.push($(this).data('doc-id'));
        });

        if (file.length <= 0) {
            //var text3 = "";
            //$("#errmessage").val(text3);
            $("#multi_restore_errmessage").show();
            $("#multi_restore_hide").hide();
            $("#titlehid").show();
            $("#hiddel").hide();
            $("#multi_restore_confirm").hide();


        } else {

            $("#multi_restore_errmessage").hide();
            $("#multi_restore_hide").show();

            $("#multi_restore_confirm").show();
            $("#titlehid").hide();
            $("#hiddel").show();
            var selected_values = file.join(",");

            $("#reDel_multi_restore").val(selected_values);


        }
    });
    //for multi delete audit history
    $('#del_mul_histry').on('click', function (e) {
       //alert("Hello");
        var file = [];
        $(".emp_checkbox:checked").each(function () {
            file.push($(this).data('doc-id'));
        });

        if (file.length <= 0) {
            //var text3 = "";
            //$("#errmessage").val(text3);
            $("#multi_audit_errmessage").show();
            $("#multi_audit_hide").hide();
            $("#titleAudit").show();
            $("#hiddelHis").hide();
            $("#multi_Audit_confirm").hide();


        } else {

            $("#multi_audit_errmessage").hide();
            $("#multi_audit_hide").show();

            $("#multi_Audit_confirm").show();
            $("#titleAudit").hide();
            $("#hiddelHis").show();
            var selected_values = file.join(",");

            $("#Del_multi_Audit").val(selected_values);


        }
    });
	
	$('#mailFiles').on('click', function (e) {

        var file = [];
        $(".emp_checkbox:checked").each(function () {
            file.push($(this).data('doc-id'));
        });
        if (file.length <= 0) {
            $("#unmail").show();
            $("#selected3").hide();
            $("#mailf").show();
            $("#mtitle").hide();
        } else {
            $("#unmail").hide();
            $("#selected3").show();
            $("#mailf").hide();
            $("#mtitle").show();

            var selected_values = file.join(",");
            $('#mail_docids').val(selected_values);


        }
    });
	
	 $('#downloadcheckedfile').on('click', function (e) {

        var file = [];
        $(".emp_checkbox:checked").each(function () {
            file.push($(this).data('doc-id'));
        });
        if (file.length <= 0) {
            $("#unselectfile").show();
            $("#filedownload").hide();
            $("#download1").show();
            $("#download2").hide();
        } else {
            $("#unselectfile").hide();
            $("#filedownload").show();
            $("#download1").hide();
            $("#download2").show();

            var selected_values = file.join(",");
            $('#totaldocId').val(selected_values);


        }
    });

    $('#subscribecheckedfile').on('click', function (e) {

        var file = [];
        $(".emp_checkbox:checked").each(function () {
            file.push($(this).data('doc-id'));
        });
        if (file.length <= 0) {
            $("#unsubcribefile").show();
            $("#filesubcrbre").hide();
            $("#subscribe1").show();
            $("#subscribe2").hide();
        } else {
            $("#unsubcribefile").hide();
            $("#filesubcrbre").show();
            $("#subscribe1").hide();
            $("#subscribe2").show();

            var selected_values = file.join(",");
            $('#totalsubsdocId').val(selected_values);


        }
    });


 $('#chngeuserIds').on('click', function (e) {

        var file = [];
        $(".emp_checkbox:checked").each(function () {
            file.push($(this).data('doc-id'));
        });
        if (file.length <= 0) {
            $("#unselectuser").show();
            $("#selectuser").hide();
            $("#actionbtn").hide();
            $("#warning").show();
            $("#warn").hide();
        } else {
             $("#selectuser").show();
              $("#unselectuser").hide();
            $("#actionbtn").show();
            $("#warning").hide();
            $("#warn").show();

            var selected_values = file.join(",");
            $('#selectuserIds').val(selected_values);


        }
    });
});