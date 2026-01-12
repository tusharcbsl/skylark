<?php
error_reporting(0);
require_once '../../sessionstart.php';
require './../config/database.php'; 
//require_once '../../classes/fileManager.php';
if (!isset($_SESSION['cdes_user_id'])) {
    header("location:../../logout.php");
}

if(!isset($_POST['token'])){
   echo "Unauthrized Access";  
}
        $mm = 0.235185;    //it is value of x in (1.5*x = 0.352778);
       
        $x1 = $_POST['X1'];  
	$y1 = $_POST['Y1'];  
	$x2 = $_POST['X2'];
	$y2 = $_POST['Y2'];
        $type = $_POST['TYPE']; 
        $pageNo = $_POST['pageNo'];
        
        $filePath = $_POST['FILEPATH']; 
    
       $canWidth = $_POST['CANWIDTH']*$mm; 
       $canHeight = $_POST['CANHEIGHT']*$mm; 
       
       $docAsignId = $_POST['DOCASIGNID']; 
       $tktId = $_POST['TKTID']; 
       $user_id = $_SESSION['cdes_user_id']; 
       
       $confirm = $_POST['CONFIRM'];
       
       // check for review
       $chk = $_POST['chk'];
       //set log table
       if($chk=='rw'){
           // when file in review.
           $is_inreview=1; 
           $log_table='tbl_reviews_log';
       } else {
           // when file in review.
           $is_inreview=0; 
          $log_table='tbl_ezeefile_logs_wf'; 
       }        
      
       
       $docid = $_POST['DOCID']; 
       $user= mysqli_query($db_con, "select * from tbl_user_master where user_id='$user_id'");
       $rwUser= mysqli_fetch_assoc($user);
       //image path for stamp
       $aprvdImgPath = "../../assets/images/approved.jpg";
        $rejectImgPath = "../../assets/images/reject.jpg";
        $signatureImgPath = "../../".$rwUser['user_sign'];
     
        $fileName = substr($filePath, strrpos($filePath, "/")+1); 
        $path=substr($filePath,0, strrpos($filePath, "/")); 

        $DOCPATH =$path.'/'.$fileName;
        $image_path = $filePath;
     
     // getDocumentFromFTP($fileserver, $port, $ftpUser,$ftpPwd, $DOCPATH, $image_path);
        
//error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
require 'fpdf/fpdf.php';
require 'FPDI/fpdi.php';


class AlphaPDF extends FPDI
{
    var $extgstates = array();

    // alpha: real value from 0 (transparent) to 1 (opaque)
    // bm:    blend mode, one of the following:
    //          Normal, Multiply, Screen, Overlay, Darken, Lighten, ColorDodge, ColorBurn,
    //          HardLight, SoftLight, Difference, Exclusion, Hue, Saturation, Color, Luminosity
    function SetAlpha($alpha, $bm='Normal')
    {
        // set alpha for stroking (CA) and non-stroking (ca) operations
        $gs = $this->AddExtGState(array('ca'=>$alpha, 'CA'=>$alpha, 'BM'=>'/'.$bm));
        $this->SetExtGState($gs);
    }

    function AddExtGState($parms)
    {
        $n = count($this->extgstates)+1;
        $this->extgstates[$n]['parms'] = $parms;
        return $n;
    }

    function SetExtGState($gs)
    {
        $this->_out(sprintf('/GS%d gs', $gs));
    }

    function _enddoc()
    {
        if(!empty($this->extgstates) && $this->PDFVersion<'1.4')
           $this->PDFVersion='1.4';
        parent::_enddoc();
    }
    function _putextgstates()
    {
        for ($i = 1; $i <= count($this->extgstates); $i++)
        {
            $this->_newobj();
            $this->extgstates[$i]['n'] = $this->n;
            $this->_out('<</Type /ExtGState');
            $parms = $this->extgstates[$i]['parms'];
            $this->_out(sprintf('/ca %.3F', $parms['ca']));
            $this->_out(sprintf('/CA %.3F', $parms['CA']));
            $this->_out('/BM '.$parms['BM']);
            $this->_out('>>');
            $this->_out('endobj');
        }
    }

    function _putresourcedict()
    {
        parent::_putresourcedict();
        $this->_out('/ExtGState <<');
        foreach($this->extgstates as $k=>$extgstate)
            $this->_out('/GS'.$k.' '.$extgstate['n'].' 0 R');
        $this->_out('>>');
    }

    function _putresources()
    {
        $this->_putextgstates();
        parent::_putresources();
    }
    
