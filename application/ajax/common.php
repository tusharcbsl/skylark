<?php

ob_start();
@session_start();
require_once '../../classes/security.php';

if (!isset($_SESSION['cdes_user_id'])) {
    header("location:../../logout");
}
require './../config/database.php';



if(isset($_POST['action'])){

    if($_POST['action']=='getToken'){
        
        getToken();

    }else if($_POST['action']=='checkSubDomain'){

    	checkSubDomain($db_con, $domain_name);

    }else if($_POST['action']=='checkCompanyName'){

    	checkCompanyName($db_con);
    }else if($_POST['action']=='checkEmailId'){

    	checkEmailId($db_con);
    }else if($_POST['action']=='getFileMetaData'){

    	getFileMetaData($db_con);
    }

}

function getToken(){
    $response = array();
    $response['token'] = csrfToken::generate();
    echo json_encode($response);
}


function checkSubDomain($db_con, $domain_name){


	$subDomain = filter_input(INPUT_POST, "subd");

    $subDomain = preg_replace("/[^a-zA-Z0-9_ ]/", "", $subDomain); //filter name
    $FullSubDomain = $subDomain . "." . $domain_name; //new subdomain
    $subDomain = mysqli_real_escape_string($db_con, $subDomain);
	//echo "select * from  `tbl_client_master` where  subdomain='$FullSubDomain'";
	$chkDuplicateCompany = mysqli_query($db_con, "select * from  `tbl_client_master` where  subdomain='$FullSubDomain'");


    if (mysqli_num_rows($chkDuplicateCompany) > 0) {

    	echo"test^1";

    }else{

    	echo"test^0";

    }
}

function checkEmailId($db_con){

	$email  = $_POST['email'];
	$chkDuplicateCompany = mysqli_query($db_con, "select * from  `tbl_client_master` where  email='$email'");
    if (mysqli_num_rows($chkDuplicateCompany) > 0) {

    	echo"test^1";

    }else{

    	echo"test^0";

    }
}


function checkCompanyName($db_con){

	$company = filter_input(INPUT_POST, "cname");
    // $company = preg_replace("/[^0-9A-Za-z]/", "", $company); //filter phone
    $company = mysqli_real_escape_string($db_con, $company);



	$chkDuplicateCompany = mysqli_query($db_con, "select * from  `tbl_client_master` where company='$company'");

    if (mysqli_num_rows($chkDuplicateCompany) > 0) {

    	echo"test^1";

    }else{

    	echo"test^0";

    }
}


function getFileMetaData($db_con){
	
	$docId = $_POST['docId'];
	$slId = $_POST['slId'];
        
       echo '<table class="table table-bordered">';
	 mysqli_set_charset($db_con, "utf8");
	$getMetaId = mysqli_query($db_con, "select * from tbl_metadata_to_storagelevel where sl_id = '$slId'") or die('Error:gg' . mysqli_error($db_con));
        if(mysqli_num_rows($getMetaId)>0){
            while ($rwgetMetaId = mysqli_fetch_assoc($getMetaId)) {

                    $getMetaName = mysqli_query($db_con, "select * from tbl_metadata_master where id = '$rwgetMetaId[metadata_id]'") or die('Error:' . mysqli_error($db_con));

                    while ($rwgetMetaName = mysqli_fetch_assoc($getMetaName)) {

                            $meta = mysqli_query($db_con, "select `$rwgetMetaName[field_name]` from tbl_document_master where doc_id='$docId'");
                            $rwMeta = mysqli_fetch_assoc($meta);

                            if ($rwMeta[$rwgetMetaName['field_name']]!="") {
                                echo "<tr>";
                                    if ($rwgetMetaName['field_name'] == 'noofpages' || $rwgetMetaName['field_name'] == 'filename') {

                                    } else {
                                            echo "<td><label>" . ucfirst(str_replace("_"," ",$rwgetMetaName['field_name'])) . "</label>  </td>";
                                            if ($rwMeta[$rwgetMetaName['field_name']] != '0000-00-00 00:00:00') {

                                                    echo '<td>'.$rwMeta[$rwgetMetaName['field_name']].'</td>';
                                            }

                                    }
                                echo "</tr>";
                            }
                    }
            }
        }else{
             echo '<tr><td>No metadata available.</td></tr>';
        }
	
         echo '</table>';
}


?>

