<?php

require_once "tools/mpdf/mpdf.php"; // MPDF library

function exportPDF($text, $path) {
    try {
        $pdf = new mPDF();
        $pdf->WriteHTML($text);
        $pdf->Output($path, 'F'); //$pdf->Output('../files/example.pdf','F');

        return true;
    } catch (Exception $e) {
        return false;
    }
}

function exportPDFSize($text, $path, $height, $width) {
    try {
        $pdf = new mPDF('utf-8', array($width, $height));
        $pdf->WriteHTML($text);
        $pdf->Output($path, 'F'); //$pdf->Output('../files/example.pdf','F');

        return true;
    } catch (Exception $e) {
        return false;
    }
}

function printFinalPdf($path,$content) {
    $mpdf = new mPDF();
    $mpdf->SetImportUse();
    $pagecount = $mpdf->SetSourceFile($path);
// Import the last page of the source PDF file
    $tplId = $mpdf->ImportPage($pagecount);
    $mpdf->UseTemplate($tplId);
    $mpdf->WriteHTML($content);
    $mpdf->Output();
}