    //ellipse fun to draw circle
function Ellipse($x, $y, $rx, $ry, $style='D')
{
    if($style=='F')
        $op='f';
    elseif($style=='FD' || $style=='DF')
        $op='B';
    else
        $op='S';
    $lx=4/3*(M_SQRT2-1)*$rx;
    $ly=4/3*(M_SQRT2-1)*$ry;
    $k=$this->k;
    $h=$this->h;
    $this->_out(sprintf('%.2F %.2F m %.2F %.2F %.2F %.2F %.2F %.2F c',
        ($x+$rx)*$k,($h-$y)*$k,
        ($x+$rx)*$k,($h-($y-$ly))*$k,
        ($x+$lx)*$k,($h-($y-$ry))*$k,
        $x*$k,($h-($y-$ry))*$k));
    $this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c',
        ($x-$lx)*$k,($h-($y-$ry))*$k,
        ($x-$rx)*$k,($h-($y-$ly))*$k,
        ($x-$rx)*$k,($h-$y)*$k));
    $this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c',
        ($x-$rx)*$k,($h-($y+$ly))*$k,
        ($x-$lx)*$k,($h-($y+$ry))*$k,
        $x*$k,($h-($y+$ry))*$k));
    $this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c %s',
        ($x+$lx)*$k,($h-($y+$ry))*$k,
        ($x+$rx)*$k,($h-($y+$ly))*$k,
        ($x+$rx)*$k,($h-$y)*$k,
        $op));
}

}
 
function generatePDFHighlight($source, $output,$replacePage,$rx1,$ry1,$rx2,$ry2,$canWidth,$canHeight) {

 //$pdf = new AlphaPDF('Portrait','mm',array(215.9,313));
$pdf = new AlphaPDF('P','mm',array($canWidth,$canHeight));
$pagecount = $pdf->setSourceFile($source);
for( $pageNo=1; $pageNo<=$pagecount; $pageNo++ )
{  
$tppl = $pdf->importPage($pageNo);
//$pdf->AddPage();
$size = $pdf->getTemplateSize($tppl);
$w = $size['w'];
$h = $size['h'];

$pdf->AddPage('P',array($w,$h));

$pdf->useTemplate($tppl,0,0,$w,$h); 


if($replacePage==$pageNo){ 
$pdf->SetAlpha(0.3);
$pdf->SetFillColor(230,230,0);
//$pdf->SetXY($xx, $yy); // X start, Y start in mm
$pdf->Rect($rx1,$ry1,$rx2,$ry2,'F',0);
}         
}
$pdf->Output($output, "F");
}

//rectangle
function generatePDFRect($source,$output,$replacePage,$rx1,$ry1,$rx2,$ry2,$canWidth,$canHeight) {

$pdf = new AlphaPDF('P','mm',array($canWidth,$canHeight));
$pagecount = $pdf->setSourceFile($source);
for( $pageNo=1; $pageNo<=$pagecount; $pageNo++ )
{  
$tppl = $pdf->importPage($pageNo);
//$pdf->AddPage();
$size = $pdf->getTemplateSize($tppl);
$w = $size['w'];
$h = $size['h'];

$pdf->AddPage('P',array($w,$h));

$pdf->useTemplate($tppl,0,0,$w,$h); 
 if($replacePage==$pageNo){ 
$pdf->SetLineWidth(0.5);
$pdf->SetDrawColor(255, 0, 0);
$pdf->Rect($rx1,$ry1,$rx2,$ry2,'D',0);
}
}
$pdf->Output($output, "F");
}

function generatePDFText($source,$output,$replacePage,$rx1,$ry1,$txt,$canWidth,$canHeight) {

$pdf = new AlphaPDF('P','mm',array($canWidth,$canHeight));
$pagecount = $pdf->setSourceFile($source);
for( $pageNo=1; $pageNo<=$pagecount; $pageNo++ )
{  
$tppl = $pdf->importPage($pageNo);
//$pdf->AddPage();
$size = $pdf->getTemplateSize($tppl);
$w = $size['w'];
$h = $size['h'];

$pdf->AddPage('P',array($w,$h));

$pdf->useTemplate($tppl,0,0,$w,$h); 
  
if($replacePage==$pageNo){ 
$pdf->SetFont('Helvetica','',14);
$pdf->SetTextColor(255,0,0); // RGB 
$pdf->SetXY($rx1, $ry1); // X start, Y start in mm
$pdf->Write(0, $txt);
}
}
$pdf->Output($output, "F");
}

//for strike
function generatePDFStrike($source,$output,$replacePage,$rx1,$ry1,$rx2,$ry2,$canWidth,$canHeight) {

$pdf = new AlphaPDF('P','mm',array($canWidth,$canHeight));
$pagecount = $pdf->setSourceFile($source);
for( $pageNo=1; $pageNo<=$pagecount; $pageNo++ )
{  
$tppl = $pdf->importPage($pageNo);
//$pdf->AddPage();
$size = $pdf->getTemplateSize($tppl);
$w = $size['w'];
$h = $size['h'];

$pdf->AddPage('P',array($w,$h));

$pdf->useTemplate($tppl,0,0,$w,$h); 
 if($replacePage==$pageNo){ 
$pdf->SetLineWidth(0.2);
$pdf->SetDrawColor(255, 0, 0);

$pdf->Line($rx1,$ry1,$rx2,$ry2);
}
}
$pdf->Output($output, "F");
}

