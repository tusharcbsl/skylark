<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

<?php
// Database connection
require_once './application/config/database.php';
require_once './application/pages/head.php';
require_once './application/pages/function.php';
require_once './excel-viewer/excel_reader.php';

require_once './loginvalidate.php';
require_once './application/pages/head.php';
require_once './application/pages/function.php';
require_once './classes/fileManager.php';
require_once __DIR__ . '/vendor/autoload.php';


// Initialize variables
$updated_at = date('Y-m-d H:i:s');
$updated_by = isset($_SESSION['cdes_user_id']) ? $_SESSION['cdes_user_id'] : null;


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Gather POST data
    $name_of_the_inspecting_engineer = $_POST['name_of_the_inspecting_engineer'] ?? null;
    $inspected_on_date = $_POST['inspected_on_date'] ?? null;
    $ticket_id = $_POST['ticket_id'] ?? null;
    $railway_master = "SELECT * FROM tbl_railway_master where ticket_id='$ticket_id'";
    $railway_query = mysqli_query($db_con, $railway_master);
    $user = "SELECT * FROM tbl_user_master where user_id='$user_id'";
    $user_query = mysqli_query($db_con, $user);
    while ($allot_roww = mysqli_fetch_assoc($railway_query)) {
        $railway_details = $allot_roww;
    }
    $document_master = "SELECT * FROM tbl_document_master where ticket_id='$ticket_id'";
    $document_query = mysqli_query($db_con, $document_master);
    while ($document_row = mysqli_fetch_assoc($document_query)) {
        $document_details = $document_row;
    }


    $enclosures_attached = $_POST['enclosures_attached'] ?? null;
    $signature_of_the_contractor = $_POST['signature_of_the_contractor'] ?? null;
    $remarks_of_the_inspection = $_POST['remarks_of_the_inspection'] ?? null;
    $signature_of_the_inspection = $_POST['signature_of_the_inspection'] ?? null;
    $date = $_POST['date'] ?? null;
    $name = $_POST['name'] ?? null;
    $designation = $_POST['designation'] ?? null;
    // $approved = isset($_POST['approved']) ? 1 : 0;
    // $not_approved = isset($_POST['not_approved']) ? 1 : 0;

    $query = "UPDATE tbl_railway_master SET 
    name_of_the_inspecting_engineer = '$name_of_the_inspecting_engineer', 
    inspected_on_date = '$inspected_on_date', 
    enclosures_attached = '$enclosures_attached', 
    signature_of_the_contractor = '$signature_of_the_contractor', 
    remarks_of_the_inspection = '$remarks_of_the_inspection', 
    signature_of_the_inspection = '$signature_of_the_inspection', 
    date = '$date', 
    name = '$name', 
    designation = '$designation', 
    updated_at = '$updated_at', 
    updated_by = $updated_by, 
    status = 1 
    WHERE ticket_id = '$ticket_id'";

    $result = mysqli_query($db_con, $query) or die('Error:' . mysqli_error($db_con));

    if ($result) {
        $mpdf = new \Mpdf\Mpdf();

        // Build the HTML content
        $htmlContent = '
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request for Inspection</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .table th, .table td { border: 1px solid #000; padding: 8px; text-align: left; }
        .top-center { text-align: center; vertical-align: middle; }
        .upper { margin-top: 20px; }
        .south_railway { width: 100px; }
    </style>
</head>
<body>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th class="col-md-2" rowspan="2" style="vertical-align:middle; text-align:center;">
                    <img src="assets/images/rail.JPEG" class="south_railway" alt="Railway Logo">
                </th>
                <th class="col-md-8" style="text-align:center; font-size:large; vertical-align:middle;">
                    <u>REQUEST FOR INSPECTION (RFI)</u>
                </th>
                <th class="col-md-2" rowspan="2" style="vertical-align:middle; text-align:center;">
                    <img src="assets/images/pra1.JPEG" class="south_railway" alt="Contractor Logo">
                </th>
            </tr>
            <tr>
                <th class="col-md-8" style="text-align:center; vertical-align:middle;">
                    Project Doubling of Railway Project comprising the section commencing from(--) Road station (End CH 967.055)
                    to Surajpur Road Station (End CH : 1006.44) (KM-39.385 KM) beside existing single 84 line in the state of chhattisgarh in the
                    south East central Railway Zone Agt No: SECR/SECRC/CMI/2024/0008/ dt 14-Mar-2024.
                </th>
            </tr>
        </thead>
    </table>

    <table class="table table-bordered upper">
        <thead>
            <tr>
                <th class="col-md-6" colspan="3" style="text-align:left; vertical-align:middle;">Client : South East Central Railway</th>
                <th class="col-md-6" colspan="3" style="text-align:center; vertical-align:middle;">Contractor : Barbrik Project Limited</th>
            </tr>
            <tr>
                <th class="col-md-2 top-center">RFI No</th>
                <th class="col-md-2 top-center">RFI Date</th>
                <th class="col-md-2 top-center">Type <br> (Regular/Spot)</th>
                <th class="col-md-2 top-center">Name Of the Contractor\'s Engineer</th>
                <th class="col-md-2 top-center">Item No as per contract <br> (for payment)</th>
                <th class="col-md-2 top-center">Inspection Required On</th>
            </tr>
            <tr>
            <td class="col-md-2">' . htmlspecialchars($railway_details['rfi_no']) . '</td>
            <td class="col-md-2">' . htmlspecialchars($railway_details['rfi_date']) . '</td>
            <td class="col-md-2">' . htmlspecialchars($railway_details['type_regular']) . '</td>
            <td class="col-md-2">' . htmlspecialchars($railway_details['name_of_the_contractor']) . '</td>
            <td class="col-md-2">' . htmlspecialchars($railway_details['item_no_as_per']) . '</td>
            <td class="col-md-2">' . htmlspecialchars($railway_details['inspection_required_date']) . '</td>
        </tr>
        </thead>
    </table>

    <table class="table table-bordered upper">
        <tr>
            <th class="col-md-2" rowspan="2" colspan="2">Location / Chainage</th>
            <th class="col-md-4" rowspan="2">
                ' . htmlspecialchars($railway_details['location_from']) . ' to ' . htmlspecialchars($railway_details['location_to']) . '
            </th>
            <th class="col-md-4">Name Of the Inspecting Engineer</th>
            <th class="col-md-2">Inspected On</th>
        </tr>
        <tr>
            <th>' . htmlspecialchars($name_of_the_inspecting_engineer) . '</th>
            <th>' . htmlspecialchars($inspected_on_date) .
            '</th>
        </tr>
    </table>

<table class="table table-bordered upper">
    <tr>
        <th class="col-md-12" colspan="2" style="text-align:left;">
            Request for Inspection of the following works, Which are /will be ready for inspection
        </th>
    </tr>';

        // Checkbox items

        $checkboxItems = [
            ['C&G', $railway_details['c_and_g']],
            ['Concreting', $railway_details['concreting']],
            ['Earthwork', $railway_details['earthwork']],
            ['Blanketing', $railway_details['blanketing']],
            ['Survey', $railway_details['survey']], // Added
            ['Safety', $railway_details['safety']], // Added
            ['QC/Material', $railway_details['qc_material']], // Added
            ['Shuttering/Reinforcement', $railway_details['shuttering_reinforcement']], // Added
            ['Drain Retaining Wall', $railway_details['drain_retaining_wall']], // Ensure you add corresponding checkboxes
            ['Roads', $railway_details['roads']],
            ['utilities', $railway_details['utilities']],
            ['Dismantling of Pway', $railway_details['dismantling_of_Pway']],
            ['bridge_work', $railway_details['bridge_work']],
            ['Others', $railway_details['other']],
            
        ];

        foreach ($checkboxItems as $index => $item) {
            // Check if the value is '1' (Yes)
            if (isset($item[1]) && $item[1] == '1') {
                $htmlContent .= '
                <tr>
                    <th class="col-md-6" style="text-align:left;">'  . htmlspecialchars($item[0]) . '</th>
                    <th class="col-md-6" style="text-align:left;">Yes</th>
                </tr>';
            }
        }

        $htmlContent .= '
</table>

    <table class="table table-bordered upper">
        <tr>
            <th colspan="2" style="text-align:left; vertical-align:top; height: 200px">Description of Work offered for Inspection</th>
            <th colspan="3" style="text-align:left; vertical-align:top;">
                ' . htmlspecialchars($railway_details['description_of_work']) . '
            </th>
        </tr>
        <tr>
            <th colspan="2" style="text-align:left; vertical-align:top; height: 200px">Enclosures attached with RFI</th>
            <th colspan="3" style="text-align:left; vertical-align:top;">
                ' . htmlspecialchars($enclosures_attached) . '
            </th>
        </tr>
        <tr>
            <th class="col-md-6" colspan="2" style="text-align:left; vertical-align:top; height: 100px">Signature of the Contractor\'s Representative requesting for Inspection</th>
<th class="col-md-6" colspan="3">
            ' . $_SESSION['admin_user_name'] . ' ' . $_SESSION['admin_user_last'] . '
        </th>
                </tr>
        <tr>
            <th class="col-md-6" colspan="2" style="text-align:left; vertical-align:top; height: 100px">Remarks of the Inspection Engineer (Representative of Authority Engineer)</th>
            <th class="col-md-6" colspan="3">' . htmlspecialchars($remarks_of_the_inspection) . '</th>
        </tr>
        <tr>
            <th colspan="2">Signature of the Inspection Engineer Representative of Authority Engineer</th>
            <th colspan="3">' . htmlspecialchars($signature_of_the_inspection) . '</th>
        </tr>
        <tr>
            <th colspan="2" style="text-align:right;">Date :</th>
            <th colspan="3">' . date('d/m/y') . '</th>
        </tr>
        <tr>
            <th colspan="2" style="text-align:right;">Name :</th>
<th class="col-md-6" colspan="3">
            ' . $_SESSION['admin_user_name'] . ' ' . $_SESSION['admin_user_last'] . '
        </th>        </tr>
        <tr>
            <th colspan="2" style="text-align:right;">Designation :</th>
            <th colspan="3">' . htmlspecialchars($designation) . '</th>
        </tr>
    </table>

    <div class="col-md-14">
        <div class="container">
            <div class="card-box">
                <div id="dynamicForm">
                    <div class="row" id="formRows">';

        $queryyy = "SELECT * FROM tbl_railway_attachment_master WHERE requested_id='" . $railway_details['id'] . "'";
        $resultt = mysqli_query($db_con, $queryyy);

        // Check if the query was successful
        if ($resultt) {
            while ($rowwe = mysqli_fetch_assoc($resultt)) {
                $htmlContent .= '
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="mobile">Remark:</label>
                                ' . htmlspecialchars($rowwe['remark']) . '
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Upload Attachment</label>
                                <a href="uploads/' . htmlspecialchars($rowwe['attachment']) . '" download>
                                    ' . htmlspecialchars($rowwe['attachment']) . '
                                </a>
                            </div>
                        </div>
                    </div>';
            }
        }

        $htmlContent .= '
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>';

        include 'exportpdf.php';
        $path = 'extract-here/' . $document_details['doc_path'];
        exportPDF($htmlContent, $path);

        // Write HTML content to the PDF
        // $mpdf->WriteHTML($htmlContent);

        // Output to download
        // $mpdf->Output('Request_for_Inspection.pdf', 'D'); // 'D' for download
        echo '<script>
    alert("Submitted Successfully");
    window.location.href = "myTask.php"; // Change this to your desired URL
</script>';
    } else {
        echo '<script>
    alert("Something went Wrong!!");
    window.location.href = "myTask.php"; // Change this to your desired URL
</script>';
    }
}
