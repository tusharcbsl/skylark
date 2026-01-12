<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
error_reporting(E_ALL);
require_once './ocr_dms/vendor/autoload.php';

use thiagoalessio\TesseractOCR\TesseractOCR;

class OCR {

    private $windowsPath, $currentDir, $outputDir;

    public function __construct($sl_name) {
        // $this->windowsPath = "C:";
        $this->currentDir = __DIR__;
        $this->outputDir = $sl_name . "/TXT";
    }
    
    public function get_outputdir(){
        return $this->outputDir;
    }

    /*
     * Execute OCR Flow
     */

    public function ocrDone($inputFile, $output, $doc_id) {
        $outDIr = $this->currentDir . "/" . $this->outputDir;
        if (!is_dir($outDIr)) {
            mkdir($this->outputDir, 0777);
        }

        $execCMD = $this->generatePathImagemagick($inputFile, $output, $outDIr, $doc_id);
        if ($execCMD) {
            unlink($inputFile);
        }
        // exec('env MAGICK_THREAD_LIMIT=1');
        // die();
        /*  $shell = exec($execCMD['path']);



          if ($shell['return'] != 0) {
          throw new Exception("Convertion Failure! Contact Server Admin.");
          } else {
          unlink($inputFile);
          if ($execCMD['total'] > 1) {
          for ($i = 0; $i < $execCMD['total']; $i++) {
          $strOutss = $this->outputDir . "/" . $execCMD['folderName'] . "/" . $i . ".txt";
          $strIn = $this->outputDir . "/" . $execCMD['folderName'] . "/ocr-" . $i . ".jpg";
          $textImage = (new TesseractOCR($strIn))
          ->lang('eng', 'hin')
          ->run();
          if (!empty($textImage)) {
          $myfile = fopen($strOutss, "w");
          fwrite($myfile, $textImage);
          fclose($myfile);
          if ($return != 0) {
          throw new Exception("Convertion Failure! Contact Server Admin.");
          } else {
          unlink($strIn); //remove all image after all txt create
          }
          }
          }
          } else {
          $strOutss = $this->outputDir . "/" . $execCMD['folderName'] . "/" . "1.txt";
          $strIn = $this->outputDir . "/" . $execCMD['folderName'] . "/ocr.jpg";
          $textImage = (new TesseractOCR($strIn))
          ->lang('eng', 'hin')
          ->run();
          if (!empty($textImage)) {
          $myfile = fopen($strOutss, "w");
          fwrite($myfile, $textImage);
          fclose($myfile);
          if ($return != 0) {
          throw new Exception("Convertion Failure! Contact Server Admin.");
          } else {
          unlink($strIn); //remove all image after all txt create
          }
          }
          }
         * 
         */
        return true;
    }

//    }

    /*
     * Step 1st
     * Generate path for imagemagick
     */

    public function generatePathImagemagick($inputFile, $output, $outDIr, $docId) {
        if (!is_dir($outDIr . "/" . $docId)) {
            mkdir($outDIr . "/" . $docId, 0777);
        }
        $inputDir = $this->currentDir . "/" . $inputFile;
        $outputDir = $this->currentDir . "/" . $this->outputDir . "/" . $docId . "/" . $output;
        //echo $inputDir;
        //die;

        $pdfPage = $this->count_pages($inputDir); //convert -density 250 -units PixelsPerInch
        $im = new Imagick();
        for ($j = 0; $j < $pdfPage; $j++) {
            $im->setResolution(300, 300);
            $im->readimage($inputDir . "[$j]");
            $im->setImageFormat('png');
            if ($im->writeImage($outputDir . 'ocr-' . $j . '.jpg')) {
//                if ($pdfPage > 1) {
                $strOutss = $this->outputDir . "/" . $docId . "/" . $j . ".txt";
                $strIn = $this->outputDir . "/" . $docId . "/ocr-" . $j . ".jpg";
                $textImage = (new TesseractOCR($strIn))
                        ->lang('eng', 'hin')
                        ->run();
                if (!empty($textImage)) {
                    $myfile = fopen($strOutss, "w");
                    fwrite($myfile, $textImage);
                    fclose($myfile);
                    if ($return != 0) {
                        throw new Exception("Convertion Failure! Contact Server Admin.");
                    } else {
                        unlink($strIn); //remove all image after all txt create
                    }
                }
//                } else {
//                    $strOutss = $this->outputDir . "/" . $docId . "/" . "1.txt";
//                    $strIn = $this->outputDir . "/" . $docId . "/ocr.jpg";
//                    $textImage = (new TesseractOCR($strIn))
//                            ->lang('eng', 'hin')
//                            ->run();
//                    if (!empty($textImage)) {
//                        $myfile = fopen($strOutss, "w");
//                        fwrite($myfile, $textImage);
//                        fclose($myfile);
//                        if ($return != 0) {
//                            throw new Exception("Convertion Failure! Contact Server Admin.");
//                        } else {
//                            unlink($strIn); //remove all image after all txt create
//                        }
//                    }
//                }
                $im->clear();
                $im->destroy();
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
//$ocr = new OCR("extract-here/testthree");
//echo $ocr->get_outputdir();
//
$ocr = new OCR("extract-here/testthree");
$ocr->ocrDone("extract-here/testthree/kundu.pdf", "", 14);
//$ocr = new OCR($_POST['outputDir']);
//$ocr->ocrDone($_POST['inputDir'], "", $_POST['docId']);
//echo $textImage= (new TesseractOCR("extract-here/freelancer/freelancer/TXT/123/ocr-0.jpg"))
//                            ->run();
//  $file=fopen("extract-here/freelancer/freelancer/TXT/123/1.txt","w");
//  fwrite($file,$textImage);
//  fclose($file);
