<?php
function mailUserCreate($superiorEmail, $superiorName, $email, $username, $user_id, $password, $subject, $db_con, $projectName,$Cuser) {

    $msgbody = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" style="font-family: \'Helvetica Neue\', Helvetica, Arial, sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
<head>
<style type="text/css">
img {
max-width: 100%;
}
body {
-webkit-font-smoothing: antialiased; -webkit-text-size-adjust: none; width: 100% !important; height: 100%; line-height: 1.6em;
}
body {
background-color: #f6f6f6;
}
@media only screen and (max-width: 640px) {
  body {
    padding: 0 !important;
  }
  h1 {
    font-weight: 800 !important; margin: 20px 0 5px !important;
  }
  h2 {
    font-weight: 800 !important; margin: 20px 0 5px !important;
  }
  h3 {
    font-weight: 800 !important; margin: 20px 0 5px !important;
  }
  h4 {
    font-weight: 800 !important; margin: 20px 0 5px !important;
  }
  h1 {
    font-size: 22px !important;
  }
  h2 {
    font-size: 18px !important;
  }
  h3 {
    font-size: 16px !important;
  }
  .container {
    padding: 0 !important; width: 100% !important;
  }
  .content {
    padding: 0 !important;
  }
  .content-wrap {
    padding: 10px !important;
  }
  .invoice {
    width: 100% !important;
  }
}
</style>
</head>

<body style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; -webkit-font-smoothing: antialiased; -webkit-text-size-adjust: none; width: 100% !important; height: 100%; line-height: 1.6em; background-color: #f6f6f6; margin: 0;" bgcolor="#f6f6f6">

<table class="body-wrap" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; width: 100%; background-color: #f6f6f6; margin: 0;" bgcolor="#f6f6f6"><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><td style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0;" valign="top"></td>
		<td class="container" width="600" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; display: block !important; max-width: 600px !important; clear: both !important; margin: 0 auto;" valign="top">
			<div class="content" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; max-width: 600px; display: block; margin: 0 auto; padding: 20px;">
				<table class="main" width="100%" cellpadding="0" cellspacing="0" itemprop="action" itemscope itemtype="http://schema.org/ConfirmAction" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; border-radius: 3px; background-color: #fff; margin: 0; border: 1px solid #e9e9e9;" bgcolor="#fff"><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><td class="content-wrap" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 20px;" valign="top">
							<meta itemprop="name" content="Confirm Email" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;" /><table width="100%" cellpadding="0" cellspacing="0" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 20px;" valign="top">
										New User ' . $username . '(Email ID - ' . $email . ') has been created by ' . $Cuser . '!!!
                                                                                <strong>URL </script> - http://'. $_SERVER['HTTP_HOST'] . '<br>
										<strong>Username </strong> - ' . $email . '<br>
										<strong>Password </strong> - ' . $password . '
									</td>
								</tr><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><td class="content-block" itemprop="handler" itemscope itemtype="http://schema.org/HttpActionHandler" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 20px;" valign="top">
										this is a system generated mail please do not reply.
									</td>
								</tr><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0; font-weight: bold;"><td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 20px;" valign="top">
										&mdash; ' . $projectName . ' 
                                                                                <br> Admin 
									</td>
								</tr></table></td>
					</tr></table><div class="footer" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; width: 100%; clear: both; color: #999; margin: 0; padding: 20px;">
					<table width="100%" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><td class="aligncenter content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 12px; vertical-align: top; color: #999; text-align: center; margin: 0; padding: 0 0 20px;" align="center" valign="top"> <a href="#" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 12px; color: #ff0000; text-decoration: underline; margin: 0;">Track, manage and store your documents and reduce paper.</a></td>
						</tr></table></div></div>
		</td>
		<td style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0;" valign="top"></td>
	</tr></table></body>
</html>';


    date_default_timezone_set('Asia/Kolkata');
    $date = date("Y-m-d H:i:s");
   
    require_once './application/PHPMailer/PHPMailerAutoload.php';
    $mail = new PHPMailer;
    $mail->isSMTP();
    $mail->SMTPDebug = 0;
    $mail->Debugoutput = 'html';
    $mail->Host = "mail.cbsl-india.com";
    $mail->Port = 587;
    $mail->SMTPAuth = true;
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );
    $mail->Username = "ezeefileadmin@cbsl-india.com";
    $mail->Password = "Kdcs@08065";
    $mail->setFrom('no-reply@cbsl-india.com', 'System');
    //$mail->addReplyTo('', '');
    $mail->addAddress($email, $username);
    $mail->addAddress($superiorEmail, $superiorName);
    $mail->addBCC('ezeefileadmin@cbsl-india.com');
    $mail->Subject = $subject;
    $mail->msgHTML($msgbody);
    $mail->AltBody = 'New Notifiation from ' . $projectName . '';
    //$mail->addAttachment('PHPMailer/examples/images/phpmailer_mini.png');
    if (!$mail->send()) {
        return false;
    } else {
        $sender = $_SESSION['cdes_user_id'];
        $host = $_SERVER['REMOTE_ADDR'];
        $msgbody = mysqli_real_escape_string($db_con, $msgbody);
        $mailsent = mysqli_query($db_con, "insert into tbl_mail_list(`send_by`, `send_to`, `send_cc`, `send_bcc`, `subject`, `mail_body`, `alt_body`, `mail_time`, `ip`, `module_name`) "
                . "value('$sender','$email','$superiorEmail','','$subject','$msgbody','New Notifiation from $projectName','$date','$host','User Creation')") or die('Error' . mysqli_error($db_con));
        return true;
    }
}
function mailPasschange($txt,$to, $projectName,$username) {

    $msgbody = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" style="font-family: \'Helvetica Neue\', Helvetica, Arial, sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
<head>
<style type="text/css">
img {
max-width: 100%;
}
body {
-webkit-font-smoothing: antialiased; -webkit-text-size-adjust: none; width: 100% !important; height: 100%; line-height: 1.6em;
}
body {
background-color: #f6f6f6;
}
@media only screen and (max-width: 640px) {
  body {
    padding: 0 !important;
  }
  h1 {
    font-weight: 800 !important; margin: 20px 0 5px !important;
  }
  h2 {
    font-weight: 800 !important; margin: 20px 0 5px !important;
  }
  h3 {
    font-weight: 800 !important; margin: 20px 0 5px !important;
  }
  h4 {
    font-weight: 800 !important; margin: 20px 0 5px !important;
  }
  h1 {
    font-size: 22px !important;
  }
  h2 {
    font-size: 18px !important;
  }
  h3 {
    font-size: 16px !important;
  }
  .container {
    padding: 0 !important; width: 100% !important;
  }
  .content {
    padding: 0 !important;
  }
  .content-wrap {
    padding: 10px !important;
  }
  .invoice {
    width: 100% !important;
  }
}
</style>
</head>

<body style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; -webkit-font-smoothing: antialiased; -webkit-text-size-adjust: none; width: 100% !important; height: 100%; line-height: 1.6em; background-color: #f6f6f6; margin: 0;" bgcolor="#f6f6f6">

<table class="body-wrap" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; width: 100%; background-color: #f6f6f6; margin: 0;" bgcolor="#f6f6f6"><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><td style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0;" valign="top"></td>
		<td class="container" width="600" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; display: block !important; max-width: 600px !important; clear: both !important; margin: 0 auto;" valign="top">
			<div class="content" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; max-width: 600px; display: block; margin: 0 auto; padding: 20px;">
				<table class="main" width="100%" cellpadding="0" cellspacing="0" itemprop="action" itemscope itemtype="http://schema.org/ConfirmAction" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; border-radius: 3px; background-color: #fff; margin: 0; border: 1px solid #e9e9e9;" bgcolor="#fff"><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><td class="content-wrap" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 20px;" valign="top">
							<meta itemprop="name" content="Confirm Email" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;" /><table width="100%" cellpadding="0" cellspacing="0" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 20px;" valign="top">
										Your  Verification Code for changing password on http://'. $_SERVER['HTTP_HOST'] . ' is <br>
                                                                                
										<strong>OTP </strong> - ' . $txt . '<br>
										
									</td>
								</tr><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><td class="content-block" itemprop="handler" itemscope itemtype="http://schema.org/HttpActionHandler" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 20px;" valign="top">
										this is a system generated mail please do not reply.
									</td>
								</tr><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0; font-weight: bold;"><td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 20px;" valign="top">
										&mdash; ' . $projectName . ' 
                                                                                <br> Admin 
									</td>
								</tr></table></td>
					</tr></table><div class="footer" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; width: 100%; clear: both; color: #999; margin: 0; padding: 20px;">
					<table width="100%" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><td class="aligncenter content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 12px; vertical-align: top; color: #999; text-align: center; margin: 0; padding: 0 0 20px;" align="center" valign="top"> <a href="#" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 12px; color: #ff0000; text-decoration: underline; margin: 0;">Track, manage and store your documents and reduce paper.</a></td>
						</tr></table></div></div>
		</td>
		<td style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0;" valign="top"></td>
	</tr></table></body>
</html>';


    date_default_timezone_set('Asia/Kolkata');
    $date = date("Y-m-d H:i:s");
    require_once './application/PHPMailer/PHPMailerAutoload.php';
    $mail = new PHPMailer;
    $mail->isSMTP();
    $mail->SMTPDebug = 0;
    $mail->Debugoutput = 'html';
    $mail->Host = "mail.cbsl-india.com";
    $mail->Port = 587;
    $mail->SMTPAuth = true;
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );
    $mail->Username = "ezeefileadmin@cbsl-india.com";
    $mail->Password = "Kdcs@08065";
    $mail->setFrom('no-reply@cbsl-india.com', 'System');
    //$mail->addReplyTo('', '');
    $mail->addAddress($to, $username);
    //$mail->addAddress($superiorEmail, $superiorName);
    //$mail->addBCC('soft.dev5@cbsl-india.com');
    $mail->Subject = "OTP for password change request.";
    $mail->msgHTML($msgbody);
    $mail->AltBody = 'New Notifiation from ' . $projectName . '';
    //$mail->addAttachment('PHPMailer/examples/images/phpmailer_mini.png');
    if (!$mail->send()) {
        return false;
    } else {
        
        return true;
    }
}
function mailResetPass($to, $pass, $projectName,$username) {

    $msgbody = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" style="font-family: \'Helvetica Neue\', Helvetica, Arial, sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
<head>
<style type="text/css">
img {
max-width: 100%;
}
body {
-webkit-font-smoothing: antialiased; -webkit-text-size-adjust: none; width: 100% !important; height: 100%; line-height: 1.6em;
}
body {
background-color: #f6f6f6;
}
@media only screen and (max-width: 640px) {
  body {
    padding: 0 !important;
  }
  h1 {
    font-weight: 800 !important; margin: 20px 0 5px !important;
  }
  h2 {
    font-weight: 800 !important; margin: 20px 0 5px !important;
  }
  h3 {
    font-weight: 800 !important; margin: 20px 0 5px !important;
  }
  h4 {
    font-weight: 800 !important; margin: 20px 0 5px !important;
  }
  h1 {
    font-size: 22px !important;
  }
  h2 {
    font-size: 18px !important;
  }
  h3 {
    font-size: 16px !important;
  }
  .container {
    padding: 0 !important; width: 100% !important;
  }
  .content {
    padding: 0 !important;
  }
  .content-wrap {
    padding: 10px !important;
  }
  .invoice {
    width: 100% !important;
  }
}
</style>
</head>

<body style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; -webkit-font-smoothing: antialiased; -webkit-text-size-adjust: none; width: 100% !important; height: 100%; line-height: 1.6em; background-color: #f6f6f6; margin: 0;" bgcolor="#f6f6f6">

<table class="body-wrap" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; width: 100%; background-color: #f6f6f6; margin: 0;" bgcolor="#f6f6f6"><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><td style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0;" valign="top"></td>
		<td class="container" width="600" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; display: block !important; max-width: 600px !important; clear: both !important; margin: 0 auto;" valign="top">
			<div class="content" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; max-width: 600px; display: block; margin: 0 auto; padding: 20px;">
				<table class="main" width="100%" cellpadding="0" cellspacing="0" itemprop="action" itemscope itemtype="http://schema.org/ConfirmAction" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; border-radius: 3px; background-color: #fff; margin: 0; border: 1px solid #e9e9e9;" bgcolor="#fff"><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><td class="content-wrap" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 20px;" valign="top">
							<meta itemprop="name" content="Confirm Email" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;" /><table width="100%" cellpadding="0" cellspacing="0" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 20px;" valign="top">
										Your new Login password for '. $projectName . ' is <br>
                                                                                
										<strong>Password </strong> - ' . $pass . '<br>
                                                                                    
										<strong>Url - </strong>http://'. $_SERVER['HTTP_HOST'] . '<br>
									</td>
								</tr><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><td class="content-block" itemprop="handler" itemscope itemtype="http://schema.org/HttpActionHandler" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 20px;" valign="top">
										this is a system generated mail please do not reply.
									</td>
								</tr><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0; font-weight: bold;"><td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 20px;" valign="top">
										&mdash; ' . $projectName . ' 
                                                                                <br> Admin 
									</td>
								</tr></table></td>
					</tr></table><div class="footer" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; width: 100%; clear: both; color: #999; margin: 0; padding: 20px;">
					<table width="100%" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><td class="aligncenter content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 12px; vertical-align: top; color: #999; text-align: center; margin: 0; padding: 0 0 20px;" align="center" valign="top"> <a href="#" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 12px; color: #ff0000; text-decoration: underline; margin: 0;">Track, manage and store your documents and reduce paper.</a></td>
						</tr></table></div></div>
		</td>
		<td style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0;" valign="top"></td>
	</tr></table></body>
</html>';


    date_default_timezone_set('Asia/Kolkata');
    $date = date("Y-m-d H:i:s");
    require_once './application/PHPMailer/PHPMailerAutoload.php';
    $mail = new PHPMailer;
    $mail->isSMTP();
    $mail->SMTPDebug = 0;
    $mail->Debugoutput = 'html';
    $mail->Host = "mail.cbsl-india.com";
    $mail->Port = 587;
    $mail->SMTPAuth = true;
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );
    $mail->Username = "ezeefileadmin@cbsl-india.com";
    $mail->Password = "Kdcs@08065";
    $mail->setFrom('no-reply@cbsl-india.com', 'System');
    //$mail->addReplyTo('', '');
    $mail->addAddress($to, $username);
    //$mail->addAddress($superiorEmail, $superiorName);
    //$mail->addBCC('soft.dev5@cbsl-india.com');
    $mail->Subject = "New Login password for $projectName";
    $mail->msgHTML($msgbody);
    $mail->AltBody = 'New Notifiation from ' . $projectName . '';
    //$mail->addAttachment('PHPMailer/examples/images/phpmailer_mini.png');
    if (!$mail->send()) {
        return false;
    } else {
        
        return true;
    }
}
function feedbackMail($to,$from,$fbackMsg,$UserName,$des,$projectName) {

    $msgbody = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" style="font-family: \'Helvetica Neue\', Helvetica, Arial, sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
<head>
<style type="text/css">
img {
max-width: 100%;
}
body {
-webkit-font-smoothing: antialiased; -webkit-text-size-adjust: none; width: 100% !important; height: 100%; line-height: 1.6em;
}
body {
background-color: #f6f6f6;
}
@media only screen and (max-width: 640px) {
  body {
    padding: 0 !important;
  }
  h1 {
    font-weight: 800 !important; margin: 20px 0 5px !important;
  }
  h2 {
    font-weight: 800 !important; margin: 20px 0 5px !important;
  }
  h3 {
    font-weight: 800 !important; margin: 20px 0 5px !important;
  }
  h4 {
    font-weight: 800 !important; margin: 20px 0 5px !important;
  }
  h1 {
    font-size: 22px !important;
  }
  h2 {
    font-size: 18px !important;
  }
  h3 {
    font-size: 16px !important;
  }
  .container {
    padding: 0 !important; width: 100% !important;
  }
  .content {
    padding: 0 !important;
  }
  .content-wrap {
    padding: 10px !important;
  }
  .invoice {
    width: 100% !important;
  }
}
</style>
</head>

<body style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; -webkit-font-smoothing: antialiased; -webkit-text-size-adjust: none; width: 100% !important; height: 100%; line-height: 1.6em; background-color: #f6f6f6; margin: 0;" bgcolor="#f6f6f6">

<table class="body-wrap" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; width: 100%; background-color: #f6f6f6; margin: 0;" bgcolor="#f6f6f6"><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><td style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0;" valign="top"></td>
		<td class="container" width="600" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; display: block !important; max-width: 600px !important; clear: both !important; margin: 0 auto;" valign="top">
			<div class="content" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; max-width: 600px; display: block; margin: 0 auto; padding: 20px;">
				<table class="main" width="100%" cellpadding="0" cellspacing="0" itemprop="action" itemscope itemtype="http://schema.org/ConfirmAction" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; border-radius: 3px; background-color: #fff; margin: 0; border: 1px solid #e9e9e9;" bgcolor="#fff"><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><td class="content-wrap" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 20px;" valign="top">
							<meta itemprop="name" content="Confirm Email" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;" /><table width="100%" cellpadding="0" cellspacing="0" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 20px;" valign="top">
										Feeedback Message of http://'. $_SERVER['HTTP_HOST'] . ' is <br>
                                                                                
										<strong>Feedback </strong> - ' . $fbackMsg . '<br>
										
									</td>
								</tr><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><td class="content-block" itemprop="handler" itemscope itemtype="http://schema.org/HttpActionHandler" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 20px;" valign="top">
										this is a system generated mail please do not reply.
									</td>
								</tr><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0; font-weight: bold;"><td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 20px;" valign="top">
										&mdash; ' . $UserName . ' 
                                                                                <br> '. $des . '
									</td>
								</tr></table></td>
					</tr></table><div class="footer" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; width: 100%; clear: both; color: #999; margin: 0; padding: 20px;">
					<table width="100%" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><td class="aligncenter content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 12px; vertical-align: top; color: #999; text-align: center; margin: 0; padding: 0 0 20px;" align="center" valign="top"> <a href="#" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 12px; color: #ff0000; text-decoration: underline; margin: 0;">Track, manage and store your documents and reduce paper.</a></td>
						</tr></table></div></div>
		</td>
		<td style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0;" valign="top"></td>
	</tr></table></body>
</html>';


    date_default_timezone_set('Asia/Kolkata');
    $date = date("Y-m-d H:i:s");
    require_once './application/PHPMailer/PHPMailerAutoload.php';
    $mail = new PHPMailer;
    $mail->isSMTP();
    $mail->SMTPDebug = 0;
    $mail->Debugoutput = 'html';
    $mail->Host = "mail.cbsl-india.com";
    $mail->Port = 587;
    $mail->SMTPAuth = true;
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );
    $mail->Username = "ezeefileadmin@cbsl-india.com";
    $mail->Password = "Kdcs@08065";
    $mail->setFrom('no-reply@cbsl-india.com', 'Ezeefile User');
    //$mail->addReplyTo('', '');
    $mail->addAddress($to, $UserName);
    //$mail->addAddress($superiorEmail, $superiorName);
    //$mail->addBCC('soft.dev5@cbsl-india.com');
    $mail->Subject = "User Feedback Message";
    $mail->msgHTML($msgbody);
    $mail->AltBody = 'New Notifiation from ' . $projectName . '';
    //$mail->addAttachment('PHPMailer/examples/images/phpmailer_mini.png');
    if (!$mail->send()) {
        return false;
    } else {
        
        return true;
    }
}
function assignTask($ticket, $id, $db_con, $projectName) {
    $task = mysqli_query($db_con, "select * from tbl_doc_assigned_wf where id='$id' and NextTask='0'") or die('Error:' . mysqli_error($db_con));

    if (mysqli_num_rows($task) > 0) {

        // echo 'heloppp'; die;

        $rwTask = mysqli_fetch_assoc($task);

        $work = mysqli_query($db_con, "select * from tbl_task_master where task_id='$rwTask[task_id]'") or die('Error:' . mysqli_error($db_con));
        $rwWork = mysqli_fetch_assoc($work);

        if ($rwWork['priority_id'] == 1) {
            $prirty = 'Urgent';
        } else if ($rwWork['priority_id'] == 2) {
            $prirty = 'Medium';
        } else if ($rwWork['priority_id'] == 3) {
            $prirty = 'Normal';
        }

        if (!empty($rwWork['priority_id'])) {
            $priorty = ' <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                                       <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
									<b>Priority</b>	
								       </td>
                                                                  
                                                                   <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px" valign="top">
									' .
                    $urgent . $medium . $normal
                    . '	
								       </td>
								</tr>';
        }


        $strtDate = strtotime($rwTask['start_date']);
        if ($rwWork['deadline_type'] == 'Date') {
            $endDate = $strtDate + $rwWork['deadline'] * 60 * 60;
        } else if ($rwWork['deadline_type'] == 'Days') {
            $endDate = $strtDate + $rwWork['deadline'] * 24 * 60 * 60;
        } else if ($rwWork['deadline_type'] == 'Hrs') {
            $endDate = $strtDate + $rwWork['deadline'] * 60 * 60;
        }
        $enddate = date('Y-m-d H:i:s', $endDate);

        $wfn = mysqli_query($db_con, "select * from tbl_workflow_master where workflow_id='$rwWork[workflow_id]'") or die('Error:' . mysqli_error($db_con));
        $rwWfn = mysqli_fetch_assoc($wfn);
        $wrkFlName = $rwWfn['workflow_name'];


        $stn = mysqli_query($db_con, "select * from tbl_step_master where step_id='$rwWork[step_id]'") or die('Error:' . mysqli_error($db_con));
        $rwStn = mysqli_fetch_assoc($stn);
        $stpName = $rwStn['step_name'];

        if ($rwTask['doc_id'] != '0') {
            $dms = mysqli_query($db_con, "select * from tbl_document_master where doc_id='$rwTask[doc_id]'") or die('Error:' . mysqli_error($db_con));
            $rwDms = mysqli_fetch_assoc($dms);
            $oldDocName = $rwDms['old_doc_name'];
        }

        $user = mysqli_query($db_con, "select first_name,last_name from tbl_user_master where user_id='$rwTask[assign_by]'") or die('Error:' . mysqli_error($db_con));
        $rwUser = mysqli_fetch_assoc($user);
        $asinBy = $rwUser['first_name'] . ' ' . $rwUser['last_name'];

        //$tskId = $rwTask['task_id'];
        //$taskRe = mysqli_query($db_con, "select * from tbl_task_master where task_id='$tskId'") or die('Error:'.mysqli_error($db_con));
        //$rwTaskRe = mysqli_fetch_assoc($taskRe);

        $asinUser = $rwWork['assign_user'];
        $asinUserId = mysqli_query($db_con, "select * from tbl_user_master where user_id='$asinUser'") or die('Error:' . mysqli_error($db_con));
        $rwasinUserId = mysqli_fetch_assoc($asinUserId);
        $asinUserEmail = $rwasinUserId['user_email_id'];
        $asinUserName = $rwasinUserId['first_name'] . ' ' . $rwasinUserId['last_name'];

        $altruser = $rwWork['alternate_user'];
        $altrUserId = mysqli_query($db_con, "select * from tbl_user_master where user_id='$altruser'") or die('Error:' . mysqli_error($db_con));
        $rwaltrUserId = mysqli_fetch_assoc($altrUserId);
        $altrUserEmail = $rwaltrUserId['user_email_id'];
        $altrUserName = $rwaltrUserId['first_name'] . ' ' . $rwaltrUserId['last_name'];

        $suprvsr = $rwWork['supervisor'];
        $suprUserId = mysqli_query($db_con, "select * from tbl_user_master where user_id='$suprvsr'") or die('Error:' . mysqli_error($db_con));
        $rwsuprUserId = mysqli_fetch_assoc($suprUserId);
        $suprUserEmail = $rwsuprUserId['user_email_id'];
        $suprUserName = $rwsuprUserId['first_name'] . ' ' . $rwsuprUserId['last_name'];

        $taskDesc = $rwTask['task_remarks'];


        if (!empty($taskDesc)) {
            $des = '<b>Description :</b> <p>' . $taskDesc . '</p>';
        }

        if (!empty($oldDocName)) {
            $docName = '<tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                                       <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
                                                                        <b>Documents</b>	
								       </td>
                                                                
                                                                <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
									' . $oldDocName . '	
								       </td>
								</tr>';
        }


        if (!empty($rwWork['task_instructions'])) {

            $taskInstruction = ' <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                                       <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
										<b>Instruction</b>
								       </td>
                                                                
                                                                <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
									' .
                    $rwWork[task_instructions]
                    . '	
								       </td>
								</tr>';
        }

        $msgbody = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" style="font-family: \'Helvetica Neue\', Helvetica, Arial, sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
<head>
<style type="text/css">
img {
max-width: 100%;
}
body {
-webkit-font-smoothing: antialiased; -webkit-text-size-adjust: none; width: 100% !important; height: 100%; line-height: 1.6em;
}
body {
background-color: #f6f6f6;
}
@media only screen and (max-width: 640px) {
  body {
    padding: 0 !important;
  }
  h1 {
    font-weight: 800 !important; margin: 20px 0 5px !important;
  }
  h2 {
    font-weight: 800 !important; margin: 20px 0 5px !important;
  }
  h3 {
    font-weight: 800 !important; margin: 20px 0 5px !important;
  }
  h4 {
    font-weight: 800 !important; margin: 20px 0 5px !important;
  }
  h1 {
    font-size: 22px !important;
  }
  h2 {
    font-size: 18px !important;
  }
  h3 {
    font-size: 16px !important;
  }
  .container {
    padding: 0 !important; width: 100% !important;
  }
  .content {
    padding: 0 !important;
  }
  .content-wrap {
    padding: 10px !important;
  }
  .invoice {
    width: 100% !important;
  }
  
}
</style>
</head>

<body style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; -webkit-font-smoothing: antialiased; -webkit-text-size-adjust: none; width: 100% !important; height: 100%; line-height: 1.6em; background-color: #f6f6f6; margin: 0;" bgcolor="#f6f6f6">

<table class="body-wrap" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; width: 100%; background-color: #f6f6f6; margin: 0;" bgcolor="#f6f6f6">
    <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
        <td style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0;" valign="top"></td>
		<td class="container" width="600" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; display: block !important; max-width: 600px !important; clear: both !important; margin: 0 auto;" valign="top">
			<div class="content" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; max-width: 600px; display: block; margin: 0 auto; padding: 20px;">
				<table class="main" width="100%" cellpadding="0" cellspacing="0" itemprop="action" itemscope itemtype="http://schema.org/ConfirmAction" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; border-radius: 3px; background-color: #fff; margin: 0; border: 1px solid #e9e9e9;" bgcolor="#fff">
                                       

                         <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                        <td class="content-wrap" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 20px;" valign="top">
                                       
							New task with Ticket Number <b>' . $ticket . '</b> has been assigned to <b>' . $asinUserName . '</b>.<br /><br />
                                                                               ' . $des . '
                                                         
                                    <table width="100%" cellpadding="0" cellspacing="0" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;" border="1">
                                                               
                                                             
                                                      ' . $priorty . '
                                                               <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                                       <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
									<b>Task Status</b>	
								       </td>
                                                                  
                                                                   <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
									' .
                $rwTask[task_status]
                . '	
								       </td>
								</tr>
                                                            <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                                       <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
									<b>Deadline</b>	
								       </td>
                                                                
                                                                <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
										' .
                $enddate
                . '
								       </td>
								</tr>
                                                            <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                                       <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px; " valign="top">
									<b>Workflow Name</b>	
								       </td>
                                                               
                                                                <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
									' .
                $wrkFlName
                . '	
								       </td>
								</tr>
                                                            <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                                       <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
										<b>Step Name<b>
								       </td>
                                                              
                                                                <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding:5px;" valign="top">
									' .
                $stpName
                . '	
								       </td>
								</tr>
                                                           
                                                            <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                                       <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
									<b>Task Name</b>	
								       </td>
                                                                
                                                                <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
									' .
                $rwWork['task_name']
                . '	
								       </td>
								</tr>
                                                ' . $docName . '
                                                            <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                                       <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
									<b>Submitted By</b>	
								       </td>
                                                               
                                                                <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
									' .
                $asinBy
                . '	
								       </td>
								</tr>
                                                            <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                                       <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
										<b>Assigned To</b>
								       </td>
                                                                
                                                                <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
									' .
                $asinUserName
                . '	
								       </td>
								</tr>
                                                            <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                                       <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
										<b>Supervisor</b>
								       </td>
                                                           
                                                                <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
									' .
                $suprUserName
                . '	
								       </td>
								</tr>
                                                             <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                                       <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
										<b>Alertnate User</b>
								       </td>
                                                                
                                                                <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
									' .
                $altrUserName
                . '	
								       </td>
								</tr>
                                                       ' . $taskInstruction . '
                                                         
                                                        </table><br>
                                                        (URL - http://'. $_SERVER['HTTP_HOST'] . ') <br>
                                                       	<p>this is a system generated mail please do not reply.</p>
									
                                                               
                                                               
										&mdash; ' . $projectName . ' 
                                                                                <br> Admin 
                                                                                </td>
						</tr>
                                </table>
                               <div class="footer" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; width: 100%; clear: both; color: #999; margin: 0; padding: 5px;">
					<table width="100%" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                               <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                   <td class="aligncenter content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 12px; vertical-align: top; color: #999; text-align: center; margin: 0; padding: 0 0 5px;" align="center" valign="top"> 
                                                       <a href="#" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 12px; color: #999; text-decoration: underline; margin: 0;">
                                                          
                                                       </a>
                                                   </td>
						</tr>
                                        </table>
                                </div>
                        </div>
		</td>
        <td style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0;" valign="top">
            
        </td>
	</tr>
</table>
</body>
</html>';

        date_default_timezone_set('Asia/Kolkata');
        $date = date("Y-m-d H:i:s");
        require_once './application/PHPMailer/PHPMailerAutoload.php';
        $mail = new PHPMailer;
        $mail->isSMTP();
        $mail->SMTPDebug = 0;
        $mail->Debugoutput = 'html';
        $mail->Host = "mail.cbsl-india.com";
        $mail->Port = 587;
        $mail->SMTPAuth = true;
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        $mail->Username = "ezeefileadmin@cbsl-india.com";
        $mail->Password = "Kdcs@08065";
        $mail->setFrom('no-reply@cbsl-india.com', 'admin');

        //$mail->addAddress($mailTo, $userName);
        $mail->addAddress($asinUserEmail, $asinUserName);
        $mail->addCC($altrUserEmail, $altrUserName);
        $mail->addCC($suprUserEmail, $suprUserName);
        $mail->addBCC("ezeefileadmin@cbsl-india.com", "Admin");
        $sendCC = $altrUserEmail . ',' . $suprUserEmail;

        $mail->Subject = $rwWork['task_name'] . ' ' . 'Task Assigened in ' . $wrkFlName . ' WorkFlow';

        $mail->msgHTML($msgbody);
        $mail->AltBody = 'New Notifiation from ' . $projectName . '';
        //$mail->addAttachment('PHPMailer/examples/images/phpmailer_mini.png');
        if (!$mail->send()) {
            echo "Mailer Error: " . $mail->ErrorInfo;
            return false;
        } else {
            $sender = $_SESSION['cdes_user_id'];
            $host = $_SERVER['REMOTE_ADDR'];
            $msgbody = mysqli_real_escape_string($db_con, $msgbody);
            $mailsent = mysqli_query($db_con, "insert into tbl_mail_list(`send_by`, `send_to`, `send_cc`, `send_bcc`, `subject`, `mail_body`, `alt_body`, `mail_time`, `ip`, `module_name`) "
                    . "value('$sender','$asinUserEmail','$sendCC','','New Task Assigned !','$msgbody','New Notifiation from $projectName','$date','$host','Assign Task')") or die('Error' . mysqli_error($db_con));
            return true;
        }
    }
}

//assignTask(61, $db_con);


function rejectTask($id, $taskId, $ticket, $db_con, $projectName,$comment,$doc_id) {
    
    //echo "<script>alert('".$ticket."')</script>";
    
    // return 1;
    $task = mysqli_query($db_con, "select * from tbl_doc_assigned_wf where id='$id' and NextTask=2") or die('Error:' . mysqli_error($db_con));
    if (mysqli_num_rows($task) > 0) {
        $rwTask = mysqli_fetch_assoc($task);
        $ticketId = $rwTask['ticket_id'];
        $work = mysqli_query($db_con, "select * from tbl_task_master where task_id='$rwTask[task_id]'") or die('Error:' . mysqli_error($db_con));
        $rwWork = mysqli_fetch_assoc($work);

        if ($rwWork['priority_id'] == 1) {
            $urgent = 'Urgent';
        } else if ($rwWork['priority_id'] == 2) {
            $medium = 'Medium';
        } else if ($rwWork['priority_id'] == 3) {
            $normal = 'Normal';
        }

        $strtDate = strtotime($rwTask['start_date']);
        if ($rwWork['deadline_type'] == 'Date') {
            $endDate = $strtDate + $rwWork['deadline'] * 60 * 60;
        } else if ($rwWork['deadline_type'] == 'Days') {
            $endDate = $strtDate + $rwWork['deadline'] * 24 * 60 * 60;
        } else if ($rwWork['deadline_type'] == 'Hrs') {
            $endDate = $strtDate + $rwWork['deadline'] * 60 * 60;
        }
        $enddate = date('Y-m-d H:i:s', $endDate);

        $wfn = mysqli_query($db_con, "select * from tbl_workflow_master where workflow_id='$rwWork[workflow_id]'") or die('Error:' . mysqli_error($db_con));
        $rwWfn = mysqli_fetch_assoc($wfn);
        $wrkFlName = $rwWfn['workflow_name'];


        $stn = mysqli_query($db_con, "select * from tbl_step_master where step_id='$rwWork[step_id]'") or die('Error:' . mysqli_error($db_con));
        $rwStn = mysqli_fetch_assoc($stn);
        $stpName = $rwStn['step_name'];


        $dms = mysqli_query($db_con, "select * from tbl_document_master where doc_id='$rwTask[doc_id]'") or die('Error:' . mysqli_error($db_con));
        $rwDms = mysqli_fetch_assoc($dms);
        $oldDocName = $rwDms['old_doc_name'];

        $user = mysqli_query($db_con, "select * from tbl_user_master where user_id='$rwTask[assign_by]'") or die('Error:' . mysqli_error($db_con));
        $rwUser = mysqli_fetch_assoc($user);
        $asinBy = $rwUser['first_name'] . ' ' . $rwUser['last_name'];
        $asinByEmail = $rwUser['user_email_id'];

        //$tskId = $rwTask['task_id'];
        //$taskRe = mysqli_query($db_con, "select * from tbl_task_master where task_id='$tskId'") or die('Error:'.mysqli_error($db_con));
        //$rwTaskRe = mysqli_fetch_assoc($taskRe);

        $asinUser = $rwWork['assign_user'];
        $asinUserId = mysqli_query($db_con, "select * from tbl_user_master where user_id='$asinUser'") or die('Error:' . mysqli_error($db_con));
        $rwasinUserId = mysqli_fetch_assoc($asinUserId);
        $asinUserEmail = $rwasinUserId['user_email_id'];
        $asinUserName = $rwasinUserId['first_name'] . ' ' . $rwasinUserId['last_name'];

        $altruser = $rwWork['alternate_user'];
        $altrUserId = mysqli_query($db_con, "select * from tbl_user_master where user_id='$altruser'") or die('Error:' . mysqli_error($db_con));
        $rwaltrUserId = mysqli_fetch_assoc($altrUserId);
        $altrUserEmail = $rwaltrUserId['user_email_id'];
        $altrUserName = $rwaltrUserId['first_name'] . ' ' . $rwaltrUserId['last_name'];

        $suprvsr = $rwWork['supervisor'];
        $suprUserId = mysqli_query($db_con, "select * from tbl_user_master where user_id='$suprvsr'") or die('Error:' . mysqli_error($db_con));
        $rwsuprUserId = mysqli_fetch_assoc($suprUserId);
        $suprUserEmail = $rwsuprUserId['user_email_id'];
        $suprUserName = $rwsuprUserId['first_name'] . ' ' . $rwsuprUserId['last_name'];

        $taskDesc = $rwTask['task_remarks'];
        $rejectByid = $rwTask['action_by'];
        $rejctByUser = mysqli_query($db_con, "select * from tbl_user_master where user_id='$rejectByid'") or die('Error:' . mysqli_error($db_con));
        $rwrejctByUser = mysqli_fetch_assoc($rejctByUser);
        $rejctByFulName = $rwrejctByUser['first_name'] . ' ' . $rwrejctByUser['last_name'];
        $dc_paths = mysqli_query($db_con, "SELECT old_doc_name,doc_path,doc_name FROM `tbl_document_master` WHERE $doc_id='$id'") or die('Error:' . mysqli_error($db_con));
         $dcPathsRow=mysqli_fetch_array($dc_paths);
         $docName=explode('_', $dcPathsRow['doc_name']);
        $updateDocName = $docName[0] . '_' .$doc_id. '_' . $docName[1];
        $fileVersion = mysqli_query($db_con, "SELECT old_doc_name,doc_path,doc_name FROM `tbl_document_master` WHERE doc_name='$updateDocName'") or die('Error:' . mysqli_error($db_con));
        if (!empty($taskDesc)) {
            $des = '<b>Description :</b> <p>' . $taskDesc . '</p>';
        }

        if (!empty($oldDocName)) {
            $docName = '<tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                                       <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
                                                                        <b>Documents</b>	
								       </td>
                                                                
                                                                <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
									' .
                    $oldDocName
                    . '	
								       </td>
								</tr>';
        }



        $msgbody = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" style="font-family: \'Helvetica Neue\', Helvetica, Arial, sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
<head>
<style type="text/css">
img {
max-width: 100%;
}
body {
-webkit-font-smoothing: antialiased; -webkit-text-size-adjust: none; width: 100% !important; height: 100%; line-height: 1.6em;
}
body {
background-color: #f6f6f6;
}
@media only screen and (max-width: 640px) {
  body {
    padding: 0 !important;
  }
  h1 {
    font-weight: 800 !important; margin: 20px 0 5px !important;
  }
  h2 {
    font-weight: 800 !important; margin: 20px 0 5px !important;
  }
  h3 {
    font-weight: 800 !important; margin: 20px 0 5px !important;
  }
  h4 {
    font-weight: 800 !important; margin: 20px 0 5px !important;
  }
  h1 {
    font-size: 22px !important;
  }
  h2 {
    font-size: 18px !important;
  }
  h3 {
    font-size: 16px !important;
  }
  .container {
    padding: 0 !important; width: 100% !important;
  }
  .content {
    padding: 0 !important;
  }
  .content-wrap {
    padding: 10px !important;
  }
  .invoice {
    width: 100% !important;
  }
}
</style>
</head>

<body style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; -webkit-font-smoothing: antialiased; -webkit-text-size-adjust: none; width: 100% !important; height: 100%; line-height: 1.6em; background-color: #f6f6f6; margin: 0;" bgcolor="#f6f6f6">

<table class="body-wrap" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; width: 100%; background-color: #f6f6f6; margin: 0;" bgcolor="#f6f6f6">
    <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
        <td style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0;" valign="top"></td>
		<td class="container" width="600" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; display: block !important; max-width: 600px !important; clear: both !important; margin: 0 auto;" valign="top">
			<div class="content table table-bordered" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; max-width: 600px; display: block; margin: 0 auto; padding: 20px;">
				<table class="main" width="100%" cellpadding="0" cellspacing="0" itemprop="action" itemscope itemtype="http://schema.org/ConfirmAction" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; border-radius: 3px; background-color: #fff; margin: 0; border: 1px solid #e9e9e9;" bgcolor="#fff">
                                    <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                        <td class="content-wrap" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 20px;" valign="top">
							 
										Task with Ticket Number <b>' . $ticket . '</b> has been rejected by ' . $rejctByFulName . '.<br /><br/>
                                                                               ' . $des . '
								
                                                          <table width="100%" cellpadding="0" cellspacing="0" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;" border="1">
                                                               
                                                             
                                                            <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                                       <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
									<b>Priority</b>	
								       </td>
                                                                  
                                                                   <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px" valign="top">
									' .
                $urgent . $medium . $normal
                . '	
								       </td>
								</tr>
                                                               <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                                       <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
									<b>Task Status</b>	
								       </td>
                                                                  
                                                                   <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
									' .
                $rwTask[task_status]
                . '	
								       </td>
								</tr>
                                                            <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                                       <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
									<b>Deadline</b>	
								       </td>
                                                                
                                                                <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
										' .
                $enddate
                . '
								       </td>
								</tr>
                                                            <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                                       <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px; " valign="top">
									<b>Workflow Name</b>	
								       </td>
                                                               
                                                                <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
									' .
                $wrkFlName
                . '	
								       </td>
								</tr>
                                                            <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                                       <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
										<b>Step Name<b>
								       </td>
                                                              
                                                                <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding:5px;" valign="top">
									' .
                $stpName
                . '	
								       </td>
								</tr>
                                                           
                                                            <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                                       <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
									<b>Task Name</b>	
								       </td>
                                                                
                                                                <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
									' .
                $rwWork['task_name']
                . '	
								       </td>
								</tr>
                                                ' . $docName . '
                                                            <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                                       <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
									<b>Submitted By</b>	
								       </td>
                                                               
                                                                <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
									' .
                $asinBy
                . '	
								       </td>
								</tr>
                                                            <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                                       <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
										<b>Assigned To</b>
								       </td>
                                                                
                                                                <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
									' .
                $asinUserName
                . '	
								       </td>
								</tr>
                                                            <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                                       <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
										<b>Supervisor</b>
								       </td>
                                                           
                                                                <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
									' .
                $suprUserName
                . '	
								       </td>
								</tr>
                                                             <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                                       <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
										<b>Alertnate User</b>
								       </td>
                                                                
                                                                <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
									' .
                $altrUserName
                . '	
								       </td>
								</tr>
                                                             <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                                       <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
										<b>Instruction</b>
								       </td>
                                                                
                                                                <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
									' .
                $rwWork[task_instructions]
                . '	
								       </td>
								</tr>
                                                         <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                                       <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
										<b>Comment</b>
								       </td>
                                                                
                                                                <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
									' .$comment.'	
								       </td>
								</tr>
                                                                <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                                       <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
										<b>attachments</b>
								       </td>
                                                                
                                                                <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top"><a href="http://localhost/myWork/ezeefile1.2/trunk/Ezeefile1.2/'.$dcPath.'">
									Attachments	
								       </td>
								</tr>';
        while($version_row=mysqli_fetch_array($fileVersion)){
          $docPaths=$version_row['doc_path'];  
          $msgbody.='<tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                                       <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
										<b>attachments</b>
								       </td>
                                                                
                                                                <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top"><a href="http://localhost/myWork/ezeefile1.2/trunk/Ezeefile1.2/'.$docPaths.'">
									Attachments	
								       </td>
								</tr>';   
        }
                                                        $msgbody.='</table><br>
                                                        (URL - http://'. $_SERVER['HTTP_HOST'] . ') <br>
                                                       	<p>this is a system generated mail please do not reply.</p>
									
                                                               
                                                               
										&mdash; ' . $projectName . '
                                                                                <br> Admin 
                                                                                </td>
						</tr>
                                </table>
                               <div class="footer" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; width: 100%; clear: both; color: #999; margin: 0; padding: 5px;">
					<table width="100%" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                               <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                   <td class="aligncenter content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 12px; vertical-align: top; color: #999; text-align: center; margin: 0; padding: 0 0 5px;" align="center" valign="top"> 
                                                       <a href="#" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 12px; color: #999; text-decoration: underline; margin: 0;">
                                                          
                                                       </a>
                                                   </td>
						</tr>
                                        </table>
                                </div>
                        </div>
		</td>
        <td style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0;" valign="top">
            
        </td>
	</tr>
</table>
</body>
</html>';
        date_default_timezone_set('Asia/Kolkata');
        $date = date("Y-m-d H:i:s");
        require_once './application/PHPMailer/PHPMailerAutoload.php';

        $mail = new PHPMailer;
        $mail->isSMTP();
        $mail->SMTPDebug = 0;
        $mail->Debugoutput = 'html';
        $mail->Host = "mail.cbsl-india.com";
        $mail->Port = 587;
        $mail->SMTPAuth = true;
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        $mail->Username = "ezeefileadmin@cbsl-india.com";
        $mail->Password = "Kdcs@08065";
        $mail->setFrom('no-reply@cbsl-india.com', 'admin');

        $mail->addAddress($asinByEmail, $asinBy);
        $mail->addBCC("ezeefileadmin@cbsl-india.com", "Admin");
        
        $getAprovBy = mysqli_query($db_con, "select tum.first_name, tum.last_name, tum.user_email_id FROM tbl_doc_assigned_wf tda INNER JOIN  tbl_user_master tum ON tda.action_by = tum.user_id where tda.ticket_id = '$ticketId' ") or die('Error:' . mysqli_error($db_con));
        $cc = array();
        while ($rwgetAprovBy = mysqli_fetch_assoc($getAprovBy)) {

            $firstName = $rwgetAprovBy['first_name'];
            $lastName = $rwgetAprovBy['last_name'];
            $fulName = $firstName . ' ' . $lastName;
            $AprovByEmail = $rwgetAprovBy['user_email_id'];
            $cc[] = $AprovByEmail;
            $mail->addCC($AprovByEmail, $fulName);
        }
        //echo "<script>alert('".print_r($cc)."')</script>";
        $mail->Subject = 'Task Rejected !';

        $mail->msgHTML($msgbody);
        $mail->AltBody = 'New Notifiation from ' . $projectName . '';
        //$mail->addAttachment('PHPMailer/examples/images/phpmailer_mini.png');
        if (!$mail->send()) {
            echo "Mailer Error: " . $mail->ErrorInfo;
            return false;
        } else {
            $sendCC = implode(', ', $cc);
            $sender = $_SESSION['cdes_user_id'];
            $host = $_SERVER['REMOTE_ADDR'];
            $msgbody = mysqli_real_escape_string($db_con, $msgbody);
            $mailsent = mysqli_query($db_con, "insert into tbl_mail_list(`send_by`, `send_to`, `send_cc`, `send_bcc`, `subject`, `mail_body`, `alt_body`, `mail_time`, `ip`, `module_name`) "
                    . "value('$sender','$asinByEmail','$sendCC','','Task Rejected !','$msgbody','New Notifiation from $projectName','$date','$host','Reject Task')") or die('Error' . mysqli_error($db_con));
            return true;
        }
    }
}

function completeTask($ticket, $id, $wfid, $db_con, $projectName) {

    $task = mysqli_query($db_con, "select * from tbl_doc_assigned_wf where id='$id' and NextTask='1'") or die('Error:' . mysqli_error($db_con));
    if (mysqli_num_rows($task) > 0) {
        $rwTask = mysqli_fetch_assoc($task);

        $work = mysqli_query($db_con, "select * from tbl_task_master where task_id='$rwTask[task_id]'") or die('Error:' . mysqli_error($db_con));
        $rwWork = mysqli_fetch_assoc($work);

        if ($rwWork['priority_id'] == 1) {
            $urgent = 'Urgent';
        } else if ($rwWork['priority_id'] == 2) {
            $medium = 'Medium';
        } else if ($rwWork['priority_id'] == 3) {
            $normal = 'Normal';
        }

        $strtDate = strtotime($rwTask['start_date']);
        if ($rwWork['deadline_type'] == 'Date') {
            $endDate = $strtDate + $rwWork['deadline'] * 60 * 60;
        } else if ($rwWork['deadline_type'] == 'Days') {
            $endDate = $strtDate + $rwWork['deadline'] * 24 * 60 * 60;
        } else if ($rwWork['deadline_type'] == 'Hrs') {
            $endDate = $strtDate + $rwWork['deadline'] * 60 * 60;
        }
        $enddate = date('Y-m-d H:i:s', $endDate);

        $wfn = mysqli_query($db_con, "select * from tbl_workflow_master where workflow_id='$rwWork[workflow_id]'") or die('Error:' . mysqli_error($db_con));
        $rwWfn = mysqli_fetch_assoc($wfn);
        $wrkFlName = $rwWfn['workflow_name'];


        $stn = mysqli_query($db_con, "select * from tbl_step_master where step_id='$rwWork[step_id]'") or die('Error:' . mysqli_error($db_con));
        $rwStn = mysqli_fetch_assoc($stn);
        $stpName = $rwStn['step_name'];


        $dms = mysqli_query($db_con, "select * from tbl_document_master where doc_id='$rwTask[doc_id]'") or die('Error:' . mysqli_error($db_con));
        $rwDms = mysqli_fetch_assoc($dms);
        $oldDocName = $rwDms['old_doc_name'];

        //$user = mysqli_query($db_con, "select first_name,last_name from tbl_user_master where user_id='$rwTask[assign_by]'") or die('Error:'.mysqli_error($db_con));
        //$rwUser = mysqli_fetch_assoc($user);
        //$asinBy = $rwUser['first_name'] . ' ' . $rwUser['last_name'];
        //$tskId = $rwTask['task_id'];
        //$taskRe = mysqli_query($db_con, "select * from tbl_task_master where task_id='$tskId'") or die('Error:'.mysqli_error($db_con));
        //$rwTaskRe = mysqli_fetch_assoc($taskRe);

        $asinUser = $rwWork['assign_user'];
        $asinUserId = mysqli_query($db_con, "select * from tbl_user_master where user_id='$asinUser'") or die('Error:' . mysqli_error($db_con));
        $rwasinUserId = mysqli_fetch_assoc($asinUserId);
        $asinUserEmail = $rwasinUserId['user_email_id'];
        $asinUserName = $rwasinUserId['first_name'] . ' ' . $rwasinUserId['last_name'];

        $altruser = $rwWork['alternate_user'];
        $altrUserId = mysqli_query($db_con, "select * from tbl_user_master where user_id='$altruser'") or die('Error:' . mysqli_error($db_con));
        $rwaltrUserId = mysqli_fetch_assoc($altrUserId);
        $altrUserEmail = $rwaltrUserId['user_email_id'];
        $altrUserName = $rwaltrUserId['first_name'] . ' ' . $rwaltrUserId['last_name'];

        $suprvsr = $rwWork['supervisor'];
        $suprUserId = mysqli_query($db_con, "select * from tbl_user_master where user_id='$suprvsr'") or die('Error:' . mysqli_error($db_con));
        $rwsuprUserId = mysqli_fetch_assoc($suprUserId);
        $suprUserEmail = $rwsuprUserId['user_email_id'];
        $suprUserName = $rwsuprUserId['first_name'] . ' ' . $rwsuprUserId['last_name'];

        $taskDesc = $rwTask['task_remarks'];

        //get asign by detail      
        $getAsinBy = mysqli_query($db_con, "select tum.user_email_id, tum.first_name, tum.last_name, tda.start_date from tbl_doc_assigned_wf tda inner join tbl_user_master tum on tda.assign_by = tum.user_id where tda.ticket_id = '$ticket'") or die('Error:' . mysqli_error($db_con));
        $rwgetAsinBy = mysqli_fetch_assoc($getAsinBy);
        $getAsinByEmail = $rwgetAsinBy['user_email_id'];
        $getAsinByName = $rwgetAsinBy['first_name'] . ' ' . $rwgetAsinBy['last_name'];
        $getAsignDate = $rwgetAsinBy['start_date'];



        //get work flow name
        $workflowName = mysqli_query($db_con, "select * from tbl_workflow_master where workflow_id='$wfid'") or die('Error:' . mysqli_error($db_con));
        $rwworkflowName = mysqli_fetch_assoc($workflowName);
        $workflwName = $rwworkflowName['workflow_name'];

        //get action by, action time and comment
        //inner join tbl_user_master tum on tum.user_id = tda.action_by inner join tbl_task_master ttm on ttm.task_id = tda.task_id

        $taskHistry = mysqli_query($db_con, "select ttc.task_status, ttc.comment_time, tum.first_name, tum.last_name, ttm.task_name, ttc.comment from tbl_doc_assigned_wf tda inner join tbl_task_comment ttc on (tda.ticket_id = ttc.tickt_id and tda.task_id = ttc.task_id) inner join tbl_user_master tum on (tum.user_id = tda.action_by) inner join tbl_task_master ttm on ttm.task_id = tda.task_id where tda.ticket_id='$ticket' and tda.NextTask='1' order by ttc.comment_time desc") or die('Error:' . mysqli_error($db_con));

        $aprBy = '';

        while ($rwTaskHistry = mysqli_fetch_assoc($taskHistry)) {

            //$actionByName = $rwTaskHistry['first_name'].' '.$rwTaskHistry['last_name'];
            // $actionByDate = $rwTaskHistry['action_time'];
            //$actionByComment = $rwTaskHistry['comment'];
            // print_r($rwTaskHistry); die;

            if ($rwTaskHistry['task_status'] == 'Approved' or $rwTaskHistry['task_status'] == 'Processed' or $rwTaskHistry['task_status'] == 'Done' or $rwTaskHistry['task_status'] == 'Complete') {

                $aprBy .= "<b>" . $rwTaskHistry['task_status'] . " by: </b>" . $rwTaskHistry['first_name'] . ' ' . $rwTaskHistry['last_name'] . " <br />";
            } elseif ($rwTaskHistry['task_status'] == 'Rejected') {
                $aprBy .= "<b>" . $rwTaskHistry['task_status'] . " by: </b>" . $rwTaskHistry['first_name'] . ' ' . $rwTaskHistry['last_name'] . " <br />";
            } elseif ($rwTaskHistry['task_status'] == 'Aborted') {
                $aprBy .= "<b>" . $rwTaskHistry['task_status'] . " by: </b>" . $rwTaskHistry['first_name'] . ' ' . $rwTaskHistry['last_name'] . " <br />";
            }

            //$aprBy .= "<b>Approved by: </b>". $rwTaskHistry['first_name'].' '.$rwTaskHistry['last_name'] ." <br />"; 
            $aprBy .= "<b>At Task: </b>" . $rwTaskHistry['task_name'] . "<br />";
            $aprBy .= "<b>On: </b>" . $rwTaskHistry['comment_time'] . "<br />";
            $aprBy .= "<b>Comment: </b>" . $rwTaskHistry['comment'] . "<br/><br/>";

            echo "<br/><br/><br/><br/>><br/>";
        }

        if (!empty($taskDesc)) {
            $des = '<b>Description :</b> <p>' . $taskDesc . '</p>';
        }

        if (!empty($oldDocName)) {
            $docName = '<tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                                       <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
                                                                        <b>Documents</b>	
								       </td>
                                                             
                                                                <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
									' .
                    $oldDocName
                    . '	
								       </td>
								</tr>';
        }

        $msgbody = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" style="font-family: \'Helvetica Neue\', Helvetica, Arial, sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
<head>
<style type="text/css">
img {
max-width: 100%;
}
body {
-webkit-font-smoothing: antialiased; -webkit-text-size-adjust: none; width: 100% !important; height: 100%; line-height: 1.6em;
}
body {
background-color: #f6f6f6;
}
@media only screen and (max-width: 640px) {
  body {
    padding: 0 !important;
  }
  h1 {
    font-weight: 800 !important; margin: 20px 0 5px !important;
  }
  h2 {
    font-weight: 800 !important; margin: 20px 0 5px !important;
  }
  h3 {
    font-weight: 800 !important; margin: 20px 0 5px !important;
  }
  h4 {
    font-weight: 800 !important; margin: 20px 0 5px !important;
  }
  h1 {
    font-size: 22px !important;
  }
  h2 {
    font-size: 18px !important;
  }
  h3 {
    font-size: 16px !important;
  }
  .container {
    padding: 0 !important; width: 100% !important;
  }
  .content {
    padding: 0 !important;
  }
  .content-wrap {
    padding: 10px !important;
  }
  .invoice {
    width: 100% !important;
  }
}
</style>
</head>

<body style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; -webkit-font-smoothing: antialiased; -webkit-text-size-adjust: none; width: 100% !important; height: 100%; line-height: 1.6em; background-color: #f6f6f6; margin: 0;" bgcolor="#f6f6f6">

<table class="body-wrap" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; width: 100%; background-color: #f6f6f6; margin: 0;" bgcolor="#f6f6f6">
    <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
        <td style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0;" valign="top"></td>
		<td class="container" width="600" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; display: block !important; max-width: 600px !important; clear: both !important; margin: 0 auto;" valign="top">
			<div class="content" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; max-width: 600px; display: block; margin: 0 auto; padding: 20px;">
				<table class="main table table-bordered" width="100%" cellpadding="0" cellspacing="0" itemprop="action" itemscope itemtype="http://schema.org/ConfirmAction" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; border-radius: 3px; background-color: #fff; margin: 0; border: 1px solid #e9e9e9;" bgcolor="#fff">
                                    <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                        <td class="content-wrap" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 20px;" valign="top">
							         Dear Concern,</br>
                                                                 This is to inform that <b>' . $workflwName . '</b> with Ticket Number <b>' . $ticket . '</b> has been approved.<br /></br />
                                
								<table width="100%" cellpadding="0" cellspacing="0" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;" border="1">
                                                               
                                                             
                                                            <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                                       <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
									<b>Priority</b>	
								       </td>
                                                                  
                                                                   <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px" valign="top">
									' .
                $urgent . $medium . $normal
                . '	
								       </td>
								</tr>
                                                               <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                                       <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
									<b>Task Status</b>	
								       </td>
                                                                  
                                                                   <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
									' .
                $rwTask[task_status]
                . '	
								       </td>
								</tr>
                                                            <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                                       <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
									<b>Deadline</b>	
								       </td>
                                                                
                                                                <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
										' .
                $enddate
                . '
								       </td>
								</tr>
                                                            <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                                       <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px; " valign="top">
									<b>Workflow Name</b>	
								       </td>
                                                               
                                                                <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
									' .
                $wrkFlName
                . '	
								       </td>
								</tr>
                                                            <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                                       <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
										<b>Step Name<b>
								       </td>
                                                              
                                                                <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding:5px;" valign="top">
									' .
                $stpName
                . '	
								       </td>
								</tr>
                                                           
                                                            <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                                       <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
									<b>Task Name</b>	
								       </td>
                                                                
                                                                <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
									' .
                $rwWork['task_name']
                . '	
								       </td>
								</tr>
                                                ' . $docName . '
                                                            <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                                       <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
									<b>Submitted By</b>	
								       </td>
                                                               
                                                                <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
									' .
                $getAsinByName
                . '	
								       </td>
								</tr>
                                                            <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                                       <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
										<b>Assigned To</b>
								       </td>
                                                                
                                                                <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
									' .
                $asinUserName
                . '	
								       </td>
								</tr>
                                                            <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                                       <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
										<b>Supervisor</b>
								       </td>
                                                           
                                                                <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
									' .
                $suprUserName
                . '	
								       </td>
								</tr>
                                                             <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                                       <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
										<b>Alertnate User</b>
								       </td>
                                                                
                                                                <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
									' .
                $altrUserName
                . '	
								       </td>
								</tr>
                                                             <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                                       <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
										<b>Instruction</b>
								       </td>
                                                                
                                                                <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
									' .
                $rwWork[task_instructions]
                . '	
								       </td>
								</tr>
                                                         
                                                        </table></br>
                                                        
                                                          ' . $aprBy . '
                                                         </br>
                                                        <b> Submitted BY: </b>' . $getAsinByName . ' <br />
                                                        <b> Submission On: </b>' . $getAsignDate . ' <br />
                                                          ' . $des . '<br>
                                                              
                                                      (URL - http://'. $_SERVER['HTTP_HOST'] . ')
                                                          
                                              	<p>This is a system generated mail please do not reply.</p>
									
                                                               
                                                               
										&mdash; ' . $projectName . ' 
                                                                                <br> Admin 
                                                                                </td>
						</tr>
                                </table>
                               <div class="footer" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; width: 100%; clear: both; color: #999; margin: 0; padding: 5px;">
					<table width="100%" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                               <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                   <td class="aligncenter content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 12px; vertical-align: top; color: #999; text-align: center; margin: 0; padding: 0 0 5px;" align="center" valign="top"> 
                                                       <a href="#" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 12px; color: #999; text-decoration: underline; margin: 0;">
                                                          
                                                       </a>
                                                   </td>
						</tr>
                                        </table>
                                </div>
                        </div>
		</td>
        <td style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0;" valign="top">
            
        </td>
	</tr>
</table>
</body>
</html>';

        date_default_timezone_set('Asia/Kolkata');
        $date = date("Y-m-d H:i:s");
        require_once './application/PHPMailer/PHPMailerAutoload.php';
        $mail = new PHPMailer;
        $mail->isSMTP();
        $mail->SMTPDebug = 0;
        $mail->Debugoutput = 'html';
        $mail->Host = "mail.cbsl-india.com";
        $mail->Port = 587;
        $mail->SMTPAuth = true;
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        $mail->Username = "ezeefileadmin@cbsl-india.com";
        $mail->Password = "Kdcs@08065";
        $mail->setFrom('no-reply@cbsl-india.com', 'admin');

        //$mail->addAddress($mailTo, $userName);
        $mail->addAddress($getAsinByEmail, $getAsinByName);
        $mail->addBCC("ezeefileadmin@cbsl-india.com", "Admin");
        $getAprovBy = mysqli_query($db_con, "select tum.first_name, tum.last_name, tum.user_email_id FROM tbl_doc_assigned_wf tda INNER JOIN  tbl_user_master tum ON tda.action_by = tum.user_id where tda.ticket_id = '$ticket' ") or die('Error:' . mysqli_error($db_con));
        $cc = array();

        while ($rwgetAprovBy = mysqli_fetch_assoc($getAprovBy)) {

            $firstName = $rwgetAprovBy['first_name'];
            $lastName = $rwgetAprovBy['last_name'];
            $fulName = $firstName . ' ' . $lastName;
            $AprovByEmail = $rwgetAprovBy['user_email_id'];
            $cc[] = $AprovByEmail;
            $mail->addCC($AprovByEmail, $fulName);
        }

        $mail->Subject = $rwWork['task_name'].' '.'has been Completed !';

        $mail->msgHTML($msgbody);
        $mail->AltBody = 'New Notifiation from ' . $projectName . '';
        //$mail->addAttachment('PHPMailer/examples/images/phpmailer_mini.png');
        if (!$mail->send()) {
            echo "Mailer Error: " . $mail->ErrorInfo;
            return false;
        } else {
            $sendCC = implode(', ', $cc);
            $sender = $_SESSION['cdes_user_id'];
            $host = $_SERVER['REMOTE_ADDR'];
            $msgbody = mysqli_real_escape_string($db_con, $msgbody);
            $mailsent = mysqli_query($db_con, "insert into tbl_mail_list(`send_by`, `send_to`, `send_cc`, `send_bcc`, `subject`, `mail_body`, `alt_body`, `mail_time`, `ip`, `module_name`) "
                    . "value('$sender','$getAsinByEmail','$sendCC','','Task has been Completed !','$msgbody','New Notifiation from $projectName','$date','$host','Task Completed')") or die('Error' . mysqli_error($db_con));
            return true;
        }
    }
}

function mailSendAccounts($msgbody1, $mailFrom, $subject, $dbc, $projectName) {

    $user = mysqli_query($dbc, "select * from adminuser where UserID='$mailFrom'"); // or die('Error'.mysqli_error($dbc));
    $rwUser = mysqli_fetch_assoc($user);
    //$UserMail=$rwUser['UserEmail'];
    //$userName=$rwUser['UserFirstName'].' '.$rwUser['UserLastName'];
    $subject = $subject . ' By ' . $rwUser['UserFirstName'] . ' ' . $rwUser['UserLastName'];
    date_default_timezone_set('Asia/Kolkata');
    require_once 'PHPMailer/PHPMailerAutoload.php';
    $mail = new PHPMailer;
    $mail->isSMTP();
    $mail->SMTPDebug = 4;
    $mail->Debugoutput = 'html';
    $mail->Host = "mail.cbsl-india.com";
    $mail->Port = 587;
    $mail->SMTPAuth = true;
    $mail->Username = "ezeefileadmin@cbsl-india.com";
    $mail->Password = "Kdcs@08065";
    $mail->setFrom('no-reply@cbsl-india.com', 'admin');
    //$mail1->addReplyTo('', '');
    if ($rwUser['UserCompany'] == '101') {
        $admin = mysqli_query($dbc, "select * from adminuser where UserRole='5'"); // or die('Error'.mysqli_error($dbc));
        while ($rwAdmin = mysqli_fetch_assoc($admin)) {
            //echo 'ok';
            $mail->addAddress($rwAdmin['UserEmail'], $rwUser['UserFirstName'] . ' ' . $rwUser['UserLastName']);
        }
    } else {
        $admin = mysqli_query($dbc, "select * from adminuser where UserRole='6'");
        while ($rwAdmin = mysqli_fetch_assoc($admin)) {

            $mail->addAddress($rwAdmin['UserEmail'], $rwUser['UserFirstName'] . ' ' . $rwUser['UserLastName']);
        }
    }
    $mail->addBCC("ezeefileadmin@cbsl-india.com", "Admin");
    $mail->Subject = $subject;
    $mail->msgHTML($msgbody1);
    $mail->AltBody = 'Notifiation from CDES';
    //$mail->addAttachment('PHPMailer/examples/images/phpmailer_mini.png');
    if (!$mail->send()) {
        return false;
    } else {
        return true;
    }
}

/*
  require_once_once './application/config/database.php';
  $mail=assignTask(63, $db_con);
  if($mail){
  echo 'ok';
  }else
  {
  echo 'failed';
  }
 * 
 */

function assignTaskAlternate($id, $db_con, $projectName) {
    $task = mysqli_query($db_con, "select * from tbl_doc_assigned_wf where id='$id' and NextTask=3") or die('Error:' . mysqli_error($db_con));
    if (mysqli_num_rows($task) > 0) {
        $rwTask = mysqli_fetch_assoc($task);
        $work = mysqli_query($db_con, "select * from tbl_task_master where task_id='$rwTask[task_id]'") or die('Error:' . mysqli_error($db_con));
        $rwWork = mysqli_fetch_assoc($work);

        if ($rwWork['priority_id'] == 1) {
            $urgent = 'Urgent';
        } else if ($rwWork['priority_id'] == 2) {
            $medium = 'Medium';
        } else if ($rwWork['priority_id'] == 3) {
            $normal = 'Normal';
        }

        $strtDate = strtotime($rwTask['start_date']);
        if ($rwWork['deadline_type'] == 'Date') {
            $endDate = $strtDate + $rwWork['deadline'] * 60 * 60;
        } else if ($rwWork['deadline_type'] == 'Days') {
            $endDate = $strtDate + $rwWork['deadline'] * 24 * 60 * 60;
        } else if ($rwWork['deadline_type'] == 'Hrs') {
            $endDate = $strtDate + $rwWork['deadline'] * 60 * 60;
        }
        $enddate = date('Y-m-d H:i:s', $endDate);

        $wfn = mysqli_query($db_con, "select * from tbl_workflow_master where workflow_id='$rwWork[workflow_id]'") or die('Error:' . mysqli_error($db_con));
        $rwWfn = mysqli_fetch_assoc($wfn);
        $wrkFlName = $rwWfn['workflow_name'];


        $stn = mysqli_query($db_con, "select * from tbl_step_master where step_id='$rwWork[step_id]'") or die('Error:' . mysqli_error($db_con));
        $rwStn = mysqli_fetch_assoc($stn);
        $stpName = $rwStn['step_name'];


        $dms = mysqli_query($db_con, "select * from tbl_document_master where doc_id='$rwTask[doc_id]'") or die('Error:' . mysqli_error($db_con));
        $rwDms = mysqli_fetch_assoc($dms);
        $oldDocName = $rwDms['old_doc_name'];

        $user = mysqli_query($db_con, "select first_name,last_name from tbl_user_master where user_id='$rwTask[assign_by]'") or die('Error:' . mysqli_error($db_con));
        $rwUser = mysqli_fetch_assoc($user);
        $asinBy = $rwUser['first_name'] . ' ' . $rwUser['last_name'];

        //$tskId = $rwTask['task_id'];
        //$taskRe = mysqli_query($db_con, "select * from tbl_task_master where task_id='$tskId'") or die('Error:'.mysqli_error($db_con));
        //$rwTaskRe = mysqli_fetch_assoc($taskRe);

        $asinUser = $rwWork['assign_user'];
        $asinUserId = mysqli_query($db_con, "select * from tbl_user_master where user_id='$asinUser'") or die('Error:' . mysqli_error($db_con));
        $rwasinUserId = mysqli_fetch_assoc($asinUserId);
        $asinUserEmail = $rwasinUserId['user_email_id'];
        $asinUserName = $rwasinUserId['first_name'] . ' ' . $rwasinUserId['last_name'];

        $altruser = $rwWork['alternate_user'];
        $altrUserId = mysqli_query($db_con, "select * from tbl_user_master where user_id='$altruser'") or die('Error:' . mysqli_error($db_con));
        $rwaltrUserId = mysqli_fetch_assoc($altrUserId);
        $altrUserEmail = $rwaltrUserId['user_email_id'];
        $altrUserName = $rwaltrUserId['first_name'] . ' ' . $rwaltrUserId['last_name'];

        $suprvsr = $rwWork['supervisor'];
        $suprUserId = mysqli_query($db_con, "select * from tbl_user_master where user_id='$suprvsr'") or die('Error:' . mysqli_error($db_con));
        $rwsuprUserId = mysqli_fetch_assoc($suprUserId);
        $suprUserEmail = $rwsuprUserId['user_email_id'];
        $suprUserName = $rwsuprUserId['first_name'] . ' ' . $rwsuprUserId['last_name'];

        $taskDesc = $rwTask['task_remarks'];


        if (!empty($taskDesc)) {
            $des = '<b>Description :</b> <p>' . $taskDesc . '</p>';
        }

        if (!empty($oldDocName)) {
            $docName = '<tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                                       <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
                                                                        <b>Documents</b>	
								       </td>
                                                                
                                                                <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
									' .
                    $oldDocName
                    . '	
								       </td>
								</tr>';
        }




        $msgbody = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" style="font-family: \'Helvetica Neue\', Helvetica, Arial, sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
<head>
<style type="text/css">
img {
max-width: 100%;
}
body {
-webkit-font-smoothing: antialiased; -webkit-text-size-adjust: none; width: 100% !important; height: 100%; line-height: 1.6em;
}
body {
background-color: #f6f6f6;
}
@media only screen and (max-width: 640px) {
  body {
    padding: 0 !important;
  }
  h1 {
    font-weight: 800 !important; margin: 20px 0 5px !important;
  }
  h2 {
    font-weight: 800 !important; margin: 20px 0 5px !important;
  }
  h3 {
    font-weight: 800 !important; margin: 20px 0 5px !important;
  }
  h4 {
    font-weight: 800 !important; margin: 20px 0 5px !important;
  }
  h1 {
    font-size: 22px !important;
  }
  h2 {
    font-size: 18px !important;
  }
  h3 {
    font-size: 16px !important;
  }
  .container {
    padding: 0 !important; width: 100% !important;
  }
  .content {
    padding: 0 !important;
  }
  .content-wrap {
    padding: 10px !important;
  }
  .invoice {
    width: 100% !important;
  }
  
}
</style>
</head>

<body style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; -webkit-font-smoothing: antialiased; -webkit-text-size-adjust: none; width: 100% !important; height: 100%; line-height: 1.6em; background-color: #f6f6f6; margin: 0;" bgcolor="#f6f6f6">

<table class="body-wrap" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; width: 100%; background-color: #f6f6f6; margin: 0;" bgcolor="#f6f6f6">
    <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
        <td style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0;" valign="top"></td>
		<td class="container" width="600" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; display: block !important; max-width: 600px !important; clear: both !important; margin: 0 auto;" valign="top">
			<div class="content" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; max-width: 600px; display: block; margin: 0 auto; padding: 20px;">
				<table class="main" width="100%" cellpadding="0" cellspacing="0" itemprop="action" itemscope itemtype="http://schema.org/ConfirmAction" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; border-radius: 3px; background-color: #fff; margin: 0; border: 1px solid #e9e9e9;" bgcolor="#fff">
                                       

                         <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                        <td class="content-wrap" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 20px;" valign="top">
                                       Dear ' . $altrUserName . ',<br>
							Ticket no ' . $rwTask[ticket_id] . ' has been assigned to ' . $altrUserName . ' from ' . $asinUserName . '. You are alternate user for performing task.<br><br>
                                                                               ' . $des . '
                                                         
                                    <table width="100%" cellpadding="0" cellspacing="0" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;" border="1">
                                                               
                                                             
                                                            <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                                       <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
									<b>Priority</b>	
								       </td>
                                                                  
                                                                   <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px" valign="top">
									' .
                $urgent . $medium . $normal
                . '	
								       </td>
								</tr>
                                                               <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                                       <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
									<b>Task Status</b>	
								       </td>
                                                                  
                                                                   <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
									' .
                $rwTask[task_status]
                . '	
								       </td>
								</tr>
                                                            <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                                       <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
									<b>Deadline</b>	
								       </td>
                                                                
                                                                <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
										' .
                $enddate
                . '
								       </td>
								</tr>
                                                            <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                                       <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px; " valign="top">
									<b>Workflow Name</b>	
								       </td>
                                                               
                                                                <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
									' .
                $wrkFlName
                . '	
								       </td>
								</tr>
                                                            <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                                       <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
										<b>Step Name<b>
								       </td>
                                                              
                                                                <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding:5px;" valign="top">
									' .
                $stpName
                . '	
								       </td>
								</tr>
                                                           
                                                            <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                                       <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
									<b>Task Name</b>	
								       </td>
                                                                
                                                                <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
									' .
                $rwWork['task_name']
                . '	
								       </td>
								</tr>
                                                ' . $docName . '
                                                            <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                                       <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
									<b>Submitted By</b>	
								       </td>
                                                               
                                                                <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
									' .
                $asinBy
                . '	
								       </td>
								</tr>
                                                            <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                                       <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
										<b>Assigned To</b>
								       </td>
                                                                
                                                                <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
									' .
                $asinUserName
                . '	
								       </td>
								</tr>
                                                            <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                                       <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
										<b>Supervisor</b>
								       </td>
                                                           
                                                                <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
									' .
                $suprUserName
                . '	
								       </td>
								</tr>
                                                             <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                                       <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
										<b>Alertnate User</b>
								       </td>
                                                                
                                                                <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
									' .
                $altrUserName
                . '	
								       </td>
								</tr>
                                                             <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                                       <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
										<b>Instruction</b>
								       </td>
                                                                
                                                                <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
									' .
                $rwWork[task_instructions]
                . '	
								       </td>
								</tr>
                                                         
                                                        </table>
                                                       	<p>this is a system generated mail please do not reply.</p>
									
                                                               
                                                               
										&mdash; ' . $projectName . '
                                                                                <br> Admin 
                                                                                </td>
						</tr>
                                </table>
                               <div class="footer" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; width: 100%; clear: both; color: #999; margin: 0; padding: 5px;">
					<table width="100%" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                               <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                   <td class="aligncenter content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 12px; vertical-align: top; color: #999; text-align: center; margin: 0; padding: 0 0 5px;" align="center" valign="top"> 
                                                       <a href="#" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 12px; color: #999; text-decoration: underline; margin: 0;">
                                                          
                                                       </a>
                                                   </td>
						</tr>
                                        </table>
                                </div>
                        </div>
		</td>
        <td style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0;" valign="top">
            
        </td>
	</tr>
</table>
</body>
</html>';

        date_default_timezone_set('Asia/Kolkata');
        $date = date("Y-m-d H:i:s");
        require_once './application/PHPMailer/PHPMailerAutoload.php';
        $mail = new PHPMailer;
        $mail->isSMTP();
        $mail->SMTPDebug = 0;
        $mail->Debugoutput = 'html';
        $mail->Host = "mail.cbsl-india.com";
        $mail->Port = 587;
        $mail->SMTPAuth = true;
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        $mail->Username = "ezeefileadmin@cbsl-india.com";
        $mail->Password = "Kdcs@08065";
        $mail->setFrom('no-reply@cbsl-india.com', '' . $projectName . ' Admin');

        //$mail->addAddress($mailTo, $userName);

        $mail->addAddress($altrUserEmail, $altrUserName);
        $mail->addCC($suprUserEmail, $suprUserName);
        $mail->addCC($asinUserEmail, $asinUserName);
        $mail->addBCC("ezeefileadmin@cbsl-india.com", "Admin");
        $sendCC = $suprUserEmail . ', ' . $asinUserEmail;

        $mail->Subject = 'Task assigned to alternate user!';

        $mail->msgHTML($msgbody);
        $mail->AltBody = 'New Notifiation from ' . $projectName . '';
        //$mail->addAttachment('PHPMailer/examples/images/phpmailer_mini.png');
        if (!$mail->send()) {
            echo "Mailer Error: " . $mail->ErrorInfo;
            return false;
        } else {

            $sender = $_SESSION['cdes_user_id'];
            $host = $_SERVER['REMOTE_ADDR'];
            $msgbody = mysqli_real_escape_string($db_con, $msgbody);
            $mailsent = mysqli_query($db_con, "insert into tbl_mail_list(`send_by`, `send_to`, `send_cc`, `send_bcc`, `subject`, `mail_body`, `alt_body`, `mail_time`, `ip`, `module_name`) "
                    . "value('$sender','$altrUserEmail','$sendCC','','Task assigned to alternate user!','$msgbody','New Notifiation from $projectName','$date','$host','Assign Task to Alternate User')") or die('Error' . mysqli_error($db_con));
            return true;
        }
    } else {
        return false;
    }
}

function assignTaskSupervisor($id, $db_con, $projectName) {
    $task = mysqli_query($db_con, "select * from tbl_doc_assigned_wf where id='$id' and NextTask=3") or die('Error:' . mysqli_error($db_con));
    if (mysqli_num_rows($task) > 0) {
        $rwTask = mysqli_fetch_assoc($task);
        $work = mysqli_query($db_con, "select * from tbl_task_master where task_id='$rwTask[task_id]'") or die('Error:' . mysqli_error($db_con));
        $rwWork = mysqli_fetch_assoc($work);

        if ($rwWork['priority_id'] == 1) {
            $urgent = 'Urgent';
        } else if ($rwWork['priority_id'] == 2) {
            $medium = 'Medium';
        } else if ($rwWork['priority_id'] == 3) {
            $normal = 'Normal';
        }

        $strtDate = strtotime($rwTask['start_date']);
        if ($rwWork['deadline_type'] == 'Date') {
            $endDate = $strtDate + $rwWork['deadline'] * 60 * 60;
        } else if ($rwWork['deadline_type'] == 'Days') {
            $endDate = $strtDate + $rwWork['deadline'] * 24 * 60 * 60;
        } else if ($rwWork['deadline_type'] == 'Hrs') {
            $endDate = $strtDate + $rwWork['deadline'] * 60 * 60;
        }
        $enddate = date('Y-m-d H:i:s', $endDate);

        $wfn = mysqli_query($db_con, "select * from tbl_workflow_master where workflow_id='$rwWork[workflow_id]'") or die('Error:' . mysqli_error($db_con));
        $rwWfn = mysqli_fetch_assoc($wfn);
        $wrkFlName = $rwWfn['workflow_name'];


        $stn = mysqli_query($db_con, "select * from tbl_step_master where step_id='$rwWork[step_id]'") or die('Error:' . mysqli_error($db_con));
        $rwStn = mysqli_fetch_assoc($stn);
        $stpName = $rwStn['step_name'];


        $dms = mysqli_query($db_con, "select * from tbl_document_master where doc_id='$rwTask[doc_id]'") or die('Error:' . mysqli_error($db_con));
        $rwDms = mysqli_fetch_assoc($dms);
        $oldDocName = $rwDms['old_doc_name'];

        $user = mysqli_query($db_con, "select first_name,last_name from tbl_user_master where user_id='$rwTask[assign_by]'") or die('Error:' . mysqli_error($db_con));
        $rwUser = mysqli_fetch_assoc($user);
        $asinBy = $rwUser['first_name'] . ' ' . $rwUser['last_name'];

        //$tskId = $rwTask['task_id'];
        //$taskRe = mysqli_query($db_con, "select * from tbl_task_master where task_id='$tskId'") or die('Error:'.mysqli_error($db_con));
        //$rwTaskRe = mysqli_fetch_assoc($taskRe);

        $asinUser = $rwWork['assign_user'];
        $asinUserId = mysqli_query($db_con, "select * from tbl_user_master where user_id='$asinUser'") or die('Error:' . mysqli_error($db_con));
        $rwasinUserId = mysqli_fetch_assoc($asinUserId);
        $asinUserEmail = $rwasinUserId['user_email_id'];
        $asinUserName = $rwasinUserId['first_name'] . ' ' . $rwasinUserId['last_name'];

        $altruser = $rwWork['alternate_user'];
        $altrUserId = mysqli_query($db_con, "select * from tbl_user_master where user_id='$altruser'") or die('Error:' . mysqli_error($db_con));
        $rwaltrUserId = mysqli_fetch_assoc($altrUserId);
        $altrUserEmail = $rwaltrUserId['user_email_id'];
        $altrUserName = $rwaltrUserId['first_name'] . ' ' . $rwaltrUserId['last_name'];

        $suprvsr = $rwWork['supervisor'];
        $suprUserId = mysqli_query($db_con, "select * from tbl_user_master where user_id='$suprvsr'") or die('Error:' . mysqli_error($db_con));
        $rwsuprUserId = mysqli_fetch_assoc($suprUserId);
        $suprUserEmail = $rwsuprUserId['user_email_id'];
        $suprUserName = $rwsuprUserId['first_name'] . ' ' . $rwsuprUserId['last_name'];

        $taskDesc = $rwTask['task_remarks'];


        if (!empty($taskDesc)) {
            $des = '<b>Description :</b> <p>' . $taskDesc . '</p>';
        }

        if (!empty($oldDocName)) {
            $docName = '<tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                                       <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
                                                                        <b>Documents</b>	
								       </td>
                                                                
                                                                <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
									' .
                    $oldDocName
                    . '	
								       </td>
								</tr>';
        }




        $msgbody = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" style="font-family: \'Helvetica Neue\', Helvetica, Arial, sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
<head>
<style type="text/css">
img {
max-width: 100%;
}
body {
-webkit-font-smoothing: antialiased; -webkit-text-size-adjust: none; width: 100% !important; height: 100%; line-height: 1.6em;
}
body {
background-color: #f6f6f6;
}
@media only screen and (max-width: 640px) {
  body {
    padding: 0 !important;
  }
  h1 {
    font-weight: 800 !important; margin: 20px 0 5px !important;
  }
  h2 {
    font-weight: 800 !important; margin: 20px 0 5px !important;
  }
  h3 {
    font-weight: 800 !important; margin: 20px 0 5px !important;
  }
  h4 {
    font-weight: 800 !important; margin: 20px 0 5px !important;
  }
  h1 {
    font-size: 22px !important;
  }
  h2 {
    font-size: 18px !important;
  }
  h3 {
    font-size: 16px !important;
  }
  .container {
    padding: 0 !important; width: 100% !important;
  }
  .content {
    padding: 0 !important;
  }
  .content-wrap {
    padding: 10px !important;
  }
  .invoice {
    width: 100% !important;
  }
  
}
</style>
</head>

<body style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; -webkit-font-smoothing: antialiased; -webkit-text-size-adjust: none; width: 100% !important; height: 100%; line-height: 1.6em; background-color: #f6f6f6; margin: 0;" bgcolor="#f6f6f6">

<table class="body-wrap" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; width: 100%; background-color: #f6f6f6; margin: 0;" bgcolor="#f6f6f6">
    <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
        <td style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0;" valign="top"></td>
		<td class="container" width="600" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; display: block !important; max-width: 600px !important; clear: both !important; margin: 0 auto;" valign="top">
			<div class="content" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; max-width: 600px; display: block; margin: 0 auto; padding: 20px;">
				<table class="main" width="100%" cellpadding="0" cellspacing="0" itemprop="action" itemscope itemtype="http://schema.org/ConfirmAction" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; border-radius: 3px; background-color: #fff; margin: 0; border: 1px solid #e9e9e9;" bgcolor="#fff">
                                       

                         <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                        <td class="content-wrap" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 20px;" valign="top">
                                       Dear ' . $suprUserName . ',<br>
							Ticket no ' . $rwTask[ticket_id] . ' has been assigned to ' . $suprUserName . ' from ' . $altrUserName . '. You are alternate user for performing task.<br><br>
                                                                               ' . $des . '
                                                         
                                    <table width="100%" cellpadding="0" cellspacing="0" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;" border="1">
                                                               
                                                             
                                                            <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                                       <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
									<b>Priority</b>	
								       </td>
                                                                  
                                                                   <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px" valign="top">
									' .
                $urgent . $medium . $normal
                . '	
								       </td>
								</tr>
                                                               <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                                       <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
									<b>Task Status</b>	
								       </td>
                                                                  
                                                                   <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
									' .
                $rwTask[task_status]
                . '	
								       </td>
								</tr>
                                                            <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                                       <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
									<b>Deadline</b>	
								       </td>
                                                                
                                                                <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
										' .
                $enddate
                . '
								       </td>
								</tr>
                                                            <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                                       <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px; " valign="top">
									<b>Workflow Name</b>	
								       </td>
                                                               
                                                                <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
									' .
                $wrkFlName
                . '	
								       </td>
								</tr>
                                                            <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                                       <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
										<b>Step Name<b>
								       </td>
                                                              
                                                                <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding:5px;" valign="top">
									' .
                $stpName
                . '	
								       </td>
								</tr>
                                                           
                                                            <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                                       <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
									<b>Task Name</b>	
								       </td>
                                                                
                                                                <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
									' .
                $rwWork['task_name']
                . '	
								       </td>
								</tr>
                                                ' . $docName . '
                                                            <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                                       <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
									<b>Submitted By</b>	
								       </td>
                                                               
                                                                <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
									' .
                $asinBy
                . '	
								       </td>
								</tr>
                                                            <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                                       <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
										<b>Assigned To</b>
								       </td>
                                                                
                                                                <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
									' .
                $asinUserName
                . '	
								       </td>
								</tr>
                                                            <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                                       <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
										<b>Supervisor</b>
								       </td>
                                                           
                                                                <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
									' .
                $suprUserName
                . '	
								       </td>
								</tr>
                                                             <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                                       <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
										<b>Alertnate User</b>
								       </td>
                                                                
                                                                <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
									' .
                $altrUserName
                . '	
								       </td>
								</tr>
                                                             <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                                       <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
										<b>Instruction</b>
								       </td>
                                                                
                                                                <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
									' .
                $rwWork[task_instructions]
                . '	
								       </td>
								</tr>
                                                         
                                                        </table><br>
                                                        (URL - http://'. $_SERVER['HTTP_HOST'] . ')
                                                       	<p>this is a system generated mail please do not reply.</p>
									
                                                               
                                                               
										&mdash; ' . $projectName . '
                                                                                <br> Admin 
                                                                                </td>
						</tr>
                                </table>
                               <div class="footer" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; width: 100%; clear: both; color: #999; margin: 0; padding: 5px;">
					<table width="100%" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                               <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                   <td class="aligncenter content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 12px; vertical-align: top; color: #999; text-align: center; margin: 0; padding: 0 0 5px;" align="center" valign="top"> 
                                                       <a href="#" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 12px; color: #999; text-decoration: underline; margin: 0;">
                                                          
                                                       </a>
                                                   </td>
						</tr>
                                        </table>
                                </div>
                        </div>
		</td>
        <td style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0;" valign="top">
            
        </td>
	</tr>
</table>
</body>
</html>';

        date_default_timezone_set('Asia/Kolkata');
        $date = date("Y-m-d H:i:s");
        require_once './application/PHPMailer/PHPMailerAutoload.php';
        $mail = new PHPMailer;
        $mail->isSMTP();
        $mail->SMTPDebug = 0;
        $mail->Debugoutput = 'html';
        $mail->Host = "mail.cbsl-india.com";
        $mail->Port = 587;
        $mail->SMTPAuth = true;
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        $mail->Username = "ezeefileadmin@cbsl-india.com";
        $mail->Password = "Kdcs@08065";
        $mail->setFrom('no-reply@cbsl-india.com', '' . $projectName . ' Admin');

        //$mail->addAddress($mailTo, $userName);

        $mail->addAddress($suprUserEmail, $suprUserName);
        $mail->addCC($asinUserEmail, $asinUserName);
        $mail->addCC($altrUserEmail, $altrUserName);
        $mail->Subject = 'Task assigned to Supervisor!';
        $mail->addBCC("ezeefileadmin@cbsl-india.com", "Admin");
        $sendCC = $asinUserEmail . ', ' . $altrUserEmail;

        $mail->msgHTML($msgbody);
        $mail->AltBody = 'New Notifiation from ' . $projectName . '';
        //$mail->addAttachment('PHPMailer/examples/images/phpmailer_mini.png');
        if (!$mail->send()) {
            echo "Mailer Error: " . $mail->ErrorInfo;
            return false;
        } else {

            $sender = $_SESSION['cdes_user_id'];
            $host = $_SERVER['REMOTE_ADDR'];
            $msgbody = mysqli_real_escape_string($db_con, $msgbody);
            $mailsent = mysqli_query($db_con, "insert into tbl_mail_list(`send_by`, `send_to`, `send_cc`, `send_bcc`, `subject`, `mail_body`, `alt_body`, `mail_time`, `ip`, `module_name`) "
                    . "value('$sender','$suprUserEmail','$sendCC','','Task assigned to Supervisor!','$msgbody','New Notifiation from $projectName','$date','$host','Assign Task to Superwiser')") or die('Error' . mysqli_error($db_con));
            return true;
        }
    } else {
        return false;
    }
}

function abortTask($ticket, $id, $wfid, $db_con, $projectName) {

    $task = mysqli_query($db_con, "select * from tbl_doc_assigned_wf where id='$id' and NextTask='5'") or die('Error:' . mysqli_error($db_con));
    if (mysqli_num_rows($task) > 0) {
        $rwTask = mysqli_fetch_assoc($task);

        $work = mysqli_query($db_con, "select * from tbl_task_master where task_id='$rwTask[task_id]'") or die('Error:' . mysqli_error($db_con));
        $rwWork = mysqli_fetch_assoc($work);

        if ($rwWork['priority_id'] == 1) {
            $urgent = 'Urgent';
        } else if ($rwWork['priority_id'] == 2) {
            $medium = 'Medium';
        } else if ($rwWork['priority_id'] == 3) {
            $normal = 'Normal';
        }

        $strtDate = strtotime($rwTask['start_date']);
        if ($rwWork['deadline_type'] == 'Date') {
            $endDate = $strtDate + $rwWork['deadline'] * 60 * 60;
        } else if ($rwWork['deadline_type'] == 'Days') {
            $endDate = $strtDate + $rwWork['deadline'] * 24 * 60 * 60;
        } else if ($rwWork['deadline_type'] == 'Hrs') {
            $endDate = $strtDate + $rwWork['deadline'] * 60 * 60;
        }
        $enddate = date('Y-m-d H:i:s', $endDate);

        $wfn = mysqli_query($db_con, "select * from tbl_workflow_master where workflow_id='$rwWork[workflow_id]'") or die('Error:' . mysqli_error($db_con));
        $rwWfn = mysqli_fetch_assoc($wfn);
        $wrkFlName = $rwWfn['workflow_name'];


        $stn = mysqli_query($db_con, "select * from tbl_step_master where step_id='$rwWork[step_id]'") or die('Error:' . mysqli_error($db_con));
        $rwStn = mysqli_fetch_assoc($stn);
        $stpName = $rwStn['step_name'];


        $dms = mysqli_query($db_con, "select * from tbl_document_master where doc_id='$rwTask[doc_id]'") or die('Error:' . mysqli_error($db_con));
        $rwDms = mysqli_fetch_assoc($dms);
        $oldDocName = $rwDms['old_doc_name'];

        //$user = mysqli_query($db_con, "select first_name,last_name from tbl_user_master where user_id='$rwTask[assign_by]'") or die('Error:'.mysqli_error($db_con));
        //$rwUser = mysqli_fetch_assoc($user);
        //$asinBy = $rwUser['first_name'] . ' ' . $rwUser['last_name'];
        //$tskId = $rwTask['task_id'];
        //$taskRe = mysqli_query($db_con, "select * from tbl_task_master where task_id='$tskId'") or die('Error:'.mysqli_error($db_con));
        //$rwTaskRe = mysqli_fetch_assoc($taskRe);

        $asinUser = $rwWork['assign_user'];
        $asinUserId = mysqli_query($db_con, "select * from tbl_user_master where user_id='$asinUser'") or die('Error:' . mysqli_error($db_con));
        $rwasinUserId = mysqli_fetch_assoc($asinUserId);
        $asinUserEmail = $rwasinUserId['user_email_id'];
        $asinUserName = $rwasinUserId['first_name'] . ' ' . $rwasinUserId['last_name'];

        $altruser = $rwWork['alternate_user'];
        $altrUserId = mysqli_query($db_con, "select * from tbl_user_master where user_id='$altruser'") or die('Error:' . mysqli_error($db_con));
        $rwaltrUserId = mysqli_fetch_assoc($altrUserId);
        $altrUserEmail = $rwaltrUserId['user_email_id'];
        $altrUserName = $rwaltrUserId['first_name'] . ' ' . $rwaltrUserId['last_name'];

        $suprvsr = $rwWork['supervisor'];
        $suprUserId = mysqli_query($db_con, "select * from tbl_user_master where user_id='$suprvsr'") or die('Error:' . mysqli_error($db_con));
        $rwsuprUserId = mysqli_fetch_assoc($suprUserId);
        $suprUserEmail = $rwsuprUserId['user_email_id'];
        $suprUserName = $rwsuprUserId['first_name'] . ' ' . $rwsuprUserId['last_name'];

        $taskDesc = $rwTask['task_remarks'];

        //get asign by detail      
        $getAsinBy = mysqli_query($db_con, "select tum.user_email_id, tum.first_name, tum.last_name, tda.start_date from tbl_doc_assigned_wf tda inner join tbl_user_master tum on tda.assign_by = tum.user_id where tda.ticket_id = '$ticket'") or die('Error:' . mysqli_error($db_con));
        $rwgetAsinBy = mysqli_fetch_assoc($getAsinBy);
        $getAsinByEmail = $rwgetAsinBy['user_email_id'];
        $getAsinByName = $rwgetAsinBy['first_name'] . ' ' . $rwgetAsinBy['last_name'];
        $getAsignDate = $rwgetAsinBy['start_date'];



        //get work flow name
        $workflowName = mysqli_query($db_con, "select * from tbl_workflow_master where workflow_id='$wfid'") or die('Error:' . mysqli_error($db_con));
        $rwworkflowName = mysqli_fetch_assoc($workflowName);
        $workflwName = $rwworkflowName['workflow_name'];

        //get action by, action time and comment
        //inner join tbl_user_master tum on tum.user_id = tda.action_by inner join tbl_task_master ttm on ttm.task_id = tda.task_id

        $taskHistry = mysqli_query($db_con, "select * from tbl_doc_assigned_wf tda inner join tbl_task_comment ttc on tda.ticket_id = ttc.tickt_id and tda.task_id = ttc.task_id inner join tbl_user_master tum on tum.user_id = tda.action_by inner join tbl_task_master ttm on ttm.task_id = tda.task_id where tda.ticket_id='$ticket' and tda.NextTask='1' order by tda.action_time desc") or die('Error:' . mysqli_error($db_con));

        $aprBy = '';

        while ($rwTaskHistry = mysqli_fetch_assoc($taskHistry)) {

            //$actionByName = $rwTaskHistry['first_name'].' '.$rwTaskHistry['last_name'];
            // $actionByDate = $rwTaskHistry['action_time'];
            //$actionByComment = $rwTaskHistry['comment'];

            $aprBy .= "<b>Approved by: </b>" . $rwTaskHistry['first_name'] . ' ' . $rwTaskHistry['last_name'] . " <br />";
            $aprBy .= "<b>At Task: </b>" . $rwTaskHistry['task_name'] . "<br />";
            $aprBy .= "<b>On: </b>" . $rwTaskHistry['action_time'] . "<br />";
            $aprBy .= "<b>Comment: </b>" . $rwTaskHistry['comment'] . "<br/><br/>";

            echo "<br/><br/><br/><br/>><br/>";
        }

        if (!empty($taskDesc)) {
            $des = '<b>Description :</b> <p>' . $taskDesc . '</p>';
        }

        if (!empty($oldDocName)) {
            $docName = '<tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                                       <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
                                                                        <b>Documents</b>	
								       </td>
                                                             
                                                                <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
									' .
                    $oldDocName
                    . '	
								       </td>
								</tr>';
        }

        $msgbody = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" style="font-family: \'Helvetica Neue\', Helvetica, Arial, sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
<head>
<style type="text/css">
img {
max-width: 100%;
}
body {
-webkit-font-smoothing: antialiased; -webkit-text-size-adjust: none; width: 100% !important; height: 100%; line-height: 1.6em;
}
body {
background-color: #f6f6f6;
}
@media only screen and (max-width: 640px) {
  body {
    padding: 0 !important;
  }
  h1 {
    font-weight: 800 !important; margin: 20px 0 5px !important;
  }
  h2 {
    font-weight: 800 !important; margin: 20px 0 5px !important;
  }
  h3 {
    font-weight: 800 !important; margin: 20px 0 5px !important;
  }
  h4 {
    font-weight: 800 !important; margin: 20px 0 5px !important;
  }
  h1 {
    font-size: 22px !important;
  }
  h2 {
    font-size: 18px !important;
  }
  h3 {
    font-size: 16px !important;
  }
  .container {
    padding: 0 !important; width: 100% !important;
  }
  .content {
    padding: 0 !important;
  }
  .content-wrap {
    padding: 10px !important;
  }
  .invoice {
    width: 100% !important;
  }
}
</style>
</head>

<body style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; -webkit-font-smoothing: antialiased; -webkit-text-size-adjust: none; width: 100% !important; height: 100%; line-height: 1.6em; background-color: #f6f6f6; margin: 0;" bgcolor="#f6f6f6">

<table class="body-wrap" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; width: 100%; background-color: #f6f6f6; margin: 0;" bgcolor="#f6f6f6">
    <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
        <td style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0;" valign="top"></td>
		<td class="container" width="600" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; display: block !important; max-width: 600px !important; clear: both !important; margin: 0 auto;" valign="top">
			<div class="content" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; max-width: 600px; display: block; margin: 0 auto; padding: 20px;">
				<table class="main table table-bordered" width="100%" cellpadding="0" cellspacing="0" itemprop="action" itemscope itemtype="http://schema.org/ConfirmAction" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; border-radius: 3px; background-color: #fff; margin: 0; border: 1px solid #e9e9e9;" bgcolor="#fff">
                                    <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                        <td class="content-wrap" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 20px;" valign="top">
							         Dear ' . $getAsinByName . ',</br>
                                                                 This is to inform that <b>' . $workflwName . '</b> with Ticket Number <b>' . $ticket . '</b> has been aborted.<br /></br />
                                
								<table width="100%" cellpadding="0" cellspacing="0" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;" border="1">
                                                               
                                                             
                                                            <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                                       <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
									<b>Priority</b>	
								       </td>
                                                                  
                                                                   <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px" valign="top">
									' .
                $urgent . $medium . $normal
                . '	
								       </td>
								</tr>
                                                               <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                                       <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
									<b>Task Status</b>	
								       </td>
                                                                  
                                                                   <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
									' .
                $rwTask[task_status]
                . '	
								       </td>
								</tr>
                                                            <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                                       <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
									<b>Deadline</b>	
								       </td>
                                                                
                                                                <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
										' .
                $enddate
                . '
								       </td>
								</tr>
                                                            <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                                       <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px; " valign="top">
									<b>Workflow Name</b>	
								       </td>
                                                               
                                                                <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
									' .
                $wrkFlName
                . '	
								       </td>
								</tr>
                                                            <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                                       <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
										<b>Step Name<b>
								       </td>
                                                              
                                                                <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding:5px;" valign="top">
									' .
                $stpName
                . '	
								       </td>
								</tr>
                                                           
                                                            <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                                       <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
									<b>Task Name</b>	
								       </td>
                                                                
                                                                <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
									' .
                $rwWork['task_name']
                . '	
								       </td>
								</tr>
                                                ' . $docName . '
                                                            <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                                       <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
									<b>Submitted By</b>	
								       </td>
                                                               
                                                                <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
									' .
                $getAsinByName
                . '	
								       </td>
								</tr>
                                                            <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                                       <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
										<b>Assigned To</b>
								       </td>
                                                                
                                                                <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
									' .
                $asinUserName
                . '	
								       </td>
								</tr>
                                                            <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                                       <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
										<b>Supervisor</b>
								       </td>
                                                           
                                                                <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
									' .
                $suprUserName
                . '	
								       </td>
								</tr>
                                                             <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                                       <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
										<b>Alertnate User</b>
								       </td>
                                                                
                                                                <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
									' .
                $altrUserName
                . '	
								       </td>
								</tr>
                                                             <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                                       <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
										<b>Instruction</b>
								       </td>
                                                                
                                                                <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
									' .
                $rwWork[task_instructions]
                . '	
								       </td>
								</tr>
                                                         
                                                        </table></br>
                                                        
                                                          ' . $aprBy . '
                                                         </br>
                                                        <b> Submitted BY: </b>' . $getAsinByName . ' <br />
                                                        <b> Submission On: </b>' . $getAsignDate . ' <br />
                                                          ' . $des . '<br>
                                                              
                                                      
                                                      (URL - http://'. $_SERVER['HTTP_HOST'] . ')    
                                              	<p>this is a system generated mail please do not reply.</p>
									
                                                               
                                                               
										&mdash; ' . $projectName . '
                                                                                <br> Admin 
                                                                                </td>
						</tr>
                                </table>
                               <div class="footer" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; width: 100%; clear: both; color: #999; margin: 0; padding: 5px;">
					<table width="100%" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                               <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                   <td class="aligncenter content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 12px; vertical-align: top; color: #999; text-align: center; margin: 0; padding: 0 0 5px;" align="center" valign="top"> 
                                                       <a href="#" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 12px; color: #999; text-decoration: underline; margin: 0;">
                                                          
                                                       </a>
                                                   </td>
						</tr>
                                        </table>
                                </div>
                        </div>
		</td>
        <td style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0;" valign="top">
            
        </td>
	</tr>
</table>
</body>
</html>';

        date_default_timezone_set('Asia/Kolkata');
        $date = date("Y-m-d H:i:s");
        require_once './application/PHPMailer/PHPMailerAutoload.php';
        $mail = new PHPMailer;
        $mail->isSMTP();
        $mail->SMTPDebug = 0;
        $mail->Debugoutput = 'html';
        $mail->Host = "mail.cbsl-india.com";
        $mail->Port = 587;
        $mail->SMTPAuth = true;
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        $mail->Username = "ezeefileadmin@cbsl-india.com";
        $mail->Password = "Kdcs@08065";
        $mail->setFrom('no-reply@cbsl-india.com', 'admin');

        //$mail->addAddress($mailTo, $userName);
        $mail->addAddress($getAsinByEmail, $getAsinByName);
        $cc = array();
        $getAprovBy = mysqli_query($db_con, "select tum.first_name, tum.last_name, tum.user_email_id FROM tbl_doc_assigned_wf tda INNER JOIN  tbl_user_master tum ON tda.action_by = tum.user_id where tda.ticket_id = '$ticket' ") or die('Error:' . mysqli_error($db_con));
        while ($rwgetAprovBy = mysqli_fetch_assoc($getAprovBy)) {

            $firstName = $rwgetAprovBy['first_name'];
            $lastName = $rwgetAprovBy['last_name'];
            $fulName = $firstName . ' ' . $lastName;
            $AprovByEmail = $rwgetAprovBy['user_email_id'];
            $cc[] = $AprovByEmail;
            $mail->addCC($AprovByEmail, $fulName);
        }
        $mail->addBCC("ezeefileadmin@cbsl-india.com", "Admin");
        $mail->Subject = 'Task has been Aborted !';

        $mail->msgHTML($msgbody);
        $mail->AltBody = 'New Notifiation from ' . $projectName . '';
        //$mail->addAttachment('PHPMailer/examples/images/phpmailer_mini.png');
        if (!$mail->send()) {
            echo "Mailer Error: " . $mail->ErrorInfo;
            return false;
        } else {

            $sendCC = implode(',', $cc);
            $sender = $_SESSION['cdes_user_id'];
            $host = $_SERVER['REMOTE_ADDR'];
            $msgbody = mysqli_real_escape_string($db_con, $msgbody);
            $mailsent = mysqli_query($db_con, "insert into tbl_mail_list(`send_by`, `send_to`, `send_cc`, `send_bcc`, `subject`, `mail_body`, `alt_body`, `mail_time`, `ip`, `module_name`) "
                    . "value('$sender','$getAsinByEmail','$sendCC','','Task has been Aborted !','$msgbody','New Notifiation from $projectName','$date','$host','Task is Aborted')") or die('Error' . mysqli_error($db_con));
            return true;
        }
    }
}

?>