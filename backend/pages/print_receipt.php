<?php
require_once '../include/head_php.inc';
require('../dist/fpdf/fpdf2.php');

$serviceType = $_GET['service_type'];
if (!isset($serviceType)) {
    echo 'Error: ไม่ได้ระบุประเภทบริการ (วิชาการ/ใบขับขี่)';
    $db->close();
    exit();
}
$traineeId = $_GET['trainee_id'];
if (!isset($traineeId)) {
    echo 'Error: ไม่ได้ระบุ ID ใบสมัคร';
    $db->close();
    exit();
}

if ($serviceType === SERVICE_TYPE_TRAINING) {
    $sql = "SELECT ct.form_number, ct.title, ct.first_name, ct.last_name, cr.receipt_name, cr.receipt_address, cr.receipt_sub_district, 
                   cr.receipt_district, cr.receipt_province, cr.receipt_postal_code, cr.receipt_tax_id,
                   cm.title AS course_title, c.batch_number AS course_batch_number, c.application_fee AS course_fee, ct.paid_amount
            FROM course_trainee ct 
                INNER JOIN course_registration cr 
                    ON cr.id = ct.course_registration_id 
                INNER JOIN course c 
                    ON c.id = cr.course_id 
                INNER JOIN course_master cm 
                    ON cm.id = c.course_master_id 
            WHERE ct.id = $traineeId";
} else if ($serviceType === SERVICE_TYPE_DRIVING_LICENSE) {
    $sql = "SELECT cr.form_number, cr.title, cr.first_name, cr.last_name, cr.pid, cr.address, cr.moo, cr.soi, cr.road, 
               cr.sub_district, cr.district, cr.province, cr.phone,
               cr.course_type, cr.license_type, dlct.application_fee AS course_fee, cr.paid_amount
            FROM course_registration_driving_license cr 
                INNER JOIN course c 
                    ON c.id = cr.course_id 
                INNER JOIN driving_license_course_type dlct 
                    ON dlct.id = cr.course_type
            WHERE cr.id = $traineeId";
} else {
    echo 'Error: Invalid service type - ประเภทบริการไม่ถูกต้อง';
    $db->close();
    exit();
}

if ($result = $db->query($sql)) {
    if ($result->num_rows > 0) {
        $trainee = $result->fetch_assoc();
        $result->close();

        if ($serviceType == SERVICE_TYPE_TRAINING) {
            $trainee['course_name'] = "{$trainee['course_title']} รุ่นที่ {$trainee['course_batch_number']}";
        } else if ($serviceType == SERVICE_TYPE_DRIVING_LICENSE) {
            $trainee['course_name'] = ((int)$trainee['course_type'] === 1)
                ? 'หลักสูตรการอบรมสำหรับผู้ขอรับใบอนุญาตขับรถ'
                : 'หลักสูตรการอบรมสำหรับผู้ขอต่ออายุใบอนุญาตขับรถ';
            $trainee['receipt_name'] = "{$trainee['title']}{$trainee['first_name']} {$trainee['last_name']}";
        }
    } else {
        echo 'Error: ไม่พบข้อมูล';
        $result->close();
        $db->close();
        exit();
    }
} else {
    echo 'เกิดข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล: ' . $db->error . $sql;
    $db->close();
    exit();
}

function convert($number)
{
    $txtNum1 = array('ศูนย์', 'หนึ่ง', 'สอง', 'สาม', 'สี่', 'ห้า', 'หก', 'เจ็ด', 'แปด', 'เก้า', 'สิบ');
    $txtNum2 = array('', 'สิบ', 'ร้อย', 'พัน', 'หมื่น', 'แสน', 'ล้าน', 'สิบ', 'ร้อย', 'พัน', 'หมื่น', 'แสน', 'ล้าน');
    $number = str_replace(",", "", $number);
    $number = str_replace(" ", "", $number);
    $number = str_replace("บาท", "", $number);
    $number = explode(".", $number);

    if (sizeof($number) > 2) {
        return 'Error: มีจุดทศนิยมมากกว่า 1 ตัว';
    }

    $length = strlen($number[0]);
    $convert = '';

    for ($i = 0; $i < $length; $i++) {
        $n = substr($number[0], $i, 1);
        if ($n != 0) {
            if ($i == ($length - 1) AND $n == 1) {
                $convert .= 'เอ็ด';
            } elseif ($i == ($length - 2) AND $n == 2) {
                $convert .= 'ยี่';
            } elseif ($i == ($length - 2) AND $n == 1) {
                $convert .= '';
            } else {
                $convert .= $txtNum1[$n];
            }
            $convert .= $txtNum2[$length - $i - 1];
        }
    }

    $convert .= 'บาท';
    if ($number[1] == '0' OR $number[1] == '00' OR $number[1] == '') {
        $convert .= 'ถ้วน';
    } else {
        $length = strlen($number[1]);
        for ($i = 0; $i < $length; $i++) {
            $n = substr($number[1], $i, 1);
            if ($n != 0) {
                if ($i == ($length - 1) AND $n == 1) {
                    $convert .= 'เอ็ด';
                } elseif ($i == ($length - 2) AND $n == 2) {
                    $convert .= 'ยี่';
                } elseif ($i == ($length - 2) AND $n == 1) {
                    $convert .= '';
                } else {
                    $convert .= $txtNum1[$n];
                }
                $convert .= $txtNum2[$length - $i - 1];
            }
        }
        $convert .= 'สตางค์';
    }
    return $convert;
}

