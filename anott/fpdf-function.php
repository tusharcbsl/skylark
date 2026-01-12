<?php
//sk@5918error_reporting(E_ALL && ~E_NOTICE);
use setasign\Fpdi\Fpdi;
use setasign\Fpdi\PdfReader;

require_once('fpdf/fpdf.php');
require_once('fpdf/Fpdi/src/autoload.php');
require_once('fpdf/custom/html2pdf.php');

class PDF extends Fpdi
{

    function Footer()
    {
        // Position at 1.5 cm from bottom
        $this->SetY(2);

        // Position at 1. cm from right
        $this->SetX(-10);

        // Arial italic 10
        $this->SetFont('Arial', 'I', 10);
        // Colors of frame, background and text
        //$this->SetDrawColor(0,80,180);
        $this->SetFillColor(255, 255, 255);
        $this->SetTextColor(128);
        //$total_pages={nb};
        //$w = $this->GetStringWidth('{nb}')+2; 
        $w = $this->GetStringWidth($this->PageNo()) + 4;
        //$this->SetTextColor(128);
        // Page number
        $this->Cell($w, 4, $this->PageNo(), 0, 0, 'C', true);
    }
}


// class defined to align image in center of page.

class imgpdf extends Fpdi
{
    const DPI = 96;
    const MM_IN_INCH = 25.4;
    const A4_HEIGHT = 297;
    const A4_WIDTH = 210;

    public function pixelToMM($val)
    {
        $result = (self::MM_IN_INCH / self::DPI) * $val;
        return $result;
    }

    function resizeImage($img)
    {
        $margin = 10; // Set margin in millimeters.
        list($width, $height) = getimagesize($img);
        ($width >= $height ? $img_type = 'l' : $img_type = 'p');

        //convert image size pixel to MM
        //$width=pixelToMM($width);
        //$height=pixelToMM($height);
        $width = (self::MM_IN_INCH / self::DPI) * $width;
        $height = (self::MM_IN_INCH / self::DPI) * $height;

        $page_width = $this->GetPageWidth();
        $page_height = $this->GetPageHeight();

        if ($width >= $page_width || $height >= $page_height) {
            if ($width >= $page_width && $height >= $page_height) {
                // Width diff percent.
                $wdp = (($width - $page_width) / $width) * 100;
                // Height diff percent.
                $hdp = (($height - $page_height) / $height) * 100;

                if ($wdp > $hdp) {
                    // new width  
                    $image_new_width = $page_width - $margin;
                    // new height 
                    $image_new_height = ($image_new_width / $width) * $height;
                } else {
                    // new Height 
                    $image_new_height = $page_height - $margin;
                    // new Width
                    $image_new_width = ($image_new_height / $height) * $width;
                }
            } else {
                if ($width > $page_width || $width == $page_width) {
                    // new width  
                    $image_new_width = $page_width - $margin;
                    // new height 
                    $image_new_height = ($image_new_width / $width) * $height;
                } else {
                    // new Height 
                    $image_new_height = $page_height - $margin;
                    // new Width
                    $image_new_width = ($image_new_height / $height) * $width;
                }
            }
        } else {
            $image_new_width = $width;
            $image_new_height = $height;
        }

        // After getting image height and width set the co ordinates
        $xpos = ($page_width - $image_new_width) / 2;
        $ypos = ($page_height - $image_new_height) / 2;
        return array("img_width" => $width, "img_height" => $height, "page_width" => $page_width, "page_height" => $page_height, "xpos" => $xpos, "ypos" => $ypos, "width" => $image_new_width, "height" => $image_new_height);
    }
}

//(sk@5918)Some general used fpdf functions.

function totalfpages($fisrst_pdf)
{
    $pdf = new Fpdi();
    $pgcc = $pdf->setSourceFile($fisrst_pdf);
    return $pgcc;
}

