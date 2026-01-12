<?php
function mailUserCreate($superiorEmail, $superiorName, $email, $username, $user_id, $password, $subject, $db_con, $projectName, $Cuser) {

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
                        <div class="header" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; width: 96.2%; clear: both; color: #fff; margin: 0; padding: 10px; background: #425f73; border-radius: 5px 6px 0px 0px !important;">
                            <table width="100%" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                    <td class="aligncenter content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 12px; vertical-align: top; color: #999; text-align: center; margin: 0; padding: 5px 40px;" valign="top">
                                        <h2 style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 30px; color: #fff; margin: 0; text-align: left;"> ' . $projectName . '</h2></td>
                                </tr></table></div>
                        <table class="main" width="100%" cellpadding="0" cellspacing="0" itemprop="action" itemscope itemtype="http://schema.org/ConfirmAction" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; border-radius: 3px; background-color: #fff; margin: 0; border-bottom: 1px solid #bec0c1;" bgcolor="#fff"><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><td class="content-wrap" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px 50px 0px;" valign="top">
                                    <meta itemprop="name" content="Confirm Email" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;" />
                                    <table width="100%" cellpadding="0" cellspacing="0" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                        <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                            <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 20px;" valign="top"><br>
                                                             <h4>New user has been created in ' . $projectName . ' </h4>
                                                             <strong>Username </strong> - ' . $username . '
                                                             <p><strong>Username </strong> - ' . $email . '</p>
                                                             <p><strong>Password </strong> - ' . $password . '</p>
                                                        </td>
                                                    </tr><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0; font-weight: bold;"><td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 20px;" valign="top">
                                                        Thank You,
                                                        <br> ' . $Cuser . '.
                                                    </td>
                                                </tr></table></td>
                                                </tr></table><div class="footer" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; width: 100%; clear: both; color: #999; margin: 0; padding: 1px;">
                                                    <table width="100%" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                            <td class="aligncenter content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 12px; vertical-align: top; color: #999; text-align: left; margin: 0; padding: 0 0 30px;" align="center" valign="top"> <a href="#" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 13px; color: #999; margin: 0; text-decoration: none;"><i>This is a system generated mail please do not reply.</i></a><a href="http://' . $_SERVER['HTTP_HOST'] . '" style="color:blue;">http://' . $_SERVER['HTTP_HOST'] . '</a></td>
                                                        </tr></table></div>
                                                       </div>
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
    $mail->Host = EMAIL_HOST;
    $mail->Port = EMAIL_PORT;
    $mail->SMTPAuth = true;
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );
    $mail->Username = EMAIL_USERNAME;
    $mail->Password = EMAIL_PASSWORD;
    $mail->setFrom(EMAIL_SETFROM, 'System');
    //$mail->addReplyTo('', '');
    $mail->addAddress($email, $username);
    $mail->addAddress($superiorEmail, $superiorName);
    //$mail->addBCC('ezeefileadmin@cbsl-india.com');
    $mail->Subject = $subject;
    $mail->msgHTML($msgbody);
    $mail->AltBody = 'New Notifiation from ' . $projectName . '';
    $mail->CharSet = 'UTF-8';
    //$mail->addAttachment('PHPMailer/examples/images/phpmailer_mini.png');
    if (!$mail->send()) {
        return false;
    } else {
        $sender = $_SESSION['cdes_user_id'];
        $host = $_SERVER['REMOTE_ADDR'];
        $msgbody = mysqli_real_escape_string($db_con, $msgbody);
        mysqli_set_charset($db_con, "utf8");
        $mailsent = mysqli_query($db_con, "insert into tbl_mail_list(`send_by`, `send_to`, `send_cc`, `send_bcc`, `subject`, `mail_body`, `alt_body`, `mail_time`, `ip`, `module_name`) "
                . "value('$sender','$email','$superiorEmail','','$subject','$msgbody','New Notifiation from $projectName','$date','$host','User Creation')") or die('Error' . mysqli_error($db_con));
        return true;
    }
}

function mailPasschange($txt, $to, $projectName, $username) {

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
                        <div class="header" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; width:96.2%; clear: both; color: #fff; margin: 0; padding: 10px; background: #193860; border-radius: 5px 6px 0px 0px !important;">
                            <table width="100%" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                    <td class="aligncenter content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 12px; vertical-align: top; color: #999; text-align: center; margin: 0; padding: 5px 40px;" valign="top">
                                       <h2 style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 30px; color: #fff; margin: 0; text-align: left;"> ' . $projectName . '</h2></td>
                                </tr></table></div>
                                <table class="main" width="100%" cellpadding="0" cellspacing="0" itemprop="action" itemscope itemtype="http://schema.org/ConfirmAction" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; border-radius: 3px; background-color: #fff; margin: 0; border-bottom: 1px solid #bec0c1;" bgcolor="#fff"><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><td class="content-wrap" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px 50px 0px;" valign="top">
                                    <meta itemprop="name" content="Confirm Email" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;" />
                                    <table width="100%" cellpadding="0" cellspacing="0" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                        <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                            <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 20px;" valign="top"><br>
                                                             <h4>Your OTP for changing password on ' . $projectName . ' </h4>
                                                             <p><strong>OTP </strong> - ' . $txt . '</p>
							    <p><strong>(Your OTP Expired Within 10 min.)</strong> </p>
                                                        </td>
                                                    </tr><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0; font-weight: bold;"><td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 30px;" valign="top">
                                                        Thank You,
                                                        <br> ' . $projectName . ' Team.
                                                    </td>
                                                </tr></table></td>
                                                </tr></table><div class="footer" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; width: 100%; clear: both; color: #999; margin: 0; padding: 1px;">
                                                    <table width="100%" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                            <td class="aligncenter content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 12px; vertical-align: top; color: #999; text-align: left; margin: 0; padding: 0 0 20px;" align="center" valign="top"> <a href="#" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 13px; color: #999; margin: 0; text-decoration: none;"><i>This is a system generated mail please do not reply. Track, manage and store your documents and reduce paper.</i></a> <a href="http://' . $_SERVER['HTTP_HOST'] . '" style="color:blue;">http://' . $_SERVER['HTTP_HOST'] . '</a></td>
                                                        </tr></table></div>
                                                        </div>
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
    $mail->Host = EMAIL_HOST;
    $mail->Port = EMAIL_PORT;
    $mail->SMTPAuth = true;
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );
    $mail->Username = EMAIL_USERNAME;
    $mail->Password = EMAIL_PASSWORD;
    $mail->setFrom(EMAIL_SETFROM, 'System');
    //$mail->addReplyTo('', '');
    $mail->addAddress($to, $username);
    //$mail->addAddress($superiorEmail, $superiorName);
    //$mail->addBCC('soft.dev5@cbsl-india.com');
    $mail->Subject = "OTP for password change request.";
    $mail->msgHTML($msgbody);
    $mail->AltBody = 'New Notifiation from ' . $projectName . '';
    $mail->CharSet = 'UTF-8';
    //$mail->addAttachment('PHPMailer/examples/images/phpmailer_mini.png');
    if (!$mail->send()) {
        return false;
    } else {

        return true;
    }
}

function mailResetPass($to, $pass, $projectName, $username) {

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
                        <div class="header" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; width:96.2%; clear: both; color: #fff; margin: 0; padding: 10px; background: #193860; border-radius: 5px 6px 0px 0px !important;">
                            <table width="100%" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                    <td class="aligncenter content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 12px; vertical-align: top; color: #999; text-align: center; margin: 0; padding: 5px 40px;" valign="top">
                                       <h2 style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 30px; color: #fff; margin: 0; text-align: left;"> ' . $projectName . '</h2></td>
                                </tr></table></div>
                                <table class="main" width="100%" cellpadding="0" cellspacing="0" itemprop="action" itemscope itemtype="http://schema.org/ConfirmAction" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; border-radius: 3px; background-color: #fff; margin: 0; border-bottom: 1px solid #bec0c1;" bgcolor="#fff"><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><td class="content-wrap" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px 50px 0px;" valign="top">
                                    <meta itemprop="name" content="Confirm Email" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;" />
                                    <table width="100%" cellpadding="0" cellspacing="0" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                        <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                            <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 20px;" valign="top"><br>
                                                             <h4>Your new login password for ' . $projectName . '</h4>
                                                             <p><strong>Password </strong> - ' . $pass . '</p>
							   </td>
                                                    </tr><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0; font-weight: bold;"><td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 30px;" valign="top">
                                                        Thank You,
                                                        <br> ' . $projectName . ' Team.
                                                    </td>
                                                </tr></table></td>
                                                </tr></table><div class="footer" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; width: 100%; clear: both; color: #999; margin: 0; padding: 1px;">
                                                    <table width="100%" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                            <td class="aligncenter content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 12px; vertical-align: top; color: #999; text-align: left; margin: 0; padding: 0 0 20px;" align="center" valign="top"> <a href="#" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 13px; color: #999; margin: 0; text-decoration: none;"><i>This is a system generated mail please do not reply. Track, manage and store your documents and reduce paper.</i></a> <a href="http://' . $_SERVER['HTTP_HOST'] . '" style="color:blue;">http://' . $_SERVER['HTTP_HOST'] . '</a></td>
                                                        </tr></table></div>
                                                        </div>
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
    $mail->Host = EMAIL_HOST;
    $mail->Port = EMAIL_PORT;
    $mail->SMTPAuth = true;
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );
    $mail->Username = EMAIL_USERNAME;
    $mail->Password = EMAIL_PASSWORD;
    $mail->setFrom(EMAIL_SETFROM, 'System');
    //$mail->addReplyTo('', '');
    $mail->addAddress($to, $username);
    //$mail->addAddress($superiorEmail, $superiorName);
    //$mail->addBCC('soft.dev5@cbsl-india.com');
    $mail->Subject = "New Login password for $projectName";
    $mail->msgHTML($msgbody);
    $mail->AltBody = 'New Notifiation from ' . $projectName . '';
    $mail->CharSet = 'UTF-8';
    //$mail->addAttachment('PHPMailer/examples/images/phpmailer_mini.png');
    if (!$mail->send()) {
        return false;
    } else {

        return true;
    }
}

function feedbackMail($from, $fbackMsg, $UserName, $des, $projectName) {

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
                        <div class="header" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; width:96.2%; clear: both; color: #fff; margin: 0; padding: 10px; background: #193860; border-radius: 5px 6px 0px 0px !important;">
                            <table width="100%" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                    <td class="aligncenter content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 12px; vertical-align: top; color: #999; text-align: center; margin: 0; padding: 5px 40px;" valign="top">
                                       <h2 style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 30px; color: #fff; margin: 0; text-align: left;"> ' . $projectName . '</h2></td>
                                </tr></table></div>
                                <table class="main" width="100%" cellpadding="0" cellspacing="0" itemprop="action" itemscope itemtype="http://schema.org/ConfirmAction" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; border-radius: 3px; background-color: #fff; margin: 0; border-bottom: 1px solid #bec0c1;" bgcolor="#fff"><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><td class="content-wrap" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px 50px 0px;" valign="top">
                                    <meta itemprop="name" content="Confirm Email" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;" />
                                    <table width="100%" cellpadding="0" cellspacing="0" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                        <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                            <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 20px;" valign="top"><br>
                                                             <h4>New feeedback message arrived from ' . $projectName . '</h4>
                                                             <p><strong>Message :-</strong>  ' . $fbackMsg . '</p>
							   </td>
                                                    </tr><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0; font-weight: bold;"><td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 30px;" valign="top">
                                                        Thank You,
                                                        <br> ' . $UserName . '<br>
                                                         &mdash; ' . $des . '
                                                    </td>
                                                </tr></table></td>
                                                </tr></table><div class="footer" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; width: 100%; clear: both; color: #999; margin: 0; padding: 1px;">
                                                    <table width="100%" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                            <td class="aligncenter content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 12px; vertical-align: top; color: #999; text-align: left; margin: 0; padding: 0 0 20px;" align="center" valign="top"> <a href="#" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 13px; color: #999; margin: 0; text-decoration: none;"><i>This is a system generated mail please do not reply. Track, manage and store your documents and reduce paper.</i></a> <a href="http://' . $_SERVER['HTTP_HOST'] . '" style="color:blue;">http://' . $_SERVER['HTTP_HOST'] . '</a></td>
                                                        </tr></table></div>
                                                        </div>
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
    $mail->Host = EMAIL_HOST;
    $mail->Port = EMAIL_PORT;
    $mail->SMTPAuth = true;
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );
    $mail->Username = EMAIL_USERNAME;
    $mail->Password = EMAIL_PASSWORD;
    $mail->setFrom(EMAIL_SETFROM, $projectName);
    //$mail->addReplyTo('', '');
    $mail->addAddress('support@ezeedigitalsolutions.in', 'Support');
    $mail->addBCC('ezeefileadmin@cbsl-india.com');
    $mail->Subject = "User feedback message";
    $mail->msgHTML($msgbody);
    $mail->AltBody = 'New Notifiation from ' . $projectName . '';
    $mail->CharSet = 'UTF-8';
    //$mail->addAttachment('PHPMailer/examples/images/phpmailer_mini.png');
    if (!$mail->send()) {
        return false;
    } else {

        return true;
    }
}

