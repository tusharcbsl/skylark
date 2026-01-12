
    <?php  
    session_start();
    require_once './application/config/database.php';
  //Delete Selected files
           if (isset($_POST['doc_id'])) {
               $docDelete = trim($_POST['doc_id']);
                    $user_id4 = $_SESSION['cdes_user_id'];
                    $chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um where FIND_IN_SET('$user_id4', user_ids) > 0") or die('Error:' . mysqli_error($db_con));
                    $rwcheckUser = mysqli_fetch_assoc($chekUsr);
                    $getDocPath = mysqli_query($db_con, "select * from tbl_document_master where doc_id in($docDelete)") or die('Error:' . mysqli_error($db_con));
                    $rwgetDocPath = mysqli_fetch_assoc($getDocPath);
                    $filePath = $rwgetDocPath['doc_path'];
                    $filename = $rwgetDocPath['old_doc_name'];
                if ($rwcheckUser['role_id'] == 1){
                    $del = mysqli_query($db_con, "DELETE FROM tbl_document_master WHERE doc_id in($docDelete)") or die('Error:' . mysqli_error($db_con));
                       foreach($filePath as $filePaths){
                         unlink('extract-here/' . $filePaths);    
                         } 
                         if ($del) {
                         foreach($filename as $filenames){
                         $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,'Storage Document $filename Deleted','$date',null,'$host',null)") or die('error : ' . mysqli_error($db_con));
                         }
                         $storgId = $rwgetDocPath['doc_name'];
                         echo 1;
                        //echo'<script>taskSuccess("storageFiles","Document Deleted Successfully !");</script>';
                         } else {
                         echo 0;
                         }
                }else{
                      $deletefilename1 = mysqli_query($db_con, "UPDATE tbl_document_master SET flag=0 WHERE doc_id in($docDelete)") or die('Error:' . mysqli_error($db_con));
                      if ($deletefilename1) {
                        foreach($filename as $filenames){
                        $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,'Storage Document $filenames Deleted','$date',null,'$host',null)") or die('error : ' . mysqli_error($db_con));
                        }
                        $storgId = $rwgetDocPath['doc_name'];
                        echo 1;
                        //echo'<script>taskSuccess("storageFiles","Document Deleted Successfully !");</script>';
                       } else {
                        echo 0;
                       }
                    
                }
                mysqli_close($db_con);     
            }
            ?>