function mergePdf($fisrst_pdf, $first_pdf_pnum, $second_pdf, $fpos, $did, $is_version = '')
{
    error_reporting(0);
    
    //ini_set('magic_quotes_runtime', 0);
    global  $chk;
    $pdf = new Fpdi();
    //to get total pages for page number printing.
    $pdf->AliasNbPages();

    //check for postion
    if ($fpos == 'b' and $first_pdf_pnum == 1) {
       
        $pageCount = $pdf->setSourceFile($second_pdf);
        //die("OKKKK123");
        $inPageCount = $pageCount;
        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            $pageId = $pdf->importPage($pageNo, PdfReader\PageBoundaries::MEDIA_BOX);
            $size = $pdf->getTemplateSize($pageId);

            $width = $size['width'];
            $height = $size['height'];

            $pdf->addPage();
            $pdf->useImportedPage($pageId, 0, 0, $width, $height, true);
        }

        $pageCount = $pdf->setSourceFile($fisrst_pdf);

        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {

            $pageId = $pdf->importPage($pageNo, PdfReader\PageBoundaries::MEDIA_BOX);
            $size = $pdf->getTemplateSize($pageId);

            $width = $size['width'];
            $height = $size['height'];

            $pdf->addPage();
            $pdf->useImportedPage($pageId, 0, 0, $width, $height, true);
        }
    } else {

        ($fpos == 'b' ? $first_pdf_pnum = $first_pdf_pnum - 1 : $first_pdf_pnum);
        $pageCount = $pdf->setSourceFile($fisrst_pdf);
        for ($pageNo = 1; $pageNo <= $first_pdf_pnum; $pageNo++) {

            $pageId = $pdf->importPage($pageNo, PdfReader\PageBoundaries::MEDIA_BOX);
            $size = $pdf->getTemplateSize($pageId);

            $width = $size['width'];
            $height = $size['height'];

            $pdf->addPage();
            $pdf->useImportedPage($pageId, 0, 0, $width, $height, true);
        }

        $pageCount = $pdf->setSourceFile($second_pdf);
        $inPageCount = $pageCount;
        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            $pageId = $pdf->importPage($pageNo, PdfReader\PageBoundaries::MEDIA_BOX);
            $size = $pdf->getTemplateSize($pageId);

            $width = $size['width'];
            $height = $size['height'];

            $pdf->addPage();
            $pdf->useImportedPage($pageId, 0, 0, $width, $height, true);
        }

        echo $pageCount = $pdf->setSourceFile($fisrst_pdf);
        if ($pageCount > $first_pdf_pnum) {
            for ($pageNo = $first_pdf_pnum + 1; $pageNo <= $pageCount; $pageNo++) {
                $pageId = $pdf->importPage($pageNo, PdfReader\PageBoundaries::MEDIA_BOX);
                $size = $pdf->getTemplateSize($pageId);

                $width = $size['width'];
                $height = $size['height'];

                $pdf->addPage();
                $pdf->useImportedPage($pageId, 0, 0, $width, $height, true);
            }
        }
    }

    // now remove temporary file
    // unlink($second_pdf);

    //$filepath='../temp/2/DMS/Leave1536130495pdf.pdf';
    if ($is_version == '1') {
        //$pdf->Output(F,'../temp/3/DMS/LW215390845677pdf.pdf');
        $redirect_url = createVersion($pdf, $did);
        $pgc = ($inPageCount > 1 ? $inPageCount . " pages" : $inPageCount . " page");
        //$pgl=($pageCount>0 ? " after page no. ".$first_pdf_pnum : " at the begining of file.");
        $pgl = ($first_pdf_pnum == 1 && $fpos == 'b' ? " at the begining of file." : " after page no. " . $first_pdf_pnum);

        $action_details = 'A Pdf file of ' . $pgc . ' inserted ' . $pgl . ' and saved as new file (Version File)';
    } else {
        $pdf->Output(F, $fisrst_pdf);
		  //update page count.
          updatePdfPageCount($fisrst_pdf, $did);
        if (FTP_ENABLED && $chk != 'rw') {
            chkFtp($fisrst_pdf);
        }

      

        // define action details to generate log
        $pgc = ($inPageCount > 1 ? $inPageCount . " pages" : $inPageCount . " page");
        //$pgl=($pageCount>0 ? " after page no. ".$first_pdf_pnum : " at the begining of file.");
        $pgl = ($first_pdf_pnum == 1 && $fpos == 'b' ? " at the begining of file." : " after page no. " . $first_pdf_pnum);
        $action_details = 'A Pdf file of ' . $pgc . ' inserted ' . $pgl;
        $redirect_url = $_SERVER['REQUEST_URI'];
    }

    fpdfLog($action_details);
    $redirect_url .= '&msg=a';
    echo "<script>taskSuccess('$redirect_url', 'New page added successfully')</script>";
    // header("location:".$redirect_url);

}