function assignTask($ticket, $id, $db_con, $projectName) {

    

    mysqli_set_charset($db_con, "utf8");
    $task = mysqli_query($db_con, "select * from tbl_doc_assigned_wf where id='$id' and NextTask='0'") or die('Error:' . mysqli_error($db_con));

    if (mysqli_num_rows($task) > 0) {

        // echo 'heloppp'; die;

        $rwTask = mysqli_fetch_assoc($task);
        mysqli_set_charset($db_con, "utf8");
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
									'.$prirty. '	
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
        mysqli_set_charset($db_con, "utf8");
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
                                                        (URL - http://' . $_SERVER['HTTP_HOST'] . ') <br>
                                                       	<p>This is a System Generated mail please do not reply.</p>
									
                                                               
                                                               
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
        $mail->Host = EMAIL_HOST;
        $mail->Port = EMAIL_PORT;
        $mail->SMTPAuth = true;
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        $mail->Username = EMAIL_USERNAME;
        $mail->Password = EMAIL_PASSWORD;
        $mail->setFrom(EMAIL_SETFROM, 'admin');

        //$mail->addAddress($mailTo, $userName);
        $mail->addAddress($asinUserEmail, $asinUserName);
        $mail->addCC($altrUserEmail, $altrUserName);
        $mail->addCC($suprUserEmail, $suprUserName);
        $mail->addBCC("ezeefileadmin@cbsl-india.com", "Admin");
        $sendCC = $altrUserEmail . ',' . $suprUserEmail;

        $mail->Subject = $rwWork['task_name'] . ' ' . 'Task Assigned in ' . $wrkFlName . ' WorkFlow';

        $mail->msgHTML($msgbody);
        $mail->AltBody = 'New Notifiation from ' . $projectName . '';
        $mail->CharSet = 'UTF-8';
        //$mail->addAttachment('PHPMailer/examples/images/phpmailer_mini.png');
        if (!$mail->send()) {
            //echo "Mailer Error: " . $mail->ErrorInfo;
            return false;
        } else {
            $sender = $_SESSION['cdes_user_id'];
            $host = $_SERVER['REMOTE_ADDR'];
            $msgbody = mysqli_real_escape_string($db_con, $msgbody);
            mysqli_set_charset($db_con, "utf8");
            $mailsent = mysqli_query($db_con, "insert into tbl_mail_list(`send_by`, `send_to`, `send_cc`, `send_bcc`, `subject`, `mail_body`, `alt_body`, `mail_time`, `ip`, `module_name`) "
                    . "value('$sender','$asinUserEmail','$sendCC','','New Task Assigned !','$msgbody','New Notifiation from $projectName','$date','$host','Assign Task')") or die('Error' . mysqli_error($db_con));
            return true;
        }
    }
}

//assignTask(61, $db_con);


function rejectTask($id, $taskId, $ticket, $db_con, $projectName, $comment, $doc_id) {

    //echo "<script>alert('".$ticket."')</script>";
    // return 1;
    mysqli_set_charset($db_con, "utf8");
    $task = mysqli_query($db_con, "select * from tbl_doc_assigned_wf where id='$id' and NextTask=2") or die('Error:' . mysqli_error($db_con));
    if (mysqli_num_rows($task) > 0) {
        $rwTask = mysqli_fetch_assoc($task);
        $ticketId = $rwTask['ticket_id'];
        mysqli_set_charset($db_con, "utf8");
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
        //echo "SELECT old_doc_name,doc_path,doc_name FROM `tbl_document_master` WHERE doc_id='$rwTask[doc_id]'";
        $dc_paths = mysqli_query($db_con, "SELECT old_doc_name,doc_path,doc_name FROM `tbl_document_master` WHERE doc_id='$rwTask[doc_id]'") or die('Error:' . mysqli_error($db_con));
        //die;
        $dcPathsRow = mysqli_fetch_array($dc_paths);
        $dcPath = $dcPathsRow['doc_path'];
        $dcName = $dcPathsRow['old_doc_name'];
        $docName = explode('_', $dcPathsRow['doc_name']);
        $num = count($docName);
        if ($num == 1) {
            $updateDocName = $docName[0] . '_' . $rwTask['doc_id'] . '_' . $docName[1];
        } else {
            $updateDocName = $docName[0] . '_' . $rwTask['doc_id'];
        }
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
									' . $comment . '	
								       </td>
								</tr>
                                                                <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                                       <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
										<b>attachments</b>
								       </td>';
        $msgbody .= '</table><br>
                                                        (URL - http://' . $_SERVER['HTTP_HOST'] . ') <br>
                                                       	<p>This is a System Generated mail please do not reply.</p>
									
                                                               
                                                               
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
        $mail->Host = EMAIL_HOST;
        $mail->Port = EMAIL_PORT;
        $mail->SMTPAuth = true;
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        $mail->Username = EMAIL_USERNAME;
        $mail->Password = EMAIL_PASSWORD;
        $mail->setFrom(EMAIL_SETFROM, 'admin');

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
        $file = 'extract-here/' . $dcPath;
        getDocumentFromFTP($file, $dcPath);
        //echo file_get_contents($file);
        $mail->addStringAttachment(file_get_contents($file), $dcName);
        //die;
        //$mail->addAttachment($file,'application/octet-stream');
        while ($version_row = mysqli_fetch_array($fileVersion)) {

            $docPaths = "extract-here/" . $version_row['doc_path'];
            getDocumentFromFTP($docPaths, $version_row['doc_path']);
            $docname = $version_row['old_doc_name'] . '.' . $version_row['doc_extn'];
            $mail->addStringAttachment(file_get_contents($docPaths), $docname);
            //$mail->addAttachment($docPaths,'application/octet-stream');
        }

        $mail->AltBody = 'New Notifiation from ' . $projectName . '';
        $mail->CharSet = 'UTF-8';
        //$mail->addAttachment('PHPMailer/examples/images/phpmailer_mini.png');

        if (!$mail->send()) {
            //echo "Mailer Error: " . $mail->ErrorInfo;
            return false;
        } else {
            $sendCC = implode(', ', $cc);
            $sender = $_SESSION['cdes_user_id'];
            $host = $_SERVER['REMOTE_ADDR'];
            $msgbody = mysqli_real_escape_string($db_con, $msgbody);
            mysqli_set_charset($db_con, "utf8");
            $mailsent = mysqli_query($db_con, "insert into tbl_mail_list(`send_by`, `send_to`, `send_cc`, `send_bcc`, `subject`, `mail_body`, `alt_body`, `mail_time`, `ip`, `module_name`) "
                    . "value('$sender','$asinByEmail','$sendCC','','Task Rejected !','$msgbody','New Notifiation from $projectName','$date','$host','Reject Task')") or die('Error' . mysqli_error($db_con));
            return true;
        }
    }
}

function completeTask($ticket, $id, $wfid, $db_con, $projectName) {
mysqli_set_charset($db_con, "utf8");
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
        mysqli_set_charset($db_con, "utf8");
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

        mysqli_set_charset($db_con, "utf8");

        //get work flow name
        $workflowName = mysqli_query($db_con, "select * from tbl_workflow_master where workflow_id='$wfid'") or die('Error:' . mysqli_error($db_con));
        $rwworkflowName = mysqli_fetch_assoc($workflowName);
        $workflwName = $rwworkflowName['workflow_name'];
        $dc_paths = mysqli_query($db_con, "SELECT old_doc_name,doc_path,doc_name,doc_extn,File_Number FROM `tbl_document_master` WHERE doc_id='$rwTask[doc_id]'") or die('Error:' . mysqli_error($db_con));
        //die;

        $dcPathsRow = mysqli_fetch_array($dc_paths);
        $dcPath = $dcPathsRow['doc_path'];
        $strgName = mysqli_query($db_con, "select sl_name from tbl_storage_level where sl_id='$dcPathsRow[doc_name]'");
        $fetchStrg = mysqli_fetch_assoc($strgName);
        // echo "test and run".$dcPathsRow['doc_name'];
        
        //sk@5032019 : set document name with extension.
         //$dcNames = $dcPathsRow['old_doc_name'];
        $dc_ext = strtolower(pathinfo($dcPathsRow['old_doc_name'], PATHINFO_EXTENSION));
        $doc_extn = $dcPathsRow['doc_extn'];
        $dcName = ($dc_ext == $doc_extn ? $dcPathsRow['old_doc_name'] : $dcPathsRow['old_doc_name'].'.'. $doc_extn);
        //end@sk
        $docName = explode('_', $dcPathsRow['doc_name']);
        $num = count($docName);
        if ($num == 2) {

            $updateDocName = $docName[0] . '_' . $rwTask['doc_id'] . '_' . $docName[1];
        } else {

            $updateDocName = $docName[0] . '_' . $rwTask['doc_id'];
        }
        $fileVersion = mysqli_query($db_con, "SELECT old_doc_name,doc_path,doc_name,doc_extn FROM `tbl_document_master` WHERE doc_name='$updateDocName'") or die('Error:' . mysqli_error($db_con));

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

            echo "<br/><br/><br/><br/><br/>";
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

        if (!empty($dcPathsRow['File_Number'])) {
            $fileNumber = '<tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                                       <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
                                                                        <b>File Number</b>	
								       </td>
                                                             
                                                                <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
									' .
                    $dcPathsRow[File_Number]
                    . '	
								       </td>
								</tr>';
        }
        if (!empty($updateDocName)) {
            $fileNum = '<tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                                       <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
                                                                        <b>File Location</b>	
								       </td>
                                                             
                                                                <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
									' .
                    $fetchStrg[sl_name]
                    . '	(URL - http://' . $_SERVER['HTTP_HOST'] . '/storage?id=' . urlencode(base64_encode($rwTask[doc_id])) . ')
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

                                                           
 
                                                ' . $docName . '
                                                            <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                                       <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
									<b>Created By</b>	
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
                                                         
                                                                ' . $fileNumber . $fileNum . '
                                                                    
                                                                
                                                         
                                                        </table></br>
                                                        
                                                          ' . $aprBy . '
                                                         </br>
                                                        <b> Submitted BY: </b>' . $getAsinByName . ' <br />
                                                        <b> Submission On: </b>' . $getAsignDate . ' <br />
                                                        
                                                              
                                                      (URL - http://' . $_SERVER['HTTP_HOST'] . ')
                                                          
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
        $mail->Host = EMAIL_HOST;
        $mail->Port = EMAIL_PORT;
        $mail->SMTPAuth = true;
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        $mail->Username = EMAIL_USERNAME;
        $mail->Password = EMAIL_PASSWORD;
        $mail->setFrom(EMAIL_SETFROM, 'admin');

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

        $mail->Subject = $rwWork['task_name'] . ' ' . 'has been Completed !';

        $mail->msgHTML($msgbody);
        
         $removeFiles=array();
         
        $file = 'extract-here/' . $dcPath;

       getDocumentFromFTP($file, $dcPath);
        
       
       $removeFiles[]=$file;
        //echo file_get_contents($file);
        $mail->addStringAttachment(file_get_contents($file), $dcName);
        //die;
        //$mail->addAttachment($file,'application/octet-stream');
        while ($version_row = mysqli_fetch_array($fileVersion)) {

            $docPaths = "extract-here/" . $version_row['doc_path'];
            getDocumentFromFTP($docPaths, $version_row['doc_path']);
            $removeFiles[]=$docPaths;
            //$docName = $version_row['old_doc_name'];
           //sk@5032019 : set document name with extension.
             $dc_ext = strtolower(pathinfo($version_row['old_doc_name'], PATHINFO_EXTENSION));
            $doc_extn = $version_row['doc_extn'];
            $docName = ($dc_ext == $doc_extn ? $version_row['old_doc_name'] : $version_row['old_doc_name'].'.'. $doc_extn);
            //echo $docName . 'aaaaaaaaaaaaa';
            //end@sk
            
            $mail->addStringAttachment(file_get_contents($docPaths), $docName);
            //$mail->addAttachment($docPaths,'application/octet-stream');
        }
        $mail->AltBody = 'New Notifiation from ' . $projectName . '';
        $mail->CharSet = 'UTF-8';
        //$mail->addAttachment('PHPMailer/examples/images/phpmailer_mini.png');
        if (!$mail->send()) {
            //echo "Mailer Error: " . $mail->ErrorInfo;
            return false;
        } else {
            $sendCC = implode(', ', $cc);
            $sender = $_SESSION['cdes_user_id'];
            $host = $_SERVER['REMOTE_ADDR'];
            $msgbody = mysqli_real_escape_string($db_con, $msgbody);
            mysqli_set_charset($db_con, "utf8");
            $mailsent = mysqli_query($db_con, "insert into tbl_mail_list(`send_by`, `send_to`, `send_cc`, `send_bcc`, `subject`, `mail_body`, `alt_body`, `mail_time`, `ip`, `module_name`) "
                    . "value('$sender','$getAsinByEmail','$sendCC','','Task has been Completed !','$msgbody','New Notifiation from $projectName','$date','$host','Task Completed')") or die('Error' . mysqli_error($db_con));
           
            foreach ($removeFiles as $key => $file){
                //unlink($removeFiles[$key]);
            }
            return true;
        }
    }
}

function mailSendAccounts($msgbody1, $mailFrom, $subject, $dbc, $projectName) {
    mysqli_set_charset($db_con, "utf8");
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
    $mail->Host = EMAIL_HOST;
    $mail->Port = EMAIL_PORT;
    $mail->SMTPAuth = true;
    $mail->Username = EMAIL_USERNAME;
    $mail->Password = EMAIL_PASSWORD;
    $mail->setFrom(EMAIL_SETFROM, 'admin');
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
    $mail->CharSet = 'UTF-8';
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
    

        
    mysqli_set_charset($db_con, "utf8");
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
        
        mysqli_set_charset($db_con, "utf8");
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
                                                       	<p>This is a System Generated mail please do not reply.</p>
									
                                                               
                                                               
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
        
        //echo "manish";
        require_once '../PHPMailer/PHPMailerAutoload.php';
        $mail = new PHPMailer;
        $mail->isSMTP();
        $mail->SMTPDebug = 0;
        $mail->Debugoutput = 'html';
        $mail->Host = EMAIL_HOST;
        $mail->Port = EMAIL_PORT;
        $mail->SMTPAuth = true;
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        $mail->Username = EMAIL_USERNAME;
        $mail->Password = EMAIL_PASSWORD;
        $mail->setFrom(EMAIL_SETFROM, '' . $projectName . ' Admin');

        //$mail->addAddress($mailTo, $userName);
        $mail->addAddress($altrUserEmail, $altrUserName);
        $mail->addCC($suprUserEmail, $suprUserName);
        $mail->addCC($asinUserEmail, $asinUserName);
        $mail->addBCC("ezeefileadmin@cbsl-india.com", "Admin");
        $sendCC = $suprUserEmail . ', ' . $asinUserEmail;

        $mail->Subject = 'Task assigned to alternate user!';

        $mail->msgHTML($msgbody);
        $mail->AltBody = 'New Notifiation from ' . $projectName . '';
        $mail->CharSet = 'UTF-8';
        //$mail->addAttachment('PHPMailer/examples/images/phpmailer_mini.png');
        if (!$mail->send()) {
            //echo "Mailer Error: " . $mail->ErrorInfo;
            return false;
        } else {

            $sender = $_SESSION['cdes_user_id'];
            $host = $_SERVER['REMOTE_ADDR'];
            $msgbody = mysqli_real_escape_string($db_con, $msgbody);
            mysqli_set_charset($db_con, "utf8");
            $mailsent = mysqli_query($db_con, "insert into tbl_mail_list(`send_by`, `send_to`, `send_cc`, `send_bcc`, `subject`, `mail_body`, `alt_body`, `mail_time`, `ip`, `module_name`) "
                    . "value('$sender','$altrUserEmail','$sendCC','','Task assigned to alternate user!','$msgbody','New Notifiation from $projectName','$date','$host','Assign Task to Alternate User')") or die('Error' . mysqli_error($db_con));
            return true;
        }
    } else {
        return false;
    }
}

function assignTaskSupervisor($id, $db_con, $projectName) {
    mysqli_set_charset($db_con, "utf8");
    $task = mysqli_query($db_con, "select * from tbl_doc_assigned_wf where id='$id' and NextTask=4") or die('Error:' . mysqli_error($db_con));
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

        mysqli_set_charset($db_con, "utf8");
        $dms = mysqli_query($db_con, "select * from tbl_document_master where doc_id='$rwTask[doc_id]'") or die('Error:' . mysqli_error($db_con));
        $rwDms = mysqli_fetch_assoc($dms);
        $oldDocName = $rwDms['old_doc_name'];
        
        mysqli_set_charset($db_con, "utf8");
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
                                                        (URL - http://' . $_SERVER['HTTP_HOST'] . ')
                                                       	<p>This is a System Generated mail please do not reply.</p>
									
                                                               
                                                               
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
        require_once '../PHPMailer/PHPMailerAutoload.php';
        $mail = new PHPMailer;
        $mail->isSMTP();
        $mail->SMTPDebug = 0;
        $mail->Debugoutput = 'html';
        $mail->Host = EMAIL_HOST;
        $mail->Port = EMAIL_PORT;
        $mail->SMTPAuth = true;
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        $mail->Username = EMAIL_USERNAME;
        $mail->Password = EMAIL_PASSWORD;
        $mail->setFrom(EMAIL_SETFROM, '' . $projectName . ' Admin');

        //$mail->addAddress($mailTo, $userName);

        $mail->addAddress($suprUserEmail, $suprUserName);
        $mail->addCC($asinUserEmail, $asinUserName);
        $mail->addCC($altrUserEmail, $altrUserName);
        $mail->Subject = 'Task assigned to Supervisor!';
        $mail->addBCC("ezeefileadmin@cbsl-india.com", "Admin");
        $sendCC = $asinUserEmail . ', ' . $altrUserEmail;

        $mail->msgHTML($msgbody);
        $mail->AltBody = 'New Notifiation from ' . $projectName . '';
        $mail->CharSet = 'UTF-8';
        //$mail->addAttachment('PHPMailer/examples/images/phpmailer_mini.png');
        if (!$mail->send()) {
            //echo "Mailer Error: " . $mail->ErrorInfo;
            return false;
        } else {

            $sender = $_SESSION['cdes_user_id'];
            $host = $_SERVER['REMOTE_ADDR'];
            $msgbody = mysqli_real_escape_string($db_con, $msgbody);
            mysqli_set_charset($db_con, "utf8");
            $mailsent = mysqli_query($db_con, "insert into tbl_mail_list(`send_by`, `send_to`, `send_cc`, `send_bcc`, `subject`, `mail_body`, `alt_body`, `mail_time`, `ip`, `module_name`) "
                    . "value('$sender','$suprUserEmail','$sendCC','','Task assigned to Supervisor!','$msgbody','New Notifiation from $projectName','$date','$host','Assign Task to Superwiser')") or die('Error' . mysqli_error($db_con));
            return true;
        }
    } else {
        return false;
    }
}

function abortTask($ticket, $id, $wfid, $db_con, $projectName) {
    mysqli_set_charset($db_con, "utf8");
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
        mysqli_set_charset($db_con, "utf8");
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
        $dc_paths = mysqli_query($db_con, "SELECT old_doc_name,doc_path,doc_name FROM `tbl_document_master` WHERE doc_id='$rwTask[doc_id]'") or die('Error:' . mysqli_error($db_con));
        //die;
        $dcPathsRow = mysqli_fetch_array($dc_paths);
        $dcPath = $dcPathsRow['doc_path'];
        $dcName = $dcPathsRow['old_doc_name'];
        $docName = explode('_', $dcPathsRow['doc_name']);
        $num = count($docName);
        if ($num == 1) {
            $updateDocName = $docName[0] . '_' . $rwTask['doc_id'];
        } else {
            $updateDocName = $docName[0] . '_' . $rwTask['doc_id'] . '_' . $docName[1];
        }
        $fileVersion = mysqli_query($db_con, "SELECT old_doc_name,doc_path,doc_name FROM `tbl_document_master` WHERE doc_name='$updateDocName'") or die('Error:' . mysqli_error($db_con));
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
                                                              
                                                      
                                                      (URL - http://' . $_SERVER['HTTP_HOST'] . ')    
                                              	<p>This is a System Generated mail please do not reply.</p>
									
                                                               
                                                               
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
        $mail->Host = EMAIL_HOST;
        $mail->Port = EMAIL_PORT;
        $mail->SMTPAuth = true;
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        $mail->Username = EMAIL_USERNAME;
        $mail->Password = EMAIL_PASSWORD;
        $mail->setFrom(EMAIL_SETFROM, 'admin');

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
        $file = 'extract-here/' . $dcPath;
        getDocumentFromFTP($file, $dcPath);
        //echo file_get_contents($file);
        $mail->addStringAttachment(file_get_contents($file), $dcName);
        //die;
        //$mail->addAttachment($file,'application/octet-stream');
        while ($version_row = mysqli_fetch_array($fileVersion)) {

            $docPaths = "extract-here/" . $version_row['doc_path'];
            getDocumentFromFTP($docPaths, $version_row['doc_path']);
            $docname = $version_row['old_doc_name'] . '.' . $version_row['doc_extn'];
            $mail->addStringAttachment(file_get_contents($docPaths), $docname);
            //$mail->addAttachment($docPaths,'application/octet-stream');
        }

        $mail->AltBody = 'New Notifiation from ' . $projectName . '';
        $mail->CharSet = 'UTF-8';
        //$mail->addAttachment('PHPMailer/examples/images/phpmailer_mini.png');
        if (!$mail->send()) {
            //echo "Mailer Error: " . $mail->ErrorInfo;
            return false;
        } else {

            $sendCC = implode(',', $cc);
            $sender = $_SESSION['cdes_user_id'];
            $host = $_SERVER['REMOTE_ADDR'];
            $msgbody = mysqli_real_escape_string($db_con, $msgbody);
            mysqli_set_charset($db_con, "utf8");
            $mailsent = mysqli_query($db_con, "insert into tbl_mail_list(`send_by`, `send_to`, `send_cc`, `send_bcc`, `subject`, `mail_body`, `alt_body`, `mail_time`, `ip`, `module_name`) "
                    . "value('$sender','$getAsinByEmail','$sendCC','','Task has been Aborted !','$msgbody','New Notifiation from $projectName','$date','$host','Task is Aborted')") or die('Error' . mysqli_error($db_con));
            return true;
        }
    }
}

function otpemail($txt, $to, $projectName, $username) {

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
                        <div class="header" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; width:96.2%; clear: both; color: #fff; margin: 0; padding: 10px; background: #193860; border-radius: 5px 6px 0px 0px !important;">
                            <table width="100%" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                    <td class="aligncenter content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 12px; vertical-align: top; color: #999; text-align: center; margin: 0; padding: 5px 40px;" valign="top">
                                       <h2 style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 30px; color: #fff; margin: 0; text-align: left;"> ' . $projectName . '</h2></td>
                                </tr></table></div>
                                <table class="main" width="100%" cellpadding="0" cellspacing="0" itemprop="action" itemscope itemtype="http://schema.org/ConfirmAction" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; border-radius: 3px; background-color: #fff; margin: 0; border-bottom: 1px solid #bec0c1;" bgcolor="#fff"><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><td class="content-wrap" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px 50px 0px;" valign="top">
                                    <meta itemprop="name" content="Confirm Email" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;" />
                                    <table width="100%" cellpadding="0" cellspacing="0" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                        <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                            <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 20px;" valign="top"><br>
                                                             <h4>'. $projectName . ' Login OTP</h4>
                                                             <p><strong>OTP : </strong>' . $txt . '</p>
							   </td>
                                                    </tr><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0; font-weight: bold;"><td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 30px;" valign="top">
                                                        Thank You,
                                                        <br> ' . $projectName . ' Team.
                                                    </td>
                                                </tr></table></td>
                                                </tr></table><div class="footer" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; width: 100%; clear: both; color: #999; margin: 0; padding: 1px;">
                                                    <table width="100%" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                            <td class="aligncenter content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 12px; vertical-align: top; color: #999; text-align: left; margin: 0; padding: 0 0 20px;" align="center" valign="top"> <a href="#" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 13px; color: #999; margin: 0; text-decoration: none;"><i>This is a system generated mail please do not reply. Track, manage and store your documents and reduce paper.</i></a> <a href="http://' . $_SERVER['HTTP_HOST'] . '" style="color:blue;">http://' . $_SERVER['HTTP_HOST'] . '</a></td>
                                                        </tr></table></div>
                                                        </div>
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
    $mail->Host = EMAIL_HOST;
    $mail->Port = EMAIL_PORT;
    $mail->SMTPAuth = true;
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );

    $mail->Username = EMAIL_USERNAME;
    $mail->Password = EMAIL_PASSWORD;
    $mail->setFrom(EMAIL_SETFROM, $projectName);
    //$mail->addReplyTo('', '');
    $mail->addAddress($to, $username);
    $mail->Subject = "Login OTP";
    $mail->msgHTML($msgbody);
    $mail->AltBody = 'New Notifiation from ' . $projectName . '';
    $mail->CharSet = 'UTF-8';
    //$mail->addAttachment('PHPMailer/examples/images/phpmailer_mini.png');
    if (!$mail->send()) {
        return false;
    } else {
        return true;
    }
}

function mailDocuments($projectName, $subject, $message, $username, $to, $docIds) {
 
    $docIds = explode(",", $docIds);
   $links = "";
   foreach($docIds as $docId){
        
    $links .='<a href="'.BASE_URL.'downloaddoc?file='.urlencode(base64_encode($docId)).'&em='.urlencode(base64_encode($to)).'">'.BASE_URL.'downloaddoc?file='.urlencode(base64_encode($docId)).'</a><br>';

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

<table class="body-wrap" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; width: 100%; background-color: #f6f6f6; margin: 0;" bgcolor="#f6f6f6"><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><td style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0;" valign="top"></td>
		<td class="container" width="600" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; display: block !important; max-width: 600px !important; clear: both !important; margin: 0 auto;" valign="top">
			<div class="content" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; max-width: 600px; display: block; margin: 0 auto; padding: 20px;">
				<table class="main" width="100%" cellpadding="0" cellspacing="0" itemprop="action" itemscope itemtype="http://schema.org/ConfirmAction" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; border-radius: 3px; background-color: #fff; margin: 0; border: 1px solid #e9e9e9;" bgcolor="#fff"><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><td class="content-wrap" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 20px;" valign="top">
							<meta itemprop="name" content="Confirm Email" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;" /><table width="100%" cellpadding="0" cellspacing="0" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 20px;" valign="top">
										Dear '.$username.' <br>Click below link to download file<br>
                                                                                    
                                                                                '.$links.'
                                                                                
										'.$message.'
										
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
					<table width="100%" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><td class="aligncenter content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 12px; vertical-align: top; color: #999; text-align: center; margin: 0; padding: 0 0 20px;" align="center" valign="top"> Track, manage and store your documents and reduce paper.</td>
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
    $mail->Host = EMAIL_HOST;
    $mail->Port = EMAIL_PORT;
    $mail->SMTPAuth = true;
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );
    
    //$to = "soft.dev4@cbsl-india.com";
    $mail->Username = EMAIL_USERNAME;
    $mail->Password = EMAIL_PASSWORD;
    $mail->setFrom('ezeefileadmin@cbslgroup.in', 'System');
    //$mail->addReplyTo('', '');
   // foreach ($to as $emailto) {
        $mail->addAddress($to, '');
    //}
    //$mail->addAddress($superiorEmail, $superiorName);
    //$mail->addBCC('soft.dev5@cbsl-india.com');
    $mail->Subject = $subject;
    $mail->msgHTML($msgbody);
    $mail->AltBody = 'New Notifiation from ' . $projectName . '';
   $mail->CharSet = 'UTF-8';
    
//    foreach($files as $docpath){
//        
//        $mail->addAttachment(''.$docpath.'');
//        
//    }
    if (!$mail->send()) {
        echo"kkkk";
        return false;
    } else {
//        foreach($files as $docpath){
//            unlink($docpath);
//        }
echo"222";
        return true;
    }
}

function notificationMail($projectName, $subject, $message, $username, $to) {

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
										Dear ' . $username . ' <br>
                                                                                
										' . $message . '
										
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
    $mail->Host = EMAIL_HOST;
    $mail->Port = EMAIL_PORT;
    $mail->SMTPAuth = true;
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );

    //$to = "soft.dev4@cbsl-india.com";
    $mail->Username = EMAIL_USERNAME;
    $mail->Password = EMAIL_PASSWORD;
    $mail->setFrom(EMAIL_SETFROM, 'System');
    //$mail->addReplyTo('', '');
    $mail->addAddress($to, $username);
    //$mail->addAddress($superiorEmail, $superiorName);
    //$mail->addBCC('soft.dev5@cbsl-india.com');
    $mail->Subject = $subject;
    $mail->msgHTML($msgbody);
    $mail->AltBody = 'New Notifiation from ' . $projectName . '';
    $mail->CharSet = 'UTF-8';

//    foreach($files as $docpath){
//        
//        $mail->addAttachment(''.$docpath.'');
//        
//    }
    if (!$mail->send()) {
        //echo "Mailer Error: " . $mail->ErrorInfo;
        return false;
    } else {

        return true;
    }
}

