<?php
require_once '../../sessionstart.php';
if (!isset($_SESSION['cdes_user_id'])) {
    header("location:../../logout");
}

require_once '../config/database.php';
//for user role
$id = $_POST['id'];
$qry = mysqli_query($db_con, "select * from tbl_client_master where client_id='$id'");
$rowsz = mysqli_fetch_assoc($qry);
?>
<div class="row">
    <div class="row col-md-12 m-t-10">

        <div class="form-group col-md-6">
            <label for="pass1">Number User<span style="color:red;">*</span></label>
            <input  name="nouser" type="number"  placeholder="Number User" required class="form-control" value="<?= $rowsz['total_user'] ?>" >
        </div>
        <div class="form-group col-md-6">
            <label for="pass1">Total Memory<span style="color:red;">*</span></label>
            <input  name="tomemory" type="number"  placeholder="Total Memory" required class="form-control" value="<?= $rowsz['total_memory'] ?>" >
        </div>

    </div>
    <div class="row col-md-12 m-t-10">
        <div class="form-group">
            <label for="privilege">Validity Over Date<span style="color:red;"></span></label>
            <input type="text" readonly=""  class="form-control" name="enddate" value="<?= date("Y-m-d", $rowsz['valid_upto']) ?>">
            <label for="privilege">Update Validity</label>
            <input type="checkbox" name="updatevalidi" id="myCheck" value="">
        </div>
    </div>
    <div class="row m-t-10" id="validi" style="display: none">
        <div class="col-md-12">
            <label for="privilege">Select Validity<span style="color:red;">*</span></label>
            <div class="form-group">

                <div class="col-md-6">
                    <select class="form-control " name="validupto_month"  data-placeholder="Select Month"  parsley-trigger="change" id="month" >

                        <option value="">Select Month</option>  
                        <option value="1 month">1 Month</option>  
                        <option value="2 month">2 Month</option>  
                        <option value="3 month">3 Month</option>  
                        <option value="4 month">4 Month</option>  
                        <option value="5 month">5 Month</option>  
                        <option value="6 month">6 Month</option> 
                        <option value="7 month">7 Month</option> 
                        <option value="8 month">8 Month</option> 
                        <option value="9 month">9 Month</option> 
                        <option value="10 month">10 Month</option>
                        <option value="11 month">11 Month</option> 
                    </select>
                </div>
                <div class="col-md-6">
                    <select class="form-control" name="validupto_year"  data-placeholder="Select Year"  parsley-trigger="change" id="year" >

                        <option value="">Select Year</option> 
                        <option value="1 Year">1 Year</option>  
                        <option value="2 Year">2 Year</option>  
                    </select>
                </div>
            </div>
        </div>
    </div>
    <div class="row  col-md-12 m-t-10">
        <div class="form-group">
            <label for="privilege">Select Product Type<span style="color:red;">*</span></label>
            <select class="select2" name="product_type"  data-placeholder="Select Product Plan"  parsley-trigger="change" id="group" required="required">
                <option value="">Select Product Type</option>   
                <?php
                $qry = mysqli_query($db_con, "select roleids  from tbl_bridge_grp_to_um where group_id='1'") or die(mysqli_error($db_con));
                $roleids = mysqli_fetch_assoc($qry);

                $role = mysqli_query($db_con, "select user_role,role_id from  tbl_user_roles where role_id IN($roleids[roleids])")or die(mysqli_error($db_con));
                while ($rows = mysqli_fetch_assoc($role)) {
                    if ($rowsz['product_type'] == $rows['role_id']) {
                        ?>
                        <option value="<?= $rows['role_id'] ?>" selected><?= $rows['user_role'] ?></option>   
                    <?php } else { ?>
                        <option value="<?= $rows['role_id'] ?>"><?= $rows['user_role'] ?></option>  
                    <?php }
                }
                ?>
            </select>
        </div>
    </div>

    <input type="hidden" value="<?= $id ?>" name="pid">
</div>

<script src="assets/js/jquery.min.js"></script>
<script type="text/javascript" src="assets/plugins/jquery-validation/js/jquery.validate.min.js"></script>
<script src="assets/plugins/bootstrap-filestyle/js/bootstrap-filestyle.min.js" type="text/javascript"></script>
<script src="assets/plugins/select2/js/select2.min.js" type="text/javascript"></script>

<script type="text/javascript" src="assets/plugins/parsleyjs/parsley.min.js"></script>

<script type="text/javascript">
    $(document).ready(function () {

        $(".select2").select2();
    });
    $("#month").change(function () {
        var valu = $(this).val();
        if (valu != "")
        {
            $("#year").removeAttr("required");
        } else {
            $("#year").attr("required", "required");
        }
    })
    $("#year").change(function () {
        var valu = $(this).val();
        if (valu != "")
        {
            $("#month").removeAttr("required");
        } else {
            $("#month").attr("required", "required");
        }
    })
    $("#myCheck").click(function () {
        if (document.getElementById("myCheck").checked)
        {
            $("#month").attr("required", "required");
            $("#year").attr("required", "required");
            //           alert("run");
            $("#validi").show();
        } else {
            //alert("run");
            $("#validi").hide();
        }
    })

</script>