function mergeImagePdf($fisrst_pdf, $first_pdf_pnum, $second_pdf, $fpos, $did, $is_version = '')
{
    global  $chk;
    //$pdf = new PDF();
    $pdf = new imgpdf();

    //to get total pages for page number printing.
    $pdf->AliasNbPages();

    //get required parameter to center align the image.
    $img_para = $pdf->resizeImage($second_pdf);
    //print_r($img_para);
    //die;
    //check for postion
    if ($fpos == 'b' and $first_pdf_pnum == 1) {
        // Add image
        $pdf->AddPage();
        $pdf->SetFont("Arial", "", 12);
        //$pdf->Cell( 0, 0, $pdf->Image($second_pdf, $width/2, $pdf->GetY(), 33.78), 0, 1, 'C', false );
        //$pdf->centreImage($second_pdf);



        $pdf->Cell(0, 0, $pdf->Image($second_pdf, $img_para['xpos'], $img_para['ypos'], $img_para['width'], $img_para['height']), 0, 1, 'C', false);

        $pageCount = $pdf->setSourceFile($fisrst_pdf);
        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {

            $pageId = $pdf->importPage($pageNo, PdfReader\PageBoundaries::MEDIA_BOX);
            $size = $pdf->getTemplateSize($pageId);

            $width = $size['width'];
            $height = $size['height'];

            $pdf->addPage();
            $pdf->useImportedPage($pageId, 0, 0, $width, $height, true);
        }
    } else {
        ($fpos == 'b' ? $first_pdf_pnum = $first_pdf_pnum - 1 : $first_pdf_pnum);
        $pageCount = $pdf->setSourceFile($fisrst_pdf);
        for ($pageNo = 1; $pageNo <= $first_pdf_pnum; $pageNo++) {

            $pageId = $pdf->importPage($pageNo, PdfReader\PageBoundaries::MEDIA_BOX);
            $size = $pdf->getTemplateSize($pageId);

            $width = $size['width'];
            $height = $size['height'];

            $pdf->addPage();
            $pdf->useImportedPage($pageId, 0, 0, $width, $height, true);
        }

        // Add image
        $pdf->AddPage();
        $pdf->SetFont("Arial", "", 12);

        //$pdf->Cell( 0, 0, $pdf->Image($second_pdf, $width/2, $pdf->GetY(), 33.78), 0, 1, 'R', false );
        $pdf->Cell(0, 0, $pdf->Image($second_pdf, $img_para['xpos'], $img_para['ypos'], $img_para['width'], $img_para['height']), 0, 1, 'C', false);


        $pageCount = $pdf->setSourceFile($fisrst_pdf);
        if ($pageCount > $first_pdf_pnum) {
            for ($pageNo = $first_pdf_pnum + 1; $pageNo <= $pageCount; $pageNo++) {
                $pageId = $pdf->importPage($pageNo, PdfReader\PageBoundaries::MEDIA_BOX);
                $size = $pdf->getTemplateSize($pageId);

                $width = $size['width'];
                $height = $size['height'];

                $pdf->addPage();
                $pdf->useImportedPage($pageId, 0, 0, $width, $height, true);
            }
        }
    }
    // now remove temporary file
    unlink($second_pdf);

    if ($is_version == '1') {
        //$pdf->Output(F,'../temp/3/DMS/LW215390845677pdf.pdf');

        $redirect_url = createVersion($pdf, $did);
        //$pgl=($pageCount>0 ? " after page no. ".$first_pdf_pnum : " at the begining of file.");
        $pgl = ($first_pdf_pnum == 1 && $fpos == 'b' ? " at the begining of file." : " after page no. " . $first_pdf_pnum);

        $action_details = 'An Image inserted ' . $pgl . ' and saved as new file (Version File)';
    } else {
        $pdf->Output(F, $fisrst_pdf);
        if (FTP_ENABLED && $chk != 'rw') {
            chkFtp($fisrst_pdf);
        }
        //update page count.
        updatePdfPageCount($fisrst_pdf, $did);

        // define action details to generate log

        $redirect_url = $_SERVER['REQUEST_URI'];
        //header("location:".$_SERVER['REQUEST_URI']);
        //pgl=($pageCount>0 ? " after page no. ".$first_pdf_pnum : " at the begining of file.");
        $pgl = ($first_pdf_pnum == 1 && $fpos == 'b' ? " at the begining of file." : " after page no. " . $first_pdf_pnum);
        $action_details = 'An Image inserted ' . $pgl;
    }


    fpdfLog($action_details);
    $redirect_url .= '&msg=a';

    echo "<script>taskSuccess('$redirect_url', 'New page added successfully')</script>";
    //header("location:".$redirect_url);   

}


function deletePdfOld($source_pdf, $pnum_to_delete, $did, $is_version = '')
{
    global  $chk;
    //global  $pdf;
    $pdf = new Fpdi();
    // $newpdf = new Fpdi();
    //to get total pages for page number printing.
    $pdf->AliasNbPages();
    // $newpdf->AliasNbPages();
    // $newpdf->setSourceFile($source_pdf);
    $pageCount = $pdf->setSourceFile($source_pdf);
    for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {

        if ($pageNo == $pnum_to_delete) {

            continue;

            // $pageId = $newpdf->importPage($pageNo, PdfReader\PageBoundaries::MEDIA_BOX);

            // $size = $newpdf->getTemplateSize($pageId);

            // $width=$size['width'];

            // $height=$size['height'];

            // $newpdf->addPage();

            // $newpdf->useImportedPage($pageId, 0, 0,$width,$height,true);  

            // $new_filename = '../extract-here/123mani/manish_'.$pageNo.".pdf";



        } else {

            $pageId = $pdf->importPage($pageNo, PdfReader\PageBoundaries::MEDIA_BOX);
            $size = $pdf->getTemplateSize($pageId);

            $width = $size['width'];
            $height = $size['height'];

            $pdf->addPage();
            $pdf->useImportedPage($pageId, 0, 0, $width, $height, true);
        }
    }

    //$newpdf->Output($new_filename, "F");

    if ($is_version == '1') {
        //$pdf->Output(F,'../temp/3/DMS/LW215390845677pdf.pdf');
        $redirect_url = createVersion($pdf, $did);
        $action_details = 'Page no. ' . $pnum_to_delete . ' deleted and saved as new file (Version File)';
    } else {
        $pdf->Output(F, $source_pdf);
        if (FTP_ENABLED && $chk != 'rw') {
            chkFtp($source_pdf);
        }
        //update page count.
        updatePdfPageCount($source_pdf, $did);

        // define action details to generate log
        $action_details = 'Page no. ' . $pnum_to_delete . ' deleted';


        $redirect_url = $_SERVER['REQUEST_URI'];
        //header("location:".$_SERVER['REQUEST_URI']);
    }
    fpdfLog($action_details);

    $redirect_url .= '&msg=d';


    // echo $redirect_url;
    //  die();
    header("location:" . $redirect_url);
}


