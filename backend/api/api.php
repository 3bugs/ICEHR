<?php
session_start();
require_once 'global.php';
require_once 'jwt.php';

error_reporting(E_ERROR | E_PARSE);
header('Access-Control-Allow-Origin: *');
header('Content-type: application/json; charset=utf-8');

header('Expires: Sun, 01 Jan 2014 00:00:00 GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', FALSE);
header('Pragma: no-cache');

$response = array();

$request = explode('/', trim($_SERVER['PATH_INFO'], '/'));
$action = strtolower(array_shift($request));
$id = array_shift($request);

require_once '../include/db_config.inc';
$db = new mysqli(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);

if ($db->connect_errno) {
    $response[KEY_ERROR_CODE] = ERROR_CODE_CONNECT_DB_FAILED;
    $response[KEY_ERROR_MESSAGE] = 'การเชื่อมต่อฐานข้อมูลล้มเหลว';
    $response[KEY_ERROR_MESSAGE_MORE] = $db->connect_error;
    echo json_encode($response);
    exit();
}
$db->set_charset("utf8");

switch ($action) {
    case 'test':
        doTest();
        break;
    case 'login_user':
        $username = $db->real_escape_string($_POST['username']);
        $password = $db->real_escape_string($_POST['password']);
        doLoginUser($username, $password);
        break;
    case 'logout_user':
        doLogoutUser();
        break;
    case 'add_course_master':
        $courseMasterTitle = $db->real_escape_string($_POST['courseMasterTitle']);
        doAddCourseMaster($courseMasterTitle);
        break;
    case 'update_course_master':
        $courseMasterId = $db->real_escape_string($_POST['courseMasterId']);
        $courseMasterTitle = $db->real_escape_string($_POST['courseMasterTitle']);
        doUpdateCourseMaster($courseMasterId, $courseMasterTitle);
        break;
    case 'delete_course_master':
        $courseMasterId = $db->real_escape_string($_POST['courseMasterId']);
        doDeleteCourseMaster($courseMasterId);
        break;
    case 'add_course':
        doAddCourse();
        break;
    case 'update_course':
        doUpdateCourse();
        break;

    default:
        $response[KEY_ERROR_CODE] = ERROR_CODE_INVALID_ACTION;
        $response[KEY_ERROR_MESSAGE] = 'No action specified or invalid action.';
        $response[KEY_ERROR_MESSAGE_MORE] = '';
        break;
}

$db->close();
echo json_encode($response);
exit();

function doTest()
{
    global $db, $response;

    $sql = "SELECT * FROM user";
    $result = $db->query($sql);
    $row = $result->fetch_assoc();
    $response['first_name'] = $row['first_name'];
    $response['status'] = (int)$row['status'];
}

function doLoginUser($username, $password)
{
    global $db, $response;

    $sql = "SELECT * FROM user WHERE username = '$username' AND LOWER(password) = LOWER(MD5('$password'))";
    if ($result = $db->query($sql)) {
        if ($result->num_rows > 0) {
            $response[KEY_ERROR_CODE] = ERROR_CODE_SUCCESS;
            $response[KEY_ERROR_MESSAGE] = 'เข้าสู่ระบบสำเร็จ';
            $response[KEY_ERROR_MESSAGE_MORE] = '';

            $row = $result->fetch_assoc();
            $user = array();
            $user['id'] = (int)$row['id'];
            $user['username'] = $row['username'];
            $user['first_name'] = $row['first_name'];
            $user['last_name'] = $row['last_name'];
            $user['email'] = $row['email'];
            $user['role'] = $row['role'];
            $response['user'] = $user;

            createSession($user);
        } else {
            $response[KEY_ERROR_CODE] = ERROR_CODE_LOGIN_FAILED;
            $response[KEY_ERROR_MESSAGE] = 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง';
            $response[KEY_ERROR_MESSAGE_MORE] = 'Username: $username, Password: $password';
        }
        $result->close();
    } else {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
        $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล';
        $errMessage = $db->error;
        $response[KEY_ERROR_MESSAGE_MORE] = "$errMessage\nSQL: $sql";
    }
}

function createSession($user)
{
    $_SESSION[KEY_SESSION_USER_ID] = $user['id'];
    $_SESSION[KEY_SESSION_USER_USERNAME] = $user['username'];
    $_SESSION[KEY_SESSION_USER_FIRST_NAME] = $user['first_name'];
    $_SESSION[KEY_SESSION_USER_LAST_NAME] = $user['last_name'];
    $_SESSION[KEY_SESSION_USER_EMAIL] = $user['email'];
    $_SESSION[KEY_SESSION_USER_ROLE] = $user['role'];
}

function doLogoutUser()
{
    global $response;

    if (session_destroy()) {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SUCCESS;
        $response[KEY_ERROR_MESSAGE] = 'ออกจากระบบสำเร็จ';
        $response[KEY_ERROR_MESSAGE_MORE] = '';
    } else {
        $response[KEY_ERROR_CODE] = ERROR_CODE_LOGOUT_FAILED;
        $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการออกจากระบบ';
        $response[KEY_ERROR_MESSAGE_MORE] = '';
    }
}

