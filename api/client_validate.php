<?php
error_reporting(E_ALL&~E_WARNING&~E_NOTICE&~E_DEPRECATED);
if ( $_SERVER['REQUEST_METHOD']=='GET' && realpath(__FILE__) == realpath( $_SERVER['SCRIPT_FILENAME'] ) ) {
        header( 'HTTP/1.0 403 Forbidden', TRUE, 403 );
        die( header( 'location:../../error.html' ) );
    }
$date=date("Y-m-d H:i:s");
$dbhost='192.168.2.108';
$dbuser='root';
$dbpwd='root';
$dbname1='ezeefile_saas';
//error_reporting(E_ALL);
$db_con= mysqli_connect($dbhost, $dbuser, $dbpwd, $dbname1)or die("unable to connect");
     
?>
