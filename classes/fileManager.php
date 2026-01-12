<?php
/*  ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
error_reporting(E_ALL); */

require directoryDepth() . 'aws/vendor/autoload.php';

require_once directoryDepth() . 'classes/ftp.php';
// include '../application/pages/function.php';

use Aws\S3\S3Client;
use Aws\Exception\AwsException;
use Aws\S3\MultipartUploader;
use Aws\Exception\MultipartUploadException;

class fileManager
{

	private $dbcon, $s3Client, $ftp, $bucketname, $servertype, $host, $port, $username, $password;

	function __construct()
	{
		global $db_con;
		$this->dbcon = $db_con;
		$fs = $this->getFileServerCredentials();
		$this->host = $fs['host'];
		$this->port = $fs['port'];
		$this->username = $fs['username'];
		$this->password = $fs['password'];
		$this->bucketname = $fs['bucketname'];
		$this->servertype = $fs['servertype'];
		if ($fs['servertype'] == 'S3') {
			$this->s3Client = new S3Client([
				'region' => 'ap-south-1',
				'version' => 'latest',
				'credentials' => [
					'key' => $fs['access_key'],
					'secret' => $fs['secret_access_key'],
				]
			]);
		} else {
			$this->ftp = new ftp();
		}
	}

	private function getFileServerCredentials()
	{

		$credentails = mysqli_query($this->dbcon, "select * from tbl_file_server_details where status=1") or die('Error:' . mysqli_error($this->dbcon));

		if (mysqli_num_rows($credentails)) {
			$row = mysqli_fetch_assoc($credentails);

			$secret = ($row['secret_access_key'] != "") ? explode(",", $row['secret_access_key']) : "";
			$secret_key = ($row['secret_access_key'] != "") ? $this->ezeefile_crypt($secret[0], 'd') . $this->ezeefile_crypt($secret[1], 'd') : "";
			return array(
				'servertype' => $row['servertype'],
				'bucketname' => $row['bucket_name'],
				'access_key' => ($row['access_key'] != "") ? $this->ezeefile_crypt($row['access_key'], 'd') : "",
				'secret_access_key' => $secret_key,
				'host' => $row['host'],
				'port' => $row['port'],
				'username' => ($row['username'] != "") ? $this->ezeefile_crypt($row['username'], 'd') : "",
				'password' => ($row['password'] != "") ? $this->ezeefile_crypt($row['password'], 'd') : ""
			);
		} else {
			echo "sc not found";
			exit;
		}
	}

	private function ezeefile_crypt($string, $action = 'e')
	{
		// you may change these values to your own
		$secret_key = 'cbsldms_key';
		$secret_iv = 'cbsldms_iv';
		$output = false;
		$encrypt_method = "aes-128-cbc";
		$key = substr(hash('sha1', $secret_key), 0, 32);
		$iv = substr(hash('sha1', $secret_iv), 0, 32);

		if ($action == 'e') {
			$output = base64_encode(openssl_encrypt($string, $encrypt_method, $key, 0, $iv));
			$filteroutput1 = str_replace("=", "ezee", $output);
		} else if ($action == 'd') {
			$output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
			$filteroutput1 = str_replace("ezee", "=", $output);
		}

		return $filteroutput1;
	}

	public function conntFileServer()
	{

		if ($this->servertype == 'ftp') {

			$this->ftp->conn($this->host, $this->port, $this->username, $this->password);
		}
	}

