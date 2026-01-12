<?php
    require_once './loginvalidate.php';
    require_once './application/config/database.php';
    require_once './application/pages/function.php';
	require_once './classes/ftp.php';
	require_once './classes/fileManager.php';
	$fileManager = new fileManager();

?>
<!DOCTYPE html>
<html>
    <head>
    <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    </head>
    <body class="bg-success">
        <div class="container" style="margin-top:5rem">
            <h4>Welcome to ftp synchronize system</h4>
            <p>Here you can transfer all files from local system to FTP, By clicking on Transfer button</p>
            <form action="" method="post">
                <input type="hidden" name="action" value="transfer_ftp">
                <button type="submit" name="transfer_ftp" class="btn btn-success">Transfer to FTP</button>
                <a href="ftp-scheduler" name="transfer_ftp" class="btn btn-danger">Reset</a>
            </form>
            <p></p>
            <?php

                function create_log_files( $content ) {
                    $filename = "move-ftp-logs.txt";
                    $file = fopen($filename,"a");
                    fwrite($file, $content);
                    fclose($file);
                    chmod($file,0777);
                }

                if (isset($_POST['action']) && $_POST['action'] == 'transfer_ftp') {
                    if (FTP_ENABLED) {
                        $sql = "SELECT doc_id,doc_path FROM tbl_document_master";
                        $query = mysqli_query($db_con, $sql);

                        $is_upload = FALSE;
                        $success_file = 0;
                        $failed_file = 0;
                        $failed_reason = array();
                        if (mysqli_num_rows($query) > 0) {
                            while($local_file = mysqli_fetch_assoc($query)) {
                                
                                $destination_path = ROOT_FTP_FOLDER.'/'.$local_file['doc_path'];
                                $source_path = 'extract-here/'.$local_file['doc_path'];

                                if(file_exists($source_path)) {

                                    $fileManager->conntFileServer();
                                    if ($fileManager->syncUpload($source_path, $destination_path)) {
                                        $is_upload = TRUE;
                                        $success_file++;
                                        create_log_files( date('d M Y h:i:s')." -> Success -> ".$source_path.PHP_EOL );
                                        $update_sql = "UPDATE tbl_document_master SET ftp_done = '1' WHERE doc_id = '".$local_file['doc_id']."'";
                                        mysqli_query($db_con, $update_sql);
                                        unlink($source_path);
                                        // echo '<p class="text-success">'.$local_file['doc_path'].' has been successfully uploaded on ftp server</p>';
                                    }
                                    else {
                                        create_log_files( date('d M Y h:i:s')." -> Failed -> ".$source_path.PHP_EOL );
                                        $failed_file++;
                                        // echo '<p class="text-danger">'.$local_file['doc_path'].' is not uploaded on ftp server</p>';
                                    }
                                }
                                else {
                                    create_log_files( date('d M Y h:i:s')." -> Failed -> ".$source_path.PHP_EOL );
                                    $failed_file++;
                                    // echo '<p class="text-danger">'.$local_file['doc_path'].' is not found in local system</p>';
                                }
                            }
                        }
                        else {
                            echo 'There is no file to synchronize !';
                        }
                    }
                    else {
                        echo '<p class="text-danger">FTP is not enabled !</p>';
                    }

                    if($is_upload) {
                        echo '<p class="text-success">'.$success_file.' File has been uploaded</p>';
                        echo '<p class="text-danger">'.$failed_file.' File has been failed</p>';
                        echo '<form><button type="submit" name="d" value="download_file" class="btn btn-success">Download</button></form>';
                    }
                }
                if (!empty($_GET['d']) && $_GET['d'] == "download_file") {
                    $file = "move-ftp-logs.txt";
                    header("Content-Description: File Transfer");
                    header("Content-Type: application/octet-stream");
                    header("Content-Disposition: attachment; filename=\"". basename($file) ."\"");
                    readfile ($file);
                    exit();
                }
            ?>
        </div>
    </body>
</html>


