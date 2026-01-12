<?php
	// FTP server details
	$ftpHost   = '192.168.3.15';
	$ftpUsername = 'ftpuser';
	$ftpPassword = 'Cb5l#$321';

	// open an FTP connection
	$connId = ftp_connect($ftpHost) or die("Couldn't connect to $ftpHost");

	// login to FTP server
	$ftpLogin = ftp_login($connId, $ftpUsername, $ftpPassword);
	
	$targetDir = "a/b/c/d";
	$dir_arr = explode("/", $targetDir);
	
	$targetPath = 'testingftp';
	
	foreach($dir_arr as $dir) {
		$targetPath .= '/'.$dir;
		//echo $targetPath .'<br>';
		
		//if (!ftp_chdir($connId, $targetPath)) {
			if (@ftp_mkdir($connId, $targetPath)) {
				echo "Directory created successfully: $targetPath";
			} else {
				//echo "Failed to create directory: $targetPath";
			}
		//}
	}
	
	
	
	ftp_close($connId);

?>