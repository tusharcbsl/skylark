<!DOCTYPE html>
<html>
<head>
	<title></title>
	<link rel="apple-touch-icon" sizes="180x180" href="assets/images/favicons/apple-touch-icon.png">
    <link rel="icon" type="image/png" href="assets/images/favicons//favicon-32x32.png" sizes="32x32">
    <link rel="icon" type="image/png" href="assets/images/favicons//favicon-16x16.png" sizes="16x16">
    <link rel="manifest" href="assets/images/favicons//manifest.json">
    <link rel="mask-icon" href="assets/images/favicons//safari-pinned-tab.svg" color="#5bbad5">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>
<body>
	<?php 
		if(isset($_GET['dcid']) and $_GET['dcid']!='')
		{ 			
		$dcid = base64_decode(urldecode($_GET['dcid']));
		$perm = base64_decode(urldecode($_GET['perm']));
		$type = base64_decode(urldecode($_GET['type']));

// 		$localPath = base64_decode(urldecode($_GET['filepath']));
		if($perm=="reader")
		{
			$perm="preview";
		}
		else
		{
			$perm="edit";			
		}
		if($type=="ppt" || $type=="pptx"){
			$doc = "presentation";
		}elseif($type=="xls" || $type=="xlsx"){
			$doc = "spreadsheets";
		}elseif($type=="doc" || $type=="docx"){
			$doc = "document";
		}else{
    		header('Location: index');
		}
		?>
		<iframe src="https://docs.google.com/<?=$doc?>/d/<?=$dcid?>" style="height: 91vh;width: 100%;"></iframe>
		<SCRIPT language=JavaScript>
		 /*window.onbeforeunload = function () {
				$.post("docs-delete.php", {dcid: "<?php echo $dcid; ?>",filepath:"<?php echo './' . $localPath; ?>",perm:"<?php echo $perm; ?>"}, function (result) {
				});
				return;
			};*/
		</SCRIPT>	
		<?php 
		}
		?>
</body>
</html>