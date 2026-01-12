<?php 
require_once './loginvalidate.php';

if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['export'])) {
    // Database connection
    mysqli_set_charset($db_con, "utf8");

    // Define CSV file name
    $filename = 'ocr_pending_list.csv';

    // Define CSV headers
    $headers = array('SNO', 'Folder Name', 'File Name', 'File Size', 'No of Pages');

    // Fetch data from the database
    $sql = "SELECT doc_name, old_doc_name, doc_extn, noofpages, doc_size FROM tbl_document_master WHERE ocr='0' AND flag_multidelete='1'";
    $result = mysqli_query($db_con, $sql);

    // print_r($result);
    // die('ss');

    // Start building CSV content
    $csvContent = implode(',', $headers) . PHP_EOL;

    // Add data rows
    $i = 1;
    while ($rwUser = mysqli_fetch_assoc($result)) {
        // Fetch folder name based on document name
        $folderQuery = "SELECT sl_name FROM tbl_storage_level WHERE sl_id = '{$rwUser['doc_name']}'";
        $folderResult = mysqli_query($db_con, $folderQuery);
        $folderName = ($folderResult && mysqli_num_rows($folderResult) > 0) ? mysqli_fetch_assoc($folderResult)['sl_name'] : '';

        // Get file name
        $fileName = preg_replace('/\\.[^.\\s]{3,4}$/', '', $rwUser['old_doc_name']) . '.' . $rwUser['doc_extn'];

        // Get file size in MB
        $fileSizeMB = round($rwUser['doc_size'] / 1024 / 1024, 2) . " MB";
        // ob_start();
        // Add row to CSV content
        $csvContent .= '"' . implode('","', array(
            $i++, // SNO
            $folderName,
            $fileName,
            $fileSizeMB,
            $rwUser['noofpages']
        )) . '"' . PHP_EOL;
    }

    // Set headers for CSV download
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');

    // Prevent any HTML output before this point
    ob_clean();

    // Output CSV content
    echo $csvContent;
    exit;
}
