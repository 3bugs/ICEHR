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

$sql = "SELECT cm.title AS course_title, cm.service_type, c.batch_number, c.begin_date, c.end_date, c.place,
               cr.coordinator_title, cr.coordinator_first_name, cr.coordinator_last_name, cr.coordinator_job_position, 
               cr.coordinator_organization_name, cr.coordinator_phone, cr.coordinator_email,
               ct.form_number, ct.title, ct.first_name, ct.last_name, ct.job_position, ct.organization_name, ct.phone, ct.email,
               ct.register_status
        FROM course_trainee ct 
            INNER JOIN course_registration cr 
                ON cr.id = ct.course_registration_id 
            INNER JOIN course c 
                ON c.id = cr.course_id 
            INNER JOIN course_master cm 
                ON cm.id = c.course_master_id
        WHERE c.id = $courseId AND ct.register_status <> 'cancel'
        ORDER BY ct.id";

$traineeList = array();
if ($result = $db->query($sql)) {
    while ($row = $result->fetch_assoc()) {
        array_push($traineeList, $row);
    }
    $result->close();

    if (sizeof($traineeList) == 0) {
        header('Content-Type: text/html');
        echo 'ไม่มีข้อมูลผู้เข้ารับการอบรมที่สถานะการลงทะเบียนสมบูรณ์ในหลักสูตรนี้';
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
$sheet->mergeCells("A1:C1");
$sheet->freezePane('A3');

$sheet->setTitle(date("d-m-Y"));
$sheet->getStyle("A1:Z1")->getFont()->setBold(true);
$sheet->getStyle("A2:Z2")->getFont()->setBold(true);

$sheet->getColumnDimension('A')->setAutoSize(true);
$sheet->getColumnDimension('B')->setAutoSize(true);
$sheet->getColumnDimension('C')->setAutoSize(true);

$trainee = $traineeList[0];
$serviceType = $trainee['service_type'];
$courseDisplayName = "หลักสูตร {$trainee['course_title']}" . ($serviceType !== SERVICE_TYPE_DRIVING_LICENSE ? " รุ่นที่ {$trainee['batch_number']}" : '');
$courseDisplayDate = null;
if ($trainee['begin_date'] === $trainee['end_date']) {
    $courseDisplayDate = "อบรม" . getThaiDate(date_create($trainee['begin_date']));
} else {
    $courseDisplayDate = "อบรม" . getThaiDate(date_create($trainee['begin_date'])) . ' - ' . getThaiDate(date_create($trainee['end_date']));
}
$courseDisplayPlace = "ณ {$trainee['place']}";

$header = "{$courseDisplayName}\n{$courseDisplayDate}\n{$courseDisplayPlace}";
$row = 1;
$sheet->setCellValueByColumnAndRow(1, $row, $header)->getStyleByColumnAndRow(1, $row)->getAlignment()->setHorizontal('center');;
$sheet->getRowDimension($row)->setRowHeight(-1); // set auto height

$row = 2;
$sheet->setCellValueByColumnAndRow(1, $row, 'ลำดับ')->getStyleByColumnAndRow(1, $row)->getAlignment()->setHorizontal('center');;
$sheet->setCellValueByColumnAndRow(2, $row, 'ผู้สมัครอบรม')->getStyleByColumnAndRow(2, $row)->getAlignment()->setHorizontal('center');
$sheet->setCellValueByColumnAndRow(3, $row, 'สถานะใบสมัคร')->getStyleByColumnAndRow(3, $row)->getAlignment()->setHorizontal('center');
$sheet->getRowDimension($row)->setRowHeight(-1); // set auto height

define('START_ROW', 3);
$row = START_ROW;
foreach ($traineeList as $trainee) {
    $sheet->setCellValueByColumnAndRow(1, $row, ($row - START_ROW) + 1)->getStyleByColumnAndRow(1, $row)->getAlignment()->setVertical('top')->setHorizontal('center');

    $displayName = "{$trainee['title']}{$trainee['first_name']} {$trainee['last_name']}";
    $sheet->setCellValueByColumnAndRow(2, $row, $displayName)->getStyleByColumnAndRow(2, $row)->getAlignment()->setVertical('top');

    $registerStatusText = null;
    switch ($trainee['register_status']) {
        case 'start':
            $registerStatusText = 'ยังไม่ได้ชำระเงิน';
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
    $sheet->setCellValueByColumnAndRow(3, $row, $registerStatusText)->getStyleByColumnAndRow(3, $row)->getAlignment()->setVertical('top')->setHorizontal('center');

    $sheet->getRowDimension($row)->setRowHeight(-1); // set auto height
    $row++;
}

//$writer = new Xlsx($spreadsheet);
$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xls');

//$writer->save('hello world.xlsx');
$writer->save("php://output");

require_once '../include/foot_php.inc';