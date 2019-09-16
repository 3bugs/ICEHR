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

usleep(350000); //todo: *****************************************************************************************

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
    case 'update_member':
        doUpdateMember();
        break;
    case 'delete_member':
        doDeleteMember();
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
    case 'update_course_status':
        doUpdateCourseStatus();
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
    case 'update_in_house':
        doUpdateInHouse();
        break;
    case 'update_in_house_status':
        doUpdateInHouseStatus();
        break;
    case 'delete_in_house':
        doDeleteInHouse();
        break;
    case 'add_academic_paper':
        doAddAcademicPaper();
        break;
    case 'update_academic_paper':
        doUpdateAcademicPaper();
        break;
    case 'delete_academic_paper':
        doDeleteAcademicPaper();
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
    case 'update_receipt_number':
        doUpdateReceiptNumber();
        break;

    case 'add_document_download':
        doAddDocumentDownload();
        break;
    case 'update_document_download':
        doUpdateDocumentDownload();
        break;
    case 'update_document_download_status':
        doUpdateDocumentDownloadStatus();
        break;
    case 'delete_document_download':
        doDeleteDocumentDownload();
        break;
    case 'add_news':
        doAddNews();
        break;
    case 'update_news':
        doUpdateNews();
        break;
    case 'update_news_status':
        doUpdateNewsStatus();
        break;
    case 'delete_news':
        doDeleteNews();
        break;
    case 'delete_news_asset':
        doDeleteNewsAsset();
        break;
    case 'add_user':
        doAddUser();
        break;
    case 'update_user':
        doUpdateUser();
        break;
    case 'update_user_status':
        doUpdateUserStatus();
        break;
    case 'add_trainer':
        doAddTrainer();
        break;
    case 'update_trainer':
        doUpdateTrainer();
        break;
    case 'update_trainer_status':
        doUpdateTrainerStatus();
        break;
    case 'add_user_department':
        doAddUserDepartment();
        break;
    case 'update_user_department':
        doUpdateUserDepartment();
        break;
    case 'delete_user_department':
        doDeleteUserDepartment();
        break;
    case 'sort_user_department':
        doSortUserDepartment();
        break;
    case 'get_user_by_department':
        doGetUserByDepartment();
        break;
    case 'sort_user':
        doSortUser();
        break;
    case 'add_training_course_category':
        doAddTrainingCourseCategory();
        break;
    case 'update_training_course_category':
        doUpdateTrainingCourseCategory();
        break;
    case 'delete_training_course_category':
        doDeleteTrainingCourseCategory();
        break;
    case 'add':
        doAdd();
        break;
    case 'update':
        doUpdate();
        break;
    case 'update_status':
        doUpdateStatus();
        break;
    case 'delete':
        doDelete();
        break;

    case 'add_intro':
        doAddIntro();
        break;
    case 'update_intro':
        doUpdateIntro();
        break;
    case 'delete_intro':
        doDeleteIntro();
        break;
    case 'update_intro_status':
        doUpdateIntroStatus();
        break;
    case 'sort_intro':
        doSortIntro();
        break;

    case 'update_service':
        doUpdateService();
        break;


    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    case 'add_training_project_news':
        doAddTrainingProjectNews();
        break;
    case 'update_training_project_news':
        doUpdateTrainingProjectNews();
        break;
    case 'add_news_old':
        doAddNewsOld();
        break;
    case 'update_news_old':
        doUpdateNewsOld();
        break;
    case 'add_menu':
        doAddMenu();
        break;
    case 'update_menu':
        doUpdateMenu();
        break;
    case 'add_banner':
        doAddBanner();
        break;
    case 'update_banner':
        doUpdateBanner();
        break;
    case 'add_research_and_academic':
        doAddResearchAndAcademic();
        break;
    case 'update_research_and_academic':
        doUpdateresearchAndAcademic();
        break;
    case 'add_organizational_structure':
        doAddOrganizationalStructure();
        break;
    case 'update_organizational_structure':
        doUpdateOrganizationalStructure();
        break;
    case 'add_mission':
        doAddMission();
        break;
    case 'update_mission':
        doUpdateMission();
        break;
    case 'add_about_organization':
        doAddAboutOrganization();
        break;
    case 'update_about_organization':
        doUpdateAboutOrganization();
        break;
    case 'add_faq':
        doAddFaq();
        break;
    case 'update_faq':
        doUpdateFaq();
        break;
    case 'add_activity':
        doAddActivity();
        break;
    case 'update_activity':
        doUpdateActivity();
        break;
    case 'add_document':
        doAddDocument();
        break;
    case 'update_document':
        doUpdateDocument();
        break;
    case 'delete_media':
        doDeleteMedia();
        break;
    case 'delete_document':
        doDeleteDocument();
        break;
    case 'delete_mp4':
        doDeleteMp4();
        break;
    case 'delete_article':
        $articleId = $db->real_escape_string($_POST['articleId']);
        doDeleteArticle($articleId);
        break;
    case 'delete_faq':
        $faqId = $db->real_escape_string($_POST['faqId']);
        doDeleteFaq($faqId);
        break;
    case 'delete_banner':
        $bannerId = $db->real_escape_string($_POST['bannerId']);
        doDeleteBanner($bannerId);
        break;
    case 'delete_menu':
        $menuId = $db->real_escape_string($_POST['menuId']);
        doDeleteMenu($menuId);
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
            //$user['role'] = $row['role'];
            $response['user'] = $user;

            createSession($row);
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
    $_SESSION[KEY_SESSION_USER_ID] = (int)$user['id'];
    $_SESSION[KEY_SESSION_USER_USERNAME] = $user['username'];
    $_SESSION[KEY_SESSION_USER_FIRST_NAME] = $user['first_name'];
    $_SESSION[KEY_SESSION_USER_LAST_NAME] = $user['last_name'];
    $_SESSION[KEY_SESSION_USER_EMAIL] = $user['email'];
    $_SESSION[KEY_SESSION_USER_ROLE] = $user['role'];
    $_SESSION[KEY_SESSION_USER_PERMISSION] = (int)$user['permissions'];
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

function doUpdateMember()
{
    global $db, $response;

    $id = $db->real_escape_string($_POST['id']);
    $title = $db->real_escape_string($_POST['title']);
    $firstName = $db->real_escape_string($_POST['firstName']);
    $lastName = $db->real_escape_string($_POST['lastName']);
    $phone = $db->real_escape_string($_POST['phone']);
    //$email = $db->real_escape_string($_POST['email']);

    $jobPosition = isset($_POST['jobPosition']) ? trim($_POST['jobPosition']) : null;
    $organizationName = isset($_POST['organizationName']) ? trim($_POST['organizationName']) : null;
    $organizationType = ((int)$_POST['organizationType']) === 0 ? null : $_POST['organizationType'];
    $organizationTypeCustom = isset($_POST['organizationTypeCustom']) ? trim($_POST['organizationTypeCustom']) : null;
    $address = isset($_POST['address']) ? trim($_POST['address']) : null;
    $subDistrict = isset($_POST['subDistrict']) ? trim($_POST['subDistrict']) : null;
    $district = isset($_POST['district']) ? trim($_POST['district']) : null;
    $province = isset($_POST['province']) ? trim($_POST['province']) : null;
    $postalCode = isset($_POST['postalCode']) ? trim($_POST['postalCode']) : null;
    $organizationPhone = isset($_POST['organizationPhone']) ? trim($_POST['organizationPhone']) : null;
    $taxId = isset($_POST['taxId']) ? trim($_POST['taxId']) : null;

    $fieldSet = '';
    $fieldSet .= $jobPosition ? ", job_position = '$jobPosition'" : ', job_position = NULL';
    $fieldSet .= $organizationName ? ", organization_name = '$organizationName'" : ', organization_name = NULL';
    $fieldSet .= $organizationType ? ", organization_type = $organizationType" : ', organization_type = NULL';
    $fieldSet .= $organizationTypeCustom ? ", organization_type_custom = '$organizationTypeCustom'" : ', organization_type_custom = NULL';
    $fieldSet .= $address ? ", address = '$address'" : ', address = NULL';
    $fieldSet .= $subDistrict ? ", sub_district = '$subDistrict'" : ', sub_district = NULL';
    $fieldSet .= $district ? ", district = '$district'" : ', district = NULL';
    $fieldSet .= $province ? ", province = '$province'" : ', province = NULL';
    $fieldSet .= $postalCode ? ", postal_code = '$postalCode'" : ', postal_code = NULL';
    $fieldSet .= $organizationPhone ? ", organization_phone = '$organizationPhone'" : ', organization_phone = NULL';
    $fieldSet .= $taxId ? ", tax_id = '$taxId'" : ', tax_id = NULL';

    $sql = "UPDATE member 
                SET title = '$title', first_name = '$firstName', last_name = '$lastName', 
                    phone = '$phone' $fieldSet
                WHERE id = $id ";

    if ($result = $db->query($sql)) {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SUCCESS;
        $response[KEY_ERROR_MESSAGE] = 'อัพเดทข้อมูลสำเร็จ';
        $response[KEY_ERROR_MESSAGE_MORE] = '';
    } else {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
        $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการอัพเดทข้อมูล: '. $db->error;
        $errMessage = $db->error;
        $response[KEY_ERROR_MESSAGE_MORE] = "$errMessage\nSQL: $sql";
    }
}

