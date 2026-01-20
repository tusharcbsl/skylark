<?php

?>
<!DOCTYPE html>
<html>
<?php
ini_set('memory_limit', '-1');
set_time_limit(0);
require_once './loginvalidate.php';
if (!$_SESSION['cdes_user_id']) {
    header('Location:index');
}
require_once './application/config/database.php';
require_once './application/pages/head.php';
require_once './application/pages/function.php';
require_once './excel-viewer/excel_reader.php';

require_once './loginvalidate.php';
require_once './application/pages/head.php';
require_once './application/pages/function.php';
require_once './classes/fileManager.php';
//error_reporting(E_ALL);
$user_id = $_SESSION['cdes_user_id'];

$ticket_id = base64_decode($_GET['id']);
$form_type = base64_decode($_GET['form_type']);



$railway_master = "SELECT * FROM tbl_railway_master where ticket_id='$ticket_id'";
$railway_query = mysqli_query($db_con, $railway_master);
$user = "SELECT * FROM tbl_user_master where user_id='$user_id'";
$user_query = mysqli_query($db_con, $user);
while ($allot_row = mysqli_fetch_assoc($railway_query)) {
    $railway_details = $allot_row;
}
while ($user_row = mysqli_fetch_assoc($user_query)) {
    $user_details = $user_row;
}

$queryString = $_SERVER["QUERY_STRING"];
// echo $rwgetRole['dashboard_mydms']; die;
if ($rwgetRole['metadata_quick_search'] != '1') {
    header('Location: ./index');
}