	public function mkFtpDir($localFilePath, $targetDir)
	{
		$ftpHost   = FTP_HOST;
		$ftpUsername = FTP_USER;
		$ftpPassword = FTP_PASSWORD;

		$connId = ftp_connect($ftpHost) or die("Couldn't connect to $ftpHost");

		// login to FTP server
		$ftpLogin = ftp_login($connId, $ftpUsername, $ftpPassword);
		$dir_arr = explode("/", $targetDir);
		array_pop($dir_arr);
		$targetPath = FTP_FOLDER;

		foreach ($dir_arr as $dir) {
			$targetPath .= '/' . $dir;
			@ftp_mkdir($connId, $targetPath);
		}

		ftp_set_option($connId, FTP_BINARY, true);
		if (ftp_put($connId, FTP_FOLDER . '/' . $targetDir, $localFilePath, FTP_BINARY)) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public function uploadFile(string $source, string $dest, $fileunlink = false)
	{

		if ($this->servertype == 'S3') {
			// upload file on s3			
			$status = $this->uploadFileS3($source, $dest);
			$fileunlink = TRUE;
		} else {
			// upload file on ftp
			if (FTP_ENABLED) {
				//echo $source;
				//echo '<br>';

				//echo $dest;
				$status = $this->mkFtpDir($source, $dest);
				// exit;

				// $status = $this->uploadFileFTP($source, $dest);

				if ($status) {
					// echo 'move in ftp';
					$fileunlink = TRUE;
				} else {
					// echo 'not move in ftp';
					// 	exit;
					// $fileunlink = TRUE;
				}
			}
		}

		// delete file after transfer to file server
		if ($fileunlink) {
			if (file_exists($source)) {
				unlink($source);
			}
		}
		return $status;
	}

	public function syncUpload(string $source, string $dest, $fileunlink = false)
	{

		if ($this->servertype == 'S3') {
			$status = $this->uploadFileS3($source, $dest);
			$fileunlink = TRUE;
		} else {
			if (FTP_ENABLED) {

				$status = $this->mkFtpDir($source, $dest);

				if ($status) {
					$isTransfer = TRUE;
				} else {
					$isTransfer = FALSE;
				}
			}
		}
		return $isTransfer;
	}

	// upload file on aws s3
	public function uploadFileS3($source, $dest)
	{

		$uploader = new MultipartUploader($this->s3Client, $source, [
			'bucket' => $this->bucketname,
			'key' => $dest,
		]);

		try {

			$result = $uploader->upload();
			//$this->s3Client.closedir();
			return  $result['ObjectURL'];
		} catch (MultipartUploadException $e) {
			echo $e->getMessage() . "\n";
		}
	}

	public function getFile(array $rwFile, $depthdir = "")
	{

		$filePath = $rwFile['doc_path'];

		if (!file_exists($depthdir . 'extract-here/' . $filePath)) {

			$slid = $rwFile['doc_name'];
			$fileName = (strpos($rwFile['old_doc_name'], '.') !== false) ? substr($rwFile['old_doc_name'], 0, strrpos($rwFile['old_doc_name'], '.')) : $rwFile['old_doc_name'];
			$doc_extn = $rwFile['doc_extn'];
			$storage = mysqli_query($this->dbcon, "select sl_name from tbl_storage_level where sl_id='$slid'") or die('Error:' . mysqli_error($this->dbcon));
			$rows = mysqli_fetch_assoc($storage);
			$folderName = 'temp/';
			$folderName = $folderName . $_SESSION['cdes_user_id'] . '/' . preg_replace('/[^A-Za-z0-9\-]/', '', $rows['sl_name']);
			if (!dir($folderName)) {
				mkdir($folderName, 0777, TRUE);
			}

			// echo $fileName = ezeefile_crypt($fileName, $action = 'e');
			$dest = $depthdir . $folderName . '/' . preg_replace('/[^A-Za-z0-9\-]/', '', $fileName) . '.' . $doc_extn;
			// echo '<br>';
			if ($this->servertype == 'S3') {
				$source = ROOT_FTP_FOLDER . '/' . $filePath;
				// download file from s3
				$this->getFileFromS3($source, $dest);
			} else {
				$source = FTP_FOLDER . '/' . ROOT_FTP_FOLDER . '/' . $filePath;
				// echo $filePath;
				// download file from ftp
				$this->getFileFromFTP($source, $dest);
			}
		} else {
			$dest = $depthdir . 'extract-here/' . $filePath;
		}
		// echo $source;
		// echo '<br>';
		// echo $dest;
		decrypt_my_file($dest);

		return $dest;
	}

	public function downloadFile(string $source, string $dest)
	{

		if (!empty($source) && !empty($dest)) {

			if ($this->servertype == 'S3') {
				// download file from s3
				return $this->getFileFromS3($source, $dest);
			} else {
				// download file from ftp
				return $this->getFileFromFTP($source, $dest);
			}
		}
	}

	// download file from s3
	public function getFileFromS3($source, $dest)
	{

		try {
			// Get the object.
			$result = $this->s3Client->getObject([
				'Bucket' => $this->bucketname,
				'Key'    => $source
			]);

			return file_put_contents($dest, $result['Body']);
		} catch (S3Exception $e) {
			echo $e->getMessage() . PHP_EOL;
		}
	}

	// upload file on ftp server
	public function uploadFileFTP($dest, $source)
	{
		try {
			if ($this->ftp->put($source, $dest)) {
				return true;
			} else {
				return false;
			}
		} catch (Exception $e) {
			echo 'Message: ' . $e->getMessage();
		}
	}

	// public function make_dir() {
	// 	try {
	// 		if($this->ftp->makeDir(FTP_FOLDER.'/a', false)) {
	// 			return true;
	// 		}else{
	// 			return false;
	// 		}
	// 	}
	// 	catch(Exception $e) {
	// 		echo 'Message: ' .$e->getMessage();
	// 	}
	// }

	// download file from ftp server
	public function getFileFromFTP($source, $dest)
	{
		// FTP server details
		$ftpHost = FTP_HOST;
		$ftpUsername = FTP_USER;
		$ftpPassword = FTP_PASSWORD;

		// Connect to FTP server
		$connId = ftp_connect($ftpHost);

		// Login to FTP server
		$ftpLogin = ftp_login($connId, $ftpUsername, $ftpPassword);

		// Enable passive mode
		ftp_pasv($connId, true);

		// Check if file exists
		$fileList = ftp_rawlist($connId, dirname($source));
		$fileExists = false;

		foreach ($fileList as $file) {
			if (strpos($file, basename($source)) !== false) {
				$fileExists = true;
				break;
			}
		}
		// Download the file
		$result = ftp_get($connId, $dest, $source, FTP_BINARY);
		ftp_close($connId);

		return $result;
	}


	// download file
	public function deleteFile(string $filePath)
	{

		if (!empty($filePath)) {

			if ($this->servertype == 'S3') {
				// delete file from s3
				return $this->deleteFileFromS3($filePath);
			} else {
				// delete file from ftp
				return $this->deleteFileFromFTP($filePath);
			}
		}
	}

	// delete file from s3
	public function deleteFileFromS3($filePath)
	{

		try {
			$info = $this->s3Client->doesObjectExist($this->bucketname, $filePath);
			if ($info) {
				$result = $this->s3Client->deleteObject([
					'Bucket' => $this->bucketname,
					'Key'    => $filePath
				]);
			}
			return true;
		} catch (S3Exception $e) {
			echo $e->getMessage() . PHP_EOL;
		}
	}

	// delete file from ftp
	public function deleteFileFromFTP($filePath)
	{

		try {
			if ($this->ftp->fileExists($filePath)) {

				if ($this->ftp->singleFileDelete($filePath)) {
					return true;
				} else {
					return false;
				}
			} else {
				return true;
			}
		} catch (Exception $e) {
			echo 'Message: ' . $e->getMessage();
		}
	}

	function __destruct()
	{
		if ($this->servertype == 'ftp') {
			$this->ftp->closeConn();
		}
	}
}
