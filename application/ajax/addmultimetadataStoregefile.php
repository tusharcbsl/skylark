<?php
require_once '../../sessionstart.php';
if (!isset($_SESSION['cdes_user_id'])) {
    header("location:../../logout.php");
}
require './../config/database.php';
if (isset($_SESSION['lang'])) {
    $file = "../../" . $_SESSION['lang'] . ".json";
} else {
    $file = "../../English.json";
}
//for user role
$data = file_get_contents($file);
$lang = json_decode($data, true);
//for user role
$slid = $_REQUEST['id'];
$page = $_REQUEST['page'];

$langDetail = mysqli_query($db_con, "select * from tbl_language where lang_name='" . $_SESSION['lang'] . "'") or die('Error : ' . mysqli_error($db_con));
$langDetail = mysqli_fetch_assoc($langDetail);
?>
<link href="./assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
<link href="assets/plugins/bootstrap-select/css/bootstrap-select.min.css" rel="stylesheet" />
<link href="assets/plugins/multiselect/css/multi-select.css"  rel="stylesheet" type="text/css" />
<script type="text/javascript" src="assets/plugins/multiselect/js/jquery.multi-select.js"></script>
<script src="assets/js/jquery.core.js"></script>

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
    $.getScript('assets/js/gapi.js', function () {
        // Call custom function defined in script
        onLoad();
    });
    google.setOnLoadCallback(onLoad);
</script>
 

<div class="col-md-2 metastorage">
    <select  class="form-control select3" id="my_multi_select1" name="metadata[]" required>
        <option disabled selected><?php echo $lang['Select_Metadata']; ?></option>
        <?php
       // if($_REQUEST['status']=="file")
        //{
        ?>
       <option value="old_doc_name"><?php echo $lang['FileName']; ?></option>
        <option value="noofpages"><?php echo $lang['No_Of_Pages']; ?></option>
        <?php //} ?>
        <?php
        $arrarMeta = array();
        $metas = mysqli_query($db_con, "select * from tbl_metadata_to_storagelevel where sl_id='$slid'") or die('Error: metaData assign' . mysqli_error($db_con));
        while ($metaval = mysqli_fetch_assoc($metas)) {
            array_push($arrarMeta, $metaval['metadata_id']);
        }
        $meta = mysqli_query($db_con, "select * from tbl_metadata_master");
        while ($rwMeta = mysqli_fetch_assoc($meta)) {
            if (in_array($rwMeta['id'], $arrarMeta)) {
                echo '<option>' . $rwMeta['field_name'] . '</option>';
            }
        }
        ?>
    </select>

</div>
<div class="col-md-2 metastorage1">
    <select class="form-control select3" name="cond[]" required>
        <option disabled selected style="background: #808080; color: #121213;"><?php echo $lang['Slt_Condition']; ?></option>
        <option <?php
        if (isset($_GET['cond']) && !empty($_GET['cond']) && $_GET['cond'] == 'Equal') {
            echo'selected';
        }
        ?>><?php echo $lang['Equal']; ?></option>
        <option <?php
        if (isset($_GET['cond']) && !empty($_GET['cond']) && $_GET['cond'] == 'Contains') {
            echo'selected';
        }
        ?>><?php echo $lang['Contains']; ?></option>
        <option <?php
        if (isset($_GET['cond']) && !empty($_GET['cond']) && $_GET['cond'] == 'Like') {
            echo'selected';
        }
        ?>><?php echo $lang['Like']; ?></option>
        <option <?php
        if (isset($_GET['cond']) && !empty($_GET['cond']) && $_GET['cond'] == 'Not Like') {
            echo'selected';
        }
        ?>><?php echo $lang['Not_Like']; ?></option>
    </select>
</div>
<div class="col-md-2 metastorage2">
    <input type="text" class="form-control translatetext" name="searchText[]" required value="<?php echo $_GET['searchText'] ?>" placeholder="<?php echo $lang['entr_srch_txt_hr']; ?>" title="<?= $lang['Search'] ?>">
</div>

<script type="text/javascript" src="assets/plugins/multiselect/js/jquery.multi-select.js"></script>
<script src="assets/js/jquery.core.js"></script>

<script src="assets/plugins/bootstrap-filestyle/js/bootstrap-filestyle.min.js" type="text/javascript"></script>
<script src="assets/plugins/select2/js/select2.min.js" type="text/javascript"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $('form').parsley();

    });
    $(".select3").select2();

    //firstname last name 
    $("input#userName, input#lastName").keypress(function (e) {
        //if the letter is not digit then display error and don't type anything
        if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57) && e.which != 46) {
            //display error message
            return true;
        } else {
            return false;
        }
        str = $(this).val();
        str = str.split(".").length - 1;
        if (str > 0 && e.which == 46) {
            return false;
        }
    });
    $("input#phone").keypress(function (e) {
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
    $(document).ready(function () {

        //Disable mouse right click
        $("body").on("contextmenu", function (e) {
            // return false;
        });
    });

</script>