function generatePDFEraser($source, $output,$replacePage,$rx1,$ry1,$rx2,$ry2,$canWidth,$canHeight) {

 //$pdf = new AlphaPDF('Portrait','mm',array(215.9,313));
$pdf = new AlphaPDF('P','mm',array($canWidth,$canHeight));
$pagecount = $pdf->setSourceFile($source);
for( $pageNo=1; $pageNo<=$pagecount; $pageNo++ )
{  
$tppl = $pdf->importPage($pageNo);
//$pdf->AddPage();
$size = $pdf->getTemplateSize($tppl);
$w = $size['w'];
$h = $size['h'];

$pdf->AddPage('P',array($w,$h));

$pdf->useTemplate($tppl,0,0,$w,$h); 


if($replacePage==$pageNo){ 

$pdf->SetFillColor(255,255,255);
//$pdf->SetXY($xx, $yy); // X start, Y start in mm
$pdf->Rect($rx1,$ry1,$rx2,$ry2,'F',0);
}         
}
$pdf->Output($output, "F");
}

//circle
function generatePDFCircle($source,$output,$replacePage,$rx1,$ry1,$rx2,$ry2,$canWidth,$canHeight) {

$pdf = new AlphaPDF('P','mm',array($canWidth,$canHeight));
$pagecount = $pdf->setSourceFile($source);
for( $pageNo=1; $pageNo<=$pagecount; $pageNo++ )
{  
$tppl = $pdf->importPage($pageNo);
//$pdf->AddPage();
$size = $pdf->getTemplateSize($tppl);
$w = $size['w'];
$h = $size['h'];

$pdf->AddPage('P',array($w,$h));

$pdf->useTemplate($tppl,0,0,$w,$h); 
 if($replacePage==$pageNo){ 
$pdf->SetLineWidth(0.5);
$pdf->SetDrawColor(255, 0, 0);

 //co-ordinate to draw circle/ellipse
// $radiusX = ($rx2 - $rx1) * 0.5;
// $radiusY = ($ry2 - $ry1) * 0.5;
// $centerX = $rx1 + $radiusX;
// $centerY = $ry1 + $radiusY;

//$pdf->Ellipse($centerX,$centerY,$radiusX,$radiusY);
$pdf->Ellipse($rx1,$ry1,$rx2,$ry2);

}
}
$pdf->Output($output, "F");
}

//approved fun
function generatePDFAprved($source,$output,$replacePage,$rx1,$ry1,$rx2,$ry2,$canWidth,$canHeight,$aprvdImgPath) {

$pdf = new AlphaPDF('P','mm',array($canWidth,$canHeight));
$pagecount = $pdf->setSourceFile($source);
for( $pageNo=1; $pageNo<=$pagecount; $pageNo++ )
{  
$tppl = $pdf->importPage($pageNo);
//$pdf->AddPage();
$size = $pdf->getTemplateSize($tppl);
$w = $size['w'];
$h = $size['h'];

$pdf->AddPage('P',array($w,$h));

$pdf->useTemplate($tppl,0,0,$w,$h); 
 if($replacePage==$pageNo){ 
 
$pdf->Image($aprvdImgPath,$rx1,$ry1,$rx2,$ry2); // X start, Y start, X width, Y width in mm
}
}
$pdf->Output($output, "F");
}

//reject fun
function generatePDFRejct($source,$output,$replacePage,$rx1,$ry1,$rx2,$ry2,$canWidth,$canHeight,$rejectImgPath) {

$pdf = new AlphaPDF('P','mm',array($canWidth,$canHeight));
$pagecount = $pdf->setSourceFile($source);
for( $pageNo=1; $pageNo<=$pagecount; $pageNo++ )
{  
$tppl = $pdf->importPage($pageNo);
//$pdf->AddPage();
$size = $pdf->getTemplateSize($tppl);
$w = $size['w'];
$h = $size['h'];

$pdf->AddPage('P',array($w,$h));

$pdf->useTemplate($tppl,0,0,$w,$h); 
 if($replacePage==$pageNo){ 

$pdf->Image($rejectImgPath,$rx1,$ry1,$rx2,$ry2); // X start, Y start, X width, Y width in mm
}
}
$pdf->Output($output, "F");
}

//signature fun

