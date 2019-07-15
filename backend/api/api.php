<?php
define('KEY_IMAGE_FILES', 'imageFiles');
define('KEY_PDF_FILES', 'pdfFiles');

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

sleep(1); //todo: *****************************************************************************************

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
    case 'get_name_title':
        doGetNameTitle();
        break;
    case 'add_course_master':
        $courseMasterTitle = $db->real_escape_string($_POST['courseMasterTitle']);
        $courseCategory = isset($_POST['courseCategory']) ? $db->real_escape_string($_POST['courseCategory']) : 'NULL';
        $serviceType = $db->real_escape_string($_POST['serviceType']);
        doAddCourseMaster($courseMasterTitle, $courseCategory, $serviceType);
        break;
    case 'update_course_master':
        $courseMasterId = $db->real_escape_string($_POST['courseMasterId']);
        $courseMasterTitle = $db->real_escape_string($_POST['courseMasterTitle']);
        $courseCategory = isset($_POST['courseCategory']) ? $db->real_escape_string($_POST['courseCategory']) : 'NULL';
        doUpdateCourseMaster($courseMasterId, $courseMasterTitle, $courseCategory);
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
    case 'delete_course_asset':
        doDeleteCourseAsset();
        break;
    case 'update_register_status':
        doUpdateRegisterStatus();
        break;
    case 'get_payment_notification':
        doGetPaymentNotification();
        break;
    case 'update_in_house_status':
        doUpdateInHouseStatus();
        break;
    case 'add_academic_paper':
        doAddAcademicPaper();
        break;
    case 'update_academic_paper':
        doUpdateAcademicPaper();
        break;
    case 'get_academic_paper_download':
        doGetAcademicPaperDownload();
        break;
    case 'update_driving_license_course_type':
        doUpdateDrivingLicenseCourseType();
        break;
    case 'update_driving_license_doc_status':
        doUpdateDrivingLicenseDocStatus();
        break;
    case 'save_driving_license_course_result':
        doSaveDrivingLicenseCourseResult();
        break;
    case 'update_driving_license_registration':
        doUpdateDrivingLicenseRegistration();
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