function doDeleteMember()
{
    global $db, $response;

    $memberId = $db->real_escape_string($_POST['member_id']);
    $sql = "DELETE FROM member WHERE id = $memberId";

    if ($result = $db->query($sql)) {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SUCCESS;
        $response[KEY_ERROR_MESSAGE] = 'ลบสมาชิกเว็บไซต์สำเร็จ';
        $response[KEY_ERROR_MESSAGE_MORE] = '';
    } else {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
        $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการลบข้อมูล';
        $errMessage = $db->error;
        $response[KEY_ERROR_MESSAGE_MORE] = "$errMessage\nSQL: $sql";
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

function doAddUserDepartment()
{
    global $db, $response;

    if (!checkPermission(PERMISSION_USER_DEPARTMENT_MANAGE)) {
        return;
    }

    $sql = "SELECT MAX(sort_index) AS max_sort_index FROM user_department";
    if ($result = $db->query($sql)) {
        $row = $result->fetch_assoc();
        $nextSortIndex = 1;
        if ($row['max_sort_index'] != null) {
            $nextSortIndex = (int)$row['max_sort_index'] + 1;
        }
        $result->close();
    } else {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
        $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการเพิ่มชื่อฝ่าย (1): ' . $db->error;
        $errMessage = $db->error;
        $response[KEY_ERROR_MESSAGE_MORE] = "$errMessage\nSQL: $sql";
        return;
    }

    $departmentName = $db->real_escape_string($_POST['departmentName']);

    $sql = "INSERT INTO user_department (name, sort_index) 
            VALUES ('$departmentName', $nextSortIndex)";
    if ($result = $db->query($sql)) {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SUCCESS;
        $response[KEY_ERROR_MESSAGE] = 'เพิ่มชื่อฝ่ายสำเร็จ';
        $response[KEY_ERROR_MESSAGE_MORE] = '';
    } else {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
        $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการเพิ่มชื่อฝ่าย (2): ' . $db->error;
        $errMessage = $db->error;
        $response[KEY_ERROR_MESSAGE_MORE] = "$errMessage\nSQL: $sql";
    }
}

function doUpdateUserDepartment()
{
    global $db, $response;

    if (!checkPermission(PERMISSION_USER_DEPARTMENT_MANAGE)) {
        return;
    }

    $departmentId = $db->real_escape_string($_POST['departmentId']);
    $departmentName = $db->real_escape_string($_POST['departmentName']);

    $sql = "UPDATE user_department SET name = '$departmentName' 
            WHERE id = $departmentId";
    if ($result = $db->query($sql)) {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SUCCESS;
        $response[KEY_ERROR_MESSAGE] = 'อัพเดทชื่อฝ่ายสำเร็จ';
        $response[KEY_ERROR_MESSAGE_MORE] = '';
    } else {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
        $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการอัพเดทชื่อฝ่าย' . $db->error;
        $errMessage = $db->error;
        $response[KEY_ERROR_MESSAGE_MORE] = "$errMessage\nSQL: $sql";
    }
}

function doDeleteUserDepartment()
{
    global $db, $response;

    if (!checkPermission(PERMISSION_USER_DEPARTMENT_MANAGE)) {
        return;
    }

    $departmentId = $db->real_escape_string($_POST['departmentId']);

    $sql = "DELETE FROM user_department  
            WHERE id = $departmentId";
    if ($result = $db->query($sql)) {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SUCCESS;
        $response[KEY_ERROR_MESSAGE] = 'ลบชื่อฝ่ายสำเร็จ';
        $response[KEY_ERROR_MESSAGE_MORE] = '';
    } else {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
        $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการลบชื่อฝ่าย' . $db->error;
        $errMessage = $db->error;
        $response[KEY_ERROR_MESSAGE_MORE] = "$errMessage\nSQL: $sql";
    }
}

function doAddTrainingCourseCategory()
{
    global $db, $response;

    $categoryTitle = $db->real_escape_string($_POST['categoryTitle']);

    $sql = "INSERT INTO training_course_category (title) 
            VALUES ('$categoryTitle')";
    if ($result = $db->query($sql)) {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SUCCESS;
        $response[KEY_ERROR_MESSAGE] = 'เพิ่มข้อมูลสำเร็จ';
        $response[KEY_ERROR_MESSAGE_MORE] = '';
    } else {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
        $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการเพิ่มข้อมูล: ' . $db->error;
        $errMessage = $db->error;
        $response[KEY_ERROR_MESSAGE_MORE] = "$errMessage\nSQL: $sql";
    }
}

function doUpdateTrainingCourseCategory()
{
    global $db, $response;

    $categoryId = $db->real_escape_string($_POST['categoryId']);
    $categoryTitle = $db->real_escape_string($_POST['categoryTitle']);

    $sql = "UPDATE training_course_category SET title = '$categoryTitle' 
            WHERE id = $categoryId";
    if ($result = $db->query($sql)) {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SUCCESS;
        $response[KEY_ERROR_MESSAGE] = 'อัพเดทข้อมูลสำเร็จ';
        $response[KEY_ERROR_MESSAGE_MORE] = '';
    } else {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
        $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการอัพเดทข้อมูล' . $db->error;
        $errMessage = $db->error;
        $response[KEY_ERROR_MESSAGE_MORE] = "$errMessage\nSQL: $sql";
    }
}

function doDeleteTrainingCourseCategory()
{
    global $db, $response;

    $categoryId = $db->real_escape_string($_POST['categoryId']);

    $sql = "DELETE FROM training_course_category  
            WHERE id = $categoryId";
    if ($result = $db->query($sql)) {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SUCCESS;
        $response[KEY_ERROR_MESSAGE] = 'ลบข้อมูลสำเร็จ';
        $response[KEY_ERROR_MESSAGE_MORE] = '';
    } else {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
        $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการลบข้อมูล' . $db->error;
        $errMessage = $db->error;
        $response[KEY_ERROR_MESSAGE_MORE] = "$errMessage\nSQL: $sql";
    }
}

function doAdd()
{
    global $db, $response;

    $tableName = $db->real_escape_string($_POST['tableName']);
    $title = $db->real_escape_string($_POST['title']);

    if (isset($_POST['details'])) {
        $details = $db->real_escape_string($_POST['details']);
    }

    if (isset($details)) {
        $sql = "INSERT INTO $tableName (title, details) 
            VALUES ('$title', '$details')";
    } else {
        $sql = "INSERT INTO $tableName (title) 
            VALUES ('$title')";
    }
    if ($result = $db->query($sql)) {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SUCCESS;
        $response[KEY_ERROR_MESSAGE] = 'เพิ่มข้อมูลสำเร็จ';
        $response[KEY_ERROR_MESSAGE_MORE] = '';
    } else {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
        $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการเพิ่มข้อมูล: ' . $db->error;
        $errMessage = $db->error;
        $response[KEY_ERROR_MESSAGE_MORE] = "$errMessage\nSQL: $sql";
    }
}

function doUpdate()
{
    global $db, $response;

    $tableName = $db->real_escape_string($_POST['tableName']);
    $id = $db->real_escape_string($_POST['id']);
    $title = $db->real_escape_string($_POST['title']);

    if (isset($_POST['details'])) {
        $details = $db->real_escape_string($_POST['details']);
    }

    if (isset($details)) {
        $sql = "UPDATE $tableName SET title = '$title', details = '$details' 
            WHERE id = $id";
    } else {
        $sql = "UPDATE $tableName SET title = '$title' 
            WHERE id = $id";
    }

    if ($result = $db->query($sql)) {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SUCCESS;
        $response[KEY_ERROR_MESSAGE] = 'อัพเดทข้อมูลสำเร็จ';
        $response[KEY_ERROR_MESSAGE_MORE] = '';
    } else {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
        $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการอัพเดทข้อมูล' . $db->error;
        $errMessage = $db->error;
        $response[KEY_ERROR_MESSAGE_MORE] = "$errMessage\nSQL: $sql";
    }
}

function doUpdateStatus()
{
    global $db, $response;

    $tableName = $db->real_escape_string($_POST['tableName']);
    $id = $db->real_escape_string($_POST['id']);
    $newStatus = $db->real_escape_string($_POST['newStatus']);

    $sql = "UPDATE $tableName SET status = '$newStatus' WHERE id = $id";
    if ($result = $db->query($sql)) {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SUCCESS;
        $response[KEY_ERROR_MESSAGE] = 'อัพเดทสถานะสำเร็จ';
        $response[KEY_ERROR_MESSAGE_MORE] = '';
    } else {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
        $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $db->error;
        $errMessage = $db->error;
        $response[KEY_ERROR_MESSAGE_MORE] = "$errMessage\nSQL: $sql";
    }
}

function doDelete()
{
    global $db, $response;

    $tableName = $db->real_escape_string($_POST['tableName']);
    $id = $db->real_escape_string($_POST['id']);

    $sql = "DELETE FROM $tableName
            WHERE id = $id";
    if ($result = $db->query($sql)) {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SUCCESS;
        $response[KEY_ERROR_MESSAGE] = 'ลบข้อมูลสำเร็จ';
        $response[KEY_ERROR_MESSAGE_MORE] = '';
    } else {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
        $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการลบข้อมูล' . $db->error;
        $errMessage = $db->error;
        $response[KEY_ERROR_MESSAGE_MORE] = "$errMessage\nSQL: $sql";
    }
}

function doUpdateService()
{
    global $db, $response;

    $id = $db->real_escape_string($_POST['id']);
    $title = $db->real_escape_string($_POST['title']);
    $details = $db->real_escape_string($_POST['details']);
    $url = $db->real_escape_string($_POST['url']);

    $sql = "UPDATE service SET title = '$title', details = '$details', url = '$url' WHERE id = $id";

    if ($result = $db->query($sql)) {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SUCCESS;
        $response[KEY_ERROR_MESSAGE] = 'อัพเดทข้อมูลสำเร็จ';
        $response[KEY_ERROR_MESSAGE_MORE] = '';
    } else {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
        $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการอัพเดทข้อมูล' . $db->error;
        $errMessage = $db->error;
        $response[KEY_ERROR_MESSAGE_MORE] = "$errMessage\nSQL: $sql";
    }
}

function doAddIntro()
{
    global $db, $response;

    $type = $db->real_escape_string($_POST['type']);
    $title = $db->real_escape_string($_POST['title']);
    $details = $db->real_escape_string($_POST['details']);

    if ($_FILES['image']) {
        if (!moveUploadedFile('image', UPLOAD_DIR_INTRO_ASSETS, $imageFileName)) {
            $response[KEY_ERROR_CODE] = ERROR_CODE_ERROR;
            $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการอัพโหลดรูปภาพ';
            $response[KEY_ERROR_MESSAGE_MORE] = '';
            return;
        }
    }

    $sql = "INSERT INTO intro (title, details, image_file_name, type, sort_index) 
                VALUES ('$title', '$details', '$imageFileName', '$type', 999999)";
    if ($result = $db->query($sql)) {
        reArrangeIntroSortIndex($type);

        $response[KEY_ERROR_CODE] = ERROR_CODE_SUCCESS;
        $response[KEY_ERROR_MESSAGE] = 'เพิ่มข้อมูลสำเร็จ';
        $response[KEY_ERROR_MESSAGE_MORE] = '';
    } else {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
        $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการเพิ่มข้อมูล: ' . $db->error;
        $errMessage = $db->error;
        $response[KEY_ERROR_MESSAGE_MORE] = "$errMessage\nSQL: $sql";
    }
}

function doUpdateIntro()
{
    global $db, $response;

    $id = $db->real_escape_string($_POST['id']);
    $title = $db->real_escape_string($_POST['title']);
    $details = $db->real_escape_string($_POST['details']);

    $imageFileName = NULL;

    if ($_FILES['image']) {
        if (!moveUploadedFile('image', UPLOAD_DIR_INTRO_ASSETS, $imageFileName)) {
            $response[KEY_ERROR_CODE] = ERROR_CODE_ERROR;
            $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการอัพโหลดรูปภาพ';
            $response[KEY_ERROR_MESSAGE_MORE] = '';
            return;
        }
    }

    $setUploadFileName = $imageFileName ? "image_file_name = '$imageFileName', " : '';

    $sql = "UPDATE intro 
            SET $setUploadFileName title = '$title', details = '$details' 
            WHERE id = $id";
    if ($result = $db->query($sql)) {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SUCCESS;
        $response[KEY_ERROR_MESSAGE] = 'แก้ไขข้อมูลสำเร็จ';
        $response[KEY_ERROR_MESSAGE_MORE] = '';
    } else {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
        $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการแก้ไขข้อมูล: ' . $db->error;
        $errMessage = $db->error;
        $response[KEY_ERROR_MESSAGE_MORE] = "$errMessage\nSQL: $sql";
    }
}

function doDeleteIntro()
{
    global $db, $response;

    $id = $db->real_escape_string($_POST['id']);

    $sql = "SELECT image_file_name FROM intro WHERE id = $id";
    if ($result = $db->query($sql)) {
        if ($result > 0) {
            $row = $result->fetch_assoc();
            $fileName = $row['image_file_name'];

            $deleteSql = "DELETE FROM intro WHERE id = $id";
            if ($deleteResult = $db->query($deleteSql)) {
                unlink(UPLOAD_DIR_INTRO_ASSETS . $fileName);

                $response[KEY_ERROR_CODE] = ERROR_CODE_SUCCESS;
                $response[KEY_ERROR_MESSAGE] = 'ลบข้อมูลสำเร็จ';
                $response[KEY_ERROR_MESSAGE_MORE] = '';
            } else {
                $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
                $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการลบข้อมูล: ' . $db->error;
                $errMessage = $db->error;
                $response[KEY_ERROR_MESSAGE_MORE] = "$errMessage\nSQL: $deleteSql";
            }
        } else {
            $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
            $response[KEY_ERROR_MESSAGE] = 'ไม่พบข้อมูล!';
            $response[KEY_ERROR_MESSAGE_MORE] = '';
        }
        $result->close();
    } else {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
        $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการเข้าถึงฐานข้อมูล: ' . $db->error;
        $errMessage = $db->error;
        $response[KEY_ERROR_MESSAGE_MORE] = "$errMessage\nSQL: $sql";
    }
}

function doUpdateIntroStatus()
{
    global $db, $response;

    if (!checkPermission(PERMISSION_MANAGE_WEB_CONTENT)) {
        return;
    }

    $id = $db->real_escape_string($_POST['id']);
    $newStatus = $db->real_escape_string($_POST['newStatus']);
    $type = $db->real_escape_string($_POST['type']);

    $sql = "UPDATE intro SET status = '$newStatus' WHERE id = $id";
    if ($result = $db->query($sql)) {
        if ($newStatus === 'publish') {
            $sql = "UPDATE intro SET sort_index = 999999 WHERE id = $id";
            $db->query($sql);
            reArrangeIntroSortIndex($type);

            /*$sql = "SELECT type FROM intro WHERE id = $id";
            if ($result = $db->query($sql)) {
                $row = $result->fetch_assoc();
                $result->close();
                reArrangeIntroSortIndex($row['type']);

                $sql = "SELECT MAX(sort_index) AS max_sort_id FROM intro WHERE type = '{$row['type']}'";
                if ($result = $db->query($sql)) {
                    $row = $result->fetch_assoc();
                    $nextId = (int)$row['max_sort_id'] + 1;
                    $result->close();

                    $sql = "UPDATE intro SET sort_index = $nextId WHERE id = $id";
                    $db->query($sql);
                }
            }*/
        } else {
            $sql = "UPDATE intro SET sort_index = NULL WHERE id = $id";
            $db->query($sql);
            reArrangeIntroSortIndex($type);
        }

        $response[KEY_ERROR_CODE] = ERROR_CODE_SUCCESS;
        $response[KEY_ERROR_MESSAGE] = 'อัพเดทสถานะสำเร็จ';
        $response[KEY_ERROR_MESSAGE_MORE] = '';
    } else {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
        $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $db->error;
        $errMessage = $db->error;
        $response[KEY_ERROR_MESSAGE_MORE] = "$errMessage\nSQL: $sql";
    }
}

function reArrangeIntroSortIndex($type)
{
    global $db;

    $sql = "SELECT id FROM intro WHERE type = '$type' AND sort_index IS NOT NULL ORDER BY sort_index";
    if ($result = $db->query($sql)) {
        $idList = array();
        while ($row = $result->fetch_assoc()) {
            array_push($idList, $row['id']);
        }
        $result->close();

        $i = 1;
        foreach ($idList as $id) {
            $sql = "UPDATE intro SET sort_index = $i WHERE id = $id";
            $db->query($sql);
            $i++;
        }
    }
}

function doSortIntro()
{
    global $db, $response;

    if (!checkPermission(PERMISSION_MANAGE_WEB_CONTENT)) {
        return;
    }

    $sortValue = $db->real_escape_string($_POST['sortValue']);

    $sortValuePartList = explode(',', $sortValue);
    foreach ($sortValuePartList as $part) {
        $p = explode('-', $part);
        $itemId = $p[0];
        $sortIndex = $p[1];

        $sql = "UPDATE intro SET sort_index = $sortIndex 
                WHERE id = $itemId";
        if ($result = $db->query($sql)) {
        } else {
            $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
            $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการอัพเดทข้อมูล' . $db->error;
            $errMessage = $db->error;
            $response[KEY_ERROR_MESSAGE_MORE] = "$errMessage\nSQL: $sql";
            return;
        }
    }

    $response[KEY_ERROR_CODE] = ERROR_CODE_SUCCESS;
    $response[KEY_ERROR_MESSAGE] = 'อัพเดทข้อมูลสำเร็จ';
    $response[KEY_ERROR_MESSAGE_MORE] = '';
}

function doSortUserDepartment()
{
    global $db, $response;

    if (!checkPermission(PERMISSION_USER_DEPARTMENT_MANAGE)) {
        return;
    }

    $sortValue = $db->real_escape_string($_POST['sortValue']);

    $sortValuePartList = explode(',', $sortValue);
    foreach ($sortValuePartList as $part) {
        $p = explode('-', $part);
        $departmentId = $p[0];
        $sortIndex = $p[1];

        $sql = "UPDATE user_department SET sort_index = $sortIndex 
                WHERE id = $departmentId";
        if ($result = $db->query($sql)) {
        } else {
            $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
            $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการอัพเดทข้อมูล' . $db->error;
            $errMessage = $db->error;
            $response[KEY_ERROR_MESSAGE_MORE] = "$errMessage\nSQL: $sql";
            return;
        }
    }

    $response[KEY_ERROR_CODE] = ERROR_CODE_SUCCESS;
    $response[KEY_ERROR_MESSAGE] = 'อัพเดทข้อมูลสำเร็จ';
    $response[KEY_ERROR_MESSAGE_MORE] = '';
}

function doSortUser()
{
    global $db, $response;

    if (!checkPermission(PERMISSION_USER_DEPARTMENT_MANAGE)) {
        return;
    }

    $sortValue = $db->real_escape_string($_POST['sortValue']);

    $sortValuePartList = explode(',', $sortValue);
    foreach ($sortValuePartList as $part) {
        $p = explode('-', $part);
        $userId = $p[0];
        $sortIndex = $p[1];

        $sql = "UPDATE user SET sort_index = $sortIndex 
                WHERE id = $userId";
        if ($result = $db->query($sql)) {
        } else {
            $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
            $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการอัพเดทข้อมูล' . $db->error;
            $errMessage = $db->error;
            $response[KEY_ERROR_MESSAGE_MORE] = "$errMessage\nSQL: $sql";
            return;
        }
    }

    $response[KEY_ERROR_CODE] = ERROR_CODE_SUCCESS;
    $response[KEY_ERROR_MESSAGE] = 'อัพเดทข้อมูลสำเร็จ';
    $response[KEY_ERROR_MESSAGE_MORE] = '';
}

function doGetUserByDepartment()
{
    global $db, $response;

    $departmentId = $db->real_escape_string($_POST['departmentId']);

    $sql = "SELECT id, username, title, first_name, last_name, position, image_file_name, sort_index FROM user 
            WHERE department_id = $departmentId 
            ORDER BY sort_index";

    if ($result = $db->query($sql)) {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SUCCESS;
        $response[KEY_ERROR_MESSAGE] = 'อ่านข้อมูลผู้ใช้งานสำเร็จ';
        $response[KEY_ERROR_MESSAGE_MORE] = '';
        $response[KEY_DATA_LIST] = array();

        while ($row = $result->fetch_assoc()) {
            $user = array();
            $user['id'] = (int)$row['id'];
            $user['username'] = $row['username'];
            $user['title'] = $row['title'];
            $user['first_name'] = $row['first_name'];
            $user['last_name'] = $row['last_name'];
            $user['position'] = $row['position'];
            $user['image_file_name'] = $row['image_file_name'];
            $user['sort_index'] = (int)$row['sort_index'];

            array_push($response[KEY_DATA_LIST], $user);
        }
        $result->close();
    } else {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
        $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการอ่านข้อมูลผู้ใช้งาน';
        $errMessage = $db->error;
        $response[KEY_ERROR_MESSAGE_MORE] = "$errMessage\nSQL: $sql";
    }
}

function doAddCourse()
{
    global $db, $response;

    $courseMasterId = $db->real_escape_string($_POST['courseMasterId']);

    /*ตรวจสอบสิทธิ์*/
    $sql = "SELECT service_type FROM course_master WHERE id = $courseMasterId";
    $result = $db->query($sql);
    $row = $result->fetch_assoc();
    switch ($row['service_type']) {
        case 'training':
            if (!checkPermission(PERMISSION_COURSE_TRAINING_CREATE)) {
                return;
            }
            break;
        case 'social':
            if (!checkPermission(PERMISSION_COURSE_SOCIAL_CREATE)) {
                return;
            }
            break;
        case 'driving_license':
            if (!checkPermission(PERMISSION_COURSE_DRIVING_LICENSE_CREATE)) {
                return;
            }
            break;
    }

    $batchNumber = isset($_POST['batchNumber']) ? $db->real_escape_string($_POST['batchNumber']) : 'NULL';
    $applicationFee = isset($_POST['applicationFee']) ? $db->real_escape_string($_POST['applicationFee']) : 'NULL';
    $traineeLimit = $db->real_escape_string($_POST['traineeLimit']);
    $beginDate = getMySqlDateFormat($db->real_escape_string($_POST['beginDate']));
    $endDate = getMySqlDateFormat($db->real_escape_string($_POST['endDate']));
    $place = $db->real_escape_string($_POST['place']);
    $responsibleUserId = $db->real_escape_string($_POST['responsibleUserId']);
    $details = $db->real_escape_string($_POST['details']);

    $trainerId = null;
    $showTrainerSignature = 1;
    if (isset($_POST['trainerId'])) {
        $trainerId = $db->real_escape_string($_POST['trainerId']);
        $showTrainerSignature = isset($_POST['showTrainerSignature']) ? 1 : 0;
    }

    $db->query('START TRANSACTION');

    if ($trainerId == null) {
        $sql = "INSERT INTO course (course_master_id, batch_number, details, application_fee, trainee_limit, place, begin_date, end_date, responsible_user_id) "
            . " VALUES ($courseMasterId, $batchNumber, '$details', $applicationFee, $traineeLimit, '$place', '$beginDate', '$endDate', $responsibleUserId)";
    } else {
        $sql = "INSERT INTO course (course_master_id, batch_number, details, application_fee, trainee_limit, place, begin_date, end_date, responsible_user_id, trainer_id, show_trainer_signature) "
            . " VALUES ($courseMasterId, $batchNumber, '$details', $applicationFee, $traineeLimit, '$place', '$beginDate', '$endDate', $responsibleUserId, $trainerId, $showTrainerSignature)";
    }

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

    /*ถ้าจะตรวจสอบสิทธิ์ตรงนี้ ต้องเพิ่มกรณี user นั้นเป็นผู้รับผิดชอบหลักสูตรด้วย*/

    /*ตรวจสอบสิทธิ์*/
    /*$sql = "SELECT service_type FROM course_master WHERE id = $courseMasterId";
    $result = $db->query($sql);
    $row = $result->fetch_assoc();
    switch ($row['service_type']) {
        case 'training':
            if (!checkPermission(PERMISSION_COURSE_TRAINING_UPDATE)) {
                return;
            }
            break;
        case 'social':
            if (!checkPermission(PERMISSION_COURSE_SOCIAL_UPDATE)) {
                return;
            }
            break;
        case 'driving_license':
            if (!checkPermission(PERMISSION_COURSE_DRIVING_LICENSE_UPDATE)) {
                return;
            }
            break;
    }*/

    $batchNumber = isset($_POST['batchNumber']) ? $db->real_escape_string($_POST['batchNumber']) : 'NULL';
    $applicationFee = isset($_POST['applicationFee']) ? $db->real_escape_string($_POST['applicationFee']) : 'NULL';
    $traineeLimit = $db->real_escape_string($_POST['traineeLimit']);
    $beginDate = getMySqlDateFormat($db->real_escape_string($_POST['beginDate']));
    $endDate = getMySqlDateFormat($db->real_escape_string($_POST['endDate']));
    $place = $db->real_escape_string($_POST['place']);
    $responsibleUserId = $db->real_escape_string($_POST['responsibleUserId']);
    $details = $db->real_escape_string($_POST['details']);

    $trainerId = null;
    $showTrainerSignature = 1;
    if (isset($_POST['trainerId'])) {
        $trainerId = $db->real_escape_string($_POST['trainerId']);
        $showTrainerSignature = isset($_POST['showTrainerSignature']) ? 1 : 0;
    }

    /*$output = sprintf(
        "File Names: %d\nCourse ID: %s\nCourse Master ID: %s\nBatch Number: %s\nFee: %s\nLimit: %s\nBegin: %s\nEnd: %s\nPlace: %s\nResponsible User ID: %s\nDetails: %s\n",
        sizeof($_FILES['imageFiles']['name']), $courseId, $courseMasterId, $batchNumber, $applicationFee, $traineeLimit, $beginDate, $endDate, $place, $responsibleUserId, $details
    );
    $response[KEY_ERROR_MESSAGE] = $output;
    return;*/

    $db->query('START TRANSACTION');

    if ($trainerId == null) {
        $sql = "UPDATE course SET course_master_id = $courseMasterId, batch_number = $batchNumber, details = '$details', application_fee = $applicationFee, "
            . " trainee_limit = $traineeLimit, place = '$place', begin_date = '$beginDate', end_date = '$endDate', responsible_user_id = $responsibleUserId "
            . " WHERE id = $courseId";
    } else {
        $sql = "UPDATE course SET course_master_id = $courseMasterId, batch_number = $batchNumber, details = '$details', application_fee = $applicationFee, "
            . " trainee_limit = $traineeLimit, place = '$place', begin_date = '$beginDate', end_date = '$endDate', responsible_user_id = $responsibleUserId, trainer_id = $trainerId, show_trainer_signature = $showTrainerSignature "
            . " WHERE id = $courseId";
    }

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

function doUpdateCourseStatus()
{
    global $db, $response;

    $courseId = $db->real_escape_string($_POST['courseId']);
    $newStatus = $db->real_escape_string($_POST['newStatus']);

    /*ถ้าจะตรวจสอบสิทธิ์ตรงนี้ ต้องเพิ่มกรณี user นั้นเป็นผู้รับผิดชอบหลักสูตรด้วย*/

    /*ตรวจสอบสิทธิ์*/
    /*$sql = "SELECT service_type FROM course_master WHERE id = $courseMasterId";
    $result = $db->query($sql);
    $row = $result->fetch_assoc();
    switch ($row['service_type']) {
        case 'training':
            if (!checkPermission(PERMISSION_COURSE_TRAINING_UPDATE)) {
                return;
            }
            break;
        case 'social':
            if (!checkPermission(PERMISSION_COURSE_SOCIAL_UPDATE)) {
                return;
            }
            break;
        case 'driving_license':
            if (!checkPermission(PERMISSION_COURSE_DRIVING_LICENSE_UPDATE)) {
                return;
            }
            break;
    }*/

    $sql = "UPDATE course SET status = '$newStatus' WHERE id = $courseId";
    if ($result = $db->query($sql)) {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SUCCESS;
        $response[KEY_ERROR_MESSAGE] = 'อัพเดทสถานะหลักสูตรสำเร็จ';
        $response[KEY_ERROR_MESSAGE_MORE] = '';
    } else {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
        $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $db->error;
        $errMessage = $db->error;
        $response[KEY_ERROR_MESSAGE_MORE] = "$errMessage\nSQL: $sql";
    }
}

function doDeleteCourseAsset()
{
    global $db, $response;

    $assetId = $db->real_escape_string($_POST['assetId']);

    $sql = "DELETE FROM course_asset WHERE id = $assetId";
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

function doDeleteNewsAsset()
{
    global $db, $response;

    $assetId = $db->real_escape_string($_POST['assetId']);

    $sql = "DELETE FROM news_asset WHERE id = $assetId";
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
        case SERVICE_TYPE_SOCIAL:
            $sql = "UPDATE course_registration_social 
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
        if ($newRegisterStatus === 'complete') {
            sendPaymentConfirmEmail($serviceType, $traineeId);
        }

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

function sendPaymentConfirmEmail($serviceType, $traineeId)
{
    global $db;

    $recipientList = array();
    $displayName = null;
    $formNumber = null;
    $displayCourseName = null;
    $displayCourseDate = null;
    $place = null;
    $paidAmount = null;
    $courseId = null;

    switch ($serviceType) {
        case SERVICE_TYPE_TRAINING:
            $sql = "SELECT cr.id AS reg_id, ct.form_number, cr.coordinator_email, ct.email AS trainee_email, m.email AS member_email, ct.first_name, ct.last_name, ct.paid_amount
                FROM course_registration cr 
                    INNER JOIN course_trainee ct 
                        ON ct.course_registration_id = cr.id 
                    LEFT JOIN member m 
                        ON cr.member_id = m.id 
                WHERE ct.id = $traineeId";
            if ($result = $db->query($sql)) {
                $row = $result->fetch_assoc();

                $coordinatorEmail = $row['coordinator_email'];
                $traineeEmail = $row['trainee_email'];
                $memberEmail = $row['member_email'];
                $displayName = "{$row['first_name']} {$row['last_name']}";
                $formNumber = $row['form_number'];
                $regId = $row['reg_id'];
                $paidAmount = number_format($row['paid_amount']);

                $result->close();

                $sql = "SELECT c.id, cm.title, c.batch_number, c.begin_date, c.end_date, c.place 
                        FROM course c 
                            INNER JOIN course_master cm 
                                ON cm.id = c.course_master_id 
                            INNER JOIN course_registration cr 
                                ON c.id = cr.course_id 
                        WHERE cr.id = $regId";
                if ($result = $db->query($sql)) {
                    if ($result->num_rows > 0) {
                        $row = $result->fetch_assoc();
                        $courseId = $row['id'];
                        $displayCourseName = "{$row['title']} รุ่นที่ {$row['batch_number']}";
                        $displayCourseDate = getThaiIntervalDate(date_create($row['begin_date']), date_create($row['end_date']));
                        $place = $row['place'];

                        $result->close();
                    } else {
                        $result->close();
                        return;
                    }
                } else {
                    return;
                }
            } else {
                // Error query
               return;
            }

            if (!is_null($coordinatorEmail)) {
                array_push($recipientList, $coordinatorEmail);
            } else if (!is_null($traineeEmail)) {
                array_push($recipientList, $traineeEmail);
            }
            if (!is_null($memberEmail) && !in_array($memberEmail, $recipientList)) {
                array_push($recipientList, $memberEmail);
            }
            break;

        case SERVICE_TYPE_SOCIAL:
            $sql = "SELECT cr.id AS reg_id, cr.form_number, cr.email AS trainee_email, m.email AS member_email, cr.first_name, cr.last_name, cr.paid_amount
                FROM course_registration_social cr 
                    LEFT JOIN member m 
                        ON cr.member_id = m.id 
                WHERE cr.id = $traineeId";
            if ($result = $db->query($sql)) {
                $row = $result->fetch_assoc();

                $coordinatorEmail = NULL;
                $traineeEmail = $row['trainee_email'];
                $memberEmail = $row['member_email'];
                $displayName = "{$row['first_name']} {$row['last_name']}";
                $formNumber = $row['form_number'];
                $regId = $row['reg_id'];
                $paidAmount = number_format($row['paid_amount']);

                $result->close();

                $sql = "SELECT c.id, cm.title, c.batch_number, c.begin_date, c.end_date, c.place 
                        FROM course c 
                            INNER JOIN course_master cm 
                                ON cm.id = c.course_master_id 
                            INNER JOIN course_registration_social cr 
                                ON c.id = cr.course_id 
                        WHERE cr.id = $regId";
                if ($result = $db->query($sql)) {
                    if ($result->num_rows > 0) {
                        $row = $result->fetch_assoc();
                        $courseId = $row['id'];
                        $displayCourseName = "{$row['title']} รุ่นที่ {$row['batch_number']}";
                        $displayCourseDate = getThaiIntervalDate(date_create($row['begin_date']), date_create($row['end_date']));
                        $place = $row['place'];

                        $result->close();
                    } else {
                        $result->close();
                        return;
                    }
                } else {
                    return;
                }
            } else {
                // Error query
                return;
            }

            if (!is_null($coordinatorEmail)) {
                array_push($recipientList, $coordinatorEmail);
            } else if (!is_null($traineeEmail)) {
                array_push($recipientList, $traineeEmail);
            }
            if (!is_null($memberEmail) && !in_array($memberEmail, $recipientList)) {
                array_push($recipientList, $memberEmail);
            }
            break;

        case SERVICE_TYPE_DRIVING_LICENSE:
            $sql = "SELECT cr.id AS reg_id, cr.form_number, m.email AS member_email, cr.first_name, cr.last_name, cr.paid_amount
                FROM course_registration_driving_license cr 
                    LEFT JOIN member m 
                        ON cr.member_id = m.id 
                WHERE cr.id = $traineeId";
            if ($result = $db->query($sql)) {
                $row = $result->fetch_assoc();

                $memberEmail = $row['member_email'];
                $displayName = "{$row['first_name']} {$row['last_name']}";
                $formNumber = $row['form_number'];
                $regId = $row['reg_id'];
                $paidAmount = number_format($row['paid_amount']);

                $result->close();

                $sql = "SELECT c.id, cm.title, c.batch_number, c.begin_date, c.end_date, c.place 
                        FROM course c 
                            INNER JOIN course_master cm 
                                ON cm.id = c.course_master_id 
                            INNER JOIN course_registration_driving_license cr 
                                ON c.id = cr.course_id 
                        WHERE cr.id = $regId";
                if ($result = $db->query($sql)) {
                    if ($result->num_rows > 0) {
                        $row = $result->fetch_assoc();
                        $courseId = $row['id'];
                        $displayCourseName = "{$row['title']}";
                        $displayCourseDate = getThaiIntervalDate(date_create($row['begin_date']), date_create($row['end_date']));
                        $place = $row['place'];

                        $result->close();
                    } else {
                        $result->close();
                        return;
                    }
                } else {
                    return;
                }
            } else {
                // Error query
                return;
            }

            if (!is_null($memberEmail)) {
                array_push($recipientList, $memberEmail);
            }
            break;
    }

    $hostFrontEnd = HOST_FRONTEND;

    $message = "สถาบันฯ ได้รับค่าสมัครอบรมของใบสม้ครเลขที่ $formNumber จำนวน $paidAmount บาท เรียบร้อยแล้ว\n\n";
    $message .= "----------\n";
    $message .= "ชื่อผู้สมัคร: $displayName\n";
    $message .= "หลักสูตรที่สมัคร: $displayCourseName\n";
    $message .= "วันที่อบรม: $displayCourseDate\n";
    $message .= "สถานที่อบรม: $place\n";
    $message .= "หน้าเว็บของหลักสูตร: {$hostFrontEnd}/service-{$serviceType}/{$courseId}\n";
    $message .= "----------\n";
    $message .= "\n\nสถาบันเสริมศึกษาและทรัพยากรมนุษย์ มหาวิทยาลัยธรรมศาสตร์\n";
    $message .= "http://www.icehr.tu.ac.th/\n";
    $message .= "โทร. 02-613-3820-3\n";
    sendMailNoAttachment($recipientList, 'สถานะการลงทะเบียนสมบูรณ์', $message);
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

function doGetNameTitle()
{
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

function doUpdateInHouse()
{
    global $db, $response;

    $id = $db->real_escape_string($_POST['id']);
    $title = $db->real_escape_string($_POST['title']);
    $firstName = $db->real_escape_string($_POST['firstName']);
    $lastName = $db->real_escape_string($_POST['lastName']);
    $phone = $db->real_escape_string($_POST['phone']);
    $email = $db->real_escape_string($_POST['email']);
    $organizationName = $db->real_escape_string($_POST['organizationName']);
    $course = $db->real_escape_string($_POST['course']);
    $numDay = $db->real_escape_string($_POST['numDay']);
    $numTrainee = $db->real_escape_string($_POST['numTrainee']);
    $place = $db->real_escape_string($_POST['place']);
    $remark = $db->real_escape_string($_POST['remark']);

    $sql = "UPDATE in_house 
                SET title = '$title', first_name = '$firstName', last_name = '$lastName', 
                    phone = '$phone', email = '$email', organization_name = '$organizationName', 
                    course = '$course', num_day = $numDay, num_trainee = $numTrainee, 
                    place = '$place', remark = '$remark'
                WHERE id = $id ";

    if ($result = $db->query($sql)) {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SUCCESS;
        $response[KEY_ERROR_MESSAGE] = 'อัพเดทข้อมูลสำเร็จ';
        $response[KEY_ERROR_MESSAGE_MORE] = '';
    } else {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
        $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการอัพเดทข้อมูล';
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

function doDeleteInHouse()
{
    global $db, $response;

    $id = $db->real_escape_string($_POST['id']);

    $sql = "DELETE FROM in_house WHERE id = $id";

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

function doAddDocumentDownload()
{
    global $db, $response;

    $documentType = $db->real_escape_string($_POST['document_type']);
    $title = $db->real_escape_string($_POST['title']);
    $shortDescription = $db->real_escape_string($_POST['short_description']);

    if (!moveUploadedFile('file', UPLOAD_DIR_DOCUMENT_DOWNLOADS, $fileName)) {
        $response[KEY_ERROR_CODE] = ERROR_CODE_ERROR;
        $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการอัพโหลดไฟล์ (PDF)';
        $response[KEY_ERROR_MESSAGE_MORE] = '';
        return;
    }

    if ($_FILES['image_file']) {
        if (!moveUploadedFile('image_file', UPLOAD_DIR_DOCUMENT_DOWNLOADS, $imageFileName)) {
            $response[KEY_ERROR_CODE] = ERROR_CODE_ERROR;
            $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการอัพโหลดไฟล์ (รูปภาพ)';
            $response[KEY_ERROR_MESSAGE_MORE] = '';
            return;
        }
    }

    $sql = "INSERT INTO document_download (title, short_description, file_name, image_file_name, document_type) 
                VALUES ('$title', '$shortDescription', '$fileName', '$imageFileName', '$documentType')";
    if ($result = $db->query($sql)) {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SUCCESS;
        $response[KEY_ERROR_MESSAGE] = 'เพิ่มเอกสารดาวน์โหลดสำเร็จ';
        $response[KEY_ERROR_MESSAGE_MORE] = '';
    } else {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
        $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการเพิ่มเอกสารดาวน์โหลด: ' . $db->error;
        $errMessage = $db->error;
        $response[KEY_ERROR_MESSAGE_MORE] = "$errMessage\nSQL: $sql";
    }
}

function doAddUser()
{
    global $db, $response;

    if (!checkPermission(PERMISSION_USER_CREATE)) {
        return;
    }

    $username = $db->real_escape_string($_POST['username']);

    $sql = "SELECT COUNT(username) username_count FROM user WHERE username = '$username'";
    if ($result = $db->query($sql)) {
        $row = $result->fetch_assoc();
        if ((int)$row['username_count'] > 0) {
            $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
            $response[KEY_ERROR_MESSAGE] = "ไม่สามารถเพิ่มผู้ใช้งานได้ เนื่องจากมีชื่อ Username '$username' ในระบบแล้ว";
            $response[KEY_ERROR_MESSAGE_MORE] = '';
            $result->close();
            return;
        }
        $result->close();
    } else {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
        $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการเพิ่มผู้ใช้งานระบบ (1): ' . $db->error;
        $errMessage = $db->error;
        $response[KEY_ERROR_MESSAGE_MORE] = "$errMessage\nSQL: $sql";
        return;
    }

    $password = $db->real_escape_string(MD5($_POST['password']));

    $title = $db->real_escape_string($_POST['title']);
    $firstName = $db->real_escape_string($_POST['firstName']);
    $lastName = $db->real_escape_string($_POST['lastName']);

    $position = $db->real_escape_string($_POST['position']);
    $department = $db->real_escape_string($_POST['department']);

    $email = $db->real_escape_string($_POST['email']);
    $phone = $db->real_escape_string($_POST['phone']);
    $phoneOffice = $db->real_escape_string($_POST['phoneOffice']);
    $phoneExtension = $db->real_escape_string($_POST['phoneExtension']);

    $showDetails = isset($_POST['showDetails']) ? 1 : 0;
    $details = isset($_POST['details']) ? $db->real_escape_string($_POST['details']) : '';

    $permissions = getPermissionValuesFromPost();

    $sql = "SELECT MAX(sort_index) AS max_sort_index FROM user WHERE department_id = $department";
    if ($result = $db->query($sql)) {
        $row = $result->fetch_assoc();
        $nextSortIndex = 1;
        if ($row['max_sort_index'] != null) {
            $nextSortIndex = (int)$row['max_sort_index'] + 1;
        }
        $result->close();

        if ($_FILES['imageFile']) {
            if (!moveUploadedFile('imageFile', UPLOAD_DIR_USER_ASSETS, $imageFileName)) {
                $response[KEY_ERROR_CODE] = ERROR_CODE_ERROR;
                $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการอัพโหลดไฟล์รูปภาพ';
                $response[KEY_ERROR_MESSAGE_MORE] = '';
                return;
            }
        }

        $sql = "INSERT INTO user (username, password, title, first_name, last_name, position, department_id, sort_index,
                              email, phone, phone_office, phone_extension, show_details, details, image_file_name, permissions) 
                VALUES ('$username', '$password', '$title', '$firstName', '$lastName', '$position', $department, $nextSortIndex, 
                        '$email', '$phone', '$phoneOffice', '$phoneExtension', $showDetails, '$details', '$imageFileName', $permissions)";
        if ($result = $db->query($sql)) {
            $response[KEY_ERROR_CODE] = ERROR_CODE_SUCCESS;
            $response[KEY_ERROR_MESSAGE] = 'เพิ่มผู้ใช้งานระบบสำเร็จ';
            $response[KEY_ERROR_MESSAGE_MORE] = '';
        } else {
            $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
            $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการเพิ่มผู้ใช้งานระบบ (3): ' . $db->error;
            $errMessage = $db->error;
            $response[KEY_ERROR_MESSAGE_MORE] = "$errMessage\nSQL: $sql";
        }
    } else {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
        $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการเพิ่มผู้ใช้งานระบบ (2): ' . $db->error;
        $errMessage = $db->error;
        $response[KEY_ERROR_MESSAGE_MORE] = "$errMessage\nSQL: $sql";
    }
}

function getPermissionValuesFromPost()
{
    $permissions = 0;
    $permissions += pow(2, PERMISSION_USER_CREATE) * (isset($_POST['permissionUserCreate']) ? 1 : 0);
    $permissions += pow(2, PERMISSION_USER_UPDATE) * (isset($_POST['permissionUserUpdate']) ? 1 : 0);
    $permissions += pow(2, PERMISSION_USER_DELETE) * (isset($_POST['permissionUserDelete']) ? 1 : 0);
    $permissions += pow(2, PERMISSION_USER_DEPARTMENT_MANAGE) * (isset($_POST['permissionUserDepartmentManage']) ? 1 : 0);

    $permissions += pow(2, PERMISSION_COURSE_TRAINING_CREATE) * (isset($_POST['permissionCourseTrainingCreate']) ? 1 : 0);
    $permissions += pow(2, PERMISSION_COURSE_TRAINING_UPDATE) * (isset($_POST['permissionCourseTrainingUpdate']) ? 1 : 0);
    $permissions += pow(2, PERMISSION_COURSE_TRAINING_DELETE) * (isset($_POST['permissionCourseTrainingDelete']) ? 1 : 0);
    $permissions += pow(2, PERMISSION_COURSE_TRAINING_MANAGE_REGISTRATION) * (isset($_POST['permissionCourseTrainingManageRegistration']) ? 1 : 0);
    $permissions += pow(2, PERMISSION_COURSE_TRAINING_MANAGE_COURSE_MASTER) * (isset($_POST['permissionCourseTrainingManageCourseMaster']) ? 1 : 0);
    $permissions += pow(2, PERMISSION_COURSE_TRAINING_MANAGE_CATEGORY) * (isset($_POST['permissionCourseTrainingManageCategory']) ? 1 : 0);

    $permissions += pow(2, PERMISSION_COURSE_SOCIAL_CREATE) * (isset($_POST['permissionCourseSocialCreate']) ? 1 : 0);
    $permissions += pow(2, PERMISSION_COURSE_SOCIAL_UPDATE) * (isset($_POST['permissionCourseSocialUpdate']) ? 1 : 0);
    $permissions += pow(2, PERMISSION_COURSE_SOCIAL_DELETE) * (isset($_POST['permissionCourseSocialDelete']) ? 1 : 0);
    $permissions += pow(2, PERMISSION_COURSE_SOCIAL_MANAGE_REGISTRATION) * (isset($_POST['permissionCourseSocialManageRegistration']) ? 1 : 0);
    $permissions += pow(2, PERMISSION_COURSE_SOCIAL_MANAGE_COURSE_MASTER) * (isset($_POST['permissionCourseSocialManageCourseMaster']) ? 1 : 0);

    $permissions += pow(2, PERMISSION_COURSE_DRIVING_LICENSE_CREATE) * (isset($_POST['permissionCourseDrivingLicenseCreate']) ? 1 : 0);
    $permissions += pow(2, PERMISSION_COURSE_DRIVING_LICENSE_UPDATE) * (isset($_POST['permissionCourseDrivingLicenseUpdate']) ? 1 : 0);
    $permissions += pow(2, PERMISSION_COURSE_DRIVING_LICENSE_DELETE) * (isset($_POST['permissionCourseDrivingLicenseDelete']) ? 1 : 0);
    $permissions += pow(2, PERMISSION_COURSE_DRIVING_LICENSE_MANAGE_REGISTRATION) * (isset($_POST['permissionCourseDrivingLicenseManageRegistration']) ? 1 : 0);
    $permissions += pow(2, PERMISSION_COURSE_DRIVING_LICENSE_MANAGE_COURSE_MASTER) * (isset($_POST['permissionCourseDrivingLicenseManageCourseMaster']) ? 1 : 0);

    $permissions += pow(2, PERMISSION_MANAGE_WEB_CONTENT) * (isset($_POST['permissionManageWebContent']) ? 1 : 0);

    return $permissions;
}

function checkPermission($permission, $errorMessage = 'คุณไม่มีสิทธิ์สำหรับการดำเนินการนี้')
{
    global $response;

    if (currentUserHasPermission($permission)) {
        return true;
    } else {
        $response[KEY_ERROR_CODE] = ERROR_CODE_ERROR;
        $response[KEY_ERROR_MESSAGE] = $errorMessage;
        $response[KEY_ERROR_MESSAGE_MORE] = '';
        return false;
    }
}

function doUpdateUser()
{
    global $db, $response;

    if (!checkPermission(PERMISSION_USER_UPDATE)) {
        return;
    }

    $userId = $db->real_escape_string($_POST['userId']);
    $departmentId = $db->real_escape_string($_POST['department']);

    $sql = "SELECT department_id, sort_index, image_file_name FROM user WHERE id = $userId";
    if ($result = $db->query($sql)) {
        $row = $result->fetch_assoc();
        $result->close();

        $oldDepartmentId = $row['department_id'];
        $sortIndex = $row['sort_index'];
        $imageFileName = $row['image_file_name'];

        if ($departmentId != $oldDepartmentId) {
            //มีการแก้ไขข้อมูล "ฝ่าย" ดังนั้นต้องกำหนด sort_index ใหม่!
            $sql = "SELECT MAX(sort_index) AS max_sort_index FROM user WHERE department_id = $departmentId";
            if ($result = $db->query($sql)) {
                $row = $result->fetch_assoc();
                $result->close();

                $sortIndex = 1;
                if ($row['max_sort_index'] != null) {
                    $sortIndex = (int)$row['max_sort_index'] + 1;
                }
            } else {
                $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
                $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการแก้ไขข้อมูลผู้ใช้งานระบบ (2): ' . $db->error;
                $errMessage = $db->error;
                $response[KEY_ERROR_MESSAGE_MORE] = "$errMessage\nSQL: $sql";
                return;
            }
        }
    } else {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
        $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการแก้ไขข้อมูลผู้ใช้งานระบบ (1): ' . $db->error;
        $errMessage = $db->error;
        $response[KEY_ERROR_MESSAGE_MORE] = "$errMessage\nSQL: $sql";
        return;
    }

    $title = $db->real_escape_string($_POST['title']);
    $firstName = $db->real_escape_string($_POST['firstName']);
    $lastName = $db->real_escape_string($_POST['lastName']);

    $position = $db->real_escape_string($_POST['position']);

    $email = $db->real_escape_string($_POST['email']);
    $phone = $db->real_escape_string($_POST['phone']);
    $phoneOffice = $db->real_escape_string($_POST['phoneOffice']);
    $phoneExtension = $db->real_escape_string($_POST['phoneExtension']);

    $showDetails = isset($_POST['showDetails']) ? 1 : 0;
    $details = isset($_POST['details']) ? $db->real_escape_string($_POST['details']) : '';

    $permissions = getPermissionValuesFromPost();

    if ($_FILES['imageFile']) {
        if (!moveUploadedFile('imageFile', UPLOAD_DIR_USER_ASSETS, $imageFileName)) {
            $response[KEY_ERROR_CODE] = ERROR_CODE_ERROR;
            $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการอัพโหลดไฟล์รูปภาพ';
            $response[KEY_ERROR_MESSAGE_MORE] = '';
            return;
        }
    }

    $sql = "UPDATE user SET title = '$title', first_name = '$firstName', last_name = '$lastName', position = '$position', department_id = $departmentId, 
                sort_index = $sortIndex, email = '$email', phone = '$phone', phone_office = '$phoneOffice', phone_extension = '$phoneExtension', 
                show_details = $showDetails, details = '$details', image_file_name = '$imageFileName', permissions = $permissions
                WHERE id = $userId";
    if ($result = $db->query($sql)) {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SUCCESS;
        $response[KEY_ERROR_MESSAGE] = 'อัพเดทข้อมูลผู้ใช้งานระบบสำเร็จ';
        $response[KEY_ERROR_MESSAGE_MORE] = '';
    } else {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
        $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการแก้ไขข้อมูลผู้ใช้งานระบบ (3): ' . $db->error;
        $errMessage = $db->error;
        $response[KEY_ERROR_MESSAGE_MORE] = "$errMessage\nSQL: $sql";
    }
}

function doUpdateUserStatus()
{
    global $db, $response;

    $userId = $db->real_escape_string($_POST['userId']);
    $newStatus = $db->real_escape_string($_POST['newStatus']);

    if ($newStatus === 'deleted') {
        if (!checkPermission(PERMISSION_USER_DELETE)) {
            return;
        }
    } else if (!checkPermission(PERMISSION_USER_UPDATE)) {
        return;
    }

    $sql = "UPDATE user SET status = '$newStatus' WHERE id = $userId";
    if ($result = $db->query($sql)) {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SUCCESS;
        $response[KEY_ERROR_MESSAGE] = 'อัพเดทสถานะผู้ใช้งานสำเร็จ';
        $response[KEY_ERROR_MESSAGE_MORE] = '';
    } else {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
        $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $db->error;
        $errMessage = $db->error;
        $response[KEY_ERROR_MESSAGE_MORE] = "$errMessage\nSQL: $sql";
    }
}

function doAddTrainer()
{
    global $db, $response;

    if (!checkPermission(PERMISSION_COURSE_DRIVING_LICENSE_MANAGE_COURSE_MASTER)) {
        return;
    }

    $title = $db->real_escape_string($_POST['title']);
    $firstName = $db->real_escape_string($_POST['firstName']);
    $lastName = $db->real_escape_string($_POST['lastName']);

    $pid = $db->real_escape_string($_POST['pid']);
    $email = $db->real_escape_string($_POST['email']);
    $phone = $db->real_escape_string($_POST['phone']);

    $imageFileName = null;

    if ($_FILES['signatureImage']) {
        if (!moveUploadedFile('signatureImage', UPLOAD_DIR_SIGNATURES, $imageFileName)) {
            $response[KEY_ERROR_CODE] = ERROR_CODE_ERROR;
            $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการอัพโหลดไฟล์รูปภาพ';
            $response[KEY_ERROR_MESSAGE_MORE] = '';
            return;
        }
    }

    $sql = "INSERT INTO trainer (title, first_name, last_name, pid, email, phone, signature_image) 
                VALUES ('$title', '$firstName', '$lastName', '$pid', '$email', '$phone', '$imageFileName')";
    if ($result = $db->query($sql)) {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SUCCESS;
        $response[KEY_ERROR_MESSAGE] = 'เพิ่มวิทยากรสำเร็จ';
        $response[KEY_ERROR_MESSAGE_MORE] = '';
    } else {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
        $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $db->error;
        $errMessage = $db->error;
        $response[KEY_ERROR_MESSAGE_MORE] = "$errMessage\nSQL: $sql";
    }
}

function doUpdateTrainer()
{
    global $db, $response;

    if (!checkPermission(PERMISSION_COURSE_DRIVING_LICENSE_MANAGE_COURSE_MASTER)) {
        return;
    }

    $trainerId = $db->real_escape_string($_POST['trainerId']);

    $title = $db->real_escape_string($_POST['title']);
    $firstName = $db->real_escape_string($_POST['firstName']);
    $lastName = $db->real_escape_string($_POST['lastName']);

    $pid = $db->real_escape_string($_POST['pid']);
    $email = $db->real_escape_string($_POST['email']);
    $phone = $db->real_escape_string($_POST['phone']);

    $imageFileName = null;

    if ($_FILES['signatureImage']) {
        if (!moveUploadedFile('signatureImage', UPLOAD_DIR_SIGNATURES, $imageFileName)) {
            $response[KEY_ERROR_CODE] = ERROR_CODE_ERROR;
            $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการอัพโหลดไฟล์รูปภาพ';
            $response[KEY_ERROR_MESSAGE_MORE] = '';
            return;
        }

        $sql = "UPDATE trainer SET title = '$title', first_name = '$firstName', last_name = '$lastName',  
                pid = '$pid', email = '$email', phone = '$phone', signature_image = '$imageFileName'
                WHERE id = $trainerId";
    } else {
        $sql = "UPDATE trainer SET title = '$title', first_name = '$firstName', last_name = '$lastName',  
                pid = '$pid', email = '$email', phone = '$phone'
                WHERE id = $trainerId";

        // อ่านชื่อไฟล์รูปภาพเดิม
        /*$sql = "SELECT signature_image FROM trainer WHERE id = $trainerId";
        if ($result = $db->query($sql)) {
            $row = $result->fetch_assoc();
            $imageFileName = $row['signature_image'];
            $result->close();
        } else {
            $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
            $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการบันทึกข้อมูล (1): ' . $db->error;
            $errMessage = $db->error;
            $response[KEY_ERROR_MESSAGE_MORE] = "$errMessage\nSQL: $sql";
            return;
        }*/
    }

    if ($result = $db->query($sql)) {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SUCCESS;
        $response[KEY_ERROR_MESSAGE] = 'อัพเดทข้อมูลวิทยากรสำเร็จ';
        $response[KEY_ERROR_MESSAGE_MORE] = '';
    } else {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
        $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการบันทึกข้อมูล (2): ' . $db->error;
        $errMessage = $db->error;
        $response[KEY_ERROR_MESSAGE_MORE] = "$errMessage\nSQL: $sql";
    }
}

function doUpdateTrainerStatus()
{
    global $db, $response;

    $trainerId = $db->real_escape_string($_POST['trainerId']);
    $newStatus = $db->real_escape_string($_POST['newStatus']);

    if ($newStatus === 'deleted') {
        if (!checkPermission(PERMISSION_COURSE_DRIVING_LICENSE_MANAGE_COURSE_MASTER)) {
            return;
        }
    } else if (!checkPermission(PERMISSION_COURSE_DRIVING_LICENSE_MANAGE_COURSE_MASTER)) {
        return;
    }

    $sql = "UPDATE trainer SET status = '$newStatus' WHERE id = $trainerId";
    if ($result = $db->query($sql)) {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SUCCESS;
        $response[KEY_ERROR_MESSAGE] = 'อัพเดทสถานะวิทยากรสำเร็จ';
        $response[KEY_ERROR_MESSAGE_MORE] = '';
    } else {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
        $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $db->error;
        $errMessage = $db->error;
        $response[KEY_ERROR_MESSAGE_MORE] = "$errMessage\nSQL: $sql";
    }
}

function doAddNews()
{
    global $db, $response;

    $newsType = $db->real_escape_string($_POST['newsType']);
    $title = $db->real_escape_string($_POST['title']);
    $shortDescription = $db->real_escape_string($_POST['shortDescription']);
    $details = $db->real_escape_string($_POST['details']);
    $newsDate = getMySqlDateFormat($db->real_escape_string($_POST['newsDate']));

    if (!moveUploadedFile('coverImageFile', UPLOAD_DIR_NEWS_ASSETS, $coverImageFileName)) {
        $response[KEY_ERROR_CODE] = ERROR_CODE_ERROR;
        $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการอัพโหลดไฟล์ (รูปภาพ)';
        $response[KEY_ERROR_MESSAGE_MORE] = '';
        return;
    }

    $db->query('START TRANSACTION');

    $sql = "INSERT INTO news (title, short_description, details, image_file_name, news_type, news_date) 
                VALUES ('$title', '$shortDescription', '$details', '$coverImageFileName', '$newsType', '$newsDate')";
    if ($result = $db->query($sql)) {
        $insertId = $db->insert_id;

        for ($i = 0; $i < sizeof($_FILES[KEY_IMAGE_FILES]['name']); $i++) {
            $fileName = null;

            if (!moveUploadedFile(KEY_IMAGE_FILES, UPLOAD_DIR_NEWS_ASSETS, $fileName, $i)) {
                $db->query('ROLLBACK');

                $response[KEY_ERROR_CODE] = ERROR_CODE_ERROR;
                $errorValue = $_FILES[KEY_IMAGE_FILES]['error'][$i];
                $response[KEY_ERROR_MESSAGE] = "เกิดข้อผิดพลาดในการอัพโหลดรูปภาพ [Error: $errorValue]";
                $response[KEY_ERROR_MESSAGE_MORE] = '';
                return;
            }

            $sql = "INSERT INTO news_asset (news_id, title, file_name) 
                    VALUES ($insertId, null, '$fileName')";
            if (!($insertCourseAssetResult = $db->query($sql))) {
                $db->query('ROLLBACK');

                $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
                $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการบันทึกข้อมูลรูปภาพ: ' . $db->error;
                $response[KEY_ERROR_MESSAGE_MORE] = '';
                return;
            }

            $response[KEY_ERROR_CODE] = ERROR_CODE_SUCCESS;
            $response[KEY_ERROR_MESSAGE] = 'เพิ่มข้อมูลสำเร็จ';
            $response[KEY_ERROR_MESSAGE_MORE] = '';

            $db->query('COMMIT');
        }
    } else {
        $db->query('ROLLBACK');

        $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
        $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการเพิ่มข้อมูล: ' . $db->error;
        $errMessage = $db->error;
        $response[KEY_ERROR_MESSAGE_MORE] = "$errMessage\nSQL: $sql";
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

function doUpdateDocumentDownload()
{
    global $db, $response;

    $id = $db->real_escape_string($_POST['id']);
    $title = $db->real_escape_string($_POST['title']);
    $shortDescription = $db->real_escape_string($_POST['short_description']);

    $fileName = NULL;
    $imageFileName = NULL;

    if ($_FILES['file']) {
        if (!moveUploadedFile('file', UPLOAD_DIR_DOCUMENT_DOWNLOADS, $fileName)) {
            $response[KEY_ERROR_CODE] = ERROR_CODE_ERROR;
            $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการอัพโหลดไฟล์ (PDF)';
            $response[KEY_ERROR_MESSAGE_MORE] = '';
            return;
        }
    }
    if ($_FILES['image_file']) {
        if (!moveUploadedFile('image_file', UPLOAD_DIR_DOCUMENT_DOWNLOADS, $imageFileName)) {
            $response[KEY_ERROR_CODE] = ERROR_CODE_ERROR;
            $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการอัพโหลดไฟล์ (รูปภาพ)';
            $response[KEY_ERROR_MESSAGE_MORE] = '';
            return;
        }
    }

    $setUploadFileName = $fileName ? "file_name = '$fileName', " : '';
    $setUploadFileName .= $imageFileName ? "image_file_name = '$imageFileName', " : '';

    $sql = "UPDATE document_download 
            SET $setUploadFileName title = '$title', short_description = '$shortDescription' 
            WHERE id = $id";
    if ($result = $db->query($sql)) {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SUCCESS;
        $response[KEY_ERROR_MESSAGE] = 'แก้ไขเอกสารดาวน์โหลดสำเร็จ';
        $response[KEY_ERROR_MESSAGE_MORE] = '';
    } else {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
        $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการแก้ไขเอกสารดาวน์โหลด: ' . $db->error;
        $errMessage = $db->error;
        $response[KEY_ERROR_MESSAGE_MORE] = "$errMessage\nSQL: $sql";
    }
}

function doUpdateDocumentDownloadStatus()
{
    global $db, $response;

    $documentDownloadId = $db->real_escape_string($_POST['documentDownloadId']);
    $newStatus = $db->real_escape_string($_POST['newStatus']);

    /*if ($newStatus === 'deleted') {
        if (!checkPermission(PERMISSION_USER_DELETE)) {
            return;
        }
    } else if (!checkPermission(PERMISSION_USER_UPDATE)) {
        return;
    }*/

    $sql = "UPDATE document_download SET status = '$newStatus' WHERE id = $documentDownloadId";
    if ($result = $db->query($sql)) {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SUCCESS;
        $response[KEY_ERROR_MESSAGE] = 'อัพเดทสถานะเอกสารดาวน์โหลดสำเร็จ';
        $response[KEY_ERROR_MESSAGE_MORE] = '';
    } else {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
        $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $db->error;
        $errMessage = $db->error;
        $response[KEY_ERROR_MESSAGE_MORE] = "$errMessage\nSQL: $sql";
    }
}

function doUpdateNews()
{
    global $db, $response;

    $id = $db->real_escape_string($_POST['newsId']);
    $title = $db->real_escape_string($_POST['title']);
    $shortDescription = $db->real_escape_string($_POST['shortDescription']);
    $details = $db->real_escape_string($_POST['details']);
    $newsDate = getMySqlDateFormat($db->real_escape_string($_POST['newsDate']));

    $coverImageFileName = NULL;

    if ($_FILES['coverImageFile']) {
        if (!moveUploadedFile('coverImageFile', UPLOAD_DIR_NEWS_ASSETS, $coverImageFileName)) {
            $response[KEY_ERROR_CODE] = ERROR_CODE_ERROR;
            $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการอัพโหลดไฟล์ (รูปภาพ)';
            $response[KEY_ERROR_MESSAGE_MORE] = '';
            return;
        }
    }

    $setUploadFileName = $coverImageFileName ? "image_file_name = '$coverImageFileName', " : '';

    $db->query('START TRANSACTION');

    $sql = "UPDATE news 
            SET $setUploadFileName title = '$title', short_description = '$shortDescription', details = '$details', news_date = '$newsDate' 
            WHERE id = $id";
    if ($result = $db->query($sql)) {
        for ($i = 0; $i < sizeof($_FILES[KEY_IMAGE_FILES]['name']); $i++) {
            $fileName = null;

            if (!moveUploadedFile(KEY_IMAGE_FILES, UPLOAD_DIR_NEWS_ASSETS, $fileName, $i)) {
                $response[KEY_ERROR_CODE] = ERROR_CODE_ERROR;
                $errorValue = $_FILES[KEY_IMAGE_FILES]['error'][$i];
                $response[KEY_ERROR_MESSAGE] = "เกิดข้อผิดพลาดในการอัพโหลดรูปภาพ [Error: $errorValue]";
                $response[KEY_ERROR_MESSAGE_MORE] = '';

                $db->query('ROLLBACK');
                return;
            }

            $sql = "INSERT INTO news_asset (news_id, title, file_name) 
                    VALUES ($id, null, '$fileName')";
            if (!($insertCourseAssetResult = $db->query($sql))) {
                $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
                $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการบันทึกข้อมูลรูปภาพ';
                $response[KEY_ERROR_MESSAGE_MORE] = '';

                $db->query('ROLLBACK');
                return;
            }
        }

        $response[KEY_ERROR_CODE] = ERROR_CODE_SUCCESS;
        $response[KEY_ERROR_MESSAGE] = 'แก้ไขข้อมูลสำเร็จ';
        $response[KEY_ERROR_MESSAGE_MORE] = '';

        $db->query('COMMIT');
    } else {
        $db->query('ROLLBACK');

        $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
        $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการแก้ไขข้อมูล: ' . $db->error;
        $errMessage = $db->error;
        $response[KEY_ERROR_MESSAGE_MORE] = "$errMessage\nSQL: $sql";
    }
}

function doUpdateNewsStatus()
{
    global $db, $response;

    $newsId = $db->real_escape_string($_POST['newsId']);
    $newStatus = $db->real_escape_string($_POST['newStatus']);

    /*if ($newStatus === 'deleted') {
        if (!checkPermission(PERMISSION_USER_DELETE)) {
            return;
        }
    } else if (!checkPermission(PERMISSION_USER_UPDATE)) {
        return;
    }*/

    $sql = "UPDATE news SET status = '$newStatus' WHERE id = $newsId";
    if ($result = $db->query($sql)) {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SUCCESS;
        $response[KEY_ERROR_MESSAGE] = 'อัพเดทสถานะข่าวสำเร็จ';
        $response[KEY_ERROR_MESSAGE_MORE] = '';
    } else {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
        $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $db->error;
        $errMessage = $db->error;
        $response[KEY_ERROR_MESSAGE_MORE] = "$errMessage\nSQL: $sql";
    }
}

function doDeleteAcademicPaper()
{
    global $db, $response;

    $id = $db->real_escape_string($_POST['id']);

    $sql = "SELECT file_name FROM academic_paper WHERE id = $id";
    if ($result = $db->query($sql)) {
        if ($result > 0) {
            $row = $result->fetch_assoc();
            $fileName = $row['file_name'];

            $deleteSql = "DELETE FROM academic_paper WHERE id = $id";
            if ($deleteResult = $db->query($deleteSql)) {
                unlink(UPLOAD_DIR_ACADEMIC_PAPERS . $fileName);

                $response[KEY_ERROR_CODE] = ERROR_CODE_SUCCESS;
                $response[KEY_ERROR_MESSAGE] = 'ลบงานวิจัย/วิชาการสำเร็จ';
                $response[KEY_ERROR_MESSAGE_MORE] = '';
            } else {
                $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
                $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการลบงานวิจัย/วิชาการ: ' . $db->error;
                $errMessage = $db->error;
                $response[KEY_ERROR_MESSAGE_MORE] = "$errMessage\nSQL: $deleteSql";
            }
        } else {
            $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
            $response[KEY_ERROR_MESSAGE] = 'ไม่พบข้อมูล!';
            $response[KEY_ERROR_MESSAGE_MORE] = '';
        }
        $result->close();
    } else {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
        $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการเข้าถึงฐานข้อมูล: ' . $db->error;
        $errMessage = $db->error;
        $response[KEY_ERROR_MESSAGE_MORE] = "$errMessage\nSQL: $sql";
    }
}

function doDeleteDocumentDownload()
{
    global $db, $response;

    $id = $db->real_escape_string($_POST['id']);

    $sql = "SELECT file_name FROM document_download WHERE id = $id";
    if ($result = $db->query($sql)) {
        if ($result > 0) {
            $row = $result->fetch_assoc();
            $fileName = $row['file_name'];

            $deleteSql = "DELETE FROM document_download WHERE id = $id";
            if ($deleteResult = $db->query($deleteSql)) {
                unlink(UPLOAD_DIR_DOCUMENT_DOWNLOADS . $fileName);

                $response[KEY_ERROR_CODE] = ERROR_CODE_SUCCESS;
                $response[KEY_ERROR_MESSAGE] = 'ลบเอกสารดาวน์โหลดสำเร็จ';
                $response[KEY_ERROR_MESSAGE_MORE] = '';
            } else {
                $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
                $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการลบเอกสารดาวน์โหลด: ' . $db->error;
                $errMessage = $db->error;
                $response[KEY_ERROR_MESSAGE_MORE] = "$errMessage\nSQL: $deleteSql";
            }
        } else {
            $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
            $response[KEY_ERROR_MESSAGE] = 'ไม่พบข้อมูล!';
            $response[KEY_ERROR_MESSAGE_MORE] = '';
        }
        $result->close();
    } else {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
        $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการเข้าถึงฐานข้อมูล: ' . $db->error;
        $errMessage = $db->error;
        $response[KEY_ERROR_MESSAGE_MORE] = "$errMessage\nSQL: $sql";
    }
}

function doDeleteNews()
{
    global $db, $response;

    $id = $db->real_escape_string($_POST['id']);

    $deleteNewsSql = "DELETE FROM news WHERE id = $id";

    if ($deleteResult = $db->query($deleteNewsSql)) {
        $deleteNewsAssetsSql = "DELETE FROM news_asset WHERE news_id = $id";

        if ($deleteNewsAssetsResult = $db->query($deleteNewsAssetsSql)) {
            $response[KEY_ERROR_CODE] = ERROR_CODE_SUCCESS;
            $response[KEY_ERROR_MESSAGE] = 'ลบข้อมูลสำเร็จ';
            $response[KEY_ERROR_MESSAGE_MORE] = '';
        } else {
            $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
            $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการลบข้อมูล (2): ' . $db->error;
            $errMessage = $db->error;
            $response[KEY_ERROR_MESSAGE_MORE] = "$errMessage\nSQL: $deleteNewsAssetsSql";
        }
    } else {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
        $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการลบข้อมูล (1): ' . $db->error;
        $errMessage = $db->error;
        $response[KEY_ERROR_MESSAGE_MORE] = "$errMessage\nSQL: $deleteNewsSql";
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
        /*$response[KEY_ERROR_CODE] = ERROR_CODE_SUCCESS;
        $response[KEY_ERROR_MESSAGE] = 'บันทึกผลการอบรมสำเร็จ';
        $response[KEY_ERROR_MESSAGE_MORE] = '';*/

        generateCertificateNumber($traineeId);
    } else {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
        $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการบันทึกผลการอบรม';
        $errMessage = $db->error;
        $response[KEY_ERROR_MESSAGE_MORE] = "$errMessage\nSQL: $sql";
    }
}

function generateCertificateNumber($traineeId)
{
    global $db, $response;

    $sql = "SELECT cr.course_type, cr.subject_1_result, cr.subject_2_result, cr.subject_3_result, cr.subject_4_result,
                   cr.certificate_number, YEAR(c.begin_date) AS course_year
            FROM course_registration_driving_license cr 
                INNER JOIN course c 
                    ON c.id = cr.course_id
            WHERE cr.id = $traineeId";
    if ($result = $db->query($sql)) {
        $row = $result->fetch_assoc();
        $result->close();

        $courseType = (int)$row['course_type'];
        $courseYear = $row['course_year'];
        $certNumber = $row['certificate_number'];
        $pass = false;
        switch ($courseType) {
            case 1:
                $pass = $row['subject_1_result'] && $row['subject_2_result'] && $row['subject_3_result'] && $row['subject_4_result'];
                break;
            case 2:
            case 3:
                $pass = $row['subject_1_result'];
                break;
        }

        if ($pass && ($certNumber == null)) {
            $sql = "SELECT MAX(cr.certificate_number) AS max_certificate_number
                    FROM course_registration_driving_license cr 
                        INNER JOIN course c 
                            ON c.id = cr.course_id
                    WHERE YEAR(c.begin_date) = $courseYear";
            if ($result = $db->query($sql)) {
                $row = $result->fetch_assoc();
                $maxCertNumber = $row['max_certificate_number'];
                $result->close();

                $nextCertNumber = $maxCertNumber == null ? MAX_CERT_NUMBER + 1 : $maxCertNumber + 1;
                $sql = "UPDATE course_registration_driving_license 
                        SET certificate_number = $nextCertNumber 
                        WHERE id = $traineeId";
                if ($db->query($sql)) {
                    $response[KEY_ERROR_CODE] = ERROR_CODE_SUCCESS;
                    $response[KEY_ERROR_MESSAGE] = 'บันทึกผลการอบรมสำเร็จ';
                    $response[KEY_ERROR_MESSAGE_MORE] = '';
                } else {
                    $response[KEY_ERROR_CODE] = ERROR_CODE_ERROR;
                    $response[KEY_ERROR_MESSAGE] = ' แต่เกิดข้อผิดพลาดในการออกเลขที่หนังสือรับรองการผ่านการอบรม (3): ' . $db->error;
                    $response[KEY_ERROR_MESSAGE_MORE] = '';
                }
            } else {
                $response[KEY_ERROR_CODE] = ERROR_CODE_ERROR;
                $response[KEY_ERROR_MESSAGE] = ' แต่เกิดข้อผิดพลาดในการออกเลขที่หนังสือรับรองการผ่านการอบรม (2): ' . $db->error;
                $response[KEY_ERROR_MESSAGE_MORE] = '';
            }
        } else {
            $response[KEY_ERROR_CODE] = ERROR_CODE_SUCCESS;
            $response[KEY_ERROR_MESSAGE] = 'บันทึกผลการอบรมสำเร็จ';
            $response[KEY_ERROR_MESSAGE_MORE] = '';
        }
    } else {
        $response[KEY_ERROR_CODE] = ERROR_CODE_ERROR;
        $response[KEY_ERROR_MESSAGE] = 'บันทึกผลการอบรมสำเร็จ แต่เกิดข้อผิดพลาดในการออกเลขที่หนังสือรับรองการผ่านการอบรม (1): '. $db->error;
        $response[KEY_ERROR_MESSAGE_MORE] = '';
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

function doUpdateReceiptNumber()
{
    global $db, $response;

    $serviceType = $db->real_escape_string($_POST['serviceType']);
    $traineeId = $db->real_escape_string($_POST['traineeId']);
    $receiptNumber = $db->real_escape_string($_POST['receiptNumber']);

    if (trim($receiptNumber) === '') {
        $response[KEY_ERROR_CODE] = ERROR_CODE_ERROR;
        $response[KEY_ERROR_MESSAGE] = 'Error: กรอกหมายเลขใบเสร็จผิดพลาด';
        $response[KEY_ERROR_MESSAGE_MORE] = '';
        return;
    }

    if ($serviceType === SERVICE_TYPE_TRAINING) {
        $sql = "UPDATE course_trainee 
                SET receipt_number = '$receiptNumber'
                WHERE id = $traineeId";
    } else if ($serviceType === SERVICE_TYPE_SOCIAL) {
        $sql = "UPDATE course_registration_social
                SET receipt_number = '$receiptNumber'
                WHERE id = $traineeId";
    } else if ($serviceType === SERVICE_TYPE_DRIVING_LICENSE) {
        $sql = "UPDATE course_registration_driving_license
                SET receipt_number = '$receiptNumber'
                WHERE id = $traineeId";
    } else {
        $response[KEY_ERROR_CODE] = ERROR_CODE_ERROR;
        $response[KEY_ERROR_MESSAGE] = 'Error: Invalid service type - ประเภทบริการไม่ถูกต้อง';
        $response[KEY_ERROR_MESSAGE_MORE] = '';
        return;
    }

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

function doAddTrainingProjectNews()
{
    global $db, $response;

    $date_now = date('Y-m-d H:i:s');

    $title = $db->real_escape_string($_POST['title']);
    $short_description = $db->real_escape_string($_POST['short_description']);
    $description = $db->real_escape_string($_POST['description']);
    $inputBeginDate = $db->real_escape_string($_POST['inputBeginDate']);
    $inputEndDate = $db->real_escape_string($_POST['inputEndDate']);

    if (!empty($inputBeginDate)) {
        $inputBeginDate = date('Y-m-d H:i:s', strtotime($inputBeginDate));
    }

    if (!empty($inputEndDate)) {
        $inputEndDate = date('Y-m-d H:i:s', strtotime($inputEndDate));
    }

    $meta_title = $db->real_escape_string($_POST['meta_title']);
    $meta_description = $db->real_escape_string($_POST['meta_description']);
    $meta_keywords = $db->real_escape_string($_POST['meta_keywords']);
    $featured = $db->real_escape_string($_POST['featured']);
    $status = $db->real_escape_string($_POST['status']);
    $uid = $db->real_escape_string($_POST['uid']);

    $slug = createSlug('article', 'slug', $title);

    $sql = "INSERT INTO article (title,
                                slug,
                                short_description, 
                                description, 
                                featured, 
                                status, 
                                start_date, 
                                end_date, 
                                meta_title, 
                                meta_keywords,
                                meta_description,
                                created_at,
                                updated_at,
                                created_by,
                                page_layout
                                )";

    $sql .= " VALUES ('$title', 
                    '$slug',
                    '$short_description', 
                    '$description', 
                    '$featured', 
                    '$status', 
                    '$inputBeginDate', 
                    '$inputEndDate',
                    '$meta_title',
                    '$meta_keywords',
                    '$meta_description',
                    '$date_now',
                    '$date_now',
                    '$uid',
                    'training_project_news'
                    )";

    if ($result = $db->query($sql)) {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SUCCESS;
        $response[KEY_ERROR_MESSAGE] = 'เพิ่มข่าวโครงการฝึกอบรมสำเร็จ';
        $response[KEY_ERROR_MESSAGE_MORE] = '';
        $id = $db->insert_id;

        $model_type = 'training_project_news';
        if (isset($_POST['cover_desktop_file_name'])) {
            $cover_desktop_file_name = $db->real_escape_string($_POST['cover_desktop_file_name']);
            $cover_desktop_name = $db->real_escape_string($_POST['cover_desktop_name']);
            $cover_desktop_size = $db->real_escape_string($_POST['cover_desktop_size']);
            $cover_desktop_type = $db->real_escape_string($_POST['cover_desktop_type']);

            $sql_cover = "INSERT INTO media (model_id,
                                            model_type,
                                            collection_name, 
                                            name, 
                                            file_name, 
                                            mime_type, 
                                            size, 
                                            order_column, 
                                            created_at, 
                                            updated_at
                                            )";

            $sql_cover .= " VALUES ('$id', 
                                '$model_type',
                                'cover_desktop', 
                                '$cover_desktop_name', 
                                '$cover_desktop_file_name', 
                                '$cover_desktop_type', 
                                '$cover_desktop_size', 
                                '0',
                                '$date_now',
                                '$date_now'
                                )";
            $db->query($sql_cover);
        }

        if (isset($_POST['gallery_desktop_file_name'])) {
            $gallery_desktop_file_name = $_POST['gallery_desktop_file_name'];
            $gallery_desktop_name = $_POST['gallery_desktop_name'];
            $gallery_desktop_size = $_POST['gallery_desktop_size'];
            $gallery_desktop_type = $_POST['gallery_desktop_type'];

            foreach ($gallery_desktop_name as $key => $value) {
                $sql_gallery = "INSERT INTO media (model_id,
                                                model_type,
                                                collection_name, 
                                                name, 
                                                file_name, 
                                                mime_type, 
                                                size, 
                                                order_column, 
                                                created_at, 
                                                updated_at
                                                )";

                $sql_gallery .= " VALUES ('$id', 
                                        '$model_type',
                                        'gallery_desktop', 
                                        '$value', 
                                        '$gallery_desktop_file_name[$key]', 
                                        '$gallery_desktop_type[$key]', 
                                        '$gallery_desktop_size[$key]', 
                                        '$key',
                                        '$date_now',
                                        '$date_now'
                                        )";
                $db->query($sql_gallery);
            }
            //echo gettype($gallery_desktop_name);
        }

        if (isset($_POST['document_file_name'])) {
            $document_file_name = $_POST['document_file_name'];
            $document_name = $_POST['document_name'];
            $document_file_size = $_POST['document_file_size'];
            $document_type = $_POST['document_type'];

            foreach ($document_file_name as $key => $value) {
                $path = '/static/media/' . $value;
                $sql_document = "INSERT INTO documents_download (article_id,
                                                status,
                                                created_at, 
                                                updated_at, 
                                                created_by, 
                                                file_name, 
                                                file_type, 
                                                file_path
                                                )";

                $sql_document .= " VALUES ('$id', 
                                        'publish',
                                        '$date_now', 
                                        '$date_now', 
                                        '$uid',
                                        '$value', 
                                        '$document_type[$key]', 
                                        '$path'
                                        )";
                $db->query($sql_document);
            }
        }
    } else {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
        $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการเพิ่มข่าวโครงการฝึกอบรม';
        $errMessage = $db->error;
        $response[KEY_ERROR_MESSAGE_MORE] = "$errMessage\nSQL: $sql";
    }

    $response['data'] = $_POST;
    $response['files'] = $_FILES;
    $response['sql'] = $sql;
    $response['slug'] = $slug;
}

function doUpdateTrainingProjectNews()
{
    global $db, $response;

    $date_now = date('Y-m-d H:i:s');
    $title = $db->real_escape_string($_POST['title']);
    $short_description = $db->real_escape_string($_POST['short_description']);
    $description = $db->real_escape_string($_POST['description']);
    $inputBeginDate = $db->real_escape_string($_POST['inputBeginDate']);
    $inputEndDate = $db->real_escape_string($_POST['inputEndDate']);

    if (!empty($inputBeginDate)) {
        $inputBeginDate = date('Y-m-d H:i:s', strtotime($inputBeginDate));
    }

    if (!empty($inputEndDate)) {
        $inputEndDate = date('Y-m-d H:i:s', strtotime($inputEndDate));
    }

    $meta_title = $db->real_escape_string($_POST['meta_title']);
    $meta_description = $db->real_escape_string($_POST['meta_description']);
    $meta_keywords = $db->real_escape_string($_POST['meta_keywords']);
    $featured = $db->real_escape_string($_POST['featured']);
    $status = $db->real_escape_string($_POST['status']);
    $uid = $db->real_escape_string($_POST['uid']);
    $itemId = $db->real_escape_string($_POST['itemId']);

    $sql = "UPDATE article SET title ='$title',short_description='$short_description',description='$description',featured ='$featured',"
        . " status = '$status', meta_title = '$meta_title', start_date = '$inputBeginDate', end_date = '$inputEndDate', "
        . " meta_keywords = '$meta_keywords', meta_description = '$meta_description', updated_at = '$date_now', updated_by = '$uid' "
        . " WHERE id = $itemId";

    if ($result = $db->query($sql)) {
        $id = $itemId;

        $model_type = 'training_project_news';
        if (isset($_POST['cover_desktop_file_name'])) {
            $cover_desktop_file_name = $db->real_escape_string($_POST['cover_desktop_file_name']);
            $cover_desktop_name = $db->real_escape_string($_POST['cover_desktop_name']);
            $cover_desktop_size = $db->real_escape_string($_POST['cover_desktop_size']);
            $cover_desktop_type = $db->real_escape_string($_POST['cover_desktop_type']);

            $sql_cover = "INSERT INTO media (model_id,
                                            model_type,
                                            collection_name, 
                                            name, 
                                            file_name, 
                                            mime_type, 
                                            size, 
                                            order_column, 
                                            created_at, 
                                            updated_at
                                            )";

            $sql_cover .= " VALUES ('$id', 
                                '$model_type',
                                'cover_desktop', 
                                '$cover_desktop_name', 
                                '$cover_desktop_file_name', 
                                '$cover_desktop_type', 
                                '$cover_desktop_size', 
                                '0',
                                '$date_now',
                                '$date_now'
                                )";
            $db->query($sql_cover);
        }

        if (isset($_POST['gallery_desktop_file_name'])) {
            $gallery_desktop_file_name = $_POST['gallery_desktop_file_name'];
            $gallery_desktop_name = $_POST['gallery_desktop_name'];
            $gallery_desktop_size = $_POST['gallery_desktop_size'];
            $gallery_desktop_type = $_POST['gallery_desktop_type'];

            foreach ($gallery_desktop_name as $key => $value) {
                $sql_gallery = "INSERT INTO media (model_id,
                                                model_type,
                                                collection_name, 
                                                name, 
                                                file_name, 
                                                mime_type, 
                                                size, 
                                                order_column, 
                                                created_at, 
                                                updated_at
                                                )";

                $sql_gallery .= " VALUES ('$id', 
                                        '$model_type',
                                        'gallery_desktop', 
                                        '$value', 
                                        '$gallery_desktop_file_name[$key]', 
                                        '$gallery_desktop_type[$key]', 
                                        '$gallery_desktop_size[$key]', 
                                        '$key',
                                        '$date_now',
                                        '$date_now'
                                        )";
                $db->query($sql_gallery);
            }
            //echo gettype($gallery_desktop_name);
        }

        if (isset($_POST['document_file_name'])) {
            $document_file_name = $_POST['document_file_name'];
            $document_name = $_POST['document_name'];
            $document_file_size = $_POST['document_file_size'];
            $document_type = $_POST['document_type'];

            foreach ($document_file_name as $key => $value) {
                $path = '/static/media/' . $value;
                $sql_document = "INSERT INTO documents_download (article_id,
                                                status,
                                                created_at, 
                                                updated_at, 
                                                created_by, 
                                                file_name, 
                                                file_type, 
                                                file_path
                                                )";

                $sql_document .= " VALUES ('$id', 
                                        'publish',
                                        '$date_now', 
                                        '$date_now', 
                                        '$uid',
                                        '$value', 
                                        '$document_type[$key]', 
                                        '$path'
                                        )";
                $db->query($sql_document);
            }
        }

        $response[KEY_ERROR_CODE] = ERROR_CODE_SUCCESS;
        $response[KEY_ERROR_MESSAGE] = 'แก้ไขข่าวโครงการฝึกอบรมสำเร็จ';
        $response[KEY_ERROR_MESSAGE_MORE] = '';
    } else {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
        $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการแก้ไขข่าวโครงการฝึกอบรม';
        $errMessage = $db->error;
        $response[KEY_ERROR_MESSAGE_MORE] = "$errMessage\nSQL: $sql";
    }
}

