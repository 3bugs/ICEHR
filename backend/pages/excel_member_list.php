<?php
require_once '../vendor/autoload.php';
require_once '../include/head_php.inc';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/*
- คำนำหน้า, ชื่อ, นามสกุล
- เบอร์โทร
- อีเมล
- ตำแหน่งงาน (บุคคลทั่วไป ไม่บังคับกรอก)
- ชื่อหน่วยงาน (บุคคลทั่วไป ไม่บังคับกรอก)
- ประเภทหน่วยงาน (บุคคลทั่วไป ไม่บังคับกรอก)
- ที่อยู่หน่วยงาน (มีเฉพาะสมาชิกแบบองค์กร/บริษัท)
- เบอร์โทรหน่วยงาน (มีเฉพาะสมาชิกแบบองค์กร/บริษัท)
 */

$memberTypeOrganization = MEMBER_TYPE_ORGANIZATION;
$memberTypePerson = MEMBER_TYPE_PERSON;

$month = $_GET['month'];
$year = $_GET['year'];

$whereDate = ' TRUE ';
$title = 'รายชื่อสมาชิกเว็บไซต์ทั้งหมด';
$titleTrainee = 'รายชื่อผู้สมัครอบรมทั้งหมด';
$noDataText = 'ไม่มีข้อมูลสมาชิกเว็บไซต์และผู้สมัครอบรมในฐานข้อมูล';

if (isset($month) && isset($year)) {
    if ((int)$month === 0 && (int)$year === 0) {
        $whereDate = ' TRUE ';
        $whereDateTrainee = ' TRUE ';
        $title = 'รายชื่อสมาชิกเว็บไซต์ทั้งหมด';
        $titleTrainee = 'รายชื่อผู้สมัครอบรมทั้งหมด';
        $noDataText = 'ไม่มีข้อมูลสมาชิกเว็บไซต์และผู้สมัครอบรมในฐานข้อมูล';
    } elseif ((int)$month === 0) {
        $whereDate = " YEAR(m.created_at) = {$year} ";
        $whereDateTrainee = " YEAR(cr.created_at) = {$year} ";
        $yearBe = (int)$year + 543;
        $title = "รายชื่อสมาชิกเว็บไซต์ที่สมัครในปี พ.ศ. {$yearBe}";
        $titleTrainee = "รายชื่อผู้สมัครอบรมที่สมัครในปี พ.ศ. {$yearBe}";
        $noDataText = "ไม่มีข้อมูลสมาชิกเว็บไซต์และผู้สมัครอบรมที่สมัครในปี พ.ศ. {$yearBe}";
    } else {
        $whereDate = " MONTH(m.created_at) = {$month} AND YEAR(m.created_at) = {$year} ";
        $whereDateTrainee = " MONTH(cr.created_at) = {$month} AND YEAR(cr.created_at) = {$year} ";
        $monthName = $monthNames[$month - 1];
        $yearBe = (int)$year + 543;
        $title = "รายชื่อสมาชิกเว็บไซต์ที่สมัครในเดือน{$monthName} ปี พ.ศ. {$yearBe}";
        $titleTrainee = "รายชื่อผู้สมัครอบรมที่สมัครในเดือน{$monthName} ปี พ.ศ. {$yearBe}";
        $noDataText = "ไม่มีข้อมูลสมาชิกเว็บไซต์และผู้สมัครอบรมที่สมัครในเดือน{$monthName} ปี พ.ศ. {$yearBe}";
    }
}

$sql = "SELECT m.title, m.first_name, m.last_name, m.phone, m.email, m.job_position,  
               m.organization_type_custom, ot.name AS organization_type_name, m.organization_name,
               m.address, m.sub_district, m.district, m.province, m.postal_code, m.organization_phone,
               DATE_FORMAT(m.created_at, '%d/%m/%Y') AS created_at
        FROM member m 
            LEFT JOIN organization_type ot 
                ON ot.id = m.organization_type 
        WHERE m.member_type = '$memberTypeOrganization' AND $whereDate
        ORDER BY m.id";