/*
 * Assign Review
 */

function assignReview($ticket, $id, $db_con, $projectName, $subject) {
  
    
    mysqli_set_charset($db_con, "utf8");
    $task = mysqli_query($db_con, "select * from tbl_doc_review where id='$id' and next_task='0'"); //or die('Error:' . mysqli_error($db_con));
    
    
//    $file=fopen('error.txt', "a");
//    fwrite($file, 'Error:' . mysqli_error($db_con)."select * from tbl_doc_review where id='$id' and next_task='0'");
//    fclose($file);
        
    if (mysqli_num_rows($task) > 0) {
        while ($rwTask = mysqli_fetch_assoc($task)) {
            
            $file=fopen('error.txt', "a");
            fwrite($file, 'mmm');
            fclose($file);
            
            if ($rwTask['doc_id'] != '0') {
                $dms = mysqli_query($db_con, "select * from tbl_document_reviewer where doc_id='$rwTask[doc_id]'") or die('Error:' . mysqli_error($db_con));
                $rwDms = mysqli_fetch_assoc($dms);
                $oldDocName = $rwDms['old_doc_name'];
            }
            mysqli_set_charset($db_con, "utf8");
            $user = mysqli_query($db_con, "select first_name,last_name from tbl_user_master where user_id='$rwTask[assign_by]'") or die('Error:' . mysqli_error($db_con));
            $rwUser = mysqli_fetch_assoc($user);
            $asinBy = $rwUser['first_name'] . ' ' . $rwUser['last_name'];

            $asinUserId = mysqli_query($db_con, "select * from tbl_user_master where user_id='$rwTask[action_by]'") or die('Error:' . mysqli_error($db_con));
            $rwasinUserId = mysqli_fetch_assoc($asinUserId);
            $asinUserEmail = $rwasinUserId['user_email_id'];
            $asinUserName = $rwasinUserId['first_name'] . ' ' . $rwasinUserId['last_name'];

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
            if (!empty($rwDms['File_Number'])) {
                $fnum = '<tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                                       <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
                                                                        <b>File Number</b>	
								       </td>
                                                                
                                                                <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
									' . $rwDms['File_Number'] . '	
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
                                       
							New File Created For Review Ticket Number <b>' . $ticket . '</b> has been assigned to <b>' . $asinUserName . '</b>.<br /><br />
                                                                               
                                                         
                                    <table width="100%" cellpadding="0" cellspacing="0" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;" border="1">
                                            
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
									<b>Assign Date</b>	
								       </td>
                                                                  
                                                                   <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
									' .
                    $rwTask[start_date]
                    . '	
								       </td>
								</tr>

                                                           

                                                ' . $docName . '
                                                            <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                                       <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
									<b>Created By</b>	
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

                                                         ' . $fnum . '
                                                        </table><br>
                                                        (URL - http://' . $_SERVER['HTTP_HOST'] . ') <br>
                                                       	<p>This is a System Generated mail please do not reply.</p>
									
                                                               
                                                               
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
            $mail->Host = EMAIL_HOST;
            $mail->Port = EMAIL_PORT;
            $mail->SMTPAuth = true;
            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );
            $mail->Username = EMAIL_USERNAME;
            $mail->Password = EMAIL_PASSWORD;
            $mail->setFrom(EMAIL_SETFROM, 'admin');
            
          
            //$mail->addAddress($mailTo, $userName);
            $mail->addAddress($asinUserEmail, $asinUserName);

            $mail->addBCC("ezeefileadmin@cbsl-india.com", "Admin");
            
             
    
            $sendCC = $altrUserEmail . ',' . $suprUserEmail;

            $mail->Subject = $subject;

            $mail->msgHTML($msgbody);
            $mail->AltBody = 'New Notifiation from ' . $projectName . '';
            $mail->CharSet = 'UTF-8';
            //$mail->addAttachment('PHPMailer/examples/images/phpmailer_mini.png');
            if (!$mail->send()) {
                //echo "Mailer Error: " . $mail->ErrorInfo;
                return false;
            } else {
                $sender = $_SESSION['cdes_user_id'];
                $host = $_SERVER['REMOTE_ADDR'];
                $msgbody = mysqli_real_escape_string($db_con, $msgbody);
                mysqli_set_charset($db_con, "utf8");
                $mailsent = mysqli_query($db_con, "insert into tbl_mail_list(`send_by`, `send_to`, `send_cc`, `send_bcc`, `subject`, `mail_body`, `alt_body`, `mail_time`, `ip`, `module_name`) "
                        . "value('$sender','$asinUserEmail','$sendCC','','New Task Assigned !','$msgbody','New Notifiation from $projectName','$date','$host','Assign Task')") or die('Error' . mysqli_error($db_con));
                return true;
            }
        }
    }
}

function completeReview($ticket, $db_con, $projectName, $subject, $fromcall="") {
    
    
    mysqli_set_charset($db_con, "utf8");
	
    $task = mysqli_query($db_con, "select * from tbl_doc_review where ticket_id='$ticket' and next_task='1'") or die('Error:' . mysqli_error($db_con));
	
	
    if (mysqli_num_rows($task) > 0) {
        $rwTask = mysqli_fetch_assoc($task);
        $dms = mysqli_query($db_con, "select * from tbl_document_reviewer where doc_id='$rwTask[doc_id]'") or die('Error:' . mysqli_error($db_con));
        $rwDms = mysqli_fetch_assoc($dms);
        $oldDocName = $rwDms['old_doc_name'];

        mysqli_set_charset($db_con, "utf8");
        $user = mysqli_query($db_con, "select first_name,last_name from tbl_user_master where user_id='$rwTask[assign_by]'") or die('Error:' . mysqli_error($db_con));
        $rwUser = mysqli_fetch_assoc($user);

        $asinBy = $rwUser['first_name'] . ' ' . $rwUser['last_name'];

        mysqli_set_charset($db_con, "utf8");
        $user1 = mysqli_query($db_con, "select first_name,last_name from tbl_user_master where user_id='$rwTask[action_by]'") or die('Error:' . mysqli_error($db_con));
        $rwUser1 = mysqli_fetch_assoc($user1);
        $actionby = $rwUser1['first_name'] . ' ' . $rwUser1['last_name'];
        //$tskId = $rwTask['task_id'];
        //$taskRe = mysqli_query($db_con, "select * from tbl_task_master where task_id='$tskId'") or die('Error:'.mysqli_error($db_con));
        //$rwTaskRe = mysqli_fetch_assoc($taskRe);
//
//        $asinUser = $rwWork['assign_user'];
//        $asinUserId = mysqli_query($db_con, "select * from tbl_user_master where user_id='$asinUser'") or die('Error:' . mysqli_error($db_con));
//        $rwasinUserId = mysqli_fetch_assoc($asinUserId);
//        $asinUserEmail = $rwasinUserId['user_email_id'];
//        $asinUserName = $rwasinUserId['first_name'] . ' ' . $rwasinUserId['last_name'];
        //get asign by detail      
        $getAsinBy = mysqli_query($db_con, "select tum.user_email_id, tum.first_name, tum.last_name, tda.start_date from tbl_doc_review tda inner join tbl_user_master tum on tda.assign_by = tum.user_id where tda.ticket_id = '$ticket'") or die('Error:' . mysqli_error($db_con));
        $rwgetAsinBy = mysqli_fetch_assoc($getAsinBy);
        $getAsinByEmail = $rwgetAsinBy['user_email_id'];
        $getAsinByName = $rwgetAsinBy['first_name'] . ' ' . $rwgetAsinBy['last_name'];
        $getAsignDate = $rwgetAsinBy['start_date'];



        //get work flow name

        $dc_paths = mysqli_query($db_con, "SELECT old_doc_name,doc_path,doc_name,doc_extn,File_Number FROM `tbl_document_reviewer` WHERE doc_id='$rwTask[doc_id]'") or die('Error:' . mysqli_error($db_con));
        //die;

        $dcPathsRow = mysqli_fetch_array($dc_paths);
        $dcPath = $dcPathsRow['doc_path'];
        $strgName = mysqli_query($db_con, "select sl_name from tbl_storage_level where sl_id='$dcPathsRow[doc_name]'");
        $fetchStrg = mysqli_fetch_assoc($strgName);
        // echo "test and run".$dcPathsRow['doc_name'];
        $dcName = $dcPathsRow['old_doc_name'] . "." . $dcPathsRow['doc_extn'];
        $docName = explode('_', $dcPathsRow['doc_name']);
        $num = count($docName);
        if ($num == 2) {

            $updateDocName = $docName[0] . '_' . $rwTask['doc_id'] . '_' . $docName[1];
        } else {

            $updateDocName = $docName[0] . '_' . $rwTask['doc_id'];
        }
        $fileVersion = mysqli_query($db_con, "SELECT old_doc_name,doc_path,doc_name,doc_extn FROM `tbl_document_reviewer` WHERE doc_name='$updateDocName'") or die('Error:' . mysqli_error($db_con));

        //get action by, action time and comment
        //inner join tbl_user_master tum on tum.user_id = tda.action_by inner join tbl_task_master ttm on ttm.task_id = tda.task_id
//        $taskHistry = mysqli_query($db_con, "select ttc.task_status, ttc.comment_time, tum.first_name, tum.last_name, ttm.task_name, ttc.comment from tbl_doc_assigned_wf tda inner join tbl_task_comment ttc on (tda.ticket_id = ttc.tickt_id and tda.task_id = ttc.task_id) inner join tbl_user_master tum on (tum.user_id = tda.action_by) inner join tbl_task_master ttm on ttm.task_id = tda.task_id where tda.ticket_id='$ticket' and tda.NextTask='1' order by ttc.comment_time desc") or die('Error:' . mysqli_error($db_con));
//
//        $aprBy = '';
//
//        while ($rwTaskHistry = mysqli_fetch_assoc($taskHistry)) {
//
//            //$actionByName = $rwTaskHistry['first_name'].' '.$rwTaskHistry['last_name'];
//            // $actionByDate = $rwTaskHistry['action_time'];
//            //$actionByComment = $rwTaskHistry['comment'];
//            // print_r($rwTaskHistry); die;
//
//            if ($rwTaskHistry['task_status'] == 'Approved' or $rwTaskHistry['task_status'] == 'Processed' or $rwTaskHistry['task_status'] == 'Done' or $rwTaskHistry['task_status'] == 'Complete') {
//
//                $aprBy .= "<b>" . $rwTaskHistry['task_status'] . " by: </b>" . $rwTaskHistry['first_name'] . ' ' . $rwTaskHistry['last_name'] . " <br />";
//            } elseif ($rwTaskHistry['task_status'] == 'Rejected') {
//                $aprBy .= "<b>" . $rwTaskHistry['task_status'] . " by: </b>" . $rwTaskHistry['first_name'] . ' ' . $rwTaskHistry['last_name'] . " <br />";
//            } elseif ($rwTaskHistry['task_status'] == 'Aborted') {
//                $aprBy .= "<b>" . $rwTaskHistry['task_status'] . " by: </b>" . $rwTaskHistry['first_name'] . ' ' . $rwTaskHistry['last_name'] . " <br />";
//            }
//
//            //$aprBy .= "<b>Approved by: </b>". $rwTaskHistry['first_name'].' '.$rwTaskHistry['last_name'] ." <br />"; 
//            $aprBy .= "<b>At Task: </b>" . $rwTaskHistry['task_name'] . "<br />";
//            $aprBy .= "<b>On: </b>" . $rwTaskHistry['comment_time'] . "<br />";
//            $aprBy .= "<b>Comment: </b>" . $rwTaskHistry['comment'] . "<br/><br/>";
//
//            echo "<br/><br/><br/><br/>><br/>";
//        }



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
$fileNumber="";
        if (!empty($dcPathsRow['File_Number'])) {
            $fileNumber = '<tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                                       <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
                                                                        <b>File Number</b>	
								       </td>
                                                             
                                                                <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
									' .
                    $dcPathsRow[File_Number]
                    . '	
								       </td>
								</tr>';
        }
        if (!empty($updateDocName)) {
            $fileLoc = '<tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                                       <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
                                                                        <b>File Location</b>	
								       </td>
                                                             
                                                                <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
									' .
                    $fetchStrg[sl_name]
                    . '	(URL - http://' . $_SERVER['HTTP_HOST'] . '/storage?id=' . urlencode(base64_encode($rwTask[doc_id])) . ')
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
                                       
							Document Reviewed Complete Ticket Number <b>' . $ticket . '</b> has been assigned to <b>' . $asinUserName . '</b>.<br /><br />
                                                                               
                                                         
                                    <table width="100%" cellpadding="0" cellspacing="0" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;" border="1">
                                            
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
									<b>Assign Date</b>	
								       </td>
                                                                  
                                                                   <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
									' .
                $rwTask[start_date]
                . '	
								       </td>
								</tr>

                                                           

                                                ' . $docName . '
                                                            <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                                       <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
									<b>Created By</b>	
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
                $actionby
                . '	
								       </td>
								</tr>
' . $fileLoc . $fileNumber . '
                                                        
                                                        </table><br>
                                                        (URL - http://' . $_SERVER['HTTP_HOST'] . ') <br>
                                                       	<p>This is a System Generated mail please do not reply.</p>
									
                                                               
                                                               
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
        if($fromcall){
             
             require_once 'application/PHPMailer/PHPMailerAutoload.php';
        }else{
           
             require_once '../application/PHPMailer/PHPMailerAutoload.php';
        }
       
        $mail = new PHPMailer;
        $mail->isSMTP();
        $mail->SMTPDebug = 0;
        $mail->Debugoutput = 'html';
        $mail->Host = EMAIL_HOST;
        $mail->Port = EMAIL_PORT;
        $mail->SMTPAuth = true;
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        $mail->Username = EMAIL_USERNAME;
        $mail->Password = EMAIL_PASSWORD;
        $mail->setFrom(EMAIL_SETFROM, 'admin');

       
        //$mail->addAddress($mailTo, $userName);
        $mail->addAddress($getAsinByEmail, $getAsinByName);
        
        $mail->addBCC("ezeefileadmin@cbsl-india.com", "Admin");
        $getAprovBy = mysqli_query($db_con, "select tum.first_name, tum.last_name, tum.user_email_id FROM tbl_doc_review tda INNER JOIN  tbl_user_master tum ON tda.action_by = tum.user_id where tda.ticket_id = '$ticket' ") or die('Error:' . mysqli_error($db_con));
        $cc = array();

        while($rwgetAprovBy = mysqli_fetch_assoc($getAprovBy)) {

            $firstName = $rwgetAprovBy['first_name'];
            $lastName = $rwgetAprovBy['last_name'];
            $fulName = $firstName . ' ' . $lastName;
            $AprovByEmail = $rwgetAprovBy['user_email_id'];
            $cc[] = $AprovByEmail;
            $mail->addCC($AprovByEmail, $fulName);
        }

        $mail->Subject = $subject;

        $mail->msgHTML($msgbody);
//        $file = 'extract-here/' . $dcPath;
//        //echo file_get_contents($file);
//        $mail->addStringAttachment(file_get_contents($file), $dcName);
//        //die;
//        //$mail->addAttachment($file,'application/octet-stream');
//        while ($version_row = mysqli_fetch_array($fileVersion)) {
//
//            $docPaths = "extract-here/" . $version_row['doc_path'];
//
//            $docName = $version_row['old_doc_name'] . "." . $version_row['doc_extn'];
//            $mail->addStringAttachment(file_get_contents($docPaths), $docName);
//            //$mail->addAttachment($docPaths,'application/octet-stream');
//        }
        $mail->AltBody = 'New Notifiation from ' . $projectName . '';
       $mail->CharSet = 'UTF-8';
        //$mail->addAttachment('PHPMailer/examples/images/phpmailer_mini.png');
        if (!$mail->send()) {
            //echo "Mailer Error: " . $mail->ErrorInfo;
            return false;
        } else {
            $sendCC = implode(', ', $cc);
            $sender = $_SESSION['cdes_user_id'];
            $host = $_SERVER['REMOTE_ADDR'];
            $msgbody = mysqli_real_escape_string($db_con, $msgbody);
            mysqli_set_charset($db_con, "utf8");
            $mailsent = mysqli_query($db_con, "insert into tbl_mail_list(`send_by`, `send_to`, `send_cc`, `send_bcc`, `subject`, `mail_body`, `alt_body`, `mail_time`, `ip`, `module_name`) "
                    . "value('$sender','$getAsinByEmail','$sendCC','','Task has been Completed !','$msgbody','New Notifiation from $projectName','$date','$host','Task Completed')") or die('Error' . mysqli_error($db_con));
            return true;
        }
    }
}

function assignNextReview($ticket, $id, $db_con, $projectName, $subject, $fromcall="") {
    $task = mysqli_query($db_con, "select * from tbl_doc_review where id='$id' and next_task='0'") or die('Error:' . mysqli_error($db_con));
    if (mysqli_num_rows($task) > 0) {
        while ($rwTask = mysqli_fetch_assoc($task)) {

//print_R($task);

            if ($rwTask['doc_id'] != '0') {
                $dms = mysqli_query($db_con, "select * from tbl_document_reviewer where doc_id='$rwTask[doc_id]'") or die('Error:' . mysqli_error($db_con));
                $rwDms = mysqli_fetch_assoc($dms);
                $oldDocName = $rwDms['old_doc_name'];
            }
            
            mysqli_set_charset($db_con, "utf8");
            $user = mysqli_query($db_con, "select first_name,last_name from tbl_user_master where user_id='$rwTask[assign_by]'") or die('Error:' . mysqli_error($db_con));
            $rwUser = mysqli_fetch_assoc($user);
            $asinBy = $rwUser['first_name'] . ' ' . $rwUser['last_name'];

            mysqli_set_charset($db_con, "utf8");
            $asinUserId = mysqli_query($db_con, "select * from tbl_user_master where user_id='$rwTask[action_by]'") or die('Error:' . mysqli_error($db_con));
            $rwasinUserId = mysqli_fetch_assoc($asinUserId);
            $asinUserEmail = $rwasinUserId['user_email_id'];
            $asinUserName = $rwasinUserId['first_name'] . ' ' . $rwasinUserId['last_name'];

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
            if (!empty($rwDms['File_Number'])) {
                $fnum = '<tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                                       <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
                                                                        <b>File Number</b>	
								       </td>
                                                                
                                                                <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
									' . $rwDms['File_Number'] . '	
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
                                       
							File Reviewed Ticket Number <b>' . $ticket . '</b> has been assigned to <b>' . $asinUserName . '</b>.<br /><br />
                                                                               
                                                         
                                    <table width="100%" cellpadding="0" cellspacing="0" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;" border="1">
                                            
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
									<b>Assign Date</b>	
								       </td>
                                                                  
                                                                   <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
									' .
                    $rwTask[start_date]
                    . '	
								       </td>
								</tr>

                                                           

                                                ' . $docName . '
                                                            <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                                       <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px;" valign="top">
									<b>Created By</b>	
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

                                                         ' . $fnum . '
                                                        </table><br>
                                                        (URL - http://' . $_SERVER['HTTP_HOST'] . ') <br>
                                                       	<p>This is a System Generated mail please do not reply.</p>
									
                                                               
                                                               
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
            if($fromcall){
                 require_once 'application/PHPMailer/PHPMailerAutoload.php';
            }else{
                require_once '../application/PHPMailer/PHPMailerAutoload.php';
            }
           
            $mail = new PHPMailer;
            $mail->isSMTP();
            $mail->SMTPDebug = 0;
            $mail->Debugoutput = 'html';
            $mail->Host = EMAIL_HOST;
            $mail->Port = EMAIL_PORT;
            $mail->SMTPAuth = true;
            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );
            $mail->Username = EMAIL_USERNAME;
            $mail->Password = EMAIL_PASSWORD;
            $mail->setFrom(EMAIL_SETFROM, 'admin');

            //$mail->addAddress($mailTo, $userName);
            $mail->addAddress($asinUserEmail, $asinUserName);

            $mail->addBCC("ezeefileadmin@cbsl-india.com", "Admin");
            $sendCC = $altrUserEmail . ',' . $suprUserEmail;

            $mail->Subject = $subject;

            $mail->msgHTML($msgbody);
            $mail->AltBody = 'New Notifiation from ' . $projectName . '';
            $mail->CharSet = 'UTF-8';
            //$mail->addAttachment('PHPMailer/examples/images/phpmailer_mini.png');
            if (!$mail->send()) {
                //echo "Mailer Error: " . $mail->ErrorInfo;
                return false;
            } else {
                $sender = $_SESSION['cdes_user_id'];
                $host = $_SERVER['REMOTE_ADDR'];
                $msgbody = mysqli_real_escape_string($db_con, $msgbody);
                mysqli_set_charset($db_con, "utf8");
                $mailsent = mysqli_query($db_con, "insert into tbl_mail_list(`send_by`, `send_to`, `send_cc`, `send_bcc`, `subject`, `mail_body`, `alt_body`, `mail_time`, `ip`, `module_name`) "
                        . "value('$sender','$asinUserEmail','$sendCC','','New Task Assigned !','$msgbody','New Notifiation from $projectName','$date','$host','Assign Task')") or die('Error' . mysqli_error($db_con));
                return true;
            }
        }
    }
}

function mailClientCreate($to, $pass, $projectName, $coustomer,$FullSubDomain) {
    
    if(!empty($coustomer)){
    $cust_no= '<strong>Your Customer Number is </strong> - '. $coustomer . '<br>';   
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

<table class="body-wrap" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; width: 100%; background-color: #f6f6f6; margin: 0;" bgcolor="#f6f6f6"><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><td style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0;" valign="top"></td>
		<td class="container" width="600" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; display: block !important; max-width: 600px !important; clear: both !important; margin: 0 auto;" valign="top">
			<div class="content" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; max-width: 600px; display: block; margin: 0 auto; padding: 20px;">
				<table class="main" width="100%" cellpadding="0" cellspacing="0" itemprop="action" itemscope itemtype="http://schema.org/ConfirmAction" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; border-radius: 3px; background-color: #fff; margin: 0; border: 1px solid #e9e9e9;" bgcolor="#fff"><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><td class="content-wrap" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 20px;" valign="top">
							<meta itemprop="name" content="Confirm Email" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;" /><table width="100%" cellpadding="0" cellspacing="0" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 20px;" valign="top">
										Your new Login password for ' . $projectName . ' is <br>
                                                                                <strong>Your Username is </strong> - ' . $to . '<br>
										<strong>Password </strong> - ' . $pass . '<br>
                                                                                '.$cust_no.'    
										<strong>Url - </strong>http://'.$FullSubDomain.'<br>
									</td>
								</tr><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><td class="content-block" itemprop="handler" itemscope itemtype="http://schema.org/HttpActionHandler" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 20px;" valign="top">
										This is a System Generated mail please do not reply.
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
    require_once 'application/PHPMailer/PHPMailerAutoload.php';
    $mail = new PHPMailer;
    $mail->isSMTP();
    $mail->SMTPDebug = 0;
    $mail->Debugoutput = 'html';
    $mail->Host = EMAIL_HOST;
    $mail->Port = EMAIL_PORT;
    $mail->SMTPAuth = true;
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );
    $mail->Username = EMAIL_USERNAME;
    $mail->Password = EMAIL_PASSWORD;
    $mail->setFrom(EMAIL_SETFROM, 'System');
    //$mail->addReplyTo('', '');
    $mail->addAddress($to, $username);
    //$mail->addAddress($superiorEmail, $superiorName);
    //$mail->addBCC('soft.dev5@cbsl-india.com');
    $mail->Subject = "Your Email ID And  password for $projectName";
    $mail->msgHTML($msgbody);
    $mail->AltBody = 'New Notifiation from ' . $projectName . '';
    $mail->CharSet = 'UTF-8';
    //$mail->addAttachment('PHPMailer/examples/images/phpmailer_mini.png');
    if (!$mail->send()) {
        return false;
    } else {

        return true;
    }
}


function docExpFinalMailtoAuthrizedUsers($mailbodyhtml, $db_con, $mailto, $projectName) {

    $asinUserEmailIds = array();
    $userEmail = mysqli_query($db_con, "SELECT * FROM tbl_user_master where user_id in($mailto)");
    while ($rwuserEmail = mysqli_fetch_assoc($userEmail)) {
        $asinUserEmailIds[$rwuserEmail['user_email_id']] = $rwuserEmail['first_name'] . ' ' . $rwuserEmail['last_name'];
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
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
</head>

<body style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; -webkit-font-smoothing: antialiased; -webkit-text-size-adjust: none; width: 100% !important; height: 100%; line-height: 1.6em; background-color: #f6f6f6; margin: 0;" bgcolor="#f6f6f6">

<table class="body-wrap" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; width: 100%; background-color: #f6f6f6; margin: 0;" bgcolor="#f6f6f6"><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><td style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0;" valign="top"></td>
    <td class="container" width="600" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; display: block !important; max-width: 600px !important; clear: both !important; margin: 0 auto;" valign="top">
        <div class="content" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; max-width: 600px; display: block; margin: 0 auto; padding: 20px;">
            <div class="header" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; width: 100%; clear: both; color: #fff; margin: 0; padding: 10px; background: #193860; border-radius: 5px 6px 0px 0px !important;">
                <table width="100%" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                        <td class="aligncenter content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 12px; vertical-align: top; color: #999; text-align: center; margin: 0; padding: 5px 40px;" valign="top">
                            <h2 style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 30px; color: #fff; margin: 0; text-align: left;"> ' . $projectName . '</h2></td>
</tr></table></div>
<table class="main" width="100%" cellpadding="0" cellspacing="0" itemprop="action" itemscope itemtype="http://schema.org/ConfirmAction" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; border-radius: 3px; background-color: #fff; margin: 0; border-bottom: 1px solid #bec0c1;" bgcolor="#fff"><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><td class="content-wrap" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0;" valign="top">
    <meta itemprop="name" content="Confirm Email" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;" />
                <table class="main" width="100%" cellpadding="0" cellspacing="0" itemprop="action" itemscope itemtype="http://schema.org/ConfirmAction" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; border-radius: 3px; background-color: #fff; margin: 0; border: 1px solid #e9e9e9;" bgcolor="#fff">
<tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
        <td class="content-wrap" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 20px;" valign="top">
<strong>Dear Concern,</strong><br>Listed documents are expired from ' . $projectName . '
</td>
</tr>

<tr>' . $mailbodyhtml . '</tr>

<tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0; font-weight: bold;"><td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px 20px 30px;" valign="top">
Thank You,
<br> ' . $projectName . ' Team.
</td>
</tr>
</table>
</div>
</td>
</td>
</tr>
 </table>
<div class="footer" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; width: 100%; clear: both; color: #999; margin: 0; padding: 1px;">
    <table width="100%" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
<td class="aligncenter content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 12px; vertical-align: top; color: #999; text-align: left; margin: 0; padding: 0 0 30px;" align="center" valign="top"> 
<a href="#" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 13px; color: #999; margin: 0; text-decoration: none;"><i>This is a system generated mail please do not reply.</i></a> <a href="http://' . $_SERVER['HTTP_HOST'] . '" style="color:blue;">http://' . $_SERVER['HTTP_HOST'] . '</a></td>
        </tr></table></div>
  </body>
</html>';

    date_default_timezone_set('Asia/Kolkata');
    $date = date("Y-m-d H:i:s");
    require_once '../application/PHPMailer/PHPMailerAutoload.php';

    $mail = new PHPMailer;
    $mail->isSMTP();
    $mail->SMTPDebug = 0;
    $mail->Debugoutput = 'html';
    $mail->Host = EMAIL_HOST;
    $mail->Port = EMAIL_PORT;
    $mail->SMTPAuth = true;
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );

    $mail->Username = EMAIL_USERNAME;
    $mail->Password = EMAIL_PASSWORD;
    $mail->setFrom(EMAIL_SETFROM, 'admin');

    $sendmailids = array();
    $kj = 0;
    foreach ($asinUserEmailIds as $email => $name) {
        $mail->addAddress($email, $name);
        $sendmailids[$kj] = $email;
        $kj++;
    }
    //$mail->addAddress($mailTo, $userName);
    $mail->addBCC("ezeefileadmin@cbsl-india.com", "Admin");
    $mail->Subject = $projectName . ' document expired !!';

    $mail->msgHTML($msgbody);
    $mail->AltBody = 'New Notifiation from ' . $projectName . '';
    //$mail->addAttachment('PHPMailer/examples/images/phpmailer_mini.png');
    $mail->CharSet = 'UTF-8';
    if (!$mail->send()) {
        //echo "Mailer Error: " . $mail->ErrorInfo;
        return false;
    } else {
        //$sender = $_SESSION['cdes_user_id'];
        $host = $_SERVER['REMOTE_ADDR'];
        $msgbody = mysqli_real_escape_string($db_con, $msgbody);
        //mysqli_set_charset($db_con,"latin1");
        //$mailsent = mysqli_query($db_con, "insert into tbl_mail_list(`send_by`, `send_cc`, `send_bcc`, `subject`, `mail_body`, `alt_body`, `mail_time`, `ip`, `module_name`) "
        //. "value('$sender','$mailto','','New Task Assigned !','$msgbody','New Notifiation from $projectName','$date','$host','Assign Task')") or die('Error' . mysqli_error($db_con));
        return true;
    }
}

function docExpMailtoAuthrizedUsers($mailbodyhtml, $db_con, $mailto, $projectName) {
    
    $asinUserEmailIds = array();
    $userEmail = mysqli_query($db_con, "SELECT * FROM tbl_user_master where user_id in($mailto)");
    while ($rwuserEmail = mysqli_fetch_assoc($userEmail)) {
        mysqli_set_charset($db_con, "utf8");
        $asinUserEmailIds[$rwuserEmail['user_email_id']] = $rwuserEmail['first_name'] . ' ' . $rwuserEmail['last_name'];
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
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
</head>

<body style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; -webkit-font-smoothing: antialiased; -webkit-text-size-adjust: none; width: 100% !important; height: 100%; line-height: 1.6em; background-color: #f6f6f6; margin: 0;" bgcolor="#f6f6f6">

<table class="body-wrap" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; width: 100%; background-color: #f6f6f6; margin: 0;" bgcolor="#f6f6f6"><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><td style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0;" valign="top"></td>
    <td class="container" width="600" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; display: block !important; max-width: 600px !important; clear: both !important; margin: 0 auto;" valign="top">
        <div class="content" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; max-width: 600px; display: block; margin: 0 auto; padding: 20px;">
            <div class="header" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; width: 100%; clear: both; color: #fff; margin: 0; padding: 10px; background: #193860; border-radius: 5px 6px 0px 0px !important;">
                <table width="100%" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                        <td class="aligncenter content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 12px; vertical-align: top; color: #999; text-align: center; margin: 0; padding: 5px 40px;" valign="top">
                            <h2 style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 30px; color: #fff; margin: 0; text-align: left;"> ' . $projectName . '</h2></td>
</tr></table></div>
<table class="main" width="100%" cellpadding="0" cellspacing="0" itemprop="action" itemscope itemtype="http://schema.org/ConfirmAction" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; border-radius: 3px; background-color: #fff; margin: 0; border-bottom: 1px solid #bec0c1;" bgcolor="#fff"><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><td class="content-wrap" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0;" valign="top">
    <meta itemprop="name" content="Confirm Email" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;" />
                <table class="main" width="100%" cellpadding="0" cellspacing="0" itemprop="action" itemscope itemtype="http://schema.org/ConfirmAction" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; border-radius: 3px; background-color: #fff; margin: 0; border: 1px solid #e9e9e9;" bgcolor="#fff">
<tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
        <td class="content-wrap" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 20px;" valign="top">
<strong>Dear Concern,</strong><br>Listed Documents/Certificate will be expired soon from ' . $projectName . '
</td>
</tr>

<tr>' . $mailbodyhtml . '</tr>

<tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0; font-weight: bold;"><td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px 20px 30px;" valign="top">
Thank You,
<br> ' . $projectName . ' Team.
</td>
</tr>
</table>
</div>
</td>
</td>
</tr>
 </table>
<div class="footer" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; width: 100%; clear: both; color: #999; margin: 0; padding: 1px;">
    <table width="100%" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
<td class="aligncenter content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 12px; vertical-align: top; color: #999; text-align: left; margin: 0; padding: 0 0 30px;" align="center" valign="top"> 
<a href="#" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 13px; color: #999; margin: 0; text-decoration: none;"><i>This is a system generated mail please do not reply.</i></a> <a href="http://' . $_SERVER['HTTP_HOST'] . '" style="color:blue;">http://' . $_SERVER['HTTP_HOST'] . '</a></td>
        </tr></table></div>
  </body>
</html>';

    date_default_timezone_set('Asia/Kolkata');
    $date = date("Y-m-d H:i:s");
    require_once '../application/PHPMailer/PHPMailerAutoload.php';

    $mail = new PHPMailer;
    $mail->isSMTP();
    $mail->SMTPDebug = 0;
    $mail->Debugoutput = 'html';
    $mail->Host = EMAIL_HOST;
    $mail->Port = EMAIL_PORT;
    $mail->SMTPAuth = true;
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );

    $mail->Username = EMAIL_USERNAME;
    $mail->Password = EMAIL_PASSWORD;
    $mail->setFrom(EMAIL_SETFROM, 'admin');
    $sendmailids = array();
    $kj = 0;
    foreach ($asinUserEmailIds as $email => $name) {
        $mail->addAddress($email, $name);
        $sendmailids[$kj] = $email;
        $kj++;
    }
    //$mail->addAddress($mailTo, $userName);
    $mail->addBCC("ezeefileadmin@cbsl-india.com", "Admin");
    $mail->Subject = 'Attention !! ' . $projectName . ' Documents/Certificate expired soon.';

    $mail->msgHTML($msgbody);
    $mail->AltBody = 'New Notifiation from ' . $projectName . '';
    //$mail->addAttachment('PHPMailer/examples/images/phpmailer_mini.png');
    $mail->CharSet = 'UTF-8';
    if (!$mail->send()) {
        //echo "Mailer Error: " . $mail->ErrorInfo;
        return false;
    } else {
        //$sender = $_SESSION['cdes_user_id'];
        $host = $_SERVER['REMOTE_ADDR'];
        $msgbody = mysqli_real_escape_string($db_con, $msgbody);
        //mysqli_set_charset($db_con,"latin1");
        //$mailsent = mysqli_query($db_con, "insert into tbl_mail_list(`send_by`, `send_cc`, `send_bcc`, `subject`, `mail_body`, `alt_body`, `mail_time`, `ip`, `module_name`) "
        //. "value('$sender','$mailto','','New Task Assigned !','$msgbody','New Notifiation from $projectName','$date','$host','Assign Task')") or die('Error' . mysqli_error($db_con));
        return true;
    }
}

function docRetentionMailtoAuthrizedUsers($mailbodyhtml, $db_con, $mailto, $projectName) {
    $asinUserEmailIds = array();
    $userEmail = mysqli_query($db_con, "SELECT * FROM tbl_user_master where user_id in($mailto)");
    while ($rwuserEmail = mysqli_fetch_assoc($userEmail)) {
        mysqli_set_charset($db_con, "utf8");
        $asinUserEmailIds[$rwuserEmail['user_email_id']] = $rwuserEmail['first_name'] . ' ' . $rwuserEmail['last_name'];
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
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
</head>

<body style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; -webkit-font-smoothing: antialiased; -webkit-text-size-adjust: none; width: 100% !important; height: 100%; line-height: 1.6em; background-color: #f6f6f6; margin: 0;" bgcolor="#f6f6f6">

<table class="body-wrap" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; width: 100%; background-color: #f6f6f6; margin: 0;" bgcolor="#f6f6f6"><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><td style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0;" valign="top"></td>
    <td class="container" width="600" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; display: block !important; max-width: 600px !important; clear: both !important; margin: 0 auto;" valign="top">
        <div class="content" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; max-width: 600px; display: block; margin: 0 auto; padding: 20px;">
            <div class="header" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; width: 100%; clear: both; color: #fff; margin: 0; padding: 10px; background: #193860; border-radius: 5px 6px 0px 0px !important;">
                <table width="100%" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                        <td class="aligncenter content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 12px; vertical-align: top; color: #999; text-align: center; margin: 0; padding: 5px 40px;" valign="top">
                            <h2 style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 30px; color: #fff; margin: 0; text-align: left;"> ' . $projectName . '</h2></td>
</tr></table></div>
<table class="main" width="100%" cellpadding="0" cellspacing="0" itemprop="action" itemscope itemtype="http://schema.org/ConfirmAction" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; border-radius: 3px; background-color: #fff; margin: 0; border-bottom: 1px solid #bec0c1;" bgcolor="#fff"><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><td class="content-wrap" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0;" valign="top">
    <meta itemprop="name" content="Confirm Email" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;" />
                <table class="main" width="100%" cellpadding="0" cellspacing="0" itemprop="action" itemscope itemtype="http://schema.org/ConfirmAction" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; border-radius: 3px; background-color: #fff; margin: 0; border: 1px solid #e9e9e9;" bgcolor="#fff">
<tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
        <td class="content-wrap" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 20px;" valign="top">
<strong>Dear Concern,</strong><br>Listed Documents/Certificate are under retention period and will be deleted soon from ' . $projectName . '
</td>
</tr>

<tr>' . $mailbodyhtml . '</tr>

<tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0; font-weight: bold;"><td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px 20px 30px;" valign="top">
Thank You,
<br> ' . $projectName . ' Team.
</td>
</tr>
</table>
</div>
</td>
</td>
</tr>
 </table>
<div class="footer" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; width: 100%; clear: both; color: #999; margin: 0; padding: 1px;">
    <table width="100%" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
<td class="aligncenter content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 12px; vertical-align: top; color: #999; text-align: left; margin: 0; padding: 0 0 30px;" align="center" valign="top"> 
<a href="#" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 13px; color: #999; margin: 0; text-decoration: none;"><i>This is a system generated mail please do not reply.</i></a> <a href="http://' . $_SERVER['HTTP_HOST'] . '" style="color:blue;">http://' . $_SERVER['HTTP_HOST'] . '</a></td>
        </tr></table></div>
  </body>
</html>';

    date_default_timezone_set('Asia/Kolkata');
    $date = date("Y-m-d H:i:s");
    require_once '../application/PHPMailer/PHPMailerAutoload.php';

    $mail = new PHPMailer;
    $mail->isSMTP();
    $mail->SMTPDebug = 0;
    $mail->Debugoutput = 'html';
    $mail->Host = EMAIL_HOST;
    $mail->Port = EMAIL_PORT;
    $mail->SMTPAuth = true;
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );

    $mail->Username = EMAIL_USERNAME;
    $mail->Password = EMAIL_PASSWORD;
    $mail->setFrom(EMAIL_SETFROM, 'admin');
    $sendmailids = array();
    $kj = 0;
    foreach ($asinUserEmailIds as $email => $name) {
        $mail->addAddress($email, $name);
        $sendmailids[$kj] = $email;
        $kj++;
    }
    //$mail->addAddress($mailTo, $userName);
    $mail->addBCC("ezeefileadmin@cbsl-india.com", "Admin");
    $mail->Subject = $rwWork['task_name'] . ' ' . 'Attention !! Documents/Certificate deleted soon.';

    $mail->msgHTML($msgbody);
    $mail->AltBody = 'New Notifiation from ' . $projectName . '';
    //$mail->addAttachment('PHPMailer/examples/images/phpmailer_mini.png');
    $mail->CharSet = 'UTF-8';
    if (!$mail->send()) {
        //echo "Mailer Error: " . $mail->ErrorInfo;
        return false;
    } else {
        $sender = $_SESSION['cdes_user_id'];
        $host = $_SERVER['REMOTE_ADDR'];
        $msgbody = mysqli_real_escape_string($db_con, $msgbody);
        //mysqli_set_charset($db_con,"latin1");
        //$mailsent = mysqli_query($db_con, "insert into tbl_mail_list(`send_by`, `send_cc`, `send_bcc`, `subject`, `mail_body`, `alt_body`, `mail_time`, `ip`, `module_name`) "
        //. "value('$sender','$mailto','','New Task Assigned !','$msgbody','New Notifiation from $projectName','$date','$host','Assign Task')") or die('Error' . mysqli_error($db_con));
        return true;
    }
}

function docRetentionFinalMailtoAuthrizedUsers($mailbodyhtml, $db_con, $mailto, $projectName) {
    //echo 'dlfkdsfkdslfkldskfldskfldskfldskfdlsfkldsfkldsfk'; die;
    $asinUserEmailIds = array();
    $userEmail = mysqli_query($db_con, "SELECT * FROM tbl_user_master where user_id in($mailto)");
    while ($rwuserEmail = mysqli_fetch_assoc($userEmail)) {
        $asinUserEmailIds[$rwuserEmail['user_email_id']] = $rwuserEmail['first_name'] . ' ' . $rwuserEmail['last_name'];
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
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
</head>

<body style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; -webkit-font-smoothing: antialiased; -webkit-text-size-adjust: none; width: 100% !important; height: 100%; line-height: 1.6em; background-color: #f6f6f6; margin: 0;" bgcolor="#f6f6f6">

<table class="body-wrap" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; width: 100%; background-color: #f6f6f6; margin: 0;" bgcolor="#f6f6f6"><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><td style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0;" valign="top"></td>
    <td class="container" width="600" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; display: block !important; max-width: 600px !important; clear: both !important; margin: 0 auto;" valign="top">
        <div class="content" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; max-width: 600px; display: block; margin: 0 auto; padding: 20px;">
            <div class="header" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; width: 100%; clear: both; color: #fff; margin: 0; padding: 10px; background: #193860; border-radius: 5px 6px 0px 0px !important;">
                <table width="100%" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                        <td class="aligncenter content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 12px; vertical-align: top; color: #999; text-align: center; margin: 0; padding: 5px 40px;" valign="top">
                            <h2 style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 30px; color: #fff; margin: 0; text-align: left;"> ' . $projectName . '</h2></td>
</tr></table></div>
<table class="main" width="100%" cellpadding="0" cellspacing="0" itemprop="action" itemscope itemtype="http://schema.org/ConfirmAction" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; border-radius: 3px; background-color: #fff; margin: 0; border-bottom: 1px solid #bec0c1;" bgcolor="#fff"><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><td class="content-wrap" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0;" valign="top">
    <meta itemprop="name" content="Confirm Email" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;" />
                <table class="main" width="100%" cellpadding="0" cellspacing="0" itemprop="action" itemscope itemtype="http://schema.org/ConfirmAction" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; border-radius: 3px; background-color: #fff; margin: 0; border: 1px solid #e9e9e9;" bgcolor="#fff">
<tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
        <td class="content-wrap" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 20px;" valign="top">
<strong>Dear Concern,</strong><br>Listed Documents/Certificate was under retention period and has been deleted from ' . $projectName . '.
</td>
</tr>

<tr>' . $mailbodyhtml . '</tr>

<tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0; font-weight: bold;"><td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px 20px 30px;" valign="top">
Thank You,
<br> ' . $projectName . ' Team.
</td>
</tr>
</table>
</div>
</td>
</td>
</tr>
 </table>
<div class="footer" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; width: 100%; clear: both; color: #999; margin: 0; padding: 1px;">
    <table width="100%" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
<td class="aligncenter content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 12px; vertical-align: top; color: #999; text-align: left; margin: 0; padding: 0 0 30px;" align="center" valign="top"> 
<a href="#" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 13px; color: #999; margin: 0; text-decoration: none;"><i>This is a system generated mail please do not reply.</i></a> <a href="http://' . $_SERVER['HTTP_HOST'] . '" style="color:blue;">http://' . $_SERVER['HTTP_HOST'] . '</a></td>
        </tr></table></div>
  </body>
</html>';

    date_default_timezone_set('Asia/Kolkata');
    $date = date("Y-m-d H:i:s");
    require_once '../application/PHPMailer/PHPMailerAutoload.php';

    $mail = new PHPMailer;
    $mail->isSMTP();
    $mail->SMTPDebug = 0;
    $mail->Debugoutput = 'html';
    $mail->Host = EMAIL_HOST;
    $mail->Port = EMAIL_PORT;
    $mail->SMTPAuth = true;
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );

    $mail->Username = EMAIL_USERNAME;
    $mail->Password = EMAIL_PASSWORD;
    $mail->setFrom(EMAIL_SETFROM, 'admin');

    $sendmailids = array();
    $kj = 0;
    foreach ($asinUserEmailIds as $email => $name) {
        $mail->addAddress($email, $name);
        $sendmailids[$kj] = $email;
        $kj++;
    }
    //$mail->addAddress($mailTo, $userName);
    $mail->addBCC("ezeefileadmin@cbsl-india.com", "Admin");
    $mail->Subject ='Documents/Certificate retention period over.';

    $mail->msgHTML($msgbody);
    $mail->AltBody = 'New Notifiation from ' . $projectName . '';
    //$mail->addAttachment('PHPMailer/examples/images/phpmailer_mini.png');
    $mail->CharSet = 'UTF-8';
    if (!$mail->send()) {
        //echo "Mailer Error: " . $mail->ErrorInfo;
        return false;
    } else {
       // $sender = $_SESSION['cdes_user_id'];
        $host = $_SERVER['REMOTE_ADDR'];
        $msgbody = mysqli_real_escape_string($db_con, $msgbody);
        //mysqli_set_charset($db_con,"latin1");
        //$mailsent = mysqli_query($db_con, "insert into tbl_mail_list(`send_by`, `send_cc`, `send_bcc`, `subject`, `mail_body`, `alt_body`, `mail_time`, `ip`, `module_name`) "
        //. "value('$sender','$mailto','','New Task Assigned !','$msgbody','New Notifiation from $projectName','$date','$host','Assign Task')") or die('Error' . mysqli_error($db_con));
        return true;
    }
}

function sharedDocumentsMail($projectName, $subject, $ToUser, $doclist, $db_con) {
    $shareUserEmailIds = array();
    $userEmail = mysqli_query($db_con, "SELECT * FROM tbl_user_master where user_id in($ToUser)");
    while ($rwuserEmail = mysqli_fetch_assoc($userEmail)) {
        $shareUserEmailIds[$rwuserEmail['user_email_id']] = $rwuserEmail['first_name'] . ' ' . $rwuserEmail['last_name'];
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

        <table class="body-wrap" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; width: 100%; background-color: #f6f6f6; margin: 0;" bgcolor="#f6f6f6"><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><td style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0;" valign="top"></td>
                <td class="container" width="600" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; display: block !important; max-width: 600px !important; clear: both !important; margin: 0 auto;" valign="top">
                    <div class="content" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; max-width: 600px; display: block; margin: 0 auto; padding: 20px;">
                        <div class="header" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; width: 100%; clear: both; color: #fff; margin: 0; padding: 10px; background: #193860; border-radius: 5px 6px 0px 0px !important;">
                            <table width="100%" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
<td class="aligncenter content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 12px; vertical-align: top; color: #999; text-align: center; margin: 0; padding: 5px 40px;" valign="top">
<h2 style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 30px; color: #fff; margin: 0; text-align: left;"> ' . $projectName . '</h2></td>
</tr></table></div>
<table class="main" width="100%" cellpadding="0" cellspacing="0" itemprop="action" itemscope itemtype="http://schema.org/ConfirmAction" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; border-radius: 3px; background-color: #fff; margin: 0; border: 1px solid #e9e9e9;" bgcolor="#fff">


<tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
<td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px 20px 30px;" valign="top">
<strong>Dear user, </strong>
<p>Below documents list are shared with you.</p>
' . $doclist . '
</td>
</tr>
<tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0; font-weight: bold;">
<td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px 20px 30px;" valign="top">
Thank You,
<br> ' . $projectName . ' Team.
</td>
</tr>
</table>
<div class="footer" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; width: 100%; clear: both; color: #999; margin: 0; padding: 1px;">
<table width="100%" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
<td class="aligncenter content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 12px; vertical-align: top; color: #999; text-align: left; margin: 0; padding: 0 0 30px;" align="center" valign="top"> 
<a href="#" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 13px; color: #999; margin: 0; text-decoration: none;"><i>This is a system generated mail please do not reply.</i></a> <a href="http://' . $_SERVER['HTTP_HOST'] . '" style="color:blue;">http://' . $_SERVER['HTTP_HOST'] . '</a></td>
</tr></table></div>

</body>
</html>';
    date_default_timezone_set('Asia/Kolkata');
    $date = date("Y-m-d H:i:s");
    require_once './application/PHPMailer/PHPMailerAutoload.php';
    $mail = new PHPMailer;
    $mail->isSMTP();
    $mail->SMTPDebug = 0;
    $mail->Debugoutput = 'html';
    $mail->Host = EMAIL_HOST;
    $mail->Port = EMAIL_PORT;
    $mail->SMTPAuth = true;
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );

    $mail->Username = EMAIL_USERNAME;
    $mail->Password = EMAIL_PASSWORD;
    $mail->setFrom('ezeefileadmin@cbslgroup.in', 'System');
   
    $sendemail = array();
    $kk = 0;
    foreach ($shareUserEmailIds as $emailto => $name) {
       $mail->addAddress($emailto, $name);
        $sendemail[$kk] = $emailto;
        $kk++;
    }

    //$mail->addBCC('soft.dev5@cbsl-india.com');
    $mail->Subject = $subject;
    $mail->msgHTML($msgbody);
    $mail->AltBody = 'New Notifiation from ' . $projectName . '';

    $mail->CharSet = 'UTF-8';
    if (!$mail->send()) {
        return false;
    } else {
        return true;
    }
}


function requestUnlockDocument($projectName, $username, $to, $toname,$files,$storageFiles) {
    


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
										Dear '.$toname.' <br>From '.$username .'<br>
                                                                               Request for unlock document <b>'.$files.'</b> located in storage <b>'.$storageFiles.'</b>
										
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
    $mail->Host = EMAIL_HOST;
    $mail->Port = EMAIL_PORT;
    $mail->SMTPAuth = true;
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );
    
    //$to = "soft.dev4@cbsl-india.com";
    $mail->Username = EMAIL_USERNAME;
    $mail->Password = EMAIL_PASSWORD;
    $mail->setFrom('ezeefileadmin@cbslgroup.in', 'System');
    //$mail->addReplyTo('', '');
    $mail->addAddress($to, $toname);
    //$mail->addAddress($superiorEmail, $superiorName);
    //$mail->addBCC('soft.dev5@cbsl-india.com');
    $mail->Subject = "New File Lock Request";
    $mail->msgHTML($msgbody);
    $mail->AltBody = 'New Notifiation from ' . $projectName . '';
    
//    foreach($files as $docpath){
//        
//        $mail->addAttachment(''.$docpath.'');
//        
//    }
	$mail->CharSet = 'UTF-8';							 
    if (!$mail->send()) {
        return false;
    } else {
//        foreach($files as $docpath){
//            unlink($docpath);
//        }
        return true;
    }
}


function mailResetPassFolder($to, $pass,$storage,$username) {

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
										Your new password for storage ' . $storage . ' is <br>
                                                                                
										<strong>Password </strong> - ' . $pass . '<br>
                                                                                    
										<strong>Url - </strong>http://' . $_SERVER['HTTP_HOST'] . '<br>
									</td>
								</tr><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><td class="content-block" itemprop="handler" itemscope itemtype="http://schema.org/HttpActionHandler" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 20px;" valign="top">
										This is a System Generated mail please do not reply.
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
    $mail->Host = EMAIL_HOST;
    $mail->Port = EMAIL_PORT;
    $mail->SMTPAuth = true;
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );
    $mail->Username = EMAIL_USERNAME;
    $mail->Password = EMAIL_PASSWORD;
    $mail->setFrom(EMAIL_SETFROM, 'System');
    //$mail->addReplyTo('', '');
    $mail->addAddress($to, $username);
    //$mail->addAddress($superiorEmail, $superiorName);
    //$mail->addBCC('soft.dev5@cbsl-india.com');
    $mail->Subject = "New Login password for $projectName";
    $mail->msgHTML($msgbody);
    $mail->AltBody = 'New Notifiation from ' . $projectName . '';
    $mail->CharSet = 'UTF-8';
    //$mail->addAttachment('PHPMailer/examples/images/phpmailer_mini.png');
    if (!$mail->send()) {
        return false;
    } else {

        return true;
    }
}
function mailChangingMultipleUsersPassword($to, $pass, $projectName, $username, $usernames, $totalUsers) {

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
                        <div class="header" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; width:100%; clear: both; color: #fff; margin: 0; padding: 10px; background: #193860; border-radius: 5px 6px 0px 0px !important;">
                            <table width="100%" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                    <td class="aligncenter content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 12px; vertical-align: top; color: #999; text-align: center; margin: 0; padding: 5px 40px;" valign="top">
                                       <h2 style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 30px; color: #fff; margin: 0; text-align: left;"> ' . $projectName . '</h2></td>
                                </tr></table></div>
                                <table class="main" width="100%" cellpadding="0" cellspacing="0" itemprop="action" itemscope itemtype="http://schema.org/ConfirmAction" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; border-radius: 3px; background-color: #fff; margin: 0; border-bottom: 1px solid #bec0c1;" bgcolor="#fff"><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><td class="content-wrap" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px 50px 0px;" valign="top">
                                    <meta itemprop="name" content="Confirm Email" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;" />
                                    <table width="100%" cellpadding="0" cellspacing="0" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                        <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                            <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 20px;" valign="top"><br>
                                                             <h4>Password of ' . $totalUsers . ' users updated</h4>
                                                               <p>' . $usernames . '</p>  
                                                             <p><strong>Password </strong> - ' . $pass . '</p>
							   </td>
                                                    </tr><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0; font-weight: bold;"><td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 30px;" valign="top">
                                                        Thank You,
                                                        <br> ' . $projectName . ' Team.
                                                    </td>
                                                </tr></table></td>
                                                </tr></table><div class="footer" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; width: 100%; clear: both; color: #999; margin: 0; padding: 1px;">
                                                    <table width="100%" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                            <td class="aligncenter content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 12px; vertical-align: top; color: #999; text-align: left; margin: 0; padding: 0 0 20px;" align="center" valign="top"> <a href="#" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 13px; color: #999; margin: 0; text-decoration: none;"><i>This is a system generated mail please do not reply. Track, manage and store your documents and reduce paper.</i></a> <a href="http://' . $_SERVER['HTTP_HOST'] . '" style="color:blue;">http://' . $_SERVER['HTTP_HOST'] . '</a></td>
                                                        </tr></table></div>
                                                        </div>
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
    $mail->Host = EMAIL_HOST;
    $mail->Port = EMAIL_PORT;
    $mail->SMTPAuth = true;
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );
    $mail->Username = EMAIL_USERNAME;
    $mail->Password = EMAIL_PASSWORD;
    $mail->setFrom(EMAIL_SETFROM, 'System');
    //$mail->addReplyTo('', '');
    $mail->addAddress($to, $username);
    //$mail->addAddress($superiorEmail, $superiorName);
    //$mail->addBCC('soft.dev5@cbsl-india.com');
    $mail->Subject = "New Login password for $projectName";
    $mail->msgHTML($msgbody);
    $mail->AltBody = 'New Notifiation from ' . $projectName . '';
    $mail->CharSet = 'UTF-8';
    //$mail->addAttachment('PHPMailer/examples/images/phpmailer_mini.png');
    if (!$mail->send()) {
        return false;
    } else {

        return true;
    }
}

function mailsenttoDocumentSubscribeUsers($to, $documentName, $projectName, $fileaction, $username) {

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
                        <div class="header" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; width:100%; clear: both; color: #fff; margin: 0; padding: 10px; background: #193860; border-radius: 5px 6px 0px 0px !important;">
                            <table width="100%" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                    <td class="aligncenter content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 12px; vertical-align: top; color: #999; text-align: center; margin: 0; padding: 5px 40px;" valign="top">
                                       <h2 style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 30px; color: #fff; margin: 0; text-align: left;"> ' . $projectName . '</h2></td>
                                </tr></table></div>
                                <table class="main" width="100%" cellpadding="0" cellspacing="0" itemprop="action" itemscope itemtype="http://schema.org/ConfirmAction" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; border-radius: 3px; background-color: #fff; margin: 0; border-bottom: 1px solid #bec0c1;" bgcolor="#fff"><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><td class="content-wrap" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 5px 50px 0px;" valign="top">
                                    <meta itemprop="name" content="Confirm Email" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;" />
                                    <table width="100%" cellpadding="0" cellspacing="0" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                        <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                            <td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 20px;" valign="top"><br>
                                                             <h4>Document subscription alert</h4>
                                                               <p><strong>Action :- </strong>' . $fileaction . '</p>
                               </td>
                                                    </tr><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0; font-weight: bold;"><td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 30px;" valign="top">
                                                        Thank You,
                                                        <br> ' . $projectName . ' Team.
                                                    </td>
                                                </tr></table></td>
                                                </tr></table><div class="footer" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; width: 100%; clear: both; color: #999; margin: 0; padding: 1px;">
                                                    <table width="100%" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                            <td class="aligncenter content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 12px; vertical-align: top; color: #999; text-align: left; margin: 0; padding: 0 0 20px;" align="center" valign="top"> <a href="#" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 13px; color: #999; margin: 0; text-decoration: none;"><i>This is a system generated mail please do not reply. Track, manage and store your documents and reduce paper.</i></a> <a href="' . BASE_URL . '" style="color:blue;">' . BASE_URL . '</a></td>
                                                        </tr></table></div>
                                                        </div>
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
    $mail->Host = EMAIL_HOST;
    $mail->Port = EMAIL_PORT;
    $mail->SMTPAuth = true;
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );


   

    $mail->Username = EMAIL_USERNAME;
    $mail->Password = EMAIL_PASSWORD;
    $mail->setFrom(EMAIL_SETFROM, 'System');
    //$mail->addReplyTo('', '');
    $mail->addAddress($to, $username);
    //$mail->addAddress($superiorEmail, $superiorName);
    //$mail->addBCC('soft.dev5@cbsl-india.com');
    $mail->Subject = "Document Alert";
    $mail->msgHTML($msgbody);
    $mail->AltBody = 'New Notifiation from ' . $projectName . '';
    $mail->CharSet = 'UTF-8';
    //$mail->addAttachment('PHPMailer/examples/images/phpmailer_mini.png');
    if (!$mail->send()) {
        return false;
    } else {

        return true;
    }
}





