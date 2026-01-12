<?php
require_once '../../sessionstart.php';
require_once '../pages/function.php';
//require_once '../../loginvalidate.php';
if (!isset($_SESSION['cdes_user_id'])) {
    header("location:../../logout");
}
require './../config/database.php';

//for user role
$chekUsr = mysqli_query($db_con, "select * from tbl_bridge_role_to_um tbr inner join tbl_user_roles tur on tbr.role_id = tur.role_id where FIND_IN_SET('$_SESSION[cdes_user_id]', user_ids) > 0") or die('Error:' . mysqli_error($db_con));
$rwgetRole = mysqli_fetch_assoc($chekUsr);

if ($rwgetRole['edit_email_credential'] != '1') {
    header('Location: ../../index');
}
?>
<?php
if (isset($_SESSION['lang'])) {
    $file = "../../" . $_SESSION['lang'] . ".json";
} else {
    $file = "../../English.json";
}
//for user role
$data = file_get_contents($file);
$lang = json_decode($data, true);

$id = preg_replace("/[^0-9]/", "", $_POST['ID']);
$retval = mysqli_query($db_con, "SELECT * FROM `tbl_file_server_details` WHERE id='$id'") or die('Could not get data: ' . mysqli_error($db_con));
$rwgetdata = mysqli_fetch_assoc($retval);

$secret_access_key = explode(",", $rwgetdata['secret_access_key']);
$secret_access_key = ezeefile_crypt($secret_access_key[0], 'd').ezeefile_crypt($secret_access_key[1], 'd');
?>

<div class="row">
    <div class="col-sm-12">
		<div class="form-group">
			<label class="text-weight"> <?= $lang['fileserver_type']; ?><span class="text-alert">*</span></label>
			<select class="form-control select3" name="servertype" id="servertype" onchange="editCheckServerType();" >
				<option value="">Select File Server Type</option>
				<option value="ftp" <?php echo ($rwgetdata['servertype']=='ftp')?"selected":""; ?> >FTP Server</option>
				<option value="S3" <?php echo ($rwgetdata['servertype']=='S3')?"selected":""; ?>  >AWS S3</option>
				<option value="same" <?php echo ($rwgetdata['servertype']=='same')?"selected":""; ?> >Same Server</option>
			</select>
		</div>
		<div id="editftpdetails" <?php if($rwgetdata['servertype']!='ftp'){ ?> style="display:none;" <?php } ?> >
			<div class="form-group">
				<label class="text-weight"> <?= $lang['host']; ?><span class="text-alert">*</span></label>
				<input type="text" class="form-control" value="<?php echo $rwgetdata['host']; ?>" placeholder="<?= $lang['host']; ?>" name="hostname" maxlength="30" <?php if($rwgetdata['servertype']=='ftp'){ ?> required <?php } ?> />
			</div>
			<div class="form-group">
				<label class="text-weight"> <?= $lang['port_number']; ?><span class="text-alert">*</span></label>
				<input type="text" class="form-control" value="<?php echo $rwgetdata['port']; ?>" placeholder="<?= $lang['port']; ?>" name="portnumber" maxlength="10" <?php if($rwgetdata['servertype']=='ftp'){ ?> required <?php } ?> />
			</div>
			<div class="form-group">
				<label class="text-weight"> <?= $lang['username']; ?><span class="text-alert">*</span></label>
				<input type="text" class="form-control" value="<?php echo ezeefile_crypt($rwgetdata['username'], 'd'); ?>" placeholder="<?= $lang['username']; ?>" name="username" maxlength="50" <?php if($rwgetdata['servertype']=='ftp'){ ?> required <?php } ?> />
			</div>
			<div class="form-group">
				<label class="text-weight"> <?= $lang['Password']; ?><span class="text-alert">*</span></label>
				<input type="password" class="form-control" value="<?php echo ezeefile_crypt($rwgetdata['password'], 'd'); ?>" placeholder="<?= $lang['Password']; ?>" name="pwd" maxlength="40" <?php if($rwgetdata['servertype']=='ftp'){ ?> required <?php } ?>/>
			</div>
		</div>
		
		<div id="edits3details" <?php if($rwgetdata['servertype']=='ftp'){ ?> style="display:none;" <?php } ?> >
			<div class="form-group ">
				<label class="text-weight"> <?= $lang['bucketname']; ?><span class="text-alert">*</span></label>
				<input type="text" class="form-control" placeholder="<?= $lang['bucketname']; ?>" name="bucketname" value="<?php echo $rwgetdata['bucket_name']; ?>" <?php if($rwgetdata['servertype']!='ftp'){ ?> required <?php } ?> maxlength="50"/>
			</div>
			<div class="form-group">
				<label class="text-weight"> <?= $lang['aws_access_key']; ?><span class="text-alert">*</span></label>
				<input type="password" class="form-control" placeholder="<?= $lang['aws_access_key']; ?>" name="aws_access_key" value="<?php echo ezeefile_crypt($rwgetdata['access_key'], 'd'); ?>" <?php if($rwgetdata['servertype']!='ftp'){ ?> required <?php } ?> maxlength="30" />
			</div>
			<div class="form-group">
				<label class="text-weight"> <?= $lang['aws_secret_access_key']; ?><span class="text-alert">*</span></label>
				<input type="password" class="form-control" placeholder="<?= $lang['aws_secret_access_key']; ?>" name="aws_secret" value="<?php echo $secret_access_key; ?>" <?php if($rwgetdata['servertype']!='ftp'){ ?> required <?php } ?> maxlength="40"/>
			</div>
		</div>
    </div>
</div> 

<input type="hidden" name="id" value="<?php echo $rwgetdata['id']; ?>">

<script type="text/javascript" src="assets/plugins/parsleyjs/parsley.min.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $('form').parsley();
		
    });
 
	
	function editCheckServerType(){
		
		var serverType = $("#servertype").val();
		
		if(serverType=="S3"){
			
			$("#editftpdetails").hide();
			$("#edits3details").show();
			$("#editftpdetails input").attr("required", false);
			$("#edits3details input").attr("required", true);
		}else{
			$("#edits3details").hide();
			$("#editftpdetails").show();
			$("#editftpdetails input").attr("required", true);
			$("#edits3details input").attr("required", false);
			
		}
	}
</script>
<script type="text/javascript">

    $('.specialchaecterlock').keyup(function ()
    {
        var groupName = $(this).val();
        re = /[`1234567890~!@#$%^&*()|+\=?;:'",.<>\{\}\[\]\\\/]/gi;
        var isSplChar = re.test(groupName);
        if (isSplChar)
        {
            var no_spl_char = groupName.replace(/[`~!@#$%^&*()|+\=?;:'",.<>\{\}\[\]\\\/]/gi, '');
            $(this).val(no_spl_char);
        }
    });
</script> 