















<pre>
<label>OCR Report</label></br>

<table border="1">
    <tr>
        <th>Database Name</th>
        <th>Domain Name</th>
        <th>Total Files</th>
        <th>OCR Done Files</th>
        <th>OCR Incomplete Files</th>
    </tr>
        <?php
        error_reporting(E_ALL);

        //require '../loginvalidate.php';
        require './application/config/database.php';

        $mainDbName = 'ezeefile_saas';

        $db_con = @mysqli_connect($dbHost, $dbUser, $dbPwd, $mainDbName) OR die('could not connect1:' . mysqli_connect_error());
        $time = time();
        $getDB = mysqli_query($db_con, "SELECT * FROM `tbl_client_master` where valid_upto > '$time' and db_name not in ('DMS_C_N_Patel___Company_1578301300','DMS_Casa__Stays_Pvt_Ltd_1590995478')") OR die('could not connect2:' . mysqli_connect_error());
        $i = 1;
        while ($rwGetdb = mysqli_fetch_assoc($getDB)) {
            $dbName = $rwGetdb['db_name'];
            $domain = $rwGetdb['subdomain'];
            if ($domain != '') {
                $db_connect = @mysqli_connect($dbHost, $dbUser, $dbPwd, $dbName);
                $checkDoc = mysqli_query($db_connect, "SELECT * FROM `tbl_document_master` where doc_extn in ('jpg', 'jpeg', 'png', 'bmp', 'pnm', 'jfif', 'jpeg', 'tiff','pdf')");
                if (!$checkDoc) {
                    continue;
                }
                $total = mysqli_num_rows($checkDoc);
                if ($total != 0) {
                    echo "<tr><td>" . $dbName . "</td>";
                    echo "<td>" . $domain . "</td>";
                    echo "<td>" . $total . "</td>";
                    $comp = 0;
                    $inc = 0;
                    while ($rwOCR = mysqli_fetch_assoc($checkDoc)) {
                        $ocr = $rwOCR['ocr'];
                        if ($ocr == 1) {
                            $comp++;
                        } else {
                            $inc++;
                        }
                    }
                    echo "<td>" . $comp . "</td>";
                    echo "<td>" . $inc . "</td>";
                }
            }
        }
        ?>
</table>
    
</pre>