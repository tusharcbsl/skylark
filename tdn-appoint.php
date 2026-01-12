<?php
error_reporting(0);
/** Date-Time 24Hrs Getter Function
 *
 * @return date_time returns Todays Date and Time in 24Hr IST format
 */
function getDateTime24() {
    date_default_timezone_set('Asia/Kolkata');
    return date('Y-m-d H:i:s');
}

/** Date-Time 12Hrs Getter Function
 *
 * @return date_time returns Todays Date and Time in 24Hr IST format
 */
function getDateTime12() {
    date_default_timezone_set('Asia/Kolkata');
    return date('Y-m-d h:i A');
}

/** Time 24Hrs Getter Function
 *
 * @return time returns Current Time in 24Hr IST format
 */
function getTime24() {
    date_default_timezone_set('Asia/Kolkata');
    return date('H:i:s');
}

/** Time 12Hrs Getter Function
 *
 * @return time returns Current Time in 12Hr IST format
 */
function getTime12() {
    date_default_timezone_set('Asia/Kolkata');
    return date('h:i A');
}

/** Date Getter Function
 *
 * @return date returns Todays Date
 */
function getCurrentDate() {
    date_default_timezone_set('Asia/Kolkata');
    return date('Y-m-d');
}

/** Date Getter Function
 *
 * @return date returns Tomorrows Date
 */
function getTommorowDate() {
    date_default_timezone_set('Asia/Kolkata');
    return date("Y-m-d", strtotime('tomorrow'));
}

/** Date Getter Function
 *
 * @return array returns Start and End date of week
 */
function getWeekDates() {
    date_default_timezone_set('Asia/Kolkata');
    $end_date = date("Y-m-d", strtotime('next Sunday'));
    $start_date = date("Y-m-d", strtotime('last Monday'));
    return array('start' => $start_date, 'end' => $end_date);
}

/** Date Formatter Function
 *  @param string date to format
 *  @param string delimeter to use for date seperation
 *
 * @return date returns Requested Format Date
 */
function formatDate($date, $delimeter) {
    $createDate = date_create($date);
    $dateFormat = 'Y' . $delimeter . 'm' . $delimeter . 'd';
    date_default_timezone_set('Asia/Kolkata');
    return date_format($createDate, $dateFormat);
}

/** 24Hrs Time Formatter Function
 *  @param string time to format
 *  @param string delimeter to use for time seperation
 *
 * @return date returns Requested Format Time
 */
function formatTime24($time, $delimeter) {
    $createTime = date_create($time);
    $timeFormat = 'H' . $delimeter . 'i';
    date_default_timezone_set('Asia/Kolkata');
    return date_format($createTime, $timeFormat);
}

/** 12Hrs Time Formatter Function
 *  @param string time to format
 *  @param string delimeter to use for time seperation
 *
 * @return date returns Requested Format Time
 */
function formatTime12($time, $delimeter) {
    $createTime = date_create($time);
    $timeFormat = 'h' . $delimeter . 'i' . $delimeter . 'A';
    date_default_timezone_set('Asia/Kolkata');
    return date_format($createTime, $timeFormat);
}

////////////// main functions //////////////////

function getAllTodo($uid) {
    global $db_con;
    if ($uid) {
        if ($uid != '1') {
            $where = " where emp_id='$uid'";
        }
        $stmt = mysqli_query($db_con, "SELECT * FROM todo_list" . $where);
        $res = mysqli_fetch_assoc($stmt);
        return $res;
    }
}

/** Function to fetch Todays Todos
 *  @param database_connection database connection
 *  @param id unique id of user
 *
 *  @return array function return database row
 */
function getTodaysTodo($connection, $userId) {
    $addon = "";
    if (substr($userId, 0, 3) == 'emp') {
        $addon = " AND emp_id LIKE '%" . $userId . "%' ";
    }
    try {
        $dates = $this->services->getDate();
        $stmt = $connection->query("SELECT * FROM todo_list WHERE task_date = '$dates'" . $addon);
        $response = $stmt->fetchAll(PDO::FETCH_OBJ);
        return $response;
    } catch (Exception $e) {
        throw new Exception($e->getMessage(), $e->getCode());
        return false;
    }
}

/** Function to fetch Tomorrows Todos
 *  @param database_connection database connection
 *  @param id unique id of user
 *
 *  @return array function return database row
 */
function getTomorrowsTodo($connection, $userId) {
    $addon = "";
    if (substr($userId, 0, 3) == 'emp') {
        $addon = " AND emp_id LIKE '%" . $userId . "%' ";
    }
    try {
        $dates = $this->services->getTommorowDate();
        $stmt = $connection->query("SELECT * FROM todo_list WHERE task_date = '$dates'" . $addon);
        $response = $stmt->fetchAll(PDO::FETCH_OBJ);
        return $response;
    } catch (Exception $e) {
        throw new Exception($e->getMessage(), $e->getCode());
        return false;
    }
}

