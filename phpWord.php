<?php

class phpWord
{
    public function createHtmlToDoc($fileName,$htmlString) 
    {
        require_once './phpWordOffice/bootstrap.php';
        // Creating the new document...
        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        $section = $phpWord->addSection();     
        \PhpOffice\PhpWord\Shared\Html::addHtml($section, $htmlString, false, false);
        $phpWord->save($fileName);
    }
    

}

?>