function deletePdf($source_pdf, $pnum_to_delete, $did, $is_version = '')
{
    error_reporting(0);
    global $chk;
    //global  $pdf;
    $pdf = new Fpdi();
    //to get total pages for page number printing.
    $pdf->AliasNbPages();
    $pageCount = $pdf->setSourceFile($source_pdf);

    //deleted page
    $pnumd = $pnum_to_delete;
    $pgedelete = $pdf->importPage($pnumd, PdfReader\PageBoundaries::MEDIA_BOX);
    $size = $pdf->getTemplateSize($pgedelete);

    $width = $size['width'];
    $height = $size['height'];

    $pdf->addPage();
    $pdf->useImportedPage($pgedelete, 0, 0, $width, $height, true);
	 if ($is_version == '1') {
    $deletedDocId = createVersionofDeletedPdf($pdf, $pnumd, $did);
	 }
    $pdf = new Fpdi();
    $pageCount = $pdf->setSourceFile($source_pdf);

    for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {

        if ($pageNo == $pnum_to_delete) {
            continue;
        }
        $pageId = $pdf->importPage($pageNo, PdfReader\PageBoundaries::MEDIA_BOX);

        $size = $pdf->getTemplateSize($pageId);

        $width = $size['width'];
        $height = $size['height'];

        $pdf->addPage();
        $pdf->useImportedPage($pageId, 0, 0, $width, $height, true);
    }
 
    //end delete page recyle
    if ($is_version == '1') {
        //$pdf->Output(F,'../temp/3/DMS/LW215390845677pdf.pdf');
        $redirect_url = createVersion($pdf, $did, $deletedDocId);
        $action_details = 'Page no. ' . $pnum_to_delete . ' deleted and saved as new file (Version File)';
    } else {
        $pdf->Output(F, $source_pdf);
		 //update page count.
        updatePdfPageCount($source_pdf, $did);
        if (FTP_ENABLED && $chk != 'rw') {
         
            chkFtp($source_pdf);
        }
       

        // define action details to generate log
        $action_details = 'Page no. ' . $pnum_to_delete . ' deleted';


        $redirect_url = $_SERVER['REQUEST_URI'];
        //header("location:".$_SERVER['REQUEST_URI']);
    }
	
    fpdfLog($action_details);

    $redirect_url .= '&msg=d';


    echo "<script>taskSuccess('$redirect_url', 'Page deleted successfully')</script>";
    //header("location:" . $redirect_url);
}

function chkFtp($file_upload_to_ftp, $sfilename = '')
{
    global $fileManager, $server_path;
    //error_reporting(E_ALL);
    if (!empty($sfilename)) {
        $new_server_path = substr($server_path, 0, strrpos($server_path, "/") + 1);
        $new_server_path = $new_server_path . $sfilename;
    } elseif ($chk == 'rw') {
        $new_server_path = substr($server_path, 0, strrpos($server_path, "/") + 1);
    } else {
        $new_server_path = $server_path;
    }

    $fileManager->conntFileServer();
    if ($fileManager->uploadFile($file_upload_to_ftp, $new_server_path, false)) {
        //unlink($file_upload_to_ftp);
        return true;
    } else {
        echo "failed to move in FTP";
        return false;
    }

    /* if(FTP_ENABLED){ 
		$uploadfile = $ftp->put($new_server_path,$file_upload_to_ftp);
		 $arr = $ftp->getLogData();
		 if ($uploadfile){
		 $uploadInToFTP=true;
		 //unlink($image_path);
		 }
		 else{
		$uploadInToFTP=false;
		echo '<h2>Error:</h2>' . implode('<br />', $arr['error']);
		}
	  }
	  else{
		$uploadInToFTP=true;
	  }  */
    //return $uploadInToFTP;
}

//sk@6918 : save fpdf actions log 

function fpdfLog($action = '')
{
    global $db_con, $id1, $host, $date, $chk;
    if (isset($chk) && $chk == 'rw') {
        $log_table = 'tbl_reviews_log';
        $sql = "insert into " . $log_table . " set 
                                          user_id='$_SESSION[cdes_user_id]',
                                          action_name='$action',
                                          start_date='$date',
                                          system_ip='$host',
                                          doc_id='$id1'
                                          ";
    } else if ($chk == "st") {
        mysqli_set_charset($db_con, "utf8");
        $log_table = 'tbl_ezeefile_logs';
        $sql = "insert into " . $log_table . " set 
                                          user_id='$_SESSION[cdes_user_id]',
                                          user_name='$_SESSION[admin_user_name] $_SESSION[admin_user_last]',
                                          action_name='Pdf edited',
                                          start_date='$date',
                                          system_ip='$host',
                                          doc_id='$id1',
                                          remarks='$action'
                                          ";
    } else {

        mysqli_set_charset($db_con, "utf8");
        $log_table = 'tbl_ezeefile_logs_wf';
        $sql = "insert into " . $log_table . " set 
                                          user_id='$_SESSION[cdes_user_id]',
                                          user_name='$_SESSION[admin_user_name] $_SESSION[admin_user_last]',
                                          action_name='Pdf edited',
                                          start_date='$date',
                                          system_ip='$host',
                                          doc_id='$id1',
                                          remarks='$action'
                                          ";
    }

    $query = mysqli_query($db_con, $sql) or die('log generation failed:' . mysqli_error($db_con));

    if ($query) {

        return true;
    } else {
        return false;
    }
}

//@101018 Update pages count and document size in KB in document master
function updatePdfPageCount($file, $did)
{
    global $db_con;
	
    if (file_exists($file)) {
        //total pages
        $total_pages = totalfpages($file);
        //document size
        $doc_size = filesize($file);
    } else {
        //total pages
        $total_pages = 0;
        //document size
        $doc_size = 0;
    }
	
    $query = mysqli_query($db_con, "update tbl_document_master set noofpages='$total_pages',doc_size='$doc_size' where doc_id='$did'");
}

