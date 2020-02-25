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
            $courseDisplayDate = "อบรม" . getThaiDate(date_create($row['begin_date']));
        } else {
            $courseDisplayDate = "อบรม" . getThaiDate(date_create($row['begin_date'])) . ' - ' . getThaiDate(date_create($row['end_date']));
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
    $sql = "SELECT ct.title, ct.first_name, ct.last_name, ct.job_position, ct.organization_name, ct.phone, ct.email,
                   cr.receipt_name, cr.receipt_address, cr.receipt_sub_district, cr.receipt_district, cr.receipt_province, cr.receipt_postal_code, cr.receipt_organization_phone
            FROM course c 
                INNER JOIN course_registration cr 
                    ON cr.course_id = c.id 
                INNER JOIN course_trainee ct 
                    ON ct.course_registration_id = cr.id 
            WHERE c.id = $courseId AND ct.register_status = 'complete' 
            ORDER BY ct.first_name, ct.last_name";

} else if ($serviceType === SERVICE_TYPE_SOCIAL) {
    $sql = "SELECT cr.title, cr.first_name, cr.last_name, cr.occupation AS job_position, cr.phone, cr.email, 
                   cr.address, cr.sub_district, cr.district, cr.province, cr.postal_code
            FROM course c 
                INNER JOIN course_registration_social cr 
                    ON cr.course_id = c.id  
            WHERE c.id = $courseId AND " . ($courseApplicationFee === 0 ? "cr.register_status <> 'cancel'" : "cr.register_status = 'complete'") . " ORDER BY cr.first_name, cr.last_name";

} else if ($serviceType === SERVICE_TYPE_DRIVING_LICENSE) {
    $sql = "SELECT cr.title, cr.first_name, cr.last_name, cr.phone, 
                   cr.address, cr.moo, cr.soi, cr.road, cr.sub_district, cr.district, cr.province, cr.postal_code
            FROM course c 
                INNER JOIN course_registration_driving_license cr 
                    ON cr.course_id = c.id  
            WHERE c.id = $courseId AND cr.register_status = 'complete' 
            ORDER BY cr.first_name, cr.last_name";
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
header('Content-Disposition: attachment; filename="class_directory.xls"');
header('Cache-Control: max-age=0');

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->mergeCells("A1:D1");
$sheet->freezePane('A3');

$sheet->setTitle(date("d-m-Y"));
$sheet->getStyle("A1:Z1")->getFont()->setBold(true);
$sheet->getStyle("A2:Z2")->getFont()->setBold(true);

$sheet->getColumnDimension('A')->setAutoSize(true);
$sheet->getColumnDimension('B')->setAutoSize(true);
$sheet->getColumnDimension('C')->setAutoSize(true);
$sheet->getColumnDimension('D')->setAutoSize(true);

$header = "{$courseDisplayName}\n{$courseDisplayDate}\n{$courseDisplayPlace}";
$row = 1;
$sheet->setCellValueByColumnAndRow(1, $row, $header)->getStyleByColumnAndRow(1, $row)->getAlignment()->setHorizontal('center');;
$sheet->getRowDimension($row)->setRowHeight(-1); // set auto height

$row = 2;
$sheet->setCellValueByColumnAndRow(1, $row, 'ลำดับ')->getStyleByColumnAndRow(1, $row)->getAlignment()->setHorizontal('center');;
$sheet->setCellValueByColumnAndRow(2, $row, 'ชื่อ-นามสกุล / ตำแหน่ง')->getStyleByColumnAndRow(2, $row)->getAlignment()->setHorizontal('center');
$sheet->setCellValueByColumnAndRow(3, $row, 'หน่วยงาน / ที่อยู่')->getStyleByColumnAndRow(3, $row)->getAlignment()->setHorizontal('center');
$sheet->setCellValueByColumnAndRow(4, $row, 'โทรศัพท์ / อีเมล')->getStyleByColumnAndRow(4, $row)->getAlignment()->setHorizontal('center');

$sheet->getRowDimension($row)->setRowHeight(-1); // set auto height

define('START_ROW', 3);
$row = START_ROW;
foreach ($traineeList as $trainee) {
    switch ($serviceType) {
        case SERVICE_TYPE_TRAINING:
            //$displayAddress = $trainee['organization_name'];

            $province = trim(str_replace(array('จังหวัด', 'จ.'), '', $trainee['receipt_province']));

            if (mb_substr($province, 0, 4) == 'กรุง' || mb_substr($province, 0, 2) == 'กท') {
                $displayAddress = "{$trainee['receipt_name']} ที่อยู่ {$trainee['receipt_address']} แขวง{$trainee['receipt_sub_district']} เขต{$trainee['receipt_district']} "
                    . "กรุงเทพมหานคร {$trainee['receipt_postal_code']} โทร. {$trainee['receipt_organization_phone']}";
            } else {
                $displayAddress = "{$trainee['receipt_name']} ที่อยู่ {$trainee['receipt_address']} ต.{$trainee['receipt_sub_district']} อ.{$trainee['receipt_district']} "
                    . "จ.{$province} {$trainee['receipt_postal_code']} โทร. {$trainee['receipt_organization_phone']}";
            }
            break;
        case SERVICE_TYPE_SOCIAL:
            $province = trim(str_replace(array('จังหวัด', 'จ.'), '', $trainee['province']));

            if (mb_substr($province, 0, 4) == 'กรุง' || mb_substr($province, 0, 2) == 'กท') {
                $displayAddress = "{$trainee['address']} แขวง{$trainee['sub_district']} เขต{$trainee['district']} กรุงเทพมหานคร {$trainee['postal_code']}";
            } else {
                $displayAddress = "{$trainee['address']} ต.{$trainee['sub_district']} อ.{$trainee['district']} จ.{$province} {$trainee['postal_code']}";
            }
            break;
        case SERVICE_TYPE_DRIVING_LICENSE:
            $province = trim(str_replace(array('จังหวัด', 'จ.'), '', $trainee['province']));

            if (mb_substr($province, 0, 4) == 'กรุง' || mb_substr($province, 0, 2) == 'กท') {
                $displayAddress = "{$trainee['address']} หมู่ {$trainee['moo']} ซ.{$trainee['soi']} ถ.{$trainee['road']} แขวง{$trainee['sub_district']} เขต{$trainee['district']} "
                    . "กรุงเทพมหานคร {$trainee['postal_code']}";
            } else {
                $displayAddress = "{$trainee['address']} หมู่ {$trainee['moo']} ซ.{$trainee['soi']} ถ.{$trainee['road']} ต.{$trainee['sub_district']} อ.{$trainee['district']} "
                    . "จ.{$province} {$trainee['postal_code']}";
            }
            break;
    }

    $sheet->setCellValueByColumnAndRow(1, $row, ($row - START_ROW) + 1)->getStyleByColumnAndRow(1, $row)->getAlignment()->setVertical('top')->setHorizontal('center');

    $displayName = "{$trainee['title']}{$trainee['first_name']} {$trainee['last_name']}\n{$trainee['job_position']}";
    $sheet->setCellValueByColumnAndRow(2, $row, $displayName)->getStyleByColumnAndRow(2, $row)->getAlignment()->setVertical('top');
    $sheet->setCellValueByColumnAndRow(3, $row, $displayAddress)->getStyleByColumnAndRow(3, $row)->getAlignment()->setVertical('top');
    $contact = "{$trainee['phone']}\n{$trainee['email']}";
    $sheet->setCellValueByColumnAndRow(4, $row, $contact)->getStyleByColumnAndRow(4, $row)->getAlignment()->setVertical('top')->setHorizontal('left');

    $sheet->getRowDimension($row)->setRowHeight(-1); // set auto height
    $row++;
}

//$writer = new Xlsx($spreadsheet);
$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xls');

//$writer->save('hello world.xlsx');
$writer->save("php://output");

require_once '../include/foot_php.inc';
