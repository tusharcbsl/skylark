<?php
require 'vendor/autoload.php';

// Start output buffering
ob_start();
include 'request_form.php'; // Capture the HTML content from this file
$content = ob_get_clean(); // Get the content

if (empty($content)) {
    die("No content to generate PDF."); // Ensure there's content
}

// Create a new PDF instance
$mpdf = new \Mpdf\Mpdf();

try {
    // Write the HTML content to the PDF
    $mpdf->WriteHTML($content);

    // Output the PDF directly to the browser
    $mpdf->Output('form_template.pdf', 'D'); // Force download
} catch (\Mpdf\MpdfException $e) {
    // Handle any exceptions
    die("Error generating PDF: " . $e->getMessage());
}

exit; // Prevent further output
?>
