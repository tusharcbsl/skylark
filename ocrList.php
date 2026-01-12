<!DOCTYPE html>
<html>
<?php
header("refresh: 40;");
require_once './loginvalidate.php';
require_once './application/config/database.php';
require_once './application/pages/head.php';
require_once './classes/ftp.php';
$sameGroupIDs = array();
$group = mysqli_query($db_con, "select group_id from tbl_bridge_grp_to_um where find_in_set('$_SESSION[cdes_user_id]',user_ids)") or die('Error' . mysqli_error($db_con));
while ($rwGroup = mysqli_fetch_assoc($group)) {
    $sameGroupIDs[] = $rwGroup['group_id'];
}
$sameGroupIDs = array_unique($sameGroupIDs);
sort($sameGroupIDs);
$sameGroupIDs = implode(',', $sameGroupIDs);

if ($rwgetRole['view_ocr_list'] != '1') {
    header('Location: ./index');
}
?>
<?php

//print_r($newArray);
//print_r($storedpath);

?>
<link href="assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
<!-- Plugin Css-->
<link rel="stylesheet" href="assets/plugins/magnific-popup/css/magnific-popup.css" />
<link rel="stylesheet" href="assets/plugins/jquery-datatables-editable/datatables.css" />

<script> 
        function toggle(source) { 
            let checkboxes = document 
                .querySelectorAll('input[type="checkbox"]'); 
            for (let i = 0; i < checkboxes.length; i++) { 
                if (checkboxes[i] != source) 
                    checkboxes[i].checked = source.checked; 
            } 
        } 
    </script> 

