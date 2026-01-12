<?php
require_once '../../sessionstart.php';
if (!isset($_SESSION['cdes_user_id'])) {
    header("location:../../logout.php");
}
if (isset($_SESSION['lang'])) {
    $file = "../../" . $_SESSION['lang'] . ".json";
} else {
    $file = "../../English.json";
}
$data = file_get_contents($file);
$lang = json_decode($data, true);
require './../config/database.php';

//for user role
$langDetail = mysqli_query($db_con, "select * from tbl_language where lang_name='" . $_SESSION['lang'] . "'") or die('Error : ' . mysqli_error($db_con));
$langDetail = mysqli_fetch_assoc($langDetail);

$chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$_SESSION[cdes_user_id]', user_ids) > 0") or die('Error:' . mysqli_error($db_con));

$rwgetRole = mysqli_fetch_assoc($chekUsr);

// echo $rwgetRole['dashboard_mydms']; die;
if ($rwgetRole['workflow_step'] != '1') {
    header('Location: ../../index');
}
if (!isset($_POST['ID'], $_POST['token'])) {
    echo "Unauthorised access !";
    exit;
}

if (intval($_POST['ID'])) {
    $id = preg_replace("/[^0-9 ]/", "", $_POST['ID']);
    mysqli_set_charset($db_con, "utf8");
    $getStep = mysqli_query($db_con, "select * from tbl_step_master where step_id='$id'") or die('Error in stepfetch:' . mysqli_error($db_con));
    $rwgetStep = mysqli_fetch_assoc($getStep);
    ?>
    <input type="hidden" name="stepid" value="<?php echo $id; ?>">
    <div class="form-group row">
        <div class="col-md-3">
            <label for=""><?php echo $lang['Stp_Nme']; ?>:<span style="color: red;">*</span></label>
        </div>
        <div class="col-md-9">
            <input type="text" class="form-control translatetext" name="workflowStep" id="workflowStep" value="<?php echo $rwgetStep[step_name]; ?>" maxlength="40" required>
        </div>
    </div>

    <div class="form-group row">
        <div class="col-md-3">
            <label for="userName"><?php echo $lang['Stp_Ordr']; ?>:<span style="color: red;">*</span></label>
        </div>
        <div class="col-md-9">

            <input type="number" class="form-control" name="workStepOrd" value="<?php echo $rwgetStep[step_order]; ?>" readonly/>                                          
        </div>
    </div>

    <div class="form-group row">
        <div class="col-md-3">
            <label for="userName"><?php echo $lang['Des']; ?>:</label>
        </div>
        <div class="col-md-9">

            <textarea class="form-control translatetext" rows="5" name="workStepDesc" id="workStepDesc"><?php echo $rwgetStep[step_description]; ?></textarea>

        </div>
    </div>


    <script type="text/javascript">
        $(document).ready(function(){
            
            $('input, textarea').keyup(function ()
            {
                var groupName = $(this).val();
                re = /[`1234567890~!@#$%^&*()|+\=?;:'",.<>\{\}\[\]\\\/]/gi;
                var isSplChar = re.test(groupName);
                if (isSplChar)
                {
                    var no_spl_char = groupName.replace(/[`~!@#$%^&*()|+\=?;:'",.<>\{\}\[\]\\\/]/gi, '');
                    $(this).val(no_spl_char);
                }
            });
            
        });
        
          

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
<?php } ?>