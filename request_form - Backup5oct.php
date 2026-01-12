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
    <table class="table table-bordered">
        <thead>
            <tr>
                <th class="col-md-2" rowspan="2" style="vertical-align:middle; text-align:center;">
                    <img src="assets/images/rail.JPEG" class="south_railway" alt="Italian Trulli">
                </th>
                <th class="col-md-8" style="text-align:center; font-size:large; vertical-align:middle;">
                    <u>REQUEST FOR INSPECTION (RFI)</u>
                </th>
                <th class="col-md-2" rowspan="2" style="vertical-align:middle; text-align:center;">
                    <img src="assets/images/pra1.JPEG" class="south_railway" alt="Italian Trulli">
                </th>
            </tr>
            <tr>
                <th class="col-md-8" style="vertical-align:middle; text-align:center;">
                    Project Doubling of Railway Project comprising the section commencing from(--) Road station (End CH 967.055)
                    to Surajpur Road Station (End CH : 1006.44) (KM-39.385 KM) beside existing single 84 line in the state of chhattisgarh in the
                    south East centeral Railway Zone Agt No: SECR/SECRC/CMI/2024/0008/ dt 14-Mar-2024.
                </th>
                <th></th>
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
                <th class="col-md-2 top-center" style="text-align:center; vertical-align:middle;">RFI No</th>
                <th class="col-md-2 top-center" style="text-align:center; vertical-align:middle;">RFI Date</th>
                <th class="col-md-2 top-center" style="text-align:center; vertical-align:middle;">Type <br> (Regular/Spot)</th>
                <th class="col-md-2 top-center" style="text-align:center; vertical-align:middle;">Name Of the Contractor's Engineer offering the work for inspection</th>
                <th class="col-md-2 top-center" style="text-align:center; vertical-align:middle;">Item No as per contract <br> (for payment)</th>
                <th class="col-md-2 top-center" style="text-align:center; vertical-align:middle;">Inspection Required On</th>
            </tr>
        </thead>
    </table>

    <table class="table table-bordered upper">
        <tr>
            <th class="col-md-2">
                <?php
                $num = 1;
                ?>
                <input type="text" class="form-control" name="rfi_no" value="<?php echo $num; ?>" readonly>
                <?php $num++; ?>
            </th>
            <th class="col-md-2">
                <input type="date" id="date1" class="form-control" name="rfi_date" placeholder="Start" />
            </th>
            <th class="col-md-2">
                <select id="selectType" class="form-control" name="type_regular">
                    <option value="Regular">Regular</option>
                    <option value="Spot">Spot</option>
                </select>
            </th>
            <th class="col-md-2">
                <input type="text" class="form-control" placeholder="Some text value..." name="name_of_the_contractor">
            </th>
            <th class="col-md-2">
                <select class="form-control" name="item_no_as_per">
                    <option>1</option>
                    <option>2</option>
                    <option>3</option>
                    <option>4</option>
                    <option>5</option>
                </select>
            </th>
            <th class="col-md-2">
                <input type="date" id="date2" class="form-control" placeholder="End" name="inspection_required_date" />
            </th>
        </tr>
    </table>

    <table class="table table-bordered upper">
        <tr>
            <th class="col-md-2" rowspan="2" colspan="2">Location / Chainage</th>
            <th class="col-md-4" rowspan="2">
                <div class="col-md-6">
                    <input type="number" id="fromValue" class="form-control" placeholder="From (967.055)" step="0.001" name="location_from">
                </div>
                <div class="col-md-6">
                    <input type="number" id="toValue" class="form-control" placeholder="To (1006.44)" step="0.001" name="location_to">
                </div>
                <div class="col-md-12">
                    <p id="errorMessage" style="color: red; display: none;">Value must be between 967.055 and 1006.44</p>
                </div>
            </th>
            <th class="col-md-4" colspan="1" style="text-align:left;" readonly>Name Of the Inspecting Engineer</th>
            <th class="col-md-2" readonly>Inspected On</th>
        </tr>
        <tr>
            <th style="height: 40px;">
                <input type="text" class="form-control" placeholder="Some text value..." name="name_of_the_inspecting_engineer" readonly>
            </th>
            <th style="height: 40px;">
                <input type="date" class="form-control" placeholder="Start" name="inspected_on_date" readonly />
            </th>
        </tr>
    </table>

    <table class="table table-bordered upper">
        <tr>
            <th class="col-md-12" colspan="6">
                Request for Inspection of the following works, Which are /will be ready for inspection
            </th>
        </tr>
        <tr>
            <th class="col-md-3">1. C&G</th>
            <th class="col-md-3"><input type="checkbox" class="form-check-input" id="C&G" name="c_and_g" value="something"></th>
            <th class="col-md-3">8. Concreting</th>
            <th class="col-md-3"><input type="checkbox" class="form-check-input" id="concreting" name="concreting" value="something"></th>
        </tr>
        <tr>
            <th class="col-md-3">2. Earthwork</th>
            <th class="col-md-3"><input type="checkbox" class="form-check-input" id="earthwork" name="earthwork" value="something"></th>
            <th class="col-md-3">9. Drain/Retaining Wall</th>
            <th class="col-md-3"><input type="checkbox" class="form-check-input" id="drain_retaining_wall" name="drain_retaining_wall" value="something"></th>
        </tr>
        <tr>
            <th class="col-md-3">3. Blanketing</th>
            <th class="col-md-3"><input type="checkbox" class="form-check-input" id="blanketing" name="blanketing" value="something"></th>
            <th class="col-md-3">10. Roads</th>
            <th class="col-md-3"><input type="checkbox" class="form-check-input" id="roads" name="roads" value="something"></th>
        </tr>
        <tr>
            <th class="col-md-3">4. Survey</th>
            <th class="col-md-3"><input type="checkbox" class="form-check-input" id="survey" name="survey" value="something"></th>
            <th class="col-md-3">11. Utilities</th>
            <th class="col-md-3"><input type="checkbox" class="form-check-input" id="utilities" name="utilities" value="something"></th>
        </tr>
        <tr>
            <th class="col-md-3">5. Safety</th>
            <th class="col-md-3"><input type="checkbox" class="form-check-input" id="safety" name="safety" value="something"></th>
            <th class="col-md-3">12. Dismantling of Pway</th>
            <th class="col-md-3"><input type="checkbox" class="form-check-input" id="dismantling_of_Pway" name="dismantling_of_Pway" value="something"></th>
        </tr>
        <tr>
            <th class="col-md-3">6. QC/Material</th>
            <th class="col-md-3"><input type="checkbox" class="form-check-input" id="qc_material" name="qc_material" value="something"></th>
            <th class="col-md-3">13. Bridge Work</th>
            <th class="col-md-3"><input type="checkbox" class="form-check-input" id="bridge_work" name="Bridge_Work" value="something"></th>
        </tr>
        <tr>
            <th class="col-md-3">7. Shuttering/Reinforcement</th>
            <th class="col-md-3"><input type="checkbox" class="form-check-input" id="shuttering_reinforcement" name="shuttering_reinforcement" value="something"></th>
            <th class="col-md-3">14. Other</th>
            <th class="col-md-3"><input type="checkbox" class="form-check-input" id="other" name="other" value="something"></th>
        </tr>

        <tr>
            <th colspan="" style="text-align:left; vertical-align:top; height: 200px">Description of Work offered for Inspection</th>
            <th colspan="3" style="text-align:left; vertical-align:top;">
                <input type="text" style="height: 50px;" class="form-control" placeholder="Some text value..." name="description_of_work">
            </th>
        </tr>

        <tr>
            <th class="col-md-3" colspan="1" style="text-align:left; vertical-align:top; height: 100px">Enclosures attached with RFI</th>
            <th class="col-md-9" colspan="3" style="text-align:center; vertical-align:middle; height: 100px"></th>
        </tr>
        <tr>
            <th class="col-md-6" colspan="2" style="text-align:left; vertical-align:top; height: 100px">Signature of the Contractor's Representative requesting for Inspection</th>
            <th class="col-md-6" colspan="3">
                <textarea class="form-control" style="text-align:left; vertical-align:top;" name="signature_of_the_contractor" readonly><?php echo $_SESSION['admin_user_name']; ?></textarea>
            </th>
        </tr>
        <tr>
            <th class="col-md-3" colspan="4" style="text-align:left; vertical-align:top; height: 200px">Remarks of the Inspection Engineer (Representative of Authority Engineer)</th>
        </tr>
        <tr>
            <th class="col-md-3" colspan="4" style="text-align:center; vertical-align:top; height: 40px">
                Approved <input type="checkbox" class="form-check-input" id="check1" name="approved" disabled>
                / Not Approved <input type="checkbox" class="form-check-input" id="check2" name="not_approved" disabled>
            </th>
        </tr>
        <tr>
            <th colspan="2">Signature of the Inspection Engineer Representative of Authority Engineer</th>
            <th colspan="3"></th>
        </tr>
        <tr>
            <th colspan="2" style="text-align:right;">Date :</th>
            <th colspan="3"><span name="date"></span></th>
        </tr>
        <tr>
            <th colspan="2" style="text-align:right;">Name :</th>
            <th colspan="3"><span name="name"></span></th>
        </tr>
        <tr>
            <th colspan="2" style="text-align:right;">Designation :</th>
            <th colspan="3"><span name="designation"></span></th>
        </tr>
    </table>

    <div class="col-md-14">
    <div class="container">
        <div class="card-box">
            <div id="dynamicForm">
                <div class="row" id="formRows">
                    <div class="col-md-1"></div>
                    <div class="col-md-1">
                        <div class="mb-3">
                            <a href="javascript:void(0)" class="btn btn-primary add-btn true_false" id="addRow">
                                <i class="fa fa-plus" aria-hidden="true"></i>
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
            </div>
        </div>
        <div class="col-md-12 mt-6" style="margin-bottom: 25px;">
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

