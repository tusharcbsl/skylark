<?php

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database connection - NO LOGIN REQUIRED
require_once './application/config/database.php';
require_once './application/pages/function.php';
require_once __DIR__ . '/vendor/autoload.php';

// Get parameters
$ticket = isset($_REQUEST['ticket']) ? $_REQUEST['ticket'] : null;
$ctaskOrder = isset($_REQUEST['ctaskOrder']) ? intval($_REQUEST['ctaskOrder']) : null;
$docID = isset($_REQUEST['docID']) ? $_REQUEST['docID'] : null;
$assignBy = isset($_REQUEST['assignBy']) ? $_REQUEST['assignBy'] : null;
$ctaskID = isset($_REQUEST['ctaskID']) ? $_REQUEST['ctaskID'] : null;
$stepId = isset($_REQUEST['stepId']) ? $_REQUEST['stepId'] : null;
$wfid = isset($_REQUEST['wfid']) ? $_REQUEST['wfid'] : null;

// Validate ticket
if (empty($ticket)) {
    error_log('approvalWorker.php ERROR: Empty ticket');
    http_response_code(400);
    exit('Invalid ticket');
}

// Get railway master data
$railway_master = "SELECT * FROM tbl_railway_master where ticket_id='$ticket'";
$railway_query = mysqli_query($db_con, $railway_master);
$railway_details = array();
while ($allot_row = mysqli_fetch_assoc($railway_query)) {
    $railway_details = $allot_row;
}

$document_master = "SELECT * FROM tbl_document_master where ticket_id='$ticket'";
    $document_query = mysqli_query($db_con, $document_master);
    while ($document_row = mysqli_fetch_assoc($document_query)) {
        $document_details = $document_row;
    }


if (empty($railway_details)) {
    error_log('approvalWorker.php ERROR: Railway details not found for ticket: ' . $ticket);
    http_response_code(404);
    exit('Railway details not found');
}

// Initialize approval data based on task order
if ($ctaskOrder == '1') {
    $agency_sign = isset($_SESSION['admin_user_name']) ? $_SESSION['admin_user_name'] . ' ' . $_SESSION['admin_user_last'] : 'Agency';
    $agency_name = isset($_SESSION['admin_user_name']) ? $_SESSION['admin_user_name'] . ' ' . $_SESSION['admin_user_last'] : 'Agency';
    $agency_desig = isset($_SESSION['designation']) ? $_SESSION['designation'] : 'Designation';
    $agency_date = date('Y-m-d H:i:s');

    $pmc_sign = $railway_details['pmc_sign'] ?? '';
    $pmc_name = $railway_details['pmc_name'] ?? '';
    $pmc_desig = $railway_details['pmc_desig'] ?? '';
    $pmc_date = !empty($railway_details['pmc_date']) ? $railway_details['pmc_date'] : '';

    $railway_sign = $railway_details['railway_sign'] ?? '';
    $railway_name = $railway_details['railway_name'] ?? '';
    $railway_desig = $railway_details['railway_desig'] ?? '';
    $railway_date = !empty($railway_details['railway_date']) ? $railway_details['railway_date'] : '';
} else if ($ctaskOrder == '2') {
    $agency_sign = $railway_details['agency_sign'] ?? '';
    $agency_name = $railway_details['agency_name'] ?? '';
    $agency_desig = $railway_details['agency_desig'] ?? '';
    $agency_date = !empty($railway_details['agency_date']) ? $railway_details['agency_date'] : '';

    $pmc_sign = isset($_SESSION['admin_user_name']) ? $_SESSION['admin_user_name'] . ' ' . $_SESSION['admin_user_last'] : 'PMC';
    $pmc_name = isset($_SESSION['admin_user_name']) ? $_SESSION['admin_user_name'] . ' ' . $_SESSION['admin_user_last'] : 'PMC';
    $pmc_desig = isset($_SESSION['designation']) ? $_SESSION['designation'] : 'Designation';
    $pmc_date = date('Y-m-d H:i:s');

    $railway_sign = $railway_details['railway_sign'] ?? '';
    $railway_name = $railway_details['railway_name'] ?? '';
    $railway_desig = $railway_details['railway_desig'] ?? '';
    $railway_date = !empty($railway_details['railway_date']) ? $railway_details['railway_date'] : '';
} else if ($ctaskOrder == '3') {
    $agency_sign = $railway_details['agency_sign'] ?? '';
    $agency_name = $railway_details['agency_name'] ?? '';
    $agency_desig = $railway_details['agency_desig'] ?? '';
    $agency_date = !empty($railway_details['agency_date']) ? $railway_details['agency_date'] : '';

    $pmc_sign = $railway_details['pmc_sign'] ?? '';
    $pmc_name = $railway_details['pmc_name'] ?? '';
    $pmc_desig = $railway_details['pmc_desig'] ?? '';
    $pmc_date = !empty($railway_details['pmc_date']) ? $railway_details['pmc_date'] : '';

    $railway_sign = isset($_SESSION['admin_user_name']) ? $_SESSION['admin_user_name'] . ' ' . $_SESSION['admin_user_last'] : 'Railway';
    $railway_name = isset($_SESSION['admin_user_name']) ? $_SESSION['admin_user_name'] . ' ' . $_SESSION['admin_user_last'] : 'Railway';
    $railway_desig = isset($_SESSION['designation']) ? $_SESSION['designation'] : 'Designation';
    $railway_date = date('Y-m-d H:i:s');
}