//Create new pdf file
$pdf = new FPDF('P', 'mm', array(190, 200));
$pdf->SetTopMargin(0);

$pdf->AddFont('AngsanaNew', '', 'angsana.php');
$pdf->AddFont('AngsanaNew', 'B', 'angsanab.php');

//Disable automatic page break
$pdf->SetAutoPageBreak(false);

//Add first page
$pdf->AddPage();

$pdf->SetFont('AngsanaNew', '', 14);
$pdf->SetXY(80, 22);
//$pdf->SetXY(80,18);
$pdf->Cell(50, 6, getThaiShortDate(new DateTime()), 0, 0, 'L', 0);
$pdf->SetXY(76, 27);
//$pdf->SetXY(76,24);
$pdf->Cell(50, 6, $trainee['receipt_name'], 0, 0, 'L', 0);
$pdf->SetXY(60, 34);
//$pdf->SetXY(60,30);

$address = '';
if ($serviceType === SERVICE_TYPE_TRAINING) {
    $address = "{$trainee['receipt_address']} แขวง/ตำบล{$trainee['receipt_sub_district']} เขต/อำเภอ{$trainee['receipt_district']} จังหวัด{$trainee['receipt_province']}";
} else if ($serviceType === SERVICE_TYPE_DRIVING_LICENSE) {
    $address = "{$trainee['address']} หมู่ {$trainee['moo']} ซอย{$trainee['soi']} ถนน{$trainee['road']} แขวง/ตำบล{$trainee['sub_district']} เขต/อำเภอ{$trainee['district']} จังหวัด{$trainee['province']}";
}
$pdf->MultiCell(300, 5, $address, 0, 'L');
$pdf->SetXY(34, 55);
//$pdf->SetXY(40,53);
$pdf->Cell(13, 6, '', 0, 0, 'C', 0);
$pdf->MultiCell(300, 6, "หลักสูตร{$trainee['course_name']}", 0, 'L');

$pdf->SetXY(47, 70);
$pdf->Cell(110, 7, "ค่าลงทะเบียนอบรม", 0, 0, 'L', 0);
$pdf->Cell(25, 7, number_format($trainee['course_fee'], 2), 0, 0, 'R', 0);

if ($serviceType === SERVICE_TYPE_TRAINING) {
    if ((int)$trainee['course_fee'] > (int)$trainee['paid_amount']) {
        $pdf->SetXY(47, 78);
        $pdf->Cell(110, 7, 'ส่วนลด', 0, 0, 'L', 0);
        $pdf->Cell(25, 7, '(' . number_format((int)$trainee['course_fee'] - (int)$trainee['paid_amount'], 2) . ')', 0, 0, 'R', 0);
    }

    $pdf->SetXY(47, 86);
    $pdf->Cell(110, 7, "{$trainee['title']}{$trainee['first_name']} {$trainee['last_name']}", 0, 0, 'L', 0);
}

$pdf->SetXY(47, 148);
//$pdf->SetXY(53,135);
$pdf->Cell(110, 7, '(' . convert($trainee['paid_amount']) . ')', 0, 0, 'L', 0);
$pdf->Cell(25, 7, number_format($trainee['paid_amount'], 2), 0, 0, 'R', 0);

//Send file
$pdf->Output();

require_once '../include/foot_php.inc';
