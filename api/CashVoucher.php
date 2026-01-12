<?php
//Cash Voucher Api

require_once 'connection.php';

//Get the divison 
if(isset($_POST['userid'])&& !empty($_POST['userid'])){

    
  $Qry = "SELECT * FROM tbl_division";
  $getDiv = mysqli_query($con,$Qry);
   
  // Fetch all
  $result = mysqli_fetch_all($getDiv,MYSQLI_ASSOC);

  echo json_encode($result);
  
}


//Get the project 

if(isset($_POST['divisionId'])&& !empty($_POST['divisionId'])){
  
   
   $divID = $_POST['divisionId'];
 

  $Qry = "SELECT * FROM tbl_project where divisionId = $divID ";
  $getProj = mysqli_query($con,$Qry);
   
  // Fetch all
  $result = mysqli_fetch_all($getProj,MYSQLI_ASSOC);

  echo json_encode($result);
  
}


//Get last cashvoucher code and add 1 


if(isset($_POST['cashVoucherNo'])&& !empty($_POST['cashVoucherNo'])){
  
   
   $id = $_POST['cashVoucherNo'];



 /*$Qry = "SELECT * FROM tbl_cash_voucher order by id desc limit 1;";
 
  $vNo = mysqli_query($con,$Qry);
     
  $rwVno= mysqli_fetch_assoc($vNo);
  $voucherNo = $rwVno['voucher_no'];

  
  $result = array();
  $result["voucher_no"] = $voucherNo; 
*/


    $query = mysqli_query($con, "select voucher_no as maxVoucherNo from tbl_cash_voucher order by Id desc limit 1");
    $res = mysqli_fetch_assoc($query);
    $maxVoucherNo = $res['maxVoucherNo'];
    $maxVoucherNo = $maxVoucherNo+1;
    $voucherNo = "";
    if(strlen($maxVoucherNo)==1){
       $voucherNo = '0000'.$maxVoucherNo; 
    }else if(strlen($maxVoucherNo)==2){
        
        $voucherNo = '000'.$maxVoucherNo;
    }
    else if(strlen($maxVoucherNo)==3){
        
        $voucherNo = '00'.$maxVoucherNo;
    }
    else if(strlen($maxVoucherNo)==4){
        
        $voucherNo = '0'.$maxVoucherNo;
    }
    else{
        $voucherNo = $maxVoucherNo;
    } 


  $result = array();
  $result["voucher_no"] = $voucherNo; 
  echo json_encode($result);

  //SELECT * from tbl_location where id in (4,5,6)


}



//Get Location

if(isset($_POST['Id'])&& !empty($_POST['Id'])){
  
   
   $id = $_POST['Id'];



 $Qry = "SELECT * FROM tbl_project where Id = $id ";
 

  $getLoc = mysqli_query($con,$Qry);
     
  $rwLoc = mysqli_fetch_assoc($getLoc);
  $loc = $rwLoc['location'];
  

   $getLocQry = "SELECT * from tbl_location where id in ($loc)";

  $Loc = mysqli_query($con,$getLocQry);
    

  // Fetch all
  $result = mysqli_fetch_all($Loc,MYSQLI_ASSOC);


  echo json_encode($result);

  //SELECT * from tbl_location where id in (4,5,6)


}



















?>