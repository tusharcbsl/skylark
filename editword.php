<?php
require 'sessionstart.php';
require_once './application/config/database.php';
require_once './loginvalidate.php';
require_once './application/pages/function.php';
//for user role
$chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$_SESSION[cdes_user_id]', user_ids) > 0") or die('Error:' . mysqli_error($db_con));

$rwgetRole = mysqli_fetch_assoc($chekUsr);
$uid = base64_decode(urldecode($_GET['i']));
if ($uid != $_SESSION['cdes_user_id']) {
    // header('Location:./index');
}
if (isset($_SESSION['lang'])) {
    $file = $_SESSION['lang'] . ".json";
} else {
    if (isset($_SESSION['cdes_user_id'])) {
        $LangQuery = mysqli_query($db_con, "SELECT * FROM `tbl_user_master` WHERE user_id='$_SESSION[cdes_user_id]'") or die('error : ' . mysqli_error($db_con));
        $LangRow = mysqli_fetch_array($LangQuery);
        if (!empty($LangRow['lang'])) {
            $file = "./" . $LangRow['lang'] . ".json";
        } else {
            $file = "./English.json";
        }
    }
}
$data = file_get_contents($file);
$lang = json_decode($data, true);
$id1 = base64_decode(urldecode($_GET['id'])); //doc_id
//$id = base64_decode(urldecode($_GET['id']));  //doc asign id
if ($_GET['chk'] == "rw") {
    // @sk(261118)define log table
    $log_table="tbl_reviews_log";
    $user_info="user_id='$_SESSION[cdes_user_id]',";
    
    // for  conversion
    $con_table="tbl_document_reviewer";
    //Redirection Url in case of pdf conversion.
    $pdf_redirect="viewer?id=".urlencode(base64_encode($_SESSION[cdes_user_id]))."&i=".urlencode(base64_encode($id1))."&pn=1&chk=rw";//
    
    $file = mysqli_query($db_con, "select doc_name, doc_path, doc_extn, old_doc_name,File_Number from tbl_document_reviewer where doc_id='$id1'") or die('error' . mysqli_error($db_con));
} else {
    // @sk(261118)define log table
    $log_table="tbl_ezeefile_logs_wf";
    $user_info="user_id='$_SESSION[cdes_user_id]',user_name='$_SESSION[admin_user_name] $_SESSION[admin_user_last]',";
    
    // for  conversion
    $con_table="tbl_document_master";
    //Redirection Url in case of pdf conversion.
    $pdf_redirect="viewer?id=".urlencode(base64_encode($_SESSION[cdes_user_id]))."&i=".urlencode(base64_encode($id1));//
    
    $file = mysqli_query($db_con, "select doc_name, filename, doc_path, doc_extn, old_doc_name from tbl_document_master where doc_id='$id1'") or die('error' . mysqli_error($db_con));
}
$rwFile = mysqli_fetch_assoc($file);
//print_r($rwFile);
$fileName = $rwFile['old_doc_name'];
$doc_old_name = $rwFile['old_doc_name'];
$filePath = $rwFile['doc_path'];
$slid = $rwFile['doc_name'];
$doc_extn = $rwFile['doc_extn'];
//$doc_temp_extn = isset($rwFile['doc_tem_ext']) ? $rwFile['doc_tem_ext'] : '';
$File_Number = isset($rwFile['File_Number']) ? $rwFile['File_Number'] : '';
$user = mysqli_query($db_con, "select * from tbl_user_master where user_id='$_SESSION[cdes_user_id]'");
$rwUser = mysqli_fetch_assoc($user);
$userSign = $rwUser['user_sign'];



$storage = mysqli_query($db_con, "select sl_name from tbl_storage_level where sl_id='$slid'") or die('Error');
$rwStor = mysqli_fetch_assoc($storage);
$folderName = "./temp";
if (!dir($folderName)) {
    mkdir($folderName, 0777, TRUE);
}

$folderName = $folderName . '/' . $_SESSION['cdes_user_id'];
if (!dir($folderName)) {
    mkdir($folderName, 0777, TRUE);
}

$folderName = $folderName . '/' . preg_replace('/[^A-Za-z0-9\-]/', '', $rwStor['sl_name']); //preg_replace('/[^A-Za-z0-9\-]/', '', $string);
if (!dir($folderName)) {
    mkdir($folderName, 0777, TRUE);
}

