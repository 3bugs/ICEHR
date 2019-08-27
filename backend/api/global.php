<?php

define('HOST_FRONTEND', 'http://localhost:3000');
//define('HOST_FRONTEND', 'http://202.6.26.3:3000');

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

define('UPLOAD_DIR_ACADEMIC_PAPERS', '../uploads/academic_papers/');
define('UPLOAD_DIR_COURSE_ASSETS', '../uploads/course_assets/');
define('UPLOAD_DIR_DOCUMENT_DOWNLOADS', '../uploads/document_downloads/');
define('UPLOAD_DIR_NEWS_ASSETS', '../uploads/news_assets/');
define('UPLOAD_DIR_USER_ASSETS', '../uploads/user_assets/');

define('KEY_SESSION_USER_ID', 'session_user_id');
define('KEY_SESSION_USER_USERNAME', 'session_user_username');
define('KEY_SESSION_USER_FIRST_NAME', 'session_user_first_name');
define('KEY_SESSION_USER_LAST_NAME', 'session_user_last_name');
define('KEY_SESSION_USER_EMAIL', 'session_user_email');
define('KEY_SESSION_USER_ROLE', 'session_user_role');
define('KEY_SESSION_USER_PERMISSION', 'session_user_permission');

define('PERMISSION_USER_READ', 0);
define('PERMISSION_USER_CREATE', 1);
define('PERMISSION_USER_UPDATE', 2);
define('PERMISSION_USER_DELETE', 3);
define('PERMISSION_USER_DEPARTMENT_MANAGE', 4);
define('PERMISSION_COURSE_TRAINING_CREATE', 5);
define('PERMISSION_COURSE_TRAINING_UPDATE', 6);
define('PERMISSION_COURSE_TRAINING_DELETE', 7);
define('PERMISSION_COURSE_TRAINING_MANAGE_REGISTRATION', 8);
define('PERMISSION_COURSE_TRAINING_MANAGE_COURSE_MASTER', 9);
define('PERMISSION_COURSE_TRAINING_MANAGE_CATEGORY', 10);
define('PERMISSION_COURSE_SOCIAL_CREATE', 11);
define('PERMISSION_COURSE_SOCIAL_UPDATE', 12);
define('PERMISSION_COURSE_SOCIAL_DELETE', 13);
define('PERMISSION_COURSE_SOCIAL_MANAGE_REGISTRATION', 14);
define('PERMISSION_COURSE_SOCIAL_MANAGE_COURSE_MASTER', 15);
define('PERMISSION_COURSE_DRIVING_LICENSE_CREATE', 16);
define('PERMISSION_COURSE_DRIVING_LICENSE_UPDATE', 17);
define('PERMISSION_COURSE_DRIVING_LICENSE_DELETE', 18);
define('PERMISSION_COURSE_DRIVING_LICENSE_MANAGE_REGISTRATION', 19);
define('PERMISSION_COURSE_DRIVING_LICENSE_MANAGE_COURSE_MASTER', 20);
define('PERMISSION_COURSE_DRIVING_LICENSE_MANAGE_CATEGORY', 21);
define('PERMISSION_MANAGE_WEB_CONTENT', 22);

$permissionList = array(
    //PERMISSION_USER_READ,
    PERMISSION_USER_CREATE,
    PERMISSION_USER_UPDATE,
    PERMISSION_USER_DELETE,
    PERMISSION_USER_DEPARTMENT_MANAGE,

    PERMISSION_COURSE_TRAINING_CREATE,
    PERMISSION_COURSE_TRAINING_UPDATE,
    PERMISSION_COURSE_TRAINING_DELETE,
    PERMISSION_COURSE_TRAINING_MANAGE_REGISTRATION,
    PERMISSION_COURSE_TRAINING_MANAGE_COURSE_MASTER,

    PERMISSION_COURSE_SOCIAL_CREATE,
    PERMISSION_COURSE_SOCIAL_UPDATE,
    PERMISSION_COURSE_SOCIAL_DELETE,
    PERMISSION_COURSE_SOCIAL_MANAGE_REGISTRATION,
    PERMISSION_COURSE_SOCIAL_MANAGE_COURSE_MASTER,

    PERMISSION_COURSE_DRIVING_LICENSE_CREATE,
    PERMISSION_COURSE_DRIVING_LICENSE_UPDATE,
    PERMISSION_COURSE_DRIVING_LICENSE_DELETE,
    PERMISSION_COURSE_DRIVING_LICENSE_MANAGE_REGISTRATION,
    PERMISSION_COURSE_DRIVING_LICENSE_MANAGE_COURSE_MASTER,

    PERMISSION_MANAGE_WEB_CONTENT
);

