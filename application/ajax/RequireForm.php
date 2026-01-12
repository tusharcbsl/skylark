<?php
require_once '../../sessionstart.php';
if (!isset($_SESSION['cdes_user_id'])) {
    header("location:../../logout");
}

require_once '../config/database.php';
     //for user role

$chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$_SESSION[cdes_user_id]', user_ids) > 0") or die('Error:'. mysqli_error($db_con));
   
   $rwgetRole = mysqli_fetch_assoc($chekUsr);
  
  // echo $rwgetRole['dashboard_mydms']; die;
   if($rwgetRole['workflow_initiate_file'] != '1'){
   header('Location: ../../index');
   }


$wid = $_POST['wid'];
if (intval($slid)) {
//echo "<script>alert('".$wid."')</script>";
 $getWorkflwDs = mysqli_query($db_con, "select * from tbl_workflow_master where workflow_id ='$wid'") or die('Error in getWorkflw upload:' . mysqli_error($db_con));
 $rwgetWorkflwIdDs = mysqli_fetch_assoc($getWorkflwDs);

?>
 <textarea class="form-control" rows="5" name="taskRemark" id="editor" ><?php echo $rwgetWorkflwIdDs['workflow_description']; ?></textarea>
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