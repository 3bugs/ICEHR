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
    case 'add_training_project_news':
        doAddTrainingProjectNews();
        break;
    case 'update_training_project_news':
        doUpdateTrainingProjectNews();
        break;
    case 'add_news':
        doAddNews();
        break;
    case 'update_news':
        doUpdateNews();
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


function doAddTrainingProjectNews()
{
    global $db, $response;

    $date_now = date('Y-m-d H:i:s');

    $title = $db->real_escape_string($_POST['title']);
    $short_description = $db->real_escape_string($_POST['short_description']);
    $description = $db->real_escape_string($_POST['description']);
    $inputBeginDate = $db->real_escape_string($_POST['inputBeginDate']);
    $inputEndDate = $db->real_escape_string($_POST['inputEndDate']);

    if(!empty($inputBeginDate)){
        $inputBeginDate = date('Y-m-d H:i:s',strtotime($inputBeginDate));
    }

    if(!empty($inputEndDate)){
        $inputEndDate = date('Y-m-d H:i:s',strtotime($inputEndDate));
    }

    $meta_title = $db->real_escape_string($_POST['meta_title']);
    $meta_description = $db->real_escape_string($_POST['meta_description']);
    $meta_keywords = $db->real_escape_string($_POST['meta_keywords']);
    $featured = $db->real_escape_string($_POST['featured']);
    $status = $db->real_escape_string($_POST['status']);
    $uid = $db->real_escape_string($_POST['uid']);

    $slug  = createSlug('article','slug',$title);

    
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
        if(isset($_POST['cover_desktop_file_name'])){

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
    
        if(isset($_POST['gallery_desktop_file_name'])){
    
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
    
    
        if(isset($_POST['document_file_name'])){
    
            $document_file_name = $_POST['document_file_name'];
            $document_name = $_POST['document_name'];
            $document_file_size = $_POST['document_file_size'];
            $document_type = $_POST['document_type'];

            foreach ($document_file_name as $key => $value) {
               
                $path ='/static/media/'.$value;
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

    if(!empty($inputBeginDate)){
        $inputBeginDate = date('Y-m-d H:i:s',strtotime($inputBeginDate));
    }

    if(!empty($inputEndDate)){
        $inputEndDate = date('Y-m-d H:i:s',strtotime($inputEndDate));
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
        if(isset($_POST['cover_desktop_file_name'])){

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
    
        if(isset($_POST['gallery_desktop_file_name'])){
    
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
    
    
        if(isset($_POST['document_file_name'])){
    
            $document_file_name = $_POST['document_file_name'];
            $document_name = $_POST['document_name'];
            $document_file_size = $_POST['document_file_size'];
            $document_type = $_POST['document_type'];

            foreach ($document_file_name as $key => $value) {
               
                $path ='/static/media/'.$value;
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


function doAddNews()
{
    global $db, $response;

    $date_now = date('Y-m-d H:i:s');

    $title = $db->real_escape_string($_POST['title']);
    $short_description = $db->real_escape_string($_POST['short_description']);
    $description = $db->real_escape_string($_POST['description']);
    $inputBeginDate = $db->real_escape_string($_POST['inputBeginDate']);
    $inputEndDate = $db->real_escape_string($_POST['inputEndDate']);

    if(!empty($inputBeginDate)){
        $inputBeginDate = date('Y-m-d H:i:s',strtotime($inputBeginDate));
    }

    if(!empty($inputEndDate)){
        $inputEndDate = date('Y-m-d H:i:s',strtotime($inputEndDate));
    }

    $meta_title = $db->real_escape_string($_POST['meta_title']);
    $meta_description = $db->real_escape_string($_POST['meta_description']);
    $meta_keywords = $db->real_escape_string($_POST['meta_keywords']);
    $featured = $db->real_escape_string($_POST['featured']);
    $status = $db->real_escape_string($_POST['status']);
    $uid = $db->real_escape_string($_POST['uid']);

    $slug  = createSlug('article','slug',$title);

    
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
        if(isset($_POST['cover_desktop_file_name'])){

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
    
        if(isset($_POST['gallery_desktop_file_name'])){
    
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
    
    
        if(isset($_POST['document_file_name'])){
    
            $document_file_name = $_POST['document_file_name'];
            $document_name = $_POST['document_name'];
            $document_file_size = $_POST['document_file_size'];
            $document_type = $_POST['document_type'];

            foreach ($document_file_name as $key => $value) {
               
                $path ='/static/media/'.$value;
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

function doUpdateNews()
{
    global $db, $response;

    $date_now = date('Y-m-d H:i:s');
    $title = $db->real_escape_string($_POST['title']);
    $short_description = $db->real_escape_string($_POST['short_description']);
    $description = $db->real_escape_string($_POST['description']);
    $inputBeginDate = $db->real_escape_string($_POST['inputBeginDate']);
    $inputEndDate = $db->real_escape_string($_POST['inputEndDate']);

    if(!empty($inputBeginDate)){
        $inputBeginDate = date('Y-m-d H:i:s',strtotime($inputBeginDate));
    }

    if(!empty($inputEndDate)){
        $inputEndDate = date('Y-m-d H:i:s',strtotime($inputEndDate));
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
        if(isset($_POST['cover_desktop_file_name'])){

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
    
        if(isset($_POST['gallery_desktop_file_name'])){
    
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
    
    
        if(isset($_POST['document_file_name'])){
    
            $document_file_name = $_POST['document_file_name'];
            $document_name = $_POST['document_name'];
            $document_file_size = $_POST['document_file_size'];
            $document_type = $_POST['document_type'];

            foreach ($document_file_name as $key => $value) {
               
                $path ='/static/media/'.$value;
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

    $slug  = createSlug('menus','slug',$title);

    
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
        if(isset($_POST['cover_desktop_file_name'])){

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
        if(isset($_POST['cover_desktop_file_name'])){

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
        if(isset($_POST['cover_desktop_file_name'])){

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
        if(isset($_POST['cover_desktop_file_name'])){

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

    $slug  = createSlug('research_and_academic','slug',$title);

    
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

        
        if(isset($_POST['document_file_name'])){
    
            $document_file_name = $_POST['document_file_name'];
            $document_name = $_POST['document_name'];
            $document_file_size = $_POST['document_file_size'];
            $document_type = $_POST['document_type'];
            $file_type = $_POST['file_type'];
            

            foreach ($document_file_name as $key => $value) {
               
                $path ='/static/media/'.$value;
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

        if(isset($_POST['document_file_name'])){
    
            $document_file_name = $_POST['document_file_name'];
            $document_name = $_POST['document_name'];
            $document_file_size = $_POST['document_file_size'];
            $document_type = $_POST['document_type'];
            $file_type = $_POST['file_type'];

            foreach ($document_file_name as $key => $value) {
               
                $path ='/static/media/'.$value;
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
    $slug  = createSlug('article','slug',$title);

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
        if(isset($_POST['cover_desktop_file_name'])){

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
        if(isset($_POST['cover_desktop_file_name'])){

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

    $slug  = createSlug('article','slug',$title);

    
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
        if(isset($_POST['cover_desktop_file_name'])){

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
        if(isset($_POST['cover_desktop_file_name'])){

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
    $slug  = createSlug('article','slug',$title);

    
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

    if(!empty($inputBeginDate)){
        $inputBeginDate = date('Y-m-d H:i:s',strtotime($inputBeginDate));
    }

    if(!empty($inputEndDate)){
        $inputEndDate = date('Y-m-d H:i:s',strtotime($inputEndDate));
    }

    $meta_title = $db->real_escape_string($_POST['meta_title']);
    $meta_description = $db->real_escape_string($_POST['meta_description']);
    $meta_keywords = $db->real_escape_string($_POST['meta_keywords']);
    $featured = $db->real_escape_string($_POST['featured']);
    $status = $db->real_escape_string($_POST['status']);
    $uid = $db->real_escape_string($_POST['uid']);
    $video_type = $db->real_escape_string($_POST['video_type']);
    $slug  = createSlug('article','slug',$title);

    
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
        if(isset($_POST['cover_desktop_file_name'])){

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
    
        if(isset($_POST['gallery_desktop_file_name'])){
    
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
    
    
        if(isset($_POST['document_file_name'])){
    
            $document_file_name = $_POST['document_file_name'];
            $document_name = $_POST['document_name'];
            $document_file_size = $_POST['document_file_size'];
            $document_type = $_POST['document_type'];

            foreach ($document_file_name as $key => $value) {
               
                $path ='/static/media/'.$value;
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


        if($video_type == 'mp4'){

            if(isset($_POST['mp4_file_name'])){
    
                $mp4_file_name = $_POST['mp4_file_name'];
                // $mp4_name = $_POST['mp4_name'];
                // $mp4_file_size = $_POST['mp4_size'];
                // $mp4_file_type = $_POST['mp4_type'];
                $path ='/static/media/'.$mp4_file_name[0];
                $sql = "UPDATE article SET video_path ='$path'"
                . " WHERE id = $id";
                $db->query($sql);

            }

        }elseif ($video_type == 'youtube_embed'){
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

    if(!empty($inputBeginDate)){
        $inputBeginDate = date('Y-m-d H:i:s',strtotime($inputBeginDate));
    }

    if(!empty($inputEndDate)){
        $inputEndDate = date('Y-m-d H:i:s',strtotime($inputEndDate));
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
        if(isset($_POST['cover_desktop_file_name'])){

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
    
        if(isset($_POST['gallery_desktop_file_name'])){
    
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
    
    
        if(isset($_POST['document_file_name'])){
    
            $document_file_name = $_POST['document_file_name'];
            $document_name = $_POST['document_name'];
            $document_file_size = $_POST['document_file_size'];
            $document_type = $_POST['document_type'];

            foreach ($document_file_name as $key => $value) {
               
                $path ='/static/media/'.$value;
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

        if($video_type == 'mp4'){

            if(isset($_POST['mp4_file_name'])){
    
                $mp4_file_name = $_POST['mp4_file_name'];
                // $mp4_name = $_POST['mp4_name'];
                // $mp4_file_size = $_POST['mp4_size'];
                // $mp4_file_type = $_POST['mp4_type'];
                $path ='/static/media/'.$mp4_file_name[0];
                $sql = "UPDATE article SET video_path ='$path'"
                . " WHERE id = $id";
                $db->query($sql);

            }

        }elseif ($video_type == 'youtube_embed'){
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
        if(isset($_POST['cover_desktop_file_name'])){

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
    
        if(isset($_POST['document_file_name'])){
    
            $document_file_name = $_POST['document_file_name'];
            $document_name = $_POST['document_name'];
            $document_file_size = $_POST['document_file_size'];
            $document_type = $_POST['document_type'];

            foreach ($document_file_name as $key => $value) {
            
                $path ='/static/media/'.$value;
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
        if(isset($_POST['cover_desktop_file_name'])){

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

        if(isset($_POST['document_file_name'])){
    
            $document_file_name = $_POST['document_file_name'];
            $document_name = $_POST['document_name'];
            $document_file_size = $_POST['document_file_size'];
            $document_type = $_POST['document_type'];

            foreach ($document_file_name as $key => $value) {
            
                $path ='/static/media/'.$value;
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

        $target_path = $_SERVER['DOCUMENT_ROOT'] . "static/media/".$file_name;
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

        $target_path = $_SERVER['DOCUMENT_ROOT'] . "static/media/".$file_name;
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

        $target_path = $_SERVER['DOCUMENT_ROOT'].$file_name;
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


function createSlug($table_name,$field_name,$title){

    global $db;

    $slug = preg_replace("/-$/","",preg_replace('/[^a-z0-9ก-๙เแ“”]+/i', "-", strtolower($title)));
    $sql = "SELECT COUNT(*) AS NumHits FROM $table_name WHERE  $field_name  LIKE '$slug%'";
    $result = $db->query($sql);
    $row = $result->fetch_assoc();
    $numHits = $row['NumHits'];

    return ($numHits > 0) ? ($slug . '-' . $numHits) : $slug;

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