$permissionText = array(
    //PERMISSION_USER_READ => 'ดูข้อมูลผู้ใช้',
    PERMISSION_USER_CREATE => 'เพิ่มผู้ใช้',
    PERMISSION_USER_UPDATE => 'แก้ไขผู้ใช้',
    PERMISSION_USER_DELETE => 'ลบผู้ใช้',
    PERMISSION_USER_DEPARTMENT_MANAGE => 'จัดการฝ่าย',

    PERMISSION_COURSE_TRAINING_CREATE => 'เพิ่มหลักสูตรวิชาการ',
    PERMISSION_COURSE_TRAINING_UPDATE => 'แก้ไขหลักสูตรวิชาการ',
    PERMISSION_COURSE_TRAINING_DELETE => 'ลบหลักสูตรวิชาการ',
    PERMISSION_COURSE_TRAINING_MANAGE_REGISTRATION => 'จัดการใบสมัครวิชาการ',
    PERMISSION_COURSE_TRAINING_MANAGE_COURSE_MASTER => 'จัดการข้อมูลพื้นฐานวิชาการ',

    PERMISSION_COURSE_SOCIAL_CREATE => 'เพิ่มหลักสูตรสังคม',
    PERMISSION_COURSE_SOCIAL_UPDATE => 'แก้ไขหลักสูตรสังคม',
    PERMISSION_COURSE_SOCIAL_DELETE => 'ลบหลักสูตรสังคม',
    PERMISSION_COURSE_SOCIAL_MANAGE_REGISTRATION => 'จัดการใบสมัครสังคม',
    PERMISSION_COURSE_SOCIAL_MANAGE_COURSE_MASTER => 'จัดการข้อมูลพื้นฐานสังคม',

    PERMISSION_COURSE_DRIVING_LICENSE_CREATE => 'เพิ่มหลักสูตรใบขับขี่',
    PERMISSION_COURSE_DRIVING_LICENSE_UPDATE => 'แก้ไขหลักสูตรใบขับขี่',
    PERMISSION_COURSE_DRIVING_LICENSE_DELETE => 'ลบหลักสูตรใบขับขี่',
    PERMISSION_COURSE_DRIVING_LICENSE_MANAGE_REGISTRATION => 'จัดการใบสมัครใบขับขี่',
    PERMISSION_COURSE_DRIVING_LICENSE_MANAGE_COURSE_MASTER => 'จัดการข้อมูลพื้นฐานใบขับขี่',

    PERMISSION_MANAGE_WEB_CONTENT => 'จัดการเนื้อหาเว็บไซต์'
);

$permissionColor = array(
    //PERMISSION_USER_READ => 'ดูข้อมูลผู้ใช้',
    PERMISSION_USER_CREATE => 'success',
    PERMISSION_USER_UPDATE => 'warning',
    PERMISSION_USER_DELETE => 'danger',
    PERMISSION_USER_DEPARTMENT_MANAGE => 'info',

    PERMISSION_COURSE_TRAINING_CREATE => 'success',
    PERMISSION_COURSE_TRAINING_UPDATE => 'warning',
    PERMISSION_COURSE_TRAINING_DELETE => 'danger',
    PERMISSION_COURSE_TRAINING_MANAGE_REGISTRATION => 'info',
    PERMISSION_COURSE_TRAINING_MANAGE_COURSE_MASTER => 'info',

    PERMISSION_COURSE_SOCIAL_CREATE => 'success',
    PERMISSION_COURSE_SOCIAL_UPDATE => 'warning',
    PERMISSION_COURSE_SOCIAL_DELETE => 'danger',
    PERMISSION_COURSE_SOCIAL_MANAGE_REGISTRATION => 'info',
    PERMISSION_COURSE_SOCIAL_MANAGE_COURSE_MASTER => 'info',

    PERMISSION_COURSE_DRIVING_LICENSE_CREATE => 'success',
    PERMISSION_COURSE_DRIVING_LICENSE_UPDATE => 'warning',
    PERMISSION_COURSE_DRIVING_LICENSE_DELETE => 'danger',
    PERMISSION_COURSE_DRIVING_LICENSE_MANAGE_REGISTRATION => 'info',
    PERMISSION_COURSE_DRIVING_LICENSE_MANAGE_COURSE_MASTER => 'info',

    PERMISSION_MANAGE_WEB_CONTENT => 'info'
);

