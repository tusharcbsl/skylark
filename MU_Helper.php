<?php

error_reporting(0);
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$sheetnum = $_POST['sheetnumber'];
$celldata = $_POST['cellInfo'];
$inputFileType = $_POST['filetype'];
$inputFileName = $_POST['filename'];
$actiontype = $_POST['actiontype'];


switch ($actiontype) {
    case "cell_value":
        $helper = new MU_Helper($sheetnum, $celldata, $inputFileType, $inputFileName);
        echo $helper->fetchCellValue();


        break;
    case "updatesheet":
        $helper = new MU_Helper($sheetnum, $celldata, $inputFileType, $inputFileName);
        echo $helper->updateCurrentDataSheet();


        break;


    default:
        break;
}

class MU_Helper {

    public $sheetnum, $celldata, $inputFileType, $inputFileName;

    function __construct($sheetnum, $celldata, $inputFileType, $inputFileName) {
        $this->sheetnum = $sheetnum;
        $this->celldata = $celldata;
        $this->inputFileType = $inputFileType;
        $this->inputFileName = $inputFileName;
    }

    public function fetchCellValue() {
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($this->inputFileType);
        $spreadsheet = $reader->load($this->inputFileName);
        $spreadsheet->getSheet($this->sheetnum);
        if ($spreadsheet->getActiveSheet()->getCell($this->celldata)->isFormula()) {
            $out = $spreadsheet->getActiveSheet()->getCell($this->celldata)->setFormulaAttributes($this->celldata);
        } else {
            $out = $spreadsheet->getActiveSheet()->getCell($this->celldata)->getValue();
        }
        return $out;
    }

    public function updateCurrentDataSheet() {
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($this->inputFileName);
        $worksheet = $spreadsheet->getSheet($this->sheetnum);
       
        $checkstatus = 0;

        for ($i = 0; $i < count($this->celldata); $i++) {
            if ($this->celldata[$i]['isformula'] == 1) {
                $spreadsheet->getActiveSheet()->setCellValue($this->celldata[$i]['cellid'], $this->celldata[$i]['formulaapply']);
            } else {
                $worksheet->getCell($this->celldata[$i]['cellid'])->setValue($this->celldata[$i]['textdata']);
            }

            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, $this->inputFileType);

            $writer->save($this->inputFileName);
            $checkstatus = 1;
        }

        if ($checkstatus == 1) {
            return json_encode(array("status" => 1));
        }
    }

}
