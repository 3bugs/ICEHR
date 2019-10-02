<?php
require_once '../vendor/autoload.php';

define('HOST_FRONTEND', 'http://localhost:3000');
//define('HOST_FRONTEND', 'http://202.6.26.3:3000');
//define('HOST_FRONTEND', 'http://www2.icehr.tu.ac.th:3000');
//define('HOST_FRONTEND', 'http://203.131.219.11:3000');

define('MAX_CERT_NUMBER', 123);

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

define('MEMBER_TYPE_ORGANIZATION', 'organization');
define('MEMBER_TYPE_PERSON', 'person');

define('UPLOAD_DIR_ACADEMIC_PAPERS', '../uploads/academic_papers/');
define('UPLOAD_DIR_COURSE_ASSETS', '../uploads/course_assets/');
define('UPLOAD_DIR_DOCUMENT_DOWNLOADS', '../uploads/document_downloads/');
define('UPLOAD_DIR_NEWS_ASSETS', '../uploads/news_assets/');
define('UPLOAD_DIR_USER_ASSETS', '../uploads/user_assets/');
define('UPLOAD_DIR_INTRO_ASSETS', '../uploads/intro_assets/');
define('UPLOAD_DIR_SIGNATURES', '../uploads/signatures/');
define('UPLOAD_DIR_SERVICE_ICONS', '../uploads/service_icons/');

define('KEY_SESSION_USER_ID', 'session_user_id');
define('KEY_SESSION_USER_USERNAME', 'session_user_username');
define('KEY_SESSION_USER_FIRST_NAME', 'session_user_first_name');
define('KEY_SESSION_USER_LAST_NAME', 'session_user_last_name');
define('KEY_SESSION_USER_EMAIL', 'session_user_email');
define('KEY_SESSION_USER_ROLE', 'session_user_role');
define('KEY_SESSION_USER_PERMISSION', 'session_user_permission');
define('KEY_SESSION_USER_PROFILE_IMAGE_URL', 'session_user_profile_image_url');

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
define('PERMISSION_MANAGE_IN_HOUSE', 23);
define('PERMISSION_MANAGE_ACADEMIC_PAPERS', 24);

define('GMAIL_SMTP_HOST', 'smtp.gmail.com');
define('GMAIL_SMTP_PORT', 465);
define('GMAIL_SMTP_USERNAME', 'icehr.tu@gmail.com');
define('GMAIL_SMTP_NAME', 'สถาบันเสริมศึกษาและทรัพยากรมนุษย์ มธ.');
define('GMAIL_SMTP_PASSWORD', 'wgyhexikkwevucwq');

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

    PERMISSION_MANAGE_WEB_CONTENT,
    PERMISSION_MANAGE_IN_HOUSE,
    PERMISSION_MANAGE_ACADEMIC_PAPERS
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

    PERMISSION_MANAGE_WEB_CONTENT => 'จัดการเนื้อหาเว็บไซต์',
    PERMISSION_MANAGE_IN_HOUSE => 'จัดการ In-House Training',
    PERMISSION_MANAGE_ACADEMIC_PAPERS => 'จัดการงานวิจัยและวิชาการ'
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

    PERMISSION_MANAGE_WEB_CONTENT => 'info',
    PERMISSION_MANAGE_IN_HOUSE => 'info',
    PERMISSION_MANAGE_ACADEMIC_PAPERS => 'info',
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

define('DL_LICENSE_TYPE_CAR', 1);
define('DL_LICENSE_TYPE_MOTOR_CYCLE', 2);
define('DL_LICENSE_TYPE_TRICYCLE', 4);
define('DL_LICENSE_TYPE_CAR_TEXT', 'รถยนต์ส่วนบุคคล');
define('DL_LICENSE_TYPE_MOTOR_CYCLE_TEXT', 'รถจักรยานยนต์ส่วนบุคคล');
define('DL_LICENSE_TYPE_TRICYCLE_TEXT', 'รถสามล้อส่วนบุคคล');

define('SOCIAL_SLUG_FB', 'facebook');
define('SOCIAL_SLUG_TWITTER', 'twitter');
define('SOCIAL_SLUG_LINE', 'line');
define('SOCIAL_SLUG_YOUTUBE', 'youtube');
define('SOCIAL_SLUG_IG', 'instagram');

