<?php

if(isset($_POST['name']) && isset($_POST['pwd']) && !empty($_POST['name']) && !empty($_POST['pwd']))
{
$dbhost='13.126.104.102';
$dbuser='root';
$dbpwd='Isoft@$#@!';
$dbname='ezeefile_saas';
require_once 'db.php';
$name=$_POST['name'];
$pwd=$_POST['pwd'];

        $result=array();
        $validate_user= mysqli_escape_string($con, $_POST['name']);
        $validate_pwd= mysqli_escape_string($con, $_POST['pwd']);
        $validate_pwd=sha1($validate_pwd);
        $validate_user_qry= mysqli_query($con, "select tbl_ag_id,client_id,db_name from tbl_aggregate_user_master where email='$validate_user' and password='$validate_pwd'") or die(mysqli_error($con));
        if(mysqli_num_rows($validate_user_qry)>0)
        {
          $datas= mysqli_fetch_assoc($validate_user_qry);
          $check_validity_qry= mysqli_query($con, "select valid_upto,total_memory,total_user from  tbl_client_master where client_id='$datas[client_id]'");//Query get validity of particular company user
          $validity_date= mysqli_fetch_assoc($check_validity_qry);//fetch validity timestamp from client table
          date_default_timezone_set("Asia/Kolkata");
          if(strtotime(date("Y-m-d"))<$validity_date['valid_upto'])
          {
			  $levelResult = mysqli_query($con, "select sl_name from tbl_storage_level where sl_depth_level = '0'") or die('Error:' . mysqli_error($db_con));
			  $level = mysqli_fetch_assoc($levelResult);
            $result['status']=100;
            $result['cid']=$datas['client_id'];// set company id of particular user
            $result['dbname']=$datas['db_name'];// set db name of the company
            $result['dbhost']=$dbhost;
            $result['dbuser']=$dbuser;
            $result['dbpwd']=$dbpwd;
            $result['ftphost']=FILE_SERVER;
            $result['ftpuser']=FTP_USER;
            $result['ftppwd']=FTP_PASS;
            $result['memory']=$validity_date['total_memory'];
			$result['rootFolder']=$level['sl_name'];
            $json= json_encode($result);
            echo $json;
          }else{
              $result=array("status"=>102,"Error"=>"Validity Expired.");
                $json= json_encode($result);
                echo $json;
          }
        }else{
            $result=array("status"=>101,"Error"=>"Username or Password do not match.");
            $json= json_encode($result);
            echo $json;
        }
}

?>