function generatePDFSigntr($source,$output,$replacePage,$rx1,$ry1,$rx2,$ry2,$canWidth,$canHeight,$signatureImgPath) {

$pdf = new AlphaPDF('P','mm',array($canWidth,$canHeight));
$pagecount = $pdf->setSourceFile($source);
for( $pageNo=1; $pageNo<=$pagecount; $pageNo++ )
{  
$tppl = $pdf->importPage($pageNo);
//$pdf->AddPage();
$size = $pdf->getTemplateSize($tppl);
$w = $size['w'];
$h = $size['h'];

$pdf->AddPage('P',array($w,$h));

$pdf->useTemplate($tppl,0,0,$w,$h); 
 if($replacePage==$pageNo){ 

$pdf->Image($signatureImgPath,$rx1,$ry1,$rx2,$ry2); // X start, Y start, X width, Y width in mm
}
}
$pdf->Output($output, "F");
}
    //echo $docAsignId;
   //for versioning
    $getDocId = mysqli_query($db_con, "select * from tbl_doc_assigned_wf where ticket_id = '$tktId'") or die('Error:' . mysqli_error($db_con));
    $rwgetDocId= mysqli_fetch_assoc($getDocId);
    $doc_id = $rwgetDocId['doc_id']; 
    //print_r($rwgetDocId);
    $getDocName = mysqli_query($db_con, "select * from tbl_document_master where doc_id = '$doc_id'") or die('Error:' . mysqli_error($db_con));
    $rwgetDocName = mysqli_fetch_assoc($getDocName);
    $docName = $rwgetDocName['doc_name'];
   // $file_name = $rwgetDocName['old_doc_name'];
   // $file_size = $rwgetDocName['doc_size'];
   // $fileExtn = $rwgetDocName['doc_extn'];
    $doc_path = $rwgetDocName['doc_path'];
    $originalFile = $rwgetDocName['old_doc_name'];
    $docName = explode("_",$docName);

    $updateDocName =$docName[0].'_'.$doc_id .((!empty($docName[1]))?'_'.$docName[1]:'');
    
   $slid = $docName[0];
    
    //echo $fileName;
    $extn = substr($fileName,strrpos($fileName,'.') + 1);
    $fname = substr($fileName, 0, strrpos($fileName, '.'));

    
    if($originalFile != $fileName){
        if(strpos( $fileName, '-' ) !== false){
            
         $fname = substr($fileName, 0, strrpos($fileName, '_')); //from last '_' remove 
         
       }
    }
    
    $chekFileVersion = mysqli_query($db_con, "SELECT * FROM `tbl_document_master` WHERE find_in_set('$updateDocName', doc_name)") or die('Error:' . mysqli_error($db_con));

    $flVersion = mysqli_num_rows($chekFileVersion);
    
    $flVersion = $flVersion + 1;
   
    $file_name = $tktId . '_' .$flVersion . '.' .$extn; 
 
    $fileNamewtVersion = $fname.'_'.$flVersion.'.'.$extn;
    

 $flag = 0;