define('ROLE_USER', 'user');
define('ROLE_ADMIN', 'admin');
define('ROLE_SUPER_ADMIN', 'super_admin');

define('REGISTER_STATUS_START', 'start');
define('REGISTER_STATUS_WAIT_APPROVE', 'wait-approve');
define('REGISTER_STATUS_COMPLETE', 'complete');
define('REGISTER_STATUS_CANCEL', 'cancel');

define('REGISTER_STATUS_START_TEXT', 'ยังไม่ได้ชำระเงิน');
define('REGISTER_STATUS_WAIT_APPROVE_TEXT', 'รอตรวจสอบการชำระเงิน');
define('REGISTER_STATUS_COMPLETE_TEXT', 'การชำระเงินสมบูรณ์');
define('REGISTER_STATUS_CANCEL_TEXT', 'ยกเลิกการสมัคร');

$registerStatusText = array(
    REGISTER_STATUS_START => REGISTER_STATUS_START_TEXT,
    REGISTER_STATUS_WAIT_APPROVE => REGISTER_STATUS_WAIT_APPROVE_TEXT,
    REGISTER_STATUS_COMPLETE => REGISTER_STATUS_COMPLETE_TEXT,
    REGISTER_STATUS_CANCEL => REGISTER_STATUS_CANCEL_TEXT
);

define('SERVICE_TYPE_TRAINING', 'training');
define('SERVICE_TYPE_SOCIAL', 'social');
define('SERVICE_TYPE_DRIVING_LICENSE', 'driving-license');

define('SERVICE_TYPE_TRAINING_TEXT', 'บริการวิชาการ');
define('SERVICE_TYPE_SOCIAL_TEXT', 'บริการสังคม');
define('SERVICE_TYPE_DRIVING_LICENSE_TEXT', 'บริการใบขับขี่');

$serviceTypeText = array(
    SERVICE_TYPE_TRAINING => SERVICE_TYPE_TRAINING_TEXT,
    SERVICE_TYPE_SOCIAL => SERVICE_TYPE_SOCIAL_TEXT,
    SERVICE_TYPE_DRIVING_LICENSE => SERVICE_TYPE_DRIVING_LICENSE_TEXT
);

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
    'อา.', 'จ.', 'อ.', 'พ.', 'พฤ.', 'ศ.', 'ส.'
);

function userHasPermission($userPermissions, $permissionToCheck) {
    return (($userPermissions & (1 << $permissionToCheck)) > 0);
}

function currentUserHasPermission($permissionToCheck) {
    return userHasPermission($_SESSION[KEY_SESSION_USER_PERMISSION], $permissionToCheck);
}

function getThaiDate($date) {
    global $monthNames, $dayNames;

    $dayOfWeek = $dayNames[date_format($date, 'w')];
    $dayOfMonth = (int)date_format($date, 'd');
    $month = $monthNames[(int)date_format($date, 'm') - 1];
    $year = (int)date_format($date, 'Y') + 543;

    return "วัน{$dayOfWeek}ที่ $dayOfMonth $month $year";
}

// ใช้ในหนังสือรับรองการผ่านการอบรม
function getThaiDate2($date) {
    global $monthNames, $dayNames;

    $dayOfWeek = $dayNames[date_format($date, 'w')];
    $dayOfMonth = (int)date_format($date, 'd');
    $month = $monthNames[(int)date_format($date, 'm') - 1];
    $year = (int)date_format($date, 'Y') + 543;

    return "$dayOfMonth เดือน{$month} พ.ศ. $year";
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

function getThaiIntervalShortDate2($beginDate, $endDate) {
    if ($beginDate === $endDate) {
        $output = getThaiShortDate($beginDate);
    } else {
        $d1 = getThaiShortDate($beginDate);
        $d2 = getThaiShortDate($endDate);
        $output = "$d1 - $d2";
    }

    return $output;
}

function thaiNumDigit($num)
{
    return str_replace(
        array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9'),
        array('๐', '๑', '๒', '๓', '๔', '๕', '๖', '๗', '๘', '๙'),
        $num
    );
}
