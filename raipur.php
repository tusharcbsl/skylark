<!DOCTYPE html>
<html>

<?php

require_once './loginvalidate.php';
require_once './application/config/database.php';
require_once './application/pages/function.php';
require_once './application/pages/head.php';
require_once './classes/fileManager.php';



session_start();
$success = false;
$errors = [];


// Ensure database connection
// if (!$db_con) {
//     die("Database connection failed: " . mysqli_connect_error());
// }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $details = $_POST['details'] ?? [];
    $descriptions = $_POST['description'] ?? [];
    $dept_id = $_POST['dept_id'];
    $uploadsDir = 'extract-here/';
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt','mpp', 'xer', 'zip', 'rar']; // Allowed file types


    // Validate form inputs
    if (empty($details) || empty($descriptions)) {
        $errors[] = "Please fill in all required fields.";
    } else {
        foreach ($details as $index => $detail) {
            if (empty($detail) || empty($descriptions[$index])) {
                $errors[] = "All fields must be filled.";
                break;
            }
        }
    }

    function count_pages($pdfname)
    {
        $cmd = "pdfinfo.exe";  // Windows
        exec("$cmd \"$pdfname\"", $output);
        $pagecount = 0;
        foreach ($output as $op) {
            if (preg_match("/Pages:\s*(\d+)/i", $op, $matches) === 1) {
                $pagecount = intval($matches[1]);
                break;
            }
        }
        return $pagecount;
    }
    

    date_default_timezone_set('Asia/Kolkata');
    require_once './application/PHPMailer/PHPMailerAutoload.php';
    // require_once './config.php'; // For DB connection and email constants

    //   add for upload cocument 09-06-25
    $slID = storage_letter_id;
    mysqli_set_charset($db_con, "utf8");
    $sl = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$slID'");
    $rwSl = mysqli_fetch_assoc($sl);
    $storageName = $rwSl['sl_name'];
    $storageName = str_replace(" ", "", $storageName);
    $storageName = preg_replace('/[^A-Za-z0-9\-]/', '', $storageName);
 
    $updir = getStoragePath($db_con, $rwSl['sl_parent_id'], $rwSl['sl_depth_level']);
    if (!empty($updir)) {
        $updir = $updir . '/';
    } else {
        $updir = '';
    }
    //   add for upload cocument 09-06-25

    $errors = [];
    $success = false;
    // $uploadsDir = './uploads/';
    $uploadsDir = $updir . $storageName.'/';
    $target_path = 'extract-here/' . $uploadsDir;
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt','mpp', 'xer', 'zip', 'rar'];

    // $projectName = "Document Management System";

    // add for upload cocument 09-06-25

    if (!is_dir("uploadLogs")) {
        mkdir("uploadLogs", 0777, true);
    }
    $logs = fopen('uploadLogs/' . date('Ymdhis') . '.dat', "a");
    // end for upload cocument 09-06-25

    // Simulated input
    $dept_id = $_POST['dept_id'] ?? '';
    $details = $_POST['details'] ?? [];
    $descriptions = $_POST['description'] ?? [];
  
    if(!empty($dept_id)){

        if (empty($errors)) {
            if (!is_dir($target_path)) {
                mkdir($target_path, 0777, true);
            }

            // Insert data and handle file uploads
            foreach ($details as $index => $detail) {
                $desc = $descriptions[$index] ?? '';
                $file = NULL;
                $doc_size = 0;
                $filePath = "";

                // Handle file upload
                if (!empty($_FILES['attachments']['name'][$index])) {
                    $fileName = $_FILES['attachments']['name'][$index];
                    $fileTmpName = $_FILES['attachments']['tmp_name'][$index];
                    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

                    // Validate file type
                    if (!in_array($fileExtension, $allowedExtensions)) {
                        $errors[] = "Invalid file type for: " . $fileName . ". Only JPG, PNG, PDF, MPP, XER, ZIP and RAR are allowed.";
                        continue;
                    }

                    $filePath = $target_path . basename($fileName);

                    // if (!move_uploaded_file($fileTmpName, $filePath)) {
                    //     $errors[] = "File upload failed for: " . $fileName;
                    //     continue;
                    // }

                    $fileUpload = move_uploaded_file($fileTmpName, $filePath) or die('File Not Uploaded' . print_r(error_get_last()));
                    $file = $filePath;
                    $doc_size = filesize($file);
                      if ($fileUpload) {
                        $sourcePath[] = $filePath;
                        $destinationPath[] = 'DMS/' . ROOT_FTP_FOLDER . '/' . $uploadsDir . basename($file);

                    }
                }

                $noofpages = ($fileExtension === 'pdf') ? count_pages($file) : 1;
                $name = basename($file);
                $doc_path = $uploadsDir.basename($file);

                // Get storage level ID
                $query = mysqli_query($db_con, "SELECT * FROM tbl_storage_level WHERE sl_parent_id='0'");
                if (!$query) {
                    $errors[] = "Error fetching storage level: " . mysqli_error($db_con);
                    continue;
                }
                $data = mysqli_fetch_assoc($query);
                if (!$data) {
                    $errors[] = "Storage level ID not found!";
                    continue;
                }
                $sl_id = $data['sl_id'];

                $sl_folder_idNew= storage_letter_id;

                $sl_idNew= $sl_folder_idNew;
                $flag_folder ='1';
                
                $sent_by = $_SESSION['cdes_user_id'];
                

                // **Check for duplicate entry**
                $checkQuery = "SELECT COUNT(*) as count FROM tbl_document_master WHERE letter_no = ? AND doc_path = ?";
                $stmt = $db_con->prepare($checkQuery);
                if (!$stmt) {
                    $errors[] = "SQL Prepare Error: " . $db_con->error;
                    continue;
                }
                $stmt->bind_param("ss", $detail, $name);
                $stmt->execute();
                $result = $stmt->get_result();
                $row = $result->fetch_assoc();
                $stmt->close();

                if ($row['count'] > 0) {
                    $errors[] = "Duplicate entry found for Letter No: $detail and File: $name.";
                    continue;
                }

                // Insert into database
                $insertStmt = $db_con->prepare("INSERT INTO tbl_document_master (doc_extn, doc_name, letter_no, description, noofpages, doc_size, doc_path, uploaded_by, old_doc_name, filename, dept_id, flag_folder, sent_by, railwayset) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,1)");
                if (!$insertStmt) {
                    $errors[] = "SQL Prepare Error (Insert): " . $db_con->error;
                    continue;
                }
                $insertStmt->bind_param("sssssssssssss", $fileExtension, $sl_idNew, $detail, $desc, $noofpages, $doc_size, $doc_path, $_SESSION['cdes_user_id'], $name, $name, $dept_id, $flag_folder, $sent_by);

                if (!$insertStmt->execute()) {
                    $errors[] = "Database Insert Error: " . $insertStmt->error;
                } else {
                    $uploadedData[] = [
                        'letter_no' => $detail,
                        'description' => $desc,
                        'file_path' => $name,
                        'pages' => $noofpages,
                        // 'size' => $doc_size,
                        'uploaded_by' => $_SESSION['cdes_user_id'],
                    ];
                    $success = true;
                }

                $insertStmt->close();
            }

            $emailList = [];
            
            $result = mysqli_query($db_con, "SELECT user_email_id,user_id FROM tbl_user_master WHERE FIND_IN_SET($dept_id, dept_id) > 0  AND active_inactive_users = '1'");
            while ($row = mysqli_fetch_assoc($result)) {
                if (filter_var($row['user_email_id'], FILTER_VALIDATE_EMAIL)) {
                    $emailList[] = $row['user_email_id'];
                }
            }
            if ($success && count($uploadedData) > 0 && count($emailList) > 0) {
                $msgbody = '
                    <!DOCTYPE html>
                    <html>
                    <head>
                        <style>
                            table {
                                border-collapse: collapse;
                                width: 100%;
                                font-family: Arial, sans-serif;
                            }
                            th, td {
                                border: 1px solid #999;
                                padding: 8px;
                                text-align: left;
                            }
                            th {
                                background-color: #f2f2f2;
                            }
                            h3 {
                                font-family: Arial, sans-serif;
                                color: #333;
                            }
                            a {
                                color: #1a73e8;
                                text-decoration: none;
                            }
                        </style>
                    </head>
                    <body>
                        <p>Dear Sir/Madam,</p>
                        <p>Please find below the details of the newly uploaded letters:</p>

                        <table>
                            <tr>
                                <th>Letter No</th>
                                <th>Description</th>
                                <th>File</th>
                                <th>Pages</th>
                                <th>Uploaded By</th>
                            </tr>';
                foreach ($uploadedData as $doc) {
                    $letterNo = $doc['letter_no'];
                    $description = $doc['description'];
                    $pages = $doc['pages'];
                    // $size = $doc['size'];
                    $uploadedBy = $doc['uploaded_by'];
                    $fileName = $doc['file_path'];
                    $link = '<a href="storageFiles?id=' . urlencode(base64_encode($sl_idNew)) . '" target="_blank">' . $fileName . '</a>';
                    
                    $uploadedBy = mysqli_query($db_con, "Select first_name , last_name from tbl_user_master where user_id=" . $doc['uploaded_by'] . "");
                    $fetchUplodedBy = mysqli_fetch_assoc($uploadedBy);
                    $first_name = $fetchUplodedBy['first_name'];
                    $last_name = $fetchUplodedBy['last_name'];
                    $full_name = $first_name .' '.$last_name;

                    $msgbody .= "<tr>
                        <td>{$letterNo}</td>
                        <td>{$description}</td>
                        <td>{$link}</td>
                        <td>{$pages}</td>
                        <td>{$full_name}</td>
                    </tr>";
                }
                $msgbody .= '
                    </table>
                    <br><br>

                    <div class="footer">
                        Thank You,<br>
                        ' . htmlspecialchars($projectName) . ' Team
                    </div>
                </body>
                </html>';
                $mail = new PHPMailer;
                $mail->isSMTP();
                $mail->SMTPDebug = 0;
                $mail->Host = EMAIL_HOST;
                $mail->Port = EMAIL_PORT;
                $mail->SMTPAuth = true;
                $mail->SMTPOptions = ['ssl' => ['verify_peer' => false, 'verify_peer_name' => false, 'allow_self_signed' => true]];
                $mail->Username = EMAIL_USERNAME;
                $mail->Password = EMAIL_PASSWORD;
                $mail->setFrom(EMAIL_SETFROM, 'System');

                foreach ($emailList as $email) {
                    $mail->addAddress($email);
                }

                $mail->Subject = "New Letter Submission Details";
                $mail->msgHTML($msgbody);
                $mail->AltBody = 'New Letter Submission Details';
                $mail->CharSet = 'UTF-8';

                if (!$mail->send()) {
                    $errors[] = "Mail send failed: " . $mail->ErrorInfo;
                }
            }
        }
    }else{
       $errors[] = "Department is required.";
    }
    if ($fileUpload) {
        if (FTP_ENABLED) {
            $ftp = new ftp();
            $ftp->conn("$fileserver", "$port", "$ftpUser", "$ftpPwd");

            foreach ($sourcePath as $key => $value) {

                if ($ftp->put($destinationPath[$key], $sourcePath[$key])) {
                    unlink($sourcePath[$key]);
                    // if (!in_array($sourcePath[$key], $fpathwithname)) {

                    //     unlink($sourcePath[$key]);
                    // } else {
                    //     //decrypt_my_file($sourcePath[$key]);
                    // }
                }

            }$uploadftp = 1;
            $ftp->closeConn();

        }
    }


}


