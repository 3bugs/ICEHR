<?php
require_once '../vendor/autoload.php';
require_once '../include/head_php.inc';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$courseId = $_GET['course_id'];

if (!isset($courseId)) {
    echo 'Error: ไม่ได้ระบุ ID หลักสูตร';
    $db->close();
    exit();
}

$header = null;
$sql = "SELECT cm.title, cm.service_type, c.batch_number, c.begin_date, c.end_date, c.place, c.application_fee
        FROM course c 
            INNER JOIN course_master cm 
                ON cm.id = c.course_master_id 
        WHERE c.id = $courseId";
if ($result = $db->query($sql)) {
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $serviceType = $row['service_type'];
        $courseDisplayName = "หลักสูตร {$row['title']}";
        if ($serviceType !== SERVICE_TYPE_DRIVING_LICENSE
            && $row['batch_number']
            && ((int)$row['batch_number'] > 0)) {
            $courseDisplayName .= " รุ่นที่ {$row['batch_number']}";
        }
        
        if ($row['begin_date'] === $row['end_date']) {
            $courseDisplayDate = "อบรม" . getThaiDate(date_create($row['begin_date']));
        } else {
            $courseDisplayDate = "อบรม" . getThaiDate(date_create($row['begin_date'])) . ' - ' . getThaiDate(date_create($row['end_date']));
        }
        $courseDisplayPlace = "ณ {$row['place']}";
        $courseApplicationFee = (int)$row['application_fee'];

        $header = "{$courseDisplayName}\n{$courseDisplayDate}\n{$courseDisplayPlace}";

        $result->close();
    } else {
        echo 'Error: ไม่พบข้อมูลหลักสูตรที่ระบุ';
        $result->close();
        $db->close();
        exit();
    }
} else {
    echo 'Error: เกิดข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล';
    $db->close();
    exit();
}

if ($serviceType === SERVICE_TYPE_TRAINING) {
    $sql = "SELECT ct.form_number, ct.title, ct.first_name, ct.last_name, 
                   ct.birth_date, TIMESTAMPDIFF(YEAR, ct.birth_date, CURDATE()) AS age,
                   ct.phone, ct.email, ct.job_position, ct.organization_name, 
                   CONCAT_WS(' ', cr.receipt_address, cr.receipt_sub_district, cr.receipt_district, cr.receipt_province, cr.receipt_postal_code) AS full_address,
                   cr.receipt_address AS address, cr.receipt_sub_district AS sub_district, cr.receipt_district AS district, 
                   cr.receipt_province AS province, cr.receipt_postal_code AS postal_code,
                   ct.register_status, DATE_FORMAT(cr.created_at, '%d/%m/%Y') AS created_at
            FROM course_trainee ct 
                INNER JOIN course_registration cr 
                    ON cr.id = ct.course_registration_id 
            WHERE cr.course_id = $courseId AND ct.register_status <> 'cancel'
            ORDER BY ct.id";
} else if ($serviceType === SERVICE_TYPE_SOCIAL) {
    $sql = "SELECT cr.form_number, cr.title, cr.first_name, cr.last_name,
                   cr.birth_date, TIMESTAMPDIFF(YEAR, cr.birth_date, CURDATE()) AS age,
                   cr.phone, cr.email, cr.occupation AS job_position, cr.work_place AS organization_name,
                   CONCAT_WS(' ', cr.address, cr.sub_district, cr.district, cr.province, cr.postal_code) AS full_address,
                   cr.address AS address, cr.sub_district AS sub_district, cr.district AS district, 
                   cr.province AS province, cr.postal_code AS postal_code,
                   cr.register_status, DATE_FORMAT(cr.created_at, '%d/%m/%Y') AS created_at
            FROM course_registration_social cr 
            WHERE cr.course_id = $courseId AND cr.register_status <> 'cancel' 
            ORDER BY cr.id";
} else {
    echo 'Error: ประเภทบริการไม่รองรับ';
    $db->close();
    exit();
}

