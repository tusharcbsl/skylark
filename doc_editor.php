<?php
//require 'sessionstart.php';
//require_once './application/config/database.php';

require_once 'loginvalidate.php';
require_once 'application/pages/function.php';
require_once 'classes/fileManager.php';
//for user role

$start_date = $date;

$chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$_SESSION[cdes_user_id]', user_ids) > 0") or die('Error:' . mysqli_error($db_con));

$rwgetRole = mysqli_fetch_assoc($chekUsr);
$uid = base64_decode(urldecode($_GET['i']));
if ($uid != $_SESSION['cdes_user_id']) {
// header('Location:./index');
}
$ses_val = $_SESSION;
if (isset($_SESSION['cdes_user_id'])) {
    $LangQuery = mysqli_query($db_con, "SELECT * FROM `tbl_user_master` WHERE user_id='$_SESSION[cdes_user_id]'") or die('error : ' . mysqli_error($db_con));
    $LangRow = mysqli_fetch_array($LangQuery);
    if (!empty($LangRow['lang'])) {
        $file = "./" . $LangRow['lang'] . ".json";
    } else {
        $file = "./English.json";
    }
}
$data = file_get_contents($file);
$lang = json_decode($data, true);
$id1 = base64_decode(urldecode($_GET['id'])); //doc_id
$docId = $id1;
//check shared document time exceed.
$docexp = base64_decode(urldecode($_GET['docexp']));
if (isset($_GET['docexp']) && ($docexp == 'docexp')) {
    $shareDoc = mysqli_query($db_con, "SELECT * FROM `tbl_document_share` WHERE doc_ids='$id1'");
    if (mysqli_num_rows($shareDoc) < 1) {
        ?>
        <script>
            alert("Sorry, Document share time exceed from valid time.");
            window.close();
        </script>
        <?php
    }
}