//@101018 Update pages count and document size in KB in document master
function createVersion($pdf, $did, $deletedDocId = "")
{
    global $db_con, $filePath, $tktId, $date, $user_id, $folderName;

    ///////////////
    $doc_id = $did;

    $filePath = "../extract-here/" . $filePath;
    $fileName = substr($filePath, strrpos($filePath, "/") + 1);
    $path = substr($filePath, 0, strrpos($filePath, "/"));

    $DOCPATH = $path . '/' . $fileName;
    $image_path = $filePath;


    $getDocName = mysqli_query($db_con, "select * from tbl_document_master where doc_id = '$doc_id'") or die('Error:' . mysqli_error($db_con));
    $rwgetDocName = mysqli_fetch_assoc($getDocName);
    $docName = $rwgetDocName['doc_name'];
    // $file_name = $rwgetDocName['old_doc_name'];
    // $file_size = $rwgetDocName['doc_size'];
    // $fileExtn = $rwgetDocName['doc_extn'];
    // $doc_path = $rwgetDocName['doc_path'];
    $originalFile = $rwgetDocName['old_doc_name'];
    $docName = explode("_", $docName);

    $updateDocName = $docName[0] . '_' . $doc_id . ((!empty($docName[1])) ? '_' . $docName[1] : '');

    $extn = substr($fileName, strrpos($fileName, '.') + 1);
    $fname = substr($fileName, 0, strrpos($fileName, '.'));

    if ($originalFile != $fileName) {
        if (strpos($fileName, '-') !== false) {
            $fname = substr($fileName, 0, strrpos($fileName, '_')); //from last '_' remove 
        }
    }

    $chekFileVersion = mysqli_query($db_con, "SELECT * FROM `tbl_document_master` WHERE find_in_set('$updateDocName', doc_name)") or die('Error:' . mysqli_error($db_con));

    //$chekFileVersion = mysqli_query($db_con, "SELECT * FROM `tbl_document_master` WHERE doc_name='$updateDocName'") or die('Error:' . mysqli_error($db_con));
    if (isset($tktId) && !empty($tktId)) {
        $tktId = $tktId;
    } else {
        $tktId = time();
    }
    $flVersion = mysqli_num_rows($chekFileVersion);
    $flVersion = $flVersion + 1;
    $file_name = $tktId . '_' . $flVersion . '.' . $extn;

    $fileNamewtVersion = $fname . '_' . $flVersion . '.' . $extn;
    $fpath = str_replace("../extract-here/", "", $path);
    if (FTP_ENABLED) {
        $nv_filename = str_replace("_", "", $file_name);
        $nv_filename = str_replace(".", "", $nv_filename) . '.' . $extn;

        $nv_file = $folderName . '/' . $nv_filename;
    } else {
        //$fpath=$path;
        $nv_file = '../extract-here/' . $fpath . '/' . $fileNamewtVersion;
    }

    // for ftp server
    //$s=$folderName
    //echo $image_path;
    //echo $fileName;
    //echo  $nv_file;
    //die;

    $pdf->Output(F, $nv_file);



    if (FTP_ENABLED) {

        chkFtp($nv_file, $fileNamewtVersion);
    }
    $numfPage = totalfpages($nv_file);
    $filesize = filesize($nv_file);


    $createVrsn = mysqli_query($db_con, "INSERT INTO tbl_document_master(doc_name, old_doc_name, doc_extn, doc_path, doc_size, uploaded_by, noofpages, dateposted, ftp_done) VALUES ('$updateDocName', '$file_name', '$extn', '$fpath/$fileNamewtVersion', '$filesize', '$user_id', '$numfPage', '$date', '1')") or die('Error:' . mysqli_error($db_con));

    $insertDociD = mysqli_insert_id($db_con);

    if ($insertDociD) {
        $deletedfile = mysqli_query($db_con, "SELECT doc_name FROM `tbl_document_master` WHERE doc_id='$deletedDocId'") or die('Error:' . mysqli_error($db_con));
        $drow = mysqli_fetch_assoc($deletedfile);
        $docnameArray = explode('_', $drow['doc_name']);
        $pageno = end($docnameArray);
        $updatedocname = $docName[0] . '_' . $insertDociD . '_' . $pageno;

        mysqli_query($db_con, "UPDATE `tbl_document_master` set doc_name='$updatedocname' WHERE doc_id='$deletedDocId'") or die('Error:' . mysqli_error($db_con));
    }

    $newdocname = base64_encode($insertDociD);
    //create thumbnail

    //changePdfToImag($nv_file,$newdocname);


    if ($insertDociD) {
        // encrypt doc id and prepare format for query string
        $encDocId = urlencode(base64_encode($insertDociD));

        $id1 = 'id1=' . $encDocId;

        $QUERY_STRING = str_replace("%3D", "=", $_SERVER['QUERY_STRING']);

        //replace doc Id in Url query string.
        $query_string = str_replace('id1=' . $_GET['id1'], $id1, $QUERY_STRING);
        $redirect_url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) . '?' . $query_string;
        //echo $redirect_url;
        //die;
        //echo '<script>taskSuccess('.$redirect_url.',"Action Performed successfully !");</script>';
        //header("location:".$redirect_url);
    }


    return $redirect_url;
    //////////////
}


function uploadDocumentOnFtp($fileserver, $port, $ftpUser, $ftpPwd, $DOCPATH, $image_path)
{
    if (FTP_ENABLED) {
        require_once '../classes/ftp.php';

        $ftp = new ftp();
        $ftp->conn("$fileserver", "$port", "$ftpUser", "$ftpPwd");

        //echo $DOCPATH;
        //die;


        $ftp->put(ROOT_FTP_FOLDER . '/' . $DOCPATH, $image_path);
        $arr = $ftp->getLogData();

        //echo $DOCPATH;
        if ($arr['error'] != "") {

            echo '<h2>Error:</h2>' . implode('<br />', $arr['error']);
        }
    }
}

