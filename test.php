<?php
$new_rtf = "Manish Singh";
header("Content-type: application/msword");
header("Content-disposition: inline;      filename=joboffer.rtf");
header("Content-length: " . strlen($new_rtf));
echo $new_rtf;

?>