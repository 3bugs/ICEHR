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
$title = 'สมาชิกทั้งหมด';
$noDataText = 'ไม่มีข้อมูลสมาชิกในฐานข้อมูล';
if (isset($month) && isset($year)) {
    if ((int)$month === 0 && (int)$year === 0) {
        $whereDate = ' TRUE ';
        $title = 'สมาชิกทั้งหมด';
        $noDataText = 'ไม่มีข้อมูลสมาชิกในฐานข้อมูล';
    } elseif ((int)$month === 0) {
        $whereDate = " YEAR(m.created_at) = $year ";
        $yearBe = (int)$year + 543;
        $title = "สมาชิกที่สมัครในปี พ.ศ. $yearBe";
        $noDataText = "ไม่มีข้อมูลสมาชิกที่สมัครในปี พ.ศ. $yearBe";
    } else {
        $whereDate = " MONTH(m.created_at) = $month AND YEAR(m.created_at) = $year ";
        $monthName = $monthNames[$month - 1];
        $yearBe = (int)$year + 543;
        $title = "สมาชิกที่สมัครในเดือน$monthName ปี พ.ศ. $yearBe";
        $noDataText = "ไม่มีข้อมูลสมาชิกที่สมัครในเดือน$monthName ปี พ.ศ. $yearBe";
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

if (empty($organizationMemberList) && empty($personMemberList)) {
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

//$writer = new Xlsx($spreadsheet);
$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xls');

//$writer->save('hello world.xlsx');
$writer->save("php://output");

require_once '../include/foot_php.inc';

function generateSheet($memberList, $memberType, $sheet)
{
    global $title;

    $sheet->setTitle($memberType);
    if (empty($memberList)) {
        $sheet->setCellValueByColumnAndRow(1, 1, 'ไม่มีข้อมูล')->getStyleByColumnAndRow(1, 1)->getAlignment()->setVertical('top')->setHorizontal('left');
        return;
    }

    if ($memberType === MEMBER_TYPE_ORGANIZATION) {
        $sheet->mergeCells("A1:K1");
    } else {
        $sheet->mergeCells("A1:H1");
    }
    $sheet->setCellValueByColumnAndRow(1, 1, $title)->getStyleByColumnAndRow(1, 1)->getAlignment()->setVertical('top')->setHorizontal('center');

    $sheet->freezePane('A3');

    $sheet->getStyle("A1:Z1")->getFont()->setBold(true);
    $sheet->getStyle("A2:Z2")->getFont()->setBold(true);

    $sheet->getColumnDimension('A')->setAutoSize(true);
    $sheet->getColumnDimension('B')->setAutoSize(true);
    $sheet->getColumnDimension('C')->setAutoSize(true);
    $sheet->getColumnDimension('D')->setAutoSize(true);
    $sheet->getColumnDimension('E')->setAutoSize(true);

    $row = 2;
    $sheet->setCellValueByColumnAndRow(1, $row, 'ลำดับ')->getStyleByColumnAndRow(1, $row)->getAlignment()->setHorizontal('center');;
    $sheet->setCellValueByColumnAndRow(2, $row, 'ชื่อ-นามสกุล')->getStyleByColumnAndRow(2, $row)->getAlignment()->setHorizontal('center');
    $sheet->setCellValueByColumnAndRow(3, $row, 'เบอร์โทร')->getStyleByColumnAndRow(3, $row)->getAlignment()->setHorizontal('center');
    $sheet->setCellValueByColumnAndRow(4, $row, 'อีเมล')->getStyleByColumnAndRow(4, $row)->getAlignment()->setHorizontal('center');
    $sheet->setCellValueByColumnAndRow(5, $row, 'ตำแหน่งงาน')->getStyleByColumnAndRow(5, $row)->getAlignment()->setHorizontal('center');
    $sheet->setCellValueByColumnAndRow(6, $row, 'ชื่อหน่วยงาน')->getStyleByColumnAndRow(6, $row)->getAlignment()->setHorizontal('center');
    $sheet->setCellValueByColumnAndRow(7, $row, 'ประเภทหน่วยงาน')->getStyleByColumnAndRow(7, $row)->getAlignment()->setHorizontal('center');
    if ($memberType === MEMBER_TYPE_ORGANIZATION) {
        $sheet->setCellValueByColumnAndRow(8, $row, 'ที่อยู่หน่วยงาน')->getStyleByColumnAndRow(8, $row)->getAlignment()->setHorizontal('center');
        $sheet->setCellValueByColumnAndRow(9, $row, 'จังหวัด')->getStyleByColumnAndRow(9, $row)->getAlignment()->setHorizontal('center');
        $sheet->setCellValueByColumnAndRow(10, $row, 'เบอร์โทรหน่วยงาน')->getStyleByColumnAndRow(10, $row)->getAlignment()->setHorizontal('center');
        $sheet->setCellValueByColumnAndRow(11, $row, 'วันที่สมัคร')->getStyleByColumnAndRow(11, $row)->getAlignment()->setHorizontal('center');
    } else {
        $sheet->setCellValueByColumnAndRow(8, $row, 'วันที่สมัคร')->getStyleByColumnAndRow(8, $row)->getAlignment()->setHorizontal('center');
    }
    $sheet->getRowDimension($row)->setRowHeight(-1); // set auto height

    define('START_ROW', 3);
    $row = START_ROW;
    foreach ($memberList as $member) {
        $sheet->setCellValueByColumnAndRow(1, $row, ($row - START_ROW) + 1)->getStyleByColumnAndRow(1, $row)->getAlignment()->setVertical('top')->setHorizontal('center');

        $displayName = "{$member['title']}{$member['first_name']} {$member['last_name']}";
        $sheet->setCellValueByColumnAndRow(2, $row, $displayName)->getStyleByColumnAndRow(2, $row)->getAlignment()->setVertical('top');

        $sheet->setCellValueByColumnAndRow(3, $row, $member['phone'])->getStyleByColumnAndRow(3, $row)->getAlignment()->setVertical('top')->setHorizontal('left');
        $sheet->setCellValueByColumnAndRow(4, $row, $member['email'])->getStyleByColumnAndRow(4, $row)->getAlignment()->setVertical('top');
        $sheet->setCellValueByColumnAndRow(5, $row, $member['job_position'])->getStyleByColumnAndRow(5, $row)->getAlignment()->setVertical('top');
        $sheet->setCellValueByColumnAndRow(6, $row, $member['organization_name'])->getStyleByColumnAndRow(6, $row)->getAlignment()->setVertical('top');
        $sheet->setCellValueByColumnAndRow(7, $row, is_null($member['organization_type_name']) ? $member['organization_type_custom'] : $member['organization_type_name'])->getStyleByColumnAndRow(7, $row)->getAlignment()->setVertical('top');

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
            $sheet->setCellValueByColumnAndRow(8, $row, $displayAddress)->getStyleByColumnAndRow(8, $row)->getAlignment()->setVertical('top');

            $sheet->setCellValueByColumnAndRow(9, $row, $isBangkok ? 'กรุงเทพฯ' : $province)->getStyleByColumnAndRow(9, $row)->getAlignment()->setVertical('top');
            $sheet->setCellValueByColumnAndRow(10, $row, $member['organization_phone'])->getStyleByColumnAndRow(10, $row)->getAlignment()->setVertical('top')->setHorizontal('left');
            $sheet->setCellValueByColumnAndRow(11, $row, $member['created_at'])->getStyleByColumnAndRow(11, $row)->getAlignment()->setVertical('top')->setHorizontal('center');
        } else {
            $sheet->setCellValueByColumnAndRow(8, $row, $member['created_at'])->getStyleByColumnAndRow(8, $row)->getAlignment()->setVertical('top')->setHorizontal('center');
        }

        $sheet->getRowDimension($row)->setRowHeight(-1); // set auto height
        $row++;
    }
}