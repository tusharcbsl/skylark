<?php

error_reporting(0);
/*
 * To change this license header, choose License Headers in Project Properties.f
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
if ($_SERVER['REQUEST_METHOD'] == 'GET' && realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME'])) {
    header('HTTP/1.0 403 Forbidden', TRUE, 403);
    die(header('location:../../error.html'));
}
$host = $_SERVER['REMOTE_ADDR']; //user's ip
$host1 = $_SERVER['HTTP_HOST']; // url name
define('BASE_URL', '' . (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]" . '/skylark/');
if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    //for WINDOWS operating system
    define('LIBRE_OFFICE', 'C:\LibreOffice\program\soffice'); // for open doc/docx psd,rtf,cdr etc file
    define('GHOST_SCRIPT', 'C:\gs\bin\gswin64c.exe'); // for OCR chnage 
    define('CHANGE_PDF_VERSION', 'C:\gs\bin\gswin64c.exe'); // for chnage pdf version
    define('PAGE_COUNT_EXE', 'C:\Program Files\pdfinfo\pdfinfo'); // for chnage pdf version
} else {
    //for LINUX operating system
    define('LIBRE_OFFICE', 'soffice'); // for open doc/docx psd,rtf,cdr etc file
    define('GHOST_SCRIPT', 'gs'); // for OCR chnage 
    define('CHANGE_PDF_VERSION', 'gs'); // for chnage pdf version
    define('PAGE_COUNT_EXE', 'pdfinfo'); // for chnage pdf version
}
define('FTP_ENABLED', TRUE); // enable disable FTP storage true/false
define('add_page_inbtwn', TRUE);
define('add_page_no', TRUE);
define('CREATE_THUMBNAIL', FALSE); // thumbnail true/false
define('MAIL_BY_SOCKET', false); // enable disable FTP storage true/false
// domain specification
$pieces = parse_url($_SERVER['HTTP_HOST']);
$domain = isset($pieces['host']) ? $pieces['host'] : $pieces['path'];
// echo $domain;


// $fileserver = "192.168.3.7"; // ftp ip of fileserver

// $port = 21; // ftp port of file server

// $ftpUser = "crcdms"; // ftp user

// $ftpPwd = "Crc&%ezeef!l3"; // ftp password
$fileserver = "localhost"; // ftp ip of fileserver

$port = 21; // ftp port of file server

$ftpUser = "Tushar"; // ftp user

$ftpPwd = "crc@123"; // ftp password

// domain specification
$pieces = parse_url($_SERVER['HTTP_HOST']);
$domain = isset($pieces['host']) ? $pieces['host'] : $pieces['path'];

// define('FTP_FOLDER', 'DMS');
// define('FTP_HOST', '192.168.3.7');
// define('FTP_USER', 'crcdms');
// define('FTP_PASSWORD', 'Crc&%ezeef!l3');
// define('FTP_PORT', '21');
define('FTP_FOLDER', 'DMS');
define('FTP_HOST', 'localhost');
define('FTP_USER', 'Tushar');
define('FTP_PASSWORD', 'crc@123');
define('FTP_PORT', '21');

if (preg_match('/(?P<domain>[a-z0-9][a-z0-9-]{1,63}.[a-z.]{2,6})$/i', $domain, $regs))
    $domain_name = $regs['domain'];
$doname = explode(".", $domain_name);
if ($doname[0] == "ezeefile") {
    $projectName = "Ezeefile";
    $projectLogo = "ezeefile.png";

    $crtinfo = "SSLCertificateFile /etc/apache2/ssl/ezeefile/new/ada63a16a8c6cc02.crt
        SSLCertificateKeyFile /etc/apache2/ssl/ezefilein.key
		SSLCertificateChainFile /etc/apache2/ssl/ezeefile/new/gd_bundle-g2-g1.crt";
} else if ($doname[0] == "ezeeprocess") {
    $projectName = "EzeeProcess";
    $projectLogo = "ezeeprocess.png";
    $crtinfo = "SSLCertificateChainFile /etc/apache2/ssl/ezeeprocess/gd_bundle-g2-g1.crt
        SSLCertificateFile /etc/apache2/ssl/ezeeprocess/2caf7e76a12d8d86.crt
        SSLCertificateKeyFile /etc/apache2/ssl/ezeeprocess/ezeeprocess.key";
} else if ($doname[0] == "ezeeoffice") {
    $projectName = "EzeeOffice";
    $projectLogo = "ezeeoffice.png";
    $crtinfo = "SSLCertificateChainFile /etc/apache2/ssl/ezeeoffice/gd_bundle-g2-g1.crt
        SSLCertificateFile /etc/apache2/ssl/ezeeoffice/e91131084dd93cdf.crt
        SSLCertificateKeyFile /etc/apache2/ssl/ezeeoffice/ezeeoffice.key";
} else {
    $projectName = "EzeeProcess";
    $projectLogo = "ezeeprocess.png";
    $crtinfo = "SSLCertificateChainFile /etc/apache2/ssl/ezeeprocess/gd_bundle-g2-g1.crt
        SSLCertificateFile /etc/apache2/ssl/ezeeprocess/2caf7e76a12d8d86.crt
        SSLCertificateKeyFile /etc/apache2/ssl/ezeeprocess/ezeeprocess.key";
}


$dbHost="localhost"; // database host
$dbUser="root"; // database user
$dbPwd="root"; // database password
$dbName = "skylark_ezeeoffice"; // database name
$mainDirectorySrc = $_SERVER['DOCUMENT_ROOT'] . "/ezeefile_saas_new/"; //main source directory change according to requirement
$mainDbName = "skylark_ezeeoffice"; //dynamic 


// $dbHost="localhost"; // database host
// $dbUser="root"; // database user
// $dbPwd="root"; // database password
// $dbName = "core_application_new"; // database name
// $mainDirectorySrc = $_SERVER['DOCUMENT_ROOT'] . "/ezeefile_saas_new/"; //main source directory change according to requirement
// $mainDbName = "core_application_new"; //dynamic 
$clientKey = "LCq7rdWjtJ2vEy/fd+SAjrZmmIgw8JMz384qHA9m9jmRcKHTzSdcLPM2ItOFXpSXKbSv0PK9HVkmogvHNJAFRoRnKqRqBLKcE0Iud1BzV8Y="; //this is static for super client 

$ocrUrl = BASE_URL; //db and directory name is similar
//define('KM_URL', "https://keymanagement.ezeefile.in/application/API/validateLicense");
//define('LICENSE_KEY', "v4BzSYf8OAy9ZTZZUah2+L/gKIgYqup8oiiFw3fxvceYgNQOnZX7STLaAF6MUtI74lBUdFpOADwqa/UqK8cUiw==");
//define('ALLOWED_EXTN', array('png', 'jpg', 'jpeg', 'gif', 'tiff', 'odt', 'rtf', 'bmp', 'tif', 'pdf', 'doc', 'docx', 'txt',  'xls', 'xlxs', 'csv', 'mp3', 'mp4', '3gp', 'mkv', 'zip', 'rar'));
