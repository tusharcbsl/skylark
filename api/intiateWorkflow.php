<?php

require_once 'connection.php';
require_once 'classes/function.php';
require_once 'notification.php';

if (isset($_POST['wid'])&&!empty($_POST['wid'])&&
    isset($_POST['fieldValues'])&&!empty($_POST['fieldValues'])
 ) {
    /* turn autocommit off */
//    mysqli_autoCommit($con,false);
    $wid = $_POST['wid'];

  /*  echo $wid;
    die;*/

    $fieldValues = $_POST['fieldValues'];
    //$tokenid = $_POST['tokenid'];
    
 

    $fieldValues= explode("^",$fieldValues);

   /* echo print_r($fieldValues);
    die;*/
    $coloum= "";
    $values = "";
    $data = "";
    $user_name = $_POST['username'];

    $wfid = base64_decode($wid);
    $userid = $_POST['userid']; 
    date_default_timezone_set("Asia/Kolkata");
    $date = date("Y-m-d H:i");

    if(!empty($_POST['ip'])){

    $host = $_POST['ip'];

    }
   else{

    $host = "";

   }
    
   

    $formExistQry = mysqli_query($con, "select * from tbl_bridge_workflow_to_form where workflow_id='$wfid'") or die("Error:" . mysqli_error($con));
    if (mysqli_num_rows($formExistQry) > 0) {
        // $_POST['taskRemark'];
        $docId = '0';
        $wfid = base64_decode($wid);
        $wfd = mysqli_query($con, "select * from tbl_workflow_master where workflow_id='$wfid'") or die('Error:' . mysqli_error($con));
        $rwWfd = mysqli_fetch_assoc($wfd);
         $workFlowName = $rwWfd['workflow_name'];
         $pdf_req = $rwWfd['pdf_req'];
  
         $user_id = $userid;

         $workFlowArray = explode(" ", $workFlowName);
         
         //print_r($workFlowArray);

         

        $ticket = '';
        for ($w = 0; $w < count($workFlowArray); $w++) {
            $name = $workFlowArray[$w];
            $ticket = $ticket . substr($name, 0, 1);
        }

     
        $ticket = $ticket . '_' . $user_id . '_' . strtotime($date);     

        if ($rwWfd['form_req'] == 1) {
        
            $taskRemark = "";
        

        } 


        else {


            $taskRemark = mysqli_real_escape_string($con, $_POST['taskRemark']);

        }


        // if file uploaded then 
        // lastmoveId = the id of the folder in which file will upload after intiate 


        if (!empty($_POST['lastMoveId'])) {

            $chkrw = mysqli_query($con, "select * from tbl_task_master where workflow_id = '$wfid'") or die('Error:' . mysqli_error($con));

            if (mysqli_num_rows($chkrw) > 0) {
                $sl_id = $_POST['lastMoveId'];
                $id = $sl_id . '_' . $wfid;


                //$docs_name =  $rwslname['sl_name'];
                if ($rwWfd['form_req'] == 1) {
                    $workFlowTblName = $rwWfd['form_tbl_name'];
                    $chkColExist = mysqli_query($con, "SELECT COLUMN_NAME FROM  information_schema.COLUMNS where  TABLE_NAME ='$workFlowTblName'  and COLUMN_NAME = 'co';")or die('Error:' . mysqli_error($con));

                    if (!empty($_POST['CO']) && mysqli_num_rows($chkColExist) > 0) {
                        $workFlowTblName = mysqli_escape_string($con, $workFlowTblName);
                        $dateofco = mysqli_escape_string($con, $_POST['CO']);
//                echo "select tbl_id from '$workFlowTblName' where user_id='$user_id' and co='$dateofco'" ;
                        $qrycochk = mysqli_query($con, "select tbl_id from " . $workFlowTblName . " where user_id='$user_id' and co='$dateofco'") or die("Error:" . mysqli_error($con));
                        

                        if (mysqli_num_rows($qrycochk) > 0) {
                            echo '<script>taskFailed("createWork", "Opps!! Submission failed")</script>';
                            die();
                        }


                         else {

                            $formbrige = mysqli_query($con, "select form_id from tbl_bridge_workflow_to_form where workflow_id='$wfid'");
                            $formid = mysqli_fetch_assoc($formbrige);
                            $formnameqry = mysqli_query($con, "select name from tbl_form_attribute where fid='$formid[form_id]' and name!='' and dependency_id is null");
                            $coloum .= "user_id,ticket_id";
                            $values .= "'" . $user_id . "'" . "," . "'" . $ticket . "'";


                            while ($rowdata = mysqli_fetch_assoc($formnameqry)) {
                                $names = $rowdata['name'];

                                if (!empty($names)) {
                                    $coloum .= "," . $names;
                                    $values .= ",'" . mysqli_real_escape_string($con, $_POST[$names]) . "'";
                                }
//                    array_push($formvalues, $_POST[$names]);
                            }
                            $sqlForm = "insert into " . $workFlowTblName . "($coloum) values ($values)";

                            $insertqry = mysqli_query($con, $sqlForm) or die('Error:' . mysqli_error($con));
                            if ($insertqry) {
                                $LastValuesId = mysqli_insert_id($con);
                                if (!empty($_POST['CO'])) {
                                    $coDate = $_POST['CO'];

                                    $chkColExist = mysqli_query($con, "SELECT COLUMN_NAME FROM  information_schema.COLUMNS where  TABLE_NAME ='$workFlowTblName'  and COLUMN_NAME = 'co';")or die('Error:' . mysqli_error($con));
                                    if (mysqli_num_rows($chkColExist) > 0) {
                                        $updateco = mysqli_query($con, "update " . $workFlowTblName . " Set co='$coDate' where tbl_id='$LastValuesId'")or die('Error:' . mysqli_error($con));
                                    } else {
                                        $qry = mysqli_query($con, "ALTER TABLE " . $workFlowTblName . " ADD co varchar(255)")or die('Error:' . mysqli_error($con));
                                        if ($qry) {
                                            $updateco = mysqli_query($con, "update " . $workFlowTblName . " Set co='$coDate' where tbl_id='$LastValuesId'")or die('Error:' . mysqli_error($con));
                                        }
                                    }
                                }

                                $form_id = $formid['form_id'];

                                $data .= "<table class='table'>";
                                $qry = mysqli_query($con, "select * from tbl_form_attribute where fid='$form_id' and dependency_id is null")or die(mysqli_error($con));

                                $colname = mysqli_query($con, "select * from $workFlowTblName where tbl_id='$LastValuesId'") or die("Error:" . mysqli_error($con));
                                $fetch = mysqli_fetch_fields($colname);
                                //print_r($fetch);
                                $userdata = mysqli_query($con, "select first_name,last_name,designation from tbl_user_master where user_id='$user_id'")or die("Error:" . mysqli_errno($con));
                                $userresult = mysqli_fetch_assoc($userdata);
                                $Name =  $userresult['first_name']." ".$userresult['last_name'];
                                $Designation = $userresult['designation'];

                                $fetchdata = mysqli_fetch_all($colname, MYSQLI_ASSOC);
                                $i = 1;
                                while ($row = mysqli_fetch_assoc($qry)) {


                                    if ($row['type'] == "header") {
                                        $data .= "<tr>";
                                        $data .= "<td colspan='2' align='center'>" . "<b>" . $row['label'] . "</b>" . "</td>";
                                        $data .= "</tr>";
                                    }
                                    if ($i == 1) {
                                        $data .= "<tr>";
                                        $data .= "<td><b>" . "Name" . "</b></td>";
                                        $data .= "<td>" . $userresult['first_name'] . " " . $userresult['last_name'] . "</td>";
                                        $data .= "</tr>";
                                        $data .= "<tr>";
                                        $data .= "<td><b>" . "Designation" . "</b></td>";
                                        $data .= "<td>" . $userresult['designation'] . "</td>";
                                        $data .= "</tr>";
                                    }

                                    $data .= "<tr>";
                                    $data .= "<td><b>" . $row['label'] . "</b></td>";
                                    foreach ($fetch as $val) {

                                        if ($row['name'] == $val->name) {

                                            $name = $val->name;
                                            foreach ($fetchdata as $values) {
                                                $data .= "<td>" . $values[$name] . (($values[$name] == 'CO(Compensatory off)') ? ' - ' . $values['co'] : '') . "</td>";
                                            }
                                        }
                                    }
                                    $data .= "</tr>";

                                    $i++;
                                }

                                $data .= "</table>";
                            }
                        }
                    }


                     elseif (!empty($_POST['cashvocher'])) {

                      /* echo  print_r($fieldValues);
                       die;*/

                        $formbrige = mysqli_query($con, "select form_id from tbl_bridge_workflow_to_form where workflow_id='$wfid'");
                        $formid = mysqli_fetch_assoc($formbrige);
                        $formnameqry = mysqli_query($con, "select name from tbl_form_attribute where fid='$formid[form_id]' and name!='' and dependency_id is null");
                        $coloum = "user_id,ticket_id";
                        $values = "'" . $user_id . "'" . "," . "'" . $ticket . "'";

                        $f=0;  
                        while ($rowdata = mysqli_fetch_assoc($formnameqry)) {
                            $names = $rowdata['name'];

                      

                            if (!empty($names)) {
                                $coloum .= "," . $names;
                                $values .= ",'" . mysqli_real_escape_string($con, $fieldValues[$f])."'";

                               // echo $values;

                               // echo  $fieldValues[$f];

                                 $f++;

                            }



                        }


                      

                        $sqlForm = "insert into " . $workFlowTblName . "($coloum) values ($values)";

                        $insertqry = mysqli_query($con, $sqlForm) or die('Error cashvoucher 1:' . mysqli_error($con));
                        if ($insertqry) {
                            $LastValuesId = mysqli_insert_id($con);
                            if (!empty($_POST['CO'])) {
                                $coDate = $_POST['CO'];

                                $chkColExist = mysqli_query($con, "SELECT COLUMN_NAME FROM  information_schema.COLUMNS where  TABLE_NAME ='$workFlowTblName'  and COLUMN_NAME = 'co';")or die('Error:' . mysqli_error($con));
                                if (mysqli_num_rows($chkColExist) > 0) {
                                    $updateco = mysqli_query($con, "update `" . $workFlowTblName . "` Set co='$coDate' where tbl_id='$LastValuesId'")or die('Error:' . mysqli_error($con));
                                } else {
                                    $qry = mysqli_query($con, "ALTER TABLE `" . $workFlowTblName . "` ADD co varchar(255)")or die('Error:' . mysqli_error($con));
                                    if ($qry) {
                                        $updateco = mysqli_query($con, "update `" . $workFlowTblName . "` Set co='$coDate' where tbl_id='$LastValuesId'")or die('Error:' . mysqli_error($con));
                                    }
                                }
                            }
                            if (!empty($_POST['cashvocher']) || !empty($_POST['amt']) || !empty($_POST['namt']) || !empty($_POST['rupee']) || !empty($_POST['amount'])) {
                               

                              /*  $purpose = implode(",", preg_replace("/[^a-zA-Z0-9_@ ]/", "", $_POST['cashvocher']));
                                $wf_amt = implode(",", $_POST['amt']);
                                $namt = implode(",", $_POST['namt']);
                                $rupee = $_POST['rupee'];
                                $famount = $_POST['amount'];
                                $desc = $_POST['descp'];
                                $wf_modeof_conveyance = $_POST['wf_modeof_conveyance'];*/


                                $purpose =explode(",",$_POST['cashvocher']);
                                $purpose = implode(",", preg_replace("/[^a-zA-Z0-9_@ ]/", " ", $purpose));
                                $wf_amt=explode(",",$_POST['amt']);
                                $wf_amt = implode(",", $wf_amt);
                                $namt = explode(",", $_POST['namt']);
                                $namt = implode(",", $namt);
                                $rupee = $_POST['rupee'];
                                $famount = $_POST['amount'];
                                $desc = $_POST['descp'];
                                $wf_modeof_conveyance = $_POST['wf_modeof_conveyance'];
                               
                                
                                $qry = mysqli_query($con, "ALTER TABLE `" . $workFlowTblName . "` ADD COLUMN  `wf_purpose` Text,ADD COLUMN `wf_amt` Text,ADD COLUMN `wf_netamt` Text,ADD COLUMN `wf_rupee` int(11),ADD COLUMN `wf_amount` varchar(50),ADD COLUMN `wf_description` Text, ADD COLUMN `wf_modeof_conveyance` varchar(50)");
                                
                                if ($qry) {
                                    $updateco = mysqli_query($con, "update `" . $workFlowTblName . "` Set wf_purpose='$purpose',wf_amt='$wf_amt',wf_netamt='$namt',wf_rupee='$rupee',wf_amount='$famount',wf_description='$desc', wf_modeof_conveyance='$wf_modeof_conveyance'  where tbl_id='$LastValuesId'")or die('Error:30' . mysqli_error($con));
                                } else {
                                    $updateco = mysqli_query($con, "update `" . $workFlowTblName . "` Set wf_purpose='$purpose',wf_amt='$wf_amt',wf_netamt='$namt',wf_rupee='$rupee',wf_amount='$famount',wf_description='$desc', wf_modeof_conveyance='$wf_modeof_conveyance' where tbl_id='$LastValuesId'")or die('Error27:' . mysqli_error($con));
                   
                                }

                            }
                            
                             

                            $form_id = $formid['form_id'];

                            $data = "<table class='table' border='1' cellspacing='0' cellpadding='7' style='margin-left:70px;'>";
                            $qry = mysqli_query($con, "select * from tbl_form_attribute where fid='$form_id' and dependency_id is null")or die(mysqli_error($con));
                            $rowdate = mysqli_fetch_assoc($qry);
                            $colname = mysqli_query($con, "select * from $workFlowTblName where tbl_id='$LastValuesId'") or die("Error:" . mysqli_error($con));
                            $fetch = mysqli_fetch_assoc($colname);

                            $userdata = mysqli_query($con, "select first_name,last_name,designation from tbl_user_master where user_id='$user_id'")or die("Error:" . mysqli_errno($con));
                            $userresult = mysqli_fetch_assoc($userdata);
                            $fetchdata = mysqli_fetch_all($colname, MYSQLI_ASSOC);

                            //echo'table'. $fetchdata['wf_materialrequired'];
                            //echo $fetchdata['ticket_id'];
                            $i = 1;
//                           while ($row = mysqli_fetch_assoc($qry)) {
//                             echo $row['name'];
                            $voucher_no = $fetch['wf_text'];
                            
                             mysqli_query($con, "INSERT INTO tbl_cash_voucher (voucher_no) values('$voucher_no')") or die('Eror:' . mysqli_error($con));
                             
                            if ($rowdate['type'] == "header") {
                                $data .= "<tr>";
                                $data .= "<td colspan='4' align='center'>" . "<b>" . "<img src='../../assets/images/logo_cbsl.png'  height='100px'>" . "</b>" . "</td>";
                                $data .= "</tr>";
                                $data .= "<tr>";
                                $data .= "<td colspan='4' align='center'>" . "<b>" . $rowdate['label'] . "</b>" . "</td>";
                                $data .= "</tr>";
                                $data .= "<tr>";
                                $data .= "<td colspan='4' align='center'>" . "<b>" . "CASH VOUCHER" . "</b>" . "</td>";
                                $data .= "</tr>";
                            }
                            if ($i == 1) {
                                $data .= "<tr>";
                                $data .= "<td><b>" . "Name" . "</b></td>";
                                $data .= "<td>" . $fetch['wf_username'] . "</td>";
                                 $data .= "<td><b>" . "Designation" . "</b></td>";
                                $data .= "<td>" . $fetch['wf_designation'] . "</td>";
                               
                                $data .= "</tr>";
                            }

                            $data .= "<tr>";

                            $wf_division = mysqli_escape_string($con, $fetch['wf_devision']);
                            $wf_project = mysqli_escape_string($con, $fetch['wf_project']);
                            $qrysql = "select * from `tbl_division` where Id='$wf_division'";
                            $qrysql1 = "select * from `tbl_project` where Id='$wf_project'";
                            $qry2 = mysqli_query($con, $qrysql)or die(mysqli_error($con));
                            $result = mysqli_fetch_assoc($qry2);
                            $qry1 = mysqli_query($con, $qrysql1)or die(mysqli_error($con));
                            $result1 = mysqli_fetch_assoc($qry1);
                            $data .= "<td><b>Division</b></td><td>" . $result['division_name'] . "</td>";
                            $data .= "<td><b>Project</b></td><td>" . $result1['project_name'] . "</td>";
                            $data .= "</tr>";
                            $data .= "<tr>";
                            $data .= "<td><b>No.</b></td><td>" . $fetch['wf_text'] . "</td>";
                            $data .= "<td><b>Location</b></td><td>" . $fetch['wf_location'] . "</td>";
                            $data .= "</tr>";
                            $data .= "<tr>";
                            $data .= "<td><b>Date</b></td><td>" . $fetch['wf_date'] . "</td>";
                            $data .= "<td><b>Mode Of Conveyance</b></td><td>" . $fetch['wf_modeof_conveyance'] . "</td>";

                            $data .= "</tr>";
                             $data .= "<tr>";
                              $data .= "<td><b>Purpose</b></td><td colspan='3'>" . $fetch['wf_description'] . "</td>";
                              $data .= "</tr>";
                            $data .= "<tr>";
                            $data .= "<td colspan='2'><b>Description</b></td>";
                            $data .= "<td><b>Rupees:</b></td>";
                            $data .= "<td><b>Paisa:</b></td>";

                            $data .= "</tr>";


                            $pupose = explode(",", $fetch['wf_purpose']);
                            $amount = explode(",", $fetch['wf_amt']);
                            $netamont = explode(",", $fetch['wf_netamt']);
                            for ($i = 0; $i < count($pupose); $i++) {
                                $data .= "<tr style='border-style: solid'>";
                                $data .= "<td colspan='2'>" . $pupose[$i] . "</td>";
                                $data .= "<td>" . $amount[$i] . "</td>";
                                $data .= "<td>" . $netamont[$i] . "</td>";
                                $data .= "</tr>";
                            }
                            
                            $data .= "<tr>";
                            $data .= "<td colspan='4' align='center'><b>Received from Capital Business Systems Pvt. Ltd. </b></td>";
                            $data .= "</tr>";
                            $data .= "<tr>";
                            $data .= "<td><b>Total Rupees:</b></td>";
                            $data .= "<td colspan='3'>" . $fetch['wf_rupee'] . "</td>";
                            $data .= "</tr>";
                            $data .= "<tr>";
                            $data .= "<td><b>Total Amount:</b></td>";
                            $data .= "<td colspan='3'>" . $fetch['wf_amount'] . "</td>";
                          
                            $data .= "</tr>";
                            $data .= "</table>";
                            
                            
                        }

                    } 

                    else

                     {
                        $formbrige = mysqli_query($con, "select form_id from tbl_bridge_workflow_to_form where workflow_id='$wfid'") or die('Error:' . mysqli_error($con));
                        $formid = mysqli_fetch_assoc($formbrige);
                        $userdata = mysqli_query($con, "select first_name,last_name,designation from tbl_user_master where user_id='$user_id'")or die("Error:" . mysqli_errno($con));
                        $userresult = mysqli_fetch_assoc($userdata);
                        $formnameqry = mysqli_query($con, "select name from tbl_form_attribute where fid='$formid[form_id]' and name!='' and dependency_id is null") or die('Error:' . mysqli_error($con));
                        $coloum .= "user_id,ticket_id";
                        $values .= "'" . $user_id . "'" . "," . "'" . $ticket . "'";

                     /*   echo $values;
                        die;*/

                        $f = 0 ;
                        while ($rowdata = mysqli_fetch_assoc($formnameqry)) {
                            $names = $rowdata['name'];

                         

                            if (!empty($names)) {
                                $coloum .= "," . $names;
                               $values .= ",'" . mysqli_real_escape_string($con, $fieldValues[$f]) . "'";
                              

                            }

                               $f++;

                        }

                     
                      $sqlForm = "insert into " . $workFlowTblName . "($coloum) values ($values)";


                      
                        $insertqry = mysqli_query($con, $sqlForm) or die('Error:' . mysqli_error($con));
                        if ($insertqry) {
                            $LastValuesId = mysqli_insert_id($con);
                            if (!empty($_POST['CO'])) {
                                $coDate = $_POST['CO'];

                                $chkColExist = mysqli_query($con, "SELECT COLUMN_NAME FROM  information_schema.COLUMNS where  TABLE_NAME ='$workFlowTblName'  and COLUMN_NAME = 'co';")or die('Error:' . mysqli_error($con));
                                if (mysqli_num_rows($chkColExist) > 0) {
                                    $updateco = mysqli_query($con, "update " . $workFlowTblName . " Set co='$coDate' where tbl_id='$LastValuesId'")or die('Error:' . mysqli_error($con));
                                } else {
                                    $qry = mysqli_query($con, "ALTER TABLE " . $workFlowTblName . " ADD co varchar(255)")or die('Error:' . mysqli_error($con));
                                    if ($qry) {
                                        $updateco = mysqli_query($con, "update " . $workFlowTblName . " Set co='$coDate' where tbl_id='$LastValuesId'")or die('Error:' . mysqli_error($con));
                                    }
                                }
                            }

                            $form_id = $formid['form_id'];

                            $data .= "<table border='1'  cellpadding='20px' width='70%'>";
                            $qry = mysqli_query($con, "select * from tbl_form_attribute where fid='$form_id' and dependency_id is null")or die(mysqli_error($con));

                            $colname = mysqli_query($con, "select * from $workFlowTblName where tbl_id='$LastValuesId'") or die("Error:" . mysqli_error($con));
                            $fetch = mysqli_fetch_fields($colname);

                            $fetchdata = mysqli_fetch_all($colname, MYSQLI_ASSOC);
                            $i = 1;
                            while ($row = mysqli_fetch_assoc($qry)) {

                                if ($row['type'] == "header") {
                                    $data .= "<tr>";
                                    $data .= "<td colspan='2' align='center'>" . "<b>" . $row['label'] . "</b>" . "</td>";
                                    $data .= "</tr>";
                                }
                                if ($i == 1) {
                                    $data .= "<tr>";
                                    $data .= "<td><b>" . "Name" . "</b></td>";
                                    $data .= "<td>" . $userresult['first_name'] . " " . $userresult['last_name'] . "</td>";
                                    $data .= "</tr>";
                                    $data .= "<tr>";
                                    $data .= "<td><b>" . "Designation" . "</b></td>";
                                    $data .= "<td>" . $userresult['designation'] . "</td>";
                                    $data .= "</tr>";
                                }

                                $data .= "<tr>";
                                $data .= "<td><b>" . $row['label'] . "</b></td>";
                                foreach ($fetch as $val) {

                                    if ($row['name'] == $val->name) {
                                        $name = $val->name;
                                        foreach ($fetchdata as $values) {
                                            $data .= "<td>" . $values[$name] . (($values[$name] == 'CO(Compensatory off)') ? ' - ' . $values['co'] : '') . "</td>";
                                        }
                                    }
                                }
                                $data .= "</tr>";

                                $i++;
                            }

                            $data .= "</table>";
                        }
                    }
                    if ($pdf_req == 1) {
                        include 'exportpdf.php';
                        //echo "export pdf ok";

//                if ((isset($_POST['taskRemark'])) && (!empty(trim($_POST['taskRemark'])))) { //if content of CKEditor ISN'T empty
                        $posted_editor = $data; //get content of CKEditor
                        //ankit
                        $slperm = mysqli_query($con, "select * from tbl_storagelevel_to_permission where user_id='$user_id'");
                        $rwSlperm = mysqli_fetch_assoc($slperm);
                        $sl_id = $rwSlperm['sl_id'];
                        $docName = mysqli_query($con, "select sl_id,sl_name from tbl_storage_level where sl_id = '$sl_id'") or die('Eror:' . mysqli_error($con));
                        $rwdocName = mysqli_fetch_assoc($docName);
                        $folderName = str_replace(" ", "", $workFlowName);
                        $pdfName = trim($workFlowName) . "_" .time() . ".pdf"; //specify the file save location and the file name
                        $path = '../extract-here/' . str_replace(" ", "", $workFlowName);
                        if (!is_dir($path)) {
                            mkdir($path, 0777, true);
                        }
                        $path = $path . '/' . $pdfName;
                        exportPDF($posted_editor, $path);
                        $wrkflowFsize = filesize($path);

                        $wrkflowFsize = round(($wrkflowFsize / 1024), 2);
                        $doc_name = $sl_id . '_' . $wfid;
                        $pagecount =$_POST['pageCount'];
						
						$destinationPath =str_replace(" ", "", $workFlowName).'/'.$pdfName;
                        $sourcePath = $path; 
                        if(uploadFileInFtpServer($destinationPath, $sourcePath)){
							
							$wrkflowDoc = mysqli_query($con, "INSERT INTO tbl_document_master (doc_name, old_doc_name, doc_extn, doc_path, uploaded_by, doc_size, noofpages, dateposted) VALUES ('$doc_name', '$pdfName', 'pdf', '$folderName/$pdfName', '$user_id', '$wrkflowFsize', '$pagecount', '$date')") or die('Eror:' . mysqli_error($con));
							$docId = mysqli_insert_id($con);
							$id = $sl_id . '_' . $docId . '_' . $wfid;
						}
						else
						{	
							$temp = array();
							$temp['error'] = 'false';
							$temp['msg'] = 'File upload failed'; 
							echo json_encode($temp); 
							exit();
						}

                        
//                }
                    } else {

                        $taskRemark = $data;
                    }
                }

                $files = $_FILES['fileName']['name'];
             /*  print_r($files);
              echo "out upload";*/
              
                if (!empty($files)) {
                /*   print_r($files);
                   echo "in upload";
                   echo count($files);*/
                    // echo "<script>alert('run')</script>";
                    for ($i = 0; $i < count($files); $i++) {
                        $file_name = $_FILES['fileName']['name'][$i];
                        $file_size = $_FILES['fileName']['size'][$i];
                        $file_type = $_FILES['fileName']['type'][$i];
                        $file_tmp = $_FILES['fileName']['tmp_name'][$i];
                        if (!empty($file_name)) {
                            $pageCount = $_POST['pageCount'];
                            // echo"<script>alert('two$pageCount')</script>";
                            $fname = substr($file_name, 0, strrpos($file_name, '.'));
                            $encryptName = urlencode(base64_encode($fname));
                            $fileExtn = substr($file_name, strrpos($file_name, '.') + 1);
                            $folder = str_replace(" ", "", $workFlowName);
                            $image_path = '../extract-here/' . $folder . '/';

                            if (!dir($image_path)) {
                                mkdir($image_path, 0777, TRUE); // or die("Error local folder:". print_r(error_get_last()));
                            }
                            $file_name = time() . '_' . $file_name;
                            $image_path = $image_path . $file_name;
                          


                            $upload = move_uploaded_file($file_tmp, $image_path) or die(print_r(error_get_last()));

                            if ($upload) {
									
                                 //ankit
								 $destinationPath =$folder.'/'.$file_name;
								 $sourcePath = $image_path; 
								 if(uploadFileInFtpServer($destinationPath, $sourcePath)){
							
									$query = "INSERT INTO tbl_document_master(doc_name, old_doc_name, doc_extn, doc_path, uploaded_by, doc_size, noofpages, dateposted) VALUES ('$id', '$fname', '$fileExtn', '$folder/$file_name', '$user_id', '$file_size', '$pageCount', '$date')";
									
									$exe = mysqli_query($con, $query) or die('Error query failed' . mysqli_error($con));
									if (empty($docId)) {
										$docId = mysqli_insert_id($con);
										$id = $sl_id . '_' . $docId . '_' . $wfid;
									}
								}
								else
								{	
									$temp = array();
									$temp['error'] = 'false';
									$temp['msg'] = 'File upload failed'; 
									echo json_encode($temp); 
									exit();
								}

                               
                            }
                        }
                    }
                }
                $getStep = mysqli_query($con, "select * from tbl_step_master where workflow_id = '$wfid' ORDER BY step_order ASC LIMIT 1") or die('Error:' . mysqli_error($con));
                $getStpId = mysqli_fetch_assoc($getStep);
                $stpId = $getStpId['step_id'];

                $getTask = mysqli_query($con, "select * from tbl_task_master where step_id = '$stpId' ORDER BY task_order ASC LIMIT 1") or die('Error:' . mysqli_error($con));
                $getTaskId = mysqli_fetch_assoc($getTask);
                // echo 'ok';
                $tskId = $getTaskId['task_id'];

                $getTaskDl = mysqli_query($con, "select * from tbl_task_master where task_id='$tskId'") or die('Error:' . mysqli_error($con));
                $rwgetTaskDl = mysqli_fetch_assoc($getTaskDl);

              
                if ($rwgetTaskDl['deadline_type'] == 'Date' || $rwgetTaskDl['deadline_type'] == 'Hrs') {

                    $endDate = date('Y-m-d H:i:s', (strtotime($date) + $rwgetTaskDl['deadline'] * 60));
                }
                if ($rwgetTaskDl['deadline_type'] == 'Days') {

                    $endDate = date('Y-m-d H:i:s', (strtotime($date) + $rwgetTaskDl['deadline'] * 24 * 60 * 60));
                }
                $taskRemark = mysqli_real_escape_string($con, $taskRemark);
                $insertInTask = mysqli_query($con, "INSERT INTO tbl_doc_assigned_wf(task_id, doc_id, start_date, end_date, task_status, assign_by, task_remarks,ticket_id) VALUES ('$tskId', '$docId', '$date', '$endDate', 'Pending', '$user_id','$taskRemark','$ticket')") or die('Error:' . mysqli_error($con));
                $idins = mysqli_insert_id($con);

                $getTask = mysqli_query($con, "select * from tbl_task_master where task_id = '$tskId'") or die('Error:' . mysqli_error($con));
                $rwgetTask = mysqli_fetch_assoc($getTask);
                $TskStpId = $rwgetTask['step_id'];
                $TskWfId = $rwgetTask['workflow_id'];
                $TskOrd = $rwgetTask['task_order'];
                $TskAsinToId = $rwgetTask['assign_user'];
                $nextTaskOrd = $TskOrd + 1;

                nextTaskAsin($nextTaskOrd, $TskWfId, $TskStpId, $docId, $date, $user_id, $con, $taskRemark, $ticket);
                if ($insertInTask) {
                    //echo '<script> alert("ok")</script>';
                    $log = mysqli_query($con, "insert into tbl_ezeefile_logs_wf(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `doc_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$userid', '$user_name',null,null,null,'Task Created','$date',null,'$host',null)") or die('error : ' . mysqli_error($con));
                    require_once 'mail.php';
                   $projectName="EzeePea";
                       $userid = $user_id;
                 $mail = assignTask($ticket, $idins, $con, $projectName,$userid);
                    if ($mail) {

                        //send sms to mob who submit
//                                $getMobNum = mysqli_query($con, "select * from tbl_user_master where user_id = '$user_id'") or die('Error:' . mysqli_error($con));
//                                $rwgetMobNum = mysqli_fetch_assoc($getMobNum);
//                                $submtByMob = $rwgetMobNum['phone_no'];
//                                $msg = 'Your Ticket Id is: ' . $ticket . ' and Your Task is in Process.';
//                                $sendMsgToMbl = smsgatewaycenter_com_Send($submtByMob, $msg, $debug = false);
                        //

                           $res = array();
                           $res['error'] = "false";
                          // $res['nxtuserid'] = $user_id;
                           $res['msg'] = "Submitted Successfully!!";
                           echo json_encode($res);


    //getting the next task id of task 
    $getNextTask = mysqli_query($con, "select * from tbl_task_master where task_order='$TskOrd' and step_id='$TskStpId'") or die('Error:' . mysqli_error($con));
    $rwgetNextTask = mysqli_fetch_assoc($getNextTask);
    $nxtuserid = $rwgetNextTask['assign_user'];
    $taskname = $rwgetNextTask['task_name'];

     $getTokenid = mysqli_query($con, "select * from tbl_user_master where user_id = $nxtuserid") or die('Error:' . mysqli_error($con));
     $rwgetToken= mysqli_fetch_assoc($getTokenid);
     $tokenid = $rwgetToken['fb_tokenid'];

     // getting id of the taskid of the task 
     $getTaskId = mysqli_query($con, "SELECT * FROM tbl_doc_assigned_wf where task_id = '$tskId' and NextTask ='0' order by start_date desc ") or die('Error:' . mysqli_error($con));
     $rwgetTaskId= mysqli_fetch_assoc($getTaskId);
     $id = $rwgetTaskId['id'];
  


     sendPushNotification($tokenid,$nxtuserid,$id,$taskname,$user_name,'In Tray','New Task '.$taskname. ' has been assigned to you'); 


    //sendPushNotification($tokenid,$prevAsignUser,$prevId,$prevTaskName,$username,'In Tray','New Task '.$prevTaskName.' has been assigned to you'); 

                    /*       if($res['error'] == 'false'){

                          

                           }*/

                           // echo '<script>taskSuccess("createWork", "Submitted Successfully!!");</script>';

                    } else {

                          $res = array();
                           $res['error'] = "false";
                           $res['msg'] = "Opps!! Mail not sent !";
                           echo json_encode($res);

                        //echo '<script>taskFailed("createWork", "Opps!! Mail not sent !")</script>';

                    }
                } else {

                           $res = array();
                           $res['error'] = "false";
                           $res['msg'] = "Opps!! Submission failed";
                           echo json_encode($res);


                    //echo '<script>taskFailed("createWork", "Opps!! Submission failed")</script>';

                }
            } else {
                           $res = array();
                           $res['error'] = "false";
                           $res['msg'] = "There is no Task in this Workflow";
                           echo json_encode($res);

               // echo '<script>taskFailed("createWork", "There is no Task in this Workflow ")</script>';
            }
        }



        else if (empty($_POST['lastMoveId'])) {


            $slperm = mysqli_query($con, "select * from tbl_storagelevel_to_permission where user_id='$userid'") or die('Error:' . mysqli_error($con));
       
            $rwSlperm = mysqli_fetch_assoc($slperm);
            $sl_id = $rwSlperm['sl_id'];
           
            $chkrw = mysqli_query($con, "select * from tbl_task_master where workflow_id = '$wfid'") or die('Error:' . mysqli_error($con));
            $id = $sl_id . '_' . $wfid;
            

            if (mysqli_num_rows($chkrw) > 0) {
                $getStep = mysqli_query($con, "select * from tbl_step_master where workflow_id = '$wfid' ORDER BY step_order ASC LIMIT 1") or die('Error:' . mysqli_error($con));
                $getStpId = mysqli_fetch_assoc($getStep);
                $stpId = $getStpId['step_id'];

                $getTask = mysqli_query($con, "select * from tbl_task_master where step_id = '$stpId' ORDER BY task_order ASC LIMIT 1") or die('Error:' . mysqli_error($con));
                $getTaskId = mysqli_fetch_assoc($getTask);
                $tskId = $getTaskId['task_id'];

                $getTaskDl = mysqli_query($con, "select * from tbl_task_master where task_id='$tskId'") or die('Error:' . mysqli_error($con));
                $rwgetTaskDl = mysqli_fetch_assoc($getTaskDl);

             
                if ($rwgetTaskDl['deadline_type'] == 'Date' || $rwgetTaskDl['deadline_type'] == 'Hrs') {

                    $endDate = date('Y-m-d H:i:s', (strtotime($date) + $rwgetTaskDl['deadline'] * 60));
                }
                if ($rwgetTaskDl['deadline_type'] == 'Days') {

                    $endDate = date('Y-m-d H:i:s', (strtotime($date) + $rwgetTaskDl['deadline'] * 24 * 60 * 60));
                }

                
               /* echo $endDate;
                die;*/   
                
              /*  echo $rwWfd['form_req'];
                die;    
*/
                //create pdf from form
                if ($rwWfd['form_req'] == 1) {
                    $workFlowTblName = $rwWfd['form_tbl_name'];


                   /* echo $workFlowTblName;

                    die;*/

                 /*   echo "SELECT COLUMN_NAME FROM  information_schema.COLUMNS where  TABLE_NAME ='$workFlowTblName'  and COLUMN_NAME = 'co'";
                    
                    die;*/
                           
                    $chkColExist = mysqli_query($con, "SELECT COLUMN_NAME FROM  information_schema.COLUMNS where  TABLE_NAME ='$workFlowTblName'  and COLUMN_NAME = 'co';")or die('Error:' . mysqli_error($con));

                    if (!empty($_POST['CO']) && mysqli_num_rows($chkColExist) > 0) {
                        $workFlowTblName = mysqli_escape_string($con, $workFlowTblName);
                        $dateofco = mysqli_escape_string($con, $_POST['CO']);
                        echo "select tbl_id from '$workFlowTblName' where user_id='$user_id' and co='$dateofco'";
                        $qrycochk = mysqli_query($con, "select tbl_id from " . $workFlowTblName . " where user_id='$user_id' and co='$dateofco'") or die("Error:" . mysqli_error($con));
                        if (mysqli_num_rows($qrycochk) > 0) {
                            echo '<script>taskFailed("createWork", "Opps!! Submission failed")</script>';
                            die();
                        } else {



                            $formbrige = mysqli_query($con, "select form_id from tbl_bridge_workflow_to_form where workflow_id='$wfid'");
                            $formid = mysqli_fetch_assoc($formbrige);
                            $formnameqry = mysqli_query($con, "select name from tbl_form_attribute where fid='$formid[form_id]' and name!='' and dependency_id is null");
                            $coloum .= "user_id,ticket_id";
                            $values .= "'" . $user_id . "'" . "," . "'" . $ticket . "'";


                            while ($rowdata = mysqli_fetch_assoc($formnameqry)) {
                                $names = $rowdata['name'];


                                if (!empty($names)) {
                                    $coloum .= "," . $names;
                                    
                                    $values .= ",'" . mysqli_real_escape_string($con, $fieldValues[$f]) . "'";

                                     

                                }
//                    array_push($formvalues, $_POST[$names]);
                            }
                            $sqlForm = "insert into " . $workFlowTblName . "($coloum) values ($values)";
                            
                           

                            $insertqry = mysqli_query($con, $sqlForm) or die('Error:' . mysqli_error($con));
                            if ($insertqry) {
                                $LastValuesId = mysqli_insert_id($con);
                                if (!empty($_POST['CO'])) {
                                    $coDate = $_POST['CO'];

                                    $chkColExist = mysqli_query($con, "SELECT COLUMN_NAME FROM  information_schema.COLUMNS where  TABLE_NAME ='$workFlowTblName'  and COLUMN_NAME = 'co';")or die('Error:' . mysqli_error($con));
                                    if (mysqli_num_rows($chkColExist) > 0) {
                                        $updateco = mysqli_query($con, "update `" . $workFlowTblName . "` Set co='$coDate' where tbl_id='$LastValuesId'")or die('Error:' . mysqli_error($con));
                                    } else {
                                        $qry = mysqli_query($con, "ALTER TABLE `" . $workFlowTblName . "` ADD co varchar(255)")or die('Error:' . mysqli_error($con));
                                        if ($qry) {
                                            $updateco = mysqli_query($con, "update `" . $workFlowTblName . "` Set co='$coDate' where tbl_id='$LastValuesId'")or die('Error:' . mysqli_error($con));
                                        }
                                    }
                                }

                                $form_id = $formid['form_id'];

                                $data = "<table class='table' border='1'>";
                                $qry = mysqli_query($con, "select * from tbl_form_attribute where fid='$form_id' and dependency_id is null")or die(mysqli_error($con));

                                $colname = mysqli_query($con, "select * from `$workFlowTblName` where tbl_id='$LastValuesId'") or die("Error:" . mysqli_error($con));
                                $fetch = mysqli_fetch_fields($colname);
                                //print_r($fetch);
                                $userdata = mysqli_query($con, "select first_name,last_name,designation from tbl_user_master where user_id='$user_id'")or die("Error:" . mysqli_errno($con));
                                $userresult = mysqli_fetch_assoc($userdata);
                                $fetchdata = mysqli_fetch_all($colname, MYSQLI_ASSOC);
                                // print_r($fetchdata);
                                $i = 1;
                                while ($row = mysqli_fetch_assoc($qry)) {


                                    if ($row['type'] == "header") {
                                        $data .= "<tr>";
                                        $data .= "<td colspan='2' align='center'>" . "<b>" . $row['label'] . "</b>" . "</td>";
                                        $data .= "</tr>";
                                    }
                                    if ($i == 1) {
                                        $data .= "<tr>";
                                        $data .= "<td><b>" . "Name" . "</b></td>";
                                        $data .= "<td>" . $userresult['first_name'] . " " . $userresult['last_name'] . "</td>";
                                        $data .= "</tr>";
                                        $data .= "<tr>";
                                        $data .= "<td><b>" . "Designation" . "</b></td>";
                                        $data .= "<td>" . $userresult['designation'] . "</td>";
                                        $data .= "</tr>";
                                    }

                                    $data .= "<tr>";
                                    $data .= "<td><b>" . $row['label'] . "</b></td>";
                                    foreach ($fetch as $val) {

                                        if ($row['name'] == $val->name) {

                                            $name = $val->name;
                                            foreach ($fetchdata as $values) {
                                                $data .= "<td>" . $values[$name] . (($values[$name] == 'CO(Compensatory off)') ? ' - ' . $values['co'] : '') . "</td>";
                                            }
                                        }
                                    }
                                    $data .= "</tr>";


                                    $i++;
                                }

                                $data .= "</table>";
                            }
                        }
                    } 

                    elseif (!empty($_POST['cashvocher']))

                     {
      
                        $formbrige = mysqli_query($con, "select form_id from tbl_bridge_workflow_to_form where workflow_id='$wfid'");
                        $formid = mysqli_fetch_assoc($formbrige);
                        $formnameqry = mysqli_query($con, "select name from tbl_form_attribute where fid='$formid[form_id]' and name!='' and dependency_id is null");
                        $coloum = "user_id,ticket_id";
                        $values = "'" . $user_id . "'" . "," . "'" . $ticket . "'";
                       
                        $f=0;

                    /*    for($f;$f<count($fieldValues);$f++){

                           echo $fieldValues[$f];

                        }
                         
                         die;*/
                        while ($rowdata = mysqli_fetch_assoc($formnameqry)) {
                            
                            $names =$rowdata['name'];
                          /*  $names = unset($rowdata[0]);*/
                          /*  echo $names;

                            die;*/
                            //echo$_POST["$names"];die();

                        
                            if (!empty($names)) {
                                $coloum .= "," . $names;
                                 $values .= ",'" . mysqli_real_escape_string($con, $fieldValues[$f]) . "'";
                                //$values .= ",'" . mysqli_real_escape_string($con,$fieldValues[$f]). "'";
                            }
                                $f++;

                        }

                        

                        $sqlForm = "insert into " . $workFlowTblName . "($coloum) values ($values)";
                

                        $insertqry = mysqli_query($con, $sqlForm) or die('Error:' . mysqli_error($con));
                        if ($insertqry) {
                            $LastValuesId = mysqli_insert_id($con);

                            if (!empty($_POST['CO'])) {
                                $coDate = $_POST['CO'];

                                $chkColExist = mysqli_query($con, "SELECT COLUMN_NAME FROM  information_schema.COLUMNS where  TABLE_NAME ='$workFlowTblName'  and COLUMN_NAME = 'co';")or die('Error22:' . mysqli_error($con));
                                if (mysqli_num_rows($chkColExist) > 0) {
                                    $updateco = mysqli_query($con, "update `" . $workFlowTblName . "` Set co='$coDate' where tbl_id='$LastValuesId'")or die('Error:' . mysqli_error($con));
                                } else {
                                    $qry = mysqli_query($con, "ALTER TABLE `" . $workFlowTblName . "` ADD co varchar(255)")or die('Error:' . mysqli_error($con));
                                    if ($qry) {
                                        $updateco = mysqli_query($con, "update `" . $workFlowTblName . "` Set co='$coDate' where tbl_id='$LastValuesId'")or die('Error:' . mysqli_error($con));
                                    }
                                }
                            }
                            if (!empty($_POST['cashvocher']) || !empty($_POST['amt']) || !empty($_POST['namt']) || !empty($_POST['rupee']) || !empty($_POST['amount'])) {
                                 
                                $purpose =explode(",",$_POST['cashvocher']);
                                $purpose = implode(",", preg_replace("/[^a-zA-Z0-9_@ ]/", " ", $purpose));
                                $wf_amt=explode(",",$_POST['amt']);
                                $wf_amt = implode(",", $wf_amt);
                                $namt = explode(",", $_POST['namt']);
                                $namt = implode(",", $namt);
                                $rupee = $_POST['rupee'];
                                $famount = $_POST['amount'];
                                $desc = $_POST['descp'];
                                $wf_modeof_conveyance = $_POST['wf_modeof_conveyance'];

                                
                                $qry = mysqli_query($con, "ALTER TABLE `" . $workFlowTblName . "` ADD COLUMN  `wf_purpose` Text,ADD COLUMN `wf_amt` Text,ADD COLUMN `wf_netamt` Text,ADD COLUMN `wf_rupee` int(11),ADD COLUMN `wf_amount` varchar(50),ADD COLUMN `wf_description` Text, ADD COLUMN `wf_modeof_conveyance` varchar(50)");
                                if ($qry) {
//                            echo '<script>alert("run");</script>';
                                    $updateco = mysqli_query($con, "update `" . $workFlowTblName . "` Set wf_purpose='$purpose',wf_amt='$wf_amt',wf_netamt='$namt',wf_rupee='$rupee',wf_amount='$famount', wf_description='$desc', wf_modeof_conveyance='$wf_modeof_conveyance' where tbl_id='$LastValuesId'")or die('Error32:' . mysqli_error($con));
                                } else {
                                    $updateco = mysqli_query($con, "update `" . $workFlowTblName . "` Set wf_purpose='$purpose',wf_amt='$wf_amt',wf_netamt='$namt',wf_rupee='$rupee',wf_amount='$famount', wf_description='$desc', wf_modeof_conveyance='$wf_modeof_conveyance' where tbl_id='$LastValuesId'")or die('Error31:' . mysqli_error($con));
//                             
                                }

                            }
                            $form_id = $formid['form_id'];

                            $data = "<table class='table' border='1' cellspacing='0' cellpadding='7' style='margin-left:70px;'>";
                            $qry = mysqli_query($con, "select * from tbl_form_attribute where fid='$form_id' and dependency_id is null")or die(mysqli_error($con));
                            $rowdate = mysqli_fetch_assoc($qry);
                            $colname = mysqli_query($con, "select * from $workFlowTblName where tbl_id='$LastValuesId'") or die("Error:" . mysqli_error($con));
                            $fetch = mysqli_fetch_assoc($colname);

                            $userdata = mysqli_query($con, "select first_name,last_name,designation from tbl_user_master where user_id='$user_id'")or die("Error userdata:" . mysqli_errno($con));
                            $userresult = mysqli_fetch_assoc($userdata);
                            $fetchdata = mysqli_fetch_all($colname, MYSQLI_ASSOC);

                            //echo'table'. $fetchdata['wf_materialrequired'];
                            //echo $fetchdata['ticket_id'];
                            $i = 1;
//                    while ($row = mysqli_fetch_assoc($qry)) {
                            // echo $row['name'];
                            $voucher_no = $fetch['wf_text'];
                            
                             mysqli_query($con, "INSERT INTO tbl_cash_voucher (voucher_no) values('$voucher_no')") or die('Eror insertinto voucher:' . mysqli_error($con));
                             
                             
                            if ($rowdate['type'] == "header") {
                                $data .= "<tr>";
                                $data .= "<td colspan='4' align='center'>" . "<b>" . "<img src='../../assets/images/logo_cbsl.png'  height='100px'>" . "</b>" . "</td>";
                                $data .= "</tr>";
                                $data .= "<tr>";
                                $data .= "<td colspan='4' align='center'>" . "<b>" . $rowdate['label'] . "</b>" . "</td>";
                                $data .= "</tr>";
                                $data .= "<tr>";
                                $data .= "<td colspan='4' align='center'>" . "<b>" . "CASH VOUCHER" . "</b>" . "</td>";
                                $data .= "</tr>";
                            }
                            if ($i == 1) {

                                $data .= "<tr>";
                                $data .= "<td><b>" . "Name" . "</b></td>";
                                $data .= "<td>" . $fetch['wf_username'] . "</td>";
                                 $data .= "<td><b>" . "Designation" . "</b></td>";
                                $data .= "<td>" . $fetch['wf_designation'] . "</td>";
                               
                                $data .= "</tr>";
                            }

                            $data .= "<tr>";

                            $wf_division = mysqli_escape_string($con, $fetch['wf_devision']);
                            $wf_project = mysqli_escape_string($con, $fetch['wf_project']);
                            $qrysql = "select * from `tbl_division` where Id='$wf_division'";
                            $qrysql1 = "select * from `tbl_project` where Id='$wf_project'";
                            $qry2 = mysqli_query($con, $qrysql)or die(mysqli_error($con));
                            $result = mysqli_fetch_assoc($qry2);
                            $qry1 = mysqli_query($con, $qrysql1)or die(mysqli_error($con));
                            $result1 = mysqli_fetch_assoc($qry1);
                            $data .= "<td><b>Division</b></td><td>" . $result['division_name'] . "</td>";
                            $data .= "<td><b>Project</b></td><td>" . $result1['project_name'] . "</td>";
                            $data .= "</tr>";
                            $data .= "<tr>";
                            $data .= "<td><b>No.</b></td><td>" . $fetch['wf_text'] . "</td>";
                            $data .= "<td><b>Location</b></td><td>" . $fetch['wf_location'] . "</td>";
                            $data .= "</tr>";
                            $data .= "<tr>";
                            $data .= "<td><b>Date</b></td><td>" . $fetch['wf_date'] . "</td>";
                            $data .= "<td><b>Mode Of Conveyance</b></td><td>" . $fetch['wf_modeof_conveyance'] . "</td>";

                            $data .= "</tr>";
                            $data .= "<tr>";
                            $data .= "<td><b>Purpose</b></td><td colspan='3'>" . $fetch['wf_description'] . "</td>";
                            $data .= "</tr>";
                            $data .= "<tr>";
                            $data .= "<td colspan='2'><b>Description</b></td>";
                            $data .= "<td><b>Rupees:</b></td>";
                            $data .= "<td><b>Paisa:</b></td>";
                            $data .= "</tr>";


                            $pupose = explode(",", $fetch['wf_purpose']);
                            $amount = explode(",", $fetch['wf_amt']);
                            $netamont = explode(",", $fetch['wf_netamt']);
                            for ($i = 0; $i < count($pupose); $i++) {
                                $data .= "<tr style='border-style: solid'>";
                                $data .= "<td colspan='2'>" . $pupose[$i] . "</td>";
                                $data .= "<td>" . $amount[$i] . "</td>";
                                $data .= "<td>" . $netamont[$i] . "</td>";
                                $data .= "</tr>";
                            }
                            
                            $data .= "<tr>";
                            $data .= "<td colspan='4' align='center'><b>Received from Capital Business Systems Pvt. Ltd. </b></td>";
                            $data .= "</tr>";
                            $data .= "<tr>";
                            $data .= "<td><b>Total Rupees:</b></td>";
                            $data .= "<td colspan='3'>" . $fetch['wf_rupee'] . "</td>";
                            $data .= "</tr>";
                            $data .= "<tr>";
                            $data .= "<td><b>Total Amount:</b></td>";
                            $data .= "<td colspan='3'>" . $fetch['wf_amount'] . "</td>";
                            $data .= "</tr>";
                            $data .= "</table>";
                            
                          
                            
                        }
                    } 

                    else 


                    {
                        
                        $formbrige = mysqli_query($con, "select form_id from tbl_bridge_workflow_to_form where workflow_id='$wfid'");
                        $formid = mysqli_fetch_assoc($formbrige);
                           
                      /*       print_r($formid);

                        die;*/
                            
                        $formnameqry = mysqli_query($con, "select name from tbl_form_attribute where fid='$formid[form_id]' and name!='' and dependency_id is null");
                        $coloum .= "user_id,ticket_id";
                        $values = "'" . $user_id . "'" . "," . "'" . $ticket . "'";

                       /* echo $values;
                        die; */

                       // $namesArray = array("ankit","mayank","Devender","Mukesh");

                        //print_r($namesArray);
                        //die;  
                      

                        $f=0;
                        while ($rowdata = mysqli_fetch_assoc($formnameqry)) {
                            $names = $rowdata['name'];
                            
                            //echo $fieldValues[$f]."+".$f;
                           
                            //echo$_POST["$names"];die();

                            if (!empty($names)) {
                                $coloum .= "," . $names;
                                //$values .= ",'" . mysqli_real_escape_string($con, $_POST["$names"]) . "'";
                                $values .= ",'" . mysqli_real_escape_string($con, $fieldValues[$f]) . "'";

                            }

                              $f++;
//                    array_push($formvalues, $_POST[$names]);
                        }
                        //echo $values;
                        $sqlForm = "insert into " . $workFlowTblName . "($coloum) values ($values)";
                           
                       /* echo $sqlForm;
                        die;*/

                        $insertqry = mysqli_query($con, $sqlForm) or die('Error:' . mysqli_error($con));
                        if ($insertqry) {
                            $LastValuesId = mysqli_insert_id($con);
                            if (!empty($_POST['CO'])) {
                                $coDate = $_POST['CO'];

                                $chkColExist = mysqli_query($con, "SELECT COLUMN_NAME FROM  information_schema.COLUMNS where  TABLE_NAME ='$workFlowTblName'  and COLUMN_NAME = 'co';")or die('Error:' . mysqli_error($con));
                                if (mysqli_num_rows($chkColExist) > 0) {
                                    $updateco = mysqli_query($con, "update `" . $workFlowTblName . "` Set co='$coDate' where tbl_id='$LastValuesId'")or die('Error:' . mysqli_error($con));
                                } else {
                                    $qry = mysqli_query($con, "ALTER TABLE `" . $workFlowTblName . "` ADD co varchar(255)")or die('Error:' . mysqli_error($con));
                                    if ($qry) {
                                        $updateco = mysqli_query($con, "update `" . $workFlowTblName . "` Set co='$coDate' where tbl_id='$LastValuesId'")or die('Error:' . mysqli_error($con));
                                    }
                                }
                            }


                            $form_id = $formid['form_id'];

                            $data = "<table class='table'>";
                            $qry = mysqli_query($con, "select * from tbl_form_attribute where fid='$form_id' and dependency_id is null")or die(mysqli_error($con));

                            $colname = mysqli_query($con, "select * from $workFlowTblName where tbl_id='$LastValuesId'") or die("Error:" . mysqli_error($con));
                            $fetch = mysqli_fetch_fields($colname);

                            $userdata = mysqli_query($con, "select first_name,last_name,designation from tbl_user_master where user_id='$user_id'")or die("Error:" . mysqli_errno($con));
                            $userresult = mysqli_fetch_assoc($userdata);
                            $fetchdata = mysqli_fetch_all($colname, MYSQLI_ASSOC);

                            //echo'table'. $fetchdata['wf_materialrequired'];
                            //echo $fetchdata['ticket_id'];
                            $i = 1;
                            while ($row = mysqli_fetch_assoc($qry)) {
                             /*   echo "ghhjkjkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkk";

                            die();*/

                           /*  echo $userresult['first_name'];
                             die;*/
                               // echo $row['name'];

                                if ($row['type'] == "header") {
                                    $data .= "<tr>";
                                    $data .= "<td colspan='2' align='center'>" . "<b>" . $row['label'] . "</b>" . "</td>";
                                    $data .= "</tr>";
                                }
                                if ($i == 1) {
                                    $data .= "<tr>";
                                    $data .= "<td><b>" . "Name" . "</b></td>";
                                    $data .= "<td>" . $userresult['first_name'] . " " . $userresult['last_name'] . "</td>";
                                    $data .= "</tr>";
                                    $data .= "<tr>";
                                    $data .= "<td><b>" . "Designation" . "</b></td>";
                                    $data .= "<td>" . $userresult['designation'] . "</td>";
                                    $data .= "</tr>";
                                }

                                $data .= "<tr>";
                                $data .= "<td><b>" . $row['label'] . "</b></td>";
                                foreach ($fetch as $val) {

                                    if ($row['name'] == $val->name) {

                                        
                                        $name = $val->name;
                                        foreach ($fetchdata as $values) {
                                            $data .= "<td>" . $values[$name] . (($values[$name] == 'CO(Compensatory off)') ? ' - ' . $values['co'] : '') . "</td>";
                                        }
                                    }
                                }
                                $data .= "</tr>";

                                $i++;
                            }

                            $data .= "</table>";
                        }
                    }
                    if ($pdf_req == 1) {

                        include 'exportpdf.php';

//                if ((isset($_POST['taskRemark'])) && (!empty(trim($_POST['taskRemark'])))) { //if content of CKEditor ISN'T empty
                        $posted_editor = $data; //get content of CKEditor
                        $slperm = mysqli_query($con, "select * from tbl_storagelevel_to_permission where user_id='$_SESSION[cdes_user_id]'");
                        $rwSlperm = mysqli_fetch_assoc($slperm);
                        $sl_id = $rwSlperm['sl_id'];
                        $docName = mysqli_query($con, "select sl_id,sl_name from tbl_storage_level where sl_id = '$sl_id'") or die('Eror:' . mysqli_error($con));
                        $rwdocName = mysqli_fetch_assoc($docName);
                        $folderName = str_replace(" ", "", $workFlowName);
                        $pdfName = trim($workFlowName) . "_" . time() . ".pdf"; //specify the file save location and the file name
                        $path = '../extract-here/' . str_replace(" ", "", $workFlowName);
                        if (!is_dir($path)) {
                            mkdir($path, 0777, true);
                        }
                        $path = $path . '/' . $pdfName;
                        exportPDF($posted_editor, $path);
                        $wrkflowFsize = filesize($path);

                        $wrkflowFsize = round(($wrkflowFsize / 1024), 2);
                        $doc_name = $sl_id . '_' . $wfid;
                        $pagecount = count_pages($path);
						$destinationPath =str_replace(" ", "", $workFlowName).'/'.$pdfName;
                        $sourcePath = $path; 
                        if(uploadFileInFtpServer($destinationPath, $sourcePath)){
							
							$wrkflowDoc = mysqli_query($con, "INSERT INTO tbl_document_master (doc_name, old_doc_name, doc_extn, doc_path, uploaded_by, doc_size, noofpages, dateposted) VALUES ('$doc_name', '$pdfName', 'pdf', '$folderName/$pdfName', '$user_id', '$wrkflowFsize', '$pagecount', '$date')") or die('Eror:' . mysqli_error($con));
							$docId = mysqli_insert_id($con);

							$id = $sl_id . '_' . $docId . '_' . $wfid;
						}
						else
						{	
							$temp = array();
							$temp['error'] = 'false';
							$temp['msg'] = 'File upload failed'; 
							echo json_encode($temp); 
							exit();
						}
                        
//                 echo $path;
//                die();
//                }
                    } else {
                        $taskRemark = $data;
                    }
                }
                //end create pdf
                //upload files if any

              

                if (!empty($_FILES['fileName']['name'])) {
                  
                  //print_r($files);

                    for ($i = 0; $i < count($files); $i++) {
                        $file_name = $_FILES['fileName']['name'][$i];
                        $file_size = $_FILES['fileName']['size'][$i];
                        $file_type = $_FILES['fileName']['type'][$i];
                        $file_tmp = $_FILES['fileName']['tmp_name'][$i];
                        if (!empty($file_name)) {
                            $pageCount = $_POST['pageCount'];
                            //echo"<script>alert('$pageCount')</script>";
                            $fname = substr($file_name, 0, strrpos($file_name, '.'));
                            $encryptName = urlencode(base64_encode($fname));
                            $fileExtn = substr($file_name, strrpos($file_name, '.') + 1);
                            $folder = str_replace(" ", "", $workFlowName);
                            $image_path = '../extract-here/' . $folder . '/';

                            if (!dir($image_path)) {
                                mkdir($image_path, 0777, TRUE); // or die("Error local folder:". print_r(error_get_last()));
                            }
                            $file_name = time() . '_' . $file_name;
                            $image_path = $image_path . $file_name;

                            $upload = move_uploaded_file($file_tmp, $image_path) or die(print_r(error_get_last()));

                            if ($upload) {
								
								$destinationPath =$folder.'/'.$file_name;
								 $sourcePath = $image_path; 
								 if(uploadFileInFtpServer($destinationPath, $sourcePath)){
							
									$query = "INSERT INTO tbl_document_master(doc_name, old_doc_name, doc_extn, doc_path, uploaded_by, doc_size, noofpages, dateposted) VALUES ('$id', '$fname', '$fileExtn', '$folder/$file_name', '$user_id', '$file_size', '$pageCount', '$date')";
									$exe = mysqli_query($con, $query) or die('Error query failed' . mysqli_error($con));
									if (empty($docId)) {
										$docId = mysqli_insert_id($con);
										$id = $sl_id . '_' . $docId . '_' . $wfid;
									}
								}
								else
								{	
									$temp = array();
									$temp['error'] = 'false';
									$temp['msg'] = 'File upload failed'; 
									echo json_encode($temp); 
									exit();
								}

                                
                            }
                        }
                    }
                }

              
                //end upload file
                // echo "INSERT INTO tbl_doc_assigned_wf(task_id, doc_id, start_date, end_date, task_status, assign_by, task_remarks,ticket_id) VALUES ('$tskId','$docId',  '$date', '$endDate', 'Pending', '$user_id','$taskRemark','$ticket')";
                $taskRemark = mysqli_real_escape_string($con, $taskRemark);
                $insertInTask = mysqli_query($con, "INSERT INTO tbl_doc_assigned_wf(task_id, doc_id, start_date, end_date, task_status, assign_by, task_remarks,ticket_id) VALUES ('$tskId','$docId',  '$date', '$endDate', 'Pending', '$user_id','$taskRemark','$ticket')") or die('Erorr: ' . mysqli_error($con));
                $idins = mysqli_insert_id($con);

                $getTask = mysqli_query($con, "select * from tbl_task_master where task_id = '$tskId'") or die('Error:' . mysqli_error($con));
                $rwgetTask = mysqli_fetch_assoc($getTask);
                $TskStpId = $rwgetTask['step_id'];
                $TskWfId = $rwgetTask['workflow_id'];
                $TskOrd = $rwgetTask['task_order'];
                $TskAsinToId = $rwgetTask['assign_user'];
                $nextTaskOrd = $TskOrd + 1;
                //for export pdf

                nextTaskAsin($nextTaskOrd, $TskWfId, $TskStpId, $docId, $date, $user_id, $con, $taskRemark, $ticket);

                if ($insertInTask) {
                    //echo '<script>taskSuccess("createWork", "Submitted Successfully!!");</script>';
                    require_once 'mail.php';
                    //echo '<script>alert("ok")</script>';

                       $projectName="EzeePea";
                       $userid = $user_id;
                       $mail = assignTask($ticket, $idins, $con, $projectName,$userid);

                    if ($mail) {

                         $temp = array();
                         $temp['error'] = 'false';
                         $temp['msg'] = 'Submitted Successfully!!'; 
                         echo json_encode($temp); 

                        /* echo "select * from tbl_task_master where task_order='$nextTaskOrd' and step_id='$TskStpId'";
                         die;*/

         $getNextTask = mysqli_query($con, "select * from tbl_task_master where task_order='$TskOrd' and step_id='$TskStpId'") or die('Error:' . mysqli_error($con));
    $rwgetNextTask = mysqli_fetch_assoc($getNextTask);
    $nxtuserid = $rwgetNextTask['assign_user'];
    $taskname = $rwgetNextTask['task_name'];

            $getTokenid = mysqli_query($con, "select * from tbl_user_master where user_id = $nxtuserid") or die('Error:' . mysqli_error($con));
            $rwgetToken= mysqli_fetch_assoc($getTokenid);
            $tokenid = $rwgetToken['fb_tokenid'];

     // getting id of the taskid of the task 
     $getTaskId = mysqli_query($con, "SELECT * FROM tbl_doc_assigned_wf where task_id = '$tskId' and NextTask ='0' order by start_date desc ") or die('Error:' . mysqli_error($con));
     $rwgetTaskId= mysqli_fetch_assoc($getTaskId);
     $id = $rwgetTaskId['id'];

   sendPushNotification($tokenid,$nxtuserid,$id,$taskname,$user_name,'In Tray','New Task '."$taskname". ' has been assigned to you'); 

              
                    } else {
                        //echo'Opps!! Mail not sent!';

                         $temp = array();
                         $temp['error'] = 'false';
                         $temp['msg'] = 'Opps!! Mail not sent!'; 
                         echo json_encode($temp); 
                        //echo '<script>taskFailed("createWork", "Opps!! Mail not sent!")</script>';
                    }
                } else {

                         $temp = array();
                         $temp['error'] = 'false';
                         $temp['msg'] = 'Opps!!  Submission failed'; 
                         echo json_encode($temp); 
                    //echo '<script>taskFailed("createWork", "Opps!!  Submission failed")</script>';
                }
            } else {

                     $temp = array();
                         $temp['error'] = 'false';
                         $temp['msg'] = 'There is no task in this workflow !'; 
                         echo json_encode($temp); 
                //echo '<script>taskFailed("createWork", "There is no task in this workflow !")</script>';
            }
        } else {

                 $temp = array();
                         $temp['error'] = 'false';
                         $temp['msg'] = 'Task Creation Failed.Please Select storage'; 
                         echo json_encode($temp); 
            //echo '<script>taskFailed("createWork", "Task Creation Failed.Please Select storage")</script>';
        }
    } 

    else {

        $docId = '0';
        $wfid = base64_decode($_POST['wid']);

        $wfd = mysqli_query($con, "select * from tbl_workflow_master where workflow_id='$wfid'");
        $rwWfd = mysqli_fetch_assoc($wfd);
        $workFlowName = $rwWfd['workflow_name'];

        $user_id = $_POST['userid'];

        $workFlowArray = explode(" ", $workFlowName);
        $ticket = '';
        for ($w = 0; $w < count($workFlowArray); $w++) {
            $name = $workFlowArray[$w];
            $ticket = $ticket . substr($name, 0, 1);
        }

        $ticket = $ticket . '_' . $user_id . '_' . strtotime($date);
        if ($rwWfd['form_req'] == 1) {

            $taskRemark = "";
        } else {

           // $taskRemark = mysqli_real_escape_string($con, $_POST['taskRemark']);
             $taskRemark = "";
        }
        //if file uploaded then
        if (!empty($_POST['lastMoveId'])) {

            $chkrw = mysqli_query($con, "select * from tbl_task_master where workflow_id = '$wfid'") or die('Error:' . mysqli_error($con));

            if (mysqli_num_rows($chkrw) > 0) {
                $sl_id = $_POST['lastMoveId'];
                $id = $sl_id . '_' . $wfid;


                //$docs_name =  $rwslname['sl_name'];
                if ($rwWfd['form_req'] == 1) {
                    include 'exportpdf.php';

                    if ((isset($_POST['taskRemark'])) && (!empty(trim($_POST['taskRemark'])))) { //if content of CKEditor ISN'T empty
                        $posted_editor = trim($_POST['taskRemark']); //get content of CKEditor
                        $folderName = str_replace(" ", "", $workFlowName);
                        $pdfName = trim($workFlowName) . "_" . time() . ".pdf"; //specify the file save location and the file name
                        $path = '../extract-here/' . str_replace(" ", "", $workFlowName);
                        if (!is_dir($path)) {
                            mkdir($path, 0777, true);
                        }
                        $path = $path . '/' . $pdfName;
                        $wrkflowFsize = filesize($path);
                        $wrkflowFsize = round(($wrkflowFsize / 1024), 2);
                        $doc_name = $sl_id . '_' . $wfid;
                        $pagecount = $pageCount;
						exportPDF($posted_editor, $path);
                       
						
						$destinationPath =str_replace(" ", "", $workFlowName).'/'.$pdfName;
                        $sourcePath = $path; 
                        if(uploadFileInFtpServer($destinationPath, $sourcePath)){
							
							 $wrkflowDoc = mysqli_query($con, "INSERT INTO tbl_document_master (doc_name, old_doc_name, doc_extn, doc_path, uploaded_by, doc_size, noofpages, dateposted) VALUES ('$doc_name', '$pdfName', 'pdf', '$folderName/$pdfName', '$user_id', '$wrkflowFsize', '$pagecount', '$date')") or die('Eror:' . mysqli_error($con));
							$docId = mysqli_insert_id($con);
							
							$id = $sl_id . '_' . $docId . '_' . $wfid;
						}
						else
						{	
							$temp = array();
							$temp['error'] = 'false';
							$temp['msg'] = 'File upload failed'; 
							echo json_encode($temp); 
							exit();
						}
                    }
                }

                $files = $_FILES['fileName']['name'];
                //print_r($files);
                if (!empty($files)) {
                    //print_r($files);
                    for ($i = 0; $i < count($files); $i++) {
                        $file_name = $_FILES['fileName']['name'][$i];
                        $file_size = $_FILES['fileName']['size'][$i];
                        $file_type = $_FILES['fileName']['type'][$i];
                        $file_tmp = $_FILES['fileName']['tmp_name'][$i];
                        if (!empty($file_name)) {
                            $pageCount = $_POST['pageCount'];
                            $fname = substr($file_name, 0, strrpos($file_name, '.'));
                            $encryptName = urlencode(base64_encode($fname));
                            $fileExtn = substr($file_name, strrpos($file_name, '.') + 1);
                            $folder = str_replace(" ", "", $workFlowName);
                            $image_path = '../extract-here/' . $folder . '/';

                            if (!dir($image_path))
                             {
                                mkdir($image_path, 0777, TRUE); // or die("Error local folder:". print_r(error_get_last()));
                            }
                            $file_name = time() . '_' . $file_name;
                            $image_path = $image_path . $file_name;

                            $upload = move_uploaded_file($file_tmp, $image_path) or die(print_r(error_get_last()));

                            if ($upload) {
								
								$destinationPath =$folder.'/'.$file_name;
								 $sourcePath = $image_path; 
								 if(uploadFileInFtpServer($destinationPath, $sourcePath)){
							
									$query = "INSERT INTO tbl_document_master(doc_name, old_doc_name, doc_extn, doc_path, uploaded_by, doc_size, noofpages, dateposted) VALUES ('$id', '$fname', '$fileExtn', '$folder/$file_name', '$user_id', '$file_size', '$pageCount', '$date')";
									$exe = mysqli_query($con, $query) or die('Error query failed' . mysqli_error($con));
									if (empty($docId)) {
										$docId = mysqli_insert_id($con);
										$id = $sl_id . '_' . $docId . '_' . $wfid;
									}
								}
								else
								{	
									$temp = array();
									$temp['error'] = 'false';
									$temp['msg'] = 'File upload failed'; 
									echo json_encode($temp); 
									exit();
								}

                                
                            }
                        }
                    }
                }
                $getStep = mysqli_query($con, "select * from tbl_step_master where workflow_id = '$wfid' ORDER BY step_order ASC LIMIT 1") or die('Error:' . mysqli_error($con));
                $getStpId = mysqli_fetch_assoc($getStep);
                $stpId = $getStpId['step_id'];

                $getTask = mysqli_query($con, "select * from tbl_task_master where step_id = '$stpId' ORDER BY task_order ASC LIMIT 1") or die('Error:' . mysqli_error($con));
                $getTaskId = mysqli_fetch_assoc($getTask);
                // echo 'ok';
                $tskId = $getTaskId['task_id'];

                $getTaskDl = mysqli_query($con, "select * from tbl_task_master where task_id='$tskId'") or die('Error:' . mysqli_error($con));
                $rwgetTaskDl = mysqli_fetch_assoc($getTaskDl);

             
                if ($rwgetTaskDl['deadline_type'] == 'Date' || $rwgetTaskDl['deadline_type'] == 'Hrs') {

                    $endDate = date('Y-m-d H:i:s', (strtotime($date) + $rwgetTaskDl['deadline'] * 60));
                }
                if ($rwgetTaskDl['deadline_type'] == 'Days') {

                    $endDate = date('Y-m-d H:i:s', (strtotime($date) + $rwgetTaskDl['deadline'] * 24 * 60 * 60));
                }
                $insertInTask = mysqli_query($con, "INSERT INTO tbl_doc_assigned_wf(task_id, doc_id, start_date, end_date, task_status, assign_by, task_remarks,ticket_id) VALUES ('$tskId', '$docId', '$date', '$endDate', 'Pending', '$user_id','$taskRemark','$ticket')"); // or die('Erorr: hh' . mysqli_error($con));
                $idins = mysqli_insert_id($con);

                $getTask = mysqli_query($con, "select * from tbl_task_master where task_id = '$tskId'") or die('Error:' . mysqli_error($con));
                $rwgetTask = mysqli_fetch_assoc($getTask);
                $TskStpId = $rwgetTask['step_id'];
                $TskWfId = $rwgetTask['workflow_id'];
                $TskOrd = $rwgetTask['task_order'];
                $TskAsinToId = $rwgetTask['assign_user'];
                $nextTaskOrd = $TskOrd + 1;

                nextTaskAsin($nextTaskOrd, $TskWfId, $TskStpId, $docId, $date, $user_id, $con, $taskRemark, $ticket);
                if ($insertInTask) {
                    //echo '<script> alert("ok")</script>';
                    $log = mysqli_query($con, "insert into tbl_ezeefile_logs_wf(`id`, `user_id`, `user_name`, `group_id`, `sl_id`, `doc_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`) values (null, '$userid, '$user_name',null,null,null,'Task Created','$date',null,'$host',null)") or die('error : ' . mysqli_error($con));
                    require_once 'mail.php';
                         $projectName="EzeePea";
                       $userid = $user_id;
                     
                      $mail = assignTask($ticket, $idins, $con, $projectName,$userid);
                    if ($mail) {

                        //send sms to mob who submit
//                                $getMobNum = mysqli_query($con, "select * from tbl_user_master where user_id = '$user_id'") or die('Error:' . mysqli_error($con));
//                                $rwgetMobNum = mysqli_fetch_assoc($getMobNum);
//                                $submtByMob = $rwgetMobNum['phone_no'];
//                                $msg = 'Your Ticket Id is: ' . $ticket . ' and Your Task is in Process.';
//                                $sendMsgToMbl = smsgatewaycenter_com_Send($submtByMob, $msg, $debug = false);
                        //

                            echo '<script>taskSuccess("createWork?wid=' . $_GET[id] . '", "Submitted Successfully!!");</script>';
                    } else {

                        echo '<script>taskFailed("createWork?wid=' . $_GET[id] . '", "Opps!! Mail not sent !")</script>';
                    }
                } else {
                    echo '<script>taskFailed("createWork?wid=' . $_GET[id] . '", "Opps!! Submission failed")</script>';
                }
            } else {
                echo '<script>taskFailed("createWork?wid=' . $_GET[id] . '", "There is no Task in this Workflow ")</script>';
            }
        } 


        else if (empty($_POST['lastMoveId'])) {
            $slperm = mysqli_query($con, "select * from tbl_storagelevel_to_permission where user_id='$_POST[userid]'");
            $rwSlperm = mysqli_fetch_assoc($slperm);
            $sl_id = $rwSlperm['sl_id'];
            $chkrw = mysqli_query($con, "select * from tbl_task_master where workflow_id = '$wfid'") or die('Error:' . mysqli_error($con));
            $id = $sl_id . '_' . $wfid;
            if (mysqli_num_rows($chkrw) > 0) {
                $getStep = mysqli_query($con, "select * from tbl_step_master where workflow_id = '$wfid' ORDER BY step_order ASC LIMIT 1") or die('Error:' . mysqli_error($con));
                $getStpId = mysqli_fetch_assoc($getStep);
                $stpId = $getStpId['step_id'];

                $getTask = mysqli_query($con, "select * from tbl_task_master where step_id = '$stpId' ORDER BY task_order ASC LIMIT 1") or die('Error:' . mysqli_error($con));
                $getTaskId = mysqli_fetch_assoc($getTask);
                $tskId = $getTaskId['task_id'];

                $getTaskDl = mysqli_query($con, "select * from tbl_task_master where task_id='$tskId'") or die('Error:' . mysqli_error($con));
                $rwgetTaskDl = mysqli_fetch_assoc($getTaskDl);


                if ($rwgetTaskDl['deadline_type'] == 'Date' || $rwgetTaskDl['deadline_type'] == 'Hrs') {

                    $endDate = date('Y-m-d H:i:s', (strtotime($date) + $rwgetTaskDl['deadline'] * 60));
                }
                if ($rwgetTaskDl['deadline_type'] == 'Days') {

                    $endDate = date('Y-m-d H:i:s', (strtotime($date) + $rwgetTaskDl['deadline'] * 24 * 60 * 60));
                }
                //create pdf from form
                if ($rwWfd['form_req'] == 1) {
                    include 'exportpdf.php';

                    if ((isset($_POST['taskRemark'])) && (!empty(trim($_POST['taskRemark'])))) { //if content of CKEditor ISN'T empty
                        $posted_editor = trim($_POST['taskRemark']); //get content of CKEditor

                        $docName = mysqli_query($con, "select sl_id,sl_name from tbl_storage_level where sl_id = '$sl_id'") or die('Eror:' . mysqli_error($con));
                        $rwdocName = mysqli_fetch_assoc($docName);
                        $folderName = str_replace(" ", "", $workFlowName);
                        $pdfName = trim($workFlowName) . "_" . time() . ".pdf"; //specify the file save location and the file name
                        $path = '../extract-here/' . str_replace(" ", "", $workFlowName);
                        if (!is_dir($path)) {
                            mkdir($path, 0777, true);
                        }
                        $path = $path . '/' . $pdfName;
                        $wrkflowFsize = filesize($path);
                        $wrkflowFsize = round(($wrkflowFsize / 1024), 2);
                        $doc_name = $sl_id . '_' . $wfid;
						exportPDF($posted_editor, $path);
                        $pagecount = count_pages($path);
						
                        
						
						$destinationPath =str_replace(" ", "", $workFlowName).'/'.$pdfName;
                        $sourcePath = $path; 
                        if(uploadFileInFtpServer($destinationPath, $sourcePath)){
							
							$wrkflowDoc = mysqli_query($con, "INSERT INTO tbl_document_master (doc_name, old_doc_name, doc_extn, doc_path, uploaded_by, doc_size, noofpages, dateposted) VALUES ('$doc_name', '$pdfName', 'pdf', '$folderName/$pdfName', '$user_id', '$wrkflowFsize', '$pagecount', '$date')") or die('Eror:' . mysqli_error($con));
							$docId = mysqli_insert_id($con);
						  
							$id = $sl_id . '_' . $docId . '_' . $wfid;
						}
						else
						{	
							$temp = array();
							$temp['error'] = 'false';
							$temp['msg'] = 'File upload failed'; 
							echo json_encode($temp); 
							exit();
						}
                    }
                }
                //end create pdf
                //upload files if any
                $files = $_FILES['fileName']['name'];
                if (!empty($files)) {
                    for ($i = 0; $i < count($files); $i++) {
                        $file_name = $_FILES['fileName']['name'][$i];
                        $file_size = $_FILES['fileName']['size'][$i];
                        $file_type = $_FILES['fileName']['type'][$i];
                        $file_tmp = $_FILES['fileName']['tmp_name'][$i];
                        if (!empty($file_name)) {
                            $pageCount = $_POST['pageCount'];
                            $fname = substr($file_name, 0, strrpos($file_name, '.'));
                            $encryptName = urlencode(base64_encode($fname));
                            $fileExtn = substr($file_name, strrpos($file_name, '.') + 1);
                            $folder = str_replace(" ", "", $workFlowName);
                            $image_path = '../extract-here/' . $folder . '/';

                            if (!dir($image_path)) {
                                mkdir($image_path, 0777, TRUE); // or die("Error local folder:". print_r(error_get_last()));
                            }
                            $file_name = time() . '_' . $file_name;
                            $image_path = $image_path . $file_name;

                            $upload = move_uploaded_file($file_tmp, $image_path) or die(print_r(error_get_last()));

                            if ($upload) {
								
								$destinationPath =$folder.'/'.$file_name;
								 $sourcePath = $image_path; 
								if(uploadFileInFtpServer($destinationPath, $sourcePath)){
							
									$query = "INSERT INTO tbl_document_master(doc_name, old_doc_name, doc_extn, doc_path, uploaded_by, doc_size, noofpages, dateposted) VALUES ('$id', '$fname', '$fileExtn', '$folder/$file_name', '$user_id', '$file_size', '$pageCount', '$date')";
									$exe = mysqli_query($con, $query) or die('Error query failed' . mysqli_error($con));
									if (empty($docId)) {
										$docId = mysqli_insert_id($con);
										$id = $sl_id . '_' . $docId . '_' . $wfid;
									}
								}
								else
								{	
									$temp = array();
									$temp['error'] = 'false';
									$temp['msg'] = 'File upload failed'; 
									echo json_encode($temp); 
									exit();
								}

                                
                            }
                        }
                    }
                }
                //end upload file

                $insertInTask = mysqli_query($con, "INSERT INTO tbl_doc_assigned_wf(task_id, doc_id, start_date, end_date, task_status, assign_by, task_remarks,ticket_id) VALUES ('$tskId','$docId',  '$date', '$endDate', 'Pending', '$user_id', '$taskRemark','$ticket')"); // or die('Erorr: hh1' . mysqli_error($con));
                $idins = mysqli_insert_id($con);

                $getTask = mysqli_query($con, "select * from tbl_task_master where task_id = '$tskId'") or die('Error:' . mysqli_error($con));
                $rwgetTask = mysqli_fetch_assoc($getTask);
                $TskStpId = $rwgetTask['step_id'];
                $TskWfId = $rwgetTask['workflow_id'];
                $TskOrd = $rwgetTask['task_order'];
                $TskAsinToId = $rwgetTask['assign_user'];
                $nextTaskOrd = $TskOrd + 1;
                //for export pdf

                nextTaskAsin($nextTaskOrd, $TskWfId, $TskStpId, $docId, $date, $user_id, $con, $taskRemark, $ticket);

                if ($insertInTask) {

                    require_once 'mail.php';
                      $projectName="EzeePea";
                       $userid = $user_id;
                    //echo '<script>alert("ok")</script>';
                    $mail = assignTask($ticket, $idins, $con, $projectName,$userid);
                    if ($mail) {

                        //send sms to mob
//                        $getMobNum = mysqli_query($con, "select * from tbl_user_master where user_id = '$user_id'") or die('Error:' . mysqli_error($con));
//                        $rwgetMobNum = mysqli_fetch_assoc($getMobNum);
//                        $submtByMob = $rwgetMobNum['phone_no'];
//                        $msg = 'Your Ticket Id is: ' . $ticket . ' and Your Task is in Process.';
//                        $sendMsgToMbl = smsgatewaycenter_com_Send($submtByMob, $msg, $debug = false);
                        //
                        //send sms to assign user
//                        $getTaskAsinToMob = mysqli_query($con, "select * from tbl_user_master where user_id='$TskAsinToId'") or die('Error:' . mysqli_error($con));
//                        $rwgetTaskAsinToMob = mysqli_fetch_assoc($getTaskAsinToMob);
//                        $asinToMob = $rwgetTaskAsinToMob['phone_no'];
//                        $msgAsinTo = 'New Task With Ticket Id : ' . $ticket . ' has been Assigned To You.';
//                        $sendMsgToMbl = smsgatewaycenter_com_Send($asinToMob, $msgAsinTo, $debug = false);
                        //

                         $temp = array();
                         $temp['error'] = 'false';
                         $temp['msg'] = 'Submitted Successfully!!';  
                         echo json_encode($temp);

   //getting the next task id of task 
    $getNextTask = mysqli_query($con, "select * from tbl_task_master where task_order='$TskOrd' and step_id='$TskStpId'") or die('Error:' . mysqli_error($con));
    $rwgetNextTask = mysqli_fetch_assoc($getNextTask);
    $nxtuserid = $rwgetNextTask['assign_user'];
    $taskname = $rwgetNextTask['task_name'];

     $getTokenid = mysqli_query($con, "select * from tbl_user_master where user_id = $nxtuserid") or die('Error:' . mysqli_error($con));
     $rwgetToken= mysqli_fetch_assoc($getTokenid);
     $tokenid = $rwgetToken['fb_tokenid'];

     // getting id of the taskid of the task 
     $getTaskId = mysqli_query($con, "SELECT * FROM tbl_doc_assigned_wf where task_id = '$tskId' and NextTask ='0' order by start_date desc ") or die('Error:' . mysqli_error($con));
     $rwgetTaskId= mysqli_fetch_assoc($getTaskId);
     $id = $rwgetTaskId['id'];

    sendPushNotification($tokenid,$nxtuserid,$id,$taskname,$user_name,'In Tray','New Task '.$taskname. ' has been assigned to you'); 


                       // echo '<script>taskSuccess("createWork", "Submitted Successfully!!");</script>';
                    } else {
                        //echo'Opps!! Mail not sent!';

                        //echo '<script>taskFailed("createWork", "Opps!! Mail not sent!")</script>';

                         $temp = array();
                         $temp['error'] = 'true';
                         $temp['msg'] = "Opps!! Mail not sent!"; 
                         echo json_encode($temp); 

                    }
                } else {

                         $temp = array();
                         $temp['error'] = 'true';
                         $temp['msg'] = "Opps!!  Submission failed"; 
                         echo json_encode($temp); 

                    //echo '<script>taskFailed("createWork", "Opps!!  Submission failed")</script>';
                }
            } else {

                         $temp = array();
                         $temp['error'] = 'true';
                         $temp['msg'] = "There is no task in this workflow !"; 
                         echo json_encode($temp); 


                //echo '<script>taskFailed("createWork", "There is no task in this workflow !")</script>';
            }
        } else {

                         $temp = array();
                         $temp['error'] = 'true';
                         $temp['msg'] = "Task Creation Failed.Please Select storage"; 
                         echo json_encode($temp); 
            //echo '<script>taskFailed("createWork", "Task Creation Failed.Please Select storage")</script>';
        }
    }
    mysqli_close($con);
}