function doAddNewsOld()
{
    global $db, $response;

    $date_now = date('Y-m-d H:i:s');

    $title = $db->real_escape_string($_POST['title']);
    $short_description = $db->real_escape_string($_POST['short_description']);
    $description = $db->real_escape_string($_POST['description']);
    $inputBeginDate = $db->real_escape_string($_POST['inputBeginDate']);
    $inputEndDate = $db->real_escape_string($_POST['inputEndDate']);

    if (!empty($inputBeginDate)) {
        $inputBeginDate = date('Y-m-d H:i:s', strtotime($inputBeginDate));
    }

    if (!empty($inputEndDate)) {
        $inputEndDate = date('Y-m-d H:i:s', strtotime($inputEndDate));
    }

    $meta_title = $db->real_escape_string($_POST['meta_title']);
    $meta_description = $db->real_escape_string($_POST['meta_description']);
    $meta_keywords = $db->real_escape_string($_POST['meta_keywords']);
    $featured = $db->real_escape_string($_POST['featured']);
    $status = $db->real_escape_string($_POST['status']);
    $uid = $db->real_escape_string($_POST['uid']);

    $slug = createSlug('article', 'slug', $title);

    $sql = "INSERT INTO article (title,
                                slug,
                                short_description, 
                                description, 
                                featured, 
                                status, 
                                start_date, 
                                end_date, 
                                meta_title, 
                                meta_keywords,
                                meta_description,
                                created_at,
                                updated_at,
                                created_by,
                                page_layout
                                )";

    $sql .= " VALUES ('$title', 
                    '$slug',
                    '$short_description', 
                    '$description', 
                    '$featured', 
                    '$status', 
                    '$inputBeginDate', 
                    '$inputEndDate',
                    '$meta_title',
                    '$meta_keywords',
                    '$meta_description',
                    '$date_now',
                    '$date_now',
                    '$uid',
                    'news'
                    )";

    if ($result = $db->query($sql)) {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SUCCESS;
        $response[KEY_ERROR_MESSAGE] = 'เพิ่มข่าวประชาสัมพันธ์สำเร็จ';
        $response[KEY_ERROR_MESSAGE_MORE] = '';
        $id = $db->insert_id;

        $model_type = 'news';
        if (isset($_POST['cover_desktop_file_name'])) {
            $cover_desktop_file_name = $db->real_escape_string($_POST['cover_desktop_file_name']);
            $cover_desktop_name = $db->real_escape_string($_POST['cover_desktop_name']);
            $cover_desktop_size = $db->real_escape_string($_POST['cover_desktop_size']);
            $cover_desktop_type = $db->real_escape_string($_POST['cover_desktop_type']);

            $sql_cover = "INSERT INTO media (model_id,
                                            model_type,
                                            collection_name, 
                                            name, 
                                            file_name, 
                                            mime_type, 
                                            size, 
                                            order_column, 
                                            created_at, 
                                            updated_at
                                            )";

            $sql_cover .= " VALUES ('$id', 
                                '$model_type',
                                'cover_desktop', 
                                '$cover_desktop_name', 
                                '$cover_desktop_file_name', 
                                '$cover_desktop_type', 
                                '$cover_desktop_size', 
                                '0',
                                '$date_now',
                                '$date_now'
                                )";
            $db->query($sql_cover);
        }

        if (isset($_POST['gallery_desktop_file_name'])) {
            $gallery_desktop_file_name = $_POST['gallery_desktop_file_name'];
            $gallery_desktop_name = $_POST['gallery_desktop_name'];
            $gallery_desktop_size = $_POST['gallery_desktop_size'];
            $gallery_desktop_type = $_POST['gallery_desktop_type'];

            foreach ($gallery_desktop_name as $key => $value) {
                $sql_gallery = "INSERT INTO media (model_id,
                                                model_type,
                                                collection_name, 
                                                name, 
                                                file_name, 
                                                mime_type, 
                                                size, 
                                                order_column, 
                                                created_at, 
                                                updated_at
                                                )";

                $sql_gallery .= " VALUES ('$id', 
                                        '$model_type',
                                        'gallery_desktop', 
                                        '$value', 
                                        '$gallery_desktop_file_name[$key]', 
                                        '$gallery_desktop_type[$key]', 
                                        '$gallery_desktop_size[$key]', 
                                        '$key',
                                        '$date_now',
                                        '$date_now'
                                        )";
                $db->query($sql_gallery);
            }
            //echo gettype($gallery_desktop_name);
        }

        if (isset($_POST['document_file_name'])) {
            $document_file_name = $_POST['document_file_name'];
            $document_name = $_POST['document_name'];
            $document_file_size = $_POST['document_file_size'];
            $document_type = $_POST['document_type'];

            foreach ($document_file_name as $key => $value) {
                $path = '/static/media/' . $value;
                $sql_document = "INSERT INTO documents_download (article_id,
                                                status,
                                                created_at, 
                                                updated_at, 
                                                created_by, 
                                                file_name, 
                                                file_type, 
                                                file_path
                                                )";

                $sql_document .= " VALUES ('$id', 
                                        'publish',
                                        '$date_now', 
                                        '$date_now', 
                                        '$uid',
                                        '$value', 
                                        '$document_type[$key]', 
                                        '$path'
                                        )";
                $db->query($sql_document);
            }
        }
    } else {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
        $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการเพิ่มข่าวประชาสัมพันธ์';
        $errMessage = $db->error;
        $response[KEY_ERROR_MESSAGE_MORE] = "$errMessage\nSQL: $sql";
    }

    $response['data'] = $_POST;
    $response['files'] = $_FILES;
    $response['sql'] = $sql;
    $response['slug'] = $slug;
}

