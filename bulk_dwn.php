<?php
                
                require_once './application/config/database.php';
                if (isset($_POST['bulkDownload'])) {
                    $rad = mysqli_real_escape_string($db_con,$_POST['raddwn']);
                    $slid = mysqli_real_escape_string($db_con,$_POST['slid']);
                    $metaName = '';
                    $header1 = '';
                    if ($rad == 'all') {
                        
                        $meta = mysqli_query($db_con, "select * from tbl_metadata_to_storagelevel where sl_id='$slid'");
                        while ($rwMeta = mysqli_fetch_assoc($meta)) {
                            $metan = mysqli_query($db_con, "select field_name from tbl_metadata_master where id='$rwMeta[metadata_id]'");
                            $rwMetan = mysqli_fetch_assoc($metan);
                            if (empty($metaName)) {
                                $metaName = ',`' . $rwMetan['field_name'] . '`';
                            } else {
                                $metaName .= ',`' . $rwMetan['field_name'] . '`';
                            }
                        }
                        //print_r($metaName);
                        //$metaName= implode(",", $metaName);
                        $download = mysqli_query($db_con, "select old_doc_name as filename,doc_extn as Extension $metaName,uploaded_by,dateposted from tbl_document_master where doc_name='$slid'");
                        //$fields = mysqli_num_fields ( $exportData );
                        $error = 'http://www.google.com';
                        $zip = new ZipArchive();
                        $zip_name ="dmsfiles.zip"; // Zip name
                        $zip->open($zip_name, ZipArchive::CREATE);
                        while($row = mysqli_fetch_assoc($download)) {
                            echo $path =$row['doc_path'].".".$row['doc_extn'];
                            $path_zip="ezeefile_local/zip/".file_get_contents($path);
                            if (file_exists($path)) {
                                $zip->addFile($path_zip,file_get_contents($path));
                                //$zip->addFromString(basename($path_zip), file_get_contents($path));
                            } else {
                                echo"file does not exist";
                            }
                        }
                       $zip->close();
                       header('Content-Type: application/zip');
                       header('Content-disposition: attachment; filename='.$zipname);
                       header('Content-Length: ' . filesize($zipname));
                       readfile($zipname); 
                    }
                }
                ?>
