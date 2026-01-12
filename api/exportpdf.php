<?php
	

require_once "../tools/mpdf/mpdf.php"; // MPDF library

	function exportPDF($text,$path)
	{	
		try 
		{	
			$pdf = new mPDF();
			$pdf->WriteHTML($text);
			$pdf->Output($path,'F'); //$pdf->Output('../files/example.pdf','F');
			
			return true;
		} 
		catch(Exception $e) 
		{
			return false;
		}
	}	
