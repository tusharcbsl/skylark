<?php
require_once '../../sessionstart.php';
//require_once '../../loginvalidate.php';
if (!isset($_SESSION['cdes_user_id'])) {
    header("location:../../logout");
}

require './../config/database.php';
//for user role
mysqli_set_charset($db_con, "utf8");
$langDetail = mysqli_query($db_con, "select * from tbl_language where lang_name='$_SESSION[lang]'") or die('Error : ' . mysqli_error($db_con));
$langDetail = mysqli_fetch_assoc($langDetail);

$chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$_SESSION[cdes_user_id]', user_ids) > 0") or die('Error:' . mysqli_error($db_con));

$rwgetRole = mysqli_fetch_assoc($chekUsr);

// echo $rwgetRole['dashboard_mydms']; die;
if ($rwgetRole['modify_group'] != '1') {
    header('Location: ../../index');
}
if (!isset($_POST['ID'],$_POST['token'])) {
    echo "Unauthorised access !";
     exit;
}
$id = preg_replace("/[^0-9 ]/", "", $_POST['ID']);
mysqli_set_charset($db_con, "utf8");
$group = mysqli_query($db_con, "select * from tbl_group_master where group_id='$id'");
$rwGroup = mysqli_fetch_assoc($group);
?>
<?php
if (isset($_SESSION['lang'])) {
    $file = "../../" . $_SESSION['lang'] . ".json";
} else {
    $file = "../../English.json";
}
//for user role
$data = file_get_contents($file);
$lang = json_decode($data, true);
?>
<div class="row"> 
    <div class="col-md-12"> 
        <div class="form-group">
            <label><?php echo $lang['group_Name']; ?><span style="color:red">*</span></label>
            <input type="text" name="groupName" autocomplete="off" required class="form-control translatetext" id="groupName" value="<?php echo $rwGroup['group_name']; ?>">
        </div>
    </div> 

</div> 
<div class="row"> 
    <div class="col-md-12"> 
        <div class="form-group">
            <label><?php echo $lang['Usrs_in_Grp']; ?> <?php echo $rwGroup['group_name']; ?></label>
            <select class="select3 select2-multiple" multiple data-placeholder="select users.." name="users[]">
                <?php
                //$userIds=array();
                $groupUser = mysqli_query($db_con, "select * from tbl_bridge_grp_to_um where group_id='$id'");
                $rwGroupuser = mysqli_fetch_assoc($groupUser);
                $userIds = $rwGroupuser['user_ids'];
                $userIds = explode(",", $userIds);

                $user = mysqli_query($db_con, "select * from tbl_user_master ");
                while ($rwUser = mysqli_fetch_assoc($user)) {
                    if ($rwUser['user_id'] != 1 && $rwUser['user_id'] != $_SESSION['cdes_user_id']) {
                        if (in_array($rwUser['user_id'], $userIds))
                            echo '<option selected value="' . $rwUser['user_id'] . '">' . $rwUser['first_name'] . ' ' . $rwUser['last_name'] . '</option>';
                        else
                            echo '<option value="' . $rwUser['user_id'] . '">' . $rwUser['first_name'] . ' ' . $rwUser['last_name'] . '</option>';
                    }
                }
                ?>
            </select>
        </div>
    </div> 

</div>                                                     

<input type="hidden" name="gid" value="<?php echo $rwGroup['group_id']; ?>">


<script src="assets/plugins/bootstrap-filestyle/js/bootstrap-filestyle.min.js" type="text/javascript"></script>
<script src="assets/plugins/select2/js/select2.min.js" type="text/javascript"></script>
<script type="text/javascript" src="assets/plugins/parsleyjs/parsley.min.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $('form').parsley();
        $('#groupName').on("cut copy paste", function (e) {
            e.preventDefault();
        });
    });
    $(".select3").select2();
    //firstname last name 
    $("input#groupName").keypress(function (e) {
        //if the letter is not digit then display error and don't type anything
        if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57) && e.which != 46) {
            //display error message
            return true;
        } else {
            return false;
        }
    });
    $(function () {

        $('input#groupName').keyup(function ()
        {
            var groupName = $(this).val();
            re = /[`~!@#$%^&*()_|+\-=?;:'",.<>\{\}\[\]\\\/]/gi;
            var isSplChar = re.test(groupName);
            if (isSplChar)
            {
                var no_spl_char = groupName.replace(/[`~!@#$%^&*()_|+\-=?;:'",.<>\{\}\[\]\\\/]/gi, '');
                $(this).val(no_spl_char);
            }
        });

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

<!---->