//@121018 - check if given file is a version file.
function isVersionFile($doc_id)
{
}
//@161018 - prepare pdf file for print.
function printPdf($path, $content)
{
    $pdf = new PDF_HTML();
    if (!empty($content)) {
        $pdf->SetFont('Arial', '', 12);
        $pdf->AddPage();
        $pdf->WriteHTML($content);
    }
    $pageCount = $pdf->setSourceFile($path);
    for ($i = 1; $i <= $pageCount; $i++) {
        $pageId = $pdf->importPage($i, PdfReader\PageBoundaries::MEDIA_BOX);
        $size = $pdf->getTemplateSize($pageId);
        $width = $size['width'];
        $height = $size['height'];
        $pdf->addPage();
        $pdf->useImportedPage($pageId, 0, 0, $width, $height, true);
    }
    $pdf->Output();
    exit;
}

function setPageNo($file, $did)
{
    $pdf = new PDF();
    $pdf->AliasNbPages();
    $pageCount = $pdf->setSourceFile($file);
    for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
        $pageId = $pdf->importPage($pageNo, PdfReader\PageBoundaries::MEDIA_BOX);
        $size = $pdf->getTemplateSize($pageId);

        $width = $size['width'];
        $height = $size['height'];

        $pdf->addPage();
        $pdf->useImportedPage($pageId, 0, 0, $width, $height, true);
    }
    $pdf->Output(F, $file);
    if (FTP_ENABLED) {
        chkFtp($file);
    }
    updatePdfPageCount($file, $did);
}

/*sk@(311018):clean folder.
 * Clean all files(not subfolder) from the given folder/directory;
 * Success: Return 1 for each deleted file on success;
 * Error : Return file name in case of unable to delete and provide corresponding error for other cases.
 */
function cleanFolder($folder)
{
    $folder = rtrim($folder, '/') . '/';
    if (file_exists($folder)) :
        $files = glob($folder . '*');
        if ($files) :
            foreach ($files as $file) :
                $status = unlink($file) ?: $file . '\n'; //delete file.
                $status .= $status;
            endforeach;
        else :
            $status = "Error: No file found in destination folder.";
        endif;
    else :
        $status = "Error: Directory not found.";
    endif;
    return $status;
}

//======================================================
// Make Review File copy for Review annotation
//======================================================
// Make copy of review file.
function reviewFileCopy($localPath, $doc_id, $lang)
{
    $ext = pathinfo($localPath, PATHINFO_EXTENSION);
    $path = substr($localPath, 0, strrpos($localPath, "/"));
    $path = $path . '/reviewPdfCopy';
    if (!dir($path)) {
        mkdir($path, 0777, TRUE);
    }
    $path = $path . '/' . $doc_id;
    if (!dir($path)) {
        mkdir($path, 0777, TRUE);
    }
    //final path with filename to copy for review
    $fpath = $path . '/' . md5($doc_id) . '.' . $ext;
    if (!file_exists($fpath)) {
        if (!copy($localPath, $fpath)) {
            $status = 'error';
            $error = $lang['unable_copy_revw_file'];
            echo $error;
            die();
        }
    }
    return $fpath;
}

function changePdfToImag($uploadedfilename, $newdocname)
{
    $pathWithoutExt = explode('.', $uploadedfilename);
    $newimage = $pathWithoutExt[0] . '.jpeg';
    //shell_exec('"C:\gs9.25\bin\gswin64c.exe" -dNOPAUSE -sDEVICE=jpeg -r144 -sOutputFile="'.$newimage.'" "'.$uploadedfilename.'"');
    shell_exec('gs -dNOPAUSE -sDEVICE=jpeg -r144 -sOutputFile="' . $newimage . '"  "' . $uploadedfilename . '"');
    $newimage1 = createPDFThumbnail($newimage, $newdocname);
    unlink($newimage);
}


function createPDFThumbnail($filenamewithPath, $newdocname)
{
    if (CREATE_THUMBNAIL) {
        $thumb = realpath('../');
        $fname = realpath($filenamewithPath);

        $os = checkOperatingSystem();

        if ($os == 'win') {
            // for windows server
            $newfilename = $thumb . '\\thumbnail\\' . $newdocname . '.jpg';
        } else {
            // for linux server
            $newfilename = $thumb . '/thumbnail/' . $newdocname . '.jpg';
        }

        $im = new imagick($fname);

        $imageprops = $im->getImageGeometry();
        $width = $imageprops['width'];
        $height = $imageprops['height'];
        if ($width > $height) {
            $newHeight = 100;
            $newWidth = (100 / $height) * $width;
        } else {
            $newWidth = 100;
            $newHeight = (100 / $width) * $height;
        }
        $im->resizeImage($newWidth, $newHeight, imagick::FILTER_LANCZOS, 0.9, true);
        $im->cropImage($newWidth, $newHeight, 0, 0);
        $im->writeImage($newfilename);
    }
}