function userHasPermission($userPermissions, $permissionToCheck)
{
    return $_SESSION[KEY_SESSION_USER_ROLE] === 'super_admin' || (($userPermissions & (1 << $permissionToCheck)) > 0);
}

function currentUserHasPermission($permissionToCheck)
{
    return userHasPermission($_SESSION[KEY_SESSION_USER_PERMISSION], $permissionToCheck);
}

function getThaiDate($date)
{
    global $monthNames, $dayNames;

    $dayOfWeek = $dayNames[date_format($date, 'w')];
    $dayOfMonth = (int)date_format($date, 'd');
    $month = $monthNames[(int)date_format($date, 'm') - 1];
    $year = (int)date_format($date, 'Y') + 543;

    return "วัน{$dayOfWeek}ที่ $dayOfMonth $month $year";
}

// ใช้ในหนังสือรับรองการผ่านการอบรม
function getThaiDate2($date)
{
    global $monthNames, $dayNames;

    $dayOfWeek = $dayNames[date_format($date, 'w')];
    $dayOfMonth = (int)date_format($date, 'd');
    $month = $monthNames[(int)date_format($date, 'm') - 1];
    $year = (int)date_format($date, 'Y') + 543;

    return "$dayOfMonth เดือน{$month} พ.ศ. $year";
}

function getThaiShortDate($date)
{
    global $monthShortNames;

    $dayOfMonth = (int)date_format($date, 'd');
    $month = $monthShortNames[(int)date_format($date, 'm') - 1];
    $yearText = strval((int)date_format($date, 'Y') + 543);
    $year = substr($yearText, strlen($yearText) - 2);

    return "$dayOfMonth $month $year";
}

function getThaiShortDateWithDayName($date)
{
    global $monthShortNames, $dayShortNames;

    $dayOfWeek = $dayShortNames[date_format($date, 'w')];
    $dayOfMonth = (int)date_format($date, 'd');
    $month = $monthShortNames[(int)date_format($date, 'm') - 1];
    $yearText = strval((int)date_format($date, 'Y') + 543);
    $year = substr($yearText, strlen($yearText) - 2);

    return "$dayOfWeek $dayOfMonth $month $year";
}

function getThaiIntervalDate($beginDate, $endDate)
{
    global $monthNames;

    $beginDay = (int)date_format($beginDate, 'd');
    $beginMonth = (int)date_format($beginDate, 'm');
    $endDay = (int)date_format($endDate, 'd');
    $endMonth = (int)date_format($endDate, 'm');

    if ($beginDay === $endDay && $beginMonth === $endMonth) {
        $month = $monthNames[$beginMonth - 1];
        $year = strval((int)date_format($beginDate, 'Y') + 543);
        $output = "$beginDay $month $year";

    } else if ($beginMonth === $endMonth) {
        $month = $monthNames[$beginMonth - 1];
        $year = strval((int)date_format($beginDate, 'Y') + 543);
        $output = "$beginDay-$endDay $month $year";

    } else {
        $beginMonthName = $monthNames[$beginMonth - 1];
        $endMonthName = $monthNames[$endMonth - 1];
        $year = strval((int)date_format($beginDate, 'Y') + 543);
        $output = "$beginDay $beginMonthName - $endDay $endMonthName $year";
    }

    return $output;
}

function getThaiDateForCertificate($inputDate, $numSpace)
{
    global $monthNames;

    $day = (int)date_format($inputDate, 'd');
    $month = $monthNames[(int)date_format($inputDate, 'm') - 1];
    $year = strval((int)date_format($inputDate, 'Y') + 543);

    $spaces = str_repeat(' ', $numSpace);

    $output = "{$day}{$spaces}{$month}{$spaces}พุทธศักราช{$spaces}{$year}";

    return $output;
}

