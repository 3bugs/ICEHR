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
               ct.form_number, ct.title, ct.first_name, ct.last_name, ct.job_position, ct.organization_name, ct.phone, ct.email
        FROM course_trainee ct 
            INNER JOIN course_registration cr 
                ON cr.id = ct.course_registration_id 
            INNER JOIN course c 
                ON c.id = cr.course_id 
            INNER JOIN course_master cm 
                ON cm.id = c.course_master_id
        WHERE c.id = $courseId AND ct.register_status = 'complete'
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
header('Content-Disposition: attachment; filename="course_trainee_status_complete.xls"');
header('Cache-Control: max-age=0');

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->mergeCells("A1:E1");
$sheet->mergeCells("B2:C2");
$sheet->mergeCells("D2:E2");
$sheet->freezePane('A3');

$sheet->setTitle(date("d-m-Y"));
$sheet->getStyle("A1:Z1")->getFont()->setBold(true);
$sheet->getStyle("A2:Z2")->getFont()->setBold(true);

$sheet->getColumnDimension('A')->setAutoSize(true);
$sheet->getColumnDimension('B')->setAutoSize(true);
$sheet->getColumnDimension('C')->setAutoSize(true);
$sheet->getColumnDimension('D')->setAutoSize(true);
$sheet->getColumnDimension('E')->setAutoSize(true);

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
$sheet->setCellValueByColumnAndRow(2, $row, 'ผู้เข้ารับการอบรม')->getStyleByColumnAndRow(2, $row)->getAlignment()->setHorizontal('center');
//$sheet->setCellValueByColumnAndRow(3, $row, 'ผู้เข้ารับการอบรม');
$sheet->setCellValueByColumnAndRow(4, $row, 'ผู้ประสานงาน')->getStyleByColumnAndRow(4, $row)->getAlignment()->setHorizontal('center');
//$sheet->setCellValueByColumnAndRow(5, $row, 'ผู้ประสานงาน');
$sheet->getRowDimension($row)->setRowHeight(-1); // set auto height

define('START_ROW', 3);
$row = START_ROW;
foreach ($traineeList as $trainee) {
    $sheet->setCellValueByColumnAndRow(1, $row, ($row - START_ROW) + 1)->getStyleByColumnAndRow(1, $row)->getAlignment()->setVertical('top')->setHorizontal('center');

    $displayName = "{$trainee['title']}{$trainee['first_name']} {$trainee['last_name']}\n{$trainee['job_position']}\n{$trainee['organization_name']}";
    $sheet->setCellValueByColumnAndRow(2, $row, $displayName)->getStyleByColumnAndRow(2, $row)->getAlignment()->setVertical('top');
    $displayContact = "โทร: {$trainee['phone']}\nเมล: {$trainee['email']}";
    $sheet->setCellValueByColumnAndRow(3, $row, $displayContact)->getStyleByColumnAndRow(3, $row)->getAlignment()->setVertical('top');

    $displayCoordinatorName = "{$trainee['coordinator_title']}{$trainee['coordinator_first_name']} {$trainee['coordinator_last_name']}\n{$trainee['coordinator_job_position']}\n{$trainee['coordinator_organization_name']}";
    $sheet->setCellValueByColumnAndRow(4, $row, $displayCoordinatorName)->getStyleByColumnAndRow(4, $row)->getAlignment()->setVertical('top');
    $displayCoordinatorContact = "โทร: {$trainee['coordinator_phone']}\nเมล: {$trainee['coordinator_email']}";
    $sheet->setCellValueByColumnAndRow(5, $row, $displayCoordinatorContact)->getStyleByColumnAndRow(5, $row)->getAlignment()->setVertical('top');

    $sheet->getRowDimension($row)->setRowHeight(-1); // set auto height
    $row++;
}

//$writer = new Xlsx($spreadsheet);
$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xls');

//$writer->save('hello world.xlsx');
$writer->save("php://output");

require_once '../include/foot_php.inc';