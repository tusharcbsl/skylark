<?php
if (isset($_POST['exportData'])) {

                $docIds = $_POST['export_doc_ids'];
                $selectFormat = trim($_POST['select_Fm']);
                $slId = $_POST['id'];

                if ($selectFormat == "excel") {

                    $meta = mysqli_query($db_con, "select * from tbl_metadata_to_storagelevel where sl_id='$slId'");
                    while ($rwMeta = mysqli_fetch_assoc($meta)) {
                        $metan = mysqli_query($db_con, "select field_name from tbl_metadata_master where id='$rwMeta[metadata_id]'");
                        $rwMetan = mysqli_fetch_assoc($metan);
                        if (empty($metaName)) {
                            $metaName = '`' . $rwMetan['field_name'] . '`';
                        } else {
                            $metaName .= ',`' . $rwMetan['field_name'] . '`';
                        }
                    }
                    $exportData = mysqli_query($db_con, "select filename,$metaName,uploaded_by,dateposted from tbl_document_master where doc_id in($docIds) and doc_name='$slId'");
                    //$fields = mysqli_num_fields ( $exportData );

                    while ($fields = mysqli_fetch_field($exportData)) {
                        $header1 .= $fields->name . "\t";
                    }
                    while ($row = mysqli_fetch_assoc($exportData)) {

                        $line = '';
                        foreach ($row as $key => $value) {
                            if ((!isset($value) ) || ( $value == "" ) || ($value == " ") || ($value == NULL)) {
                                $value = "--\t";
                            } else {
                                if ($key == 'uploaded_by') {
                                    $dataOwner = mysqli_fetch_assoc(mysqli_query($db_con, "select first_name,last_name from tbl_user_master where user_id='$value'"));
                                    $name = $dataOwner['first_name'] . ' ' . $dataOwner['last_name'];
                                    if ((!isset($name) ) || ( $name == "" )) {
                                        $value = "\t";
                                    } else {
                                        $value = str_replace('"', '""', $name);
                                        $value = '"' . $value . '"' . "\t";
                                    }
                                } else {
                                    $value = str_replace('"', '""', $value);
                                    $value = '"' . $value . '"' . "\t";
                                }
                            }

                            $line .= $value;
                        }
                        $result1 .= trim($line) . "\n";
                    }
                    $result1 = str_replace("\r", "", $result1);

                    if ($result1 == "") {
                        //$result1 = "\nNo Record(s) Found!\n";                        
                    }
                    header("Content-type: application/octet-stream");
                    header("Content-Disposition: attachment; filename=export.xls");
                    header("Pragma: no-cache");
                    header("Expires: 0");
                    print "$header1\n$result1";
                } elseif ($selectFormat == "csv") {
                    $meta = mysqli_query($db_con, "select * from tbl_metadata_to_storagelevel where sl_id='$slId'");
                    while ($rwMeta = mysqli_fetch_assoc($meta)) {
                        $metan = mysqli_query($db_con, "select field_name from tbl_metadata_master where id='$rwMeta[metadata_id]'");
                        $rwMetan = mysqli_fetch_assoc($metan);
                        if (empty($metaName)) {
                            $metaName = '`' . $rwMetan['field_name'] . '`';
                        } else {
                            $metaName .= ',`' . $rwMetan['field_name'] . '`';
                        }
                    }
                    $exportData = mysqli_query($db_con, "select filename,$metaName,uploaded_by,dateposted from tbl_document_master where doc_id in($docIds) and doc_name='$slId'");
                    //$fields = mysqli_num_fields ( $exportData );

                    while ($fields = mysqli_fetch_field($exportData)) {
                        $header1 .= $fields->name . ",";
                    }
                    while ($row = mysqli_fetch_assoc($exportData)) {

                        $line = '';
                        foreach ($row as $key => $value) {
                            if ((!isset($value) ) || ( $value == "" ) || ($value == " ") || ($value == NULL)) {
                                $value = "-- ,";
                            } else {
                                if ($key == 'uploaded_by') {
                                    $dataOwner = mysqli_fetch_assoc(mysqli_query($db_con, "select first_name,last_name from tbl_user_master where user_id='$value'"));
                                    $name = $dataOwner['first_name'] . ' ' . $dataOwner['last_name'];
                                    if ((!isset($name) ) || ( $name == "" )) {
                                        $value = ",";
                                    } else {
                                        $value = str_replace('"', '""', $name);
                                        $value = '"' . $value . '"' . ",";
                                    }
                                } else {
                                    $value = str_replace('"', '""', $value);
                                    $value = '"' . $value . '"' . ",";
                                }
                            }

                            $line .= $value;
                        }
                        $result1 .= trim($line) . "\n";
                    }
                    $result1 = str_replace("\r", "", $result1);
                    header("Content-type: application/octet-stream");
                    header("Content-Disposition: attachment; filename=export.csv");
                    header("Pragma: no-cache");
                    header("Expires: 0");
                    print "$header1\n$result1";
                } elseif ($selectFormat = "pdf") {
                    $meta = mysqli_query($db_con, "select * from tbl_metadata_to_storagelevel where sl_id='$slId'");
                    while ($rwMeta = mysqli_fetch_assoc($meta)) {
                        $metan = mysqli_query($db_con, "select field_name from tbl_metadata_master where id='$rwMeta[metadata_id]'");
                        $rwMetan = mysqli_fetch_assoc($metan);
                        if (empty($metaName)) {
                            $metaName = '`' . $rwMetan['field_name'] . '`';
                        } else {
                            $metaName .= ',`' . $rwMetan['field_name'] . '`';
                        }
                    }
                    $exportData = mysqli_query($db_con, "select filename,$metaName,uploaded_by,dateposted from tbl_document_master where doc_id in($docIds) and doc_name='$slId'");
                    //$fields = mysqli_num_fields ( $exportData );
                    $header1 = "<table><thead><tr>";
                    while ($fields = mysqli_fetch_field($exportData)) {
                        $header1 .= "<th>" . $fields->name . "</th>";
                    }
                    $header1 .= "</thead></tr>";
                    $result1 = "<tbody>";
                    while ($row = mysqli_fetch_assoc($exportData)) {

                        $line = '<tr>';
                        foreach ($row as $key => $value) {
                            if ((!isset($value) ) || ( $value == "" ) || ($value == " ") || ($value == NULL)) {
                                $value = "<td>--</td>";
                            } else {
                                if ($key == 'uploaded_by') {
                                    $dataOwner = mysqli_fetch_assoc(mysqli_query($db_con, "select first_name,last_name from tbl_user_master where user_id='$value'"));
                                    $name = $dataOwner['first_name'] . ' ' . $dataOwner['last_name'];
                                    if ((!isset($name) ) || ( $name == "" )) {
                                        $value = "<td>--</td>";
                                    } else {
                                        $value = "<td>" . $name . "</td>";
                                    }
                                } else {

                                    $value = "<td>" . $value . "</td>";
                                }
                            }

                            $line .= $value;
                        }
                        $result1 .= trim($line) . "</tr>";
                    }
                    $result1 .= "</tbody></table>";
                    $data = "<html>";
                    $data .= "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=Windows-1252\">";
                    $data .= "<body>";
                    $data .= $result1;
                    $data .= "</body>";
                    $data .= "</html>";
                   require('application/ajax/fpdfpdf_html.php');
                    $pdf = new PDF_HTML();
                    $pdf->AddPage();
                    $pdf->SetFont('Arial');
                    $pdf->WriteHTML($data);
                    $pdf->Output();
                    header("Content-Type: application/pdf");
                    header("Cache-Control: no-cache");
                    header("Accept-Ranges: none");
                    header("Content-Disposition: attachment; filename=\"export.pdf\"");
                } elseif ($selectFormat=="word") {
                    
                    $meta = mysqli_query($db_con, "select * from tbl_metadata_to_storagelevel where sl_id='$slId'");
                    while ($rwMeta = mysqli_fetch_assoc($meta)) {
                        $metan = mysqli_query($db_con, "select field_name from tbl_metadata_master where id='$rwMeta[metadata_id]'");
                        $rwMetan = mysqli_fetch_assoc($metan);
                        if (empty($metaName)) {
                            $metaName = '`' . $rwMetan['field_name'] . '`';
                        } else {
                            $metaName .= ',`' . $rwMetan['field_name'] . '`';
                        }
                    }
                    $exportData = mysqli_query($db_con, "select filename,$metaName,uploaded_by,dateposted from tbl_document_master where doc_id in($docIds) and doc_name='$slId'");
                     while ($fields = mysqli_fetch_field($exportData)) {
                        $header1 .= $fields->name . "\t";
                    }
                    while ($row = mysqli_fetch_assoc($exportData)) {
                        $line = '';
                        foreach ($row as $key => $value) {
                            if ((!isset($value) ) || ( $value == "" ) || ($value == " ") || ($value == NULL)) {
                                $value = "--\t";
                            } else {
                                if ($key == 'uploaded_by') {
                                    $dataOwner = mysqli_fetch_assoc(mysqli_query($db_con, "select first_name,last_name from tbl_user_master where user_id='$value'"));
                                    $name = $dataOwner['first_name'] . ' ' . $dataOwner['last_name'];
                                    if ((!isset($name) ) || ( $name == "" )) {
                                        $value = "\t";
                                    } else {
                                        $value = str_replace('"', '""', $name);
                                        $value = '"' . $value . '"' . "\t";
                                    }
                                } else {
                                    $value = str_replace('"', '""', $value);
                                    $value = '"' . $value . '"' . "\t";
                                }
                            }

                            $line .= $value;
                        }
                        $result1 .= trim($line) . "\n";
                    }
                    $result1 = str_replace("\r", "", $result1);
                      print "$header1\n$result1";
                    header("Content-type: application/x-ms-download");
                    header("Content-Disposition: attachment; filename=export.doc");
                    header("Pragma: no-cache");
                    header("Expires: 0");
                    
                }
            }
			?>