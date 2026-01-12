<?php

// $User_ip=$_SERVER['REMOTE_ADDR'];
// $url = "https://ajax.googleapis.com/ajax/services/search/images?v=1.0&q=insure&userip=".$User_ip;

// // sendRequest
// // note how referer is set manually
// $ch = curl_init();
// curl_setopt($ch, CURLOPT_URL, $url);
// curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
// curl_setopt($ch, CURLOPT_REFERER, "http://dms.cbslgroup.in/assets/images/ezeefile.png");
// $body[] = curl_exec($ch);
// curl_close($ch);
// print_r($body);
// // now, process the JSON string
// echo $json = json_decode($body);
// // now have some fun with the results...
function curl_get_file_contents($URL)
    {
        $c = curl_init();
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($c, CURLOPT_URL, $URL);
        $contents = curl_exec($c);
        curl_close($c);

        if ($contents) return $contents;
            else return FALSE;
    }
	$url='BASE_URL/assets/images/ezeefile.png';
	//echo curl_get_file_contents($url);
	echo file_get_contents($url,rb);
?>