$traineeList = array();
if ($result = $db->query($sql)) {
    while ($row = $result->fetch_assoc()) {
        array_push($traineeList, $row);
    }
    $result->close();

    if (sizeof($traineeList) == 0) {
        header('Content-Type: text/html');
        echo 'ไม่มีข้อมูลผู้สมัครในหลักสูตรนี้';
        $db->close();
        exit();
    }
} else {
    header('Content-Type: text/html');
    echo 'Error: เกิดข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล - ' . $db->error;
    $db->close();
    exit();
}

header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="course_trainee_status_all.xls"');
header('Cache-Control: max-age=0');

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->mergeCells("A1:J1");
$sheet->freezePane('A3');

$sheet->setTitle(date("d-m-Y"));
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

/*$trainee = $traineeList[0];
$serviceType = $trainee['service_type'];
$courseDisplayName = "หลักสูตร {$trainee['course_title']}" . ($serviceType !== SERVICE_TYPE_DRIVING_LICENSE ? " รุ่นที่ {$trainee['batch_number']}" : '');
$courseDisplayDate = null;
if ($trainee['begin_date'] === $trainee['end_date']) {
    $courseDisplayDate = "อบรม" . getThaiDate(date_create($trainee['begin_date']));
} else {
    $courseDisplayDate = "อบรม" . getThaiDate(date_create($trainee['begin_date'])) . ' - ' . getThaiDate(date_create($trainee['end_date']));
}
$courseDisplayPlace = "ณ {$trainee['place']}";

$header = "{$courseDisplayName}\n{$courseDisplayDate}\n{$courseDisplayPlace}";*/

$row = 1;
$sheet->setCellValueByColumnAndRow(1, $row, $header)->getStyleByColumnAndRow(1, $row)->getAlignment()->setHorizontal('center');;
$sheet->getRowDimension($row)->setRowHeight(-1); // set auto height

$row = 2;
$sheet->setCellValueByColumnAndRow(1, $row, 'ลำดับ')->getStyleByColumnAndRow(1, $row)->getAlignment()->setHorizontal('center');;
$sheet->setCellValueByColumnAndRow(2, $row, 'คำนำหน้าชื่อ')->getStyleByColumnAndRow(2, $row)->getAlignment()->setHorizontal('center');
$sheet->setCellValueByColumnAndRow(3, $row, 'ชื่อ-นามสกุล')->getStyleByColumnAndRow(3, $row)->getAlignment()->setHorizontal('center');
$sheet->setCellValueByColumnAndRow(4, $row, 'เบอร์โทร')->getStyleByColumnAndRow(4, $row)->getAlignment()->setHorizontal('center');
$sheet->setCellValueByColumnAndRow(5, $row, 'อีเมล')->getStyleByColumnAndRow(5, $row)->getAlignment()->setHorizontal('center');
$sheet->setCellValueByColumnAndRow(6, $row, 'ตำแหน่งงาน/อาชีพ')->getStyleByColumnAndRow(6, $row)->getAlignment()->setHorizontal('center');
$sheet->setCellValueByColumnAndRow(7, $row, 'ชื่อหน่วยงาน/สถานที่ทำงาน')->getStyleByColumnAndRow(7, $row)->getAlignment()->setHorizontal('center');
$sheet->setCellValueByColumnAndRow(8, $row, 'ที่อยู่')->getStyleByColumnAndRow(8, $row)->getAlignment()->setHorizontal('center');
$sheet->setCellValueByColumnAndRow(9, $row, 'วันที่สมัคร')->getStyleByColumnAndRow(9, $row)->getAlignment()->setHorizontal('center');
$sheet->setCellValueByColumnAndRow(10, $row, 'สถานะใบสมัคร')->getStyleByColumnAndRow(10, $row)->getAlignment()->setHorizontal('center');

$sheet->getRowDimension($row)->setRowHeight(-1); // set auto height

