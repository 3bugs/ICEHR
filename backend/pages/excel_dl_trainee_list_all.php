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

$sql = "SELECT cm.title, cm.service_type, c.batch_number, c.begin_date, c.end_date, c.place, c.application_fee
        FROM course c 
            INNER JOIN course_master cm 
                ON cm.id = c.course_master_id 
        WHERE c.id = $courseId";
if ($result = $db->query($sql)) {
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $serviceType = $row['service_type'];
        $courseDisplayName = "หลักสูตร {$row['title']}" . ($serviceType !== SERVICE_TYPE_DRIVING_LICENSE ? " รุ่นที่ {$row['batch_number']}" : '');
        if ($row['begin_date'] === $row['end_date']) {
            $courseDisplayDate = getThaiDate(date_create($row['begin_date']));
        } else {
            $courseDisplayDate = getThaiDate(date_create($row['begin_date'])) . ' - ' . getThaiDate(date_create($row['end_date']));
        }
        $courseDisplayPlace = "ณ {$row['place']}";
        $courseApplicationFee = (int)$row['application_fee'];

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
    /*$sql = "SELECT ct.title, ct.first_name, ct.last_name, ct.job_position, ct.organization_name, ct.phone, ct.email,
                   cr.receipt_name, cr.receipt_address, cr.receipt_sub_district, cr.receipt_district, cr.receipt_province, cr.receipt_postal_code, cr.receipt_organization_phone
            FROM course c
                INNER JOIN course_registration cr
                    ON cr.course_id = c.id
                INNER JOIN course_trainee ct
                    ON ct.course_registration_id = cr.id
            WHERE c.id = $courseId AND ct.register_status = 'complete'
            ORDER BY ct.first_name, ct.last_name";*/

    echo 'Error: ไม่พบข้อมูลหลักสูตรใบขับขี่';
    $db->close();
    exit();

} else if ($serviceType === SERVICE_TYPE_SOCIAL) {
    /*$sql = "SELECT cr.title, cr.first_name, cr.last_name, cr.occupation AS job_position, cr.phone, cr.email,
                   cr.address, cr.sub_district, cr.district, cr.province, cr.postal_code
            FROM course c
                INNER JOIN course_registration_social cr
                    ON cr.course_id = c.id
            WHERE c.id = $courseId AND " . ($courseApplicationFee === 0 ? "cr.register_status <> 'cancel'" : "cr.register_status = 'complete'") . " ORDER BY cr.first_name, cr.last_name";*/

    echo 'Error: ไม่พบข้อมูลหลักสูตรใบขับขี่';
    $db->close();
    exit();

} else if ($serviceType === SERVICE_TYPE_DRIVING_LICENSE) {
    $sql = "SELECT cr.title, cr.first_name, cr.last_name, cr.phone, cr.pid, cr.course_type, cr.license_type
            FROM course c 
                INNER JOIN course_registration_driving_license cr 
                    ON cr.course_id = c.id  
            WHERE c.id = $courseId AND cr.register_status <> 'cancel' 
            ORDER BY cr.id";
}

if ($result = $db->query($sql)) {
    if ($result->num_rows > 0) {
        $traineeList = array();

        while ($trainee = $result->fetch_assoc()) {
            array_push($traineeList, $trainee);
        }
        $result->close();
    } else {
        echo 'ไม่มีข้อมูลผู้เข้ารับการอบรมที่สถานะการลงทะเบียนสมบูรณ์ในหลักสูตรนี้';
        $result->close();
        $db->close();
        exit();
    }
} else {
    echo 'เกิดข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล: ' . $db->error . $sql;
    $db->close();
    exit();
}

header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="course_trainee.xls"');
header('Cache-Control: max-age=0');

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

$sheet->mergeCells("A1:G1");

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

$header = "รายชื่อผู้เข้ารับการอบรม\n{$courseDisplayName}\nณ สถาบันเสริมศึกษาและทรัพยากรมนุษย์ มหาวิทยาลัยธรรมศาสตร์ ท่าพระจันทร์\n{$courseDisplayDate}   เวลา 08.30 - 14.30 น.";
$row = 1;
$sheet->setCellValueByColumnAndRow(1, $row, $header)->getStyleByColumnAndRow(1, $row)->getAlignment()->setHorizontal('center');;
$sheet->getRowDimension($row)->setRowHeight(-1); // set auto height