// function mailBySocket($paramsArray) {
//     //$url = 'http://192.168.2.63/ezeefile_cbsl/trunk/mail.php';
//     //$url = 'http://localhost/workspace/ezeefile_cbsl/trunk/mail.php';
//     $url = BASE_URL.'mail.php';
    
//     $params = $paramsArray;

//     foreach ($params as $key => &$val) {
//         if (is_array($val))
//             $val = implode(',', $val);
//         $post_params[] = $key . '=' . urlencode($val);
//     }
//     $post_string = implode('&', $post_params);
//     $parts = parse_url($url);
//     // if($_SERVER['HTTP_HOST']=="dms.cbslgroup.in")
//     // {
//     // $fp = fsockopen('ssl://'. $parts['host'], isset($parts['port']) ? $parts['port'] : 443, $errno, $errstr, 30);
//     // }
//     // else
//     // {
//         // //$fp = fsockopen('ssl://'. $parts['host'], isset($parts['port']) ? $parts['port'] : 443, $errno, $errstr, 30);

//     // $fp = fsockopen($parts['host'], isset($parts['port']) ? $parts['port'] : 80, $errno, $errstr, 30000000);
//     // }
// 	$fp = fsockopen('ssl://'. $parts['host'], isset($parts['port']) ? $parts['port'] : 443, $errno, $errstr, 30);
//     $out = "POST " . $parts['path'] . " HTTP/1.1\r\n";
//     $out .= "Host: " . $parts['host'] . "\r\n";
//     $out .= "Content-Type: application/x-www-form-urlencoded\r\n";
//     $out .= "Content-Length: " . strlen($post_string) . "\r\n";
//     $out .= "Connection: Close\r\n\r\n";
//     if (isset($post_string))
//     $out .= $post_string;

