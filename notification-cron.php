<?php

require_once('./application/config/database.php');
require_once('./sessionstart.php');
require_once('./tdn-appoint.php');
require_once('./mail.php');
sendToDoNotifyMail($db_con);
sendAppointNotifyMail($db_con);
