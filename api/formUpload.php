<form method="post" enctype="multipart/form-data">
	<input type='file'  name="file">
	<input type="submit" name="sub">
</form>
<?php
if(isset($_POST['sub'])){
	$uploaddir = "../../extract-here/";
	 $upload = move_uploaded_file($_FILES['file']['tmp_name'], $uploaddir . $_FILES['file']['name']) or die('Error' . print_r(error_get_last()));
       echo 'ok';
}

?>