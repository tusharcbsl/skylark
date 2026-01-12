<?php
require '../sessionstart.php';
require_once '../application/config/database.php';
require_once '../application/pages/function.php';
require_once '../classes/fileManager.php';
$fileManager = new fileManager();

error_reporting(0);
//require_once '../loginvalidate.php';
//  require_once '../application/pages/head.php';
//for user role
$chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$_SESSION[cdes_user_id]', user_ids) > 0") or die('Error:' . mysqli_error($db_con));

$rwgetRole = mysqli_fetch_assoc($chekUsr);
$uid = base64_decode(urldecode($_GET['i']));
if ($uid != $_SESSION['cdes_user_id']) {
    // header('Location:../index');
}
if (isset($_SESSION['lang'])) {
    $file = "../".$_SESSION['lang'] . ".json";
} else {
    if (isset($_SESSION['cdes_user_id'])) {
        $LangQuery = mysqli_query($db_con, "SELECT * FROM `tbl_user_master` WHERE user_id='$_SESSION[cdes_user_id]'") or die('error : ' . mysqli_error($db_con));
        $LangRow = mysqli_fetch_array($LangQuery);
        if (!empty($LangRow['lang'])) {
            $file = "../" . $LangRow['lang'] . ".json";
        } else {
            $file = '../English.json';
        }
    }
}
$data = file_get_contents($file);
$lang = json_decode($data, true);

$id1 = base64_decode(urldecode($_GET['id'])); //doc_id
//$id = base64_decode(urldecode($_GET['id']));  //doc asign id
if ($_GET['chk'] == "rw") {
    $file = mysqli_query($db_con, "select doc_name, doc_path, doc_extn, old_doc_name,File_Number,doc_desc from tbl_document_reviewer where doc_id='$id1'") or die('error' . mysqli_error($db_con));
} else {
    $file = mysqli_query($db_con, "select doc_name, filename, doc_path, doc_extn, old_doc_name from tbl_document_master where doc_id='$id1'") or die('error' . mysqli_error($db_con));
}
$rwFile = mysqli_fetch_assoc($file);
$fileName = $rwFile['old_doc_name'];
$doc_old_name = $rwFile['old_doc_name'];
$filePath = $rwFile['doc_path'];
$slid = $rwFile['doc_name'];
$doc_extn = $rwFile['doc_extn'];
$doc_desc = $rwFile['doc_desc'];
$File_Number = isset($rwFile['File_Number']) ? $rwFile['File_Number'] : '';
$user = mysqli_query($db_con, "select * from tbl_user_master where user_id='$_SESSION[cdes_user_id]'");
$rwUser = mysqli_fetch_assoc($user);
$userSign = $rwUser['user_sign'];

$lpath = explode("/", $filePath);
$ectns = explode(".", end($lpath));

// Connect to file server
$fileManager->conntFileServer();
$localPath = $fileManager->getFile($rwFile, '../');



