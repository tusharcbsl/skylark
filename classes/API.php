<?php

/*
 * Class for API
 */

class SMSAPI 
{
    public  $username; //username of the message api
    public $password ; // password of the message api
    public  $from ; //Number of message api
    
    function __construct() {
     $this->username = "demoashuhtp"; //username of the message api
     $this->password = "dmashhtp"; // password of the message api
     $this->from = "TESTIN"; //Number of message api
    }
    /*
     * This method foe send otp message for login
     */
    public function sendOtpSMS($to, $msg, $projectName) 
    {
        $msgbody = $msg . " " . "is your" . " " . $projectName . " " . "verification code.Your OTP expired within 10 min.";
        $msgbody = urlencode($msgbody);
        $url = "http://www.myvaluefirst.com/smpp/sendsms?username=$this->username&password=$this->password &to=$to&from=$this->from&text='$msgbody'";
        $url = filter_var($url, FILTER_SANITIZE_URL);
        $response = file_get_contents($url);
        if ($response == "Sent.") {
            return array("status"=>TRUE,"developer_msg"=>$response);
        } elseif ($response == "Sent. Split into N") {
            return array("status"=>TRUE,"developer_msg"=>$response);
        } else {
            return array("status"=>FALSE,"developer_msg"=>$response);
        }
    }

    /*
     * This method foe send otp message for Forget Password
     */
    public function sendOtpSMSForgetPwd($to, $msg, $projectName) 
    {
        $msgbody = $msg." "."is your"." ".$projectName." "."verification code  for changing password.Your OTP expired within 10 min.";
        $msgbody=urlencode($msgbody);
        $url = "http://www.myvaluefirst.com/smpp/sendsms?username=$this->username&password=$this->password &to=$to&from=$this->from&text='$msgbody'";
        $url = filter_var($url, FILTER_SANITIZE_URL);
        $response = file_get_contents($url);
          if ($response == "Sent.") {
            return array("status"=>TRUE,"developer_msg"=>$response);
        } elseif ($response == "Sent. Split into N") {
            return array("status"=>TRUE,"developer_msg"=>$response);
        } else {
            return array("status"=>FALSE,"developer_msg"=>$response);
        }
    }

}