$organizationMemberList = array();
if ($result = $db->query($sql)) {
    while ($row = $result->fetch_assoc()) {
        array_push($organizationMemberList, $row);
    }
    $result->close();
} else {
    header('Content-Type: text/html');
    echo 'Error: เกิดข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล - ' . $db->error;
    $db->close();
    exit();
}

$sql = "SELECT m.title, m.first_name, m.last_name, m.phone, m.email, m.job_position, 
               m.organization_type_custom, ot.name AS organization_type_name, m.organization_name,
               DATE_FORMAT(m.created_at, '%d/%m/%Y') AS created_at
        FROM member m 
            LEFT JOIN organization_type ot 
                ON ot.id = m.organization_type 
        WHERE m.member_type = '$memberTypePerson' AND $whereDate
        ORDER BY m.id";

$personMemberList = array();
if ($result = $db->query($sql)) {
    while ($row = $result->fetch_assoc()) {
        array_push($personMemberList, $row);
    }
    $result->close();
} else {
    header('Content-Type: text/html');
    echo 'Error: เกิดข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล - ' . $db->error;
    $db->close();
    exit();
}

$traineeList = array();

/*วิชาการ*/
$sql = "SELECT ct.title, ct.first_name, ct.last_name, ct.birth_date, TIMESTAMPDIFF(YEAR, ct.birth_date, CURDATE()) AS age, 
               ct.phone, ct.email, ct.job_position, ct.organization_name, DATE_FORMAT(cr.created_at, '%d/%m/%Y') AS created_at,
               CONCAT_WS(' ', cr.receipt_address, cr.receipt_sub_district, cr.receipt_district, cr.receipt_province, cr.receipt_postal_code) AS full_address,
               cr.receipt_address AS address, cr.receipt_sub_district AS sub_district, cr.receipt_district AS district, cr.receipt_province AS province, cr.receipt_postal_code AS postal_code,
               c.id AS course_id, cm.title AS course_title, c.batch_number, cm.service_type
        FROM course_trainee ct 
            INNER JOIN course_registration cr 
                ON cr.id = ct.course_registration_id 
            INNER JOIN course c 
                ON c.id = cr.course_id 
            INNER JOIN course_master cm 
                ON cm.id = c.course_master_id 
        WHERE $whereDateTrainee AND ct.register_status <> 'cancel'
        ORDER BY c.begin_date DESC, c.id, ct.first_name, ct.last_name";
if ($result = $db->query($sql)) {
    while ($row = $result->fetch_assoc()) {
        array_push($traineeList, $row);
    }
    $result->close();
} else {
    header('Content-Type: text/html');
    echo 'เกิดข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล (ดึงข้อมูลผู้สมัครอบรม-วิชาการ): ' . $db->error;
    $db->close();
    exit();
}

/*สังคม*/
$sql = "SELECT cr.title, cr.first_name, cr.last_name, cr.birth_date, TIMESTAMPDIFF(YEAR, cr.birth_date, CURDATE()) AS age, 
               cr.phone, cr.email, cr.occupation AS job_position, cr.work_place AS organization_name, DATE_FORMAT(cr.created_at, '%d/%m/%Y') AS created_at,
               CONCAT_WS(' ', cr.address, cr.sub_district, cr.district, cr.province, cr.postal_code) AS full_address,
               cr.address AS address, cr.sub_district AS sub_district, cr.district AS district, cr.province AS province, cr.postal_code AS postal_code,
               c.id AS course_id, cm.title AS course_title, c.batch_number, cm.service_type
        FROM course_registration_social cr  
            INNER JOIN course c 
                ON c.id = cr.course_id 
            INNER JOIN course_master cm 
                ON cm.id = c.course_master_id 
        WHERE $whereDateTrainee AND cr.register_status <> 'cancel'
        ORDER BY c.begin_date DESC, c.id, cr.first_name, cr.last_name";
