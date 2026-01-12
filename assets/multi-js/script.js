
var files;
var fileindex = 0;
function selectFiles(Id) {
    
//$("#uploadImage").on("change", function (e) {
    files = $("#uploadImage" + Id)[0].files;
    //$("#filename").html("");
    $(".rmvId" + Id).remove();
    $(".removeList" + Id).remove();
    $.each(files, function (index, value) {

        $("#filename").append("<li data-index='" + fileindex + "' class='rmvId" + Id + "'>" + value.name + " <i class='fa fa-times-circle-o'></i> </li>");
        fileindex += 1;
    });
//});
    //checkRemainingMemory(Id);
    
    showDocumentList(Id);
}
$(document).on("click", "#filename li", function () {
    var newFiles = {};
    var index = $(this).index();

    var classId = $(this).attr("class");
    classId = classId.replace("rmvId", "");

    //alert(classId);

    $("#filename li:nth-child(" + (index + 1) + ")").remove();

    var removeId = $(this).attr("data-index");
    $("#previewrow" + removeId).remove();
    $("#filename li").each(function (index, element) {
        var dataIndex = $(element).attr("data-index");
        newFiles[index] = files[dataIndex];
    });
    var finalFile = $("#remainingFile" + classId).val();
    if (finalFile == "") {
        finalFile = removeId;
    } else {
        finalFile = finalFile + "," + removeId;
    }
    $("#remainingFile" + classId).val(finalFile);
    //console.log(newFiles);
});


//$(".dropzone").change(function () {
//    // readFile(this);
//});
//
//$('.dropzone-wrapper').on('dragover', function (e) {
//    e.preventDefault();
//    e.stopPropagation();
//    $(this).addClass('dragover');
//});
//
//$('.dropzone-wrapper').on('dragleave', function (e) {
//    e.preventDefault();
//    e.stopPropagation();
//    $(this).removeClass('dragover');
//});