function doUpdateNewsOld()
{
    global $db, $response;

    $date_now = date('Y-m-d H:i:s');
    $title = $db->real_escape_string($_POST['title']);
    $short_description = $db->real_escape_string($_POST['short_description']);
    $description = $db->real_escape_string($_POST['description']);
    $inputBeginDate = $db->real_escape_string($_POST['inputBeginDate']);
    $inputEndDate = $db->real_escape_string($_POST['inputEndDate']);

    if (!empty($inputBeginDate)) {
        $inputBeginDate = date('Y-m-d H:i:s', strtotime($inputBeginDate));
    }

    if (!empty($inputEndDate)) {
        $inputEndDate = date('Y-m-d H:i:s', strtotime($inputEndDate));
    }

    $meta_title = $db->real_escape_string($_POST['meta_title']);
    $meta_description = $db->real_escape_string($_POST['meta_description']);
    $meta_keywords = $db->real_escape_string($_POST['meta_keywords']);
    $featured = $db->real_escape_string($_POST['featured']);
    $status = $db->real_escape_string($_POST['status']);
    $uid = $db->real_escape_string($_POST['uid']);
    $itemId = $db->real_escape_string($_POST['itemId']);

    $sql = "UPDATE article SET title ='$title',short_description='$short_description',description='$description',featured ='$featured',"
        . " status = '$status', meta_title = '$meta_title', start_date = '$inputBeginDate', end_date = '$inputEndDate', "
        . " meta_keywords = '$meta_keywords', meta_description = '$meta_description', updated_at = '$date_now', updated_by = '$uid' "
        . " WHERE id = $itemId";

    if ($result = $db->query($sql)) {
        $id = $itemId;

        $model_type = 'news';
        if (isset($_POST['cover_desktop_file_name'])) {
            $cover_desktop_file_name = $db->real_escape_string($_POST['cover_desktop_file_name']);
            $cover_desktop_name = $db->real_escape_string($_POST['cover_desktop_name']);
            $cover_desktop_size = $db->real_escape_string($_POST['cover_desktop_size']);
            $cover_desktop_type = $db->real_escape_string($_POST['cover_desktop_type']);

            $sql_cover = "INSERT INTO media (model_id,
                                            model_type,
                                            collection_name, 
                                            name, 
                                            file_name, 
                                            mime_type, 
                                            size, 
                                            order_column, 
                                            created_at, 
                                            updated_at
                                            )";

            $sql_cover .= " VALUES ('$id', 
                                '$model_type',
                                'cover_desktop', 
                                '$cover_desktop_name', 
                                '$cover_desktop_file_name', 
                                '$cover_desktop_type', 
                                '$cover_desktop_size', 
                                '0',
                                '$date_now',
                                '$date_now'
                                )";
            $db->query($sql_cover);
        }

        if (isset($_POST['gallery_desktop_file_name'])) {
            $gallery_desktop_file_name = $_POST['gallery_desktop_file_name'];
            $gallery_desktop_name = $_POST['gallery_desktop_name'];
            $gallery_desktop_size = $_POST['gallery_desktop_size'];
            $gallery_desktop_type = $_POST['gallery_desktop_type'];

            foreach ($gallery_desktop_name as $key => $value) {
                $sql_gallery = "INSERT INTO media (model_id,
                                                model_type,
                                                collection_name, 
                                                name, 
                                                file_name, 
                                                mime_type, 
                                                size, 
                                                order_column, 
                                                created_at, 
                                                updated_at
                                                )";

                $sql_gallery .= " VALUES ('$id', 
                                        '$model_type',
                                        'gallery_desktop', 
                                        '$value', 
                                        '$gallery_desktop_file_name[$key]', 
                                        '$gallery_desktop_type[$key]', 
                                        '$gallery_desktop_size[$key]', 
                                        '$key',
                                        '$date_now',
                                        '$date_now'
                                        )";
                $db->query($sql_gallery);
            }
            //echo gettype($gallery_desktop_name);
        }

        if (isset($_POST['document_file_name'])) {
            $document_file_name = $_POST['document_file_name'];
            $document_name = $_POST['document_name'];
            $document_file_size = $_POST['document_file_size'];
            $document_type = $_POST['document_type'];

            foreach ($document_file_name as $key => $value) {
                $path = '/static/media/' . $value;
                $sql_document = "INSERT INTO documents_download (article_id,
                                                status,
                                                created_at, 
                                                updated_at, 
                                                created_by, 
                                                file_name, 
                                                file_type, 
                                                file_path
                                                )";

                $sql_document .= " VALUES ('$id', 
                                        'publish',
                                        '$date_now', 
                                        '$date_now', 
                                        '$uid',
                                        '$value', 
                                        '$document_type[$key]', 
                                        '$path'
                                        )";
                $db->query($sql_document);
            }
        }

        $response[KEY_ERROR_CODE] = ERROR_CODE_SUCCESS;
        $response[KEY_ERROR_MESSAGE] = 'แก้ไขข่าวประชาสัมพันธ์สำเร็จ';
        $response[KEY_ERROR_MESSAGE_MORE] = '';
    } else {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
        $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการแก้ไขข่าวประชาสัมพันธ์';
        $errMessage = $db->error;
        $response[KEY_ERROR_MESSAGE_MORE] = "$errMessage\nSQL: $sql";
    }
}

