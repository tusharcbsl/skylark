<?php
require_once './application/config/database.php';
require_once './application/pages/head.php';
require_once './application/pages/function.php';
require_once './excel-viewer/excel_reader.php';

require_once './loginvalidate.php';
require_once './application/pages/head.php';
require_once './application/pages/function.php';
require_once './classes/fileManager.php';



if (isset($_POST['action']) && $_POST['action'] == "save") {
    $railway_item_no = mysqli_real_escape_string($db_con, $_POST['railway_item_no']);

    // Check if the railway_item_no already exists
    $check_sql = "SELECT * FROM tbl_railway_item_no WHERE railway_item_no = '" . $railway_item_no . "'";
    $check_query = mysqli_query($db_con, $check_sql);

    if (mysqli_num_rows($check_query) > 0) {
        // If the railway_item_no exists, show an error message
        echo "<script>
            Swal.fire({
                title: 'Error!',
                text: 'Railway item No already exists.',
                icon: 'error'
            });
        </script>";
    } else {
        // If the railway_item_no doesn't exist, insert the new record
        $sql = "INSERT INTO tbl_railway_item_no SET railway_item_no = '" . $railway_item_no . "'";
        $result = mysqli_query($db_con, $sql);

        if ($result) {
            echo "<script>
                Swal.fire({
                    title: 'Success!',
                    text: 'Railway item No saved.',
                    icon: 'success'
                });
            </script>";
        } else {
            echo "<script>
                Swal.fire({
                    title: 'Error!',
                    text: 'Failed to save Railway item No.',
                    icon: 'error'
                });
            </script>";
        }
    }
}
