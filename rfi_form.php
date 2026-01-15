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
    $received_name = $_POST['received_name'] ?? null;
    $received_sign = $_POST['received_sign'] ?? null;
    $received_designation = $_POST['received_designation'] ?? null;
    $received_date = !empty($_POST['received_date']) 
    ? date('Y-m-d H:i:s', strtotime($_POST['received_date'])) 
    : NULL;

    $ticket_id = $_POST['ticket_id'] ?? null;
    $form_type = $_POST['form_type'] ?? null;
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


    // $approved = isset($_POST['approved']) ? 1 : 0;
    // $not_approved = isset($_POST['not_approved']) ? 1 : 0;

    $query = "UPDATE tbl_railway_master SET 
    received_name = '$received_name', 
    received_date = '$received_date', 
    received_designation = '$received_designation', 
    received_sign = '$received_sign', 
    enclosures_attached = '$enclosures_attached', 
    signature_of_the_contractor = '$signature_of_the_contractor', 
    remarks_of_the_inspection = '$remarks_of_the_inspection', 
    signature_of_the_inspection = '$signature_of_the_inspection', 

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
                            
                                .info-table{
                                    border-collapse: collapse;
                                    width:100%;
                                }
                                .info-table th,
                                .info-table td{
                                    border:1px solid #000;
                                    padding:6px;
                                    font-size:12px;
                                }
                                .info-table th{
                                    font-weight:bold;
                                }
                                .title{
                                    text-align:center;
                                    font-weight:bold;
                                }
                                </style>
</head>
<body>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th class="col-md-2" rowspan="2" style="vertical-align:middle; text-align:center;">
                    <img src="assets/images/ecr.png" class="south_railway" alt="ECR Logo">
                </th>
                <th class="col-md-8" style="text-align:center; font-size:large; vertical-align:middle;">
                    <u>REQUEST FOR INSPECTION (RFI)</u>
                </th>';

        if (
            $form_type == 1
        ) {
            $htmlContent .= '
                <th class="col-md-2" rowspan="2" style="vertical-align:middle; text-align:center;">
                    <img src="assets/images/pra1.JPEG" class="south_railway" alt="Contractor Logo">
                </th>';
        } else {
            $htmlContent .= '
                <th class="col-md-2" rowspan="2" style="vertical-align:middle; text-align:center;">
                    <img src="assets/images/ecr.png" class="south_railway" alt="Contractor Logo">
                </th>';
        }

        $htmlContent .= '
            </tr>
            <tr>';

        if (
            $form_type == 1
        ) {
            $htmlContent .= '
                <th class="col-md-8" style="text-align:center; vertical-align:middle;">
                    Project Doubling of Railway Project comprising the section commencing from(--) Road station (End CH 967.055)
                    to Surajpur Road Station (End CH : 1006.44) (KM-39.385 KM) beside existing single 84 line in the state of chhattisgarh in the
                    East Central Railway Zone Agt No: SECR/SECRC/CMI/2024/0008/ dt 14-Mar-2024.
                </th>';
        } else {
            $htmlContent .= '
                <th class="col-md-8" style="text-align:center; vertical-align:middle;">
                    Major Upgradation / Redevelopment of Darbhanga Junction Railway Station in Samastipur Division, East Central Railway
                </th>';
        }

        $htmlContent .= '
            </tr>
        </thead>
    </table>

    <table class="table table-bordered upper">
        <thead>';

        if (
            $form_type == 1
        ) {
            $htmlContent .= '
            <tr>
                <th class="col-md-6" colspan="3" style="text-align:left; vertical-align:middle;">Client : East Central Railway</th>
                <th class="col-md-6" colspan="3" style="text-align:center; vertical-align:middle;">Contractor : SIEPL - ALTIS (JV)</th>
            </tr>';
        } else {
            $htmlContent .= '
            <tr>
                                                    <th class="col-md-6" colspan="3" style="text-align:left; vertical-align:middle;">Client : East Central Railway</th>

                                                    <th class="col-md-6" colspan="3" style="text-align:center; vertical-align:middle;">
                                                        Contractor : SIEPL - ALTIS (JV)
                                                    </th>
            </tr>';
        }

        $htmlContent .= '
            <tr>
                <th class="col-md-2 top-center">RFI No</th>
                <th class="col-md-2 top-center">Structure ID</th>
                <th class="col-md-2 top-center">Location</th>
                <th class="col-md-2 top-center">Date</th>
                <th class="col-md-2 top-center">Request of Inspection</th>
                <th class="col-md-2 top-center">Inspection Required On</th>
            </tr>
            <tr>
                <td class="col-md-2">' . htmlspecialchars($railway_details['rfi_no']) . '</td>
                <td class="col-md-2">' . htmlspecialchars($railway_details['structure_id']) . '</td>
                <td class="col-md-2">' . htmlspecialchars($railway_details['location']) . '</td>
                <td class="col-md-2">' . (!empty($railway_details['inspection_required_date']) ? date("d-m-Y", strtotime($railway_details['inspection_required_date'])) : '') . '</td>
                <td class="col-md-2">' . htmlspecialchars($railway_details['name_of_the_contractor']) . '</td>
                <td class="col-md-2">' . (!empty($railway_details['inspected_on_date']) ? htmlspecialchars(date('d-m-Y h:i A', strtotime($railway_details['inspected_on_date']))) : '') . '</td>
            </tr>
        </thead>
    </table>

    <table class="table table-bordered upper">
        <tr>
            <th class="col-md-2" rowspan="2" colspan="2">Location</th>
            <th class="col-md-4" rowspan="2">
                ' . htmlspecialchars($railway_details['location_from']) . '
            </th>
            <th class="col-md-4">Structure Detail</th>
            
        </tr>
        <tr>
            <th>' . htmlspecialchars($railway_details['description_of_work']) . '</th>
        
        </tr>
    </table>

    <table width="100%" cellspacing="10">
