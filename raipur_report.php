<!DOCTYPE html>
<html>


<?php

require_once './tdn-appoint.php';
$todo = new toDo();
$appoint = new appointment();

require_once './loginvalidate.php';
require_once './application/config/database.php';
require_once './application/pages/function.php';
require_once './application/pages/head.php';
require_once './classes/fileManager.php';

$search = isset($_POST['search']) ? $_POST['search'] : '';
$date1 = isset($_POST['date1']) ? $_POST['date1'] : '';
$date2 = isset($_POST['date2']) ? $_POST['date2'] : '';
// print_r($_SESSION); die();

$user_id = $_SESSION['cdes_user_id'];

$usersql = "SELECT dept_id FROM tbl_user_master WHERE user_id=$user_id";
$result = mysqli_query($db_con, $usersql);

if ($row = mysqli_fetch_assoc($result)) {
    $user_dept_id = $row['dept_id'];
}

$baseQuery = "SELECT * FROM tbl_document_master WHERE railwayset = 1";

if (isset($_GET['loc']) && $_GET['loc'] == 'sent') {
    if ($user_id != 1) {
        $baseQuery .= " AND sent_by = $user_id";
    }
} else if (isset($_GET['loc']) && $_GET['loc'] == 'inbox') {
    if ($user_id != 1) {
        $baseQuery .= " AND dept_id IN ($user_dept_id) AND sent_by != $user_id";
    }
}

// Apply search filter
if (!empty($search)) {
    $searchEsc = mysqli_real_escape_string($db_con, $search);
    $baseQuery .= " AND (
        letter_no LIKE '%$searchEsc%' OR 
        description LIKE '%$searchEsc%' OR 
        doc_path LIKE '%$searchEsc%'
    )";
}
// Apply date range filter
if (!empty($date1) && !empty($date2)) {
    $date1Esc = mysqli_real_escape_string($db_con, $date1);
    $date2Esc = mysqli_real_escape_string($db_con, $date2);
    $baseQuery .= " AND DATE(dateposted) BETWEEN '$date1Esc' AND '$date2Esc'";
}
$baseQuery .= " ORDER BY doc_id DESC";
$result = $db_con->query($baseQuery);


?>

<link href="assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
<link href="assets/plugins/bootstrap-select/css/bootstrap-select.min.css" rel="stylesheet" />
<link href="assets/plugins/multiselect/css/multi-select.css" rel="stylesheet" type="text/css" />
<link href="assets/bootstrap-datetimepicker.css" rel="stylesheet" type="text/css" />

<style>
    button.btn.btn-primary.export_button {
        margin-top: 27px !important;
        margin-left: -21px !important;
    }

    a.btn.btn-warning.reset_button {
        margin-top: 26px;
    }

    table {
        border-collapse: collapse;
        width: 100%;
    }

    button.btn.btn-primary.submit_upper {
        margin-top: 27px;
    }

    #documentTable_filter {
        float: right;
    }

    ul.pagination {
        float: right;
    }

    th,
    td {
        border: 1px solid black;
        padding: 8px;
        text-align: left;
    }
</style>