//     fwrite($fp, $out);
//    //$server_response = fread($fp, 4096);
//     //$file=fopen('error.txt', "a");
//     //fwrite($file, $server_response);

//     //fclose($file);
//     fclose($fp);

// }
function mailBySocket($paramsArray)
{
    //$url = 'http://192.168.2.63/ezee_lopts/mail.php';
    $url = BASE_URL . '/mail.php';
    
    //$url = 'http://144.48.78.35/Testing/ezeeoffice_core/mail.php';
    //$url = 'http://dms.cbslgroup.in/mail.php';

    $params = $paramsArray;


    foreach ($params as $key => &$val) {
        if (is_array($val))
            $val = implode(',', $val);
        $post_params[] = $key . '=' . urlencode($val);
    }

    $post_string = implode('&', $post_params);
    $parts = parse_url($url);

    $fp = fsockopen($parts['host'], isset($parts['port']) ? $parts['port'] : 80, $errno, $errstr, 30);
    $out = "POST " . $parts['path'] . " HTTP/1.1\r\n";
    $out .= "Host: " . $parts['host'] . "\r\n";
    $out .= "Content-Type: application/x-www-form-urlencoded\r\n";
    $out .= "Content-Length: " . strlen($post_string) . "\r\n";
    $out .= "Connection: Close\r\n\r\n";
    if (isset($post_string))
        $out .= $post_string;

    fwrite($fp, $out);
    // $server_response = fread($fp, 4096);
    // print_r($server_response);
    // die("rrrrrrr");
    //$file=fopen('error.txt', "a");
    //fwrite($file, $server_response);
    //fclose($file);
    fclose($fp);
}