function doAddMenu()
{
    global $db, $response;

    $date_now = date('Y-m-d H:i:s');

    $title = $db->real_escape_string($_POST['title']);
    $description = $db->real_escape_string($_POST['description']);
    $layout = $db->real_escape_string($_POST['layout']);
    $link_type = $db->real_escape_string($_POST['link_type']);
    $url_external = $db->real_escape_string($_POST['url_external']);
    $position = $db->real_escape_string($_POST['position']);
    $meta_title = $db->real_escape_string($_POST['meta_title']);
    $meta_description = $db->real_escape_string($_POST['meta_description']);
    $meta_keywords = $db->real_escape_string($_POST['meta_keywords']);
    $target = $db->real_escape_string($_POST['target']);
    $ordering = $db->real_escape_string($_POST['ordering']);
    $parent_id = $db->real_escape_string($_POST['parent_id']);
    $status = $db->real_escape_string($_POST['status']);
    $uid = $db->real_escape_string($_POST['uid']);

    $slug = createSlug('menus', 'slug', $title);

    $sql = "INSERT INTO menus (name,
                                slug,
                                description, 
                                target, 
                                status,
                                layout,
                                link_type,
                                url_external,
                                position,
                                ordering,
                                meta_title, 
                                meta_keywords,
                                meta_description,
                                created_at,
                                updated_at,
                                created_by,
                                parent_id
                                )";

    $sql .= " VALUES ('$title', 
                    '$slug',
                    '$description', 
                    '$target', 
                    '$status', 
                    '$layout', 
                    '$link_type',
                    '$url_external',
                    '$position',
                    '$ordering',
                    '$meta_title',
                    '$meta_keywords',
                    '$meta_description',
                    '$date_now',
                    '$date_now',
                    '$uid',
                    '$parent_id'
                    )";

    if ($result = $db->query($sql)) {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SUCCESS;
        $response[KEY_ERROR_MESSAGE] = 'เพิ่มเมนูสำเร็จ';
        $response[KEY_ERROR_MESSAGE_MORE] = '';
        $id = $db->insert_id;

        $model_type = 'menu';
        if (isset($_POST['cover_desktop_file_name'])) {
            $cover_desktop_file_name = $db->real_escape_string($_POST['cover_desktop_file_name']);
            $cover_desktop_name = $db->real_escape_string($_POST['cover_desktop_name']);
            $cover_desktop_size = $db->real_escape_string($_POST['cover_desktop_size']);
            $cover_desktop_type = $db->real_escape_string($_POST['cover_desktop_type']);

            $sql_cover = "INSERT INTO media (model_id,
                                            model_type,
                                            collection_name, 
                                            name, 
                                            file_name, 
                                            mime_type, 
                                            size, 
                                            order_column, 
                                            created_at, 
                                            updated_at
                                            )";

            $sql_cover .= " VALUES ('$id', 
                                '$model_type',
                                'cover_desktop', 
                                '$cover_desktop_name', 
                                '$cover_desktop_file_name', 
                                '$cover_desktop_type', 
                                '$cover_desktop_size', 
                                '0',
                                '$date_now',
                                '$date_now'
                                )";
            $db->query($sql_cover);
        }
    } else {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
        $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการเพิ่มเมนู';
        $errMessage = $db->error;
        $response[KEY_ERROR_MESSAGE_MORE] = "$errMessage\nSQL: $sql";
    }

    $response['data'] = $_POST;
    $response['files'] = $_FILES;
    $response['sql'] = $sql;
    $response['slug'] = $slug;
}

