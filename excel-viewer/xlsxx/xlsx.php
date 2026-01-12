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
$sheet_tabs = '<table class="table_body" name="tab_table"><tr>';
$n=0;
foreach($sheetNames as $sheetName) {
    
	$sheet_tabs.='<td class="tab_base" id="sheet_tab_'. $n .'" onclick="changeWSTabs('. $n .');">'. escape($sheetName) .'</td>';
	$n++;
}
$sheet_tabs .= '<tr></table>';
echo $sheet_tabs;
$n=0;
foreach($sheetNames as $sheetName) {
	$sheet = $xlsx->getSheet($sheetName);
        //$sheetsTabs.='<td class="tab_base" id="sheet_tab_'. $sheet .'" onclick="changeWSTabs('. $sheet .');">'. escape($sheetName) .'</td>';
	
	$sheetdata=array2Table($sheet->getData(),$n);
        
        $n++;
}

?>




<?php
$data = array_map(function($row) {
	$converted = XLSXReader::toUnixTimeStamp($row[0]);
	return array($row[0], $converted, date('c', $converted), $row[1]);
}, $xlsx->getSheetData('Dates'));
array_unshift($data, array('Excel Date', 'Unix Timestamp', 'Formatted Date', 'Data'));
//array2Table($data);

?>

</body>
</html>


<?php
function array2Table($data,$sheet) {
    echo '<div class="hide_div" id="sheet_div_'. $sheet .'">';
	echo '<table class="table_body" >';
        $i=0;
        echo '<tr><td>&nbsp;</td>';
        foreach($data[0] as $row){
                       
            echo '<td class="table_sub_heading">'.make_alpha_from_numbers($i).'</td>';
            $i++;           
        }
         echo '<tr>';
        $rn=1;
	foreach($data as $row) {
		echo "<tr>";
                echo '<td class="table_data">'.$rn.'</td>';
		foreach($row as $cell) {
			echo "<td class='table_data'>" . escape($cell) . "</td>";
		}
		echo "</tr>";
                $rn++;
	}
	echo '</table> </div>';
}
function make_alpha_from_numbers($number) {
  $numeric = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
  if($number<strlen($numeric)) return $numeric[$number];
  else {
    $dev_by = floor($number/strlen($numeric));
    return make_alpha_from_numbers($dev_by-1) . make_alpha_from_numbers($number-($dev_by*strlen($numeric)));
  }
}
function debug($data) {
	echo '<pre>';
	print_r($data);
	echo '</pre>';
}

function escape($string) {
	return htmlspecialchars($string, ENT_QUOTES);
}
?>

<style>
.table_data {
  border:2px ridge #000;
  padding:1px 3px;
}
.tab_base {
  background:#C8DaDD;
  font-weight:bold;
  border:2px ridge #000;
  cursor:pointer;
  padding: 2px 4px;
}
.table_sub_heading {
  background:#CCCCCC;
  font-weight:bold;
  border:2px ridge #000;
  text-align:center;
}
.table_body {
  background:#F0F0F0;
  font-wieght:normal;
  font-family:Calibri, sans-serif;
  font-size:16px;
  border:2px ridge #000;
  border-spacing: 0px;
  border-collapse: collapse;
}
.tab_loaded {
  background:#222222;
  color:white;
  font-weight:bold;
  border:2px groove #000;
  cursor:pointer;
}
.hide_div { display:none;}
</style>
<script>
// shows the Div with worksheet content according to clicked tab
function changeWSTabs(sheet) {
  for(i=0; i< <?php echo $n; ?>; i++) {
    document.getElementById('sheet_tab_' + i).className = 'tab_base';
    document.getElementById('sheet_div_' + i).className = 'hide_div';
  }
  document.getElementById('sheet_tab_' + sheet).className = 'tab_loaded';
  document.getElementById('sheet_div_' + sheet).className = 'show_div';
}

// displays the first sheet 
changeWSTabs(0);
</script>
