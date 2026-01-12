<!--display wait gif image after submit-->
<div style="display: none; background: rgba(0,0,0,0.5); width: 100%; z-index: 2000; position: fixed; top:0;" id="wait">;
    <img src="assets/images/proceed.gif" alt="load"  style=" margin-left: 48%; margin-top: 250px; width: 100px; height:100px; position: fixed; "/>
</div>  
<script>
    
    
    //for wait gif display after submit
    var heiht = $(document).height();
    //alert(heiht);
    $('#wait').css('height', heiht);
    $('#wfasign').submit(function () {
        if ($.trim($("#wfid").val()) != "") {
            $('#wait').show();
            //$('#wait').css('height',heiht);
            $('#assign-workflow').hide();
            return true;
        }
    });

</script>
<script>
    $("a#checkout").click(function () {
        var path = $(this).attr('data');
        // alert(id);

        $.post("application/ajax/checkout.php", {CHECKOUT: path}, function (result, status) {
            window.location.href = "<?php echo basename($_SERVER['PHP_SELF']) . '?' . $_SERVER['QUERY_STRING']; ?>";

        });
    });


    $("a#singlesubscribe").click(function () {
     var subDocId = $(this).attr('data');
      $("#singlesubsdocId").val(subDocId);
    });
	
	
	$("a#exportocr").click(function () {
     var DocId = $(this).attr('data');
      $("#exdocid").val(DocId);
    });
    
    $("a#editMdata").click(function () {
        var id = $(this).attr('data');
        // alert(id);

        $.post("application/ajax/checkin.php", {CHECKIN: id}, function (result, status) {

        });
    });
//    $("a#editRow").click(function () {
//        var id = $(this).attr('data');
//        // alert(id);
//
//        $.post("application/ajax/updateDocument.php", {ID: id}, function (result, status) {
//            if (status == 'success') {
//                $("#modalModify").html(result);
//                //alert(result);
//            }
//        });
//    });

    $("a#removeRow").click(function () {
        var id = $(this).attr('data');
        //alert(id);
        $("#uidd").val(id);
    });

    $("a#video").click(function () {
        alert(id);
        var id = $(this).attr('data');
        //alert(id);
        $.post("application/ajax/videoformat.php", {vid: id}, function (result, status) {
            if (status == 'success') {
                $("#videofor").html(result);
                //alert(result);

            }
        });
    });
    $("a#audio").click(function () {
        var id = $(this).attr('data');
        $.post("application/ajax/audioformat.php", {aid: id}, function (result, status) {
            if (status == 'success') {
                $("#foraudio").html(result);
                //alert(result);

            }
        });
    });
    $("a#moveToWf").click(function () {
        var id = $(this).attr('data');
        // alert(id);
        $("#mTowf").val(id);
    });

</script>
<script>
    $("a#editMdata").click(function () {

        var id = $(this).attr('data');
        var $row = $(this).closest('tr');
        var name = '';
        var values = [];
        values = $row.find('td:nth-child(2)').map(function () {
            var $this = $(this);
            if ($this.hasClass('actions')) {

            } else {
                name = $.trim($this.text());
            }
             
            $("#editmetadata .modal-title").html("<?php echo $lang['Updt_Mta_Data_of_File']; ?>: <strong>" + name + "</strong>");
            $.post("application/ajax/editMdataValue.php", {ID: id}, function (result, status) {
                if (status == 'success') {
                    $("#modalModifyMvalue").html(result);
                }
            });
        });
    });
    //view metadata in popup history
    function getFileHistory(doc_id, slid) {
        var token = $("input[name='token']").val();
        $.ajax({
            url: "application/ajax/fileHistory.php",
            //dataType:"json",
            type: "POST",
            data: {doc_id: doc_id, slid: slid, token:token},
            beforeSend: function ()
            {
                //$('#comment_btn').val('Wait...').prop('disabled', true);
            },
            success: function (result, status)
            {
                getToken();
                $("#history-modal-content").html(result);
            }
        });
    }

</script> 
<script>
    $("a#viewMeta").click(function () {
        if ($(this).find('i').hasClass('fa-eye')) {
            $(".metadata").css('display', 'none');
            $("a#viewMeta").find('i').removeClass('fa-eye');
            $("a#viewMeta").find('i').addClass('fa-eye');
            var mid = $(this).attr("data");
            $("#" + mid).css('display', 'block');
            $(this).find('i').removeClass('fa-eye');
            $(this).find('i').addClass('fa-eye')
        } else {
            $(".metadata").css('display', 'none');
            $("a#viewMeta").find('i').removeClass('fa-eye');
            $("a#viewMeta").find('i').addClass('fa-eye');
        }
    });
    $("input:checkbox").click(function () {
        var column = "table ." + $(this).attr("name");
        $(column).toggle();
    });
    //filter limit
    var url = window.location.href + "?";
    function removeParam(key, sourceURL) {
        sourceURL = String(sourceURL).replace("#/", "");
        var rtn = sourceURL.split("?")[0],
                param,
                params_arr = [],
                queryString = (sourceURL.indexOf("?") !== -1) ? sourceURL.split("?")[1] : "";
        if (queryString !== "") {
            params_arr = queryString.split("&");
            for (var i = params_arr.length - 1; i >= 0; i -= 1) {
                param = params_arr[i].split("=")[0];
                if (param === key) {
                    params_arr.splice(i, 1);
                }
            }
            rtn = rtn + "?" + params_arr.join("&");
        } else {
            rtn = rtn + '?';
        }
        return rtn;
    }
    jQuery(document).ready(function ($) {
        $("#limit").change(function () {
            lval = $(this).val();
            url = removeParam("limit", url);
            url = removeParam("token", url);
            url = url + "&limit=" + lval;
            window.open(url, "_parent");
        });
    });
    
    function getLinkedFiles(doc_id){
        
        var token = $("input[name='token']").val();
        $.ajax({
            url: "application/ajax/common.php",
            //dataType:"json",
            type: "POST",
            data: {doc_id: doc_id, token:token},
            beforeSend: function ()
            {
                //$('#comment_btn').val('Wait...').prop('disabled', true);
            },
            success: function (result, status)
            {
                getToken();
                $("#linkfilesdetail").html(result);
                
                $("a#checkout").click(function () {
                    var path = $(this).attr('data');
                    $.post("application/ajax/checkout.php", {CHECKOUT: path}, function (result, status) {
                        window.location.href = "<?php echo basename($_SERVER['PHP_SELF']) . '?' . $_SERVER['QUERY_STRING']; ?>";

                    });
                });
            }
        });
    }

	function getFileMetaData(docId, slId){
		
		$.post("application/ajax/common.php", {docId:docId, slId:slId, action:'getFileMetaData'}, function (result, status) {
				
			$("#filemetadata").html(result);

		});
	}

</script>