<body class="fixed-left">
    <!-- Begin page -->
    <div id="wrapper">
        <!-- Top Bar Start -->
        <?php require_once './application/pages/topBar.php'; ?>
        <!-- Top Bar End -->
        <!-- ========== Left Sidebar Start ========== -->
        <?php require_once './application/pages/sidebar.php'; ?>

        <!-- Left Sidebar End -->
        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="content-page">
            <!-- Start content -->
            <div class="content">
                <div class="container">
                    <!-- Page-Title -->
                    <div class="row">
                        <div class="col-sm-12">
                            <ol class="breadcrumb">
                                <li>
                                    <a href="index"><?php echo $lang['Das']; ?></a>
                                </li>
                                <li>
                                    <a href="ocrList"><?php echo $lang['ocr_pending_list']; ?></a>
                                </li>

                                <a href="javascript:void(0)" class="btn btn-primary waves-effect waves-light pull-right btn-sm margin-t-9" onclick="goPrevious();" title="<?php echo $lang['Go_to_previous_page']; ?>"><i class="fa fa-arrow-circle-left"></i></a>
                            </ol>
                        </div>
                    </div>
                    <form action="" method="post" style="margin-top:20px">
                        <input placeholder="FROM DATE" type='text' onfocus="(this.type='date')" id='fromdate' name='fromdate' style="height:35px;width:20%;" />
                        <input placeholder="TO DATE" type='text' onfocus="(this.type='date')" id='todate' name='todate' placeholder="To Date" style="height:35px;width:20%;" />
                        <input placeholder="ENTER NO OF PAGES" type='text' id='page' name='page' style="height:35px;width:20%;" />
                        <select type="select" name="storage" style="height:35px;border-radius:3px;width:20%;">
                            <option value="">Select Storage Level</option>
                            <?php
                            $result = mysqli_query($db_con,"SELECT sl_id,sl_name FROM  tbl_storage_level");
                        
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                
                        ?>
                                <option value="<?php echo $row['sl_id'] ?>"><?php echo $row['sl_name'] ?></option>
                                <?php
                            }
                        }
                        ?>
                        </select>     
                    <input type="submit" class="btn btn-primary" value="FILTER LIST" name="abc" />
                    </form>
                    <?php
                    mysqli_set_charset($db_con, "utf8");
                    $sql = "SELECT doc_name,old_doc_name,doc_extn,noofpages,doc_size FROM  tbl_document_master where ocr='0' and flag_multidelete='1'";
                    $retval = mysqli_query($db_con, $sql);
                    $foundnum = mysqli_num_rows($retval);
                    if ($foundnum != 0) {
                    ?>
                        <div style="text-align: right; margin-bottom: 15px">
                            <!-- <form action="" method="post">
                                <input type="submit" class="btn btn-primary" value="Start OCR Process" name="ocr" />
                            </form> -->
                            <form action="export_pendingocr" method="post" style="margin-top:10px;">
                                <input type="submit" class="btn btn-primary" value="Export" name="export" />
                            </form>
                        </div>
                        <!-- <div style="text-align: right; margin-bottom: 15px">
    <form id="combinedForm">
        <input type="submit" class="btn btn-primary" value="Start OCR Process" name="ocr" data-action="" />
        <input type="submit" class="btn btn-primary" value="Export" name="export" data-action="export_pendingocr" />
    </form>
</div> -->

                        <!-- <script>
document.getElementById('combinedForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Prevent default form submission

    // Determine which button was clicked
    var formData = new FormData(this);
    var action = formData.get('ocr') ? '' : 'export_pendingocr'; // Default action for 'ocr', custom action for 'export'

    // Set form action based on the clicked button
    this.action = action;
    this.submit(); // Submit the form
});
</script> -->

                    <?php
                    }
                    ?>
                    <div class="box box-primary">
                        <div class="panel">
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-sm-12">

                                    </div>
                                </div>
                                <div class="row">
                                    <?php
                                    if (isset($_POST['abc']) && !empty($_POST['abc'])) {
                                                //$groupName = preg_replace("/[^A-Za-z0-9 ]/", "", $_GET['grpname']);
                                            $fromdate = $_POST['fromdate'];
                                            $todate = $_POST['todate'];
                                            $storagel = $_POST['storage'];
                                            $page = $_POST['page'];
                                            // print_r($fromdate.$todate.$storagel.$page);
                                            // die('q');
                                            if(!empty($fromdate) and !empty($todate) and !empty($storagel) and !empty($page)){
                                                $where .= "and dateposted BETWEEN '$fromdate' AND '$todate' and doc_name='$storagel' and noofpages <= $page";
                                            }
                                            else if(!empty($fromdate) and empty($todate) and empty($storagel) and !empty($page)){
                                                $where .= "and dateposted BETWEEN '$fromdate' AND '$fromdate' and noofpages <= $page";
                                            }
                                            else if(!empty($fromdate) and empty($todate) and empty($storagel) and !empty($page)){
                                                $where .= "and dateposted BETWEEN '$fromdate' AND '$fromdate'";
                                            }
                                            else if(!empty($fromdate) and !empty($todate) and empty($storagel) and empty($page)){
                                                $where .= "and dateposted BETWEEN '$fromdate' AND '$todate'";
                                            }
                                            else if(!empty($fromdate) and !empty($todate) and empty($storagel) and !empty($page)){
                                                $where .= "and dateposted BETWEEN '$fromdate' AND '$todate' and noofpages <= $page";
                                            }
                                            else if(empty($fromdate) and empty($todate) and !empty($storagel) and empty($page)){
                                                $where .= "and doc_name='$storagel'";
                                            }
                                            else if(empty($fromdate) and empty($todate) and !empty($storagel) and !empty($page)){
                                                $where .= "and doc_name='$storagel' and noofpages <= $page";
                                            }
                                            else if(empty($fromdate) and empty($todate) and empty($storagel) and !empty($page)){
                                                $where .= "and noofpages <= $page";
                                            }
                                        }
                                    // print_r($where);
                                    // die('qq');    
                                    mysqli_set_charset($db_con, "utf8");
                                    $sql = "SELECT doc_id,doc_name,old_doc_name,doc_extn,noofpages,doc_size FROM  tbl_document_master where ocr='0' and flag_multidelete='1' $where";
                                    // print_r($sql);
                                    // die('qq');    

                                    $retval = mysqli_query($db_con, $sql); //or die('Could not get data: ' . mysqli_error($db_con));
                                    $foundnum = mysqli_num_rows($retval);
                                    if ($foundnum > 0) {
                                        $StartPoint = preg_replace("/[^0-9]/", "", $_GET['limit']); //filter limit from all special chars
                                        if (is_numeric($StartPoint)) {
                                            $per_page = $StartPoint;
                                        } else {
                                            $per_page = 10;
                                        }
                                        //$start = preg_replace("/[^0-9]/", "", $_GET['start']); //filter start variable
                                        $start = isset($_GET['start']) ? ($_GET['start'] > 0) ? $_GET['start'] : 0 : 0;
                                        $max_pages = ceil($foundnum / $per_page);
                                        if (!$start) {
                                            $start = 0;
                                        }
                                        $limit = $_GET['limit'];
                                    ?>
                                        <div class="box-body">
                                            <label><?php echo $lang['show_lst']; ?> </label>
                                            <select id="limit" class="input-sm">
                                                <option value="10" <?php
                                                                    if ($limit == 10) {
                                                                        echo 'selected';
                                                                    }
                                                                    ?>>10</option>
                                                <option value="25" <?php
                                                                    if ($limit == 25) {
                                                                        echo 'selected';
                                                                    }
                                                                    ?>>25</option>
                                                <option value="50" <?php
                                                                    if ($limit == 50) {
                                                                        echo 'selected';
                                                                    }
                                                                    ?>>50</option>
                                                <option value="100" <?php
                                                                    if ($limit == 100) {
                                                                        echo 'selected';
                                                                    }
                                                                    ?>>100</option>
                                                <option value="250" <?php
                                                                    if ($limit == 250) {
                                                                        echo 'selected';
                                                                    }
                                                                    ?>>250</option>
                                                <option value="500" <?php
                                                                    if ($limit == 500) {
                                                                        echo 'selected';
                                                                    }
                                                                    ?>>500</option>
                                            </select>
                                            <label><?php echo $lang['ttl_recrds']; ?></label>

                                            <div class="pull-right record">
                                                <label> <?php echo $start + 1 ?> <?php echo $lang['To']; ?> <?php
                                                                                                            if ($start + $per_page > $foundnum) {
                                                                                                                echo $foundnum;
                                                                                                            } else {
                                                                                                                echo ($start + $per_page);
                                                                                                            }
                                                                                                            ?> <span><?php echo $lang['ttl_recrds']; ?>: <?php echo $foundnum; ?></span></label>
                                            </div>
                                            <?php
                                            mysqli_set_charset($db_con, "utf8");
                                            $users = mysqli_query($db_con, "$sql LIMIT $start, $per_page") or die('Error:' . mysqli_error($db_con));
                                            //                                             
                                            showData($users, $db_con, $start, $lang);
                                            ?>
                                            <?php
                                            echo "<center>";
                                            $prev = $start - $per_page;
                                            $next = $start + $per_page;
                                            $adjacents = 3;
                                            $last = $max_pages - 1;
                                            if ($max_pages > 1) {
                                            ?>

                                                <ul class='pagination strgePage'>
                                                    <?php
                                                    //previous button
                                                    if (!($start <= 0))
                                                        echo " <li><a href='?start=$prev'>$lang[Prev]</a> </li>";
                                                    else
                                                        echo " <li class='disabled'><a href='javascript:void(0)'>$lang[Prev]</a> </li>";
                                                    //pages 
                                                    if ($max_pages < 7 + ($adjacents * 2)) {   //not enough pages to bother breaking it up
                                                        $i = 0;
                                                        for ($counter = 1; $counter <= $max_pages; $counter++) {
                                                            if ($i == $start) {
                                                                echo "<li class='active'><a href='?start=$i&limit=$per_page'><b>$counter</b></a> </li>";
                                                            } else {
                                                                echo "<li><a href='?start=$i&limit=$per_page'>$counter</a></li> ";
                                                            }
                                                            $i = $i + $per_page;
                                                        }
                                                    } elseif ($max_pages > 5 + ($adjacents * 2)) {    //enough pages to hide some
                                                        //close to beginning; only hide later pages
                                                        if (($start / $per_page) < 1 + ($adjacents * 2)) {
                                                            $i = 0;
                                                            for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++) {
                                                                if ($i == $start) {
                                                                    echo " <li class='active'><a href='?start=$i&limit=$per_page'><b>$counter</b></a></li> ";
                                                                } else {
                                                                    echo "<li> <a href='?start=$i&limit=$per_page'>$counter</a> </li>";
                                                                }
                                                                $i = $i + $per_page;
                                                            }
                                                        }
                                                        //in middle; hide some front and some back
                                                        elseif ($max_pages - ($adjacents * 2) > ($start / $per_page) && ($start / $per_page) > ($adjacents * 2)) {
                                                            echo " <li><a href='?start=0'>1</a></li> ";
                                                            echo "<li><a href='?start=$per_page'>2</a></li>";
                                                            echo "<li><a href='javascript:void(0)'>...</a></li>";

                                                            $i = $start;
                                                            for ($counter = ($start / $per_page) + 1; $counter < ($start / $per_page) + $adjacents + 2; $counter++) {
                                                                if ($i == $start) {
                                                                    echo " <li class='active'><a href='?start=$i&limit=$per_page'><b>$counter</b></a></li> ";
                                                                } else {
                                                                    echo " <li><a href='?start=$i&limit=$per_page'>$counter</a> </li>";
                                                                }
                                                                $i = $i + $per_page;
                                                            }
                                                        }
                                                        //close to end; only hide early pages
                                                        else {
                                                            echo "<li> <a href='?start=0'>1</a> </li>";
                                                            echo "<li><a href='?start=$per_page'>2</a></li>";
                                                            echo "<li><a href='javascript:void(0)'>...</a></li>";

                                                            $i = $start;
                                                            for ($counter = ($start / $per_page) + 1; $counter <= $max_pages; $counter++) {
                                                                if ($i == $start) {
                                                                    echo " <li class='active'><a href='?start=$i&limit=$per_page'><b>$counter</b></a></li> ";
                                                                } else {
                                                                    echo "<li> <a href='?start=$i&limit=$per_page'>$counter</a></li> ";
                                                                }
                                                                $i = $i + $per_page;
                                                            }
                                                        }
                                                    }
                                                    //next button
                                                    if (!($start >= $foundnum - $per_page))
                                                        echo "<li><a href='?start=$next'>$lang[Next]</a></li>";
                                                    else
                                                        echo "<li class='disabled'><a href='javascript:void(0)'>$lang[Next]</a></li>";
                                                    ?>
                                                </ul>
                                            <?php
                                            }
                                            echo "</center>";
                                        } else {
                                            ?>

                                            <div class="form-group form-group no-records-found"><label><strong class="text-danger"><i class="ti-face-sad text-pink"></i> <?php echo $lang['Who0ps!_No_Records_Found']; ?></strong></label></div>
                                        <?php }
                                        ?>
                                        </div>
                                </div>
                                <!-- <form action="" method="post" style="margin-top:20px"> -->
                                    <!-- <input type="hidden" name="selecteddoc" value="abc" /> -->
                                    <!-- <input type="submit" class="btn btn-primary" value="Start OCR Process" name="ocr" /> -->
                                <!-- </form> -->
                                <!-- end: page -->
                            </div> <!-- end Panel -->
                        </div> <!-- container -->

                    </div> <!-- content -->
                </div>

            </div>
            <?php

            //  if ($_SERVER['REQUEST_METHOD'] == "POST" and isset($_POST['daterange'])) {
            //     global $fromdate;
            //     $fromdate=$_POST['fromdate'];
            //     $todate=$_POST['fromdate'];
            //     }

                // print_r($fromdate);
                // die('ss');

            //     if (isset($_POST['abc'])){
			// 	global $checkbox;
            //     $checkbox = $_POST['checkboxvalue'];
            //     //die('ss');
			// }


            if ($_SERVER['REQUEST_METHOD'] == "POST" and isset($_POST['ocr'])) {
                //header("refresh: 10;");
                //echo'<script>alert("OCR PROCESS STARTED.. IT WILL TAKE TIME. PAGE WILL REFRESH ON EVERY 30 SECONDS.. ");</script>';
                //echo '<script>taskSuccess("ocrList","OCR PROCESS STARTED.. IT WILL TAKE TIME. PAGE WILL REFRESH ON EVERY 30 SECONDS.. ");</script>';
                                // print_r($fromdate);
                // die('ss');

                // $fromdate=$_POST['fromdate'];
                // $todate=$_POST['todate'];
                //$selecteddoc=$_POST['selecteddoc'];
                // $storage=$_POST['storage'];
                $checkedbox=$_POST['checkboxvalue'];

            // print_r($checkedbox);
            // die('qq');

                // echo'<script>alert("OCR PROCESS STARTED.. IT WILL TAKE TIME. PAGE WILL REFRESH ON EVERY 30 SECONDS.. ");</script>';

                // if(!empty($fromdate) and !empty($todate) and !empty($storage)){
                // $sqlite = "SELECT doc_id, doc_path FROM  tbl_document_master where ocr='0' and flag_multidelete='1' and dateposted BETWEEN '$fromdate' AND '$todate' and doc_name='$storage'";
                // }
                // else if(!empty($storage)){
                //     $sqlite = "SELECT doc_id, doc_path FROM  tbl_document_master where ocr='0' and flag_multidelete='1' and doc_name='$storage'";
                // }
                // else if(!empty($fromdate) and !empty($todate)){
                //     $sqlite = "SELECT doc_id, doc_path FROM  tbl_document_master where ocr='0' and flag_multidelete='1' and dateposted BETWEEN '$fromdate' AND '$todate'";
                // }
                if(!empty($checkedbox)){
                    $sqlite = "SELECT doc_id, doc_path FROM  tbl_document_master where ocr='0' and flag_multidelete='1' and doc_id IN (".implode(',',$checkedbox).")";
                }
                else{
                    echo'<script>alert("PLEASE SELECT FILE FIRST!")</script>';
                    
                }
                // else{
                //     $sqlite = "SELECT doc_id, doc_path FROM  tbl_document_master where ocr='0' and flag_multidelete='1'";
                // }
                // print_r($sqlite);
                // die('s');
                $result = mysqli_query($db_con, $sqlite);
                
                // Initialize an array to store the results
                $docIds = [];
                $docpath = [];
                // Fetch the results
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $docIds[] = $row['doc_id'];
                        $docpath[] = 'DMS/' . ROOT_FTP_FOLDER . '/' . $row['doc_path'];
                        $docpath2[] = 'extract-here' . '/' . $row['doc_path'];
                        $docpath3[] = $row['doc_path'];
                    }
                }
                $storedid = $docIds;
                $storedpath = $docpath;
                $storedpath2 = $docpath2;
                $storedpath3 = $docpath3;


                // print_r($storedid);
                // die('aa');


                $newArray = array_map(function ($path) {
                    return dirname($path) . '/';
                }, $storedpath);
                $newArray2 = array_map(function ($path) {
                    return dirname($path) . '/';
                }, $storedpath2);
                $newArray3 = array_map(function ($path) {
                    return dirname($path) . '/';
                }, $storedpath3);
