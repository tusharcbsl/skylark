<?php
require_once '../../sessionstart.php';
if (!isset($_SESSION['cdes_user_id'])) {
    header("location:../../logout");
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
$ses_val = $_SESSION;
$langDetail = mysqli_query($db_con, "select * from tbl_language where lang_name='$_SESSION[lang]'") or die('Error : ' . mysqli_error($db_con));
$langDetail = mysqli_fetch_assoc($langDetail);
$chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$_SESSION[cdes_user_id]', user_ids) > 0") or die('Error:' . mysqli_error($db_con));

$rwgetRole = mysqli_fetch_assoc($chekUsr);

// echo $rwgetRole['dashboard_mydms']; die;
if ($rwgetRole['rename_document'] != '1') {
    header('Location: ../../index');
}

$id = $_POST['FID'];
$reName = mysqli_query($db_con, "SELECT old_doc_name,doc_name FROM `tbl_document_master` where doc_id = '$id';") or die("Error in dd" . mysqli_error($db_con));
$rwreName = mysqli_fetch_assoc($reName) or die("Error in file fetch" . mysqli_error($db_con));
$fname = substr($rwreName['old_doc_name'], 0, strrpos($rwreName['old_doc_name'], '.'));


if ($fname === '') {
    $file1 = $rwreName['old_doc_name'];
?>
    <label class="text-primary"><?php echo $lang['edit_File_name']; ?><span class="text-alert">*</span></label>
    <input type="text" id="filen" name="filename" class="form-control specialchaecterlock" value="<?php echo $file1; ?>" placeholder="<?php echo $lang['edit_File_name']; ?>" required />

<?php
} else {
?>
    <label class="text-primary"><?php echo $lang['edit_File_name']; ?><span class="text-alert">*</span></label>
    <input type="text" id="filen" name="filename" class="form-control specialchaecterlock" value="<?php echo $fname; ?>" placeholder="<?php echo $lang['edit_File_name']; ?>" required />

<?php
}
?>



<input type="hidden" value="<?php echo $id; ?>" name="docId" />
<input type="hidden" value="<?php echo $rwreName['doc_name']; ?>" name="slId" />
<script>
           $('#filen').keyup(function() {
       
       //alert("okayyyy");
       var groupName = $(this).val();
       var re = /[|\\?:"<>\/]/gi;
       var isSplChar = re.test(groupName);
       
       if (isSplChar) {
           var no_spl_char = groupName.replace(re, '');
           $(this).val(no_spl_char);
       }
   });
</script>
<script type="text/javascript">
    google.load("elements", "1", {
        packages: "transliteration"
    });

    function onLoad12() {
        var langcode = '<?php echo $langDetail['lang_code']; ?>';
        var options = {
            sourceLanguage: 'en',
            destinationLanguage: [langcode],
            shortcutKey: 'ctrl+g',
            transliterationEnabled: true
        };
        var control = new google.elements.transliteration.TransliterationControl(options);
        var ids = ["filen"];
        control.makeTransliteratable(ids);
    }
    $.getScript('test.js', function() {
        //console.log("hfbfdbfd ");
        // Call custom function defined in script
        onLoad12();
    });
    google.setOnLoadCallback(onLoad12);
    //console.log("test 12");
    // $('.specialchaecterlock').keyup(function() {
    //     var groupName = $(this).val();
    //     re = /[`1234567890~!@#$%^&*()|+\=?;:'",.<>\{\}\[\]\\\/]/gi;
    //     var isSplChar = re.test(groupName);
    //     if (isSplChar) {
    //         var no_spl_char = groupName.replace(/[`~!@#$%^&*()|+\=?;:'",.<>\{\}\[\]\\\/]/gi, '');
    //         $(this).val(no_spl_char);
    //     }
    // });
</script>