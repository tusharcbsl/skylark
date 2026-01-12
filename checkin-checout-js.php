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
</script>