// print_r($newArray3[0]);
// die('ss');

                $img_array = array('pdf', 'jpg', 'jpeg', 'png', 'bmp', 'pnm', 'jfif', 'jpeg', 'tiff');
                for ($i = 0; $i <= count($storedpath); $i++) {

                    $ftpdir = $storedpath[$i];
                    $ftpdir2 = $storedpath3;
                    // print_r($ftpdir2);
                    // die('qq');


                    // if (FTP_ENABLED) {

                    //     $ftp = new ftp();

                    //     $ftp->conn("$fileserver", "$port", "$ftpUser", "$ftpPwd");

                    //     if ($ftp->get($storedpath2[$i], $ftpdir)) {
                    //         //print_r("success");
                    //         //die('ss');
                    //     } else {
                    //         //print_r("failed");
                    //         //die('sst');
                    //     }
                    // }


                    $path_info = pathinfo($storedpath2[$i]);

                    $extn = $path_info['extension'];

                    if ($extn == "pdf") {
                        $noofPages = count_pages($target_path . $filenameEnct);
                    } elseif ($extn == "docx") {
                        $noofPages = PageCount_DOCX($target_path . $filenameEnct);
                    } else {
                        $noofPages = 1;
                    }

                    $txtpath = $newArray2[$i] . 'TXT/';

                    $txtpath2[] = $newArray[$i] . 'TXT/';




                    if (!is_dir($txtpath)) {
                        mkdir($txtpath, 0777, TRUE) or die(print_r(error_get_last()));
                    }

                    $extractHereDirfile = $storedpath[$i];
                    if (strtolower($extn) == "doc") {
                        $docText = read_doc($extractHereDirfile);
                    } elseif (strtolower($extn) == "docx") {
                        $docText = read_docx($extractHereDirfile);
                    } elseif (strtolower($extn) == "xlsx") {
                        $docText = xlsx_to_text($extractHereDirfile);
                    } elseif (strtolower($extn) == "xls") {
                        //$docText = xls_to_txt($extractHereDirfile);
                    } elseif (strtolower($extn) == "pptx" || strtolower($extn) == "ppt") {
                        $docText = pptx_to_text($extractHereDirfile);
                    } else if (strtolower($extn) == "txt" || strtolower($extn) == "text") {
                        $docText = txt_to_text($extractHereDirfile);
                    } else if (in_array(strtolower($extn), $img_array)) {
                        $fpathwithname[] = $storedpath2;
                        $fpath[] = $newArray2;
                        $fdocid[] = $storedid;
                        $pCount[] = $noofPages;
                    }
                    // echo $docText;
                    // die('hghe');
                    if (!empty($docText)) {
                        $fp = fopen($txtpath . $doc_id . ".txt", "wb");
                        fwrite($fp, $docText);
                        fclose($fp);
                    }
                    
                    
                }
                // print_r($txtpath2);
                // die('ss');
                getData($storedid, $newArray2, $storedpath2, $pCount, $ocrUrl, $ftpdir2 );
                    // print_r($storedpath2);
                // die('sss');


                if (FTP_ENABLED) {

                    $ftp = new ftp();

                    $ftp->conn("$fileserver", "$port", "$ftpUser", "$ftpPwd");

                    if ($ftp->put($storedpath2[$i], $ftpdir)) {
                        //print_r("success");
                        //die('ss');
                    } else {
                        //print_r("failed");
                        //die('sst');
                    }
                }
            }

            function spreadSheetCount()
            {
                $excel = new PhpExcelReader;
                $excel->read('test.xls');
                $number_of_Sheets = count($excel->sheets);
            }

            function PageCount_DOCX($file)
            {
                $pageCount = 0;

                $zip = new ZipArchive();

                if ($zip->open($file) === true) {
                    if (($index = $zip->locateName('docProps/app.xml')) !== false) {
                        $data = $zip->getFromIndex($index);
                        $zip->close();
                        $xml = new SimpleXMLElement($data);
                        $pageCount = $xml->Pages;
                    }
                    $zip->close();
                }

                return $pageCount;
            }


            // function count_pages($pdfPath)

            // {

            //     exec('"C:\Program Files\pdfinfo\pdfinfo"' . ' "' . $pdfPath . '"', $output);


            //     $pagecount = 0;

            //     foreach ($output as $op) {
            //         if (preg_match("/Pages:\s*(\d+)/i", $op, $matches) === 1) {

            //             $pagecount = intval($matches[1]);

            //             break;
            //         }
            //     }



            //     return $pagecount;
            // }

            function count_pages($pdfname) {

                // $pdftext = file_get_contents($pdfname);

                // $num = preg_match_all("/\/Page\W/", $pdftext, $dummy);

                // return $num;

                $cmd = "pdfinfo.exe";  // Windows
                // Parse entire output
                // Surround with double quotes if file name has spaces

                exec("$cmd \"$pdfname\"", $output);
                // Iterate through lines

                $pagecount = 0;
                foreach($output as $op) {
                    // Extract the number
                    if(preg_match("/Pages:\s*(\d+)/i", $op, $matches) === 1) {
                        $pagecount = intval($matches[1]);
                        break;
                    }
                }
                return $pagecount;
            }


            function getData($docId, $outputDir, $inputDir, $pCount, $ocrUrl, $ftppath)
            {
                /**
                 * 
                 * @param String $url
                 * @param Array $params 
                 * done by M.U
                 */
                // print_r($docId);
                // die('ss');
                $docId = implode(",", $docId);
                $outputDir = implode(",", $outputDir);
                $inputDir = implode(",", $inputDir);
                $pCount = implode(",", $pCount);
                
                $ftppath= $ftppath;
                $ftppath2= implode(",", $ftppath);;
//                 print_r($ftppath2);
// die('ss');
                $url = BASE_URL . 'ocr_bulk.php';
                $params = array('docId' => $docId, 'outputDir' => $outputDir, 'inputDir' => $inputDir, 'pCount' => $pCount, 'ftppath2'=>$ftppath2);
                //print_r($params);
                // print_r($params);
                foreach ($params as $key => &$val) {
                    if (is_array($val))
                        $val = implode(',', $val);
                    $post_params[] = $key . '=' . urlencode($val);
                }
                $post_string = implode('&', $post_params);
                $parts = parse_url($url); //print_r($parts);die();
                if (isset($_SERVER['HTTPS'])) {
                    $fp = fsockopen('ssl://' . $parts['host'], isset($parts['port']) ? $parts['port'] : 443, $errno, $errstr, 3600);
                } else {
                    $fp = fsockopen($parts['host'], isset($parts['port']) ? $parts['port'] : 80, $errno, $errstr, 3600);
                }
                // $fp = fsockopen('ssl://' . $parts['host'], isset($parts['port']) ? $parts['port'] : 443, $errno, $errstr, 3600000);

                if (!$fp) {
                } else {
                    $out = "POST " . $parts['path'] . " HTTP/1.1\r\n";
                    $out .= "Host: " . $parts['host'] . "\r\n";
                    $out .= "Content-Type: application/x-www-form-urlencoded\r\n";
                    $out .= "Content-Length: " . strlen($post_string) . "\r\n";
                    $out .= "Connection: Close\r\n\r\n";
                    if (isset($post_string))
                        $out .= $post_string;
                    fwrite($fp, $out);
                }
            }

            ?>

            <?php
            ?>

            <!-- END wrapper -->
            <?php require_once './application/pages/footer.php'; ?>

        </div>
        <!-- ============================================================== -->
        <!-- End Right content here -->
        <!-- ============================================================== -->
        <!-- Right Sidebar -->
        <?php //require_once './application/pages/rightSidebar.php';    
        ?>
        <?php require_once './application/pages/footerForjs.php'; ?>

        <script src="assets/plugins/select2/js/select2.min.js" type="text/javascript"></script>

        <script type="text/javascript" src="assets/plugins/parsleyjs/parsley.min.js"></script>
        <script src="assets/plugins/sweetalert2/sweet-alert.init.js"></script>
        <script>
            function taskSuccess(page, msg) {
                swal({
                    title: "Success!",
                    text: msg,
                    type: "success",
                    showCancelButton: false,
                    cancelButtonClass: 'btn-white btn-md waves-effect',
                    confirmButtonClass: 'btn-primary btn-md waves-effect waves-light',
                    confirmButtonText: 'Ok'
                }).then(function() {
                    swal(
                        load(page)
                    )
                })
            }
        </script>



        <script>
            //limit filter
            var url = window.location.href + "?";

            function removeParam(key, sourceURL) {
                sourceURL = String(sourceURL).replace("#/", "");
                var rtn = sourceURL.split("?")[0],
                    param,
                    params_arr = [],
                    queryString = (sourceURL.indexOf("?") !== -1) ? sourceURL.split("?")[1] : "";
                if (queryString !== "") {
                    params_arr = queryString.split("&");
                    for (var i = params_arr.length - 1; i >= 0; i -= 1) {
                        param = params_arr[i].split("=")[0];
                        if (param === key) {
                            params_arr.splice(i, 1);
                        }
                    }
                    rtn = rtn + "?" + params_arr.join("&");
                } else {
                    rtn = rtn + '?';
                }
                return rtn;
            }
            jQuery(document).ready(function($) {
                $("#limit").change(function() {
                    lval = $(this).val();
                    url = removeParam("limit", url);
                    url = removeParam("token", url);
                    url = url + "&limit=" + lval;
                    window.open(url, "_parent");
                });
            });
        </script>