$lpath = explode("/", $filePath);
$ectns = explode(".", end($lpath));
if ($ectns[1] == "html") {
    $localPath = $folderName . '/' . preg_replace('/[^A-Za-z0-9\-]/', '', $fileName) . '.' . "html";
} else {
    $localPath = $folderName . '/' . str_replace("doc", "d", preg_replace('/[^A-Za-z0-9\-]/', '', $fileName)) . '.' . $doc_extn;
}
if (FTP_ENABLED) {
    if (!empty($fileName)) {
        require_once './classes/ftp.php';
        $ftp = new ftp();
        $ftp->conn("$fileserver", "$port", "$ftpUser", "$ftpPwd");

        $server_path = ROOT_FTP_FOLDER . '/' . $filePath;
       // echo $server_path;
        //die;

        $ftp->get($localPath, $server_path); // download live "$server_path"  to local "$localpath"
        $arr = $ftp->getLogData();
       // print_r($arr);
       // die;
        if ($arr['error'] != "")
        // echo '<h2>Error:</h2>' . implode('<br />', $arr['error']);
            if ($arr['ok'] != "") {
                //echo 'success';
                //header("location:pdf/web/viewer.php?file=$folderName/view_pdf.pdf");
            }
    }
} else {
    $localPath = 'extract-here/' . $filePath;
}


 decrypt_my_file($localPath);
 
// set local path as permanent file path
$perma_folder = substr($localPath, 0, strrpos($localPath, "/"));
$perma_filename = substr($localPath, strrpos($localPath, "/"));



