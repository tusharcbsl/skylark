<?php
require '../../sessionstart.php';
require '../config/database.php';

if(isset($_POST['filepath'])){
    $fileName= $_POST['filename'];
	
	if (strpos($filepath, 'extract-here') !== true) {
		 die();
	}
	
    $dfilepath='../../viewer-pdf/'.$fileName;
    if(FTP_ENABLED){
        
        $filepath = $_POST['filepath'];
        if(unlink('../'.$filepath)){
        
            $file_dir =   substr($filepath,0, strrpos($filepath, "/"));

            if (is_dir_empty($file_dir)) {

                rmdir($file_dir);
            }
         }
    }
    if(file_exists($dfilepath)){
        unlink($dfilepath);
    }
}

function is_dir_empty($dir) {
  if (!is_readable($dir)) return NULL; 
  return (count(scandir($dir)) == 2);
}
?>