?>

<style>
    .form-control {
        border: 1px solid #464040 !important;
    }

    table {
        border-collapse: collapse;
        width: 100%;
    }

    /* th {
        background-color: #009688 !important;
        color: #fff;
    } */

    th,
    td {
        border: 1px solid black;
        padding: 8px;
        text-align: left;
    }



    .actions {
        text-align: right;
    }
</style>

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
                                    <a href="raipur.php">Letter Submission Form</a>
                                </li>
                                <li> <a href="#" data-toggle="modal" data-target="#help-modal" id="helpview" data="33" title="<?= $lang['help']; ?>"><i class="fa fa-question-circle"></i> </a></li>
                                <a href="javascript:void(0)" class="btn btn-primary waves-effect waves-light pull-right btn-sm margin-t-9" onclick="goPrevious();" title="<?php echo $lang['Go_to_previous_page']; ?>"><i class="fa fa-arrow-circle-left"></i></a>
                            </ol>
                        </div>
                    </div>
                    <div class="box box-primary">
                        <div class="panel">
                            <div class="panel-body">
                                <!-- **Success Message (disappears in 10 sec, with close button)** -->
                                <?php if ($success): ?>
                                    <div class="alert alert-success alert-dismissible success-message" role="alert">
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                        Data Submitted Successfully!
                                    </div>
                                    <script>
                                        setTimeout(function() {
                                            window.location.href = "raipur.php";
                                        }, 10000); // Redirect after 10 seconds
                                    </script>
                                <?php endif; ?>

                                <!-- **Error Messages (inside panel body, does not disappear, with close button)** -->
                                <?php if (!empty($errors)): ?>
                                    <div class="alert alert-danger alert-dismissible" role="alert">
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                        Error: <?php echo implode('<br>', $errors); ?> <!-- Show error messages -->
                                    </div>
                                    <script>
                                        setTimeout(function() {
                                            window.location.href = "raipur.php";
                                        }, 10000); // Redirect after 10 seconds
                                    </script>
                                <?php endif; ?>

                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th class="col-md-2" rowspan="2" style="vertical-align:middle; text-align:center;">
                                                <img src="assets/images/rail.JPEG" class="south_railway" alt="Italian Trulli">
                                            </th>
                                            <th class="col-md-8" style="text-align:center; font-size:large; vertical-align:middle;">
                                                <u>Letter Submission Form</u>
                                            </th>
                                            <?php if ($rwgetWorkflwIdDs['form_type'] == 1) { ?>
                                                <th class="col-md-2" rowspan="2" style="vertical-align:middle; text-align:center;">
                                                    <img src="assets/images/pra1.JPEG" class="south_railway" alt="Italian Trulli">
                                                </th>
                                            <?php } else { ?>
                                                <th class="col-md-2" rowspan="2" style="vertical-align:middle; text-align:center;">
                                                    <img src="assets/images/raipur.jpg" class="south_railway" alt="Italian Trulli">
                                                </th>
                                            <?php } ?>
                                        </tr>
                                        <tr>
                                            <?php if ($rwgetWorkflwIdDs['form_type'] == 1) { ?>
                                                <th class="col-md-8" style="vertical-align:middle; text-align:center;">
                                                    Major Upgradation / Redevelopment of Darbhanga Junction Railway Station in Samastipur Division, East Central Railway
                                                </th>
                                            <?php } else { ?>
                                                <th class="col-md-8" style="vertical-align:middle; text-align:center;">
                                                     Major Upgradation / Redevelopment of Darbhanga Junction Railway Station in Samastipur Division, East Central Railway
                                                </th>
                                            <?php } ?>

                                        </tr>
                                    </thead>
                                </table>
                               


                                <form method="POST" enctype="multipart/form-data">
                                     <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="reporting"><?php echo $lang["select_department"]; ?></label>
                                            <select class="form-control select2" name="dept_id"   parsley-trigger="change" id="dept_id" required>
                                                <option value="" disabled selected><?php echo $lang["select_department"]; ?></option>
                                                <?php
                                                    $dept_data = mysqli_query($db_con, "SELECT * FROM tbl_department");
                                                    while ($row = mysqli_fetch_assoc($dept_data)) {
                                                        echo '<option value="' . $row['id'] . '">' . $row['department_name'] . '</option>';
                                                    }
                                                ?>
                                            </select>

                                        </div>
                                    </div>
                                    <table id="detailsTable">
                                        <tr>
                                            <th>S. No.</th>
                                            <th>Letter No./Document No./Ref</th>
                                            <th>Description of Document</th>
                                            <th>Upload File (images, pdf, doc, xls, mpp, xer, zip, rar)</th>
                                            <th class="actions">Actions</th>
                                        </tr>
                                        <tr>
                                            <td>1</td>
                                            <td><input  placeholder="Letter No./Document No./Ref" class="form-control" type="text" name="details[]" required></td>
                                            <td><input placeholder="Description of Document" class="form-control" type="text" name="description[]" required></td>
                                            <td><input class="form-control" type="file" name="attachments[]" required>
                                            

                                            </td>

                                            <td class="actions">
                                                <button type="button" class="btn btn-primary" onclick="addRow()">+</button>
                                                <button type="button" class="btn btn-danger" onclick="removeRow(this)" style="display:none;">-</button>
                                            </td>
                                        </tr>
                                    </table>
                                    <br>
                                    <input type="submit" value="Submit" class="btn btn-primary">
                                    <!-- <a href="raipur_report.php" class="btn btn-primary" target="_blank" title="Click and See Raipur Report">View Submitted Reports</a> -->
                                </form>


                            </div>
                        </div> <!-- end Panel -->
                    </div>

                </div> <!-- content -->

                <?php require_once './application/pages/footer.php'; ?>
            </div>
            <!-- Right Sidebar -->
            <?php //require_once './application/pages/rightSidebar.php';       
            ?>
        </div>

        <!-- END wrapper -->
        <?php require_once './application/pages/footerForjs.php'; ?>
        <script type="text/javascript" src="assets/plugins/parsleyjs/parsley.min.js"></script>

        <script type="text/javascript" src="assets/multi_function_script.js"></script>
        <!-- **JavaScript to remove success message after 10 seconds** -->
        <script>
            setTimeout(function() {
                var successMessage = document.querySelector('.success-message');
                if (successMessage) {
                    successMessage.style.display = 'none';
                }
            }, 10000); // 10 seconds
        </script>
        
        <script>
            function updateDeptInRows() {
                const deptId = document.getElementById('dept_id').value;
                document.querySelectorAll('.row-dept-id').forEach(input => {
                    input.value = deptId;
                });
            }

            function addRow() {
                let table = document.getElementById("detailsTable");
                let rowCount = table.rows.length;
                let row = table.insertRow();
                const deptId = document.getElementById('dept_id').value
                row.innerHTML = `
                <td>${rowCount}</td>
                <td><input type="text" placeholder="Letter No./Document No./Ref" class="form-control" name="details[]" required></td>
                <td><input type="text" placeholder="Description of Document" class="form-control"  name="description[]" required></td>
                <td><input type="file" class="form-control" name="attachments[]" required></td>
                <td class="actions">
                    <button type="button" class="btn btn-primary" onclick="addRow()">+</button>
                    <button type="button" class="btn btn-danger" onclick="removeRow(this)" style="display:none;">-</button>
                </td>
            `;
                updateDeptInRows();
                updateRemoveButtons();
            }

            function removeRow(button) {
                let row = button.parentNode.parentNode;
                row.parentNode.removeChild(row);
                updateSerialNumbers();
                updateRemoveButtons();
            }

            function updateSerialNumbers() {
                let rows = document.querySelectorAll("#detailsTable tr");
                for (let i = 1; i < rows.length; i++) {
                    rows[i].cells[0].innerText = i;
                }
            }

            function updateRemoveButtons() {
                let rows = document.querySelectorAll("#detailsTable tr");
                let removeButtons = document.querySelectorAll("#detailsTable button[onclick='removeRow(this)']");
                removeButtons.forEach(btn => btn.style.display = rows.length > 2 ? "inline" : "none");
            }
            document.addEventListener("DOMContentLoaded", updateRemoveButtons);

            // Show success alert if submission was successful
            <?php if ($success) { ?>
                // window.onload = function() {
                //     alert("Submitted Successfully");
                //     window.location.href = "raipur.php";
                // };
            <?php } ?>
        </script>



</body>

</html>