function count_pages($pdfname) {

    $pdftext = file_get_contents($pdfname);

    $num = preg_match_all("/\/Page\W/", $pdftext, $dummy);

    return $num;
}


//find next task to asssin doc
function nextTaskAsin($nextTaskOrd, $TskWfId, $TskStpId, $docId, $date, $user_id, $con, $taskRemark, $ticket) {
   //echo "select * from tbl_task_master where task_order='$nextTaskOrd' and step_id='$TskStpId'";

     //global $date;
     
      //$endDateNexTsk = null ;
     $endDateNexTsk = date('Y-m-d H:i:s');

    $getNextTask = mysqli_query($con, "select * from tbl_task_master where task_order='$nextTaskOrd' and step_id='$TskStpId'") or die('Error:' . mysqli_error($con));
    $rwgetNextTask = mysqli_fetch_assoc($getNextTask);

    if (mysqli_num_rows($getNextTask) > 0) {

        $NextTaskId = $rwgetNextTask['task_id'];

        $getNextTaskDl = mysqli_query($con, "select * from tbl_task_master where task_id='$NextTaskId'") or die('Error:' . mysqli_error($con));
        $rwgetNextTaskDl = mysqli_fetch_assoc($getNextTaskDl);
//old code
      if ($rwgetNextTaskDl['deadline_type'] == 'Date' || $rwgetNextTaskDl['deadline_type'] == 'Hrs') {

            $endDateNexTsk = date('Y-m-d H:i:s', (strtotime($date) + $rwgetNextTaskDl['deadline'] * 60));
        }
        if ($rwgetNextTaskDl['deadline_type'] == 'Days') {

            $endDateNexTsk = date('Y-m-d H:i:s', (strtotime($date) + $rwgetNextTaskDl['deadline'] * 24 * 60 * 60));
        }
                    
         
        if (!empty($docId) && $docId!=0){
            $insertInNextTask = mysqli_query($con, "INSERT INTO tbl_doc_assigned_wf(task_id, doc_id, start_date, end_date, task_status, assign_by, NextTask, task_remarks,ticket_id) VALUES ('$NextTaskId', '$docId', '$date', '$endDateNexTsk', 'Pending', '$user_id', 2, '$taskRemark','$ticket')") or die('Erorr: ff' . mysqli_error($con));
        } else {
            $insertInNextTask = mysqli_query($con, "INSERT INTO tbl_doc_assigned_wf(task_id, start_date, end_date, task_status, assign_by, NextTask, task_remarks,ticket_id) VALUES ('$NextTaskId', '$date', '$endDateNexTsk', 'Pending', '$user_id', 2, '$taskRemark','$ticket')") or die('Erorr: ff' . mysqli_error($con));
        }
    } else {
        $getStpOr = mysqli_query($con, "select * from tbl_step_master where workflow_id='$TskWfId' and step_id='$TskStpId'") or die('Error:' . mysqli_error($con));
        $rwgetStpOr = mysqli_fetch_assoc($getStpOr);
        $getStpOrd = $rwgetStpOr['step_order'];
        $nextStpOrd = $getStpOrd + 1;
        $getNexStp = mysqli_query($con, "select * from tbl_step_master where workflow_id='$TskWfId' and step_order='$nextStpOrd'") or die('Error:' . mysqli_error($con));
        $rwgetNexStp = mysqli_fetch_assoc($getNexStp);

        if (mysqli_num_rows($getNexStp) > 0) {


            $nextStpId = $rwgetNexStp['step_id'];
            $getNextTask1 = mysqli_query($con, "select * from tbl_task_master where step_id = '$nextStpId' ORDER BY task_order ASC LIMIT 1") or die('Error:' . mysqli_error($con));
            $rwgetNextTask1 = mysqli_fetch_assoc($getNextTask1);
            $getNexTskId = $rwgetNextTask1['task_id'];
            $getNexTskOrd = $rwgetNextTask1['task_order'];

            nextTaskAsin($getNexTskOrd, $TskWfId, $nextStpId, $docId, $date, $user_id, $con, $taskRemark, $ticket);
            // echo 'gg'; die;
            return;
        }
    }
}


if(isset($_POST['tktid'])&&!empty($_POST['tktid'])){

    $tckid = $_POST['tktid'];
    $task=mysqli_query($con,"SELECT ttm.workflow_id,tdaf.doc_id,tdaf.task_remarks FROM tbl_task_master as ttm inner join `tbl_doc_assigned_wf` as tdaf on ttm.task_id=tdaf.task_id  WHERE ticket_id='$tckid'");
    $rwTask=mysqli_fetch_array($task);
    $wid = $rwTask['workflow_id'];

//select * from tbl_workflow_master where workflow_id = '190'


    $wId=mysqli_query($con,"select * from tbl_workflow_master where workflow_id = '$wid'");
    $rwTask=mysqli_fetch_array($wId);

    $res = array();
    $res['workflow_id'] = $rwTask['workflow_id'];
    $res['workflow_name'] = $rwTask['workflow_name'];

    echo json_encode($res);


}



?>