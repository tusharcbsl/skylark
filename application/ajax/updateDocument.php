<?php
require_once '../../sessionstart.php';
if(!isset($_SESSION['cdes_user_id'])){
    header("location:../../logout");
}
require './../config/database.php';
$id = $_POST['ID'];
$docmodify = mysqli_query($db_con, "select * from tbl_document_master where doc_id='$id'") or die('Error:'. mysqli_error($db_con));
 $rwModify = mysqli_fetch_assoc($docmodify); 
 //$rwModify['old_doc_name']; 
 if (isset($_SESSION['lang'])){
     $file = "../../".$_SESSION['lang'].".json";
 } else {
     $file = "../../English.json";
 }
 //for user role
$data = file_get_contents($file);
 $lang = json_decode($data, true);
?>       
<!--modify starts-->
<div class="row"> 
    <div class="col-md-6"> 
<div class="form-group">
    <label for="userName"><?php echo $lang['File_Name'];?></label>
    <input type="hidden" name="docId" value="<?php echo $id; ?>"/>
    <input type="text" name="renameName" parsley-trigger="change" value="<?php echo $rwModify['old_doc_name']; ?>" class="form-control" required  class="form-control" id="reporting" placeholder="<?php echo $lang['Entr_Fil_Nam'];?>">
</div>
    </div>
</div>
<!-- /.modal modify ends --> 


<script src="assets/plugins/bootstrap-filestyle/js/bootstrap-filestyle.min.js" type="text/javascript"></script>
<script src="assets/plugins/select2/js/select2.min.js" type="text/javascript"></script>

<script type="text/javascript" src="assets/plugins/parsleyjs/parsley.min.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $('form').parsley();

    });
    $(".select2").select2();
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
</script>