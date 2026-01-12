<?php

// diabling direct access of file
if ($_SERVER['REQUEST_METHOD'] == 'GET' && realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME'])) {
    header('HTTP/1.0 403 Forbidden', TRUE, 403);
    die(header('location:../../error.html'));
}
// ini_set('session.cookie_httponly', 1);
// ini_set('session.use_only_cookies', 1);
// ini_set('session.cookie_secure', 1);

header("Cache-Control: no-cache, must-revalidate"); //HTTP 1.1
header("Pragma: no-cache"); //HTTP 1.0
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
//or, if you DO want a file to cache, use:
header("Cache-Control: max-age=2592000"); //30days (60sec * 60min * 24hours * 30days)
header("X-XSS-Protection: 1; mode=block");
header("X-Frame-Options: SAMEORIGIN");
header("X-Content-Type-Options: nosniff");
header("Content-Security-Policy: default-src 'self';");
$urls=(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]" . '/';
$headerCSP = "Content-Security-Policy:".
        "connect-src 'self' http://csi.gstatic.com ".$urls."/*:". // XMLHttpRequest (AJAX request), WebSocket or EventSource.
        "default-src 'self' *:". // Default policy for loading html elements
        "frame-ancestors 'self':". //allow parent framing - this one blocks click jacking and ui redress
        "frame-src 'none':". // vaid sources for frames
        "media-src 'self' ".$urls."/*:". // vaid sources for media (audio and video html tags src)
        "object-src 'none':". // valid object embed and applet tags src
        "img-src 'self' https://www.gstatic.com/inputtools/images/suggestmenu_bg.png https://ssl.gstatic.com/editor/button-bg.png http://csi.gstatic.com/csi:". //Allowed sources for images
        "font-src 'self':". //Allowed sources for fonts

                //"report-uri https://example.com/violationReportForCSP.php;". //A URL that will get raw json data in post that lets you know what was violated and blocked
        "script-src 'self' 'unsafe-inline' https://cdn.polyfill.io/v2/polyfill.min.js https://www.google.com/jsapi https://www.google.com/uds/ http://www.google.com/inputtools/request http://csi.gstatic.com/csi:". // allows js from self, jquery and google analytics.  Inline allows inline js
        "style-src 'self' 'unsafe-inline' https://www.google.com/uds/api/elements/1.0/7ded0ef8ee68924d96a6f6b19df266a8/transliteration.css http://csi.gstatic.com/csi:";// allows css from self and inline allows inline css
//Sends the Header in the HTTP response to instruct the Browser how it should handle content and what is whitelisted
//Its up to the browser to follow the policy which each browser has varying support
header($headerCSP);
header("Strict-Transport-Security:max-age=63072000");
//disable end
ob_start();
@session_start();
//session_regenerate_id();
require_once 'classes/security.php';

//if(isset($_POST['token'])){
//    if(!csrfToken::validate($_POST['token'])){
//       header('location:access-denied.html');
//       exit();
//}
//}

?>