for($i=0;$i<count($x1);$i++){
	$x=$x1[$i] * $mm;
	$y=$y1[$i] * $mm;
	$w=$x2[$i] * $mm;
	$h=$y2[$i] * $mm;
        
        switch ($type[$i]){
            case "highlight":

              if($confirm==1){
              
               $highlight = generatePDFHighlight("$filePath","$filePath",$pageNo[$i],$x,$y,$w,$h,$canWidth,$canHeight);
              }else{
                  if($flag){
                $highlight = generatePDFHighlight("$path/$fileNamewtVersion","$path/$fileNamewtVersion", $pageNo[$i],$x,$y,$w,$h,$canWidth,$canHeight);
                  }else{
                    
                $highlight = generatePDFHighlight("$filePath","$path/$fileNamewtVersion", $pageNo[$i],$x,$y,$w,$h,$canWidth,$canHeight);
                    $flag = 1;
                
                  }
                
                  }

                break;
                
            case "rectangle":
             if($confirm == 1){
              
               generatePDFRect("$filePath","$filePath",$pageNo[$i],$x,$y,$w,$h,$canWidth,$canHeight);
              }else{
                  
                  if($flag){
                generatePDFRect("$path/$fileNamewtVersion","$path/$fileNamewtVersion", $pageNo[$i],$x,$y,$w,$h,$canWidth,$canHeight);
                  }else{
                      
               generatePDFRect("$filePath","$path/$fileNamewtVersion", $pageNo[$i],$x,$y,$w,$h,$canWidth,$canHeight);
                   $flag = 1;
               
                  }
              }
                break;
                
            case "text":
              $text = $_POST['TEXT']; 
                   if($confirm == 1){
              
               generatePDFText("$filePath","$filePath", $pageNo[$i],$x,$y,$text[$i],$canWidth,$canHeight);
              }else{
                   if($flag){
                       generatePDFText("$path/$fileNamewtVersion","$path/$fileNamewtVersion", $pageNo[$i],$x,$y,$text[$i],$canWidth,$canHeight);
                   }else{
                      
                  generatePDFText("$filePath","$path/$fileNamewtVersion", $pageNo[$i],$x,$y,$text[$i],$canWidth,$canHeight);
                   $flag = 1;
                  
                   }
                   }
                  break;
                  
            case "strikeout":
             if($confirm == 1){
              
               generatePDFStrike("$filePath","$filePath",$pageNo[$i],$x,$y,$w,$h,$canWidth,$canHeight);
              }else{
                  
                  if($flag){
                generatePDFStrike("$path/$fileNamewtVersion","$path/$fileNamewtVersion", $pageNo[$i],$x,$y,$w,$h,$canWidth,$canHeight);
                  }else{
                      
               generatePDFStrike("$filePath","$path/$fileNamewtVersion", $pageNo[$i],$x,$y,$w,$h,$canWidth,$canHeight);
                   $flag = 1;
               
                  }
              }
                break;
                
              case "eraser":

              if($confirm==1){
              
               $highlight = generatePDFEraser("$filePath","$filePath",$pageNo[$i],$x,$y,$w,$h,$canWidth,$canHeight);
              }else{
                  if($flag){
                $highlight = generatePDFEraser("$path/$fileNamewtVersion","$path/$fileNamewtVersion", $pageNo[$i],$x,$y,$w,$h,$canWidth,$canHeight);
                  }else{
                    
                $highlight = generatePDFEraser("$filePath","$path/$fileNamewtVersion", $pageNo[$i],$x,$y,$w,$h,$canWidth,$canHeight);
                    $flag = 1;
                
                  }
                
                  }
                   break;
                   
             case "circle":
             if($confirm == 1){
              
               generatePDFCircle("$filePath","$filePath",$pageNo[$i],$x,$y,$w,$h,$canWidth,$canHeight);
              }else{
                  
                  if($flag){
                generatePDFCircle("$path/$fileNamewtVersion","$path/$fileNamewtVersion", $pageNo[$i],$x,$y,$w,$h,$canWidth,$canHeight);
                  }else{
                      
               generatePDFCircle("$filePath","$path/$fileNamewtVersion", $pageNo[$i],$x,$y,$w,$h,$canWidth,$canHeight);
                   $flag = 1;
               
                  }
              }
                break;
                
             case "approved":
             if($confirm == 1){
              
               generatePDFAprved("$filePath","$filePath",$pageNo[$i],$x,$y,$w,$h,$canWidth,$canHeight,$aprvdImgPath);
              }else{
                  
                  if($flag){
                generatePDFAprved("$path/$fileNamewtVersion","$path/$fileNamewtVersion", $pageNo[$i],$x,$y,$w,$h,$canWidth,$canHeight,$aprvdImgPath);
                  }else{
                      
               generatePDFAprved("$filePath","$path/$fileNamewtVersion", $pageNo[$i],$x,$y,$w,$h,$canWidth,$canHeight,$aprvdImgPath);
                   $flag = 1;
               
                  }
              }
                break;
                
             case "reject":
             if($confirm == 1){
              
               generatePDFRejct("$filePath","$filePath",$pageNo[$i],$x,$y,$w,$h,$canWidth,$canHeight,$rejectImgPath);
              }else{
                  
                  if($flag){
                generatePDFRejct("$path/$fileNamewtVersion","$path/$fileNamewtVersion", $pageNo[$i],$x,$y,$w,$h,$canWidth,$canHeight,$rejectImgPath);
                  }else{
                      
               generatePDFRejct("$filePath","$path/$fileNamewtVersion", $pageNo[$i],$x,$y,$w,$h,$canWidth,$canHeight,$rejectImgPath);
                   $flag = 1;
               
                  }
              }
                break;
                
            case "signature":
             if($confirm == 1){
              
               generatePDFSigntr("$filePath","$filePath",$pageNo[$i],$x,$y,$w,$h,$canWidth,$canHeight,$signatureImgPath);
              }else{
                 
                  if($flag){
                generatePDFSigntr("$path/$fileNamewtVersion","$path/$fileNamewtVersion", $pageNo[$i],$x,$y,$w,$h,$canWidth,$canHeight,$signatureImgPath);
                  }else{
                    
               generatePDFSigntr("$filePath","$path/$fileNamewtVersion", $pageNo[$i],$x,$y,$w,$h,$canWidth,$canHeight,$signatureImgPath);
                   $flag = 1;
               
                  }
              }
                break;
          }
}


            if($confirm == 1){ // '1' means make change in original file.
            $newFilePath = "$filePath";
            $filesize = filesize($newFilePath); 
           
           $numfPage = getNumPagesPdf($newFilePath);
           //$DOCPATH =   str_replace("../../temp/".$_SESSION['cdes_user_id']."", "", $DOCPATH);
           $DOCPATH=$doc_path;
           
           if($chk!='rw'){
			   //uploadDocumentOnFtp($fileserver, $port, $ftpUser,$ftpPwd, $DOCPATH, $image_path, $path);
			   uploadDocumentOnFtp($DOCPATH, $image_path);
           } 
            
       $updateCurentFile = mysqli_query($db_con, "update tbl_document_master set doc_size = '$filesize', noofpages = '$numfPage', ftp_done='1' where doc_id = '$docid'") or die('Error:' . mysqli_error($db_con));
       for($j=0;$j<count($x1);$j++){
	$xi=$x1[$j];
	$yi=$y1[$j];
	$wi=$x2[$j];
	$hi=$y2[$j];
       
        switch ($type[$j]){
            case "highlight":
              $insertCordinate = mysqli_query($db_con, "INSERT INTO tbl_anotation(doc_id,anotation_type,anotation_by,page_no,co_ordinate,date_time,is_inreview) VALUES ('$docid','$type[$j]','$_SESSION[cdes_user_id]','$pageNo[$j]','$xi,$yi,$wi,$hi','$date','$is_inreview')") or die('Error:' . mysqli_error($db_con));
               $insertCrdinateId = mysqli_insert_id($db_con);
             $action="Page No. $pageNo[$j] highlighted. ";
                 break;
            case "rectangle":
                 $insertCordinate = mysqli_query($db_con, "INSERT INTO tbl_anotation(doc_id,anotation_type,anotation_by,page_no,co_ordinate,date_time,is_inreview) VALUES ('$docid','$type[$j]','$_SESSION[cdes_user_id]','$pageNo[$j]','$xi,$yi,$wi,$hi','$date','$is_inreview')") or die('Error:' . mysqli_error($db_con));
                 $insertCrdinateId = mysqli_insert_id($db_con);
                 $action.="Rectangle added on page no. $pageNo[$j]. ";
                 break;
            case "text":
               $insertCordinate = mysqli_query($db_con, "INSERT INTO tbl_anotation(doc_id,anotation_type,anotation_by,page_no,co_ordinate,date_time,is_inreview) VALUES ('$docid','$type[$j]','$_SESSION[cdes_user_id]','$pageNo[$j]','$xi,$yi','$date','$is_inreview')") or die('Error:' . mysqli_error($db_con));
               $insertCrdinateId = mysqli_insert_id($db_con);
               $action.="Text added on page no. $pageNo[$j]. ";
                break;
            case "strikeout":
               $insertCordinate = mysqli_query($db_con, "INSERT INTO tbl_anotation(doc_id,anotation_type,anotation_by,page_no,co_ordinate,date_time,is_inreview) VALUES ('$docid','$type[$j]','$_SESSION[cdes_user_id]','$pageNo[$j]','$xi,$yi,$wi,0','$date','$is_inreview')") or die('Error:' . mysqli_error($db_con));
               $insertCrdinateId = mysqli_insert_id($db_con);
               $action.="Strikeout added on page no. $pageNo[$j]. ";
                break;
             case "circle":
                 $insertCordinate = mysqli_query($db_con, "INSERT INTO tbl_anotation(doc_id,anotation_type,anotation_by,page_no,co_ordinate,date_time,is_inreview) VALUES ('$docid','$type[$j]','$_SESSION[cdes_user_id]','$pageNo[$j]','$xi,$yi,$wi,$hi','$date','$is_inreview')") or die('Error:' . mysqli_error($db_con));
                 $insertCrdinateId = mysqli_insert_id($db_con);
                 $action.="Circle added on page no. $pageNo[$j]. ";
                 break;
              case "approved":
                 $insertCordinate = mysqli_query($db_con, "INSERT INTO tbl_anotation(doc_id,anotation_type,anotation_by,page_no,co_ordinate,date_time,is_inreview) VALUES ('$docid','$type[$j]','$_SESSION[cdes_user_id]','$pageNo[$j]','$xi,$yi,$wi,$hi','$date','$is_inreview')") or die('Error:' . mysqli_error($db_con));
                 $insertCrdinateId = mysqli_insert_id($db_con);
                 $action.="Page no. $pageNo[$j] approved. ";
                 break;
              case "reject":
                 $insertCordinate = mysqli_query($db_con, "INSERT INTO tbl_anotation(doc_id,anotation_type,anotation_by,page_no,co_ordinate,date_time,is_inreview) VALUES ('$docid','$type[$j]','$_SESSION[cdes_user_id]','$pageNo[$j]','$xi,$yi,$wi,$hi','$date','$is_inreview')") or die('Error:' . mysqli_error($db_con));
                 $insertCrdinateId = mysqli_insert_id($db_con);
                 $action.="Page no. $pageNo[$j] rejected. ";
                 break;
              case "signature":
                 $insertCordinate = mysqli_query($db_con, "INSERT INTO tbl_anotation(doc_id,anotation_type,anotation_by,page_no,co_ordinate,date_time,is_inreview) VALUES ('$docid','$type[$j]','$_SESSION[cdes_user_id]','$pageNo[$j]','$xi,$yi,$wi,$hi','$date','$is_inreview')") or die('Error:' . mysqli_error($db_con));
                 $insertCrdinateId = mysqli_insert_id($db_con);
                 $action.="Signature added on page no. $pageNo[$j]. ";
                 break;
                   }
                //set log for    
                   
          }
            
            }else{
                
            $newFilePath = "$path/$fileNamewtVersion";
            
             $filesize = filesize($newFilePath); 
            $numfPage = getNumPagesPdf($newFilePath);
            }
            
         if($confirm == 2){ // '2' means not confirm and make version of file.
             
              $doc_pathArray = explode('/', $doc_path);
             $fpath= $doc_pathArray[0];
             
             $filepath =$path.'/'.$fileNamewtVersion;
             
             //unlink($path.'/CashVoucher1536211584_5.pdf');
             if($chk!='rw'){
                uploadDocumentOnFtp($fpath.'/'.$fileNamewtVersion,$filepath);
             }
             
             
              $cols = '';
                    $columns = mysqli_query($db_con, "SHOW COLUMNS FROM tbl_document_master");
                    while ($rwCols = mysqli_fetch_array($columns)) {
                        if ($rwCols['Field'] != 'doc_id') {
                            if (empty($cols)) {
                                $cols = '`' . $rwCols['Field'] . '`';
                            } else {
                                $cols = $cols . ',`' . $rwCols['Field'] . '`';
                            }
                        }
                    }
                    
                    $createVrsn = mysqli_query($db_con, "INSERT INTO tbl_document_master($cols) select $cols from tbl_document_master where doc_id='$docid'") or die('Error:' . mysqli_error($db_con));
                    $insertDocID = mysqli_insert_id($db_con);
                    
                    
              //$createVrsn = mysqli_query($db_con, "INSERT INTO tbl_document_master(doc_name, old_doc_name, doc_extn, doc_path, doc_size, uploaded_by, noofpages, dateposted) VALUES ('$updateDocName', '$file_name', '$extn', '$fpath/$fileNamewtVersion', '$filesize', '$user_id', '$numfPage', '$date')") or die('Error:' . mysqli_error($db_con));
             // $insertDociD = mysqli_insert_id($db_con);
              
             // $slid = $slid.'_'.$docid;
            $updateNew = mysqli_query($db_con, "update tbl_document_master set doc_name='$updateDocName' where doc_id='$insertDocID'");
            $qry = mysqli_query($db_con, "update tbl_document_master set doc_extn='$extn', old_doc_name='$file_name', doc_path='$fpath/$fileNamewtVersion', uploaded_by='$user_id', doc_size='$filesize', noofpages='1', dateposted='$date' where doc_id='$docid'");

              
       for($j=0;$j<count($x1);$j++){
	$xi=$x1[$j];
	$yi=$y1[$j];
	$wi=$x2[$j];
	$hi=$y2[$j];
     
        switch ($type[$j]){
            case "highlight":
              $insertCordinate = mysqli_query($db_con, "INSERT INTO tbl_anotation(doc_id,anotation_type,anotation_by,page_no,co_ordinate,date_time,is_inreview) VALUES ('$insertDociD','$type[$j]','$_SESSION[cdes_user_id]','$pageNo[$j]','$xi,$yi,$wi,$hi','$date','$is_inreview')") or die('Error:' . mysqli_error($db_con));
               $insertCrdinateId = mysqli_insert_id($db_con);
               $action="Page No. $pageNo[$j] highlighted and saved as new file. ";
                 break;
            case "rectangle":
                 $insertCordinate = mysqli_query($db_con, "INSERT INTO tbl_anotation(doc_id,anotation_type,anotation_by,page_no,co_ordinate,date_time,is_inreview) VALUES ('$insertDociD','$type[$j]','$_SESSION[cdes_user_id]','$pageNo[$j]','$xi,$yi,$wi,$hi','$date','$is_inreview')") or die('Error:' . mysqli_error($db_con));
                 $insertCrdinateId = mysqli_insert_id($db_con);
                 $action.="Rectangle added on page no. $pageNo[$j] and saved as new file. ";
                 break;
            case "text":
               $insertCordinate = mysqli_query($db_con, "INSERT INTO tbl_anotation(doc_id,anotation_type,anotation_by,page_no,co_ordinate,date_time,is_inreview) VALUES ('$insertDociD','$type[$j]','$_SESSION[cdes_user_id]','$pageNo[$j]','$xi,$yi','$date','$is_inreview')") or die('Error:' . mysqli_error($db_con));
               $insertCrdinateId = mysqli_insert_id($db_con);
               $action.="Text added on page no. $pageNo[$j]. and saved as new file. ";
                break;
             case "strikeout":
               $insertCordinate = mysqli_query($db_con, "INSERT INTO tbl_anotation(doc_id,anotation_type,anotation_by,page_no,co_ordinate,date_time,is_inreview) VALUES ('$insertDociD','$type[$j]','$_SESSION[cdes_user_id]','$pageNo[$j]','$xi,$yi,$wi,0','$date','$is_inreview')") or die('Error:' . mysqli_error($db_con));
               $insertCrdinateId = mysqli_insert_id($db_con);
               $action.="Strikeout added on page no. $pageNo[$j] and saved as new file. ";
                break;
             case "circle":
                 $insertCordinate = mysqli_query($db_con, "INSERT INTO tbl_anotation(doc_id,anotation_type,anotation_by,page_no,co_ordinate,date_time,is_inreview) VALUES ('$insertDociD','$type[$j]','$_SESSION[cdes_user_id]','$pageNo[$j]','$xi,$yi,$wi,$hi','$date','$is_inreview')") or die('Error:' . mysqli_error($db_con));
                 $insertCrdinateId = mysqli_insert_id($db_con);
                 $action.="Circle added on page no. $pageNo[$j] and saved as new file. ";
                 break;
              case "approved":
                 $insertCordinate = mysqli_query($db_con, "INSERT INTO tbl_anotation(doc_id,anotation_type,anotation_by,page_no,co_ordinate,date_time,is_inreview) VALUES ('$insertDociD','$type[$j]','$_SESSION[cdes_user_id]','$pageNo[$j]','$xi,$yi,$wi,$hi','$date','$is_inreview')") or die('Error:' . mysqli_error($db_con));
                 $insertCrdinateId = mysqli_insert_id($db_con);
                 $action.="Page no. $pageNo[$j] approved and saved as new file. ";
                 break;
              case "reject":
                 $insertCordinate = mysqli_query($db_con, "INSERT INTO tbl_anotation(doc_id,anotation_type,anotation_by,page_no,co_ordinate,date_time,is_inreview) VALUES ('$insertDociD','$type[$j]','$_SESSION[cdes_user_id]','$pageNo[$j]','$xi,$yi,$wi,$hi','$date','$is_inreview')") or die('Error:' . mysqli_error($db_con));
                 $insertCrdinateId = mysqli_insert_id($db_con);
                 $action.="Page no. $pageNo[$j] rejected. and saved as new file. ";
                 break;
             case "signature":
                 $insertCordinate = mysqli_query($db_con, "INSERT INTO tbl_anotation(doc_id,anotation_type,anotation_by,page_no,co_ordinate,date_time,is_inreview) VALUES ('$insertDociD','$type[$j]','$_SESSION[cdes_user_id]','$pageNo[$j]','$xi,$yi,$wi,$hi','$date','$is_inreview')") or die('Error:' . mysqli_error($db_con));
                 $insertCrdinateId = mysqli_insert_id($db_con);
                 $action.="Signature added on page no. $pageNo[$j] and saved as new file. ";
                 break;
                   }
          }
          
          //get annotation of previous file n add to version file
          
          $checkAnot = mysqli_query($db_con,"select anotation_type, anotation_by, page_no, co_ordinate,date_time from tbl_anotation where doc_id = '$docid' ") or die('Error:' . mysqli_error($db_con));
          if(mysqli_num_rows($checkAnot) >= 1){
              while($rwcheckAnot = mysqli_fetch_assoc($checkAnot)){
            $insertAnotPrevFile = mysqli_query($db_con,"INSERT INTO tbl_anotation(doc_id,anotation_type,anotation_by,page_no,co_ordinate,date_time) VALUES ('$insertDociD','$rwcheckAnot[anotation_type]','$rwcheckAnot[anotation_by]','$rwcheckAnot[page_no]','$rwcheckAnot[co_ordinate]','$rwcheckAnot[date_time]')") or die('Error:' . mysqli_error($db_con));
                  
              }
              
          }
              //for url
              echo 'test^id='.urlencode(base64_encode($_SESSION['cdes_user_id'])).'&id1='.urlencode(base64_encode($insertDociD)).'&pn=1';
         }
  
         
 if($chk=='rw'){
  // Insert annotation activity log 
   $log_sql="insert into ".$log_table." set 
                                          user_id='$_SESSION[cdes_user_id]',
                                          action_name='$action',
                                          start_date='$date',
                                          system_ip='$_SERVER[REMOTE_ADDR]]',
                                          doc_id='$docid'";    
 }else{
   // Insert annotation activity log 
   $log_sql="insert into ".$log_table." set 
                                          user_id='$_SESSION[cdes_user_id]',
                                          user_name='$_SESSION[admin_user_name] $_SESSION[admin_user_last]',
                                          action_name='$action',
                                          start_date='$date',
                                          system_ip='$_SERVER[REMOTE_ADDR]]',
                                          doc_id='$docid'";   
 }
 
 $log_query= mysqli_query($db_con, $log_sql);
         
         