function doUpdateMenu()
{
    global $db, $response;

    $date_now = date('Y-m-d H:i:s');
    $title = $db->real_escape_string($_POST['title']);
    $description = $db->real_escape_string($_POST['description']);
    $layout = $db->real_escape_string($_POST['layout']);
    $link_type = $db->real_escape_string($_POST['link_type']);
    $url_external = $db->real_escape_string($_POST['url_external']);
    $position = $db->real_escape_string($_POST['position']);
    $meta_title = $db->real_escape_string($_POST['meta_title']);
    $meta_description = $db->real_escape_string($_POST['meta_description']);
    $meta_keywords = $db->real_escape_string($_POST['meta_keywords']);
    $target = $db->real_escape_string($_POST['target']);
    $ordering = $db->real_escape_string($_POST['ordering']);
    $parent_id = $db->real_escape_string($_POST['parent_id']);
    $status = $db->real_escape_string($_POST['status']);
    $uid = $db->real_escape_string($_POST['uid']);

    $itemId = $db->real_escape_string($_POST['itemId']);

    $sql = "UPDATE menus SET name ='$title',description='$description',target ='$target',ordering='$ordering',parent_id='$parent_id',"
        . " status = '$status', meta_title = '$meta_title', layout = '$layout', url_external = '$url_external',position='$position', "
        . " meta_keywords = '$meta_keywords', meta_description = '$meta_description', updated_at = '$date_now', updated_by = '$uid' "
        . " WHERE id = $itemId";

    if ($result = $db->query($sql)) {
        $id = $itemId;

        $model_type = 'menu';
        if (isset($_POST['cover_desktop_file_name'])) {

            $cover_desktop_file_name = $db->real_escape_string($_POST['cover_desktop_file_name']);
            $cover_desktop_name = $db->real_escape_string($_POST['cover_desktop_name']);
            $cover_desktop_size = $db->real_escape_string($_POST['cover_desktop_size']);
            $cover_desktop_type = $db->real_escape_string($_POST['cover_desktop_type']);

            $sql_cover = "INSERT INTO media (model_id,
                                            model_type,
                                            collection_name, 
                                            name, 
                                            file_name, 
                                            mime_type, 
                                            size, 
                                            order_column, 
                                            created_at, 
                                            updated_at
                                            )";

            $sql_cover .= " VALUES ('$id', 
                                '$model_type',
                                'cover_desktop', 
                                '$cover_desktop_name', 
                                '$cover_desktop_file_name', 
                                '$cover_desktop_type', 
                                '$cover_desktop_size', 
                                '0',
                                '$date_now',
                                '$date_now'
                                )";
            $db->query($sql_cover);
        }

        $response[KEY_ERROR_CODE] = ERROR_CODE_SUCCESS;
        $response[KEY_ERROR_MESSAGE] = 'แก้ไขเมนูสำเร็จ';
        $response[KEY_ERROR_MESSAGE_MORE] = '';
    } else {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
        $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการแก้ไขเมนู';
        $errMessage = $db->error;
        $response[KEY_ERROR_MESSAGE_MORE] = "$errMessage\nSQL: $sql";
    }
}

function doAddBanner()
{
    global $db, $response;

    $date_now = date('Y-m-d H:i:s');
    $title = $db->real_escape_string($_POST['title']);
    $description = $db->real_escape_string($_POST['description']);
    $target = $db->real_escape_string($_POST['target']);
    $status = $db->real_escape_string($_POST['status']);
    $uid = $db->real_escape_string($_POST['uid']);

    $sql = "INSERT INTO banners (name,
                                description, 
                                target, 
                                status, 
                                created_at,
                                updated_at,
                                created_by
                                )";

    $sql .= " VALUES ('$title', 
                    '$description', 
                    '$target', 
                    '$status', 
                    '$date_now',
                    '$date_now',
                    '$uid'
                    )";

    if ($result = $db->query($sql)) {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SUCCESS;
        $response[KEY_ERROR_MESSAGE] = 'เพิ่มแบนเนอร์สำเร็จ';
        $response[KEY_ERROR_MESSAGE_MORE] = '';
        $id = $db->insert_id;

        $model_type = 'banner';
        if (isset($_POST['cover_desktop_file_name'])) {
            $cover_desktop_file_name = $db->real_escape_string($_POST['cover_desktop_file_name']);
            $cover_desktop_name = $db->real_escape_string($_POST['cover_desktop_name']);
            $cover_desktop_size = $db->real_escape_string($_POST['cover_desktop_size']);
            $cover_desktop_type = $db->real_escape_string($_POST['cover_desktop_type']);

            $sql_cover = "INSERT INTO media (model_id,
                                            model_type,
                                            collection_name, 
                                            name, 
                                            file_name, 
                                            mime_type, 
                                            size, 
                                            order_column, 
                                            created_at, 
                                            updated_at
                                            )";

            $sql_cover .= " VALUES ('$id', 
                                '$model_type',
                                'cover_desktop', 
                                '$cover_desktop_name', 
                                '$cover_desktop_file_name', 
                                '$cover_desktop_type', 
                                '$cover_desktop_size', 
                                '0',
                                '$date_now',
                                '$date_now'
                                )";
            $db->query($sql_cover);
        }
    } else {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
        $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการเพิ่มแบนเนอร์';
        $errMessage = $db->error;
        $response[KEY_ERROR_MESSAGE_MORE] = "$errMessage\nSQL: $sql";
    }

    $response['data'] = $_POST;
    $response['files'] = $_FILES;
    $response['sql'] = $sql;
}

function doUpdateBanner()
{
    global $db, $response;

    $date_now = date('Y-m-d H:i:s');
    $title = $db->real_escape_string($_POST['title']);
    $description = $db->real_escape_string($_POST['description']);
    $target = $db->real_escape_string($_POST['target']);
    $status = $db->real_escape_string($_POST['status']);
    $uid = $db->real_escape_string($_POST['uid']);
    $itemId = $db->real_escape_string($_POST['itemId']);

    $sql = "UPDATE banners SET name ='$title',description='$description',target ='$target',"
        . " status = '$status', "
        . " updated_at = '$date_now', updated_by = '$uid'"
        . " WHERE id = $itemId";

    if ($result = $db->query($sql)) {
        $id = $itemId;

        $model_type = 'banner';
        if (isset($_POST['cover_desktop_file_name'])) {

            $cover_desktop_file_name = $db->real_escape_string($_POST['cover_desktop_file_name']);
            $cover_desktop_name = $db->real_escape_string($_POST['cover_desktop_name']);
            $cover_desktop_size = $db->real_escape_string($_POST['cover_desktop_size']);
            $cover_desktop_type = $db->real_escape_string($_POST['cover_desktop_type']);

            $sql_cover = "INSERT INTO media (model_id,
                                            model_type,
                                            collection_name, 
                                            name, 
                                            file_name, 
                                            mime_type, 
                                            size, 
                                            order_column, 
                                            created_at, 
                                            updated_at
                                            )";

            $sql_cover .= " VALUES ('$id', 
                                '$model_type',
                                'cover_desktop', 
                                '$cover_desktop_name', 
                                '$cover_desktop_file_name', 
                                '$cover_desktop_type', 
                                '$cover_desktop_size', 
                                '0',
                                '$date_now',
                                '$date_now'
                                )";
            $db->query($sql_cover);
        }

        $response[KEY_ERROR_CODE] = ERROR_CODE_SUCCESS;
        $response[KEY_ERROR_MESSAGE] = 'แก้ไขแบนเนอร์สำเร็จ';
        $response[KEY_ERROR_MESSAGE_MORE] = '';
    } else {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
        $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการแก้ไขแบนเนอร์';
        $errMessage = $db->error;
        $response[KEY_ERROR_MESSAGE_MORE] = "$errMessage\nSQL: $sql";
    }
}

function doAddResearchAndAcademic()
{
    global $db, $response;

    $date_now = date('Y-m-d H:i:s');

    $title = $db->real_escape_string($_POST['title']);
    $description = $db->real_escape_string($_POST['description']);
    $published_year = $db->real_escape_string($_POST['published_year']);
    $researcher_name = $db->real_escape_string($_POST['researcher_name']);
    $research_funding_source = $db->real_escape_string($_POST['research_funding_source']);

    $meta_title = $db->real_escape_string($_POST['meta_title']);
    $meta_description = $db->real_escape_string($_POST['meta_description']);
    $meta_keywords = $db->real_escape_string($_POST['meta_keywords']);
    $featured = $db->real_escape_string($_POST['featured']);
    $status = $db->real_escape_string($_POST['status']);
    $uid = $db->real_escape_string($_POST['uid']);

    $slug = createSlug('research_and_academic', 'slug', $title);

    $sql = "INSERT INTO research_and_academic (title,
                                slug,
                                description, 
                                featured, 
                                status, 
                                meta_title, 
                                meta_keywords,
                                meta_description,
                                created_at,
                                updated_at,
                                created_by,
                                published_year,
                                researcher_name,
                                research_funding_source
                                )";

    $sql .= " VALUES ('$title', 
                    '$slug',
                    '$description', 
                    '$featured', 
                    '$status', 
                    '$meta_title',
                    '$meta_keywords',
                    '$meta_description',
                    '$date_now',
                    '$date_now',
                    '$uid',
                    '$published_year',
                    '$researcher_name',
                    '$research_funding_source'
                    )";

    if ($result = $db->query($sql)) {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SUCCESS;
        $response[KEY_ERROR_MESSAGE] = 'เพิ่มโครงการวิจัยและวิชาการสำเร็จ';
        $response[KEY_ERROR_MESSAGE_MORE] = '';
        $id = $db->insert_id;

        if (isset($_POST['document_file_name'])) {
            $document_file_name = $_POST['document_file_name'];
            $document_name = $_POST['document_name'];
            $document_file_size = $_POST['document_file_size'];
            $document_type = $_POST['document_type'];
            $file_type = $_POST['file_type'];

            foreach ($document_file_name as $key => $value) {
                $path = '/static/media/' . $value;
                $sql_document = "INSERT INTO documents_download (article_id,
                                                status,
                                                created_at, 
                                                updated_at, 
                                                created_by, 
                                                file_name, 
                                                file_type, 
                                                file_path,
                                                document_type
                                                )";

                $sql_document .= " VALUES ('$id', 
                                        'publish',
                                        '$date_now', 
                                        '$date_now', 
                                        '$uid',
                                        '$value', 
                                        '$document_type[$key]', 
                                        '$path',
                                        '$file_type'
                                        )";
                $db->query($sql_document);
            }
        }
    } else {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
        $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการเพิ่มโครงการวิจัยและวิชาการ';
        $errMessage = $db->error;
        $response[KEY_ERROR_MESSAGE_MORE] = "$errMessage\nSQL: $sql";
    }

    $response['data'] = $_POST;
    $response['files'] = $_FILES;
    $response['sql'] = $sql;
    $response['slug'] = $slug;
}

function doUpdateResearchAndAcademic()
{
    global $db, $response;

    $date_now = date('Y-m-d H:i:s');
    $title = $db->real_escape_string($_POST['title']);
    $description = $db->real_escape_string($_POST['description']);
    $published_year = $db->real_escape_string($_POST['published_year']);
    $researcher_name = $db->real_escape_string($_POST['researcher_name']);
    $research_funding_source = $db->real_escape_string($_POST['research_funding_source']);

    $meta_title = $db->real_escape_string($_POST['meta_title']);
    $meta_description = $db->real_escape_string($_POST['meta_description']);
    $meta_keywords = $db->real_escape_string($_POST['meta_keywords']);
    $featured = $db->real_escape_string($_POST['featured']);
    $status = $db->real_escape_string($_POST['status']);
    $uid = $db->real_escape_string($_POST['uid']);
    $itemId = $db->real_escape_string($_POST['itemId']);

    $sql = "UPDATE research_and_academic SET title ='$title',description='$description',featured ='$featured',"
        . " status = '$status', meta_title = '$meta_title',published_year='$published_year',researcher_name='$researcher_name',research_funding_source='$research_funding_source', "
        . " meta_keywords = '$meta_keywords', meta_description = '$meta_description', updated_at = '$date_now', updated_by = '$uid' "
        . " WHERE id = $itemId";

    if ($result = $db->query($sql)) {
        $id = $itemId;

        if (isset($_POST['document_file_name'])) {
            $document_file_name = $_POST['document_file_name'];
            $document_name = $_POST['document_name'];
            $document_file_size = $_POST['document_file_size'];
            $document_type = $_POST['document_type'];
            $file_type = $_POST['file_type'];

            foreach ($document_file_name as $key => $value) {
                $path = '/static/media/' . $value;
                $sql_document = "INSERT INTO documents_download (article_id,
                                                status,
                                                created_at, 
                                                updated_at, 
                                                created_by, 
                                                file_name, 
                                                file_type, 
                                                file_path,
                                                document_type,
                                                )";

                $sql_document .= " VALUES ('$id', 
                                        'publish',
                                        '$date_now', 
                                        '$date_now', 
                                        '$uid',
                                        '$value', 
                                        '$document_type[$key]', 
                                        '$path',
                                        '$file_type'
                                        )";
                $db->query($sql_document);
            }
        }

        $response[KEY_ERROR_CODE] = ERROR_CODE_SUCCESS;
        $response[KEY_ERROR_MESSAGE] = 'แก้ไขข่าวประชาสัมพันธ์สำเร็จ';
        $response[KEY_ERROR_MESSAGE_MORE] = '';
    } else {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
        $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการแก้ไขข่าวประชาสัมพันธ์';
        $errMessage = $db->error;
        $response[KEY_ERROR_MESSAGE_MORE] = "$errMessage\nSQL: $sql";
    }
}

