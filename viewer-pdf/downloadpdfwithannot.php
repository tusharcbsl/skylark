<?php

require_once '../sessionstart.php';
$mm = 0.235185;
$docid = base64_decode(urldecode($_POST['docid']));
$docName = $_POST['docname'];
$slid = $_POST['slid'];
$docPath = $_POST['docPath'];
$username = $_SESSION['adminMail'];
$docExtn = $_POST['docExtn'];
$withoutExt = preg_replace('/\\.[^.\\s]{3,4}$/', '', $docName);
$exportPath = $withoutExt . '.' . $docExtn;

$username = substr($username, 0, strrpos($username, '@'));
require '../application/config/database.php';

require '../application/ajax/fpdf/fpdf.php';
require '../application/ajax/FPDI/fpdi.php';
//error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);

class PDF extends FPDI {

    var $angle = 0;

    function Rotate($angle, $x = -1, $y = -1) {
        if ($x == -1)
            $x = $this->x;
        if ($y == -1)
            $y = $this->y;
        if ($this->angle != 0)
            $this->_out('Q');
        $this->angle = $angle;
        if ($angle != 0) {
            $angle *= M_PI / 180;
            $c = cos($angle);
            $s = sin($angle);
            $cx = $x * $this->k;
            $cy = ($this->h - $y) * $this->k;

            $this->_out(sprintf('q %.5f %.5f %.5f %.5f %.2f %.2f cm 1 0 0 1 %.2f %.2f cm', $c, $s, -$s, $c, $cx, $cy, -$cx, -$cy));
        }
    }

    function _endpage() {
        if ($this->angle != 0) {
            $this->angle = 0;
            $this->_out('Q');
        }
        parent::_endpage();
    }

    var $extgstates = array();

    // alpha: real value from 0 (transparent) to 1 (opaque)
    // bm:    blend mode, one of the following:
    //          Normal, Multiply, Screen, Overlay, Darken, Lighten, ColorDodge, ColorBurn,
    //          HardLight, SoftLight, Difference, Exclusion, Hue, Saturation, Color, Luminosity
    function SetAlpha($alpha, $bm = 'Normal') {
        // set alpha for stroking (CA) and non-stroking (ca) operations
        $gs = $this->AddExtGState(array('ca' => $alpha, 'CA' => $alpha, 'BM' => '/' . $bm));
        $this->SetExtGState($gs);
    }

    function AddExtGState($parms) {
        $n = count($this->extgstates) + 1;
        $this->extgstates[$n]['parms'] = $parms;
        return $n;
    }

    function SetExtGState($gs) {
        $this->_out(sprintf('/GS%d gs', $gs));
    }

    function _enddoc() {
        if (!empty($this->extgstates) && $this->PDFVersion < '1.4')
            $this->PDFVersion = '1.4';
        parent::_enddoc();
    }

    function _putextgstates() {
        for ($i = 1; $i <= count($this->extgstates); $i++) {
            $this->_newobj();
            $this->extgstates[$i]['n'] = $this->n;
            $this->_out('<</Type /ExtGState');
            $parms = $this->extgstates[$i]['parms'];
            $this->_out(sprintf('/ca %.3F', $parms['ca']));
            $this->_out(sprintf('/CA %.3F', $parms['CA']));
            $this->_out('/BM ' . $parms['BM']);
            $this->_out('>>');
            $this->_out('endobj');
        }
    }

    function _putresourcedict() {
        parent::_putresourcedict();
        $this->_out('/ExtGState <<');
        foreach ($this->extgstates as $k => $extgstate)
            $this->_out('/GS' . $k . ' ' . $extgstate['n'] . ' 0 R');
        $this->_out('>>');
    }

    function _putresources() {
        $this->_putextgstates();
        parent::_putresources();
    }

}

function generatePDF($source, $output, $text) {

    $pdf = new PDF('P', 'mm'); // Array sets the X, Y dimensions in mm

    $pagecount = $pdf->setSourceFile($source);
    for ($pageNo = 1; $pageNo <= $pagecount; $pageNo++) {

        $tppl = $pdf->importPage($pageNo);
        $size = $pdf->getTemplateSize($tppl);
        $w = $size['w'];
        $h = $size['h'];
        $pdf->AddPage('P', array($w, $h));
        $pdf->useTemplate($tppl, 0, 0, 0, 0);
//$pdf->Image($image,10,10,200,260); // X start, Y start, X width, Y width in mm 
        $pdf->SetFont('Helvetica', '', 20); // Font Name, Font Style (eg. 'B' for Bold), Font Size
        $pdf->SetTextColor(0, 0, 0); // RGB 
        $pdf->Rotate(45, 10, 120);
        $pdf->SetAlpha(0.6);
        $pdf->SetXY($w / 2, $h * 3 / 4); // X start, Y start in mm
        $pdf->Write(0, $text);
    }
    $pdf->Output($output, "F");
}

$pdf = generatePDF("../$docPath", "$exportPath", "$username@$host");
if ($pdf) {
    $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`,`doc_id`) values ('$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,'$slid','Document Downloaded','$date',null,'$host','Document $docName printed,downloaded or tried to printed,downloaded by $_SESSION[adminMail]','$docid')") or die('error : ' . mysqli_error($db_con));
} else {
    $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`, `group_id`, `sl_id`, `action_name`, `start_date`, `end_date`, `system_ip`, `remarks`,`doc_id`) values ('$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]',null,'$slid','Document Downloaded','$date',null,'$host','Document $docName printed,downloaded or tried to printed,downloaded by $_SESSION[adminMail]','$docid')") or die('error : ' . mysqli_error($db_con));
}
echo $exportPath;
?>

