<?php
    require_once './application/config/database.php';
    function getPDFPages($document) {
        // echo $document;
        $cmd = "pdfinfo.exe";  // Windows
        // Parse entire output
        // Surround with double quotes if file name has spaces

        exec("$cmd \"$document\"", $output);
        // Iterate through lines

        $pagecount = 0;
        foreach($output as $op) {
            // Extract the number
            if(preg_match("/Pages:\s*(\d+)/i", $op, $matches) === 1) {
                $pagecount = intval($matches[1]);
                break;
            }
        }
        return $pagecount;
    }

    
    if (isset($_POST['action']) && $_POST['action'] == "countPdfPage") {
        echo $count = getPDFPages($_FILES['file']['tmp_name']);
    }