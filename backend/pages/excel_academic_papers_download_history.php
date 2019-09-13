<?php
require_once '../vendor/autoload.php';
require_once '../include/head_php.inc';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$sql = "SELECT ap.id, ap.title, ap.first_name AS author_first_name, ap.last_name AS author_last_name, ap.year_published, 
               apd.first_name, apd.last_name, apd.occupation, apd.job_position, apd.organization_name, apd.email, apd.use_purpose, apd.created_at
        FROM academic_paper ap 
            INNER JOIN academic_paper_download apd 
                ON ap.id = apd.academic_paper_id 
        WHERE TRUE 
        ORDER BY ap.id ASC, apd.id DESC";

$paperDownloadList = array();
if ($result = $db->query($sql)) {
    while ($row = $result->fetch_assoc()) {
        array_push($paperDownloadList, $row);
    }
    $result->close();

    if (sizeof($paperDownloadList) == 0) {
        header('Content-Type: text/html');
        echo 'ไม่มีข้อมูลประวัติการดาวน์โหลดเอกสารงานวิจัยและวิชาการ';
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
header('Content-Disposition: attachment; filename="academic_paper.xls"');
header('Cache-Control: max-age=0');

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

$sheet->setTitle(date("d-m-Y"));
$sheet->getStyle("A1:Z1")->getFont()->setBold(true);

$sheet->getColumnDimension('A')->setAutoSize(true);
$sheet->getColumnDimension('B')->setWidth(40);
$sheet->getColumnDimension('C')->setAutoSize(true);
$sheet->getColumnDimension('D')->setAutoSize(true);
$sheet->getColumnDimension('E')->setAutoSize(true);
$sheet->getColumnDimension('F')->setAutoSize(true);
$sheet->getColumnDimension('G')->setAutoSize(true);
$sheet->getColumnDimension('H')->setWidth(30);
$sheet->getColumnDimension('I')->setAutoSize(true);

$row = 1;
$sheet->setCellValueByColumnAndRow(1, $row, 'ลำดับ');
$sheet->setCellValueByColumnAndRow(2, $row, 'ชื่อเรื่องงานวิจัยและวิชาการ/ผู้ทำวิจัย/ปีที่เผยแพร่');
$sheet->setCellValueByColumnAndRow(3, $row, 'ชื่อ-นามสกุลผู้ดาวน์โหลด');
$sheet->setCellValueByColumnAndRow(4, $row, 'อีเมล');
$sheet->setCellValueByColumnAndRow(5, $row, 'อาชีพ');
$sheet->setCellValueByColumnAndRow(6, $row, 'ชื่อหน่วยงาน');
$sheet->setCellValueByColumnAndRow(7, $row, 'ตำแหน่ง');
$sheet->setCellValueByColumnAndRow(8, $row, 'การนำไปใช้ประโยชน์');
$sheet->setCellValueByColumnAndRow(9, $row, 'วันที่ดาวน์โหลด');

$previousPaperId = 0;
define('START_ROW', 2);
$row = START_ROW;
foreach ($paperDownloadList as $paperDownload) {
    $paperId = (int)$paperDownload['id'];
    /*if ($paperId !== $previousPaperId) {
        if ($row !== START_ROW) { // เว้นวรรค 1 บรรทัด
            $row++;
        }
        $paperDisplayTitle = "{$paperDownload['title']} ({$paperDownload['author_first_name']} {$paperDownload['author_last_name']}, {$paperDownload['year_published']})";
        $sheet->setCellValueByColumnAndRow(1, $row, $paperDownload['title'] . );
    }*/

    $sheet->setCellValueByColumnAndRow(1, $row, ($row - START_ROW) + 1);

    $paperDisplayTitle = "{$paperDownload['title']} / {$paperDownload['author_first_name']} {$paperDownload['author_last_name']} / {$paperDownload['year_published']}";
    $sheet->setCellValueByColumnAndRow(2, $row, $paperDisplayTitle);
    $sheet->setCellValueByColumnAndRow(3, $row, "{$paperDownload['first_name']} {$paperDownload['last_name']}");
    $sheet->setCellValueByColumnAndRow(4, $row, "{$paperDownload['email']}");
    $sheet->setCellValueByColumnAndRow(5, $row, "{$paperDownload['occupation']}");
    $sheet->setCellValueByColumnAndRow(6, $row, "{$paperDownload['organization_name']}");
    $sheet->setCellValueByColumnAndRow(7, $row, "{$paperDownload['job_position']}");
    $sheet->setCellValueByColumnAndRow(8, $row, "{$paperDownload['use_purpose']}");
    $sheet->setCellValueByColumnAndRow(9, $row, getThaiShortDate(date_create($paperDownload['created_at'])));

    $row++;
}

//$writer = new Xlsx($spreadsheet);
$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xls');

//$writer->save('hello world.xlsx');
$writer->save("php://output");

require_once '../include/foot_php.inc';