<?php
/*
 * Class for API
 */
    
class SMSAPI
{
    public  $username; //username of the message api
    public $password ; // password of the message api
    public  $senderId ; //Sender Id
    public $channel;//channel
    public $DCS,$flashsms,$route;//DCS Flashcheck route
 
    function __construct() {

        $this->username = "CBSL"; //username of the message api 
        $this->password = "123456"; // password of the message api
        $this->senderId = "EZEEFL"; //Sender Id
        $this->channel = "2"; //Channel Id
        $this->DCS = 0; //Channel Id
        $this->flashsms = 0; //Channel Id
        $this->route = 4; //Channel Id
       /*
        * This method for send otp message for login
        */
    }
    public function sendOtpSMS($to, $module, $fourRandomDigit) 
    {
        if($module == 'login' || $module == 'forgotpwd'){
            $msgbody = "Dear user, Your OTP for login to EZEEFILE portal is ".$fourRandomDigit." Valid for 10 minutes. Please don't share this OTP. Regards EZEEFILE";    
        }
//        elseif($module == 'forgotpwd'){
//            $msgbody = "Dear user, Your OTP for reset password to EZEEFILE portal is ".$fourRandomDigit." Valid for 10 minutes. Please don't share this OTP. Regards EZEEFILE";    
//        }
        else{
           return array("error"=>FALSE,"msg"=>'Wrong params');
        }
        $username = "CBSL"; //username of the message api 
        $password = "123456"; // password of the message api
        $senderId = "EZEEFL"; //Sender Id
        $channel = "2"; //Channel Id
        $DCS = 0; //Channel Id
        $flashsms = 0; //Channel Id
        $route = 4; //Channel Id
        $module='login';

         $url =  "http://bulksms.anksms.com/api/mt/SendSMS?user=$username&password=$password&senderid=$senderId&channel=$channel&DCS=$DCS&flashsms=$flashsms&number=$to&text=". urlencode($msgbody); 
        //$url = "http://bulksms.anksms.com/api/mt/SendSMS?user=$this->username&password=$this->password&senderid=$this->senderId&channel=$this->channel&DCS=$this->DCS&flashsms=$this->flashsms&number=$to&text=$msgbody";
        
       $curl_request = curl_init();
       if (!$curl_request){
            die("Couldn't initialize a cURL handle");
        }
        curl_setopt($curl_request, CURLOPT_URL, $url);
        curl_setopt($curl_request, CURLOPT_CUSTOMREQUEST, "GET");
        //curl_setopt($curl_request, CURLOPT_HEADER, 1);
        curl_setopt($curl_request, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl_request, CURLOPT_RETURNTRANSFER, true);
        
        $result = curl_exec($curl_request);
    	//if(!$result){
            //die('Error: "' . curl_error($curl_request) . '" - Code: ' . curl_errno($curl_request));
	//} 
        curl_close($curl_request);	
    
        $response = json_decode($result, true) or die(print_r(error_get_last()));
     
//      $url = filter_var($url, FILTER_SANITIZE_URL);
//      $response = file_get_contents($url);
//      $response= json_decode($response, TRUE);
//      echo $response; 
        if ($response['ErrorCode']=="000") {
                return array("status"=>TRUE,"developer_msg"=>$response['ErrorMessage']);
        } else {
                return array("status"=>FALSE,"developer_msg"=>$response['ErrorMessage']);
        }
    }

}

?>