function doAddCourseMaster($courseMasterTitle, $courseCategory, $serviceType)
{
    global $db, $response, $serviceTypeText;

    if (!array_key_exists($serviceType, $serviceTypeText)) {
        $response[KEY_ERROR_CODE] = ERROR_CODE_ERROR;
        $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการเพิ่มชื่อหลักสูตร: โปรแกรมระบุ Service Type ไม่ถูกต้อง, กรุณาติดต่อผูัพัฒนา';
        $response[KEY_ERROR_MESSAGE_MORE] = '';
        return;
    }

    $courseMasterTitle = trim($courseMasterTitle);

    $sql = "INSERT INTO course_master (title, category, service_type) 
            VALUES ('$courseMasterTitle', $courseCategory, '$serviceType')";
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

function doUpdateCourseMaster($courseMasterId, $courseMasterTitle, $courseCategory)
{
    global $db, $response;

    $courseMasterTitle = trim($courseMasterTitle);

    $sql = "UPDATE course_master 
            SET title = '$courseMasterTitle', category = $courseCategory 
            WHERE id = $courseMasterId";
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
    $batchNumber = isset($_POST['batchNumber']) ? $db->real_escape_string($_POST['batchNumber']) : 'NULL';
    $applicationFee = isset($_POST['applicationFee']) ? $db->real_escape_string($_POST['applicationFee']) : 'NULL';
    $traineeLimit = $db->real_escape_string($_POST['traineeLimit']);
    $beginDate = getMySqlDateFormat($db->real_escape_string($_POST['beginDate']));
    $endDate = getMySqlDateFormat($db->real_escape_string($_POST['endDate']));
    $place = $db->real_escape_string($_POST['place']);
    $responsibleUserId = $db->real_escape_string($_POST['responsibleUserId']);
    $details = $db->real_escape_string($_POST['details']);

    $db->query('START TRANSACTION');

    $sql = "INSERT INTO course (course_master_id, batch_number, details, application_fee, trainee_limit, place, begin_date, end_date, responsible_user_id) "
        . " VALUES ($courseMasterId, $batchNumber, '$details', $applicationFee, $traineeLimit, '$place', '$beginDate', '$endDate', $responsibleUserId)";
    if ($insertCourseResult = $db->query($sql)) {
        $insertId = $db->insert_id;

        $feeValueList = null;
        for ($i = 0; $i < sizeof($_POST['feeTitle']); $i++) {
            $feeTitle = $db->real_escape_string($_POST['feeTitle'][$i]);
            $feeAmount = $db->real_escape_string($_POST['feeAmount'][$i]);

            if (trim($feeAmount) === '') {
                $feeAmount = 'NULL';
            }

            $feeValueList .= "($insertId, '$feeTitle', $feeAmount)" . ($i === sizeof($_POST['feeTitle']) - 1 ? '' : ',');
        }
        if ($feeValueList) {
            $sql = "INSERT INTO course_fee (course_id, title, amount) 
                    VALUES $feeValueList";
            if (!($db->query($sql))) {
                $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
                $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการบันทึกข้อมูลราคา: ' . $db->error;
                $response[KEY_ERROR_MESSAGE_MORE] = '';

                $db->query('ROLLBACK');
                return;
            }
        }

        for ($i = 0; $i < sizeof($_FILES[KEY_IMAGE_FILES]['name']); $i++) {
            $fileName = null;

            if (!moveUploadedFile(KEY_IMAGE_FILES, UPLOAD_DIR_COURSE_ASSETS, $fileName, $i)) {
                $response[KEY_ERROR_CODE] = ERROR_CODE_ERROR;
                $errorValue = $_FILES[KEY_IMAGE_FILES]['error'][$i];
                $response[KEY_ERROR_MESSAGE] = "เกิดข้อผิดพลาดในการอัพโหลดรูปภาพ [Error: $errorValue]";
                $response[KEY_ERROR_MESSAGE_MORE] = '';

                $db->query('ROLLBACK');
                return;
            }

            $sql = "INSERT INTO course_asset (course_id, title, file_name, type) 
                    VALUES ($insertId, null, '$fileName', 'image')";
            if (!($insertCourseAssetResult = $db->query($sql))) {
                $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
                $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการบันทึกข้อมูลรูปภาพ: ' . $db->error;
                $response[KEY_ERROR_MESSAGE_MORE] = '';

                $db->query('ROLLBACK');
                return;
            }
        }

        for ($i = 0; $i < sizeof($_FILES[KEY_PDF_FILES]['name']); $i++) {
            $fileName = null;

            if (!moveUploadedFile(KEY_PDF_FILES, UPLOAD_DIR_COURSE_ASSETS, $fileName, $i)) {
                $response[KEY_ERROR_CODE] = ERROR_CODE_ERROR;
                $errorValue = $_FILES[KEY_PDF_FILES]['error'][$i];
                $response[KEY_ERROR_MESSAGE] = "เกิดข้อผิดพลาดในการอัพโหลด PDF [Error: $errorValue]";
                $response[KEY_ERROR_MESSAGE_MORE] = '';

                $db->query('ROLLBACK');
                return;
            }

            $sql = "INSERT INTO course_asset (course_id, title, file_name, type) 
                    VALUES ($insertId, null, '$fileName', 'pdf')";
            if (!($insertCourseAssetResult = $db->query($sql))) {
                $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
                $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการบันทึกข้อมูล PDF: ' . $db->error;
                $response[KEY_ERROR_MESSAGE_MORE] = '';

                $db->query('ROLLBACK');
                return;
            }
        }

        $response[KEY_ERROR_CODE] = ERROR_CODE_SUCCESS;
        $response[KEY_ERROR_MESSAGE] = 'เพิ่มหลักสูตรสำเร็จ';
        $response[KEY_ERROR_MESSAGE_MORE] = '';

        $db->query('COMMIT');
    } else {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
        $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $db->error;
        $errMessage = $db->error;
        $response[KEY_ERROR_MESSAGE_MORE] = "$errMessage\nSQL: $sql";
    }
}

function getMySqlDateFormat($thaiDate)
{
    $thaiDatePart = explode('/', $thaiDate);
    $yearAd = (int)$thaiDatePart[2] - 543;
    return "{$yearAd}-{$thaiDatePart[1]}-{$thaiDatePart[0]}";
}

function doUpdateCourse()
{
    global $db, $response;

    $courseId = $db->real_escape_string($_POST['courseId']);
    $courseMasterId = $db->real_escape_string($_POST['courseMasterId']);
    $batchNumber = isset($_POST['batchNumber']) ? $db->real_escape_string($_POST['batchNumber']) : 'NULL';
    $applicationFee = isset($_POST['applicationFee']) ? $db->real_escape_string($_POST['applicationFee']) : 'NULL';
    $traineeLimit = $db->real_escape_string($_POST['traineeLimit']);
    $beginDate = getMySqlDateFormat($db->real_escape_string($_POST['beginDate']));
    $endDate = getMySqlDateFormat($db->real_escape_string($_POST['endDate']));
    $place = $db->real_escape_string($_POST['place']);
    $responsibleUserId = $db->real_escape_string($_POST['responsibleUserId']);
    $details = $db->real_escape_string($_POST['details']);

    /*$output = sprintf(
        "File Names: %d\nCourse ID: %s\nCourse Master ID: %s\nBatch Number: %s\nFee: %s\nLimit: %s\nBegin: %s\nEnd: %s\nPlace: %s\nResponsible User ID: %s\nDetails: %s\n",
        sizeof($_FILES['imageFiles']['name']), $courseId, $courseMasterId, $batchNumber, $applicationFee, $traineeLimit, $beginDate, $endDate, $place, $responsibleUserId, $details
    );
    $response[KEY_ERROR_MESSAGE] = $output;
    return;*/

    $db->query('START TRANSACTION');

    $sql = "UPDATE course SET course_master_id = $courseMasterId, batch_number = $batchNumber, details = '$details', application_fee = $applicationFee, "
        . " trainee_limit = $traineeLimit, place = '$place', begin_date = '$beginDate', end_date = '$endDate', responsible_user_id = $responsibleUserId "
        . " WHERE id = $courseId";
    if ($updateCourseResult = $db->query($sql)) {
        $sql = "DELETE FROM course_fee WHERE course_id = $courseId";
        if (!($db->query($sql))) {
            $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
            $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการบันทึกข้อมูลราคา (1): ' . $db->error;
            $response[KEY_ERROR_MESSAGE_MORE] = '';

            $db->query('ROLLBACK');
            return;
        }

        $feeValueList = null;
        for ($i = 0; $i < sizeof($_POST['feeTitle']); $i++) {
            $feeTitle = $db->real_escape_string($_POST['feeTitle'][$i]);
            $feeAmount = $db->real_escape_string($_POST['feeAmount'][$i]);

            if (trim($feeAmount) === '') {
                $feeAmount = 'NULL';
            }

            $feeValueList .= "($courseId, '$feeTitle', $feeAmount)" . ($i === sizeof($_POST['feeTitle']) - 1 ? '' : ',');
        }
        if ($feeValueList) {
            $sql = "INSERT INTO course_fee (course_id, title, amount) 
                    VALUES $feeValueList";
            if (!($db->query($sql))) {
                $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
                $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการบันทึกข้อมูลราคา (2): ' . $db->error;
                $response[KEY_ERROR_MESSAGE_MORE] = '';

                $db->query('ROLLBACK');
                return;
            }
        }

        for ($i = 0; $i < sizeof($_FILES[KEY_IMAGE_FILES]['name']); $i++) {
            $fileName = null;

            if (!moveUploadedFile(KEY_IMAGE_FILES, UPLOAD_DIR_COURSE_ASSETS, $fileName, $i)) {
                $response[KEY_ERROR_CODE] = ERROR_CODE_ERROR;
                $errorValue = $_FILES[KEY_IMAGE_FILES]['error'][$i];
                $response[KEY_ERROR_MESSAGE] = "เกิดข้อผิดพลาดในการอัพโหลดรูปภาพ [Error: $errorValue]";
                $response[KEY_ERROR_MESSAGE_MORE] = '';

                $db->query('ROLLBACK');
                return;
            }

            $sql = "INSERT INTO course_asset (course_id, title, file_name, type) 
                    VALUES ($courseId, null, '$fileName', 'image')";
            if (!($insertCourseAssetResult = $db->query($sql))) {
                $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
                $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการบันทึกข้อมูลรูปภาพ';
                $response[KEY_ERROR_MESSAGE_MORE] = '';

                $db->query('ROLLBACK');
                return;
            }
        }
        for ($i = 0; $i < sizeof($_FILES[KEY_PDF_FILES]['name']); $i++) {
            $fileName = null;

            if (!moveUploadedFile(KEY_PDF_FILES, UPLOAD_DIR_COURSE_ASSETS, $fileName, $i)) {
                $response[KEY_ERROR_CODE] = ERROR_CODE_ERROR;
                $errorValue = $_FILES[KEY_PDF_FILES]['error'][$i];
                $response[KEY_ERROR_MESSAGE] = "เกิดข้อผิดพลาดในการอัพโหลด PDF [Error: $errorValue]";
                $response[KEY_ERROR_MESSAGE_MORE] = '';

                $db->query('ROLLBACK');
                return;
            }

            $sql = "INSERT INTO course_asset (course_id, title, file_name, type) 
                    VALUES ($courseId, null, '$fileName', 'pdf')";
            if (!($insertCourseAssetResult = $db->query($sql))) {
                $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
                $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการบันทึกข้อมูล PDF';
                $response[KEY_ERROR_MESSAGE_MORE] = '';

                $db->query('ROLLBACK');
                return;
            }
        }

        $response[KEY_ERROR_CODE] = ERROR_CODE_SUCCESS;
        $response[KEY_ERROR_MESSAGE] = 'แก้ไขหลักสูตรสำเร็จ';
        $response[KEY_ERROR_MESSAGE_MORE] = '';

        $db->query('COMMIT');
    } else {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
        $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการบันทึกข้อมูล (1)';
        $errMessage = $db->error;
        $response[KEY_ERROR_MESSAGE_MORE] = "$errMessage\nSQL: $sql";
    }
}

function doDeleteCourseAsset()
{
    global $db, $response;

    $assetId = $db->real_escape_string($_POST['assetId']);

    $sql = "DELETE FROM course_asset WHERE id=$assetId";
    if ($result = $db->query($sql)) {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SUCCESS;
        $response[KEY_ERROR_MESSAGE] = 'ลบข้อมูลสำเร็จ';
        $response[KEY_ERROR_MESSAGE_MORE] = '';
    } else {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
        $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการลบข้อมูล';
        $errMessage = $db->error;
        $response[KEY_ERROR_MESSAGE_MORE] = "$errMessage\nSQL: $sql";
    }
}

function doUpdateRegisterStatus()
{
    global $db, $response;

    $serviceType = $db->real_escape_string($_POST['serviceType']);
    $traineeId = $db->real_escape_string($_POST['traineeId']);
    $newRegisterStatus = $db->real_escape_string($_POST['registerStatus']);
    $paidAmount = $db->real_escape_string($_POST['paidAmount']);

    switch ($serviceType) {
        case SERVICE_TYPE_TRAINING:
            $sql = "UPDATE course_trainee 
                SET register_status = '$newRegisterStatus', paid_amount = $paidAmount
                WHERE id = $traineeId ";
            break;
        case SERVICE_TYPE_DRIVING_LICENSE:
            $sql = "UPDATE course_registration_driving_license 
                SET register_status = '$newRegisterStatus', paid_amount = $paidAmount
                WHERE id = $traineeId ";
            break;
    }

    if ($result = $db->query($sql)) {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SUCCESS;
        $response[KEY_ERROR_MESSAGE] = 'อัพเดทสถานะการลงทะเบียนสำเร็จ';
        $response[KEY_ERROR_MESSAGE_MORE] = '';
    } else {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
        $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการอัพเดทสถานะการลงทะเบียน';
        $errMessage = $db->error;
        $response[KEY_ERROR_MESSAGE_MORE] = "$errMessage\nSQL: $sql";
    }
}

function doUpdateDrivingLicenseDocStatus()
{
    global $db, $response;

    $traineeId = $db->real_escape_string($_POST['traineeId']);
    $newDocStatus = $db->real_escape_string($_POST['docStatus']);

    $sql = "UPDATE course_registration_driving_license 
            SET doc_status = $newDocStatus
            WHERE id = $traineeId ";

    if ($result = $db->query($sql)) {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SUCCESS;
        $response[KEY_ERROR_MESSAGE] = 'อัพเดทสถานะข้อมูล/เอกสารผู้สมัครสำเร็จ';
        $response[KEY_ERROR_MESSAGE_MORE] = '';
    } else {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
        $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการอัพเดทสถานะข้อมูล/เอกสารผู้สมัคร';
        $errMessage = $db->error;
        $response[KEY_ERROR_MESSAGE_MORE] = "$errMessage\nSQL: $sql";
    }
}

function doGetNameTitle() {
    global $db, $response;

    $sql = "SELECT id, title FROM name_title ORDER BY id";
    if ($result = $db->query($sql)) {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SUCCESS;
        $response[KEY_ERROR_MESSAGE] = 'อ่านข้อมูลสำเร็จ';
        $response[KEY_ERROR_MESSAGE_MORE] = '';
        $response[KEY_DATA_LIST] = array();

        while ($row = $result->fetch_assoc()) {
            $nameTitle = array();
            $nameTitle['id'] = (int)$row['id'];
            $nameTitle['title'] = $row['title'];
            array_push($response[KEY_DATA_LIST], $nameTitle);
        }
    } else {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
        $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการอ่านข้อมูล';
        $errMessage = $db->error;
        $response[KEY_ERROR_MESSAGE_MORE] = "$errMessage\nSQL: $sql";
    }
}

function doGetPaymentNotification()
{
    global $db, $response;

    $serviceType = $db->real_escape_string($_POST['serviceType']);
    $traineeId = $db->real_escape_string($_POST['traineeId']);

    $sql = "SELECT p.id, p.trainee_id, p.amount, p.transfer_date, p.slip_file_name, p.created_at,
                   m.id AS member_id, m.title AS member_title, m.first_name AS member_first_name, m.last_name AS member_last_name,
                   m.phone AS member_phone, m.email AS member_email
                FROM payment_notification p 
                    LEFT JOIN member m 
                        ON p.member_id = m.id  
                WHERE trainee_id = $traineeId AND service_type = '$serviceType'
                ORDER BY id DESC";
    if ($result = $db->query($sql)) {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SUCCESS;
        $response[KEY_ERROR_MESSAGE] = 'อ่านข้อมูลสำเร็จ';
        $response[KEY_ERROR_MESSAGE_MORE] = '';
        $response[KEY_DATA_LIST] = array();

        while ($row = $result->fetch_assoc()) {
            $paymentNotification = array();
            $paymentNotification['id'] = (int)$row['id'];
            $paymentNotification['trainee_id'] = (int)$row['trainee_id'];
            $paymentNotification['amount'] = (int)$row['amount'];
            $paymentNotification['transfer_date'] = $row['transfer_date'];
            $paymentNotification['slip_file_name'] = $row['slip_file_name'];
            $paymentNotification['created_at'] = $row['created_at'];

            $createdAt = $row['created_at'];
            $dateTimePart = explode(' ', $createdAt);
            $displayDate = getThaiShortDateWithDayName(date_create($dateTimePart[0]));
            $timePart = explode(':', $dateTimePart[1]);
            $displayTime = $timePart[0] . '.' . $timePart[1] . ' น.';

            $paymentNotification['notification_date_format'] = $displayDate;
            $paymentNotification['notification_time_format'] = $displayTime;

            $paymentNotification['member'] = array(
                'id' => (int)$row['member_id'], //กรณีผู้ใช้ไม่ได้ login : $row['member_id'] จะเป็น null และ id จะเป็น 0
                'title' => $row['member_title'],
                'first_name' => $row['member_first_name'],
                'last_name' => $row['member_last_name'],
                'phone' => $row['member_phone'],
                'email' => $row['member_email']
            );
            array_push($response[KEY_DATA_LIST], $paymentNotification);
        }
        $result->close();
    } else {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
        $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการอ่านข้อมูล';
        $errMessage = $db->error;
        $response[KEY_ERROR_MESSAGE_MORE] = "$errMessage\nSQL: $sql";
    }
}

function doUpdateInHouseStatus()
{
    global $db, $response;

    $id = $db->real_escape_string($_POST['id']);
    $newStatus = $db->real_escape_string($_POST['newStatus']);

    $sql = "UPDATE in_house 
                SET status = '$newStatus'
                WHERE id = $id ";

    if ($result = $db->query($sql)) {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SUCCESS;
        $response[KEY_ERROR_MESSAGE] = 'อัพเดทสถานะสำเร็จ';
        $response[KEY_ERROR_MESSAGE_MORE] = '';
    } else {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
        $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการอัพเดทสถานะ';
        $errMessage = $db->error;
        $response[KEY_ERROR_MESSAGE_MORE] = "$errMessage\nSQL: $sql";
    }
}

function doAddAcademicPaper()
{
    global $db, $response;

    $title = $db->real_escape_string($_POST['title']);
    $firstName = $db->real_escape_string($_POST['first_name']);
    $lastName = $db->real_escape_string($_POST['last_name']);
    $yearPublished = $db->real_escape_string($_POST['year_published']);
    $abstract = $db->real_escape_string($_POST['abstract']);
    $fundSource = $db->real_escape_string($_POST['fund_source']);

    if (!moveUploadedFile('file', UPLOAD_DIR_ACADEMIC_PAPERS, $fileName)) {
        $response[KEY_ERROR_CODE] = ERROR_CODE_ERROR;
        $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการอัพโหลดไฟล์';
        $response[KEY_ERROR_MESSAGE_MORE] = '';
    } else {
        $sql = "INSERT INTO academic_paper (title, file_name, first_name, last_name, year_published, abstract, fund_source) 
                VALUES ('$title', '$fileName', '$firstName', '$lastName', '$yearPublished', '$abstract', '$fundSource')";
        if ($result = $db->query($sql)) {
            $response[KEY_ERROR_CODE] = ERROR_CODE_SUCCESS;
            $response[KEY_ERROR_MESSAGE] = 'เพิ่มงานวิจัย/วิชาการสำเร็จ';
            $response[KEY_ERROR_MESSAGE_MORE] = '';
        } else {
            $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
            $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการเพิ่มงานวิจัย/วิชาการ: ' . $db->error;
            $errMessage = $db->error;
            $response[KEY_ERROR_MESSAGE_MORE] = "$errMessage\nSQL: $sql";
        }
    }
}

function doUpdateAcademicPaper()
{
    global $db, $response;

    $id = $db->real_escape_string($_POST['id']);
    $title = $db->real_escape_string($_POST['title']);
    $firstName = $db->real_escape_string($_POST['first_name']);
    $lastName = $db->real_escape_string($_POST['last_name']);
    $yearPublished = $db->real_escape_string($_POST['year_published']);
    $abstract = $db->real_escape_string($_POST['abstract']);
    $fundSource = $db->real_escape_string($_POST['fund_source']);

    $fileName = NULL;

    if ($_FILES['file']) {
        if (!moveUploadedFile('file', UPLOAD_DIR_ACADEMIC_PAPERS, $fileName)) {
            $response[KEY_ERROR_CODE] = ERROR_CODE_ERROR;
            $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการอัพโหลดไฟล์';
            $response[KEY_ERROR_MESSAGE_MORE] = '';
            return;
        }
    }

    $setUploadFileName = $fileName ? "file_name = '$fileName', " : '';

    $sql = "UPDATE academic_paper 
            SET $setUploadFileName title = '$title', first_name = '$firstName', last_name = '$lastName', year_published = '$yearPublished', abstract = '$abstract', fund_source = '$fundSource' 
            WHERE id = $id";
    if ($result = $db->query($sql)) {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SUCCESS;
        $response[KEY_ERROR_MESSAGE] = 'แก้ไขงานวิจัย/วิชาการสำเร็จ';
        $response[KEY_ERROR_MESSAGE_MORE] = '';
    } else {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
        $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการแก้ไขงานวิจัย/วิชาการ: ' . $db->error;
        $errMessage = $db->error;
        $response[KEY_ERROR_MESSAGE_MORE] = "$errMessage\nSQL: $sql";
    }
}

function doGetAcademicPaperDownload()
{
    global $db, $response;

    $academicPaperId = $db->real_escape_string($_POST['academicPaperId']);

    $sql = "SELECT *
                FROM academic_paper_download  
                WHERE academic_paper_id = $academicPaperId
                ORDER BY id DESC";
    if ($result = $db->query($sql)) {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SUCCESS;
        $response[KEY_ERROR_MESSAGE] = 'อ่านข้อมูลสำเร็จ';
        $response[KEY_ERROR_MESSAGE_MORE] = '';
        $response[KEY_DATA_LIST] = array();

        while ($row = $result->fetch_assoc()) {
            $academicPaperDownload = array();
            $academicPaperDownload['id'] = (int)$row['id'];
            $academicPaperDownload['first_name'] = $row['first_name'];
            $academicPaperDownload['last_name'] = $row['last_name'];
            $academicPaperDownload['organization_name'] = $row['organization_name'];
            $academicPaperDownload['job_position'] = $row['job_position'];
            $academicPaperDownload['occupation'] = $row['occupation'];
            $academicPaperDownload['email'] = $row['email'];
            $academicPaperDownload['use_purpose'] = $row['use_purpose'];
            $academicPaperDownload['created_at'] = $row['created_at'];

            $createdAt = $row['created_at'];
            $dateTimePart = explode(' ', $createdAt);
            $displayDate = getThaiShortDateWithDayName(date_create($dateTimePart[0]));
            $timePart = explode(':', $dateTimePart[1]);
            $displayTime = $timePart[0] . '.' . $timePart[1] . ' น.';

            $academicPaperDownload['download_date_format'] = $displayDate;
            $academicPaperDownload['download_time_format'] = $displayTime;

            array_push($response[KEY_DATA_LIST], $academicPaperDownload);
        }
        $result->close();
    } else {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
        $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการอ่านข้อมูล';
        $errMessage = $db->error;
        $response[KEY_ERROR_MESSAGE_MORE] = "$errMessage\nSQL: $sql";
    }
}

function doUpdateDrivingLicenseCourseType()
{
    global $db, $response;

    $id = $db->real_escape_string($_POST['id']);
    $title = $db->real_escape_string($_POST['title']);
    $fee = $db->real_escape_string($_POST['fee']);

    $sql = "UPDATE driving_license_course_type 
                SET title = '$title', application_fee = $fee
                WHERE id = $id ";

    if ($result = $db->query($sql)) {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SUCCESS;
        $response[KEY_ERROR_MESSAGE] = 'อัพเดทข้อมูลประเภทหลักสูตรสำเร็จ';
        $response[KEY_ERROR_MESSAGE_MORE] = '';
    } else {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
        $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการอัพเดทข้อมูลประเภทหลักสูตร';
        $errMessage = $db->error;
        $response[KEY_ERROR_MESSAGE_MORE] = "$errMessage\nSQL: $sql";
    }
}

function doSaveDrivingLicenseCourseResult()
{
    global $db, $response;

    $traineeId = $db->real_escape_string($_POST['traineeId']);
    $subject1Result = $db->real_escape_string($_POST['subject1Result']);
    $subject2Result = $db->real_escape_string($_POST['subject2Result']);
    $subject3Result = $db->real_escape_string($_POST['subject3Result']);
    $subject4Result = $db->real_escape_string($_POST['subject4Result']);

    if ($subject1Result == null && $subject2Result == null && $subject3Result == null && $subject4Result == null) {
        $response[KEY_ERROR_CODE] = ERROR_CODE_ERROR;
        $response[KEY_ERROR_MESSAGE] = "กรุณาเลือก 'ผ่าน' หรือ 'ไม่ผ่าน'";
        $response[KEY_ERROR_MESSAGE_MORE] = '';
        return;
    }

    $subject1Set = "subject_1_result = " . ($subject1Result == null ? 'NULL' : $subject1Result);
    $subject2Set = "subject_2_result = " . ($subject2Result == null ? 'NULL' : $subject2Result);
    $subject3Set = "subject_3_result = " . ($subject3Result == null ? 'NULL' : $subject3Result);
    $subject4Set = "subject_4_result = " . ($subject4Result == null ? 'NULL' : $subject4Result);

    $sql = "UPDATE course_registration_driving_license
            SET $subject1Set, $subject2Set, $subject3Set, $subject4Set
            WHERE id = $traineeId ";

    if ($result = $db->query($sql)) {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SUCCESS;
        $response[KEY_ERROR_MESSAGE] = 'บันทึกผลการอบรมสำเร็จ';
        $response[KEY_ERROR_MESSAGE_MORE] = '';
    } else {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
        $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการบันทึกผลการอบรม';
        $errMessage = $db->error;
        $response[KEY_ERROR_MESSAGE_MORE] = "$errMessage\nSQL: $sql";
    }
}

function doUpdateDrivingLicenseRegistration()
{
    global $db, $response;

    $traineeId = $db->real_escape_string($_POST['traineeId']);
    $traineeTitle = $db->real_escape_string($_POST['traineeTitle']);
    $traineeFirstName = $db->real_escape_string($_POST['traineeFirstName']);
    $traineeLastName = $db->real_escape_string($_POST['traineeLastName']);
    $traineePid = $db->real_escape_string($_POST['traineePid']);
    $traineePhone = $db->real_escape_string($_POST['traineePhone']);

    $address = $db->real_escape_string($_POST['address']);
    $moo = $db->real_escape_string($_POST['moo']);
    $soi = $db->real_escape_string($_POST['soi']);
    $road = $db->real_escape_string($_POST['road']);
    $subDistrict = $db->real_escape_string($_POST['subDistrict']);
    $district = $db->real_escape_string($_POST['district']);
    $province = $db->real_escape_string($_POST['province']);
    $courseType = $db->real_escape_string($_POST['courseType']);
    $licenseTypeCar = $db->real_escape_string($_POST['licenseTypeCar']) == null ? 0 : 1;
    $licenseTypeBicycle = $db->real_escape_string($_POST['licenseTypeBicycle']) == null ? 0 : 1;
    $licenseTypeTricycle = $db->real_escape_string($_POST['licenseTypeTricycle']) == null ? 0 : 1;

    $licenseType = $licenseTypeCar + ($licenseTypeBicycle * 2) + ($licenseTypeTricycle * 4);

    /*$response[KEY_ERROR_CODE] = ERROR_CODE_SUCCESS;
    $response[KEY_ERROR_MESSAGE] = "Car: $licenseTypeCar, Bicycle: $licenseTypeBicycle, Tricycle: $licenseTypeTricycle";
    return;*/

    $sql = "UPDATE course_registration_driving_license
            SET title = '$traineeTitle', first_name = '$traineeFirstName', last_name = '$traineeLastName', pid = '$traineePid', phone = '$traineePhone',
                address = '$address', moo = '$moo', soi = '$soi', road = '$road', sub_district = '$subDistrict', district = '$district', province = '$province',
                course_type = $courseType, license_type = $licenseType
            WHERE id = $traineeId ";

    if ($result = $db->query($sql)) {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SUCCESS;
        $response[KEY_ERROR_MESSAGE] = 'บันทึกข้อมูลใบสมัครสำเร็จ';
        $response[KEY_ERROR_MESSAGE_MORE] = '';
    } else {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
        $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการบันทึกข้อมูลใบสมัคร';
        $errMessage = $db->error;
        $response[KEY_ERROR_MESSAGE_MORE] = "$errMessage\nSQL: $sql";
    }
}

function moveUploadedFile($key, $dest, &$randomFileName, $index = -1)
{
    global $response;

    $clientName = $index === -1 ? $_FILES[$key]['name'] : $_FILES[$key]['name'][$index];
    $response['name'] = $clientName;
    $response['type'] = $index === -1 ? $_FILES[$key]['type'] : $_FILES[$key]['type'][$index];
    $response['size'] = $index === -1 ? $_FILES[$key]['size'] : $_FILES[$key]['size'][$index];
    $response['tmp_name'] = $index === -1 ? $_FILES[$key]['tmp_name'] : $_FILES[$key]['tmp_name'][$index];

    $src = $index === -1 ? $_FILES[$key]['tmp_name'] : $_FILES[$key]['tmp_name'][$index];
    $response['upload_src'] = $src;
    $response['upload_dest'] = $dest;

    //$date = date('Y-m-d H:i:s');
    //$timestamp = time();
    $timestamp = round(microtime(true) * 1000);
    $randomFileName = "{$timestamp}-{$clientName}";
    return move_uploaded_file($src, "{$dest}{$randomFileName}");
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