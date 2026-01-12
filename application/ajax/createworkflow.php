<?php
require_once('./../../sessionstart.php');
require './../config/database.php';
//sk@201218 : for multilingual
if (isset($_SESSION['lang'])) {
    $file = '../../' . $_SESSION['lang'] . ".json";
} else {
    $file = "../../English.json";
}
$data = file_get_contents($file);
$lang = json_decode($data, true); //end

if (!empty($_POST['workflowName']) && !empty($_POST['token'])) {
    // print_r($_POST);
    // die('de');
    $workflowName = mysqli_real_escape_string($db_con, $_POST['workflowName']);
    $workflowName = preg_replace("/[^a-zA-Z_ ]/", "", $workflowName);

    $checkWrkFlwName = mysqli_query($db_con, "select workflow_name from tbl_workflow_master where FIND_IN_SET('$workflowName', workflow_name)") or die('Error: ' . mysqli_error($db_con));

    if (mysqli_num_rows($checkWrkFlwName) == 1) { //check duplicate name of workflow
        echo '<script>taskFailed("addWorkflow","Workflow Already Exist !");</script>';
    } else {


        if (!empty($_POST['workDesc'])) {

            $workflowDesc = xss_clean($_POST['workDesc']);
            $workflowDesc = mysqli_real_escape_string($db_con, $workflowDesc);
        }
        if (!empty($_POST['groups'])) {

            $workflowgroups = str_replace(' ', '', $_POST['groups']);
        }
        if (!empty($_POST['formRequire'])) {

            $formReq = $_POST['formRequire'];
        } else {
            $formReq = 0;
        }
        if (!empty($_POST['pdfreq'])) {
            $pdfreq = $_POST['pdfreq'];
        } else {
            $pdfreq = 0;
        }
        if (!empty($_POST['rfi'])) {
            $formtype = $_POST['rfi'];
        } else {
            $formtype = 0;
        }
        if ($formReq == 1) {
            $data = $_REQUEST['data'];


            $data = json_decode($data, true);
            if (!empty($data)) {
                if ($data[0]['type'] == "header") {

                    $formname = $data[0]['label'];
                } else {
                    $formname = mysqli_real_escape_string($db_con, $_POST['workflowName']);
                }
                //            $formNameChk= mysqli_query($db_con, "select * from tbl_form_master where form_name='$formname'") or die("Error Validate FormName:".mysqli_error($db_con));
                //            if(mysqli_num_rows($formNameChk)==0){
                $insert = mysqli_query($db_con, "insert into  tbl_form_master(`form_name`) values('$formname')") or die("Error occurs:" . mysqli_error($db_con));
                $id = mysqli_insert_id($db_con);
                $flag = 0;
                if ($insert) {
                    $wfname = str_replace(' ', '_', mysqli_real_escape_string($db_con, $_POST['workflowName']));
                    $tblworkflow = "tbl_wf_" . $wfname;
                    $tblworkflow = preg_replace("/[^a-zA-Z_ ]/", "", $tblworkflow);

                    $createqry = mysqli_query($db_con, "CREATE TABLE " . $tblworkflow . "(tbl_id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,user_id INT NOT NULL,ticket_id VARCHAR(100) NULL)") or die("Table Create Error occurs:" . mysqli_error($db_con));
                    if ($createqry) {
                        foreach ($data as $key => $value) {

                            $label = isset($value['label']) ? $value['label'] : Null;
                            $name = isset($value['name']) ? $value['name'] : Null;
                            $name = mysqli_real_escape_string($db_con, $name);
                            $name = str_replace('-', '_', $name);
                            $name = preg_replace("/[^a-zA-Z0-9_]/", "", $name); //filter name
                            $subtype = isset($value['subtype']) ? $value['subtype'] : Null;
                            $type = isset($value['type']) ? $value['type'] : Null;
                            $class = isset($value['className']) ? $value['className'] : Null;
                            $multiple = isset($value['multiple']) ? $value['multiple'] : Null;
                            $required = isset($value['required']) ? $value['required'] : Null;
                            $placeholder = isset($value['placeholder']) ? $value['placeholder'] : Null;
                            $values = isset($value['value']) ? $value['value'] : Null;
                            $values = strip_tags($values, '<table><td><tr><th><ol><li><b><p>');

                            $inline = isset($value['inline']) ? $value['inline'] : 1;
                            $maxlength = isset($value['maxlength']) ? $value['maxlength'] : 4000;
                            $id = $id;
                            if (!empty($name)) {

                                $prefixname = "wf_" . $name;
                            }
                            $sqlqry = mysqli_query($db_con, "insert into  tbl_form_attribute(fid,label,name,type,class,multiple_files,required,placeholder,value,maxlength,inline,subtype) values('$id','$label','$prefixname','$type','$class','$multiple','$required','$placeholder','$values','$maxlength','$inline','$subtype')") or die("error occurs:" . mysqli_error($db_con));
                            $selectid = mysqli_insert_id($db_con);
                            if ($sqlqry) {
                                if ($type == "select") {
                                    $values = isset($value['values']) ? $value['values'] : Null;
                                    $required = 0;
                                    foreach ($values as $options) {
                                        $label = $options['label'];
                                        $values = $options['value'];
                                        $selected = isset($options['selected']) ? $options['selected'] : Null;
                                        if ($selected == 1) {
                                            $selected = "true";
                                        } else {
                                            $selected = "false";
                                        }
                                        $type = "option";
                                        $sql = mysqli_query($db_con, "insert into  tbl_form_attribute(fid,label,name,type,class,multiple_files,required,placeholder,value,maxlength,dependency_id,selected) values('$id','$label','$name','$type','$class','$multiple','$required','$placeholder','$values','$maxlength','$selectid','$selected')") or die("Error occurs:" . mysqli_error($db_con));
                                    }
                                }
                                if ($type == "radio-group") {
                                    $values = isset($value['values']) ? $value['values'] : Null;
                                    $required = 0;
                                    foreach ($values as $radio) {
                                        $label = $radio['label'];
                                        $values = $radio['value'];
                                        $selected = isset($radio['selected']) ? $radio['selected'] : Null;
                                        if ($selected == 1) {
                                            $selected = "true";
                                        } else {
                                            $selected = "false";
                                        }

                                        $sql = mysqli_query($db_con, "insert into  tbl_form_attribute(fid,label,name,type,class,multiple_files,required,placeholder,value,maxlength,dependency_id,selected) values('$id','$label','$name','$type','$class','$multiple','$required','$placeholder','$values','$maxlength','$selectid','$selected')") or die("Error occurs:" . mysqli_error($db_con));
                                    }
                                }
                                if ($type == "checkbox-group") {
                                    $values = isset($value['values']) ? $value['values'] : Null;
                                    $required = 0;
                                    foreach ($values as $checkbox) {
                                        $label = $checkbox['label'];
                                        $values = $checkbox['value'];
                                        $selected = isset($checkbox['selected']) ? $checkbox['selected'] : Null;
                                        if ($selected == 1) {
                                            $selected = "true";
                                        } else {
                                            $selected = "false";
                                        }

                                        $sql = mysqli_query($db_con, "insert into  tbl_form_attribute(fid,label,name,type,class,multiple_files,required,placeholder,value,maxlength,dependency_id,selected) values('$id','$label','$name','$type','$class','$multiple','$required','$placeholder','$values','$maxlength','$selectid','$selected')") or die("Error occurs:" . mysqli_error($db_con));
                                    }
                                }
                            } else {
                                mysqli_rollback($db_con);
                            }
                            if (!empty($name)) {

                                if ($type == "textarea") {
                                    $type = "text";
                                } else {
                                    $type = "VARCHAR(255)";
                                }

                                $name = "wf_" . $name;
                                $alterqry = mysqli_query($db_con, "ALTER TABLE " . $tblworkflow . " ADD  `" . $name . "` " . $type);
                            }
                        }
                    }
                    //                 $table_coloums= implode(",",  $names);
                    //                 $createqry= mysqli_query($db_con, "CREATE TABLE ".$tblworkflow."($table_coloums)")or die("Error occurs:" . mysqli_error($db_con));
                    //                
                    $insertWorkflow = mysqli_query($db_con, "insert into tbl_workflow_master (workflow_name,form_req,form_tbl_name,pdf_req,form_type) values ('$workflowName','$formReq','$tblworkflow','$pdfreq','$formtype')") or die('Error in workflow:' . mysqli_error($db_con));
                    $workflId = mysqli_insert_id($db_con);
                    $insertWorkflowform = mysqli_query($db_con, "insert into tbl_bridge_workflow_to_form (workflow_id,form_id) values ('$workflId','$id')") or die('Error in workflow:' . mysqli_error($db_con));
                }
            }
        } else {
            $insertWorkflow = mysqli_query($db_con, "insert into tbl_workflow_master (workflow_name,pdf_req,form_type) values ('$workflowName','$pdfreq','$formtype')") or die('Error in workflow:' . mysqli_error($db_con));
            $workflId = mysqli_insert_id($db_con);
        }
        if ($insertWorkflow) {
            $insertworkflowgrp = mysqli_query($db_con, "insert into tbl_workflow_to_group(workflow_id,group_id) values ('$workflId','$workflowgroups')") or die('Error in workflow:' . mysqli_error($db_con));
            $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs_wf(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,'Workflow $workflowName Created','$date',null,'$host',null)") or die('error : ' . mysqli_error($db_con));

            echo '<script>taskSuccess("addWorkflow","' . $lang['wf_cs'] . '");</script>';
            //echo'<script>window.location.href="addWorkflow";</script>';
        } else {
            echo '<script>taskFailed("addWorkflow","' . $lang['wf_nc'] . '");</script>';
        }
    }
}
