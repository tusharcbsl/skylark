<div class="col-md-6">
    <label>CO(Compansatory OFF)</label>
    <div class="input-group">
        <input type="text" value=""  class="form-control datepicker" placeholder="Enter CO Date" name="CO" required onchange="checkCODate(this.value)">
        <span class="input-group-addon bg-custom b-0 text-white"><i class="icon-calender"></i></span>
    </div>
    <span id="result" style="float:right"></span>
</div>

<link href="assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css" rel="stylesheet">
<script src="assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
<script>
            $(document).ready(function () {
                var d1 = new Date();
                d1 = d1.setDate(d1.getDate() - 30);
                var d = new Date(d1);
                var month = d.getMonth() + 1;
                var day = d.getDate();
                var output = d.getFullYear() + '-' +
                        (('' + month).length < 2 ? '0' : '') + month + '-' +
                        (('' + day).length < 2 ? '0' : '') + day;
                //alert(output);
                $('.datepicker').datepicker({
                    format: "dd-mm-yyyy",
                    startDate: output
                });
            });
</script>
