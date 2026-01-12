<script>
$(".bit").keyup(function () {
        var bitVal = $(this).val();
        if (bitVal == 0 || bitVal == 1)
        {

          $(".nextBtn").removeAttr("disabled", "disabled");
          $("#errormsg").html("");
        } else {
          $(".nextBtn").attr("disabled", "disabled");
          $("#errormsg").html("Invalid Value!Value should be 0 or 1");
        }
    })
    $('.char').keyup(function ()
    {
        var GrpNme = $(this).val();
        re = /[`12345679890~!@#$%^&*()_|+\-=?;:'",.<>\{\}\[\]\\\/]/gi;
        var isSplChar = re.test(GrpNme);
        if (isSplChar)
        {
            var no_spl_char = GrpNme.replace(/[`~!@#$%^&*()_|0-9+\-=?;:'",.<>\{\}\[\]\\\/]/gi, '');
            $(this).val(no_spl_char);
        }
    });
    $('.char').bind(function () {
        $(this).val($(this).val().replace(/[<>]/g, ""))
    });
    
    $("input.intvl").keypress(function (e) {
        //if the letter is not digit then display error and don't type anything
        if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
            //display error message
            return false;
        }
        str = $(this).val();
        str = str.split(".").length - 1;
        if (str > 0 && e.which == 46) {
            return false;
        }
    });
    
    $('.varchar').keyup(function ()
    {
        var GrpNme = $(this).val();
        re = /[`~!@#$%^&*()_|+\-=?;:'",.<>\{\}\[\]\\\/]/gi;
        var isSplChar = re.test(GrpNme);
        if (isSplChar)
        {
            var no_spl_char = GrpNme.replace(/[`~!@#$%^&*()_|+\-=?;:'",.<>\{\}\[\]\\\/]/gi, '');
            $(this).val(no_spl_char);
        }
    });
    $('.varchar').bind(function () {
        $(this).val($(this).val().replace(/[<>]/g, ""))
    });
    
    
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

<script src="assets/plugins/sweetalert2/sweet-alert.init.js"></script>  
<script src="assets/plugins/sweetalert2/sweetalert2-new.js"></script>
<script src="https://cdn.polyfill.io/v2/polyfill.min.js"></script>