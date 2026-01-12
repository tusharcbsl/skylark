<?php
$dbHost = "192.168.20.155:3307"; 
$dbUser = "root"; 
$dbPwd = "Root#123"; 
$dbName = "barbarik_ezeeoffice";

// Create connection
$conn = new mysqli($dbHost, $dbUser, $dbPwd, $dbName);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch data from the database
$sql = "SELECT id, letter_no, description, file_path, created_at FROM raipur_report ORDER BY created_at DESC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Define the filename
    $filename = "Raipur_Report_" . date("Y-m-d") . ".xls";

    // Set headers for download
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=\"$filename\"");

    // Open output buffer
    $output = fopen("php://output", "w");

    // Column headings
    $header = ["S. No.", "Letter No./Document No./Ref", "Description of Document", "File Name", "Uploaded By", "Date Submitted"];
    fputcsv($output, $header, "\t"); // Use tab as delimiter

    // Fetch data and write to file
    $count = 1;
    while ($row = $result->fetch_assoc()) {
        $fileName = !empty($row['file_path']) ? basename($row['file_path']) : 'No File';
        $data = [
            $count++,
            $row['letter_no'],
            $row['description'],
            $fileName,
            "Satya",
            $row['created_at']
        ];
        fputcsv($output, $data, "\t");
    }

    fclose($output);
    exit;
} else {
    echo "No records found.";
}

$conn->close();
?>
