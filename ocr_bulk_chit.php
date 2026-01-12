<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
 
/* ini_set('display_errors', 1); 
ini_set('display_startup_errors', 1);  */
error_reporting(0);
require_once './ocr_dms/vendor/autoload.php';
require_once './application/pages/function.php';

use thiagoalessio\TesseractOCR\TesseractOCR;

class OCR {

    private $windowsPath, $currentDir, $outputDir;

    public function __construct($sl_name) {
        // $this->windowsPath = "C:";
        $this->currentDir = __DIR__;
        $this->outputDir = $sl_name . "/TXT";
    }

    /*
     * Execute OCR Flow
     */

    public function ocrDone($inputFile, $output, $doc_id) {
        $outDIr = $this->currentDir . "/" . $this->outputDir;
        if (!is_dir($outDIr)) {
            mkdir($this->outputDir, 0777);
        }

        //sk@180319: Check file type before Ocr
        $ftype = strtolower(pathinfo($inputFile, PATHINFO_EXTENSION));
        $img_array = array('jpg', 'jpeg', 'png', 'bmp', 'pnm', 'jfif', 'jpeg', 'tiff');
        if (in_array($ftype, $img_array)) {
            $execCMD = $this->generateImageOcr($inputFile, $output, $outDIr, $doc_id);
        } else {
            $execCMD = $this->generatePathImagemagick($inputFile, $output, $outDIr, $doc_id);
        }

        if ($execCMD) {
            //unlink($inputFile);
            return TRUE;
        }
       
        return true;
    }

//    }

    /*
     * Step 1st
     * Generate path for imagemagick
     */
	// Pdf Ocr with the help of imagick & tessract
    public function generatePathImagemagick($inputFile, $output, $outDIr, $docId) {
        if (!is_dir($outDIr . "/" . $docId)) {
            mkdir($outDIr . "/" . $docId, 0777);
        }
        $inputDir = $this->currentDir . "/" . $inputFile;
        $outputDir = $this->currentDir . "/" . $this->outputDir . "/" . $docId . "/" . $output;

        $pdfPage = $this->count_pages($inputDir); //convert -density 250 -units PixelsPerInch
        $im = new Imagick();
        for ($j = 0; $j < $pdfPage; $j++) {
            $im->setResolution(300, 300);
            $im->readimage($inputDir . "[$j]");
            $im->setImageFormat('jpg');
            $im->setImageBackgroundColor('#ffffff');
            $im = $im->mergeImageLayers(0);
            if ($im->writeImage($outputDir . 'ocr-' . $j . '.jpg')) {
                //if ($pdfPage > 1) {
                //$strOutss = $this->outputDir . "/" . $docId . "/" . $j . ".txt";
				$strOutss = $this->outputDir . "/" . $docId .".txt";
                $strIn = $this->outputDir . "/" . $docId . "/ocr-" . $j . ".jpg";
                $textImage = (new TesseractOCR($strIn))
                        ->lang('eng', 'hin')
                        ->run();
                if (!empty($textImage)) {
                    $myfile = fopen($strOutss, "a+");
                    fwrite($myfile, $textImage);
                    fclose($myfile);
                    if ($return != 0) {
                        throw new Exception("Convertion Failure! Contact Server Admin.");
                    } else {
                        unlink($strIn); //remove all image after all txt create
                    }
                }
                unlink($strIn); //remove all image after all txt create

                $im->clear();
                $im->destroy();
            }
        }
		rmdir($this->outputDir . "/" . $docId); //remove all image after all txt create
        //unlink($inputFile);
        //$winPathImage = "convert -density 250 -units PixelsPerInch " . $inputDir . " " . $outputDir;
        // return array("path" => $winPathImage, "total" => $pdfPage, "folderName" => $docId);
        return TRUE;
    }

    //sk@180319 : Function designed for Image OCR.
    public function generateImageOcr($inputFile, $output, $outDIr, $docId) {
        /* if (!is_dir($outDIr . "/" . $docId)) {
            mkdir($outDIr . "/" . $docId, 0777);
        } */
        $inputDir = $this->currentDir . "/" . $inputFile;
        //$outputDir = $this->currentDir . "/" . $this->outputDir . "/" . $docId . "/" . $output;
        $outputDir = $this->currentDir . "/" . $this->outputDir . "/" . $output;

        $strOutss = $this->outputDir . "/" . $docId . ".txt";
        $strIn = $inputDir;
        $textImage = (new TesseractOCR($strIn))
                ->lang('eng')
                ->run();
        if (!empty($textImage)) {
            $myfile = fopen($strOutss, "a+");
            fwrite($myfile, $textImage);
            fclose($myfile);
            if ($return != 0) {
                throw new Exception("Convertion Failure! Contact Server Admin.");
            } else {
                //unlink($strIn); //remove all image after all txt create
            }
        }

        return TRUE;

        //$winPathImage = "convert -density 250 -units PixelsPerInch " . $inputDir . " " . $outputDir;
        // return array("path" => $winPathImage, "total" => $pdfPage, "folderName" => $docId);


    }

    /*
     * count number of pages
     */

    function count_pages($pdfname) {

        $pdftext = file_get_contents($pdfname);

        $num = preg_match_all("/\/Page\W/", $pdftext, $dummy);

        return $num;
    }

}


require_once './application/config/database.php';

/* $ocr = new OCR("extract-here/AFGa/30-03-21");
$ocr->ocrDone("extract-here/AFGa/30-03-21/1627375591UmVqZWN0ZWQtZG9jdW1lbnQ2.pdf", "", 12); */


 $outdir = explode(",", $_POST['outputDir']);
$inputdir = explode(",", $_POST['inputDir']);
$docId = explode(",", $_POST['docId']);
$k = 0;


while ($k < count($docId)) {
	
	// decrypt file before ocr.
	decrypt_my_file($inputdir[$k]);
	 
    $ocr = new OCR($outdir[$k]);
	// call ocr process
    if ($ocr->ocrDone($inputdir[$k], "", $docId[$k])) {
		
		// Update document ocr status while done.
        mysqli_query($db_con, "update tbl_document_master set ocr=1 where doc_id='$docId[$k]'") or die('Error' . mysqli_error($db_con));
        $k++;
    }
}  

//echo $textImage= (new TesseractOCR("extract-here/freelancer/freelancer/TXT/123/ocr-0.jpg"))
//                            ->run();
//  $file=fopen("extract-here/freelancer/freelancer/TXT/123/1.txt","w");
//  fwrite($file,$textImage);
//  fclose($file);
