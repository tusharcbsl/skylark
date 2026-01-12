<?php
    ini_set('memory_limit', '-1');
    set_time_limit(0);
   
    require_once './application/config/database.php';
    //require_once './application/config/db_sql.php';
    require_once './application/pages/head.php';
    require_once './application/pages/function.php';
    require_once './excel-viewer/excel_reader.php';
   
    $doc_path='extract-here/demo/sample.doc';
    if(file_exists($doc_path)){
     echo 'okkk';
     $contents = read_doc($filename);
     var_dump($contents);
    }
  
    ?>
    