if ($ectns[1] != "html") {
	

    /*require 'bootstrap.php';
    // echo $localPath;
    $phpWord = new \PhpOffice\PhpWord\PhpWord();
    $phpWord = \PhpOffice\PhpWord\IOFactory::load($localPath);
    $htmlWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'HTML');
    $path = "TEMP";
    if (!is_dir($path)) {
        mkdir($path, 0777, true);
    }
    $fileName = strtotime(date('Y-m-d h:i:s')) . "_" . $fileName;
    $htmlFilename = $path . '/' . $fileName . '.html';
    $htmlWriter->save($path . '/' . $fileName . '.html');
    $content = file_get_contents($htmlFilename);
    unlink($htmlFilename); //delete temp file after geting data*/
   
    require './OfficeConverter.php';
    $path = "TEMP";
    if (!is_dir($path)) {
        mkdir($path, 0777, true);
    }
        $converter = new OfficeConverter($localPath, $path . "/");
		
        $fileprefix=strtotime(date('Y-m-d h:i:s'));
        $fileName = $fileprefix."_" . $fileName;
        $filename = str_replace(" ", "", $fileName);
        $filename2 = str_replace(".", "_", $filename);
        $encName = base64_encode($filename2);
        $filename1 = preg_replace("/[^A-Za-z0-9]/", "", $encName);
        //$filename1 = basename($localPath,(pathinfo($localPath, PATHINFO_EXTENSION) ? '.'.pathinfo($localPath, PATHINFO_EXTENSION):''));
        $fnamepdf = $filename1.'.html';
        $converter->convertTo($fnamepdf);
		
	
        $localPath = "TEMP/" . $fnamepdf; 
        
        $data = file_get_contents($localPath);
        $content = str_replace('img src="', 'img src="' . $path . '/', $data);
        /* if (FTP_ENABLED) {
			unlink($localPath); //delete temp file after geting data
        } */
    
} else {

    $data = file_get_contents($localPath);
    $content = $data;
    /* if(FTP_ENABLED){
		unlink($localPath); //delete temp file after geting data
    } */
}
//
?>
<!DOCTYPE html>
<html>
    <head>
        <title><?= $fileName ?></title>
        <link href="../assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
		<style>
			.tox-notifications-container{
				display: none !important;
			}
		</style>
    </head>
    <body>
        <div class="container-fluid" >
            <div style="padding-top: 1px;padding: 1px;background-color:whitesmoke ">
                <center><p><h3><?= $doc_old_name . "." . $doc_extn ?></h3></p></center>

            </div>
          
            <div class="col-md-10 hides" style="background-color: whitesmoke;height: 100%">


                <form method="post" id="sbmt" name="submit">
                    <textarea class="form-control"  name="taskRemark" id="editor" ><?= $content ?></textarea>
                </form>

                <a  id="confirm" class="btn btn-default pull-right m-t-10" style="margin-top: 15px;"><?php echo $lang['Submit_Review'] ?></a>
                <a href="<?=basename($_SERVER[REQUEST_URI]) ?>" class="btn btn-warning pull-right" style="margin: 15px;"><?php echo $lang['Reset'] ?></a>

            </div>
            <div class="col-md-2" style="background-color: white;height: 100%">
                <div id="comment-wrapper">
                    <h4><center><?php echo $lang['Review_Log'] ?></center></h4>
                    <div class="comment-list">
                        <div class="comment-list-container">
                            <!--div class="comment-list-item"-->
                            <div id="comentAdd">
                                <?php
                                $docReview = mysqli_query($db_con, "select * from `tbl_reviews_log` where doc_id='$id1' and in_review='0' order by id asc");
                                if (mysqli_num_rows($docReview) > 0) {
                                    while ($rwcomment = mysqli_fetch_assoc($docReview)) {

                                        $usr = mysqli_query($db_con, "select first_name, last_name,profile_picture from tbl_user_master where user_id='$rwcomment[user_id]'");
                                        $rwUsr = mysqli_fetch_assoc($usr);
                                        ?>
                                        <div class="comment-list-item">   
                                            <li class="clearfix">
                                                <div class="conversation-text">

                                                    <div class="ctext-wrap">
                                                        <span style="float:left;">   <?php
                                                            if (!empty($rwcomment['action_name'])) {
                                                                echo '<strong>Action: </strong>' . $rwcomment['action_name'] . '<br>';
                                                            }
                                                          
                                                            ?> </span> <div class="clearfix"></div>
                                                        <span style="float:right;">
                                                            <i><?php echo '<strong>Action By: </strong>' .$rwUsr['first_name'] . ' ' . $rwUsr['last_name']; ?></i>
                                                            <br/>
                                                            <?php echo '<strong>Action Time: </strong>' .date("j F, Y, H:i", strtotime($rwcomment['start_date'])); ?></span>
                                                    </div>
                                                </div>
                                            </li>
                                        </div>

                                        <?php
                                    }
                                } else {
                                    ?>
                                    <div class="comment-list-item"><center><?php echo $lang['No_Review_Log'] ?></center></div>
                                            <?php
                                        }
                                        ?>
                            </div>
                            <!--/div-->
                        </div>

                    </div>
                </div>

            </div>
        </div>

    </body>
    <script src="../assets/js/jquery.min.js"></script>
    <script src="../assets/plugins/tinymce/tinymce.min.js"></script>
    <script type="text/javascript">
        
        $("#confirm").click(function () {
            var r = confirm("Do You Want To Submit Review?");
            if (r == true) {
                document.getElementById("sbmt").submit();
            } else {

            }
        })
        $(document).ready(function () {
            if ($("#editor").length > 0) {
                tinymce.init({
                    selector: "textarea#editor",
                 
                    height: 500,
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


</html> 
<?php
if (isset($_POST['taskRemark'])) {
	
	$htmlString = $_POST['taskRemark'];

    $revId = base64_decode(urldecode($_GET['reid']));
   
    //echo $lang;
    // echo 'run';
    $status = createVersion($htmlString, $fileName, $filePath, $slid, $doc_extn, $doc_temp_extn, $db_con, $date, $File_Number, $fileserver, $port, $ftpUser, $ftpPwd, $id1, $revId, $host, $lang,$projectName,$doc_desc);
    if ($status['status']) {

        // Commit transaction
        mysqli_commit($status['conn']);
        echo "<script>$('.hides').hide();alert('$status[msg]');window.location.href='../reviewintray';</script>";
    } else {
        echo "<script>alert('$status[msg]');</script>";
    }
}

function createVersion($htmlString, $fileName, $filePath, $slid, $doc_extn, $doc_temp_extn, $db_con, $date, $File_Number, $fileserver, $port, $ftpUser, $ftpPwd, $id1, $revId, $host, $lang,$projectName,$doc_desc) {

    if (!empty($htmlString)) {
        if (!empty($fileName)) {
            if (!empty($filePath)) {
                mysqli_autocommit($db_con, FALSE);
                $slid = $slid . "_" . $id1;
                $docVersionCount = mysqli_query($db_con, "select count(doc_id) as num from tbl_document_reviewer where doc_name='$slid'");
                $lastDoc = mysqli_fetch_assoc($docVersionCount);
                $uploadedBy = $_SESSION['cdes_user_id'];
                $fnameExpload = explode("_", $fileName);
                $fXplode = isset($fnameExpload[2]) && !empty($fnameExpload[2]) ? $fnameExpload[2] + 1 : "1";
                //$fname = $fnameExpload[0]. "_" . ($lastDoc['num']+1);
                $fname = setFileVersionName($fileName,$lastDoc['num']);
                $newfilePath = explode("/", $filePath);
                $indexCount = count($newfilePath);
                unset($newfilePath[$indexCount - 1]);
                $docDesc= json_decode($doc_desc,TRUE);
                $subject=$docDesc['subject'];
                /*
                 * Filter File Name remove unknown chars
                 */
                $filterename=preg_replace('/[^A-Za-z]/', '', $fname);
                /*
                 * Encrypted File Name
                 */
                $fileEncName=urlencode(base64_encode($filterename));
                $fileEncName=strtotime($date).(preg_replace('/[^A-Za-z0-9]/', '', $fileEncName));
                $normalPath = implode("/", $newfilePath) . "/" . $fileEncName . ".html";
                $ExtractPath = "../extract-here/" . implode("/", $newfilePath) . "/" . $fileEncName . ".html";
                //echo $ExtractPath;
                //die;
                $myfile = fopen($ExtractPath, "w");
                $fFileByte = fwrite($myfile, trim($htmlString));
                if ($fFileByte) {
                    $Fsize = filesize($ExtractPath);
                    $Fsize = round(($Fsize / 1000), 2);
                    $fetchReview = mysqli_query($db_con, "select * from `tbl_doc_review` where id='$revId'");
                    if (mysqli_num_rows($fetchReview) > 0) {
                        $reviewInfo = mysqli_fetch_assoc($fetchReview);
                        $ticketId = $reviewInfo['ticket_id'];
                        $currentOredr = $reviewInfo['review_order'];
                        $maxOrderCurrentTicket = mysqli_query($db_con, "select max(review_order) as lastOrder from `tbl_doc_review` where ticket_id='$ticketId'");
                        $lastOrder = mysqli_fetch_assoc($maxOrderCurrentTicket);
                        $nextOredr = (($currentOredr + 1) < $lastOrder['lastOrder']) ? $currentOredr + 1 : $lastOrder['lastOrder'];
                        $updatenextReview = mysqli_query($db_con, "update tbl_doc_review set next_task='0' where review_order='$nextOredr' and ticket_id='$ticketId'");
                      
                        $updateReview = mysqli_query($db_con, "update tbl_doc_review set next_task='1',review_status='1',task_status='Reviewed',action_time='$date' where id='$revId'");
                        $fetchQry=mysqli_query($db_con,"select id from `tbl_doc_review` where review_order='$nextOredr' and ticket_id='$ticketId' and next_task='0'");
                        if(mysqli_num_rows($fetchQry)>0)
                        {
                            $idins= mysqli_fetch_assoc($fetchQry);
                            $nextOrderAvai=1;
                        }else{
                            
                           $nextOrderAvai=0; 
                        }
                      
                        if ($updateReview) {
                            if ($updateReview) {
                                $cols = '';
                                $columns = mysqli_query($db_con, "SHOW COLUMNS FROM tbl_document_reviewer");
                                while ($rwCols = mysqli_fetch_array($columns)) {
                                    if ($rwCols['Field'] != 'doc_id') {
                                        if (empty($cols)) {
                                            $cols = '`' . $rwCols['Field'] . '`';
                                        } else {
                                            $cols = $cols . ',`' . $rwCols['Field'] . '`';
                                        }
                                    }
                                }
                                $createVrsn = mysqli_query($db_con, "INSERT INTO tbl_document_reviewer($cols) select $cols from tbl_document_reviewer where doc_id='$id1'") or die('Error:' . mysqli_error($db_con));
                                $insertDocID = mysqli_insert_id($db_con);
                                if ($createVrsn) {
                                    $updateNew = mysqli_query($db_con, "update tbl_document_reviewer set doc_name='$slid' where doc_id='$insertDocID'");
                                    $qry = mysqli_query($db_con, "update tbl_document_reviewer set old_doc_name='$fname', doc_extn='docx', doc_path='$normalPath', uploaded_by='$uploadedBy', doc_size='$Fsize', noofpages='1', dateposted='$date',File_Number='$File_Number' where doc_id='$id1'");


                                    //$qry = mysqli_query($db_con, "insert into `tbl_document_reviewer`(`doc_name`,`old_doc_name`,`doc_extn`,`doc_path`,`uploaded_by`,`doc_size`,`noofpages`,`dateposted`,`File_Number`)values('$slid','$fname','docx','$normalPath','$uploadedBy','$Fsize','1','$date','$File_Number')");
                                    if ($qry) {
                                        $qry = mysqli_query($db_con, "INSERT INTO `tbl_reviews_log`(`user_id`,`doc_id`,`action_name`,`start_date`,`end_date`,`system_ip`,`remarks`)values('$_SESSION[cdes_user_id]','$id1','Document Reviewed ','$date','$date','$host','')");
                                        if ($qry) {
											
											
                                            if (uploadFileInFtpServer($fileserver, $port, $ftpUser, $ftpPwd, $normalPath, $ExtractPath)) {
                                                require '../mail.php';
                                            
                                                if($nextOrderAvai==1)
                                                {
                                                
                                                $mail=assignNextReview($ticketId, $idins['id'], $db_con, $projectName, $subject);
                                                }
                                                else {
                                                   
                                                   $mail=completeReview($ticketId ,$db_con, $projectName, $subject); 
                                                }
                                               
                                                if($mail)
                                                {
                                                return array("status" => True, "msg" => $lang['Document_Review_Successfully'], "conn" => $db_con);
                                                } else {
                                                 return array("status" => False, "msg" => "Mail Not Sent");    
                                                }
                                            } else {
                                                return array("status" => False, "msg" => $lang['Document_Upload_Failed']);
                                            }
                                        } else {
                                            return array("status" => False, "msg" => $lang['Log_Create_Failed']);
                                        }
                                    } else {
                                        return array("status" => False, "msg" => $lang['Failed_To_Register_Document']);
                                    }
                                } else {
                                    return array("status" => False, "msg" => $lang['Failed_To_Version_Document']);
                                }
                            } else {
                                return array("status" => False, "msg" => $lang['Update_Review_Failed']);
                            }
                        } else {
                            return array("status" => False, "msg" => $lang['Invalid_Order_ID']);
                        }
                    } else {
                        return array("status" => False, "msg" => $lang['Invalid_Reviewer']);
                    }
                } else {
                    return array("status" => False, "msg" => $lang['File_Export_Failed']);
                }
            } else {
                return array("status" => False, "msg" => $lang['Invalid_File_Location']);
            }
        } else {
            return array("status" => False, "msg" => $lang['Invalid_File_Name']);
        }
    } else {
        return array("status" => False, "msg" => $lang['Invalid_Input']);
    }
}

function uploadFileInFtpServer($fileserver, $port, $ftpUser, $ftpPwd, $destinationPath, $sourcePath) {

    encrypt_my_file($sourcePath);

 if (FTP_ENABLED) {

    require_once '../classes/ftp.php';

    $ftp = new ftp();
    $ftp->conn("$fileserver", "$port", "$ftpUser", "$ftpPwd");

    if ($ftp->put(ROOT_FTP_FOLDER . '/' . $destinationPath, $sourcePath)) {
        return TRUE;
    } else {
        return FALSE;
        $arr = $ftp->getLogData();
        if ($arr['error'] != "") {

            echo '<h2>Error:</h2>' . implode('<br />', $arr['error']);
        }
    }
 }
else{
     return TRUE;
} 

}

function setFileVersionName($filename,$vno) {
    global $fileprefix;
    if(!empty($fileprefix)){
      $filename=ltrim($filename,$fileprefix.'_'); 
    }
    //check for if file name is with extension
    $ext= strtolower(pathinfo($filename,PATHINFO_EXTENSION));
    // List of Extension to be considered. 
    $allow_exts=array('doc','docx','pdf');
    if(!empty($ext)){
        if(in_array($ext, $allow_exts)){
            // set filename without extension
            $new_filename= basename($filename,".$ext");
        }
        else{
          $new_filename=$filename;
          $ext='';
        }
    }
    else{
        $new_filename=$filename;
    }
    $exploded_filename= explode('_', $new_filename);
    $new_vno=$vno+1;
    if($vno>0){
        if(end($exploded_filename)==$vno){
            array_pop($exploded_filename);
            $new_filename=implode("_",$exploded_filename);
        }
    }
    //echo $new_filename;
    $version_filename=$new_filename."_".$new_vno.(!empty($ext) ? '.'.$ext : '');
    //echo $version_filename;
    return $version_filename;
}
?>