if ($ectns[1] != "html") {
    require './phpWordOffice/OfficeConverter.php';
    $path = "phpWordOffice/TEMP";
    if (!is_dir($path)) {
        mkdir($path, 0777, true);
    }


    //@sk(111518) - Update the file content in Editor
    if (isset($_POST['taskRemark'])) {
        //print_r($_POST);
        $fcontents = $_POST['taskRemark'];
        $fcontent = str_replace('img src="' . $path . '/', 'img src="', $fcontents);

        $fileName = strtotime(date('Y-m-d h:i:s')) . "_" . $fileName;
        $filename = str_replace(" ", "", $fileName);
        $filename2 = str_replace(".", "_", $filename);
        $encName = base64_encode($filename2);
        $filename1 = preg_replace("/[^A-Za-z0-9]/", "", $encName);
        $fnamepdf = $filename1 . '.html';
        //echo $path.'/'.$fnamepdf;
        $myfile = fopen($path . '/' . $fnamepdf, "w");
        $fFileByte = fwrite($myfile, $fcontent);

        if ($fFileByte) {

            if (isset($_POST['pdfcreate'])) {
                $filePdfName = explode(".", $rwFile['old_doc_name']);
                $fileNamePdfExt = $filePdfName[0] . ".pdf";
                $filePdfNamePath = explode(".", $filePath);
                $fileNamePdfExtPath = $filePdfNamePath[0] . ".pdf";
                $filePath = $fileNamePdfExtPath;
                include 'exportpdf.php';
                if (FTP_ENABLED) {
					$localPath = $folderName . '/' . str_replace("doc", "d", preg_replace('/[^A-Za-z0-9\-]/', '', $fileName)) . ".pdf";
                } else {
                $fn=basename($fileNamePdfExtPath);   
                $localPath = substr($localPath, 0, strrpos($localPath, "/")).'/'.$fn;             
                }
                exportPDF($fcontents, $localPath);
                // echo "run=".$path.'/'.$fnamepdf;
                $updateQry = mysqli_query($db_con, "update ".$con_table." set old_doc_name='$fileNamePdfExt',doc_extn='pdf',doc_path='$fileNamePdfExtPath' where doc_id='$id1'");
                
                //set action for log
                $action="Word Document saved as Pdf.";
            } else {
                copy($path . '/' . $fnamepdf, $localPath);
                //set action for log
                $action="Document updated.";
            }

            if (FTP_ENABLED) {
                require_once './classes/ftp.php';

                $ftp = new ftp();
                $ftp->conn("$fileserver", "$port", "$ftpUser", "$ftpPwd");

                //ROOT_FTP_FOLDER.'/'.$filePath;
              //  echo ROOT_FTP_FOLDER . '/' . $filePath;
                if ($ftp->put(ROOT_FTP_FOLDER . '/' . $filePath, $localPath)) {
                    // echo "sdd";  
                    if (isset($_POST['pdfcreate'])) {
                    //$iduser=urlencode(base64_encode($_SESSION[cdes_user_id]));
                    //echo '<script>alert("'.$lang['save_as_pdf_successfully'].'")</script>';
                    header("location:".$pdf_redirect);
                    }
                } else {
                    echo "error";
                }

                // $arr = $ftp->getLogData();
                //echo $DOCPATH;
                // if ($arr['error'] != ""){
                //  echo '<h2>Error:</h2>' . implode('<br />', $arr['error']);
                //}
            } else {
                //echo 'error';
                if (isset($_POST['pdfcreate'])) {
                   // $iduser=urlencode(base64_encode($_SESSION[cdes_user_id]));
                    //echo '<script>alert("'.$lang['save_as_pdf_successfully'].'")</script>';
                    header("location:".$pdf_redirect);
                    }
            }
                // set Log
                $log_query= mysqli_query($db_con, "insert into ". $log_table ." set ".$user_info."
                                          action_name='$action',
                                          start_date='$date',
                                          end_date='$date',
                                          system_ip='$host',
                                          doc_id='$id1'");
        }

        // change Temprary File.
        //file_put_contents($localPath, $fcontent);
        // Now Update the Permanent File.
        //echo "okk";
        //die;
        // Convert and overwrite file to destination
        //  $converter = new OfficeConverter($path."/",$localPath);
        //echo 'okk';
        //die;
        //$converter->convertTo($perma_filename);
        // Now the local path for  showing file in editor will be...
        echo '<script>alert("'.$lang['saved_successfully'].'")</script>';
        $localPath = $path . '/' . $fnamepdf;
    } else {

        $converter = new OfficeConverter($localPath, $path . "/");


//    require 'bootstrap.php';
//    $phpWord = new \PhpOffice\PhpWord\PhpWord();
//    $phpWord = \PhpOffice\PhpWord\IOFactory::load($localPath);
//    $htmlWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'HTML');



        $fileName = strtotime(date('Y-m-d h:i:s')) . "_" . $fileName;
        $filename = str_replace(" ", "", $fileName);
        $filename2 = str_replace(".", "_", $filename);
        $encName = base64_encode($filename2);
        $filename1 = preg_replace("/[^A-Za-z0-9]/", "", $encName);
        $fnamepdf = $filename1 . '.html';
        $converter->convertTo($fnamepdf);
        $localPath = "phpWordOffice/TEMP/" . $fnamepdf;
    }
    //echo $localPath;
    //die;
    ?>

    <?php
	$data = file_get_contents($localPath);

    //$content=$data;
    $content = str_replace('img src="', 'img src="' . $path . '/', $data);
    if (FTP_ENABLED) {
        unlink($localPath); //delete temp file after geting data
    }
    ?>


    <div style="height: 80%">
        <link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="assets/css/core.css" rel="stylesheet" type="text/css" />
        <script src="assets/js/bootstrap.min.js"></script>

        <form id="sbmt" method="post">
            <textarea class="form-control"  name="taskRemark" id="editor" ><?= $content ?></textarea>
            <button  name="submit" class="btn btn-primary pull-left m-t-10" style="margin-top: 15px;"> Save </button>
            <button  name="pdfcreate" class="btn btn-info pull-left m-t-10 m-l-5" style="margin-top: 15px;" name="pdfConvert">Save As Pdf</button>
        </form>
    </div>
     <script src="./assets/js/jquery.min.js"></script>
    <script src="./assets/plugins/tinymce/tinymce.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            if ($("#editor").length > 0) {
                tinymce.init({
                    selector: "textarea#editor",
                    theme: "modern",
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

    //@sk(151118) - Edit content Confirmation
        $("#edit_btn").click(function () {
            var r = confirm("Are You Sure You Want to edit?");
            if (r == true) {
                document.getElementById("sbmt").submit();
            } else {
                return false;
            }
        })
        $("#save_as_pdf").click(function () {
            $("#pdfchkval").val("1");
            debugger;
            var r = confirm("Are You Sure You Want to Save as pdf?");
            if (r == true) {

                document.getElementById("sbmt").submit();
            } else {
                return false;
            }
        })
    </script>
    <?php
    //unlink("TEMP/".$fileName.".html"); //delete temp file after geting data
}
  else {

    $data = file_get_contents($localPath);
    $content = $data;
    if (FTP_ENABLED) {
        unlink($localPath); //delete temp file after geting data
    }
//
    ?>

    <html>
        <head>
            <title><?= $fileName ?></title>
            <link href="./assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        </head>
        <body>
            <div class="container-fluid" style="background-color: #006dcc;">

                <div style="padding-top: 1px;padding: 1px;background-color:whitesmoke ">
                    <center><p><h3><?= $doc_old_name . "." . $doc_extn ?></h3></p></center>

                </div>
                <div class="col-md-10 " style="background-color: whitesmoke;height: 100%">

                    <textarea class="form-control"  name="taskRemark" id="editor" ><?= $content ?></textarea>




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
                                                                <i><?php echo '<strong>Action By: </strong>' . $rwUsr['first_name'] . ' ' . $rwUsr['last_name']; ?></i>
                                                                <br/>
            <?php echo '<strong>Action Time: </strong>' . date("j F, Y, H:i", strtotime($rwcomment['start_date'])); ?></span>
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
        <script src="./assets/js/jquery.min.js"></script>
        <script src="./assets/plugins/tinymce/tinymce.min.js"></script>
        <script type="text/javascript">
            $(document).ready(function () {
                if ($("#editor").length > 0) {
                    tinymce.init({
                        selector: "textarea#editor",
                        theme: "modern",
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
<?php } ?>