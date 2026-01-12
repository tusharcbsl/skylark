<?php
error_reporting(E_ALL);
$command = 'mysql -uphpmyadmin -pCbr@@t##123 ezeefile_saas < /var/www/eprocess/db_file/ezeefile_saas_upgraded_routines.sql';
if(exec($command, $output)){
	print_r($output);
	if(count($output)>0){
		print_r($output);
	}else{
		echo "Import succesfully";
	}
}


?>