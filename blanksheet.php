<head>
    <title>Excel Viewer</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <style>

        html {
            font-family: Times New Roman;
            font-size: 9pt;
            background-color: white;
        }

        table tr td:hover{cursor:cell ; border: dotted 3px #000000 !important;} /* For all tables*/



        .table-wrapper {
            max-width: 700px !important;
            overflow: scroll !important;
        }

        table {
            position: relative !important;
            border: 1px solid #ddd !important;
            border-collapse: collapse !important;
        }

        td, th {
            white-space: nowrap !important;
            border: 1px solid #ddd !important;
            padding: 3px !important;
            min-width: 100px !important;
            max-width: 100px !important;
        }
        
        


        tbody tr td:first-of-type {
            background-color: #eee !important;
            position: sticky !important;
            left: -1px !important;
            text-align: left !important;
            max-width: 50px !important;
            min-width: 50px !important;
        }
        tbody tr:first-of-type {
            background-color: #eee !important;
            z-index: 2 !important;
            text-align: center !important;

        }


        /* Header/Logo Title */
        .header{
            background: #dad8d8;
            font-size: 10px;
            max-width:100%;
            min-width: 100%;
        }

        .container{
            max-width: 100% !important;
        }
        body{

            margin-left: 0in !important;
            margin-right: 0in !important;
            margin-top: 0in !important;
            margin-bottom: 0in !important;
        }


    </style>
    <?php
    require 'vendor/autoload.php';

    use PhpOffice\PhpSpreadsheet\Spreadsheet;
    use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

    $spreadsheet = new Spreadsheet();

    $inputFileType = $_POST['filetype'];
    $inputFileName = $_POST['filename'];

    /**  Create a new Reader of the type defined in $inputFileType  * */
    $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
    $spreadsheet = $reader->load("$inputFileName");
    /**  Advise the Reader that we only want to load cell data  * */
    $worksheet = $spreadsheet->getActiveSheet();
// Get the highest row number and column letter referenced in the worksheet
    $highestRow = $worksheet->getHighestRow(); // e.g. 10
    $highestColumn = $worksheet->getHighestColumn(); // e.g 'F'
// Increment the highest column letter
    $highestColumn++;

    /*
     * find my coloums
     */

    echo '<table border="1" class="">' . "\n";
    for ($row = 1; $row <= $highestRow; ++$row) {
        if ($row == 1) {
            echo "<tr>"
            . "<td></td>";
            for ($head = 1; $head <= 100; $head++) {
                echo "<td><b>" . getColFromNumber($head) . "</b></td>";
            }
            echo "</tr>";
        }
        echo '<tr>' . PHP_EOL;
        echo "<td><b>$row</b></td>";
        for ($col = 1; $col <= 100; $col++) {
            echo '<td contenteditable="true" data='.getColFromNumber($col).$row .'>' .
            '</td>' . PHP_EOL;
        }
        echo '</tr>' . PHP_EOL;
    }
    echo '</table>' . PHP_EOL;

    function addressbyrowcol($row, $col) {
        return getColFromNumber($col) . $row;
    }

    function getColFromNumber($num) {
        $numeric = ($num - 1) % 26;
        $letter = chr(65 + $numeric);
        $num2 = intval(($num - 1) / 26);
        if ($num2 > 0) {
            return getColFromNumber($num2) . $letter;
        } else {
            return $letter;
        }
    }
