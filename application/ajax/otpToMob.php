<?php
require_once '../../sessionstart.php';
if (!isset($_SESSION['cdes_user_id'])) {
    header("location:../../logout");
}
require './../config/database.php';

//for user role

$chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$_SESSION[cdes_user_id]', user_ids) > 0") or die('Error:' . mysqli_error($db_con));

$rwgetRole = mysqli_fetch_assoc($chekUsr);

// echo $rwgetRole['dashboard_mydms']; die;
if ($rwgetRole['create_user'] != '1') {
    header('Location: ../../index');
}

$mobno = $_POST['mobno'];



require_once('../pages/sendSms.php');

$string = '0123456789';
$string_shuffled = str_shuffle($string);
$otp = substr($string_shuffled, 0, 4);
$_SESSION['otpMob'] = $otp;
$msgOtp = 'Your OTP is : ' . $otp;
$sendMsgToMbl = smsgatewaycenter_com_Send($mobno, $msgOtp, $debug = false);
?><br />
<div class="form-group">
    <input type="text" name="OTP" parsley-trigger="change" data-parsley-type="number" data-parsley-minlength="4" data-parsley-maxlength="4" required placeholder="Enter OTP" class="form-control" id="otpmob" maxlength="4" onblur="checkOtp()">        
</div>
<script>

    function checkOtp() {
        var x = document.getElementById("otpmob");

        if (x.value == "<?php echo $_SESSION['otpMob']; ?>") {
            //$("#otpmob").css('color', 'green');
            $("#otpmob").css({'color': 'green', 'border-color': 'green'})
            $("#otpmob").val('<?php echo $_SESSION['otpMob']; ?>');


        } else {

            $("#otpmob").css({'color': 'red', 'border-color': 'red'})
            //$("#otpmob").css('color', 'red');
            //$("#otpmob").css('border-color', 'red');
            $("#otpmob").val('');
            $("#otpmob").attr('placeholder', 'Please Enter Right OTP').blur();

        }
    }
    $("input#otpmob").keypress(function (e) {
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
<?php echo $_SESSION['otpMob']; ?>
</script>