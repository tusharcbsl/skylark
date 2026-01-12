<?php
require_once './loginvalidate.php';
require_once './application/config/database.php';
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$inputFileType = $_POST['filetype'];
$inputFileName = $_POST['filename'];
$sheetnumber = $_POST['sheetnumber'];
/**  Create a new Reader of the type defined in $inputFileType  * */
$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
$spreadsheet = $reader->load("$inputFileName");

$writer = new \PhpOffice\PhpSpreadsheet\Writer\Html($spreadsheet);
$writer->setPreCalculateFormulas(TRUE);
$writer->setSheetIndex($sheetnumber);
?>
<style>

    html {
        font-family: Times New Roman;
        font-size: 9pt;
        background-color: white;
    }

</style>

<?php
echo $writer->generateStyles(TRUE); // do not write <style> and </style>
echo $writer->generateSheetData();
echo $writer->generateHTMLFooter();
?>

<script>
    var lastfocusinid = "", lastfocusouid = "", isitformula = 0, formula = ['SUM'], arrid = [], formatarray = [];
    $(document).ready(function () {


        /*
         * sheet changer js start
         */
        $(".sheetid").click(function () {
            var num = $(this).attr("data");
            $.post("viewerxlsx.php", {sheetnumber: num}, function (result, status) {
                if (status == 'success') {
                    $("#loadviewer").html(result);
                }
            });

        });

        /*
         * stop crtl+s
         */
        /*
         * end
         */



    });
    $("td").bind("focusin keypress", function () {
        $("td").removeClass("cellformatter");
        lastfocusinid = $(this).attr("id");
        $(this).addClass("cellformatter");
        validatedropdown();//validate d
        var currentdatavalidater = $(this).text();
        currentdatavalidater = currentdatavalidater.trim();
        var res = currentdatavalidater.substring(0, 1);
        if (res == "=")
        {
            isitformula = 1;
        }
    });

    $("td").focusout(function () {
        arrid = [];
        lastfocusouid = $(this).attr("id");
        $(this).removeClass("cellformatter");
    });
    jQuery(document).bind("keyup keydown", function (e) {
        if (e.ctrlKey && e.keyCode == 83) {
            $("#myModal").modal();
            return false;
        }
    });

    $(function () {


        var $k = 0;
        var lastid = "", first = "", last = "", data = "";
        var isMouseDown = false,
                isHighlighted;
        $("td")
                .mousedown(function (event) {
                    if (isitformula == 1)
                    {
                        if (event.ctrlKey)
                        {
                            var lastid = "", first = "", last = "", data = "";


                            lastid = $("#" + lastfocusinid).text();
                            var lastdata = lastid.split("(");
                            isMouseDown = true;
                            $(this).toggleClass("cellformatter");
                            isHighlighted = $(this).hasClass("cellformatter");
                            arrid[$k] = $(this).attr("data");
                            $k++;
                            var testString = arrid.filter(function (item) {
                                return item != ""
                            }).join(",");
                            arrid = testString.split(",");
                            var length = arrid.length;
                            first = arrid[0];
                            last = arrid[length - 1];
                            data = lastdata[0] + "(" + first + ":" + last + ")"

                            $("#" + lastfocusinid).text(data);


                            return false; // prevent text selection
                        } else {

                            arrid = [];//unset whole data
                        }
                    }

//                    console.log(arrid);
                })
                .mouseover(function (event) {

                    var lastid = "", first = "", last = "", data = "";
                    if (isitformula == 1)
                    {
                        if (event.ctrlKey)
                        {



                            lastid = $("#" + lastfocusinid).text();
                            var lastdata = lastid.split("(");
                            arrid[$k] = $(this).attr("data");
                            $k++;
                            var testString = arrid.filter(function (item) {
                                return item != ""
                            }).join(",");
                            arrid = testString.split(",");
                            var length = arrid.length;
                            first = arrid[0];
                            last = arrid[length - 1];
                            data = lastdata[0] + "(" + first + ":" + last + ")"

                            $("#" + lastfocusinid).text(data);
                            if (isMouseDown) {
                                $(this).toggleClass("cellformatter", isHighlighted);

                            }



                        } else {
                            arrid = [];//unset whole data
                        }
                    }

                })

        $(document)
                .mouseup(function () {
                    isMouseDown = false;
                });

    });
    /*
     * cell data picker Js ends
     */

    var sheetnum = "<?php echo $sheetnumber; ?>"
    var $i = 0;
    var sheetData = [];
    $("td").keyup(function (e) {
        if (isitformula == 1)
        {
            if (e.ctrlKey && e.keyCode == 13) {

                // Ctrl-Enter pressed
                formulaGenerator("SUM", arrid, lastfocusinid)
                arrid = [];
                isitformula = 0;
//                console.log("ok:".isitformula);

            }

        }
        var cellid = $(this).attr("data");
        var textdata = $(this).text();
        var isformula = $(this).attr("isformula");
        var formulapply = $(this).attr("formulaapply");
        sheetData[$i] = {
            cellid: cellid,
            textdata: textdata,
            isformula: isformula,
            formulaapply: formulapply

        };
        $i++;
//        console.log(sheetData);

    });

    function mu_filterArray(sheetData)
    {
        var reversed = sheetData.reverse();
        var dupes = [];
        var uniqueArray = [];
        $.each(reversed, function (index, entry) {
            if (!dupes[entry.cellid]) {
                dupes[entry.cellid] = true;
                uniqueArray.push(entry);
            }
        });
        return uniqueArray;
    }
    /*
     * 
     * @type Number
     * run ony 1 at a time
     */
    var indigator = 0;
    $("#save_mucurrent_sheet").click(function () {
        if (indigator == 0)
        {
            $("#loadviewer").empty();
            var filtersheetdata = mu_filterArray(sheetData);
            var heiht = $(document).height();
            $('#wait').css('height', heiht);
            $('#wait').show();
            $.post("MU_Helper.php", {sheetnumber: <?php echo $sheetnumber; ?>, cellInfo: filtersheetdata, filetype: "<?php echo $inputFileType; ?>", filename: "<?php echo $inputFileName; ?>", actiontype: "updatesheet"}, function (result, status) {
                if (status == 'success') {
                    var res = JSON.parse(result);
                    if (res.status == 1)
                    {
                        $('#myModal').modal('hide');
                        $.post("viewer.php", {sheetnumber: <?php echo $sheetnumber; ?>, filetype: "<?php echo $inputFileType; ?>", filename: "<?php echo $inputFileName; ?>"}, function (result, status) {
                            if (status == 'success') {
                                $("#loadviewer").html(result);
                                $('#wait').hide();
                                sheetData = [];
                            }
                        });
                    }
                }
            });
        }
        indigator++;
    });

    /*
     * fetch cell value
     */
    $("td").click(function () {
        var currentcell = $(this).attr("data");
        var currentcelltext = $(this).text();
        var currentcellformula = $(this).attr("formulaapply");
        $.post("MU_Helper.php", {sheetnumber: <?php echo $sheetnumber; ?>, filetype: "<?php echo $inputFileType; ?>", filename: "<?php echo $inputFileName; ?>", actiontype: "cell_value", cellInfo: currentcell}, function (result, status) {
            if (status == 'success') {
                if (currentcellformula == "")
                {
                    $("#f_bar").val(result);
                    $("#cellnumbershow").val(currentcell);
                } else {
                    $("#f_bar").val(currentcellformula);
                    $("#cellnumbershow").val(currentcell);
                }
            }
        });
    });


    $("#f_bar").focusin(function () {
        $("#" + lastfocusinid).addClass("cellformatter");//add last focust in class 

    });





    /*
     * end
     */

    /*
     * execute current formula
     */

    /*
     * 
     */

    function formulaGenerator(ftype, arr)
    {
        switch (ftype) {
            case "SUM":
                console.log(arr);
                $("#" + lastfocusinid).attr("formulaapply", $("#" + lastfocusinid).text().trim());
                $("#" + lastfocusinid).attr("isformula", "1");
                var counter = 0, i = 0;
                arr = unique(arr);
                for (i = 0; i < (arr.length); i++)
                {
                    counter += parseInt($("#" + arr[i]).text().trim());
                }
//                console.log(counter);
                $("#" + lastfocusinid).text(counter);
                return counter;
                // code block
                break;
            default:
                // code block
                return "Invalid"
        }
    }

    function unique(list) {
        var result = [];
        $.each(list, function (i, e) {
            if ($.inArray(e, result) == -1)
                result.push(e);
        });
        return result;
    }

    $(function () {


        var $kformat = 0;
        var isMouseDownFormat = false,
                isHighlightedFormat;
        $("td")
                .mousedown(function (event) {

                    if (event.ctrlKey && isitformula == 0 && event.which==1)
                    {
                        isMouseDownFormat = true;
                        $(this).toggleClass("cellformatter");
                        isHighlightedFormat = $(this).hasClass("cellformatter");
                        formatarray[$kformat] = $(this).attr("data");
                        $kformat++;
                        return false; // prevent text selection
                    }

                })
                .mouseover(function (event) {


                    if (event.ctrlKey && isitformula == 0  && event.which==1)
                    {

                        formatarray[$kformat] = $(this).attr("data");
                        $kformat++;
                        if (isMouseDownFormat) {
                            $(this).toggleClass("cellformatter", isHighlightedFormat);

                        }



                    }
                })

        $(document)
                .mouseup(function () {
                    isMouseDownFormat = false;
                });

    });

    $(".homeaction").click(function () {
        var currentaction = $(this).attr("data");
        switch (currentaction) {
            case "BOLD":
                $(this).attr("data", "UNBOLD");
                makebold(formatarray);
                // code block
                break;
            case "UNBOLD":
                $(this).attr("data", "BOLD");
                makeunbold(formatarray);
                // code block
                break;
            case "ITALIC":
                $(this).attr("data", "UNITALIC");
                makeitalic(formatarray);
                // code block
                break;
            case "UNITALIC":
                $(this).attr("data", "ITALIC");
                makeunitalic(formatarray);
                // code block
                break;
            default:
                // code block
                return "Invalid"
        }
    })

    /*
     * make me bold
     */
    function makebold(formatarray)
    {
        var uniquelist = unique(formatarray);
        for (var $i = 0; $i < uniquelist.length; $i++)
        {
            $("#" + uniquelist[$i]).addClass("cellformatter");
            $("#" + uniquelist[$i]).addClass("makemebold");
            $("#" + uniquelist[$i]).attr("bold", "1");
        }


    }
    /*
     * make me unbold
     */
    function makeunbold(formatarray)
    {
        var uniquelist = unique(formatarray);
        for (var $i = 0; $i < uniquelist.length; $i++)
        {
            $("#" + uniquelist[$i]).addClass("cellformatter");
            $("#" + uniquelist[$i]).removeClass("makemebold");
            $("#" + uniquelist[$i]).attr("bold", "0");
        }


    }
    /*
     * make me bold
     */
    function makeitalic(formatarray)
    {
        var uniquelist = unique(formatarray);
        for (var $i = 0; $i < uniquelist.length; $i++)
        {
            $("#" + uniquelist[$i]).addClass("cellformatter");
            $("#" + uniquelist[$i]).addClass("makritalic");
            $("#" + uniquelist[$i]).attr("italic", "1");
        }


    }
    /*
     * make me unbold
     */
    function makeunitalic(formatarray)
    {
        var uniquelist = unique(formatarray);
        for (var $i = 0; $i < uniquelist.length; $i++)
        {
            $("#" + uniquelist[$i]).addClass("cellformatter");
            $("#" + uniquelist[$i]).removeClass("makritalic");
            $("#" + uniquelist[$i]).attr("italic", "0");
        }


    }
    
    function validatedropdown()
    {
      var iamitalic=$("#" + lastfocusinid).attr("italic");  
      var iambold=$("#" + lastfocusinid).attr("bold");  
      if(iamitalic==1)
      {
          $(".italic").attr("data", "UNITALIC");
      }else{
          $(".italic").attr("data", "ITALIC");
      }
      
      if(iambold==1)
      {
          $(".bold").attr("data", "UNBOLD");
      }else{
          $(".bold").attr("data", "BOLD");
      }
    }
    function generateFilter() {
        var colCount = 0;
        $('tr:nth-child(1) td').each(function () {
            if ($(this).text() !== "") {
                colCount++;
                ;
            }
        });
        console.log(colCount);
    }


</script>
