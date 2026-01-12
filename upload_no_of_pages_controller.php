<?php
    require_once './application/config/database.php';
    // print_r($_FILES);
    // print_r($_POST);

    if ( isset($_POST['action']) && $_POST['action'] == "fileupload" ) {
        // echo 'ok';
        $file_name = $_FILES['files']['name'];
        $temp_name = $_FILES['files']['tmp_name'];

        $columns = mysqli_query($db_con, "SHOW COLUMNS FROM `tbl_document_master`");
        $col = array();
        while ($rwCol = mysqli_fetch_array($columns)) {
            $col[] = $rwCol['Field'];
        }

        // print_r($col);
        // exit;

        $chunk_file = explode('.', $file_name);
        $extension = end($chunk_file);
        $response = array();
        if ($extension == 'csv') {
            if ( ($handle = fopen($temp_name, "r")) !== false ) {
                $i = 0;
                
                $meta_data = array();
                while ( ($row = fgetcsv($handle, 1000, ",")) !== false ) {
                    if ($i == 0) {
                        $i++;
                        for($sheet_col = 1; $sheet_col < count($row); $sheet_col++) {
                            // echo 'ok';
                            // echo $row[$sheet_col].',';
                            $meta_data[] = $row[$sheet_col];
                        }
                        // echo 'okg';
                        // print_r($meta_data);
                        continue;
                    }
                    // exit;


                    if ( !empty($_POST['storage']) ) {
                        $storage = mysqli_real_escape_string($db_con, $_POST['storage']);
                        $sql = "SELECT * FROM tbl_document_master WHERE doc_name = '".$storage."' AND old_doc_name = '".$row[0]."'";
                        $query = mysqli_query($db_con, $sql);
                        if ( mysqli_num_rows($query) == 1 ) {

                            $k = 0;
                            $q = "";
                            for($cl = 1; $cl < count($row); $cl++) {
                                // echo $k.',';
                                if (in_array($meta_data[$k], $col)) {
                                    $q .= $meta_data[$k]." = '".$row[$cl]."',";
                                }
                                $k++;
                            }
                            
                            $qu = rtrim($q, ',');
                            $sql = "UPDATE tbl_document_master SET $qu WHERE old_doc_name = '".$row[0]."' ";
                            $q1 = mysqli_query($db_con, $sql);
                            
                            if ($q1) {
                                array_push($response, array('status' => true, 'msg' => $row[0].' file has been updated' ));
                            }
                            else {
                                array_push($response, array('status' => false, 'msg' => 'Something wrong in '.$row[0] ));
                            }
                        }
                        elseif(mysqli_num_rows($query) <= 0) {
                            array_push($response, array('status' => false, 'msg' => $row[0].' file not found ' ));
                        }
                        elseif(mysqli_num_rows($query) > 1) {
                            array_push($response, array('status' => false, 'msg' => $row[0].' file has been multiple' ));
                        }
                    }
                    else {
                        $sql = "SELECT * FROM tbl_document_master WHERE old_doc_name = '".$row[0]."'";
                        $query = mysqli_query($db_con, $sql);
                        if ( mysqli_num_rows($query) == 1 ) {
                            
                            $k = 0;
                            $q = "";
                            for($cl = 1; $cl < count($row); $cl++) {
                                // echo $k.',';
                                if (in_array($meta_data[$k], $col)) {
                                    $q .= $meta_data[$k]." = '".$row[$cl]."',";
                                }
                                $k++;
                            }
                            
                            $qu = rtrim($q, ',');
                            $sql = "UPDATE tbl_document_master SET $qu WHERE old_doc_name = '".$row[0]."' ";
                            $q1 = mysqli_query($db_con, $sql);
                            if ($q1) {
                                array_push($response, array('status' => true, 'msg' => $row[0].' file has been updated' ));
                            }
                            else {
                                array_push($response, array('status' => false, 'msg' => 'Something wrong in '.$row[0] ));
                            }
                        }
                        elseif(mysqli_num_rows($query) <= 0) {
                            array_push($response, array('status' => false, 'msg' => $row[0].' file not found ' ));
                        }
                        elseif(mysqli_num_rows($query) > 1) {
                            array_push($response, array('status' => false, 'msg' => $row[0].' file has been multiple' ));
                        }
                    }
                    $i++;
                }
            }
        }
        else {
            array_push($response, array('status' => false, 'msg' => 'Please select only csv file !' ));
        }
        echo json_encode($response);
    }
?>