if ($result = $db->query($sql)) {
    while ($row = $result->fetch_assoc()) {
        array_push($traineeList, $row);
    }
    $result->close();
} else {
    header('Content-Type: text/html');
    echo 'เกิดข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล (ดึงข้อมูลผู้สมัครอบรม-สังคม): ' . $db->error;
    $db->close();
    exit();
}

/*ใบขับขี่*/
$sql = "SELECT cr.title, cr.first_name, cr.last_name, cr.phone, DATE_FORMAT(cr.created_at, '%d/%m/%Y') AS created_at,
               CONCAT_WS(' ', cr.address, cr.moo, cr.soi, cr.road, cr.sub_district, cr.district, cr.province) AS full_address, 
               cr.address, cr.moo, cr.soi, cr.road, cr.sub_district, cr.district, cr.province,
               c.id AS course_id, cm.title AS course_title, c.batch_number, cm.service_type
        FROM course_registration_driving_license cr  
            INNER JOIN course c 
                ON c.id = cr.course_id 
            INNER JOIN course_master cm 
                ON cm.id = c.course_master_id 
        WHERE $whereDateTrainee AND cr.register_status <> 'cancel'
        ORDER BY c.begin_date DESC, c.id, cr.first_name, cr.last_name";
if ($result = $db->query($sql)) {
    while ($row = $result->fetch_assoc()) {
        array_push($traineeList, $row);
    }
    $result->close();
} else {
    header('Content-Type: text/html');
    echo 'เกิดข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล (ดึงข้อมูลผู้สมัครอบรม-ใบขับขี่): ' . $db->error;
    $db->close();
    exit();
}

if (empty($organizationMemberList) && empty($personMemberList) && empty($traineeList)) {
    header('Content-Type: text/html');
    echo $noDataText;
    $db->close();
    exit();
}

$today = date('d-M-Y');
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="member_list' . "_{$today}" . '.xls"');
header('Cache-Control: max-age=0');

define('MEMBER_TYPE_ORGANIZATION', 'Organization');
define('MEMBER_TYPE_PERSON', 'Person');

$spreadsheet = new Spreadsheet();

$sheet = $spreadsheet->getActiveSheet();
generateSheet($organizationMemberList, MEMBER_TYPE_ORGANIZATION, $sheet);

$sheet = $spreadsheet->createSheet();
generateSheet($personMemberList, MEMBER_TYPE_PERSON, $sheet);

$sheet = $spreadsheet->createSheet();
generateTraineeSheet($traineeList, $sheet);

//$writer = new Xlsx($spreadsheet);
$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xls');

//$writer->save('hello world.xlsx');
$writer->save("php://output");

require_once '../include/foot_php.inc';