//$id = base64_decode(urldecode($_GET['id']));  //doc asign id
$status = 0;
$checkfileLockqry = mysqli_query($db_con, "SELECT * FROM `tbl_locked_file_master` WHERE doc_id='$id1' and is_active='1'");
if (mysqli_num_rows($checkfileLockqry) > 0) {
    $checkfileLock = mysqli_query($db_con, "SELECT * FROM `tbl_locked_file_master` WHERE doc_id='$id1' and is_active='1' and user_id='$_SESSION[cdes_user_id]'");
    if (mysqli_num_rows($checkfileLock) > 0) {
        $status = 1;
    } else {
        $status = 0;
    }
} else {
    $status = 1;
}
/* ------------lock file end---------------- */
if ($status == 1) {
    if ($_GET['chk'] == "rw") {
        mysqli_set_charset($db_con, "utf8");
        $file = mysqli_query($db_con, "select doc_name, doc_path, doc_extn, old_doc_name,File_Number from tbl_document_reviewer where doc_id='$id1'") or die('error' . mysqli_error($db_con));
    } else {

        mysqli_set_charset($db_con, "utf8");
        $file = mysqli_query($db_con, "select doc_name, filename, doc_path, doc_extn, old_doc_name,checkin_checkout from tbl_document_master where doc_id='$id1' and flag_multidelete='1'") or die('error' . mysqli_error($db_con));
    }

    $rwFile = mysqli_fetch_assoc($file);
    $fileName = $rwFile['old_doc_name'];
    $doc_old_name = $rwFile['old_doc_name'];
    if (strpos($doc_old_name, ".")) {
        $fname = preg_replace('/.[^.]*$/', '', $doc_old_name);
    } else {
        $fname = $doc_old_name;
    }
    $filePath = $rwFile['doc_path'];
    $slid = $rwFile['doc_name'];
    $doc_extn = $rwFile['doc_extn'];
    $CheckinCheckout = 0;
    
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

    //here

    $sql = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$slid'") or die('Error');
    $pass_check = mysqli_fetch_assoc($sql);
    $pass_word = $pass_check['password'];
    $errorMsg = false;

    if (isset($_POST['checkpass'])) {

        $pass = $_POST['password'];
        unset($_SESSION['pass']);
        if (SHA1($pass) == $pass_word) {
            $_SESSION['pass'] = $pass_word;
        } else {
            $errorMsg = 'Password is not valid';
        }
    }


    if ($ectns[1] != "html") {
        $sql = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$slid'") or die('Error');
        $pass_check = mysqli_fetch_assoc($sql);

        if (($_SESSION['pass'] != $pass_word) && ($pass_check['is_protected'] == 1 || $pass_check['is_protected'] == 2)) { 
            ?>
            <html>
                <head>
                    <meta charset="utf-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1">
                    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
                    <link rel="apple-touch-icon" sizes="180x180" href="assets/images/favicons/apple-touch-icon.png">
                    <link rel="icon" type="image/png" href="assets/images/favicons//favicon-32x32.png" sizes="32x32">
                    <link rel="icon" type="image/png" href="assets/images/favicons//favicon-16x16.png" sizes="16x16">
                    <link rel="manifest" href="assets/images/favicons//manifest.json">
                    <link rel="mask-icon" href="assets/images/favicons//safari-pinned-tab.svg" color="#5bbad5">
                    <script src="./assets/js/jquery.min.js"></script>

                    <style>
                        #mask {
                            position:absolute;
                            left:0;
                            top:0;
                            z-index:9000;
                            background-color:grey;
                            display:none;
                        } 

                        #boxes .window {
                            position:absolute;
                            left:0;
                            top:0;
                            width:440px;
                            height:850px;
                            display:none;
                            z-index:9999;
                            padding:20px;
                            border-radius: 5px;
                            text-align: center;
                        }
                        #boxes #dialog {
                            width:550px; 
                            height:auto;
                            padding: 10px 10px 10px 10px;
                            background-color:#ffffff;
                            font-size: 15pt;
                        }

                        .agree:hover{
                            background-color: #D1D1D1;
                        }
                        .popupoption:hover{
                            background-color:#D1D1D1;
                            color: green;
                        }
                        .popupoption2:hover{
                            color: red;
                        }
                    </style>
                </head>
                <body>

                    <div id="boxes">
                        <div style="top: 50%; left: 50%; display: none;" id="dialog" class="window">
                            <form method="post">
                                <div class="modal-header">
                                    <h4 class="modal-title">Please enter password</h4>
                                </div>

                                <input type="password" class="form-control" name="password" id="password" autocomplete="off" autofocus >

                                <div class="modal-footer">
                                    <input type="submit" class="btn btn-danger" name="checkpass" id="enter_btn"  value="Enter" >


                                </div>
                            </form>
                        </div>                                                          

                        <div style="width: 2478px; font-size: 32pt; color:white; height: 1202px; display: none; opacity: 0.4;" id="mask"></div>

                    </div>

                </body>
            </html>
        <?php } ?>
        <!DOCTYPE html>
		<html>
    		<head>
    			<title></title>
                <meta charset="utf-8">
                <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
                <meta name="google" content="notranslate">
                <meta http-equiv="X-UA-Compatible" content="IE=edge">

                <link rel="apple-touch-icon" sizes="180x180" href="assets/images/favicons/apple-touch-icon.png">
                <link rel="icon" type="image/png" href="assets/images/favicons//favicon-32x32.png" sizes="32x32">
                <link rel="icon" type="image/png" href="assets/images/favicons//favicon-16x16.png" sizes="16x16">
                <link rel="manifest" href="assets/images/favicons//manifest.json">
                <link rel="mask-icon" href="assets/images/favicons//safari-pinned-tab.svg" color="#5bbad5">
    			<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script> -->


                <link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
                <script type="text/javascript" src="assets/js/jquery.min.js"></script>
                <link href="viewer-pdf/modal.css" rel="stylesheet" type="text/css" />

                <script src="assets/js/bootstrap.min.js"></script>
                <!-- This snippet is used in production (included from viewer.html) -->
                <link href="assets/css/imageviewer.css" rel="stylesheet" type="text/css" />
                <link href="assets/plugins/sweetalert2/sweetalert2.min.css" rel="stylesheet" type="text/css">
                <link href="assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css" rel="stylesheet">
    		</head>
            <style>
                .w-78 {
                    width: 77%;
                    position: fixed;
                    left: 22%;
                }
            </style>
    		<body>
    			<?php 

				if(isset($_GET['dcid']) and $_GET['dcid']!='')
				{ 			
				$dcid = base64_decode(urldecode($_GET['dcid']));
				$perm = base64_decode(urldecode($_GET['perm']));
				$type = base64_decode(urldecode($_GET['type']));
                // echo $_GET['dcid'];exit;
				$localPath = base64_decode(urldecode($_GET['filepath']));
				if($perm=="reader")
				{
					$perm="preview";
				}
				else
				{
					$perm="edit";			
				}
				if($type=="ppt" || $type=="pptx"){
					$doc = "presentation";
				}elseif($type=="xls" || $type=="xlsx"){
					$doc = "spreadsheets";
				}elseif($type=="doc" || $type=="docx"){
					$doc = "document";
				}else{
		    		header('Location: index');
				}
				?>

				<iframe src="https://docs.google.com/<?=$doc?>/d/<?=$dcid."/".$perm?>" class="w-78" style="height: 100vh;z-index: 99;" class="w-78" ></iframe>
				<SCRIPT language=JavaScript>
				 /*window.onbeforeunload = function () {
						$.post("docs-delete.php", {dcid: "<?php echo $dcid; ?>",filepath:"<?php echo './' . $localPath; ?>",perm:"<?php echo $perm; ?>"}, function (result) {
						});
						return;
					};*/
				</SCRIPT>	
				<?php 
				} 
				?>
                
    		</body>
		</html>
        <?php
        //unlink("TEMP/".$fileName.".html"); //delete temp file after geting data
    } ?>
    <?php require_once 'checkin-checkout-html.php'; ?>
    <?php require_once 'checkin-checkout-js.php'; ?>    
    <?php require_once 'checkin-checkout-php.php'; ?>
<?php } else { ?>
    <script>
        alert("File Is Locked Please Contact To Administrator");
        window.close();
    </script>
<?php } ?>