<?php
// Database connection
$host = 'localhost'; // your host
$db = 'barbarik_ezeefile'; // your database name
$user = 'admin'; // your database username
$pass = 'Admin@123'; // your database password

session_start(); // Ensure session is started to access session variables

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Could not connect to the database: " . $e->getMessage());
}

// Initialize variables
$created_at = date('Y-m-d H:i:s');
$created_by = isset($_SESSION['admin_user_id']) ? $_SESSION['admin_user_id'] : null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Gather POST data for main table
    $rfi_no = $_POST['rfi_no'] ?? null;
    $rfi_date = $_POST['rfi_date'] ?? null;
    $type_regular = $_POST['type_regular'] ?? null;
    $name_of_the_contractor = $_POST['name_of_the_contractor'] ?? null;
    $item_no_as_per = $_POST['item_no_as_per'] ?? null;
    $inspection_required_date = $_POST['inspection_required_date'] ?? null;
    $location_from = $_POST['location_from'] ?? null;
    $location_to = $_POST['location_to'] ?? null;
    $name_of_the_inspecting_engineer = $_POST['name_of_the_inspecting_engineer'] ?? null;
    $inspected_on_date = $_POST['inspected_on_date'] ?? null;

    // Checkboxes
    $c_and_g = isset($_POST['c_and_g']) ? 1 : 0;
    $earthwork = isset($_POST['earthwork']) ? 1 : 0;
    $blanketing = isset($_POST['blanketing']) ? 1 : 0;
    $survey = isset($_POST['survey']) ? 1 : 0;
    $safety = isset($_POST['safety']) ? 1 : 0;
    $qc_material = isset($_POST['qc_material']) ? 1 : 0;
    $shuttering_reinforcement = isset($_POST['shuttering_reinforcement']) ? 1 : 0;
    $concreting = isset($_POST['concreting']) ? 1 : 0;
    $drain_retaining_wall = isset($_POST['drain_retaining_wall']) ? 1 : 0;
    $roads = isset($_POST['roads']) ? 1 : 0;
    $utilities = isset($_POST['utilities']) ? 1 : 0;
    $dismantling_of_Pway = isset($_POST['dismantling_of_Pway']) ? 1 : 0;
    $bridge_work = isset($_POST['Bridge_Work']) ? 1 : 0;
    $other = isset($_POST['other']) ? 1 : 0;

    $descriptio_of_work = $_POST['descriptio_of_work'] ?? null;
    $signature_of_the_contractor = $_POST['signature_of_the_contractor'] ?? null;
    $remarks_of_the_inspection = $_POST['remarks_of_the_inspection'] ?? null;
    $approved = isset($_POST['approved']) ? 1 : 0;
    $not_approved = isset($_POST['not_approved']) ? 1 : 0;

    // Prepare and execute the first insert query
    try {
        $stmt = $pdo->prepare("INSERT INTO tbl_railway_master (rfi_no, rfi_date, type_regular, name_of_the_contractor, item_no_as_per, inspection_required_date, location_from, location_to, name_of_the_inspecting_engineer, inspected_on_date, c_and_g, earthwork, blanketing, survey, safety, qc_material, shuttering_reinforcement, concreting, drain_retaining_wall, roads, utilities, dismantling_of_Pway, bridge_work, other, description_of_work, signature_of_the_contractor, remarks_of_the_inspection, approved, not_approved, created_at, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $stmt->execute([$rfi_no, $rfi_date, $type_regular, $name_of_the_contractor, $item_no_as_per, 
                         $inspection_required_date, $location_from, $location_to, 
                         $name_of_the_inspecting_engineer, $inspected_on_date, 
                         $c_and_g, $earthwork, $blanketing, $survey, $safety, 
                         $qc_material, $shuttering_reinforcement, $concreting, 
                         $drain_retaining_wall, $roads, $utilities, 
                         $dismantling_of_Pway, $bridge_work, $other, 
                         $descriptio_of_work, $signature_of_the_contractor, 
                         $remarks_of_the_inspection, $approved, $not_approved, 
                         $created_at, $created_by]);

        $lastInsertId = $pdo->lastInsertId();

        // Handle file uploads for tbl_railway_attachment_master
        if (isset($_FILES['file'])) {
            $files = $_FILES['file'];
            $remarks = $_POST['remark'];

            for ($i = 0; $i < count($files['name']); $i++) {
                if ($files['error'][$i] === UPLOAD_ERR_OK) {
                    $tmpName = $files['tmp_name'][$i];
                    $name = basename($files['name'][$i]);
                    $uploadDir = 'uploads/'; // Ensure this directory exists and is writable
                    $uploadFile = $uploadDir . $name;

                    if (move_uploaded_file($tmpName, $uploadFile)) {
                        // Insert into tbl_railway_attachment_master
                        $attachmentStmt = $pdo->prepare("INSERT INTO tbl_railway_attachment_master (requested_id, remark, attachment, created_at, created_by) VALUES (?, ?, ?, ?, ?)");
                        $attachmentStmt->execute([$lastInsertId, $remarks[$i] ?? null, $uploadFile, $created_at, $created_by]);
                    }
                }
            }
        }

        echo "Data successfully saved!";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
