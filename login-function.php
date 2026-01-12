<?php

function absolute_url($page) {
    if (isset($_SERVER['HTTPS'])) {
        $url = 'https://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);
    } else {
        $url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);
    }
    $url = rtrim($url, '/\\');

    $url .= '/' . $page;

    return $url;
}

// function ends here



function check_login($dbc, $email = '', $pass = '', $captcha = '', $hidden_captcha = '', $date, $lang, $passExpiry, $passExpdays, $captcha_enabled, $concurrent_user) {

    $errors = array();

    //validate email

    if (empty($email)) {

        $errors =  $lang['yfteyea'];
    } else {

        $e = mysqli_real_escape_string($dbc, trim($email));
    }

    //validat password

    if (empty($pass)) {

        $errors =  $lang['yfteyp'];
    } else {

        $p = mysqli_real_escape_string($dbc, trim($pass));
    }
    if (empty($captcha) && $captcha_enabled==1) {

        $errors = $lang['yfteyc'];
    } else {

        $c = mysqli_real_escape_string($dbc, trim($captcha));
    }

    if ($captcha == $hidden_captcha || $captcha_enabled!=1) {

        if (empty($errors)) {

            $q = "SELECT  * FROM tbl_user_master WHERE (user_email_id='$e' OR emp_id='$e') AND password=SHA1('$p')";

            $r = mysqli_query($dbc, $q) or die('Error' . mysqli_error($dbc));

            //check the results

            if (mysqli_num_rows($r) == 1) {

                if($concurrent_user!=0 AND mysqli_num_rows($check_concurrent) >= $concurrent_user){
                    $m_error = 'Your user login limit exceeded.';
                    return array(2, $m_error);
                }else{

                    $row = mysqli_fetch_array($r, MYSQLI_ASSOC);
                    // for checked password expiry
                    if ($passExpiry == 0) {
                        $expriytime = strtotime($row['last_pass_change']) + (1000 * 24 * 60 * 60);
                    } else {
                        $expriytime = strtotime($row['last_pass_change']) + $passExpdays;
                    }
                   
                   return array(true, $row);
                }
            } else{
                    
                    return array(0, $lang['invalid_login']);
                }
            
        }
    } else {
        $errors = $lang['invalid_captcha'];
        return array(3, $errors);
    }
}

//end of check_login() function
?>

<!-- function check_login($dbc, $email = '', $pass = '', $captcha = '', $hidden_captcha = '', $date, $lang, $passExpiry, $passExpdays, $captcha_enabled, $concurrent_user) {

    $errors = array();

    //validate email

    if (empty($email)) {

        $errors =  $lang['yfteyea'];
    } else {

        $e = mysqli_real_escape_string($dbc, trim($email));
    }

    //validat password

    if (empty($pass)) {

        $errors =  $lang['yfteyp'];
    } else {

        $p = mysqli_real_escape_string($dbc, trim($pass));
    }
    if (empty($captcha) && $captcha_enabled==1) {

        $errors = $lang['yfteyc'];
    } else {

        $c = mysqli_real_escape_string($dbc, trim($captcha));
    }

    if ($captcha == $hidden_captcha || $captcha_enabled!=1) {

        if (empty($errors)) {

            $q = "SELECT  * FROM tbl_user_master WHERE (user_email_id='$e' OR emp_id='$e') AND password=SHA1('$p')";

            $r = mysqli_query($dbc, $q) or die('Error' . mysqli_error($dbc));

            //check the results

            if (mysqli_num_rows($r) == 1) {

                if($concurrent_user!=0 AND mysqli_num_rows($check_concurrent) >= $concurrent_user){
                    $m_error = 'Your user login limit exceeded.';
                    return array(2, $m_error);
                }else{

                    $row = mysqli_fetch_array($r, MYSQLI_ASSOC);
                    // for checked password expiry
                    if ($passExpiry == 0) {
                        $expriytime = strtotime($row['last_pass_change']) + (1000 * 24 * 60 * 60);
                    } else {
                        $expriytime = strtotime($row['last_pass_change']) + $passExpdays;
                    }
                   
                    if ((strtotime($date) < $expriytime) || empty($row['last_pass_change'])) {
                        
                        $activate_qry = mysqli_query($dbc, "SELECT  * FROM tbl_user_master WHERE (user_email_id='$e' OR emp_id='$e') AND password=SHA1('$p') and active_inactive_users=1"); //or die(mysqli_error($dbc));
                        if (mysqli_num_rows($activate_qry) == 1) {
                            $checkLockout = mysqli_query($dbc, "SELECT failed_login_attempts FROM tbl_user_master WHERE (user_email_id='$e' OR emp_id='$e') AND password=SHA1('$p') AND failed_login_attempts >= 5"); //or die(mysqli_error($dbc));
                            if (mysqli_num_rows($checkLockout) <= 0) {
                                $lock = mysqli_query($dbc, "update tbl_user_master set failed_login_attempts=0 where user_email_id='$e'");
                               
                                return array(true, $row);
                            } else {
                                $lock = mysqli_query($dbc, "update tbl_user_master set active_inactive_users=0 where user_email_id='$e'");
                                return array(2, "Your account is temporarily blocked. You have exceeded maximum(5) failed login attempts");
                            }
                        } else {
                            $errors = $lang['yacd'];
                            return array(4, $errors);
                        }
                    }else{
                         return array(6, $row);
                    }
                }
            } else {
                $checkLockout = mysqli_query($dbc, "SELECT failed_login_attempts FROM tbl_user_master WHERE (user_email_id='$e' OR emp_id='$e')"); //or die(mysqli_error($dbc));
               
                if(mysqli_num_rows($checkLockout)>0){
                     $rwCheck = mysqli_fetch_assoc($checkLockout);
                    if ($rwCheck['failed_login_attempts'] < 5) {
                        $lock = mysqli_query($dbc, "update tbl_user_master set failed_login_attempts=failed_login_attempts+1 where user_email_id='$e'");
                        $attemptsleft = 4 - $rwCheck['failed_login_attempts'];
                        $errors = 'user and pwd does not matched  ';
                         $err_msg = str_replace("attemptsleft", $attemptsleft, $lang['uopii_yhla']);
                        return array(0, $err_msg);
                    } else {
                        $lock = mysqli_query($dbc, "update tbl_user_master set active_inactive_users=0 where user_email_id='$e'");
                        return array(2, $lang['yatb_yhemfla']);
                    }
                
                }else{
                    
                    return array(0, $lang['invalid_login']);
                }
            }
        }
    } else {
        $errors = $lang['invalid_captcha'];
        return array(3, $errors);
    }
} -->