function doAddOrganizationalStructure()
{
    global $db, $response;

    $date_now = date('Y-m-d H:i:s');
    $title = $db->real_escape_string($_POST['title']);

    $meta_title = $db->real_escape_string($_POST['meta_title']);
    $meta_description = $db->real_escape_string($_POST['meta_description']);
    $meta_keywords = $db->real_escape_string($_POST['meta_keywords']);
    $status = $db->real_escape_string($_POST['status']);
    $uid = $db->real_escape_string($_POST['uid']);
    $slug = createSlug('article', 'slug', $title);

    $sql = "INSERT INTO article (title,
                                slug,
                                status, 
                                meta_title, 
                                meta_keywords,
                                meta_description,
                                created_at,
                                updated_at,
                                created_by,
                                page_layout
                                )";

    $sql .= " VALUES ('$title', 
                    '$slug',
                    '$status', 
                    '$meta_title',
                    '$meta_keywords',
                    '$meta_description',
                    '$date_now',
                    '$date_now',
                    '$uid',
                    'organizational_structure'
                    )";

    if ($result = $db->query($sql)) {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SUCCESS;
        $response[KEY_ERROR_MESSAGE] = 'เพิ่มข่าวประชาสัมพันธ์สำเร็จ';
        $response[KEY_ERROR_MESSAGE_MORE] = '';
        $id = $db->insert_id;

        $model_type = 'organizational_structure';
        if (isset($_POST['cover_desktop_file_name'])) {

            $cover_desktop_file_name = $db->real_escape_string($_POST['cover_desktop_file_name']);
            $cover_desktop_name = $db->real_escape_string($_POST['cover_desktop_name']);
            $cover_desktop_size = $db->real_escape_string($_POST['cover_desktop_size']);
            $cover_desktop_type = $db->real_escape_string($_POST['cover_desktop_type']);

            $sql_cover = "INSERT INTO media (model_id,
                                            model_type,
                                            collection_name, 
                                            name, 
                                            file_name, 
                                            mime_type, 
                                            size, 
                                            order_column, 
                                            created_at, 
                                            updated_at
                                            )";

            $sql_cover .= " VALUES ('$id', 
                                '$model_type',
                                'cover_desktop', 
                                '$cover_desktop_name', 
                                '$cover_desktop_file_name', 
                                '$cover_desktop_type', 
                                '$cover_desktop_size', 
                                '0',
                                '$date_now',
                                '$date_now'
                                )";
            $db->query($sql_cover);
        }
    } else {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
        $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการเพิ่มโครงสร้างองค์กร';
        $errMessage = $db->error;
        $response[KEY_ERROR_MESSAGE_MORE] = "$errMessage\nSQL: $sql";
    }

    $response['data'] = $_POST;
    $response['files'] = $_FILES;
    $response['sql'] = $sql;
    $response['slug'] = $slug;
}

function doUpdateOrganizationalStructure()
{
    global $db, $response;

    $date_now = date('Y-m-d H:i:s');
    $title = $db->real_escape_string($_POST['title']);

    $meta_title = $db->real_escape_string($_POST['meta_title']);
    $meta_description = $db->real_escape_string($_POST['meta_description']);
    $meta_keywords = $db->real_escape_string($_POST['meta_keywords']);
    $status = $db->real_escape_string($_POST['status']);
    $uid = $db->real_escape_string($_POST['uid']);
    $itemId = $db->real_escape_string($_POST['itemId']);

    $sql = "UPDATE article SET title ='$title',"
        . " status = '$status', meta_title = '$meta_title', "
        . " meta_keywords = '$meta_keywords', meta_description = '$meta_description', updated_at = '$date_now', updated_by = '$uid' "
        . " WHERE id = $itemId";

    if ($result = $db->query($sql)) {
        $id = $itemId;

        $model_type = 'organizational_structure';
        if (isset($_POST['cover_desktop_file_name'])) {
            $cover_desktop_file_name = $db->real_escape_string($_POST['cover_desktop_file_name']);
            $cover_desktop_name = $db->real_escape_string($_POST['cover_desktop_name']);
            $cover_desktop_size = $db->real_escape_string($_POST['cover_desktop_size']);
            $cover_desktop_type = $db->real_escape_string($_POST['cover_desktop_type']);

            $sql_cover = "INSERT INTO media (model_id,
                                            model_type,
                                            collection_name, 
                                            name, 
                                            file_name, 
                                            mime_type, 
                                            size, 
                                            order_column, 
                                            created_at, 
                                            updated_at
                                            )";

            $sql_cover .= " VALUES ('$id', 
                                '$model_type',
                                'cover_desktop', 
                                '$cover_desktop_name', 
                                '$cover_desktop_file_name', 
                                '$cover_desktop_type', 
                                '$cover_desktop_size', 
                                '0',
                                '$date_now',
                                '$date_now'
                                )";
            $db->query($sql_cover);
        }

        $response[KEY_ERROR_CODE] = ERROR_CODE_SUCCESS;
        $response[KEY_ERROR_MESSAGE] = 'แก้ไขโครงสร้างองค์กรสำเร็จ';
        $response[KEY_ERROR_MESSAGE_MORE] = '';
    } else {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
        $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการแก้ไขโครงสร้างองค์กร';
        $errMessage = $db->error;
        $response[KEY_ERROR_MESSAGE_MORE] = "$errMessage\nSQL: $sql";
    }
}

function doAddMission()
{
    global $db, $response;

    $date_now = date('Y-m-d H:i:s');

    $title = $db->real_escape_string($_POST['title']);
    $short_description = $db->real_escape_string($_POST['short_description']);
    $status = $db->real_escape_string($_POST['status']);
    $uid = $db->real_escape_string($_POST['uid']);

    $slug = createSlug('article', 'slug', $title);

    $sql = "INSERT INTO article (title,
                                slug,
                                short_description, 
                                status,
                                created_at,
                                updated_at,
                                created_by,
                                page_layout
                                )";

    $sql .= " VALUES ('$title', 
                    '$slug',
                    '$short_description', 
                    '$status', 
                    '$date_now',
                    '$date_now',
                    '$uid',
                    'mission'
                    )";

    if ($result = $db->query($sql)) {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SUCCESS;
        $response[KEY_ERROR_MESSAGE] = 'เพิ่มภารกิจสำเร็จ';
        $response[KEY_ERROR_MESSAGE_MORE] = '';
        $id = $db->insert_id;

        $model_type = 'mission';
        if (isset($_POST['cover_desktop_file_name'])) {

            $cover_desktop_file_name = $db->real_escape_string($_POST['cover_desktop_file_name']);
            $cover_desktop_name = $db->real_escape_string($_POST['cover_desktop_name']);
            $cover_desktop_size = $db->real_escape_string($_POST['cover_desktop_size']);
            $cover_desktop_type = $db->real_escape_string($_POST['cover_desktop_type']);

            $sql_cover = "INSERT INTO media (model_id,
                                            model_type,
                                            collection_name, 
                                            name, 
                                            file_name, 
                                            mime_type, 
                                            size, 
                                            order_column, 
                                            created_at, 
                                            updated_at
                                            )";

            $sql_cover .= " VALUES ('$id', 
                                '$model_type',
                                'cover_desktop', 
                                '$cover_desktop_name', 
                                '$cover_desktop_file_name', 
                                '$cover_desktop_type', 
                                '$cover_desktop_size', 
                                '0',
                                '$date_now',
                                '$date_now'
                                )";
            $db->query($sql_cover);
        }
    } else {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
        $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการเพิ่มภารกิจ';
        $errMessage = $db->error;
        $response[KEY_ERROR_MESSAGE_MORE] = "$errMessage\nSQL: $sql";
    }

    $response['data'] = $_POST;
    $response['files'] = $_FILES;
    $response['sql'] = $sql;
    $response['slug'] = $slug;
}

function doUpdateMission()
{
    global $db, $response;

    $date_now = date('Y-m-d H:i:s');
    $title = $db->real_escape_string($_POST['title']);
    $short_description = $db->real_escape_string($_POST['short_description']);

    $status = $db->real_escape_string($_POST['status']);
    $uid = $db->real_escape_string($_POST['uid']);
    $itemId = $db->real_escape_string($_POST['itemId']);

    $sql = "UPDATE article SET title ='$title',short_description='$short_description',"
        . " status = '$status', "
        . " updated_at = '$date_now', updated_by = '$uid' "
        . " WHERE id = $itemId";

    if ($result = $db->query($sql)) {
        $id = $itemId;

        $model_type = 'mission';
        if (isset($_POST['cover_desktop_file_name'])) {
            $cover_desktop_file_name = $db->real_escape_string($_POST['cover_desktop_file_name']);
            $cover_desktop_name = $db->real_escape_string($_POST['cover_desktop_name']);
            $cover_desktop_size = $db->real_escape_string($_POST['cover_desktop_size']);
            $cover_desktop_type = $db->real_escape_string($_POST['cover_desktop_type']);

            $sql_cover = "INSERT INTO media (model_id,
                                            model_type,
                                            collection_name, 
                                            name, 
                                            file_name, 
                                            mime_type, 
                                            size, 
                                            order_column, 
                                            created_at, 
                                            updated_at
                                            )";

            $sql_cover .= " VALUES ('$id', 
                                '$model_type',
                                'cover_desktop', 
                                '$cover_desktop_name', 
                                '$cover_desktop_file_name', 
                                '$cover_desktop_type', 
                                '$cover_desktop_size', 
                                '0',
                                '$date_now',
                                '$date_now'
                                )";
            $db->query($sql_cover);
        }
        $response[KEY_ERROR_CODE] = ERROR_CODE_SUCCESS;
        $response[KEY_ERROR_MESSAGE] = 'แก้ไขภารกิจสำเร็จ';
        $response[KEY_ERROR_MESSAGE_MORE] = '';
    } else {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
        $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการแก้ไขภารกิจ';
        $errMessage = $db->error;
        $response[KEY_ERROR_MESSAGE_MORE] = "$errMessage\nSQL: $sql";
    }
}

function doAddAboutOrganization()
{
    global $db, $response;

    $date_now = date('Y-m-d H:i:s');

    $title = $db->real_escape_string($_POST['title']);
    $short_description = $db->real_escape_string($_POST['short_description']);
    $status = $db->real_escape_string($_POST['status']);
    $uid = $db->real_escape_string($_POST['uid']);
    $slug = createSlug('article', 'slug', $title);

    $sql = "INSERT INTO article (title,
                                slug,
                                short_description, 
                                status, 
                                created_at,
                                updated_at,
                                created_by,
                                page_layout
                                )";

    $sql .= " VALUES ('$title', 
                    '$slug',
                    '$short_description', 
                    '$status', 
                    '$date_now',
                    '$date_now',
                    '$uid',
                    'about_organization'
                    )";

    if ($result = $db->query($sql)) {

        $response[KEY_ERROR_CODE] = ERROR_CODE_SUCCESS;
        $response[KEY_ERROR_MESSAGE] = 'เพิ่มเกี่ยวกับองค์กรสำเร็จ';
        $response[KEY_ERROR_MESSAGE_MORE] = '';
        $id = $db->insert_id;

    } else {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
        $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการเพิ่มเกี่ยวกับองค์กร';
        $errMessage = $db->error;
        $response[KEY_ERROR_MESSAGE_MORE] = "$errMessage\nSQL: $sql";
    }

    $response['data'] = $_POST;
    $response['files'] = $_FILES;
    $response['sql'] = $sql;
    $response['slug'] = $slug;
}

function doUpdateAboutOrganization()
{
    global $db, $response;

    $date_now = date('Y-m-d H:i:s');
    $title = $db->real_escape_string($_POST['title']);
    $short_description = $db->real_escape_string($_POST['short_description']);

    $status = $db->real_escape_string($_POST['status']);
    $uid = $db->real_escape_string($_POST['uid']);
    $itemId = $db->real_escape_string($_POST['itemId']);

    $sql = "UPDATE article SET title ='$title',short_description='$short_description',"
        . " status = '$status', "
        . " updated_at = '$date_now', updated_by = '$uid' "
        . " WHERE id = $itemId";

    if ($result = $db->query($sql)) {
        $id = $itemId;
        $response[KEY_ERROR_CODE] = ERROR_CODE_SUCCESS;
        $response[KEY_ERROR_MESSAGE] = 'แก้ไขเกี่ยวกับองค์กสำเร็จ';
        $response[KEY_ERROR_MESSAGE_MORE] = '';
    } else {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
        $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการแก้ไขเกี่ยวกับองค์ก';
        $errMessage = $db->error;
        $response[KEY_ERROR_MESSAGE_MORE] = "$errMessage\nSQL: $sql";
    }
}

function doAddActivity()
{
    global $db, $response;

    $date_now = date('Y-m-d H:i:s');

    $title = $db->real_escape_string($_POST['title']);
    $short_description = $db->real_escape_string($_POST['short_description']);
    $description = $db->real_escape_string($_POST['description']);
    $inputBeginDate = $db->real_escape_string($_POST['inputBeginDate']);
    $inputEndDate = $db->real_escape_string($_POST['inputEndDate']);

    if (!empty($inputBeginDate)) {
        $inputBeginDate = date('Y-m-d H:i:s', strtotime($inputBeginDate));
    }

    if (!empty($inputEndDate)) {
        $inputEndDate = date('Y-m-d H:i:s', strtotime($inputEndDate));
    }

    $meta_title = $db->real_escape_string($_POST['meta_title']);
    $meta_description = $db->real_escape_string($_POST['meta_description']);
    $meta_keywords = $db->real_escape_string($_POST['meta_keywords']);
    $featured = $db->real_escape_string($_POST['featured']);
    $status = $db->real_escape_string($_POST['status']);
    $uid = $db->real_escape_string($_POST['uid']);
    $video_type = $db->real_escape_string($_POST['video_type']);
    $slug = createSlug('article', 'slug', $title);

    $sql = "INSERT INTO article (title,
                                slug,
                                short_description, 
                                description, 
                                featured, 
                                status, 
                                start_date, 
                                end_date, 
                                meta_title, 
                                meta_keywords,
                                meta_description,
                                created_at,
                                updated_at,
                                created_by,
                                page_layout,
                                video_type
                                )";

    $sql .= " VALUES ('$title', 
                    '$slug',
                    '$short_description', 
                    '$description', 
                    '$featured', 
                    '$status', 
                    '$inputBeginDate', 
                    '$inputEndDate',
                    '$meta_title',
                    '$meta_keywords',
                    '$meta_description',
                    '$date_now',
                    '$date_now',
                    '$uid',
                    'activity',
                    '$video_type'
                    )";

    if ($result = $db->query($sql)) {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SUCCESS;
        $response[KEY_ERROR_MESSAGE] = 'เพิ่มภาพกิจกรรมสำเร็จ';
        $response[KEY_ERROR_MESSAGE_MORE] = '';
        $id = $db->insert_id;

        $model_type = 'activity';
        if (isset($_POST['cover_desktop_file_name'])) {
            $cover_desktop_file_name = $db->real_escape_string($_POST['cover_desktop_file_name']);
            $cover_desktop_name = $db->real_escape_string($_POST['cover_desktop_name']);
            $cover_desktop_size = $db->real_escape_string($_POST['cover_desktop_size']);
            $cover_desktop_type = $db->real_escape_string($_POST['cover_desktop_type']);

            $sql_cover = "INSERT INTO media (model_id,
                                            model_type,
                                            collection_name, 
                                            name, 
                                            file_name, 
                                            mime_type, 
                                            size, 
                                            order_column, 
                                            created_at, 
                                            updated_at,
                                            )";

            $sql_cover .= " VALUES ('$id', 
                                '$model_type',
                                'cover_desktop', 
                                '$cover_desktop_name', 
                                '$cover_desktop_file_name', 
                                '$cover_desktop_type', 
                                '$cover_desktop_size', 
                                '0',
                                '$date_now',
                                '$date_now'
                                )";
            $db->query($sql_cover);
        }

        if (isset($_POST['gallery_desktop_file_name'])) {
            $gallery_desktop_file_name = $_POST['gallery_desktop_file_name'];
            $gallery_desktop_name = $_POST['gallery_desktop_name'];
            $gallery_desktop_size = $_POST['gallery_desktop_size'];
            $gallery_desktop_type = $_POST['gallery_desktop_type'];

            foreach ($gallery_desktop_name as $key => $value) {
                $sql_gallery = "INSERT INTO media (model_id,
                                                model_type,
                                                collection_name, 
                                                name, 
                                                file_name, 
                                                mime_type, 
                                                size, 
                                                order_column, 
                                                created_at, 
                                                updated_at
                                                )";

                $sql_gallery .= " VALUES ('$id', 
                                        '$model_type',
                                        'gallery_desktop', 
                                        '$value', 
                                        '$gallery_desktop_file_name[$key]', 
                                        '$gallery_desktop_type[$key]', 
                                        '$gallery_desktop_size[$key]', 
                                        '$key',
                                        '$date_now',
                                        '$date_now'
                                        )";
                $db->query($sql_gallery);
            }
            //echo gettype($gallery_desktop_name);
        }

        if (isset($_POST['document_file_name'])) {
            $document_file_name = $_POST['document_file_name'];
            $document_name = $_POST['document_name'];
            $document_file_size = $_POST['document_file_size'];
            $document_type = $_POST['document_type'];

            foreach ($document_file_name as $key => $value) {
                $path = '/static/media/' . $value;
                $sql_document = "INSERT INTO documents_download (article_id,
                                                status,
                                                created_at, 
                                                updated_at, 
                                                created_by, 
                                                file_name, 
                                                file_type, 
                                                file_path
                                                )";

                $sql_document .= " VALUES ('$id', 
                                        'publish',
                                        '$date_now', 
                                        '$date_now', 
                                        '$uid',
                                        '$value', 
                                        '$document_type[$key]', 
                                        '$path'
                                        )";
                $db->query($sql_document);
            }
        }

        if ($video_type == 'mp4') {
            if (isset($_POST['mp4_file_name'])) {
                $mp4_file_name = $_POST['mp4_file_name'];
                // $mp4_name = $_POST['mp4_name'];
                // $mp4_file_size = $_POST['mp4_size'];
                // $mp4_file_type = $_POST['mp4_type'];
                $path = '/static/media/' . $mp4_file_name[0];
                $sql = "UPDATE article SET video_path ='$path'"
                    . " WHERE id = $id";
                $db->query($sql);
            }
        } elseif ($video_type == 'youtube_embed') {
            //echo "Case111111";
            $video_case_embed = $db->real_escape_string($_POST['video_case_embed']);
            $sql_update = "UPDATE article SET video_path='$video_case_embed'"
                . " WHERE id = $id";
            //echo $sql_update;
            $db->query($sql_update);
        }
    } else {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
        $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการเพิ่มภาพกิจกรรม';
        $errMessage = $db->error;
        $response[KEY_ERROR_MESSAGE_MORE] = "$errMessage\nSQL: $sql";
    }

    $response['data'] = $_POST;
    $response['files'] = $_FILES;
    $response['sql'] = $sql;
    $response['slug'] = $slug;
}