function createVersionofDeletedPdf($pdf, $pnumd, $did)
{
    global $db_con, $filePath, $tktId, $date, $user_id, $folderName;

    ///////////////
    $doc_id = $did;

    $filePath = "../extract-here/" . $filePath;
    $fileName = substr($filePath, strrpos($filePath, "/") + 1);
    $path = substr($filePath, 0, strrpos($filePath, "/"));

    $DOCPATH = $path . '/' . $fileName;
    $image_path = $filePath;


    $getDocName = mysqli_query($db_con, "select * from tbl_document_master where doc_id = '$doc_id'") or die('Error:' . mysqli_error($db_con));
    $rwgetDocName = mysqli_fetch_assoc($getDocName);
    $docName = $rwgetDocName['doc_name'];
    $delfile = 'Deleted_Page_' . time() . '_' . $rwgetDocName['old_doc_name'];
    // $file_size = $rwgetDocName['doc_size'];
    // $fileExtn = $rwgetDocName['doc_extn'];
    // $doc_path = $rwgetDocName['doc_path'];
    $originalFile = $rwgetDocName['old_doc_name'];
    $docName = explode("_", $docName);

    $updateDocName = $docName[0] . '_' . $doc_id . '_' . $pnumd . ((!empty($docName[1])) ? '_' . $docName[1] : '');

    $extn = substr($fileName, strrpos($fileName, '.') + 1);
    $extn = strtolower($extn);
    $fname = substr($fileName, 0, strrpos($fileName, '.'));

    if ($originalFile != $fileName) {
        if (strpos($fileName, '-') !== false) {
            $fname = substr($fileName, 0, strrpos($fileName, '_')); //from last '_' remove 
        }
    }

    /*  $chekFileVersion = mysqli_query($db_con, "SELECT * FROM `tbl_document_master` WHERE find_in_set('$updateDocName', doc_name)") or die('Error:' . mysqli_error($db_con));

    $flVersion = mysqli_num_rows($chekFileVersion); */

    $fileNamewtVersion = $fname . '_' . time() . '.' . $extn;
    $fpath = str_replace("../extract-here/", "", $path);
    if (FTP_ENABLED) {
        //$nv_filename = str_replace("_", "", $fileNamewtVersion);
        $nv_filename = $fileNamewtVersion;
        $nv_filename = str_replace(".pdf", "", $nv_filename) . '.' . $extn;

        $nv_file = $folderName . '/' . $nv_filename;
    } else {
        //$fpath=$path;
        $nv_file = '../extract-here/' . $fpath . '/' . $fileNamewtVersion;
    }

    $updateDocName  = $docName[0] . '_' . $doc_id . '_' . $pnumd;
    $chekFileVersion = mysqli_query($db_con, "SELECT * FROM `tbl_document_master` WHERE find_in_set('$updateDocName', doc_name)") or die('Error:' . mysqli_error($db_con));


    $flVersion = mysqli_num_rows($chekFileVersion);

    if ($flVersion > 0) {

        $updateDocName = getActualPageNo($db_con, $flVersion, $docName[0],  $doc_id);
    }

    // for ftp server
    //    echo $s=$folderName;
    //    echo $image_path;
    //    echo $fileName;
    //    echo  $nv_file;
    //    die;

    $pdf->Output(F, $nv_file);

    if (FTP_ENABLED) {
		
        chkFtp($nv_file, $fileNamewtVersion);
		
    }
	
    $numfPage = totalfpages($nv_file);
	
    $filesize = round(filesize($nv_file) / 1024);
    $createVrsn = mysqli_query($db_con, "INSERT INTO tbl_document_master(doc_name, old_doc_name, doc_extn, doc_path, doc_size, uploaded_by, noofpages, dateposted,flag_multidelete, ftp_done) VALUES ('$updateDocName', '$delfile', '$extn', '$fpath/$fileNamewtVersion', '$filesize', '$user_id', '$numfPage', '$date', '5', '1')") or die('Error:' . mysqli_error($db_con));

    $insertDociD = mysqli_insert_id($db_con);
    if ($insertDociD) {
        $log = mysqli_query($db_con, "insert into tbl_ezeefile_logs(`user_id`, `user_name`, `sl_id`,`doc_id`, `action_name`, `start_date`, `system_ip`, `remarks`) values ('$_SESSION[cdes_user_id]', '$_SESSION[admin_user_name] $_SESSION[admin_user_last]', '$docName[0]', '$insertDociD', 'Pdf page recycle','$date','$host','Page of $originalFile Deleted')") or die('error : ' . mysqli_error($db_con));
    }
    //
    //    if ($insertDociD) {
    //        // encrypt doc id and prepare format for query string
    //        $encDocId = urlencode(base64_encode($insertDociD));
    //
    //        $id1 = 'id1=' . $encDocId;
    //
    //        $QUERY_STRING = str_replace("%3D", "=", $_SERVER['QUERY_STRING']);
    //
    //        //replace doc Id in Url query string.
    //        $query_string = str_replace('id1=' . $_GET['id1'], $id1, $QUERY_STRING);
    //        $redirect_url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) . '?' . $query_string;
    //       
    //    }


    return $insertDociD;
    //////////////
}

function getActualPageNo($db_con, $versionCount, $slid, $docId)
{

    $pagenum = $versionCount + 1;

    $updateDocName  = $slid . '_' . $docId . '_' . $pagenum;

    $chekFileVersion = mysqli_query($db_con, "SELECT * FROM `tbl_document_master` WHERE find_in_set('$updateDocName', doc_name)") or die('Error:' . mysqli_error($db_con));

    if (mysqli_num_rows($chekFileVersion) > 0) {

        return getActualPageNo($db_con, $pagenum, $slid, $docId);
    } else {

        return $updateDocName;
    }
}


