<?php

define('SECRET_KEY', 'icehr_and_2fellows');
define('FOLDER_UPLOAD_INFO', '../files/info/');
define('FOLDER_UPLOAD_PARTY', '../files/party/');

define('ERROR_CODE_SUCCESS', 0);
define('ERROR_CODE_CONNECT_DB_FAILED', 1);
define('ERROR_CODE_SQL_ERROR', 2);
define('ERROR_CODE_INVALID_ACTION', 3);
define('ERROR_CODE_INSERT_ERROR', 4);
define('ERROR_CODE_UPDATE_ERROR', 5);
define('ERROR_CODE_USERNAME_ALREADY_EXISTS', 6);
define('ERROR_CODE_LOGIN_FAILED', 7);
define('ERROR_CODE_LOGOUT_FAILED', 8);
define('ERROR_CODE_DATE_EXISTS', 9);
define('ERROR_CODE_MOVE_UPLOADED_FILE_ERROR', 10);
define('ERROR_CODE_PERMISSION_DENIED', 11);
define('ERROR_CODE_ELECTION_DATE_NOT_SET', 12);
define('ERROR_CODE_STATUS_NOT_SET', 13);
define('ERROR_CODE_ERROR', 9999);

define('KEY_ERROR_CODE', 'error_code');
define('KEY_ERROR_MESSAGE', 'error_message');
define('KEY_ERROR_MESSAGE_MORE', 'error_message_more');
define('KEY_DATA_LIST', 'data_list');
//define('KEY_LOGIN_SUCCESS', 'login_success');

define('KEY_SESSION_USER_ID', 'session_user_id');
define('KEY_SESSION_USER_USERNAME', 'session_user_username');
define('KEY_SESSION_USER_FIRST_NAME', 'session_user_first_name');
define('KEY_SESSION_USER_LAST_NAME', 'session_user_last_name');
define('KEY_SESSION_USER_EMAIL', 'session_user_email');
define('KEY_SESSION_USER_ROLE', 'session_user_role');

define('ROLE_USER', 'user');
define('ROLE_ADMIN', 'admin');
define('ROLE_SUPER_ADMIN', 'super_admin');

$monthNames = array(
    'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน',
    'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'
);
$monthShortNames = array(
    'ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.',
    'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'
);
$dayNames = array(
    'อาทิตย์', 'จันทร์', 'อังคาร', 'พุธ', 'พฤหัสบดี', 'ศุกร์', 'เสาร์'
);
$dayShortNames = array(
    'อา', 'จ', 'อ', 'พ', 'พฤ', 'ศ', 'ส'
);

function getThaiDate($date) {
    global $monthNames, $dayNames;

    $dayOfWeek = $dayNames[date_format($date, 'w')];
    $dayOfMonth = (int)date_format($date, 'd');
    $month = $monthNames[(int)date_format($date, 'm') - 1];
    $year = (int)date_format($date, 'Y') + 543;

    return "วัน{$dayOfWeek}ที่ $dayOfMonth $month $year";
}

function getThaiShortDate($date) {
    global $monthShortNames;

    $dayOfMonth = (int)date_format($date, 'd');
    $month = $monthShortNames[(int)date_format($date, 'm') - 1];
    $yearText = strval((int)date_format($date, 'Y') + 543);
    $year = substr($yearText, strlen($yearText) - 2);

    return "$dayOfMonth $month $year";
}

function getThaiShortDateWithDayName($date) {
    global $monthShortNames, $dayShortNames;

    $dayOfWeek = $dayShortNames[date_format($date, 'w')];
    $dayOfMonth = (int)date_format($date, 'd');
    $month = $monthShortNames[(int)date_format($date, 'm') - 1];
    $yearText = strval((int)date_format($date, 'Y') + 543);
    $year = substr($yearText, strlen($yearText) - 2);

    return "$dayOfWeek $dayOfMonth $month $year";
}

function getThaiIntervalShortDate($beginDate, $endDate) {
    global $monthShortNames;

    if ((int)date_format($beginDate, 'm') === (int)date_format($endDate, 'm')) {
        $beginDay = (int)date_format($beginDate, 'd');
        $endDay = (int)date_format($endDate, 'd');
        $month = $monthShortNames[(int)date_format($beginDate, 'm') - 1];
        $yearText = strval((int)date_format($beginDate, 'Y') + 543);
        $year = substr($yearText, strlen($yearText) - 2);
        $output = "$beginDay-$endDay $month $year";
    } else {
        $d1 = getThaiShortDate($beginDate);
        $d2 = getThaiShortDate($endDate);
        $output = "$d1 - $d2";
    }

    return $output;
}

?>