/** Function to fetch Weeks Todos
 *  @param database_connection database connection
 *  @param id unique id of user
 *
 *  @return array function return database row
 */
function getWeeksTodo($connection, $userId) {
    $addon = "";
    if (substr($userId, 0, 3) == 'emp') {
        $addon = " AND emp_id LIKE '%" . $userId . "%' ";
    }
    try {
        $dates = $this->services->getWeekDates();
        $start = $dates['start'];
        $end = $dates['end'];
        $dates = $this->services->getTommorowDate();
        $stmt = $connection->query("SELECT * FROM todo_list WHERE task_date BETWEEN '$start' AND '$end'" . $addon);
        $response = $stmt->fetchAll(PDO::FETCH_OBJ);
        return $response;
    } catch (Exception $e) {
        throw new Exception($e->getMessage(), $e->getCode());
        return false;
    }
}

/** Function to fetch all Appointments
 *  @param database_connection database connection
 *  @param id unique id of user
 *
 *  @return array function return database row
 */
function getAllAppointments($connection, $userId) {
    if ($connection && $userId) {
        try {
            $stmt = $connection->query("SELECT * FROM appointments WHERE userId = '" . $userId . "'");
            $response = $stmt->fetchAll(PDO::FETCH_OBJ);
            return $response;
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
            return false;
        }
    } else {
        throw new Exception("Error In Request Params", 401);
        return false;
    }
}

function getRecord($tbl, $where = '') {
    global $db_con;
    if (!empty($tbl)) {
        $sql = "select * from $tbl where 1=1";
        if ($where != '') {
            $sql .= $where;
        }
        $query = mysqli_query($db_con, $sql);
        while ($res = mysqli_fetch_assoc($query)) {
            $result[] = $res;
        }
    } else {
        return 'Required parameter missing';
    }
    return $result;
}

class dateFormat {

    // formatting date.
    public function formatDate($date, $delimeter) {
        $createDate = date_create($date);
        $dateFormat = 'Y' . $delimeter . 'm' . $delimeter . 'd';
        date_default_timezone_set('Asia/Kolkata');
        return date_format($createDate, $dateFormat);
    }

    public function formatTime24($time, $delimeter) {
        $createTime = date_create($time);
        $timeFormat = 'H' . $delimeter . 'i';
        date_default_timezone_set('Asia/Kolkata');
        return date_format($createTime, $timeFormat);
    }

}

class toDo extends dateFormat {

    public function getAllToDo($db_con, $uid) {
        $sql = "select * from todo_list where 1=1 ";
        // if($uid!='1'){
        $sql .= " and find_in_set($uid,emp_id)";
        //}
        $sql .= " and is_archived='0'";
        $query = mysqli_query($db_con, $sql);
        while ($res = mysqli_fetch_assoc($query)) {
            $res_array[] = $res;
        }
        return $res_array;
    }

}

class notification {

    public function getUserDetails($uid) {
        $sql = "select * from tbl_user_master where id='$uid'";
        $query = mysqli_query($db_con, $sql);
        $res = mysqli_fetch_assoc($query);
        return $res;
    }

    public function getNotify($db_con, $uid) {
		// prepare current time for comparison
        date_default_timezone_set("Asia/Kolkata");
        $nt_time=date('H:i:s', time());
        //$nt_time=ltrim($nt_time, '0'); 
        
         $sql = "SELECT * FROM `todo_list` where find_in_set($uid,emp_id) and datediff(task_date,now())>=0 and datediff(task_date,now())<=task_notification_frequency AND STR_TO_DATE( `task_notify_time`, '%l:%i %p' ) <= '$nt_time' and is_archived='0'";
         //echo $sql;
        $nt_array = array();
        $query = mysqli_query($db_con, $sql);
        while ($res = mysqli_fetch_assoc($query)) {
            $nt_array[] = $res;
        }
        return $nt_array;
    }

    public function getAppointNotify($db_con, $uid) {
	 // prepare current time for comparison
        date_default_timezone_set("Asia/Kolkata");
        $nt_time=date('H:i:s', time());
        //$nt_time=ltrim($nt_time, '0'); 
		
        $sql = "SELECT * FROM `appointments` where user_id='$uid' and datediff(app_date,now())>=0 and datediff(app_date,now())<=notify_frequency AND notify_time <= '$nt_time' and is_archived='0' and notify_status='0'";
		
        //echo $sql;
        $nt_array = array();
        $query = mysqli_query($db_con, $sql);
        while ($res = mysqli_fetch_assoc($query)) {
            $nt_array[] = $res;
        }
        return $nt_array;
    }

