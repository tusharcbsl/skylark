<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
error_reporting(0);
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
        
        //sk@180319: Check file type before Ocr
        $ftype = strtolower(pathinfo($inputFile, PATHINFO_EXTENSION));
        $img_array=array('jpg','jpeg','png','bmp','pnm','jfif','jpeg','tiff');
        
         if(in_array($ftype,$img_array)){
          $execCMD = $this->generateImageOcr($inputFile, $output, $outDIr, $doc_id);  
        }else{
             
          $execCMD = $this->generatePathImagemagick($inputFile, $output, $outDIr, $doc_id);  
        }
        if ($execCMD) {
           // unlink($inputFile);
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
           
            $im->setImageFormat('jpg');
            
            $im->setImageBackgroundColor('#ffffff');
            
            $im = $im->mergeImageLayers(0);
            
            if ($im->writeImage($outputDir . 'ocr-' . $j . '.jpg')) {
//                if ($pdfPage > 1) {
                $strOutss = $this->outputDir . "/" . $docId .".txt";
                $strIn = $this->outputDir . "/" . $docId . "/ocr-" . $j . ".jpg";
                $textImage = (new TesseractOCR($strIn))
                        ->lang('eng')
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
		
		rmdir($this->outputDir . "/" . $docId);
		
        return TRUE;
        //$winPathImage = "convert -density 250 -units PixelsPerInch " . $inputDir . " " . $outputDir;
        // return array("path" => $winPathImage, "total" => $pdfPage, "folderName" => $docId);
    }

    /*
     * count number of pages
     */
    
   //sk@180319 : Function designed for Image OCR.
   public function generateImageOcr($inputFile, $output, $outDIr, $docId) {
	   
        if (!is_dir($outDIr . "/" . $docId)) {
           // mkdir($outDIr . "/" . $docId, 0777);
        }
        $inputDir = $this->currentDir . "/" . $inputFile;
        //$outputDir = $this->currentDir . "/" . $this->outputDir . "/" . $docId . "/" . $output;

		$strOutss = $this->outputDir . "/" . $docId . ".txt";
		$strIn = $inputDir;
		
		$textImage = (new TesseractOCR($strIn))
				->lang('eng')
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
                
        return TRUE;
        //$winPathImage = "convert -density 250 -units PixelsPerInch " . $inputDir . " " . $outputDir;
        // return array("path" => $winPathImage, "total" => $pdfPage, "folderName" => $docId);

 }
 
 function count_pages($pdfname) {

        $pdftext = file_get_contents($pdfname);

        $num = preg_match_all("/\/Page\W/", $pdftext, $dummy);

        return $num;
    }
}





