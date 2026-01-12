<?php

error_reporting(0);
require_once '/var/www/ezeefile_saas/application/config/database.php';
$time = time();
$time7 = $time + (60 * 60 * 24 * 60);
$sql = mysqli_query($db_con, "select * from tbl_client_master where valid_upto <='$time7' and valid_upto >='$time' order by valid_upto asc");
if (mysqli_num_rows($sql) > 0) {
    $data .= '<table border="1" width="100%" cellpadding="0" cellspacing="0" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><thead><tr>';
    $data .= '<th>S.No</th>                                                            
                                                            <th>Company Name</th>
                                                            <th>Client Name</th>                                                            
                                                            <th>Expiry Date</th>
                                                            <th>Domain Name</th>                                                            
                                                        </tr>                                                        
                                                    </thead><tbody>';
    $i = 1;
    while ($rwClient = mysqli_fetch_assoc($sql)) {
        $data .= '<tr>';
        $data .= '<td style="padding: 0 0 0 2px;">' . $i . '</td>';
        $data .= '<td style="padding: 0 0 0 2px;">' . $rwClient['company'] . '</td>';
        $data .= '<td style="padding: 0 0 0 2px;">' . $rwClient['fname'] . " " . $rwClient['lname'] . '</td>';
        $data .= '<td style="padding: 0 0 0 2px;">' . date("d-M-Y", $rwClient['valid_upto']) . '</td>';
        $data .= '<td style="padding: 0 0 0 2px;">' . $rwClient['subdomain'] . '</td></tr>';
        $i++;
    }
    $data .= " </tbody></table>";

    sendClientValidityMail($data);
    $fp = fopen("schedule.txt", "wb");
    fwrite($fp, $data);
    fclose($fp);
    session_destroy();
}

function sendClientValidityMail($data) {

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
                <td class="container" width="600" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; display: block !important; max-width: 700px !important; clear: both !important; margin: 0 auto;" valign="top">
                    <div class="content" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; max-width: 700px; display: block; margin: 0 auto; padding: 20px;">
                        <table class="main" width="100%" cellpadding="0" cellspacing="0" itemprop="action" itemscope itemtype="http://schema.org/ConfirmAction" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; border-radius: 3px; background-color: #fff; margin: 0; border: 1px solid #e9e9e9;" bgcolor="#fff"><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><td class="content-wrap" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 20px;" valign="top">
                                    <meta itemprop="name" content="Confirm Email" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;" /><table width="100%" cellpadding="0" cellspacing="0" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 20px;" valign="top">
                                                Validity of following Clients is going to expire within next 4 days : <br><br>

                                                    ' . $data . '<br>

                                                        
                                                            </td>
                                                        </tr><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><td class="content-block" itemprop="handler" itemscope itemtype="http://schema.org/HttpActionHandler" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 20px;" valign="top">
                                                            This is a System Generated mail please do not reply.
                                                        </td>
                                                    </tr><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0; font-weight: bold;"><td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 20px;" valign="top">
                                                            
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
    require_once '/var/www/ezeefile_saas/application/PHPMailer/PHPMailerAutoload.php';
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
    $mail->setFrom('no-reply@cbsl-india.com', 'Admin');
    $mail->addAddress("soft.dev6@cbsl-india.com", "Ajay");
//    $mail->addAddress("kinal.singh@cbsl-india.com", "Kunal Singh");
//    $mail->addCC("ezee.support@cbsl-india.com", "Satyanand");
//    $mail->addBCC('soft.dev6@cbsl-india.com', "Ajay Kr. Sharma");
    $mail->Subject = "Validity Expiry of SAAS Client";
    $mail->msgHTML($msgbody);
    $mail->CharSet = 'UTF-8';
    if (!$mail->send()) {
        return false;
    } else {

        return true;
    }
}

?>