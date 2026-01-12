<?php
require_once '../../sessionstart.php';
if (!isset($_SESSION['cdes_user_id'])) {
    header("location:../../logout.php");
}
if (!isset($_SESSION['cdes_user_id'])) {
    header("location:../../logout.php");
}
if (isset($_SESSION['lang'])) {
    $file = "../../" . $_SESSION['lang'] . ".json";
} else {
    $file = "../../English.json";
}
$data = file_get_contents($file);
$lang = json_decode($data, true);
require_once '../config/database.php';

    $pageno = $_POST['pageno'];
	$slid=$_POST['slid'];
	//echo  $pageno;die;
    $no_of_records_per_page = 18;
    $offset = ($pageno-1) * $no_of_records_per_page;

    
			$store = mysqli_query($db_con, "select * from tbl_storage_level where sl_parent_id='$slid' and delete_status=0 order by sl_name asc limit $offset, $no_of_records_per_page");
			while ($rwStore = mysqli_fetch_assoc($store)) {
				$hasSub = mysqli_query($db_con, "select * from tbl_storage_level where sl_parent_id='$rwStore[sl_id]' and delete_status=0");
				if (mysqli_num_rows($hasSub) > 0) {


					$contFile = mysqli_query($db_con, "select sum(doc_size) as total, count(doc_name) as count from tbl_document_master where substring_index(doc_name,'_',1) in($rwStore[sl_id]) and flag_multidelete='1' ") or die('Error:' . mysqli_error($db_con));
					$rwcontFile = mysqli_fetch_assoc($contFile);
					$totalFSize = $rwcontFile['total'];
					$totalFSize = round($totalFSize / (1024 * 1024), 2);   //convert in kb
					$numFile = $rwcontFile['count'];

					echo '<a class="dropdown-toggle waves-effect waves-light col-md-2 col-lg-2 col-sm-2 col-xs-2 view1" href="storage?id=' . urlencode(base64_encode($rwStore['sl_id'])) . '"title="'.$lang['no_of_file'] .' = '. $numFile .' ' .$lang['total_size'] . ' = '. $totalFSize . $lang['MB'].'"><i class="fa fa-folder dv"></i><div style="overflow:hidden; height:25px;" title="' . $rwStore['sl_name'] . '">' . $rwStore['sl_name'] . '</div></a>';


					echo'<a class="view2" href="storage?id=' . urlencode(base64_encode($rwStore['sl_id'])) . '" title="'.$lang['no_of_file'] .' = '. $numFile .' ' .$lang['total_size'] . ' = '. $totalFSize . $lang['MB'].'" style="display: none;" ><i class="fa fa-folder-o"></i> '. $rwStore['sl_name'] .'<span class="pull-right"> '.$lang['no_of_file'] .' = '. $numFile .' ' .$lang['total_size'] . ' = '. $totalFSize . $lang['MB'].'</span></a>';

					$string = strip_tags($string);

					if (strlen($string) > 500) {

						// truncate string
						$stringCut = substr($string, 0, 500);

						// make sure it ends in a word so assassinate doesn't become ass...
						$string = substr($stringCut, 0, strrpos($stringCut, ' ')) . '... <a href="/this/story">Read More</a>';
					}
					echo $string;
					'</a></span>';
				} else {
					$file = mysqli_query($db_con, "SELECT doc_id as total from tbl_document_master where doc_name='$rwStore[sl_id]' and flag_multidelete='1'");
					if (mysqli_num_rows($file) > 0) {


						$contFile = mysqli_query($db_con, "select sum(doc_size) as total, count(doc_name) as count from tbl_document_master where substring_index(doc_name,'_',1) in($rwStore[sl_id]) and flag_multidelete='1'") or die('Error:' . mysqli_error($db_con));

						$rwcontFile = mysqli_fetch_assoc($contFile);
						$totalFSize = $rwcontFile['total'];
						$totalFSize = round($totalFSize / (1024 * 1024), 2);
						$numFile = $rwcontFile['count'];

						echo'<a class="dropdown-toggle waves-effect waves-light col-md-2 col-lg-2 col-sm-2 col-xs-2 view1" href="storage?id=' . urlencode(base64_encode($rwStore['sl_id'])) . '"title="'.$lang['no_of_file'] .' = '. $numFile .' ' .$lang['total_size'] . ' = '. $totalFSize . $lang['MB'].'"><i class="fa fa-folder dv"></i><div style="overflow:hidden; height:25px;" title="' . $rwStore['sl_name'] . '">' . $rwStore['sl_name'] . '</div></a>';

						echo'<a class="view2" href="storage?id=' . urlencode(base64_encode($rwStore['sl_id'])) . '" title="'.$lang['no_of_file'] .' = '. $numFile .' ' .$lang['total_size'] . ' = '. $totalFSize . $lang['MB'].'" style="display: none;" ><i class="fa fa-folder-o"></i> '. $rwStore['sl_name'] .'<span class="pull-right"> '.$lang['no_of_file'] .' = '. $numFile .' ' .$lang['total_size'] . ' = '. $totalFSize . $lang['MB'].'</span></a>';

					} else {
						$contFile = mysqli_query($db_con, "select sum(doc_size) as total, count(doc_name) as count from tbl_document_master where FIND_IN_SET('$rwStore[sl_id]',doc_name) and flag_multidelete='1'") or die('Error:' . mysqli_error($db_con));

						$rwcontFile = mysqli_fetch_assoc($contFile);
						$totalFSize = $rwcontFile['total'];
						$totalFSize = round($totalFSize / (1024 * 1024), 2);
						$numFile = $rwcontFile['count'];
						echo'<a class="dropdown-toggle waves-effect waves-light col-md-2 col-lg-2 col-sm-2 col-xs-2 view1" href="storage?id=' . urlencode(base64_encode($rwStore['sl_id'])) . '" title="'.$lang['no_of_file'] .' = '. $numFile .' ' .$lang['total_size'] . ' = '. $totalFSize . $lang['MB'].'"><i class="fa fa-folder-o dv"></i><div style="overflow:hidden; height:25px;" title="' . $rwStore['sl_name'] . '">' . $rwStore['sl_name'] . '</div></a>';

						echo'<a class="view2" href="storage?id=' . urlencode(base64_encode($rwStore['sl_id'])) . '" title="'.$lang['no_of_file'] .' = '. $numFile .' ' .$lang['total_size'] . ' = '. $totalFSize . $lang['MB'].'" style="display: none;" ><i class="fa fa-folder-o"></i> '. $rwStore['sl_name'] .'<span class="pull-right"> '.$lang['no_of_file'] .' = '. $numFile .' ' .$lang['total_size'] . ' = '. $totalFSize . $lang['MB'].'</span></a>';
					}
				}
			}

?>