?>
<link href="assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
<!-- Plugin Css-->
<link rel="stylesheet" href="assets/plugins/magnific-popup/css/magnific-popup.css" />
<link rel="stylesheet" href="assets/plugins/jquery-datatables-editable/datatables.css" />
<link href="assets/plugins/datatables/dataTables.bootstrap.min.css" rel="stylesheet" type="text/css" />
<style>
    .south_railway {
        height: 85px;
    }

    table.table.table-bordered.upper {
        margin-top: -11px;
    }

    .center_content {
        text-align: center;
    }

    .top-center {
        vertical-align: top;
        text-align: center;
    }

    a.btn.btn-primary.add-btn.true_false {
        margin-top: 24px;
    }

    .remove-btn {
        margin-top: 24px;
    }

    hr {
        margin-top: 20px;
        margin-bottom: 20px;
        border: 0;
        border-top: 4px solid #193860;
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
                    <div class="bandhan_bank">
                        <div class="col-lg-12">
                            <div class="container">
                                <div class="card-box">
                                    <form method="post" action="rfi_form.php" enctype="multipart/form-data">
                                        <input type="hidden" value="<?php echo $ticket_id; ?>" name="ticket_id" />
                                        <input type="hidden" value="<?php echo $form_type; ?>" name="form_type" />

                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th class="col-md-2" rowspan="2" style="vertical-align:middle; text-align:center;">
                                                        <img src="assets/images/ecr.png" class="south_railway" alt="Italian Trulli">
                                                    </th>
                                                    <th class="col-md-8" style="text-align:center; font-size:large; vertical-align:middle;">
                                                        <u>REQUEST FOR INSPECTION (RFI)</u>
                                                    </th>
                                                    <?php if ($form_type == 1) { ?>
                                                        <th class="col-md-2" rowspan="2" style="vertical-align:middle; text-align:center;">
                                                            <img src="assets/images/skylark_logo.jpeg" class="south_railway" alt="Italian Trulli">
                                                        </th>
                                                    <?php } else {
                                                    ?>

                                                        <th class="col-md-2 text-center align-middle" rowspan="2">
                                                            <img src="assets/images/skylark_logo.jpeg"
                                                                class="img-fluid south_railway"
                                                                alt="ECR Logo">
                                                        </th>
                                                    <?php } ?>
                                                </tr>
                                                <tr>
                                                    <?php if ($form_type == 1) { ?>
                                                        <th class="col-md-8" style="vertical-align:middle; text-align:center;">
                                                            Project Doubling of Railway Project comprising the section commencing from(--) Road station (End CH 967.055)
                                                            to Surajpur Road Station (End CH : 1006.44) (KM-39.385 KM) beside existing single 84 line in the state of chhattisgarh in the
                                                            East centeral Railway Zone Agt No: SECR/SECRC/CMI/2024/0008/ dt 14-Mar-2024.
                                                        </th>
                                                    <?php } else { ?>
                                                        <th class="col-md-8" style="vertical-align:middle; text-align:center;">
                                                            Major Upgradation / Redevelopment of Darbhanga Junction Railway Station in Samastipur Division, East Central Railway </th>
                                                    <?php } ?>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                        </table>

                                        <table class="table table-bordered upper">
                                            <thead>
                                                <?php if ($rwgetWorkflwIdDs['form_type'] == 1) { ?>
                                                    <tr>
                                                        <th colspan="3" class="text-left">Client : East Central Railway</th>
                                                        <th colspan="3" class="text-center">Contractor : SIEPL - ALTIS (JV)</th>
                                                    </tr>
                                                <?php } else { ?>
                                                    <tr>
                                                        <th colspan="3" class="text-left">Client : East Central Railway</th>
                                                        <th colspan="3" class="text-center">Contractor : SIEPL - ALTIS (JV)</th>
                                                    </tr>
                                                <?php } ?>

                                                <tr class="text-center">
                                                    <th>RFI No</th>
                                                    <th>Structure ID</th>
                                                    <th>Location</th>
                                                    <th>Date</th>
                                                    <th>Request of Inspection</th>
                                                    <th>Inspection Required On</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <input type="text"
                                                            class="form-control"
                                                            name="rfi_no"
                                                            value="<?php echo $railway_details['rfi_no'] ?>" readonly>

                                                    </td>
                                                    <td>
                                                        <input type="text"
                                                            class="form-control"
                                                            name="structure_id"
                                                            placeholder="Enter Structure ID" value="<?php echo $railway_details['structure_id'] ?>" readonly>
                                                    </td>

                                                    <!-- Location -->
                                                    <td>
                                                        <input type="text"
                                                            class="form-control"
                                                            name="location"
                                                            placeholder="Enter Location"
                                                            value="<?php echo $railway_details['location'] ?>" readonly>
                                                    </td>
                                                    <td>
                                                        <input type="date"
                                                            class="form-control"
                                                            name="inspection_required_date"
                                                            value="<?php echo $railway_details['inspection_required_date'] ?>" readonly>
                                                    </td>

                                                    <!-- Request of Inspection -->
                                                    <td>
                                                        <input type="text"
                                                            class="form-control"
                                                            name="name_of_the_contractor"
                                                            placeholder="Enter Name"
                                                            value="<?php echo $railway_details['name_of_the_contractor'] ?>" readonly>
                                                    </td>

                                                    <!-- Inspection Required On -->
                                                    <td>
                                                        <input type="datetime-local"
                                                            class="form-control"
                                                            name="inspected_on_date"
                                                            value="<?= !empty($railway_details['inspected_on_date'])
                                                                        ? date('Y-m-d\TH:i', strtotime($railway_details['inspected_on_date']))
                                                                        : '' ?>"
                                                            readonly>
                                                    </td>

                                                </tr>
                                        </table>
                                        <table class="table table-bordered upper">
                                            <tr>
                                                <th rowspan="2" colspan="2" style="vertical-align: middle; text-align:center;">
                                                    Activity
                                                </th>
                                            </tr>

                                            <tr>
                                                <th rowspan="2" style="vertical-align: middle;">
                                                    <textarea class="form-control"
                                                        placeholder="Activity"
                                                        name="description_of_work"
                                                        rows="2"
                                                        readonly><?php echo $railway_details['description_of_work']; ?></textarea>
                                                </th>
                                            </tr>

                                        </table>

                                        <div class="row">

                                            <!-- Left Table -->
                                            <div class="col-md-6">
                                                <table class="table table-bordered upper">

                                                    <tr>
                                                        <th colspan="8" style="text-align:center;">Requested by</th>
                                                    </tr>
                                                    <tr>
                                                        <th colspan="2" style="text-align:right;">Name :</th>
                                                        <th colspan="3">
                                                            <input type="text" class="form-control" name="requested_name" id="requested_name" placeholder="Name" value="<?php echo $railway_details['requested_name'] ?>" readonly>
                                                        </th>
                                                    </tr>
                                                    <tr>
                                                        <th colspan="2" style="text-align:right;">Agency :</th>
                                                        <th colspan="3">
                                                            <input type="text" class="form-control" name="requested_agency" id="requested_agency" placeholder="Agency" value="<?php echo $railway_details['requested_agency'] ?>" readonly>
                                                        </th>
                                                    </tr>
                                                    <tr>
                                                        <th colspan="2" style="text-align:right;">Date :</th>
                                                        <th colspan="3">
                                                            <input type="datetime-local"
                                                                class="form-control"
                                                                name="requested_date"
                                                                id="requested_date"
                                                                value="<?= date('Y-m-d\TH:i', strtotime($railway_details['requested_date'])) ?>"
                                                                readonly>
                                                        </th>
                                                    </tr>

                                                </table>
                                            </div>
                                        </div>

                                        <div class="inspection-box mt-3" style="padding:15px; font-family:serif;">

                                            <h5 style="text-decoration:underline;"><b>INSPECTION RESULTS:</b></h5>

                                            <p><b>Mark to Indicate</b></p>

                                            <div style="margin-left:40px;">
                                                <span>Approval for Commencement of work.</span><br>

                                                <span>Remedial works required as below but no further approval required.</span><br>

                                                <span>Remedial works required as below but re-inspection and approval required.</span><br>
                                            </div>

                                            <br>

                                            <label>Comments if any :</label>
                                            <textarea class="form-control"
                                                rows="3"
                                                name="inspection_comment"
                                                readonly><?php echo htmlspecialchars($railway_details['inspection_comment']); ?></textarea>


                                        </div>

                                        <table class="table table-bordered mt-3">
                                            <tr>
                                                <th rowspan="2">Signature</th>
                                                <th>Agency</th>
                                                <th>PMC</th>
                                                <th>Railway</th>
                                            </tr>

                                            <tr>
                                                <td><input type="text" class="form-control" name="agency_sign" readonly></td>
                                                <td><input type="text" class="form-control" name="pmc_sign" readonly></td>
                                                <td><input type="text" class="form-control" name="railway_sign" readonly></td>
                                            </tr>

                                            <tr>
                                                <th>Name</th>
                                                <td><input type="text" class="form-control" name="agency_name" readonly></td>
                                                <td><input type="text" class="form-control" name="pmc_name" readonly></td>
                                                <td><input type="text" class="form-control" name="railway_name" readonly></td>
                                            </tr>

                                            <tr>
                                                <th>Designation</th>
                                                <td><input type="text" class="form-control" name="agency_desig" readonly></td>
                                                <td><input type="text" class="form-control" name="pmc_desig" readonly></td>
                                                <td><input type="text" class="form-control" name="railway_desig" readonly></td>
                                            </tr>

                                            <tr>
                                                <th>Date</th>
                                                <td><input type="date" class="form-control" name="agency_date" readonly></td>
                                                <td><input type="date" class="form-control" name="pmc_date" readonly></td>
                                                <td><input type="date" class="form-control" name="railway_date" readonly></td>
                                            </tr>
                                        </table>


                                        <div class="col-md-14">
                                            <div class="container">
                                                <h4 class="mb-3" style="font-weight: bold; text-decoration: none; font-size:15px;">Enclosures attached with RFI</h4>
                                                <div class="card-box">
                                                    <div id="dynamicForm">
                                                        <div class="row" id="formRows">
                                                            <?php
                                                            // Query to fetch data from the database
                                                            $query = "SELECT * FROM tbl_railway_attachment_master WHERE requested_id='" . $railway_details['id'] . "'";
                                                            $result = mysqli_query($db_con, $query);

                                                            // Check if the query was successful
                                                            if ($result) {
                                                                while ($row = mysqli_fetch_assoc($result)) {
                                                                    // Display each remark and file input
                                                            ?><div class="row">

                                                                        <div class="col-md-4">
                                                                            <div class="form-group">
                                                                                <label for="mobile">Remark:</label>
                                                                                <input type="text" name="remark[]" class="form-control"
                                                                                    placeholder="Enter Remark"
                                                                                    value="<?php echo htmlspecialchars($row['remark']); ?>"
                                                                                    disabled>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-4">
                                                                            <div class="form-group">
                                                                                <label for="">Upload Attachment</label>
                                                                                <input type="file" name="file[]" class="form-control"
                                                                                    value="<?php echo htmlspecialchars($row['attachment']); ?>"
                                                                                    disabled>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                            <?php
                                                                }
                                                            } else {
                                                                echo "<div class='col-md-12'><p>Error fetching data: " . mysqli_error($db_con) . "</p></div>";
                                                            }
                                                            ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                </div>
                                <div class="col-md-12 mt-6" style="margin-bottom: 25px;">
                                    <input type="hidden" name="rfi_date" value="<?php echo $railway_details['rfi_date'] ?>">
                                    <input type="submit" class="btn btn-primary" name="submit" value="Submit">
                                </div>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>

            </div> <!-- container -->



        </div> <!-- content -->
        <?php require_once './application/pages/footer.php'; ?>
    </div>

    </div>
    <!-- END wrapper -->
    <?php require_once './application/pages/footerForjs.php'; ?>

    <script src="assets/plugins/bootstrap-filestyle/js/bootstrap-filestyle.min.js" type="text/javascript"></script>
    <script src="assets/plugins/select2/js/select2.min.js" type="text/javascript"></script>
    <script type="text/javascript" src="assets/plugins/parsleyjs/parsley.min.js"></script>

    <script src="assets/multi_function_script.js"></script>
    <script src="assets/js/gs_sortable.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.getElementById('selectType').addEventListener('change', function() {
            const date1 = document.getElementById('date1');
            const date2 = document.getElementById('date2');
            const selectedType = this.value;

            if (selectedType === 'Spot') {
                // Automatically fill date2 with date1's value
                date2.value = date1.value;
                date2.setAttribute('readonly', true); // Make date2 read-only
            } else if (selectedType === 'Regular') {
                date2.removeAttribute('readonly');
                date2.value = ''; // Clear date2 for new input
            }
        });

        document.getElementById('date1').addEventListener('change', function() {
            const date2 = document.getElementById('date2');
            const selectType = document.getElementById('selectType').value;

            if (selectType === 'Spot') {
                // For Spot, ensure date2 is same as date1
                date2.value = this.value;
            } else if (selectType === 'Regular') {
                // For Regular, ensure date2 is after date1
                if (date2.value && date2.value < this.value) {
                    Swal.fire({
                        title: 'Error!',
                        text: 'End date must be after the start date.',
                        icon: 'error'
                    });
                    date2.value = ''; // Clear date2 if it's not valid
                }
            }
        });

        document.getElementById('date2').addEventListener('change', function() {
            const date1 = document.getElementById('date1').value;

            if (this.value < date1) {
                Swal.fire({
                    title: 'Error!',
                    text: 'Inspection Required On Date is not Select Grater than RFI Date  when select Regular.',
                    icon: 'error'
                });
                this.value = ''; // Clear invalid date2
            }
        });
    </script>


    <!-- range fix -->

    <script>
        document.getElementById('fromValue').addEventListener('input', function() {
            validateRange(this.value, 967.055, 1006.44);
        });

        document.getElementById('toValue').addEventListener('input', function() {
            validateRange(this.value, 967.055, 1006.44);
        });

        function validateRange(value, min, max) {
            const errorMessage = document.getElementById('errorMessage');
            if (value < min || value > max) {
                errorMessage.style.display = 'block'; // Show error message
            } else {
                errorMessage.style.display = 'none'; // Hide error message
            }
        }
    </script>


    <!-- range end -->
    <!-- <script>
        document.addEventListener("DOMContentLoaded", function() {
            const formRows = document.getElementById('formRows');

            document.getElementById('addRow').addEventListener('click', function() {
                const newRow = document.createElement('div');
                newRow.classList.add('row', 'dynamic-row');
                newRow.innerHTML = `
                <div class="col-md-12">
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
                            <label for="mobile">Remark:</label>
                            <input type="text" name="remark[]" class="form-control" placeholder="Enter Remark" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="">Upload Attachment</label>
                            <input type="file" name="file[]" class="form-control">
                        </div>
                    </div>
                </div>
                `;

                formRows.appendChild(newRow);

                newRow.querySelector('.remove-btn').addEventListener('click', function() {
                    formRows.removeChild(newRow);
                });
            });
        });
    </script> -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const formRows = document.getElementById('dynamicForm');

            document.getElementById('addRow').addEventListener('click', function() {
                const newRow = document.createElement('div');
                newRow.classList.add('row', 'dynamic-row');
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
                    <label for="mobile">Remark:</label>
                    <input type="text" name="remark[]" class="form-control" placeholder="Enter Remark" required>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="">Upload Attachment</label>
                    <input type="file" name="file[]" class="form-control">
                </div>
            </div>
        `;

                formRows.appendChild(newRow);

                newRow.querySelector('.remove-btn').addEventListener('click', function() {
                    formRows.removeChild(newRow);
                });
            });
        });
    </script>
</body>

</html>