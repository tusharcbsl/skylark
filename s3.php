<?php
ini_set('display_errors', 1); 
ini_set('display_startup_errors', 1); 
error_reporting(E_ALL);

require 'aws/vendor/autoload.php';
use Aws\S3\S3Client;
use Aws\Exception\AwsException;
use Aws\S3\MultipartUploader;
use Aws\Exception\MultipartUploadException;


$key = 'AKIATNYF7HWZ62A7BG5O';
$secret = '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';

$s3Client = new S3Client([
    'region' => 'ap-south-1',
    'version' => 'latest',
	'credentials' => [
        'key' => $key,
        'secret' => $secret,
  ]
]);

$source = './extract-here/mani/Hydrangeas.jpg';

if(file_exists($source)){
	echo "yes";
}else{
	echo "no";
}

//die();
$dest = 's3://demobucketapl';
$manager = new \Aws\S3\Transfer($s3Client, $source, $dest);

// Initiate the transfer and get a promise
$promise = $manager->promise();

// Do something when the transfer is complete using the then() method
$promise->then(function () {
    echo 'Done!';
});

$promise->otherwise(function ($reason) {
    echo 'Transfer failed: ';
    //var_dump($reason);
});
?>


<html>
    <head><meta charset="UTF-8"></head>
    <body>
        <h1>S3 upload example</h1>
<?php
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['userfile']) && $_FILES['userfile']['error'] == UPLOAD_ERR_OK && is_uploaded_file($_FILES['userfile']['tmp_name'])) {
    // FIXME: you should add more of your own validation here, e.g. using ext/fileinfo
    try {
        // FIXME: you should not use 'name' for the upload, since that's the original filename from the user's computer - generate a random filename that you then store in your database, or similar
        $upload = $s3Client->upload('demobucketapl', $_FILES['userfile']['name'], fopen($_FILES['userfile']['tmp_name'], 'rb'), 'bucket-owner-full-control');
?>
        <p>Upload <a href="<?=htmlspecialchars($upload->get('ObjectURL'))?>">successful</a> :)</p>
<?php } catch(Exception $e) { ?>
        <p><?php echo $e->getMessage() . "\n";?></p>
<?php } } ?>
        <h2>Upload a file</h2>
        <form enctype="multipart/form-data" action="<?=$_SERVER['PHP_SELF']?>" method="POST">
            <input name="userfile" type="file"><input type="submit" value="Upload">
        </form>
    </body>
</html>
<?php
// Use multipart upload
/*  $source = './extract-here/mani/Hydrangeas.jpg';
 $uploader = new MultipartUploader($s3Client, $source, [
    'bucket' => 'demobucketapl',
    'key' => 'mani/Hydrangeas.jpg',
]);

try {
    $result = $uploader->upload();
    echo "Upload complete: {$result['ObjectURL']}\n";
} catch (MultipartUploadException $e) {
    echo $e->getMessage() . "\n";
} */ 

// download file from s3
/* try {
    // Get the object.
    $result = $s3Client->getObject([
        'Bucket' => 'demobucketapl',
        'Key'    => 'Hydrangeas.jpg'
    ]);
	
	file_put_contents('./extract-here/s3/image.jpeg', $result['Body']);
    // Display the object in the browser.
    //header("Content-Type: {$result['ContentType']}");
    //echo $result['Body'];
} catch (S3Exception $e) {
    echo $e->getMessage() . PHP_EOL;
} */

// Delete an object from the bucket.
/* $result = $s3Client->deleteObject([
    'Bucket' => 'demobucketapl',
	'Key'    => 'Hydrangeas.jpg'
]);

print_r($result); */



 

?>