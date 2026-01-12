<?php
require_once '../../sessionstart.php';
if (!isset($_SESSION['cdes_user_id'])) {
    header("location:../../logout");
}
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.


  require './../config/database.php';
  echo $ID=$_POST['ID'];
  $name=$_POST['NAME'];
  echo $fname= substr($name,0, strrpos($name, " "));
  echo $lname= substr($name, strrpos($name, " "));
  echo $email=$_POST['EMAIL'];
  $update= mysqli_query($db_con,"update tbl_user_master set first_name='$fname', last_name='$lname', UserEmail='$email' where UserID='$ID'");
 */
require './../config/database.php';
//for user role
if (isset($_SESSION['lang'])) {
    $file = "../../" . $_SESSION['lang'] . ".json";
} else {
    $file = "../../English.json";
}
//for user role
$data = file_get_contents($file);
$lang = json_decode($data, true);
$chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$_SESSION[cdes_user_id]', user_ids) > 0") or die('Error:' . mysqli_error($db_con));

$rwgetRole = mysqli_fetch_assoc($chekUsr);

// echo $rwgetRole['dashboard_mydms']; die;
if ($rwgetRole['edit_faq'] != '1') {
    header('Location: ../../index');
}
if (intval($_POST['ID'])) {
    $id = $_POST['ID'];
    $editFaq = mysqli_query($db_con, "SELECT * FROM `tbl_faq_master` where id = '$id';") or die("Error in edit" . mysqli_error($db_con));
    $rweditFaq = mysqli_fetch_assoc($editFaq) or die("Error in edit" . mysqli_error($db_con));
    ?>
    <div class="row">
        <div class="form-group">
            <label><?php echo $lang['Etr_ur_Que'] ?></label>
            <input type="text" name="faq" class="form-control" value="<?php echo $rweditFaq['question']; ?>" placeholder="<?php echo $lang['Etr_ur_Que'] ?>..."required>
        </div>
        <div class="form-group">
            <label><?php echo $lang['Ent_ur_Ans'] ?></label>
            <textarea class="form-control" rows="5" name="faqans" id="editor" required><?php echo $rweditFaq['answer']; ?></textarea>
    <!--        <input type="text" name="faqans" class="form-control" value="<?php echo $rweditFaq['answer']; ?>" placeholder="Enter your Answer here..."required>-->
        </div>
        <input type="hidden" name="faqid" value="<?php echo $rweditFaq['id']; ?>">
    </div> 

    <!---html textarea editor js code--->
    <script src="assets/plugins/tinymce/tinymce.min.js"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            if ($("#editor").length > 0) {
                tinymce.init({
                    selector: "textarea#editor",
                    theme: "modern",
                    height: 200,
                    plugins: [
                        "advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker",
                        "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
                        "save table contextmenu directionality emoticons template paste textcolor"
                    ],
                    toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | forecolor backcolor emoticons",
                    style_formats: [
                        {title: 'Bold text', inline: 'b'},
                        {title: 'Red text', inline: 'span', styles: {color: '#ff0000'}},
                        {title: 'Red header', block: 'h1', styles: {color: '#ff0000'}},
                    ]
                });
            }
        });
    </script>

<?php } ?>