define('START_ROW', 3);
$row = START_ROW;
foreach ($traineeList as $trainee) {
    $id = $trainee['id'];
    $title = $trainee['title'];
    $firstName = $trainee['first_name'];
    $lastName = $trainee['last_name'];
    $displayName = "{$firstName} {$lastName}";

    $birthDate = $trainee['birth_date'];
    $displayBirthDate = $birthDate ? getThaiShortDate(date_create($birthDate)) : null;
    $birthDateHidden = "<span style=\"display: none\">$birthDate</span></span>";
    $age = $trainee['age'] ? $trainee['age'] : null;

    $phone = $trainee['phone'];
    $email = $trainee['email'];
    $jobPosition = $trainee['job_position'];
    $organizationName = $trainee['organization_name'];

    $fullAddress = $trainee['full_address'];

    $province = trim($trainee['province']);
    $isBangkok = false;
    if (mb_substr($province, 0, 4) == 'กรุง' || substr($province, 0, 2) == 'กท') {
        $isBangkok = true;
    }

    $displayAddress = null;
    if (!$trainee['address'] || ($trainee['address'] && trim($trainee['address']) === '-')) {
        $displayAddress = '-';
    } else {
        if ($isBangkok) {
            $displayAddress = "{$trainee['address']} แขวง{$trainee['sub_district']} เขต{$trainee['district']} กรุงเทพฯ {$trainee['postal_code']}";
        } else {
            $displayAddress = "{$trainee['address']} ต.{$trainee['sub_district']} อ.{$trainee['district']} จ.{$trainee['province']} {$trainee['postal_code']}";
        }
    }

    // ลำดับ
    $sheet->setCellValueByColumnAndRow(1, $row, ($row - START_ROW) + 1)->getStyleByColumnAndRow(1, $row)->getAlignment()->setVertical('top')->setHorizontal('center');
    // คำนำหน้าชื่อ
    $sheet->setCellValueByColumnAndRow(2, $row, $trainee['title'])->getStyleByColumnAndRow(2, $row)->getAlignment()->setVertical('top');
    // ชื่อ-นามสกุล
    $sheet->setCellValueByColumnAndRow(3, $row, $displayName)->getStyleByColumnAndRow(3, $row)->getAlignment()->setVertical('top')->setHorizontal('left');
    // เบอร์โทร
    $sheet->setCellValueByColumnAndRow(4, $row, $phone)->getStyleByColumnAndRow(4, $row)->getAlignment()->setVertical('top')->setHorizontal('left');
    // อีเมล
    $sheet->setCellValueByColumnAndRow(5, $row, $email ? $email : '-')->getStyleByColumnAndRow(5, $row)->getAlignment()->setVertical('top')->setHorizontal('left');
    // ตำแหน่งงาน/อาชีพ
    $sheet->setCellValueByColumnAndRow(6, $row, $jobPosition ? $jobPosition : '-')->getStyleByColumnAndRow(6, $row)->getAlignment()->setVertical('top')->setHorizontal('left');
    // ชื่อหน่วยงาน/สถานที่ทำงาน
    $sheet->setCellValueByColumnAndRow(7, $row, $organizationName ? $organizationName : '-')->getStyleByColumnAndRow(7, $row)->getAlignment()->setVertical('top')->setHorizontal('left');
    // ที่อยู่
    $sheet->setCellValueByColumnAndRow(8, $row, $displayAddress)->getStyleByColumnAndRow(8, $row)->getAlignment()->setVertical('top')->setHorizontal('left');
    // วันที่สมัคร
    $sheet->setCellValueByColumnAndRow(9, $row, $trainee['created_at'])->getStyleByColumnAndRow(9, $row)->getAlignment()->setVertical('top')->setHorizontal('center');

    $registerStatusText = null;
    switch ($trainee['register_status']) {
        case 'start':
            $registerStatusText = ($courseApplicationFee === 0 ? 'สมัคร' : 'ยังไม่ได้ชำระเงิน');
            break;
        case 'wait-approve':
            $registerStatusText = 'มีการแจ้งชำระเงิน รอตรวจสอบ';
            break;
        case 'complete':
            $registerStatusText = 'ชำระเงินแล้ว';
            break;
        case 'cancel':
            $registerStatusText = 'ใบสมัครถูกยกเลิก';
            break;
    }
    // สถานะใบสมัคร
    $sheet->setCellValueByColumnAndRow(10, $row, $registerStatusText)->getStyleByColumnAndRow(10, $row)->getAlignment()->setVertical('top')->setHorizontal('center');

    $sheet->getRowDimension($row)->setRowHeight(-1); // set auto height
    $row++;
}

//$writer = new Xlsx($spreadsheet);
$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xls');

//$writer->save('hello world.xlsx');
$writer->save("php://output");

require_once '../include/foot_php.inc';