<body class="fixed-left">
    <!-- Begin page -->
    <div id="wrapper">

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
                                    <a href="raipur_report.php">Letter Submission Report</a>
                                </li>
                                <li> <a href="#" data-toggle="modal" data-target="#help-modal" id="helpview" data="33"
                                        title="<?= $lang['help']; ?>"><i class="fa fa-question-circle"></i> </a></li>
                                <a href="javascript:void(0)"
                                    class="btn btn-primary waves-effect waves-light pull-right btn-sm margin-t-9"
                                    onclick="goPrevious();" title="<?php echo $lang['Go_to_previous_page']; ?>"><i
                                        class="fa fa-arrow-circle-left"></i></a>
                            </ol>
                        </div>
                    </div>


                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card-box">
                                <form method="POST" action="">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label>Search:</label>
                                            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>"
                                                class="form-control" placeholder="Search">
                                        </div>
                                        <div class="col-md-3">
                                            <label>From Date:</label>
                                            <input type="date" class="form-control" name="date1"
                                                value="<?= htmlspecialchars($date1) ?>" />
                                        </div>
                                        <div class="col-md-3">
                                            <label>To Date:</label>
                                            <input type="date" class="form-control" name="date2"
                                                value="<?= htmlspecialchars($date2) ?>" />
                                        </div>
                                        <div class="col-md-2 d-flex align-items-end">
                                            <button type="submit" class="btn btn-primary submit_upper"><i
                                                    class="fa fa-search"></i> Search</button>
                                            <!-- <a href="raipur_report.php" type="button" class="btn btn-primary" style="margin-top: 25px;">
                                                <span class="glyphicon glyphicon-refresh"><b> Reset </b></span>
                                            </a> -->
                                            <a href="raipur_report.php" class="btn btn-warning reset_button">Reset <i
                                                    class="fa fa-refresh"></i></a>
                                        </div>
                                        <div class="col-md-1">
                                            <button type="submit" class="btn btn-primary export_button" name="export"
                                                onclick="exportToExcel()">
                                                <i class="fa fa-download"></i> Export
                                            </button>
                                        </div>


                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>


                    <div class="box box-primary">
                        <div class="panel">
                            <div class="panel-body">
                                <table id="documentTable" class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>S.N.</th>
                                            <th>Letter No./Document No./Ref</th>
                                            <th>Description of Document</th>
                                            <th>File Name (DMS Link)</th>
                                            <th>Uploaded By</th>
                                            <th>Date Submitted</th>
                                            <th><?php echo $lang['department_name']; ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $sl_id = "SELECT * FROM tbl_storage_level where sl_parent_id='0'";
                                        $query = mysqli_query($db_con, $sl_id);
                                        $data = mysqli_fetch_assoc($query);
                                        $count = 1;
                                        if ($result->num_rows > 0) {
                                            while ($row = $result->fetch_assoc()) {
                                                echo '<tr>';
                                                echo '<td>' . $count++ . '</td>';
                                                echo '<td>' . htmlspecialchars($row['letter_no']) . '</td>';
                                                echo '<td>' . htmlspecialchars($row['description']) . '</td>';

                                                echo '<td>';
                                                if (!empty($row['doc_path'])) {
                                                    $fileName = basename($row['doc_path']); // Extracts file name
                                                    echo '<a href="storageFiles?id=' . urlencode(base64_encode(storage_letter_id)) . '" target="_blank">' . htmlspecialchars($fileName) . '</a>';
                                                } else {
                                                    echo 'No File';
                                                }
                                                echo '</td>';
                                                $uploadedBy = mysqli_query($db_con, "Select first_name , last_name from tbl_user_master where user_id=" . $row['uploaded_by'] . "");
                                                $fetchUplodedBy = mysqli_fetch_assoc($uploadedBy);
                                                $first_name = $fetchUplodedBy['first_name'];
                                                $last_name = $fetchUplodedBy['last_name'];

                                                echo '<td>' . $first_name . ' ' . $last_name . '</td>';
                                                // echo '<td>' . $_SESSION['admin_user_name'] . '</td>';
                                                echo '<td>' . date('d-m-Y H:i:s', (strtotime($row['dateposted']))) . '</td>';
                                                echo '<td>';
                                                if (!empty($row['dept_id'])) {
                                                    $deptId = intval($row['dept_id']);
                                                    $dept_data = mysqli_query($db_con, "SELECT department_name FROM tbl_department WHERE id = '$deptId'");
                                                    $department = mysqli_fetch_assoc($dept_data);
                                                    echo htmlspecialchars($department['department_name'] ?? '');
                                                } else {
                                                    echo '';
                                                }
                                                echo '</td>';
                                                                                            
                                                echo '</tr>';
                                            }
                                        } else {
                                            echo '<tr><td colspan="6">No records found</td></tr>';
                                        }
                                        ?>
                                    </tbody>
                                </table>
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

        <script type="text/javascript" src="assets/js/xlsx.full.min.js"></script>



        <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.4/xlsx.full.min.js"></script> -->

        <script>
            function exportToExcel() {
                let table = document.getElementById("documentTable");
                let wb = XLSX.utils.table_to_book(table, {
                    sheet: "Document Data"
                });
                XLSX.writeFile(wb, "RPP_Report.xlsx");
            }
        </script>
        <script>
            $(document).ready(function () {
                $('#documentTable').DataTable({
                    "paging": true, // Enables pagination
                    "searching": false, // Enables search
                    "ordering": false, // Enables sorting
                    "info": true // Shows info about entries
                });
            });
        </script>
        <script>
            function addRow() {
                let table = document.getElementById("detailsTable");
                let rowCount = table.rows.length;
                let row = table.insertRow();
                row.innerHTML = `
                <td>${rowCount}</td>
                <td><input type="text" name="details[]" required></td>
                <td><input type="text" name="description[]" required></td>
                <td><input type="file" name="attachments[]" required></td>
                <td class="actions">
                    <button type="button" class="btn btn-danger" onclick="addRow()">+</button>
                    <button type="button" class="btn btn-primary" onclick="removeRow(this)" style="display:none;">-</button>
                </td>
            `;
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
            // <?php if ($success) { ?>
                //     window.onload = function() {
                //         alert("Submitted Successfully");
                //     };
                // <?php } ?>
        </script>
</body>

</html>