</body>

</html>
<?php

function showData($user, $db_con, $start, $lang)
{
?>
<form method="POST" action="">
    <table class="table table-striped table-bordered js-sort-table">
        <thead>
            <tr>
                <th><input type="checkbox" onclick="toggle(this);" id="new">
<?php echo $lang['SNO']; ?>
                </th>
                <th><?php echo $lang['folder_name']; ?></th>
                <th><?php echo $lang['file_name']; ?></th>
                <th><?php echo $lang['file_size']; ?></th>
                <th><?php echo $lang['No_of_Pages']; ?></th>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 1;
            $i += $start;
            while ($rwUser = mysqli_fetch_assoc($user)) {
            ?>
                <tr class="gradeX">
                    <td><input type="checkbox" value="<?php echo $rwUser['doc_id']; ?>" name="checkboxvalue[]">
                        <?php echo $i . '.'; ?>
                    </td>
                    <td><?php
                        $sl = mysqli_query($db_con, "select sl_name from tbl_storage_level where sl_id='$rwUser[doc_name]'");
                        $rwsl = mysqli_fetch_assoc($sl);
                        echo $rwsl['sl_name'];
                        ?></td>
                    <?php
                    $filename = $rwUser['old_doc_name'];
                    $filewithoutExt = preg_replace('/\\.[^.\\s]{3,4}$/', '', $filename);
                    $fileExtn = $rwUser['doc_extn'];
                    ?>
                    <td><?php echo $filewithoutExt . '.' . $fileExtn; ?></td>
                    <td><?php echo round($rwUser['doc_size'] / 1024 / 1024, 2) . " MB"; ?></td>
                    <td><?php echo $rwUser['noofpages']; ?></td>
                </tr>
            <?php
                $i++;
            }
            ?>
        </tbody>
    </table>
    <!-- <input type="submit" name="ocr" value="Submit"> -->
    <input type="submit" class="btn btn-primary" value="Start OCR Process" name="ocr" />

	</form>

<?php
}
?>