$row = 2;
$sheet->setCellValueByColumnAndRow(1, $row, 'ลำดับ')->getStyleByColumnAndRow(1, $row)->getAlignment()->setHorizontal('center');;
$sheet->setCellValueByColumnAndRow(2, $row, 'คำนำหน้าชื่อ')->getStyleByColumnAndRow(2, $row)->getAlignment()->setHorizontal('center');
$sheet->setCellValueByColumnAndRow(3, $row, 'ชื่อ')->getStyleByColumnAndRow(3, $row)->getAlignment()->setHorizontal('center');
$sheet->setCellValueByColumnAndRow(4, $row, 'นามสกุล')->getStyleByColumnAndRow(4, $row)->getAlignment()->setHorizontal('center');
$sheet->setCellValueByColumnAndRow(5, $row, 'เลขที่บัตรประชาชน/เลขที่หนังสือเดินทาง')->getStyleByColumnAndRow(5, $row)->getAlignment()->setHorizontal('center');
$sheet->setCellValueByColumnAndRow(6, $row, 'ประเภทบัตร/ประเภทรถ')->getStyleByColumnAndRow(6, $row)->getAlignment()->setHorizontal('center');
$sheet->setCellValueByColumnAndRow(7, $row, 'ลายเซ็น')->getStyleByColumnAndRow(7, $row)->getAlignment()->setHorizontal('center');
$sheet->getRowDimension($row)->setRowHeight(-1); // set auto height

define('START_ROW', 3);
$row = START_ROW;
$income = 0;
foreach ($traineeList as $trainee) {
    $sheet->setCellValueByColumnAndRow(1, $row, ($row - START_ROW) + 1)->getStyleByColumnAndRow(1, $row)->getAlignment()->setVertical('top')->setHorizontal('center');

    //$displayName = "{$trainee['title']} {$trainee['first_name']} {$trainee['last_name']}";
    $sheet->setCellValueByColumnAndRow(2, $row, $trainee['title'])->getStyleByColumnAndRow(2, $row)->getAlignment()->setVertical('top')->setHorizontal('left');
    $sheet->setCellValueByColumnAndRow(3, $row, $trainee['first_name'])->getStyleByColumnAndRow(3, $row)->getAlignment()->setVertical('top')->setHorizontal('left');
    $sheet->setCellValueByColumnAndRow(4, $row, $trainee['last_name'])->getStyleByColumnAndRow(4, $row)->getAlignment()->setVertical('top')->setHorizontal('left');

    $pid = trim($trainee['pid']);
    $pid = strlen($pid) === 13 ? formatPid($pid) : $pid;
    $sheet->setCellValueByColumnAndRow(5, $row, $pid)->getStyleByColumnAndRow(5, $row)->getAlignment()->setVertical('top')->setHorizontal('center');

    $licenseType = (int)$trainee['license_type'];
    $licenseTypeText = '';
    if (($licenseType & 1) > 0) {
        $licenseTypeText .= 'รถยนต์/';
    }
    if (($licenseType & 2) > 0) {
        $licenseTypeText .= 'จยย./';
    }
    if (($licenseType & 4) > 0) {
        $licenseTypeText .= 'สามล้อ/';
    }
    $licenseTypeText = mb_substr($licenseTypeText, 0, -1);
    $sheet->setCellValueByColumnAndRow(6, $row, ((int)$trainee['course_type'] === 1 ? 'ขอใหม่/' : 'ต่ออายุ/') . $licenseTypeText)->getStyleByColumnAndRow(6, $row)->getAlignment()->setVertical('top');

    $sheet->getRowDimension($row)->setRowHeight(-1); // set auto height
    $row++;
}

$row++;

//$writer = new Xlsx($spreadsheet);
$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xls');

//$writer->save('hello world.xlsx');
$writer->save("php://output");

require_once '../include/foot_php.inc';