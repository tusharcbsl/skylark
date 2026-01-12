<?php
require_once '../../sessionstart.php';
if (!isset($_SESSION['cdes_user_id'])) {
    header("location:../../logout");
}
if (isset($_SESSION['lang'])){
     $file = "../../".$_SESSION['lang'].".json";
 } else {
     $file = "../../English.json";
 }
  //for user role
$data = file_get_contents($file);
 $lang = json_decode($data, true);
require_once '../config/database.php';
$qryDoc= mysqli_query($db_con, "select * from `tbl_document_master` order by doc_id desc limit 1");
$fetchData= mysqli_fetch_assoc($qryDoc);
$start_num="00000".$fetchData['doc_id']+1;
     //for user role
echo "<div class='col-md-6 dymicadd'> "
. "<label>$lang[file_num]<span></span></label>"
. "<div class='input-group' style=''>"
. "<input type='text' class='form-control specialchaecterlock1' name='fnumber' placeholder='$lang[Entr_Fil_Numbr]'>"
. "<span class='input-group-addon'><input type='hidden' value='$start_num' class='form-control' name='autoFNum'><i class='fa fa-book'></i></span>
    </div>
</div>";
?>
<script src="assets/js/jquery.core.js"></script>
<script src="assets/plugins/bootstrap-inputmask/bootstrap-inputmask.min.js" type="text/javascript"></script>
<script>
    $(document).ready(function(){
        
       $('.specialchaecterlock1').keyup(function ()
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
</script>