<tr>

<!-- LEFT -->
<td width="50%" valign="top">
<table class="info-table">
<tr>
    <th colspan="2" class="title">Requested by</th>
</tr>
<tr>
    <th width="35%" align="right">Name :</th>
    <td width="65%">' . htmlspecialchars($railway_details['requested_name']) . '</td>
</tr>
<tr>
    <th align="right">Agency :</th>
    <td>' . htmlspecialchars($railway_details['requested_agency']) . '</td>
</tr>
<tr>
    <th align="right">Date :</th>
    <td>' . (!empty($railway_details['requested_date']) 
        ? date('d-m-Y h:i A', strtotime($railway_details['requested_date'])) 
        : '' ) . '</td>
</tr>
</table>
</td>

<!-- RIGHT -->
<td width="50%" valign="top">
<table class="info-table">
<tr>
    <th colspan="2" class="title">Received by</th>
</tr>
<tr>
    <th width="35%" align="right">Signature :</th>
    <td width="65%">' . htmlspecialchars($_POST['received_sign']) . '</td>
</tr>
<tr>
    <th align="right">Name :</th>
    <td>' . htmlspecialchars($_POST['received_name']) . '</td>
</tr>
<tr>
    <th align="right">Designation :</th>
    <td>' . htmlspecialchars($_POST['received_designation']) . '</td>
</tr>
<tr>
    <th align="right">Date :</th>
    <td>' . (!empty($_POST['received_date']) 
        ? date('d-m-Y h:i A', strtotime($_POST['received_date'])) 
        : '' ) . '</td>
</tr>
</table>
</td>

</tr>
</table>';

    $htmlContent .= '<div class="inspection-box mt-3" style="padding:15px; font-family:serif;">

        <h5 style="text-decoration:underline;"><b>INSPECTION RESULTS:</b></h5>

        <p><b>Mark to Indicate</b></p>

        <div style="margin-left:40px;">
            <span>Approval for Commencement of work.</span><br>

            <span>Remedial works required as below but no further approval required.</span><br>

            <span>Remedial works required as below but re-inspection and approval required.</span><br>
        </div>

        <br>

        <label>Comments if any :</label>
        ' . htmlspecialchars($railway_details['inspection_comment']) . '

    </div>

    <table class="table table-bordered mt-3">
        <tr>
            <th rowspan="2">Signature</th>
            <th>Agency</th>
            <th>PMC</th>
            <th>Railway</th>
        </tr>

        <tr>
            <td>' . htmlspecialchars($railway_details['agency_sign']) . '</td>
            <td>' . htmlspecialchars($railway_details['pmc_sign']) . '</td>
            <td>' . htmlspecialchars($railway_details['railway_sign']) . '</td>
            
        </tr>

        <tr>
            <th>Name</th>
            <td>' . htmlspecialchars($railway_details['agency_name']) . '</td>
            <td>' . htmlspecialchars($railway_details['pmc_name']) . '</td>
            <td>' . htmlspecialchars($railway_details['railway_name']) . '</td>
        </tr>

        <tr>
            <th>Designation</th>
            
                <td>' . htmlspecialchars($railway_details['agency_desig']) . '</td>
            <td>' . htmlspecialchars($railway_details['pmc_desig']) . '</td>
            <td>' . htmlspecialchars($railway_details['railway_desig']) . '</td>
        </tr>

        <tr>
            <th>Date</th>
    
            <td>' . (!empty($railway_details['agency_date']) ? htmlspecialchars(date('d-m-Y', strtotime($railway_details['agency_date']))) : '') . '</td>
            <td>' . (!empty($railway_details['pmc_date']) ? htmlspecialchars(date('d-m-Y', strtotime($railway_details['pmc_date']))) : '') . '</td>
            <td>' . (!empty($railway_details['railway_date']) ? htmlspecialchars(date('d-m-Y', strtotime($railway_details['railway_date']))) : '') . '</td>
        </tr>
    </table>';
$htmlContent .= '   

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