//fun for count no. of pages
function getNumPagesPdf($filepath) {
    $fp = @fopen(preg_replace("/\[(.*?)\]/i", "", $filepath), "r");
    $max = 0;
    if (!$fp) {
        return "Could not open file: $filepath";
    } else {
        while (!@feof($fp)) {
            $line = @fgets($fp);
            if (preg_match('/\/Count [0-9]+/', $line, $matches)) {
                preg_match('/[0-9]+/', $matches[0], $matches2);
                if ($max < $matches2[0]) {
                    $max = trim($matches2[0]);
                    break;
                }
            }
        }
        @fclose($fp);
    }

    return $max;
}

function uploadDocumentOnFtp($destinationPath, $sourcePath)
{
	$fileManager = new fileManager();
	// Connect to file server
	$fileManager->conntFileServer();
	if($fileManager->uploadFile($sourcePath, ROOT_FTP_FOLDER . '/' . $destinationPath)){
		 return true;
	}else{
		 return false;
	}  
}

function getDocumentFromFTP($destinationPath, $sourcePath){

	$fileManager = new fileManager();
	// Connect to file server
	$fileManager->conntFileServer();
	if($fileManager->downloadFile($sourcePath, ROOT_FTP_FOLDER . '/' . $destinationPath)){
		 return true;
	}else{
		 return false;
	}
}
?>