function doUpdateActivity()
{
    global $db, $response;

    $date_now = date('Y-m-d H:i:s');
    $title = $db->real_escape_string($_POST['title']);
    $short_description = $db->real_escape_string($_POST['short_description']);
    $description = $db->real_escape_string($_POST['description']);
    $inputBeginDate = $db->real_escape_string($_POST['inputBeginDate']);
    $inputEndDate = $db->real_escape_string($_POST['inputEndDate']);

    if (!empty($inputBeginDate)) {
        $inputBeginDate = date('Y-m-d H:i:s', strtotime($inputBeginDate));
    }

    if (!empty($inputEndDate)) {
        $inputEndDate = date('Y-m-d H:i:s', strtotime($inputEndDate));
    }

    $meta_title = $db->real_escape_string($_POST['meta_title']);
    $meta_description = $db->real_escape_string($_POST['meta_description']);
    $meta_keywords = $db->real_escape_string($_POST['meta_keywords']);
    $featured = $db->real_escape_string($_POST['featured']);
    $status = $db->real_escape_string($_POST['status']);
    $uid = $db->real_escape_string($_POST['uid']);
    $video_type = $db->real_escape_string($_POST['video_type']);
    $itemId = $db->real_escape_string($_POST['itemId']);

    $sql = "UPDATE article SET title ='$title',short_description='$short_description',description='$description',featured ='$featured',"
        . " status = '$status', meta_title = '$meta_title', start_date = '$inputBeginDate', end_date = '$inputEndDate', "
        . " meta_keywords = '$meta_keywords', meta_description = '$meta_description', updated_at = '$date_now', updated_by = '$uid',video_type='$video_type' "
        . " WHERE id = $itemId";

    if ($result = $db->query($sql)) {
        $id = $itemId;

        $model_type = 'activity';
        if (isset($_POST['cover_desktop_file_name'])) {
            $cover_desktop_file_name = $db->real_escape_string($_POST['cover_desktop_file_name']);
            $cover_desktop_name = $db->real_escape_string($_POST['cover_desktop_name']);
            $cover_desktop_size = $db->real_escape_string($_POST['cover_desktop_size']);
            $cover_desktop_type = $db->real_escape_string($_POST['cover_desktop_type']);

            $sql_cover = "INSERT INTO media (model_id,
                                            model_type,
                                            collection_name, 
                                            name, 
                                            file_name, 
                                            mime_type, 
                                            size, 
                                            order_column, 
                                            created_at, 
                                            updated_at
                                            )";

            $sql_cover .= " VALUES ('$id', 
                                '$model_type',
                                'cover_desktop', 
                                '$cover_desktop_name', 
                                '$cover_desktop_file_name', 
                                '$cover_desktop_type', 
                                '$cover_desktop_size', 
                                '0',
                                '$date_now',
                                '$date_now'
                                )";
            $db->query($sql_cover);
        }

        if (isset($_POST['gallery_desktop_file_name'])) {
            $gallery_desktop_file_name = $_POST['gallery_desktop_file_name'];
            $gallery_desktop_name = $_POST['gallery_desktop_name'];
            $gallery_desktop_size = $_POST['gallery_desktop_size'];
            $gallery_desktop_type = $_POST['gallery_desktop_type'];

            foreach ($gallery_desktop_name as $key => $value) {
                $sql_gallery = "INSERT INTO media (model_id,
                                                model_type,
                                                collection_name, 
                                                name, 
                                                file_name, 
                                                mime_type, 
                                                size, 
                                                order_column, 
                                                created_at, 
                                                updated_at
                                                )";

                $sql_gallery .= " VALUES ('$id', 
                                        '$model_type',
                                        'gallery_desktop', 
                                        '$value', 
                                        '$gallery_desktop_file_name[$key]', 
                                        '$gallery_desktop_type[$key]', 
                                        '$gallery_desktop_size[$key]', 
                                        '$key',
                                        '$date_now',
                                        '$date_now'
                                        )";
                $db->query($sql_gallery);
            }
            //echo gettype($gallery_desktop_name);
        }

        if (isset($_POST['document_file_name'])) {
            $document_file_name = $_POST['document_file_name'];
            $document_name = $_POST['document_name'];
            $document_file_size = $_POST['document_file_size'];
            $document_type = $_POST['document_type'];

            foreach ($document_file_name as $key => $value) {
                $path = '/static/media/' . $value;
                $sql_document = "INSERT INTO documents_download (article_id,
                                                status,
                                                created_at, 
                                                updated_at, 
                                                created_by, 
                                                file_name, 
                                                file_type, 
                                                file_path
                                                )";

                $sql_document .= " VALUES ('$id', 
                                        'publish',
                                        '$date_now', 
                                        '$date_now', 
                                        '$uid',
                                        '$value', 
                                        '$document_type[$key]', 
                                        '$path'
                                        )";
                $db->query($sql_document);
            }
        }

        if ($video_type == 'mp4') {
            if (isset($_POST['mp4_file_name'])) {
                $mp4_file_name = $_POST['mp4_file_name'];
                // $mp4_name = $_POST['mp4_name'];
                // $mp4_file_size = $_POST['mp4_size'];
                // $mp4_file_type = $_POST['mp4_type'];
                $path = '/static/media/' . $mp4_file_name[0];
                $sql = "UPDATE article SET video_path ='$path'"
                    . " WHERE id = $id";
                $db->query($sql);
            }

        } elseif ($video_type == 'youtube_embed') {
            //echo "Case111111";
            $video_case_embed = $db->real_escape_string($_POST['video_case_embed']);
            $sql_update = "UPDATE article SET video_path='$video_case_embed'"
                . " WHERE id = $id";
            //echo $sql_update;
            $db->query($sql_update);
        }

        $response[KEY_ERROR_CODE] = ERROR_CODE_SUCCESS;
        $response[KEY_ERROR_MESSAGE] = 'แก้ไขภาพกิจกรรมสำเร็จ';
        $response[KEY_ERROR_MESSAGE_MORE] = '';
    } else {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
        $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการแก้ไขภาพกิจกรรม';
        $errMessage = $db->error;
        $response[KEY_ERROR_MESSAGE_MORE] = "$errMessage\nSQL: $sql";
    }

}

function doAddDocument()
{
    global $db, $response;

    $response['test'] = print_r($_FILES);
    return;

    $date_now = date('Y-m-d H:i:s');
    $title = $db->real_escape_string($_POST['title']);
    $short_description = $db->real_escape_string($_POST['short_description']);
    $status = $db->real_escape_string($_POST['status']);
    $uid = $db->real_escape_string($_POST['uid']);
    $doc_type = $db->real_escape_string($_POST['doc_type']);

    $sql = "INSERT INTO documents_download (title,
                                short_description, 
                                status, 
                                created_at,
                                updated_at,
                                created_by,
                                document_type
                                )";

    $sql .= " VALUES ('$title', 
                    '$short_description', 
                    '$status', 
                    '$date_now',
                    '$date_now',
                    '$uid',
                    '$doc_type'
                    )";

    if ($result = $db->query($sql)) {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SUCCESS;
        $response[KEY_ERROR_MESSAGE] = 'เพิ่มเอกสาร';
        $response[KEY_ERROR_MESSAGE_MORE] = '';
        $id = $db->insert_id;

        $model_type = 'document';
        if (isset($_POST['cover_desktop_file_name'])) {
            $cover_desktop_file_name = $db->real_escape_string($_POST['cover_desktop_file_name']);
            $cover_desktop_name = $db->real_escape_string($_POST['cover_desktop_name']);
            $cover_desktop_size = $db->real_escape_string($_POST['cover_desktop_size']);
            $cover_desktop_type = $db->real_escape_string($_POST['cover_desktop_type']);

            $sql_cover = "INSERT INTO media (model_id,
                                            model_type,
                                            collection_name, 
                                            name, 
                                            file_name, 
                                            mime_type, 
                                            size, 
                                            order_column, 
                                            created_at, 
                                            updated_at
                                            )";

            $sql_cover .= " VALUES ('$id', 
                                '$model_type',
                                'cover_desktop', 
                                '$cover_desktop_name', 
                                '$cover_desktop_file_name', 
                                '$cover_desktop_type', 
                                '$cover_desktop_size', 
                                '0',
                                '$date_now',
                                '$date_now'
                                )";
            $db->query($sql_cover);
        }

        if (isset($_POST['document_file_name'])) {
            $document_file_name = $_POST['document_file_name'];
            $document_name = $_POST['document_name'];
            $document_file_size = $_POST['document_file_size'];
            $document_type = $_POST['document_type'];

            foreach ($document_file_name as $key => $value) {
                $path = '/static/media/' . $value;
                $sql_document = "UPDATE documents_download SET file_name= '$value',file_type='$document_type[$key]',file_path='$path' WHERE id =$id";
                //echo $sql_document;
                $db->query($sql_document);
            }

        }
    } else {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
        $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการเพิ่มเอกสาร';
        $errMessage = $db->error;
        $response[KEY_ERROR_MESSAGE_MORE] = "$errMessage\nSQL: $sql";
    }

    $response['data'] = $_POST;
    $response['files'] = $_FILES;
    $response['sql'] = $sql;
}

function doUpdateDocument()
{
    global $db, $response;

    $date_now = date('Y-m-d H:i:s');
    $title = $db->real_escape_string($_POST['title']);
    $short_description = $db->real_escape_string($_POST['short_description']);
    $status = $db->real_escape_string($_POST['status']);
    $uid = $db->real_escape_string($_POST['uid']);
    $itemId = $db->real_escape_string($_POST['itemId']);
    $doc_type = $db->real_escape_string($_POST['doc_type']);

    $sql = "UPDATE documents_download SET title ='$title',short_description='$short_description',status='$status',updated_by='$uid'"
        . " WHERE id = $itemId";

    if ($result = $db->query($sql)) {
        $id = $itemId;
        $model_type = 'document';
        if (isset($_POST['cover_desktop_file_name'])) {

            $cover_desktop_file_name = $db->real_escape_string($_POST['cover_desktop_file_name']);
            $cover_desktop_name = $db->real_escape_string($_POST['cover_desktop_name']);
            $cover_desktop_size = $db->real_escape_string($_POST['cover_desktop_size']);
            $cover_desktop_type = $db->real_escape_string($_POST['cover_desktop_type']);

            $sql_cover = "INSERT INTO media (model_id,
                                            model_type,
                                            collection_name, 
                                            name, 
                                            file_name, 
                                            mime_type, 
                                            size, 
                                            order_column, 
                                            created_at, 
                                            updated_at
                                            )";

            $sql_cover .= " VALUES ('$id', 
                                '$model_type',
                                'cover_desktop', 
                                '$cover_desktop_name', 
                                '$cover_desktop_file_name', 
                                '$cover_desktop_type', 
                                '$cover_desktop_size', 
                                '0',
                                '$date_now',
                                '$date_now'
                                )";
            $db->query($sql_cover);
        }

        if (isset($_POST['document_file_name'])) {
            $document_file_name = $_POST['document_file_name'];
            $document_name = $_POST['document_name'];
            $document_file_size = $_POST['document_file_size'];
            $document_type = $_POST['document_type'];

            foreach ($document_file_name as $key => $value) {
                $path = '/static/media/' . $value;
                $sql_document = "UPDATE documents_download SET file_name= '$value',file_type='$document_type[$key]',file_path='$path' WHERE id =$id";
                //echo $sql_document;
                $db->query($sql_document);
            }
        }

        $response[KEY_ERROR_CODE] = ERROR_CODE_SUCCESS;
        $response[KEY_ERROR_MESSAGE] = 'แก้ไขเอกสารสำเร็จ';
        $response[KEY_ERROR_MESSAGE_MORE] = '';
    } else {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
        $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการแก้ไขเอกสาร';
        $errMessage = $db->error;
        $response[KEY_ERROR_MESSAGE_MORE] = "$errMessage\nSQL: $sql";
    }
}

function doAddFaq()
{
    global $db, $response;

    $date_now = date('Y-m-d H:i:s');

    $title = $db->real_escape_string($_POST['title']);
    $description = $db->real_escape_string($_POST['description']);
    $status = $db->real_escape_string($_POST['status']);
    $uid = $db->real_escape_string($_POST['uid']);

    $sql = "INSERT INTO faq (title,
                                description,
                                status, 
                                created_at,
                                updated_at,
                                created_by
                                )";

    $sql .= " VALUES ('$title', 
                    '$description', 
                    '$status', 
                    '$date_now',
                    '$date_now',
                    '$uid'
                    )";

    if ($result = $db->query($sql)) {

        $response[KEY_ERROR_CODE] = ERROR_CODE_SUCCESS;
        $response[KEY_ERROR_MESSAGE] = 'เพิ่มคำถามที่พบบ่อยสำเร็จ';
        $response[KEY_ERROR_MESSAGE_MORE] = '';
        $id = $db->insert_id;

    } else {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
        $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการเพิ่มคำถามที่พบบ่อย';
        $errMessage = $db->error;
        $response[KEY_ERROR_MESSAGE_MORE] = "$errMessage\nSQL: $sql";
    }

    $response['data'] = $_POST;
    $response['sql'] = $sql;
}

function doUpdateFaq()
{
    global $db, $response;

    $date_now = date('Y-m-d H:i:s');
    $title = $db->real_escape_string($_POST['title']);
    $description = $db->real_escape_string($_POST['description']);
    $status = $db->real_escape_string($_POST['status']);
    $uid = $db->real_escape_string($_POST['uid']);
    $itemId = $db->real_escape_string($_POST['itemId']);

    $sql = "UPDATE faq SET title ='$title',description='$description',"
        . " status = '$status',"
        . " updated_at = '$date_now', updated_by = '$uid' "
        . " WHERE id = $itemId";

    if ($result = $db->query($sql)) {

        $id = $itemId;
        $response[KEY_ERROR_CODE] = ERROR_CODE_SUCCESS;
        $response[KEY_ERROR_MESSAGE] = 'แก้ไขคำถามที่พบบ่อยสำเร็จ';
        $response[KEY_ERROR_MESSAGE_MORE] = '';
    } else {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
        $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการแก้ไขคำถามที่พบบ่อย';
        $errMessage = $db->error;
        $response[KEY_ERROR_MESSAGE_MORE] = "$errMessage\nSQL: $sql";
    }
}

function doDeleteMedia()
{
    global $db, $response;

    $id = $db->real_escape_string($_POST['id']);
    $file_name = $db->real_escape_string($_POST['file_name']);
    $sql = "DELETE FROM media WHERE id=$id";

    if ($result = $db->query($sql)) {

        $target_path = $_SERVER['DOCUMENT_ROOT'] . "static/media/" . $file_name;
        unlink($target_path);

        $response[KEY_ERROR_CODE] = ERROR_CODE_SUCCESS;
        $response[KEY_ERROR_MESSAGE] = 'ลบรูปสำเร็จ';
        $response[KEY_ERROR_MESSAGE_MORE] = '';
    } else {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
        $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการลบรูป';
        $errMessage = $db->error;
        $response[KEY_ERROR_MESSAGE_MORE] = "$errMessage\nSQL: $sql";
    }

}

function doDeleteDocument()
{
    global $db, $response;

    $id = $db->real_escape_string($_POST['id']);
    $file_name = $db->real_escape_string($_POST['file_name']);
    $sql = "DELETE FROM documents_download WHERE id=$id";

    if ($result = $db->query($sql)) {

        $target_path = $_SERVER['DOCUMENT_ROOT'] . "static/media/" . $file_name;
        unlink($target_path);

        $response[KEY_ERROR_CODE] = ERROR_CODE_SUCCESS;
        $response[KEY_ERROR_MESSAGE] = 'ลบไฟล์แนบสำเร็จ';
        $response[KEY_ERROR_MESSAGE_MORE] = '';
    } else {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
        $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการลบไฟล์แนบ';
        $errMessage = $db->error;
        $response[KEY_ERROR_MESSAGE_MORE] = "$errMessage\nSQL: $sql";
    }
}

function doDeleteMp4()
{
    global $db, $response;

    $id = $db->real_escape_string($_POST['id']);
    $file_name = $db->real_escape_string($_POST['file_name']);
    //$sql = "DELETE FROM documents_download WHERE id=$id";
    $sql = "UPDATE article SET video_path =''"
        . " WHERE id = $id";

    if ($result = $db->query($sql)) {

        $target_path = $_SERVER['DOCUMENT_ROOT'] . $file_name;
        unlink($target_path);

        $response[KEY_ERROR_CODE] = ERROR_CODE_SUCCESS;
        $response[KEY_ERROR_MESSAGE] = 'ลบไฟล์แนบสำเร็จ';
        $response[KEY_ERROR_MESSAGE_MORE] = '';
    } else {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
        $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการลบไฟล์แนบ';
        $errMessage = $db->error;
        $response[KEY_ERROR_MESSAGE_MORE] = "$errMessage\nSQL: $sql";
    }

}

function doDeleteArticle($articleId)
{
    global $db, $response;

    $id = $articleId;

    // $sql_media = "DELETE FROM media WHERE id=$id";
    // $db->query($sql_media);

    $sql_document = "DELETE FROM documents_download WHERE id=$id";
    $db->query($sql_document);

    $sql_article = "DELETE FROM article WHERE id=$id";

    if ($result = $db->query($sql_article)) {

        // $target_path = $_SERVER['DOCUMENT_ROOT'] . "static/media/".$file_name;
        // unlink($target_path);

        $response[KEY_ERROR_CODE] = ERROR_CODE_SUCCESS;
        $response[KEY_ERROR_MESSAGE] = 'ลบข้อมูลสำเร็จ';
        $response[KEY_ERROR_MESSAGE_MORE] = '';
    } else {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
        $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการลบข้อมูล';
        $errMessage = $db->error;
        $response[KEY_ERROR_MESSAGE_MORE] = "$errMessage\nSQL: $sql";
    }
    //$response['id'] = $id;

}

function doDeleteMenu($menuId)
{
    global $db, $response;

    $id = $menuId;

    // $sql_media = "DELETE FROM media WHERE id=$id";
    // $db->query($sql_media);

    $sql_menu = "DELETE FROM menus WHERE id=$id";

    if ($result = $db->query($sql_menu)) {

        // $target_path = $_SERVER['DOCUMENT_ROOT'] . "static/media/".$file_name;
        // unlink($target_path);

        $response[KEY_ERROR_CODE] = ERROR_CODE_SUCCESS;
        $response[KEY_ERROR_MESSAGE] = 'ลบข้อมูลสำเร็จ';
        $response[KEY_ERROR_MESSAGE_MORE] = '';
    } else {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
        $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการลบข้อมูล';
        $errMessage = $db->error;
        $response[KEY_ERROR_MESSAGE_MORE] = "$errMessage\nSQL: $sql";
    }
    //$response['id'] = $id;

}

function doDeleteBanner($bannerId)
{
    global $db, $response;

    $id = $bannerId;

    // $sql_media = "DELETE FROM media WHERE id=$id";
    // $db->query($sql_media);

    $sql_media = "DELETE FROM media WHERE model_id=$id AND model_type='banner'";
    $db->query($sql_media);

    $sql_banner = "DELETE FROM banners WHERE id=$id";

    if ($result = $db->query($sql_banner)) {

        // $target_path = $_SERVER['DOCUMENT_ROOT'] . "static/media/".$file_name;
        // unlink($target_path);

        $response[KEY_ERROR_CODE] = ERROR_CODE_SUCCESS;
        $response[KEY_ERROR_MESSAGE] = 'ลบข้อมูลสำเร็จ';
        $response[KEY_ERROR_MESSAGE_MORE] = '';
    } else {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
        $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการลบข้อมูล';
        $errMessage = $db->error;
        $response[KEY_ERROR_MESSAGE_MORE] = "$errMessage\nSQL: $sql";
    }
    //$response['id'] = $id;

}

function doDeleteFaq($faqId)
{
    global $db, $response;

    $id = $faqId;
    $sql_faq = "DELETE FROM faq WHERE id=$id";

    if ($result = $db->query($sql_faq)) {

        // $target_path = $_SERVER['DOCUMENT_ROOT'] . "static/media/".$file_name;
        // unlink($target_path);

        $response[KEY_ERROR_CODE] = ERROR_CODE_SUCCESS;
        $response[KEY_ERROR_MESSAGE] = 'ลบข้อมูลสำเร็จ';
        $response[KEY_ERROR_MESSAGE_MORE] = '';
    } else {
        $response[KEY_ERROR_CODE] = ERROR_CODE_SQL_ERROR;
        $response[KEY_ERROR_MESSAGE] = 'เกิดข้อผิดพลาดในการลบข้อมูล';
        $errMessage = $db->error;
        $response[KEY_ERROR_MESSAGE_MORE] = "$errMessage\nSQL: $sql";
    }
    //$response['id'] = $id;
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

function createSlug($table_name, $field_name, $title)
{
    global $db;

    $slug = preg_replace("/-$/", "", preg_replace('/[^a-z0-9ก-๙เแ“”]+/i', "-", strtolower($title)));
    $sql = "SELECT COUNT(*) AS NumHits FROM $table_name WHERE  $field_name  LIKE '$slug%'";
    $result = $db->query($sql);
    $row = $result->fetch_assoc();
    $numHits = $row['NumHits'];

    return ($numHits > 0) ? ($slug . '-' . $numHits) : $slug;
}

?>