function restorePage($fisrst_pdf, $first_pdf_pnum, $second_pdf, $fpos, $did, $is_version = '')
{
    global $chk;
    $pdf = new Fpdi();
    //to get total pages for page number printing.
    $pdf->AliasNbPages();

    //check for postion
    if ($fpos == 'b' and $first_pdf_pnum == 1) {
        $pageCount = $pdf->setSourceFile($second_pdf);
        $inPageCount = $pageCount;
        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            $pageId = $pdf->importPage($pageNo, PdfReader\PageBoundaries::MEDIA_BOX);
            $size = $pdf->getTemplateSize($pageId);

            $width = $size['width'];
            $height = $size['height'];

            $pdf->addPage();
            $pdf->useImportedPage($pageId, 0, 0, $width, $height, true);
        }

        $pageCount = $pdf->setSourceFile($fisrst_pdf);
        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {

            $pageId = $pdf->importPage($pageNo, PdfReader\PageBoundaries::MEDIA_BOX);
            $size = $pdf->getTemplateSize($pageId);

            $width = $size['width'];
            $height = $size['height'];

            $pdf->addPage();
            $pdf->useImportedPage($pageId, 0, 0, $width, $height, true);
        }
    } else {

        ($fpos == 'b' ? $first_pdf_pnum = $first_pdf_pnum - 1 : $first_pdf_pnum);
        $pageCount = $pdf->setSourceFile($fisrst_pdf);

        for ($pageNo = 1; $pageNo <= $first_pdf_pnum; $pageNo++) {

            $pageId = $pdf->importPage($pageNo, PdfReader\PageBoundaries::MEDIA_BOX);
            $size = $pdf->getTemplateSize($pageId);

            $width = $size['width'];
            $height = $size['height'];

            $pdf->addPage();
            $pdf->useImportedPage($pageId, 0, 0, $width, $height, true);
        }

        $pageCount = $pdf->setSourceFile($second_pdf);
        $inPageCount = $pageCount;

        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            $pageId = $pdf->importPage($pageNo, PdfReader\PageBoundaries::MEDIA_BOX);
            $size = $pdf->getTemplateSize($pageId);

            $width = $size['width'];
            $height = $size['height'];

            $pdf->addPage();
            $pdf->useImportedPage($pageId, 0, 0, $width, $height, true);
        }

        $pageCount = $pdf->setSourceFile($fisrst_pdf);
        if ($pageCount > $first_pdf_pnum) {
            for ($pageNo = $first_pdf_pnum + 1; $pageNo <= $pageCount; $pageNo++) {
                $pageId = $pdf->importPage($pageNo, PdfReader\PageBoundaries::MEDIA_BOX);
                $size = $pdf->getTemplateSize($pageId);

                $width = $size['width'];
                $height = $size['height'];

                $pdf->addPage();
                $pdf->useImportedPage($pageId, 0, 0, $width, $height, true);
            }
        }
    }

    $pdf->Output(F, $fisrst_pdf);
    if (FTP_ENABLED && $chk != 'rw') {
        chkFtp($fisrst_pdf);
    }

    //update page count.
    updatePdfPageCount($fisrst_pdf, $did);

    // define action details to generate log
    $pgc = ($inPageCount > 1 ? $inPageCount . " pages" : $inPageCount . " page");
    //$pgl=($pageCount>0 ? " after page no. ".$first_pdf_pnum : " at the begining of file.");
    $pgl = ($first_pdf_pnum == 1 && $fpos == 'b' ? " at the begining of file." : " after page no. " . $first_pdf_pnum);
    $action_details = 'A Pdf file of ' . $pgc . ' inserted ' . $pgl;

    return true;
}

//======================================================================================
// TCPDF Section start here                                                             =
//=======================================================================================
/*sk@(281118):Add Digital Signature to the pdf file.
 * Accept only pdf file.
 * parameters used:
 * -> source file required to be digitally signed.
 * -> Certificate  required for implementation
 * -> Signature image if we want to view/appear signature image on the document after digitally signed. 
 */

/*function setDigitalSignature($source_file,$certificate,$sign_image){
      
    require_once('tcpdf/examples/tcpdf_include.php');
    require_once('tcpdf/Fpdi/src/autoload.php');
   
    $pdf = new Fpdi\TcpdfFpdi();
     
    // set additional information
    $info = array(
        'Name' => 'TCPDF',
        'Location' => 'Office',
        'Reason' => 'Testing TCPDF',
        'ContactInfo' => 'http://www.tcpdf.org',
        );

    // set document signature
    $pdf->setSignature($certificate, $certificate, 'tcpdfdemo', '', 2, $info);

    // get file for processing
    $pageCount = $pdf->setSourceFile($source_file);
    for ($i = 1; $i <= $pageCount; $i++) {
        $tplIdx = $pdf->importPage($i, '/MediaBox');
        $size = $pdf->getTemplateSize($tplIdx);
        $width=$size['width'];
        $height=$size['height'];
        $pdf->AddPage();
        // *** set signature appearance ***

        // create content for signature (image and/or text)
        $pdf->Image($sign_image, 180, 60, 15, 15, 'PNG');

       // define active area for signature appearance
       $pdf->setSignatureAppearance(180, 60, 15, 15);

    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

    // *** set an empty signature appearance ***
        $pdf->addEmptySignatureAppearance(180, 80, 15, 15);
        $pdf->useTemplate($tplIdx);
        $pdf->useImportedPage($tplIdx, 0, 0,$width,$height,true);
    }

    $pdf->Output('abc.pdf', 'D');

}*/
