<?php
require_once '../../sessionstart.php';
if (!isset($_SESSION['cdes_user_id'])) {
    header("location:../../logout");
}

require './../config/database.php';
//for user role
$langDetail = mysqli_query($db_con, "select * from tbl_language where lang_name='" . $_SESSION['lang'] . "'") or die('Error : ' . mysqli_error($db_con));
$langDetail = mysqli_fetch_assoc($langDetail);

$chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$_SESSION[cdes_user_id]', user_ids) > 0") or die('Error:' . mysqli_error($db_con));

$rwgetRole = mysqli_fetch_assoc($chekUsr);
$sameGroupIDs = array();
$group = mysqli_query($db_con, "select * from tbl_bridge_grp_to_um where find_in_set('$_SESSION[cdes_user_id]',user_ids)") or die('Error' . mysqli_error($db_con));
while ($rwGroup = mysqli_fetch_assoc($group)) {
    $sameGroupIDs[] = $rwGroup['user_ids'];
}
$sameGroupIDs = implode(',', $sameGroupIDs);
$sameGroupIDs = explode(',', $sameGroupIDs);
$sameGroupIDs = array_unique($sameGroupIDs);
sort($sameGroupIDs);
$sameGroupIDs = implode(',', $sameGroupIDs);

if ($rwgetRole['edit_holiday'] != '1') {
    header('Location: ../../index');
}
if (isset($_SESSION['lang'])){
     $file = "../../".$_SESSION['lang'].".json";
 } else {
     $file = "../../English.json";
 }
 //for user role
$data = file_get_contents($file);
 $lang = json_decode($data, true);

if(!isset($_POST['token'], $_POST['HID'])){
   echo "Unauthrized Access";  
 }
$id = preg_replace("/[^0-9 ]/", "", $_POST['HID']);
mysqli_set_charset($db_con, "utf8");
$holiday = mysqli_query($db_con, "select * from tbl_events_master where id='$id'");
$rwholiday = mysqli_fetch_assoc($holiday);

?>

<div class="row"> 
    <div class="col-md-12"> 
        <div class="form-group">
            <label><?= $lang['holiday_name']; ?> <span class="astrick">*</span></label>
            <input type="text" name="HoliName" autocomplete="off" required class="form-control translatetext" id="groupName" value="<?php echo $rwholiday['holiday_name']; ?>">
        </div>
    </div> 

</div> 
<div class="row"> 
    <div class="col-md-12"> 
        <div class="form-group">
            <label> <?= $lang['holiday_date']; ?> <span class="astrick">*</span></label>
            <div class="input-group">
                <input type="text" name="HoliDate" required class="form-control" id="datepicker" value="<?php echo $rwholiday['date']; ?>">
                <span class="input-group-addon bg-custom b-0 text-white"><i class="icon-calender"></i></span>
            </div>
        </div>
    </div> 
</div>                                                     

<input type="hidden" name="hid" value="<?php echo $rwholiday['id']; ?>">

<script type="text/javascript" src="assets/plugins/jquery-validation/js/jquery.validate.min.js"></script>
<script src="assets/plugins/bootstrap-filestyle/js/bootstrap-filestyle.min.js" type="text/javascript"></script>

<script type="text/javascript" src="assets/plugins/parsleyjs/parsley.min.js"></script>
<link href="assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css" rel="stylesheet">
<script src="assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
<script>
// Get the modal for apply leave
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
            $('#datepicker').datepicker({
                format: "yyyy-mm-dd",
                startDate: output
            });
        });
</script>
<script type="text/javascript">
    $(document).ready(function () {
        $('form').parsley();

    });

    //for avoid special charecter
    $('#groupName').keyup(function ()
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

</script>

<script type="text/javascript">
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

            var control =
                    new google.elements.transliteration.TransliterationControl(options);
            //var ids = ["groupName12"];
            var elements = document.getElementsByClassName('translatetext');
            control.makeTransliteratable(elements);
        }
        $.getScript('assets/js/test.js', function () {
            // Call custom function defined in script
            onLoad();
        });
        google.setOnLoadCallback(onLoad);
    </script>
