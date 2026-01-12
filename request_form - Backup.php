<?php
// Make sure you are handling file uploads and database insertions securely.
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Database connection
    $conn = new mysqli('host', 'username', 'password', 'database');
    if ($conn->connect_error) {
        die('Database connection failed: ' . $conn->connect_error);
    }

    // Sanitize the input data
    $rfi_no = $conn->real_escape_string($_POST['rfi_no']);
    $rfi_date = $conn->real_escape_string($_POST['rfi_date']);
    $type_regular = $conn->real_escape_string($_POST['type_regular']);
    $name_of_the_contractor = $conn->real_escape_string($_POST['name_of_the_contractor']);
    $item_no_as_per = $conn->real_escape_string($_POST['item_no_as_per']);
    $inspection_required_date = $conn->real_escape_string($_POST['inspection_required_date']);
    $location_from = $conn->real_escape_string($_POST['location_from']);
    $location_to = $conn->real_escape_string($_POST['location_to']);
    // Add any other fields similarly...

    // Insert into tbl_railway_master
    $insertMasterQuery = "
        INSERT INTO tbl_railway_master (
            rfi_no, rfi_date, type_regular, name_of_the_contractor, item_no_as_per,
            inspection_required_date, location_from, location_to, 
            -- Add any other columns
            created_at, created_by
        ) VALUES (
            '$rfi_no', '$rfi_date', '$type_regular', '$name_of_the_contractor', '$item_no_as_per', 
            '$inspection_required_date', '$location_from', '$location_to', 
            -- Add other values accordingly
            NOW(), 'admin_user'
        )
    ";

    if ($conn->query($insertMasterQuery)) {
        // Get the last inserted ID for tbl_railway_master
        $lastInsertId = $conn->insert_id;

        // Handle attachments and remarks (dynamically added rows)
        if (!empty($_POST['remark']) && is_array($_POST['remark'])) {
            foreach ($_POST['remark'] as $key => $remark) {
                // Handle file upload for the corresponding remark
                if (!empty($_FILES['file']['name'][$key])) {
                    $fileName = $_FILES['file']['name'][$key];
                    $fileTmp = $_FILES['file']['tmp_name'][$key];
                    $uploadDir = 'uploads/'; // Make sure the directory exists
                    $targetFilePath = $uploadDir . basename($fileName);
                    
                    // Upload file to server
                    if (move_uploaded_file($fileTmp, $targetFilePath)) {
                        // Insert into tbl_railway_attachment_master
                        $remark = $conn->real_escape_string($remark);
                        $filePath = $conn->real_escape_string($targetFilePath);

                        $insertAttachmentQuery = "
                            INSERT INTO tbl_railway_attachment_master (
                                requested_id, remark, attachment, created_at, created_by
                            ) VALUES (
                                '$lastInsertId', '$remark', '$filePath', NOW(), 'admin_user'
                            )
                        ";

                        if (!$conn->query($insertAttachmentQuery)) {
                            echo "Error: " . $conn->error;
                        }
                    } else {
                        echo "Failed to upload file for remark: " . $remark;
                    }
                }
            }
        }
        
        echo "Data saved successfully!";
    } else {
        echo "Error: " . $conn->error;
    }

    $conn->close();
}
?>
<script>
document.getElementById('addRow').addEventListener('click', function() {
    const formRows = document.getElementById('formRows');
    const newRow = document.createElement('div');
    newRow.classList.add('row');
    newRow.innerHTML = `
        <div class="col-md-1"></div>
        <div class="col-md-1">
            <div class="mb-3">
                <a href="javascript:void(0)" class="btn btn-danger remove-btn">
                    <i class="fa fa-minus" aria-hidden="true"></i>
                </a>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="remark">Remark:</label>
                <input type="text" name="remark[]" class="form-control" placeholder="Enter Remark" required>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="attachment">Upload Attachment</label>
                <input type="file" name="file[]" class="form-control">
            </div>
        </div>
    `;
    formRows.appendChild(newRow);

    // Add event listener for remove button
    newRow.querySelector('.remove-btn').addEventListener('click', function() {
        newRow.remove();
    });
});
</script>