if(isset($_POST['action']))
{
    //include('sessionstart.php');



    require_once './application/config/database.php';

    
    if($_POST['action']=='assignTask'){


        $ticket = $_POST['ticket'];
        $idins = $_POST['idins'];
        $projectName = $_POST['projectName'];

        assignTask($ticket, $idins, $db_con, $projectName);

    }else if($_POST['action']=='completeTask'){
        
       $ticket = $_POST['ticket']; 
       $id = $_POST['id']; 
       $wfid = $_POST['wfid']; 
       $projectName = $_POST['projectName']; 
       
       $user_id= "";
       if(isset($_POST['user_id'])){
           $user_id= $_POST['user_id'];
       }
       completeTask($ticket, $id, $wfid, $db_con, $projectName, $user_id);
       
    }else if($_POST['action']=='abortTask'){
        
       $ticket = $_POST['ticket']; 
       $id = $_POST['id']; 
       $wfid = $_POST['wfid']; 
       $projectName = $_POST['projectName']; 
       
        abortTask($ticket, $id, $wfid, $db_con, $projectName);
       
    }else if($_POST['action']=='rejectTask'){

       $id = $_POST['id']; 
       $ctaskID = $_POST['ctaskID']; 
       $tktId = $_POST['tktId']; 
       $comment = $_POST['comment']; 
       $docID = $_POST['docID']; 
       $projectName = $_POST['projectName']; 
       $approvedByIds = $_POST['approvedByIds']; 
       
       
       
       rejectTask($id, $ctaskID, $tktId, $db_con, $projectName, $comment, $docID, $approvedByIds);
       
    }else if($_POST['action']=='assignReview'){
        
       $ticket = $_POST['ticket'];
        $idins = $_POST['idins'];
        $projectName = $_POST['projectName'];
        $subject = $_POST['subject'];
        
        assignReview($ticket, $idins, $db_con, $projectName, $subject);

    }else if($_POST['action']=='assignNextReview'){
        
        $ticket = $_POST['ticket'];
        $idins = $_POST['idins'];
        $projectName = $_POST['projectName'];
        $subject = $_POST['subject'];

        assignNextReview($ticket, $idins, $db_con, $projectName, $subject);

    }else if($_POST['action']=='completeReview'){
        
        $ticket = $_POST['ticket'];
        $projectName = $_POST['projectName'];
        $subject = $_POST['subject'];

        completeReview($ticket, $db_con, $projectName, $subject);

    }else if ($_POST['action'] == 'sharedocument') {
        $projectName = $_POST['projectName'];
        $ToUser = $_POST['ToUser'];
        $doclist = $_POST['doclist'];
        $projectName = $_POST['projectName'];
        $subject = $_POST['subject'];
        sharedDocumentsMail($projectName, $subject, $ToUser, $doclist, $db_con);
    } elseif ($_POST['action'] == 'filesubscribe') {
        $email = $_POST['email'];
        $filenamed = $_POST['filenamed'];
        $fileaction = $_POST['fileaction'];
        $name = $_POST['name'];
        $projectName = $_POST['projectName'];
        mailsenttoDocumentSubscribeUsers($email, $filenamed, $projectName, $fileaction, $name);
    } elseif ($_POST['action'] == 'maildocumentoutside') {
        $subject = $_POST['subject'];
        $mailbody = $_POST['mailbody'];
        $username = $_POST['username'];
        $to = $_POST['to'];
        $docIds = $_POST['docIds'];
        $projectName = $_POST['projectName'];
        mailDocuments($projectName, $subject, $mailbody, $username, $to, $docIds);
        
    }else if($_POST['action']=='mailTestBySocket'){
        
       mailTestBySocket($_POST['name']);
    } 
}


