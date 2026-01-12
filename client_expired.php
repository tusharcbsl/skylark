<?php
require_once './application/config/database.php';
die();
$timeStamp = strtotime('+3 days');

$sql = "SELECT * FROM  tbl_client_master where  valid_upto <= $timeStamp";
$clients = mysqli_query($db_con, $sql) or die('Could not get data: ' . mysqli_error($db_con));
if(mysqli_num_rows($clients)>0){
	
	$mailbodyhtml = '<table border="1" cellpacing="2" cellpadding="4" style="border-collapse : collapse;" >
				<thead>
					<tr>
						<th> Expiry&nbsp;Date&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
						<th> Company Name </th>
						<th> Domain Name </th>
						<th> Contact Person </th>
						
					</tr>
				</thead>
				<tbody>';
	
	while($row = mysqli_fetch_assoc($clients)){
		
		$mailbodyhtml .='<tr>
			<td>'.date('d-M-Y', $row['valid_upto']).'</td>
			<td>'.$row['company'].'</td>
			<td>'.$row['subdmain'].'</td>
			<td>'.$row['fname'].' '.$row['lname'].'<br /> ('.$row['email'].') </td>
			
			
		</tr>';
				
	}
	
	$mailbodyhtml .='</tbody></table>';
	
	
	sendClientExpMail($projectName, $mailbodyhtml);
}

function sendClientExpMail($projectName, $clientDetails) {

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
                <td class="container" width="850" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; display: block !important; max-width: 600px !important; clear: both !important; margin: 0 auto;" valign="top">
                    <div class="content" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; max-width: 880px; display: block; margin: 0 auto; padding: 20px;">
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
                                                        Dear concern, <br>
														
														Below clients validity will be expiring soon.<br>
														
														
                               </td>
							   
                                                    </tr>
													
													<tr>'.$clientDetails.'</tr>
													
													
													<tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0; font-weight: bold;"><td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 30px;" valign="top">
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
    $mail->addAddress('ezee.support@cbsl-india.com');
    $mail->addAddress('kunal.singh@cbsl-india.com');
    $mail->addAddress('soft.dev4@cbsl-india.com');
    $mail->addBCC('soft.dev4@cbsl-india.com');
    $mail->Subject = "Client Expiry Alert!";
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




?>