    // public function sendNotification(){}
}

class appointment extends dateFormat {

    public function getAllAppointment($db_con, $uid) {
        $sql = "select * from appointments where 1=1 ";
        //if($uid!='1'){
        $sql .= " and user_id='$uid'";
        //}
        $sql .= " and is_archived='0'";
        $query = mysqli_query($db_con, $sql);
        while ($res = mysqli_fetch_assoc($query)) {
            $res_array[] = $res;
        }
        return $res_array;
    }

}

// send to do notification mail 
function sendToDoNotifyMail($db_con) {
    $sql = "SELECT *,subtime(current_time(),'00:10:00'),addtime(current_time(),'00:10:00'),STR_TO_DATE(task_notify_time, '%l:%i %p') FROM `todo_list` where  datediff(task_date,now())>=0 and datediff(task_date,now())<=task_notification_frequency and STR_TO_DATE(task_notify_time, '%l:%i %p')>subtime(current_time(),'00:10:00') and STR_TO_DATE(task_notify_time, '%l:%i %p')<addtime(current_time(),'00:10:00');";

    $query = mysqli_query($db_con, $sql);
    while ($noty = mysqli_fetch_assoc($query)) {
        //print_r($noty);
        // die;
        $uids = $noty['emp_id'];
        //echo $uids;
        // die;

        $uquery = mysqli_query($db_con, "select * from tbl_user_master where user_id in($uids) and active_inactive_users='1'");
        //echo "select * from tbl_user_master where user_id in($uids) and active_inactive_users='1'";
        while ($ures = mysqli_fetch_assoc($uquery)) {
            //assign Required Parameter
            $projectName = 'Ezeeoffice';
            $subject = 'To Do Notification';
            $message = 'This is to notify you that you have to do ' . $noty[task_name] . ' on ' . date("d M Y", strtotime($noty[task_date])) . ' at ' . $noty[task_time] . '.';
            $username = $ures['first_name'] . " " . $ures['last_name'];
            $to = $ures['user_email_id'];

            echo $projectName . EOL;
            echo $subject . EOL;
            echo $message . EOL;
            echo $username . EOL;
            echo $to . EOL;
            //die;
            $mst = notificationMail($projectName, $subject, $message, $username, $to);
            if (!$mst) {
                
            }
            //sleep for .5 second
            usleep(50000);
        }
    }
}

// Send Appointment Notification Mail
function sendAppointNotifyMail($db_con) {
    $sql = "SELECT *,subtime(current_time(),'00:10:00'),addtime(current_time(),'00:10:00'),STR_TO_DATE(task_notify_time, '%l:%i %p') FROM `todo_list` where  datediff(task_date,now())>=0 and datediff(task_date,now())<=task_notification_frequency and STR_TO_DATE(task_notify_time, '%l:%i %p')>subtime(current_time(),'00:10:00') and STR_TO_DATE(task_notify_time, '%l:%i %p')<addtime(current_time(),'00:10:00');";

    $query = mysqli_query($db_con, $sql);
    while ($noty = mysqli_fetch_assoc($query)) {
        //print_r($noty);
        // die;
        $uids = $noty['emp_id'];
        //echo $uids;
        // die;

        $uquery = mysqli_query($db_con, "select * from tbl_user_master where user_id in($uids) and active_inactive_users='1'");
        //echo "select * from tbl_user_master where user_id in($uids) and active_inactive_users='1'";
        while ($ures = mysqli_fetch_assoc($uquery)) {
            //assign Required Parameter
            $projectName = 'Ezeeoffice';
            $subject = 'To Do Notification';
            $message = 'This is to notify you that you have to do ' . $noty[task_name] . ' on ' . date("d M Y", strtotime($noty[task_date])) . ' at ' . $noty[task_time] . '.';
            $username = $ures['first_name'] . " " . $ures['last_name'];
            $to = $ures['user_email_id'];

            echo $projectName . EOL;
            echo $subject . EOL;
            echo $message . EOL;
            echo $username . EOL;
            echo $to . EOL;
            //die;
            $mst = notificationMail($projectName, $subject, $message, $username, $to);
            $is_sent = ($mst) ? '1' : '0';
            //set varible for log
            $user_id = $ures['user_id'];
            $type = 'Appointments';
            //sleep for .5 second
            usleep(50000);
        }
    }
}

function notificationLog($user_id, $type, $is_sent, $db_con) {
    $date = getDateTime24();
    $sql = "insert into notification_log set 
                                         user_id='$user_id'
                                         type='$type',
                                         is_sent='$is_sent',
                                         tstp='$date'";
    $query = mysqli_query($db_con, $query);
}