function generateSheet($memberList, $memberType, $sheet)
{
    global $title;

    $sheet->setTitle(ucfirst($memberType));
    if (empty($memberList)) {
        $sheet->setCellValueByColumnAndRow(1, 1, 'ไม่มีข้อมูล')->getStyleByColumnAndRow(1, 1)->getAlignment()->setVertical('top')->setHorizontal('left');
        return;
    }

    if ($memberType === MEMBER_TYPE_ORGANIZATION) {
        $sheet->mergeCells("A1:L1");
    } else {
        $sheet->mergeCells("A1:I1");
    }
    $sheet->setCellValueByColumnAndRow(1, 1, $title)->getStyleByColumnAndRow(1, 1)->getAlignment()->setVertical('top')->setHorizontal('left');

    $sheet->freezePane('A3');

    $sheet->getStyle("A1:Z1")->getFont()->setBold(true);
    $sheet->getStyle("A2:Z2")->getFont()->setBold(true);

    $sheet->getColumnDimension('A')->setAutoSize(true);
    $sheet->getColumnDimension('B')->setAutoSize(true);
    $sheet->getColumnDimension('C')->setAutoSize(true);
    $sheet->getColumnDimension('D')->setAutoSize(true);
    $sheet->getColumnDimension('E')->setAutoSize(true);
    $sheet->getColumnDimension('F')->setAutoSize(true);
    $sheet->getColumnDimension('G')->setAutoSize(true);
    $sheet->getColumnDimension('H')->setAutoSize(true);
    $sheet->getColumnDimension('I')->setAutoSize(true);

    if ($memberType === MEMBER_TYPE_ORGANIZATION) {
        $sheet->getColumnDimension('J')->setAutoSize(true);
        $sheet->getColumnDimension('K')->setAutoSize(true);
        $sheet->getColumnDimension('L')->setAutoSize(true);
    }

    $row = 2;
    $sheet->setCellValueByColumnAndRow(1, $row, 'ลำดับ')->getStyleByColumnAndRow(1, $row)->getAlignment()->setHorizontal('center');;
    $sheet->setCellValueByColumnAndRow(2, $row, 'คำนำหน้าชื่อ')->getStyleByColumnAndRow(2, $row)->getAlignment()->setHorizontal('center');;
    $sheet->setCellValueByColumnAndRow(3, $row, 'ชื่อ-นามสกุล')->getStyleByColumnAndRow(3, $row)->getAlignment()->setHorizontal('center');
    $sheet->setCellValueByColumnAndRow(4, $row, 'เบอร์โทร')->getStyleByColumnAndRow(4, $row)->getAlignment()->setHorizontal('center');
    $sheet->setCellValueByColumnAndRow(5, $row, 'อีเมล')->getStyleByColumnAndRow(5, $row)->getAlignment()->setHorizontal('center');
    $sheet->setCellValueByColumnAndRow(6, $row, 'ตำแหน่งงาน')->getStyleByColumnAndRow(6, $row)->getAlignment()->setHorizontal('center');
    $sheet->setCellValueByColumnAndRow(7, $row, 'ชื่อหน่วยงาน')->getStyleByColumnAndRow(7, $row)->getAlignment()->setHorizontal('center');
    $sheet->setCellValueByColumnAndRow(8, $row, 'ประเภทหน่วยงาน')->getStyleByColumnAndRow(8, $row)->getAlignment()->setHorizontal('center');
    if ($memberType === MEMBER_TYPE_ORGANIZATION) {
        $sheet->setCellValueByColumnAndRow(9, $row, 'ที่อยู่หน่วยงาน')->getStyleByColumnAndRow(9, $row)->getAlignment()->setHorizontal('center');
        $sheet->setCellValueByColumnAndRow(10, $row, 'จังหวัด')->getStyleByColumnAndRow(10, $row)->getAlignment()->setHorizontal('center');
        $sheet->setCellValueByColumnAndRow(11, $row, 'เบอร์โทรหน่วยงาน')->getStyleByColumnAndRow(11, $row)->getAlignment()->setHorizontal('center');
        $sheet->setCellValueByColumnAndRow(12, $row, 'วันที่สมัคร')->getStyleByColumnAndRow(12, $row)->getAlignment()->setHorizontal('center');
    } else {
        $sheet->setCellValueByColumnAndRow(9, $row, 'วันที่สมัคร')->getStyleByColumnAndRow(9, $row)->getAlignment()->setHorizontal('center');
    }
    $sheet->getRowDimension($row)->setRowHeight(-1); // set auto height

    define('START_ROW', 3);
    $row = START_ROW;
    foreach ($memberList as $member) {
        $sheet->setCellValueByColumnAndRow(1, $row, ($row - START_ROW) + 1)->getStyleByColumnAndRow(1, $row)->getAlignment()->setVertical('top')->setHorizontal('center');

        $sheet->setCellValueByColumnAndRow(2, $row, $member['title'])->getStyleByColumnAndRow(2, $row)->getAlignment()->setVertical('top')->setHorizontal('left');

        $displayName = "{$member['title']}{$member['first_name']} {$member['last_name']}";
        $sheet->setCellValueByColumnAndRow(3, $row, $displayName)->getStyleByColumnAndRow(3, $row)->getAlignment()->setVertical('top');

        $sheet->setCellValueByColumnAndRow(4, $row, $member['phone'])->getStyleByColumnAndRow(4, $row)->getAlignment()->setVertical('top')->setHorizontal('left');
        $sheet->setCellValueByColumnAndRow(5, $row, $member['email'])->getStyleByColumnAndRow(5, $row)->getAlignment()->setVertical('top');
        $sheet->setCellValueByColumnAndRow(6, $row, $member['job_position'])->getStyleByColumnAndRow(6, $row)->getAlignment()->setVertical('top');
        $sheet->setCellValueByColumnAndRow(7, $row, $member['organization_name'])->getStyleByColumnAndRow(7, $row)->getAlignment()->setVertical('top');
        $sheet->setCellValueByColumnAndRow(8, $row, is_null($member['organization_type_name']) ? $member['organization_type_custom'] : $member['organization_type_name'])->getStyleByColumnAndRow(8, $row)->getAlignment()->setVertical('top');

        if ($memberType === MEMBER_TYPE_ORGANIZATION) {
            $province = $member['province'];
            $isBangkok = false;
            if (mb_substr($province, 0, 4) == 'กรุง' || substr($province, 0, 2) == 'กท') {
                $isBangkok = true;
            }

            if ($isBangkok) {
                $displayAddress = "{$member['address']} แขวง{$member['sub_district']} เขต{$member['district']} กรุงเทพฯ {$member['postal_code']}";
            } else {
                $displayAddress = "{$member['address']} ต.{$member['sub_district']} อ.{$member['district']} จ.{$member['province']} {$member['postal_code']}";
            }
            $sheet->setCellValueByColumnAndRow(9, $row, $displayAddress)->getStyleByColumnAndRow(9, $row)->getAlignment()->setVertical('top');

            $sheet->setCellValueByColumnAndRow(10, $row, $isBangkok ? 'กรุงเทพฯ' : $province)->getStyleByColumnAndRow(10, $row)->getAlignment()->setVertical('top');
            $sheet->setCellValueByColumnAndRow(11, $row, $member['organization_phone'])->getStyleByColumnAndRow(11, $row)->getAlignment()->setVertical('top')->setHorizontal('left');
            $sheet->setCellValueByColumnAndRow(12, $row, $member['created_at'])->getStyleByColumnAndRow(12, $row)->getAlignment()->setVertical('top')->setHorizontal('center');
        } else {
            $sheet->setCellValueByColumnAndRow(9, $row, $member['created_at'])->getStyleByColumnAndRow(9, $row)->getAlignment()->setVertical('top')->setHorizontal('center');
        }

        $sheet->getRowDimension($row)->setRowHeight(-1); // set auto height
        $row++;
    }
}