function getThaiIntervalShortDate($beginDate, $endDate)
{
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

function getThaiIntervalShortDate2($beginDate, $endDate)
{
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

function formatPid($pid, $separator = '-')
{
    $formattedPid = substr_replace($pid, $separator, 1, 0);
    $formattedPid = substr_replace($formattedPid, $separator, 5 + strlen($separator), 0);
    $formattedPid = substr_replace($formattedPid, $separator, 10 + 2 * strlen($separator), 0);
    $formattedPid = substr_replace($formattedPid, $separator, 12 + 3 * strlen($separator), 0);
    return $formattedPid;
}

function isValidPid($pid)
{
    $pid = trim($pid);

    if (strlen($pid) !== 13) {
        return false;
    }
    $rev = strrev($pid); // reverse string ขั้นที่ 0 เตรียมตัว
    $total = 0;
    for ($i = 1; $i < 13; $i++) { // ขั้นตอนที่ 1 - เอาเลข 12 หลักมา เขียนแยกหลักกันก่อน
        $mul = $i + 1;
        $count = $rev[$i] * $mul; // ขั้นตอนที่ 2 - เอาเลข 12 หลักนั้นมา คูณเข้ากับเลขประจำหลักของมัน
        $total = $total + $count; // ขั้นตอนที่ 3 - เอาผลคูณทั้ง 12 ตัวมา บวกกันทั้งหมด
    }
    $mod = $total % 11; //ขั้นตอนที่ 4 - เอาเลขที่ได้จากขั้นตอนที่ 3 มา mod 11 (หารเอาเศษ)
    $sub = 11 - $mod; //ขั้นตอนที่ 5 - เอา 11 ตั้ง ลบออกด้วย เลขที่ได้จากขั้นตอนที่ 4
    $check_digit = $sub % 10; //ถ้าเกิด ลบแล้วได้ออกมาเป็นเลข 2 หลัก ให้เอาเลขในหลักหน่วยมาเป็น Check Digit

    return $rev[0] === $check_digit;  // ตรวจสอบ ค่าที่ได้ กับ เลขตัวสุดท้ายของ บัตรประจำตัวประชาชน
}

function sendMail($pdfFileName, $pdfFileContent, $recipientList, $courseDisplayName, $isCourseFree)
{
    if (!empty($recipientList)) {
        // Create instance of Swift_Attachment with our PDF file
        $attachment = new Swift_Attachment($pdfFileContent, $pdfFileName, 'application/pdf');

        $body = "เรียนผู้เข้ารับการอบรม\n\nสถาบันฯ ขอยืนยันใบสมัครการอบรม $courseDisplayName ตามรายละเอียดไฟล์ที่แนบมาด้วยนี้\n\n\n";
        $body .= "สถาบันเสริมศึกษาและทรัพยากรมนุษย์ มหาวิทยาลัยธรรมศาสตร์\n";
        $body .= "http://www.icehr.tu.ac.th/\n";
        $body .= 'โทร. 02-613-3820-3';

        $message = new Swift_Message();
        $message
            ->setSubject(($isCourseFree ? 'ใบสมัคร: ' : 'ใบสมัครและรายละเอียดการชำระเงิน: ') . $courseDisplayName)
            ->setFrom(array(GMAIL_SMTP_USERNAME => GMAIL_SMTP_NAME))
            ->setTo($recipientList)
            ->setBody($body)
            ->attach($attachment);

        //$transport = new Swift_SmtpTransport('smtp.gmail.com', 465, 'ssl');
        //$transport->setUsername('email@gmail.com')->setPassword('password');

        $transport = new Swift_SmtpTransport(GMAIL_SMTP_HOST, GMAIL_SMTP_PORT, 'ssl'); // port 465 or 587
        $transport->setUsername(GMAIL_SMTP_USERNAME)->setPassword(GMAIL_SMTP_PASSWORD);

        // Create the Mailer using your created Transport
        $mailer = new Swift_Mailer($transport);

        // Send the created message
        $mailer->send($message);
    }
}

function sendMailNoAttachment($recipientList, $subject, $body)
{
    if (!empty($recipientList)) {
        $message = new Swift_Message();
        $message
            ->setSubject($subject)
            ->setFrom(array(GMAIL_SMTP_USERNAME => GMAIL_SMTP_NAME))
            ->setTo($recipientList)
            ->setBody($body);

        $transport = new Swift_SmtpTransport(GMAIL_SMTP_HOST, GMAIL_SMTP_PORT, 'ssl'); // port 465 or 587
        $transport->setUsername(GMAIL_SMTP_USERNAME)->setPassword(GMAIL_SMTP_PASSWORD);

        // Create the Mailer using your created Transport
        $mailer = new Swift_Mailer($transport);

        // Send the created message
        $mailer->send($message);
    }
}