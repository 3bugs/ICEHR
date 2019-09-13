<?php
require_once '../vendor/autoload.php';
require_once '../include/head_php.inc';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$sql = "SELECT title, first_name, last_name, organization_name, phone, email, course, num_day, num_trainee, place, remark, status, created_at
        FROM in_house 
        WHERE TRUE 
        ORDER BY id DESC";

$inHouseList = array();
if ($result = $db->query($sql)) {
    while ($row = $result->fetch_assoc()) {
        array_push($inHouseList, $row);
    }
    $result->close();

    if (sizeof($inHouseList) == 0) {
        header('Content-Type: text/html');
        echo 'ไม่มีข้อมูลรายชื่อผู้ติดต่อ In-House Training';
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
header('Content-Disposition: attachment; filename="in_house_training.xls"');
header('Cache-Control: max-age=0');

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

$sheet->setTitle(date("d-m-Y"));
$sheet->getStyle("A1:Z1")->getFont()->setBold(true);

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

$row = 1;
$sheet->setCellValueByColumnAndRow(1, $row, 'ลำดับ');
$sheet->setCellValueByColumnAndRow(2, $row, 'ชื่อ-นามสกุลผู้ติดต่อ');
$sheet->setCellValueByColumnAndRow(3, $row, 'ชื่อหน่วยงาน');
$sheet->setCellValueByColumnAndRow(4, $row, 'เบอร์โทร');
$sheet->setCellValueByColumnAndRow(5, $row, 'อีเมล');
$sheet->setCellValueByColumnAndRow(6, $row, 'หลักสูตรที่ต้องการ');
$sheet->setCellValueByColumnAndRow(7, $row, 'จำนวนวัน');
$sheet->setCellValueByColumnAndRow(8, $row, 'จำนวนผู้เข้าอบรม');
$sheet->setCellValueByColumnAndRow(9, $row, 'สถานที่');
$sheet->setCellValueByColumnAndRow(10, $row, 'อื่นๆ');
$sheet->setCellValueByColumnAndRow(11, $row, 'วันที่ส่งข้อมูล');
$sheet->setCellValueByColumnAndRow(12, $row, 'สถานะ');

$previousPaperId = 0;
define('START_ROW', 2);
$row = START_ROW;
foreach ($inHouseList as $inHouse) {
    $paperId = (int)$inHouse['id'];

    $sheet->setCellValueByColumnAndRow(1, $row, ($row - START_ROW) + 1);

    $displayName = "{$inHouse['title']}{$inHouse['first_name']} {$inHouse['last_name']}";
    $sheet->setCellValueByColumnAndRow(2, $row, $displayName);
    $sheet->setCellValueByColumnAndRow(3, $row, "{$inHouse['organization_name']}");
    $sheet->setCellValueByColumnAndRow(4, $row, "{$inHouse['phone']}");
    $sheet->setCellValueByColumnAndRow(5, $row, "{$inHouse['email']}");
    $sheet->setCellValueByColumnAndRow(6, $row, "{$inHouse['course']}");
    $sheet->setCellValueByColumnAndRow(7, $row, "{$inHouse['num_day']}");
    $sheet->setCellValueByColumnAndRow(8, $row, "{$inHouse['num_trainee']}");
    $sheet->setCellValueByColumnAndRow(9, $row, "{$inHouse['place']}");
    $sheet->setCellValueByColumnAndRow(10, $row, "{$inHouse['remark']}");
    $sheet->setCellValueByColumnAndRow(11, $row, getThaiShortDate(date_create($inHouse['created_at'])));

    $status = '???';
    switch ($inHouse['status']) {
        case 'wait':
            $status = 'ยังไม่ได้ติดต่อกลับ';
            break;
        case 'complete':
            $status = 'ติดต่อกลับแล้ว';
            break;
    }
    $sheet->setCellValueByColumnAndRow(12, $row, $status);

    $row++;
}

//$writer = new Xlsx($spreadsheet);
$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xls');

//$writer->save('hello world.xlsx');
$writer->save("php://output");

require_once '../include/foot_php.inc';