function doAddCourseMaster($courseMasterTitle)
{
    global $db, $response;

    $courseMasterTitle = trim($courseMasterTitle);

    $sql = "INSERT INTO course_master (title) VALUES ('$courseMasterTitle')";
    if ($result = $db->query($sql)) {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SUCCESS;
        $response[KEY_ERROR_MESSAGE] = 'เพิ่มชื่อหลักสูตรสำเร็จ';
        $response[KEY_ERROR_MESSAGE_MORE] = '';
    } else {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
        $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการเพิ่มชื่อหลักสูตร';
        $errMessage = $db->error;
        $response[KEY_ERROR_MESSAGE_MORE] = "$errMessage\nSQL: $sql";
    }
}

function doUpdateCourseMaster($courseMasterId, $courseMasterTitle)
{
    global $db, $response;

    $courseMasterTitle = trim($courseMasterTitle);

    $sql = "UPDATE course_master SET title='$courseMasterTitle' WHERE id=$courseMasterId";
    if ($result = $db->query($sql)) {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SUCCESS;
        $response[KEY_ERROR_MESSAGE] = 'แก้ไขชื่อหลักสูตรสำเร็จ';
        $response[KEY_ERROR_MESSAGE_MORE] = '';
    } else {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
        $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการแก้ไขชื่อหลักสูตร';
        $errMessage = $db->error;
        $response[KEY_ERROR_MESSAGE_MORE] = "$errMessage\nSQL: $sql";
    }
}

function doDeleteCourseMaster($courseMasterId)
{
    global $db, $response;

    $sql = "DELETE FROM course_master WHERE id=$courseMasterId";
    if ($result = $db->query($sql)) {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SUCCESS;
        $response[KEY_ERROR_MESSAGE] = 'ลบชื่อหลักสูตรสำเร็จ';
        $response[KEY_ERROR_MESSAGE_MORE] = '';
    } else {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
        $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการลบชื่อหลักสูตร';
        $errMessage = $db->error;
        $response[KEY_ERROR_MESSAGE_MORE] = "$errMessage\nSQL: $sql";
    }
}

function doAddCourse()
{
    global $db, $response;

    $courseMasterId = $db->real_escape_string($_POST['courseMasterId']);
    $batchNumber = $db->real_escape_string($_POST['batchNumber']);
    $applicationFee = $db->real_escape_string($_POST['applicationFee']);
    $beginDate = $db->real_escape_string($_POST['beginDate']);
    $endDate = $db->real_escape_string($_POST['endDate']);
    $place = $db->real_escape_string($_POST['place']);
    $responsibleUserId = $db->real_escape_string($_POST['responsibleUserId']);
    $details = $db->real_escape_string($_POST['details']);

    $sql = "INSERT INTO course (course_master_id, batch_number, details, application_fee, place, begin_date, end_date, responsible_user_id) "
        . " VALUES ($courseMasterId, $batchNumber, '$details', $applicationFee, '$place', '$beginDate', '$endDate', $responsibleUserId)";
    if ($result = $db->query($sql)) {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SUCCESS;
        $response[KEY_ERROR_MESSAGE] = 'เพิ่มหลักสูตรสำเร็จ';
        $response[KEY_ERROR_MESSAGE_MORE] = '';
    } else {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
        $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการเพิ่มหลักสูตร';
        $errMessage = $db->error;
        $response[KEY_ERROR_MESSAGE_MORE] = "$errMessage\nSQL: $sql";
    }
}

function doUpdateCourse()
{
    global $db, $response;

    $courseId = $db->real_escape_string($_POST['courseId']);
    $courseMasterId = $db->real_escape_string($_POST['courseMasterId']);
    $batchNumber = $db->real_escape_string($_POST['batchNumber']);
    $applicationFee = $db->real_escape_string($_POST['applicationFee']);
    $beginDate = $db->real_escape_string($_POST['beginDate']);
    $endDate = $db->real_escape_string($_POST['endDate']);
    $place = $db->real_escape_string($_POST['place']);
    $responsibleUserId = $db->real_escape_string($_POST['responsibleUserId']);
    $details = $db->real_escape_string($_POST['details']);

    $sql = "UPDATE course SET course_master_id = $courseMasterId, batch_number = $batchNumber, details = '$details', application_fee = $applicationFee, "
        . " place = '$place', begin_date = '$beginDate', end_date = '$endDate', responsible_user_id = $responsibleUserId "
        . " WHERE id = $courseId";
    if ($result = $db->query($sql)) {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SUCCESS;
        $response[KEY_ERROR_MESSAGE] = 'แก้ไขหลักสูตรสำเร็จ';
        $response[KEY_ERROR_MESSAGE_MORE] = '';
    } else {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
        $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการแก้ไขหลักสูตร';
        $errMessage = $db->error;
        $response[KEY_ERROR_MESSAGE_MORE] = "$errMessage\nSQL: $sql";
    }
}

function moveUploadedFile($key, $dest)
{
    global $response;

    $response['name'] = $_FILES[$key]['name'];
    $response['type'] = $_FILES[$key]['type'];
    $response['size'] = $_FILES[$key]['size'];
    $response['tmp_name'] = $_FILES[$key]['tmp_name'];

    $src = $_FILES[$key]['tmp_name'];
    $response['upload_src'] = $src;
    $response['upload_dest'] = $dest;

    return move_uploaded_file($src, $dest);
}

function createRandomString($length)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

/*function createRandomString($length)
{
    $token = "";
    $codeAlphabet = "abcdefghijklmnopqrstuvwxyz";
    $codeAlphabet .= "0123456789";
    $max = strlen($codeAlphabet); // edited

    for ($i = 0; $i < $length; $i++) {
        $token .= $codeAlphabet[crypto_rand_secure(0, $max - 1)];
    }
    return $token;
}*/

?>