function getDocumentFromFTP($destinationPath, $sourcePath){

   // global $fileserver, $port, $ftpUser, $ftpPwd;

    require_once './classes/fileManager.php';  
    require_once './application/pages/function.php'; 


	$fileManager = new fileManager();
	// Connect to file server
	$fileManager->conntFileServer();
	if($fileManager->downloadFile($sourcePath, ROOT_FTP_FOLDER . '/' . $destinationPath)){
			
		decrypt_my_file($sourcePath);
			
		return true;
	}else{
		 return false;
	}	
        
    /* if(FTP_ENABLED){

        $ftp = new ftp();
        $ftp->conn($fileserver, $port, $ftpUser, $ftpPwd);
        if($ftp->get($destinationPath, ROOT_FTP_FOLDER.'/'.$sourcePath))
        {
             //decrypt file
             decrypt_my_file($destinationPath);

        }else{

//            $arr = $ftp->getLogData();
//            if ($arr['error'] != "") {
//                echo '<h2>Error:</h2>' . implode('<br />', $arr['error']);
//            }
        }
    } */
}


function mailTestBySocket($name) {

    $msgbody = 'Hi '.$name.', <br> It is working';


    date_default_timezone_set('Asia/Kolkata');
    $date = date("Y-m-d H:i:s");

    require_once './application/PHPMailer/PHPMailerAutoload.php';
    $mail = new PHPMailer;
    $mail->isSMTP();
    $mail->SMTPDebug = 0;
    $mail->Debugoutput = 'html';
    $mail->Host = EMAIL_HOST;
    $mail->Port = EMAIL_PORT;
    $mail->SMTPAuth = true;
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );
    $mail->Username = EMAIL_USERNAME;
    $mail->Password = EMAIL_PASSWORD;
    $mail->setFrom(EMAIL_SETFROM, 'System');
    //$mail->addReplyTo('', '');
    //$email = 'soft.dev4@cbsl-india.com';
    //$mail->addAddress('soft.dev4@cbsl-india.com', 'Manendra');

    //$mail->addBCC('ezeefileadmin@cbsl-india.com');
    $mail->Subject = 'Mail Verification';
    $mail->msgHTML($msgbody);
    $mail->AltBody = 'New Notifiation from DMS';
    //$mail->addAttachment('PHPMailer/examples/images/phpmailer_mini.png');
    if (!$mail->send()) {
        return false;
    } else {

        return true;
    }
}
?>