// Update railway master
$updated_by = isset($_SESSION['cdes_user_id']) ? $_SESSION['cdes_user_id'] : null;
$updated_at = date('Y-m-d H:i:s');

$query = "UPDATE tbl_railway_master SET 
    agency_sign = '$agency_sign', 
    agency_name = '$agency_name', 
    agency_desig = '$agency_desig', 
    agency_date = '$agency_date', 
    pmc_sign = '$pmc_sign', 
    pmc_name = '$pmc_name', 
    pmc_desig = '$pmc_desig', 
    pmc_date = '$pmc_date', 
    railway_sign = '$railway_sign', 
    railway_name = '$railway_name', 
    railway_desig = '$railway_desig', 
    railway_date = '$railway_date', 
    updated_by = $updated_by, 
    status = 1 
    WHERE ticket_id = '$ticket'";

$result = mysqli_query($db_con, $query);

if ($result) {
    // Define form_type before using it
    $form_type = 1;
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
                    <img src="assets/images/ecr.png" class="south_railway" alt="Contractor Logo">
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
                    Major Upgradation / Redevelopment of Darbhanga Junction Railway Station in Samastipur Division, East Central Railway
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
        : '') . '</td>
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
    <td width="65%">' . htmlspecialchars($railway_details['received_sign']) . '</td>
</tr>
<tr>
    <th align="right">Name :</th>
    <td>' . htmlspecialchars($railway_details['received_name']) . '</td>
</tr>
<tr>
    <th align="right">Designation :</th>
    <td>' . htmlspecialchars($railway_details['received_designation']) . '</td>
</tr>
<tr>
    <th align="right">Date :</th>
    <td>' . (!empty($railway_details['received_date'])
        ? date('d-m-Y h:i A', strtotime($railway_details['received_date']))
        : '') . '</td>
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
            <td>' . htmlspecialchars($agency_sign) . '</td>
            <td>' . htmlspecialchars($pmc_sign) . '</td>
            <td>' . htmlspecialchars($railway_sign) . '</td>
            
        </tr>

        <tr>
            <th>Name</th>
            <td>' . htmlspecialchars($agency_name) . '</td>
            <td>' . htmlspecialchars($pmc_name) . '</td>
            <td>' . htmlspecialchars($railway_name) . '</td>
        </tr>

        <tr>
            <th>Designation</th>
            
                <td>' . htmlspecialchars($agency_desig) . '</td>
            <td>' . htmlspecialchars($pmc_desig) . '</td>
            <td>' . htmlspecialchars($railway_desig) . '</td>
        </tr>

        <tr>
            <th>Date</th>

            <td>' . (
                (!empty($agency_date) && 
                $agency_date != '0000-00-00' && 
                $agency_date != '0000-00-00 00:00:00')
                ? htmlspecialchars(date('d-m-Y', strtotime($agency_date)))
                : ''
            ) . '</td>

            <td>' . (
                (!empty($pmc_date) && 
                $pmc_date != '0000-00-00' && 
                $pmc_date != '0000-00-00 00:00:00')
                ? htmlspecialchars(date('d-m-Y', strtotime($pmc_date)))
                : ''
            ) . '</td>

            <td>' . (
                (!empty($railway_date) && 
                $railway_date != '0000-00-00' && 
                $railway_date != '0000-00-00 00:00:00')
                ? htmlspecialchars(date('d-m-Y', strtotime($railway_date)))
                : ''
            ) . '</td>
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

    // Generate PDF using exportpdf.php (same as rfi_form.php)
    include 'exportpdf.php';
    $path = 'extract-here/' . $document_details['doc_path'];
    exportPDF($htmlContent, $path);


    http_response_code(200);
    echo 'OK';
} else {
    error_log('approvalWorker.php ERROR: ' . mysqli_error($db_con));
    http_response_code(500);
    echo 'Database error: ' . mysqli_error($db_con);
}

mysqli_close($db_con);
