<?php

require_once 'connection.php';

if(isset($_POST['wId'])&&!empty($_POST['wId'])){

 $workflowId = $_POST['wId'];
 
 $getFormIdQry = "SELECT * FROM tbl_bridge_workflow_to_form where workflow_id = '$workflowId'";

 //echo $getFormIdQry;


 $getFormId = mysqli_query($con,$getFormIdQry);

 $rwFormId = mysqli_fetch_assoc($getFormId);
 $formid = $rwFormId['form_id'];
 $res = array();

 //echo  "SELECT * FROM tbl_form_attribute where fid ='$formid'";

//die;
 
$getAttriQry = "SELECT * FROM tbl_form_attribute where fid ='$formid'";
$getAttri = mysqli_query($con,$getAttriQry);


//$attri =  mysqli_fetch_all($getAttri,MYSQLI_ASSOC);

while($rwAttri = mysqli_fetch_assoc($getAttri)){

  $temp = array();
  $optionArray = array();
  $temp['type'] = $rwAttri['type'];

  if($rwAttri['type'] == 'option'){

   //nothing

  }
  else{


 if($rwAttri['type'] == 'select'){

    $fid = $rwAttri['fid'];
    $aid = $rwAttri['aid'];
    $dId = $rwAttri['dependency_id'];

    $getOptionQry = "SELECT * FROM tbl_form_attribute where fid ='$fid' and dependency_id = '$aid'";
    $getOption = mysqli_query($con,$getOptionQry);
   
    $op =  mysqli_fetch_all($getOption,MYSQLI_ASSOC);
    
    array_push($optionArray,$op);

  }



  $temp['label'] = $rwAttri['label'];
  $temp['maxlength'] = $rwAttri['maxlength'];
  $temp['aid'] = $rwAttri['aid'];
  $temp['fid'] = $rwAttri['fid'];
  $temp['required'] = $rwAttri['required'];
  $temp['value'] = $rwAttri['value'];
  $temp['option'] = $optionArray;
  $temp['dependency_id'] = $rwAttri['dependency_id'];
  $temp['placeholder'] = $rwAttri['placeholder'];
  $temp['class'] = $rwAttri['class'];
  $temp['placeholder'] = $rwAttri['placeholder'];

  array_push($res,$temp);

}

  } 
  
 

 echo json_encode($res);
  

}





 /*  "aid": "988",
        "fid": "73",
        "label": "From Date",
        "name": "wf_from",
        "type": "date",
        "class": "form-control",
        "multiple_files": "",
        "required": "1",
        "placeholder": "",
        "value": "",
        "maxlength": "4000",
        "dependency_id": null,
        "inline": "1",
        "selected": null,
        "subtype": ""*/




?>