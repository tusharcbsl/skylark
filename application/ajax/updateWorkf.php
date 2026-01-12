<?php
require_once '../../sessionstart.php';
require_once './../config/database.php';
//sk@201218 : for multilingual
if (isset($_SESSION['lang'])) {
    $file = '../../' . $_SESSION['lang'] . ".json";
} else {
    $file = "../../English.json";
}
$data = file_get_contents($file);
$lang = json_decode($data, true); //end

/* if (!isset($_POST['workflowName'], $_POST['token'])) {
    echo "Unauthorised access !";
    exit;
} */

if (!empty($_POST['workflowName'])) {
    
    $workflowName = mysqli_real_escape_string($db_con, $_POST['workflowName']);
    $workflowName = preg_replace("/[^a-zA-Z_ ]/", "", $workflowName);
    if (!empty($_POST['workDesc'])) {

        $workflowDesc = $_POST['workDesc'];
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
    $id = preg_replace("/[^0-9 ]/", "", $_REQUEST['formid']);
    $wfid = preg_replace("/[^0-9 ]/", "", $_REQUEST['wfid']);
    if ($formReq == 1) {
        $data = $_REQUEST['data'];


        $data = json_decode($data, true);
        if (!empty($data)) {
            if ($data[0]['type'] == "header") {

                $formname = $data[0]['label'];
            } else {
                $formname = mysqli_real_escape_string($db_con, $_POST['workflowName']);
            }

            $insert = mysqli_query($db_con, "delete from  tbl_form_attribute where fid='$id'") or die("Error occurs:" . mysqli_error($db_con));
            $flag = 0;
            $newColums = array();
            if ($insert) {
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
                        array_push($newColums, $prefixname);
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
                }

                $tblNameqry = mysqli_query($db_con, "select form_tbl_name from tbl_workflow_master where workflow_id='$wfid'");
                $tblname = mysqli_fetch_assoc($tblNameqry);
                $oldColums = array();

                $qryOldCol = mysqli_query($db_con, "show COLUMNS FROM `$tblname[form_tbl_name]`") or die(mysqli_error($db_con));
                while ($rdata = mysqli_fetch_assoc($qryOldCol)) {


                    array_push($oldColums, $rdata['Field']);
                }
                for ($i = 0; $i < count($newColums); $i++) {
                    if (in_array($newColums[$i], $oldColums)) {
                        
                    } else {
                        $qrynewCol = mysqli_query($db_con, "ALTER TABLE `$tblname[form_tbl_name]` ADD $newColums[$i] VARCHAR(255)") or die(mysqli_error($db_con));
                    }
                }

                //create new coloums
                $update = mysqli_query($db_con, "update tbl_workflow_master set workflow_name='$workflowName',form_req='$formReq',pdf_req='$pdfreq' where workflow_id='$wfid'");
                if (isset($workflowgroups) && !empty($workflowgroups)) {
                    $update = mysqli_query($db_con, "update tbl_workflow_to_group set group_id='$workflowgroups' where workflow_id='$wfid'") or die('Error in workflow grop:' . mysqli_error($db_con));
                }
                // $workflId = mysqli_insert_id($db_con);
                //$insertWorkflowform = mysqli_query($db_con, "insert into tbl_bridge_workflow_to_form (workflow_id,form_id) values ('$workflId','$id')") or die('Error in workflow:' . mysqli_error($db_con));
            }
        }
        if ($update) {
            echo'<script>taskSuccess("addWorkflow","' . $lang['Wf_Updted_Scesfly'] . '");</script>';
        } else {
            echo'<script>taskFailed("addWorkflow","' . $lang['Fld_to_Updt_Wf'] . '");</script>';
        }
    } else {
		
        $update = mysqli_query($db_con, "update tbl_workflow_master set workflow_name='$workflowName', form_req = '$formReq',pdf_req='$pdfreq' where workflow_id='$wfid'") or die('Error in workflow:' . mysqli_error($db_con));
        if ($update) {
            if (isset($workflowgroups) && !empty($workflowgroups)) {
                $update = mysqli_query($db_con, "update tbl_workflow_to_group set group_id='$workflowgroups' where workflow_id='$wfid'") or die('Error in workflow grop:' . mysqli_error($db_con));
            }
            $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs_wf(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,null,'Workflow $workflowName Created','$date',null,'$host',null)") or die('error : ' . mysqli_error($db_con));
        }
        if ($log) {
            echo'<script>taskSuccess("addWorkflow","' . $lang['Wf_Updted_Scesfly'] . '");</script>';
        } else {
            echo'<script>taskFailed("addWorkflow","' . $lang['Fld_to_Updt_Wf'] . '");</script>';
        }
    }
}
?>