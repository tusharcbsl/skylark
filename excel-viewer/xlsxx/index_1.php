<?php
$path = @$_GET['file']; 
date_default_timezone_set('UTC');
require('XLSXReader.php');
 
//$xlsx = new XLSXReader('sample.xlsx');

$xlsx = new XLSXReader($path);
$sheetNames = $xlsx->getSheetNames();

?>
<!DOCTYPE html>
<html>
<head>
	<title>XLSXReader Sample</title>
	<style>
		body {
			font-family: Helvetica, sans-serif;
			font-size: 12px;
		}

		table, td {
			border: 1px solid #000;
			border-collapse: collapse;
			padding: 2px 4px;
		}
	</style>
</head>
<body>


<?//=debug($sheetNames);?>
<?php
foreach($sheetNames as $sheetName) {
	$sheet = $xlsx->getSheet($sheetName);
	?>
	<h3><?=escape($sheetName);?></h3>
	<?php
	array2Table($sheet->getData());
}

?>
<br>



<?php
$data = array_map(function($row) {
	$converted = XLSXReader::toUnixTimeStamp($row[0]);
	return array($row[0], $converted, date('c', $converted), $row[1]);
}, $xlsx->getSheetData('Dates'));
array_unshift($data, array('Excel Date', 'Unix Timestamp', 'Formatted Date', 'Data'));
array2Table($data);
?>

</body>
</html>


<?php
function array2Table($data) {
	echo '<table>';
	foreach($data as $row) {
		echo "<tr>";
		foreach($row as $cell) {
			echo "<td>" . escape($cell) . "</td>";
		}
		echo "</tr>";
	}
	echo '</table>';
}

function debug($data) {
	echo '<pre>';
	print_r($data);
	echo '</pre>';
}

function escape($string) {
	return htmlspecialchars($string, ENT_QUOTES);
}