function generateTraineeSheet($traineeList, $sheet)
{
    global $titleTrainee;

    $sheet->setTitle('Trainee');
    if (empty($traineeList)) {
        $sheet->setCellValueByColumnAndRow(1, 1, 'ไม่มีข้อมูล')->getStyleByColumnAndRow(1, 1)->getAlignment()->setVertical('top')->setHorizontal('left');
        return;
    }

    $sheet->mergeCells("A1:M1");

    $sheet->setCellValueByColumnAndRow(1, 1, $titleTrainee)->getStyleByColumnAndRow(1, 1)->getAlignment()->setVertical('top')->setHorizontal('left');

    $sheet->freezePane('A3');

    $sheet->getStyle("A1:Z1")->getFont()->setBold(true);
    $sheet->getStyle("A2:Z2")->getFont()->setBold(true);

    $sheet->getColumnDimension('A')->setAutoSize(true);
    $sheet->getColumnDimension('B')->setAutoSize(true);
    $sheet->getColumnDimension('C')->setAutoSize(true);
    $sheet->getColumnDimension('D')->setAutoSize(true);
    $sheet->getColumnDimension('E')->setAutoSize(true);
    $sheet->getColumnDimension('F')->setAutoSize(true);
    $sheet->getColumnDimension('G')->setAutoSize(true);
    $sheet->getColumnDimension('H')->setAutoSize(true);
    $sheet->getColumnDimension('I')->setAutoSize(true);
    $sheet->getColumnDimension('J')->setAutoSize(true);
    $sheet->getColumnDimension('K')->setAutoSize(true);
    $sheet->getColumnDimension('L')->setAutoSize(true);
    $sheet->getColumnDimension('M')->setAutoSize(true);

    $row = 2;
    $sheet->setCellValueByColumnAndRow(1, $row, 'ลำดับ')->getStyleByColumnAndRow(1, $row)->getAlignment()->setHorizontal('center');;
    $sheet->setCellValueByColumnAndRow(2, $row, 'คำนำหน้าชื่อ')->getStyleByColumnAndRow(2, $row)->getAlignment()->setHorizontal('center');
    $sheet->setCellValueByColumnAndRow(3, $row, 'ชื่อ-นามสกุล')->getStyleByColumnAndRow(3, $row)->getAlignment()->setHorizontal('center');
    $sheet->setCellValueByColumnAndRow(4, $row, 'เบอร์โทร')->getStyleByColumnAndRow(4, $row)->getAlignment()->setHorizontal('center');
    $sheet->setCellValueByColumnAndRow(5, $row, 'อีเมล')->getStyleByColumnAndRow(5, $row)->getAlignment()->setHorizontal('center');
    $sheet->setCellValueByColumnAndRow(6, $row, 'ตำแหน่งงาน/อาชีพ')->getStyleByColumnAndRow(6, $row)->getAlignment()->setHorizontal('center');
    $sheet->setCellValueByColumnAndRow(7, $row, 'ชื่อหน่วยงาน/สถานที่ทำงาน')->getStyleByColumnAndRow(7, $row)->getAlignment()->setHorizontal('center');
    $sheet->setCellValueByColumnAndRow(8, $row, 'ที่อยู่')->getStyleByColumnAndRow(8, $row)->getAlignment()->setHorizontal('center');
    $sheet->setCellValueByColumnAndRow(9, $row, 'จังหวัด')->getStyleByColumnAndRow(9, $row)->getAlignment()->setHorizontal('center');
    $sheet->setCellValueByColumnAndRow(10, $row, 'ชื่อหลักสูตรที่สมัคร')->getStyleByColumnAndRow(10, $row)->getAlignment()->setHorizontal('center');
    $sheet->setCellValueByColumnAndRow(11, $row, 'รุ่นที่')->getStyleByColumnAndRow(11, $row)->getAlignment()->setHorizontal('center');
    $sheet->setCellValueByColumnAndRow(12, $row, 'ประเภท')->getStyleByColumnAndRow(12, $row)->getAlignment()->setHorizontal('center');
    $sheet->setCellValueByColumnAndRow(13, $row, 'วันที่สมัคร')->getStyleByColumnAndRow(13, $row)->getAlignment()->setHorizontal('center');

    $sheet->getRowDimension($row)->setRowHeight(-1); // set auto height

    define('START_ROW', 3);
    $row = START_ROW;
    foreach ($traineeList as $trainee) {
        $id = $trainee['id'];
        $title = $trainee['title'];
        $firstName = $trainee['first_name'];
        $lastName = $trainee['last_name'];

        $birthDate = $trainee['birth_date'];
        $displayBirthDate = $birthDate ? getThaiShortDate(date_create($birthDate)) : null;
        $birthDateHidden = "<span style=\"display: none\">$birthDate</span></span>";
        $age = $trainee['age'] ? $trainee['age'] : null;

        $phone = $trainee['phone'];
        $email = $trainee['email'];
        $jobPosition = $trainee['job_position'];
        $organizationName = $trainee['organization_name'];

        /*$memberOrganizationType = $trainee['organization_type'];
        $memberOrganizationTypeText = $trainee['organization_type_name'];
        $memberOrganizationTypeCustom = $trainee['organization_type_custom'];
        $memberTaxId = $trainee['tax_id'];*/

        $fullAddress = $trainee['full_address'];

        $courseId = $trainee['course_id'];
        $courseName = $trainee['course_title'] . ($trainee['service_type'] !== SERVICE_TYPE_DRIVING_LICENSE ? "รุ่นที่ {$trainee['batch_number']}" : '');
        $serviceType = $trainee['service_type'];
        $serviceTypeText = $trainee['service_type'] === SERVICE_TYPE_TRAINING ? 'วิชาการ' :
            ($trainee['service_type'] === SERVICE_TYPE_SOCIAL ? 'สังคม' : 'ใบขับขี่');

        /*$registerDate = $trainee['created_at'];
        $dateTimePart = explode(' ', $registerDate);
        $displayDate = getThaiShortDateWithDayName(date_create($dateTimePart[0]));
        $timePart = explode(':', $dateTimePart[1]);
        $displayTime = $timePart[0] . '.' . $timePart[1] . ' น.';
        $displayDateTime = "$displayDate<br>$displayTime";
        $createdDateHidden = '<span style="display: none">' . $registerDate . '</span></span>';*/

        $sheet->setCellValueByColumnAndRow(1, $row, ($row - START_ROW) + 1)->getStyleByColumnAndRow(1, $row)->getAlignment()->setVertical('top')->setHorizontal('center');

        $sheet->setCellValueByColumnAndRow(2, $row, $title)->getStyleByColumnAndRow(2, $row)->getAlignment()->setVertical('top')->setHorizontal('left');

        $displayName = "{$firstName} {$lastName}";
        $sheet->setCellValueByColumnAndRow(3, $row, $displayName)->getStyleByColumnAndRow(3, $row)->getAlignment()->setVertical('top')->setHorizontal('left');

        $sheet->setCellValueByColumnAndRow(4, $row, $phone)->getStyleByColumnAndRow(4, $row)->getAlignment()->setVertical('top')->setHorizontal('left');
        $sheet->setCellValueByColumnAndRow(5, $row, $email ? $email : '-')->getStyleByColumnAndRow(5, $row)->getAlignment()->setVertical('top')->setHorizontal('left');
        $sheet->setCellValueByColumnAndRow(6, $row, $jobPosition ? $jobPosition : '-')->getStyleByColumnAndRow(6, $row)->getAlignment()->setVertical('top')->setHorizontal('left');
        $sheet->setCellValueByColumnAndRow(7, $row, $organizationName ? $organizationName : '-')->getStyleByColumnAndRow(7, $row)->getAlignment()->setVertical('top')->setHorizontal('left');

        $province = trim($trainee['province']);
        $isBangkok = false;
        if (mb_substr($province, 0, 4) == 'กรุง' || mb_substr($province, 0, 2) == 'กท') {
            $isBangkok = true;
        }

        $displayAddress = null;
        if (!$trainee['address'] || ($trainee['address'] && trim($trainee['address']) === '-')) {
            $displayAddress = '-';
        } else {
            if ($isBangkok) {
                if ($trainee['service_type'] === SERVICE_TYPE_DRIVING_LICENSE) {
                    $displayAddress = "{$trainee['address']} หมู่ที่ {$trainee['moo']} ซอย{$trainee['soi']} ถนน{$trainee['road']} แขวง{$trainee['sub_district']} เขต{$trainee['district']} กรุงเทพฯ {$trainee['postal_code']}";
                } else {
                    $displayAddress = "{$trainee['address']} แขวง{$trainee['sub_district']} เขต{$trainee['district']} กรุงเทพฯ {$trainee['postal_code']}";
                }
            } else {
                if ($trainee['service_type'] === SERVICE_TYPE_DRIVING_LICENSE) {
                    $displayAddress = "{$trainee['address']} หมู่ที่ {$trainee['moo']} ซ.{$trainee['soi']} ถ.{$trainee['road']} ต.{$trainee['sub_district']} อ.{$trainee['district']} จ.{$trainee['province']} {$trainee['postal_code']}";
                } else {
                    $displayAddress = "{$trainee['address']} ต.{$trainee['sub_district']} อ.{$trainee['district']} จ.{$trainee['province']} {$trainee['postal_code']}";
                }
            }
        }
        $sheet->setCellValueByColumnAndRow(8, $row, $displayAddress)->getStyleByColumnAndRow(8, $row)->getAlignment()->setVertical('top')->setHorizontal('left');

        $sheet->setCellValueByColumnAndRow(9, $row, $trainee['province'])->getStyleByColumnAndRow(9, $row)->getAlignment()->setVertical('top')->setHorizontal('left');
        $sheet->setCellValueByColumnAndRow(10, $row, $trainee['course_title'])->getStyleByColumnAndRow(10, $row)->getAlignment()->setVertical('top')->setHorizontal('left');
        $sheet->setCellValueByColumnAndRow(11, $row, $trainee['batch_number'])->getStyleByColumnAndRow(11, $row)->getAlignment()->setVertical('top')->setHorizontal('center');
        $sheet->setCellValueByColumnAndRow(12, $row, $serviceTypeText)->getStyleByColumnAndRow(12, $row)->getAlignment()->setVertical('top')->setHorizontal('center');
        $sheet->setCellValueByColumnAndRow(13, $row, $trainee['created_at'])->getStyleByColumnAndRow(13, $row)->getAlignment()->setVertical('top')->setHorizontal('center');

        /*if ($memberType === MEMBER_TYPE_ORGANIZATION) {
            $province = $trainee['province'];
            $isBangkok = false;
            if (mb_substr($province, 0, 4) == 'กรุง' || substr($province, 0, 2) == 'กท') {
                $isBangkok = true;
            }

            if ($isBangkok) {
                $displayAddress = "{$trainee['address']} แขวง{$trainee['sub_district']} เขต{$trainee['district']} กรุงเทพฯ {$trainee['postal_code']}";
            } else {
                $displayAddress = "{$trainee['address']} ต.{$trainee['sub_district']} อ.{$trainee['district']} จ.{$trainee['province']} {$trainee['postal_code']}";
            }
            $sheet->setCellValueByColumnAndRow(8, $row, $displayAddress)->getStyleByColumnAndRow(8, $row)->getAlignment()->setVertical('top');

            $sheet->setCellValueByColumnAndRow(9, $row, $isBangkok ? 'กรุงเทพฯ' : $province)->getStyleByColumnAndRow(9, $row)->getAlignment()->setVertical('top');
            $sheet->setCellValueByColumnAndRow(10, $row, $trainee['organization_phone'])->getStyleByColumnAndRow(10, $row)->getAlignment()->setVertical('top')->setHorizontal('left');
            $sheet->setCellValueByColumnAndRow(11, $row, $trainee['created_at'])->getStyleByColumnAndRow(11, $row)->getAlignment()->setVertical('top')->setHorizontal('center');
        } else {
            $sheet->setCellValueByColumnAndRow(8, $row, $trainee['created_at'])->getStyleByColumnAndRow(8, $row)->getAlignment()->setVertical('top')->setHorizontal('center');
        }*/

        $sheet->getRowDimension($row)->setRowHeight(-1); // set auto height
        $row++;
    }
}