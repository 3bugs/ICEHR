<?php
require_once '../vendor/autoload.php';
require_once '../include/head_php.inc';

$month = trim($_GET['month']);
$year = trim($_GET['year']);
if (!isset($month) || !isset($year) || !is_numeric($month) || !is_numeric($year) || strlen($month) > 2 || strlen($year) !== 4) {
    echo 'Error: ระบุ parameter ไม่ถูกต้อง';
    $db->close();
    exit();
}
//$month = substr('0' . trim($month), -2);

$sql = "SELECT cr.title, cr.first_name, cr.last_name, cr.pid, cr.course_type, cr.license_type, cr.created_at AS register_date, 
               cr.certificate_number, c.begin_date AS course_date 
        FROM course_registration_driving_license cr 
            INNER JOIN course c 
                ON c.id = cr.course_id 
        WHERE MONTH(c.begin_date) = $month AND YEAR(c.begin_date) = $year AND cr.certificate_number IS NOT NULL 
        ORDER BY cr.certificate_number";

if ($result = $db->query($sql)) {
    if ($result->num_rows > 0) {
        $traineeList = array();

        while ($trainee = $result->fetch_assoc()) {
            array_push($traineeList, $trainee);
        }
        $result->close();
    } else {
        echo 'ไม่มีข้อมูลผู้เข้ารับการอบรมในช่วงเวลาที่เลือก';
        $result->close();
        $db->close();
        exit();
    }
} else {
    echo 'เกิดข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล (1): ' . $db->error . $sql;
    $db->close();
    exit();
}

$drivingLicenseService = SERVICE_TYPE_DRIVING_LICENSE;
$sql = "SELECT title FROM course_master WHERE service_type = '$drivingLicenseService'";
if ($result = $db->query($sql)) {
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $courseTitle = $row['title'];
        $result->close();
    } else {
        echo 'Error: ไม่พบชื่อหลักสูตรบริการใบขับขี่ !!!';
        $result->close();
        $db->close();
        exit();
    }
} else {
    echo 'เกิดข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล (2): ' . $db->error . $sql;
    $db->close();
    exit();
}

$fileName = "Summary-{$year}-{$month}.docx";

header("Content-Type: application/vnd.openxmlformats-officedocument.wordprocessing‌​ml.document");
header('Content-Disposition: attachment; filename="' . $fileName . '"');
header('Cache-Control: max-age=0');

$phpWord = new \PhpOffice\PhpWord\PhpWord();

$phpWord->addFontStyle(
    'fStyleTitle', array(
        'name' => 'Angsana New',
        'bold' => true,
        'size' => 16
    )
);
$phpWord->addFontStyle(
    'fStyleTitleUnderline', array(
        'name' => 'Angsana New',
        'bold' => true,
        'underline' => 'single',
        'size' => 16,
    )
);
$phpWord->addFontStyle(
    'fStyleBody', array(
        'name' => 'Angsana New',
        'bold' => false,
        'size' => 14
    )
);
$phpWord->addFontStyle(
    'fStyleBodyBold', array(
        'name' => 'Angsana New',
        'bold' => true,
        'size' => 14
    )
);
$phpWord->addParagraphStyle(
    'pStyleTitle', array(
        'align' => 'center',
        'spaceAfter' => 0
    )
);

$section = $phpWord->addSection([
    'paperSize' => 'A4',
    'marginLeft' => 700,
    'marginRight' => 700,
    'marginTop' => 1000,
    'marginBottom' => 1000,
]);

$title = 'รายงานสรุปผลการอบรมประจำเดือน';
$courseDisplayName = "หลักสูตร {$courseTitle}";
$courseDisplayDate = 'ประจำเดือน' . $monthNames[$month - 1] . ' ' . thaiNumDigit($year + 543);
$academyName = 'สถาบันเสริมศึกษาและทรัพยากรมนุษย์ มหาวิทยาลัยธรรมศาสตร์';
$academyAddress = thaiNumDigit('เลขที่ 2 ถนนพระจันทร์ แขวงพระบรมมหาราชวัง เขตพระนคร กรุงเทพมหานคร');
$traineeCountText = 'จำนวนผู้เข้ารับการอบรมทั้งหมด ' . sizeof($traineeList) . ' คน';

$section->addImage(
    dirname(__FILE__) . '/../images/logo_icehr.png',
    array(
        'height' => 60,
        'align' => 'center'
    )
);

$text = $section->addText($title, 'fStyleTitle', 'pStyleTitle');
$text = $section->addText($courseDisplayName, 'fStyleTitle', 'pStyleTitle');
$text = $section->addText($courseDisplayDate, 'fStyleTitleUnderline', 'pStyleTitle');
$text = $section->addText('');

$textRun = $section->addTextRun();
$textRun->addText('ชื่อสถาบันการศึกษา   ', 'fStyleBodyBold');
$textRun->addText($academyName, 'fStyleBody');

$textRun = $section->addTextRun();
$textRun->addText('ที่ตั้ง   ', 'fStyleBodyBold');
$textRun->addText($academyAddress, 'fStyleBody');
$text = $section->addText('');

$text = $section->addText(thaiNumDigit($traineeCountText), 'fStyleBodyBold');
$text = $section->addText('');

$fancyTableStyleName = 'MyTable';
$fancyTableStyle = array(
    'borderSize' => 1,
    'borderColor' => '333333',
    'cellMargin' => 0,
    'alignment' => \PhpOffice\PhpWord\SimpleType\JcTable::CENTER
);
$fancyTableFirstRowStyle = array(
    'borderBottomSize' => 1,
    'borderBottomColor' => '333333',
);
$fancyTableCellStyle = array(
    'valign' => 'center',
);
$fancyTableCellBtlrStyle = array(
    'valign' => 'center',
    'textDirection' => \PhpOffice\PhpWord\Style\Cell::TEXT_DIR_BTLR
);
$fancyTableFontStyle = array('name' => 'Angsana New', 'size' => 12, 'bold' => false);
$fancyTableFontStyleBold = array('name' => 'Angsana New', 'size' => 12, 'bold' => true);
$phpWord->addTableStyle($fancyTableStyleName, $fancyTableStyle, $fancyTableFirstRowStyle);
$table = $section->addTable($fancyTableStyleName);

$tableParagraphStyleCenter = new \PhpOffice\PhpWord\Style\Paragraph();
$tableParagraphStyleCenter->setAlignment('center');

$table->addRow();

$text = $table->addCell(700, $fancyTableCellStyle)->addText("ลำดับ\nที่", $fancyTableFontStyleBold);
$text->setParagraphStyle($tableParagraphStyleCenter);

$text = $table->addCell(1300, $fancyTableCellStyle)->addText("วัน/เดือน/ปี\nที่อบรม", $fancyTableFontStyleBold);
$text->setParagraphStyle($tableParagraphStyleCenter);

$text = $table->addCell(2500, $fancyTableCellStyle)->addText("ชื่อ-นามสกุล\nผู้สมัครเข้ารับการอบรม", $fancyTableFontStyleBold);
$text->setParagraphStyle($tableParagraphStyleCenter);

$text = $table->addCell(1900, $fancyTableCellStyle)->addText("เลขประจำตัว\nประชาชน", $fancyTableFontStyleBold);
$text->setParagraphStyle($tableParagraphStyleCenter);

$text = $table->addCell(2400, $fancyTableCellStyle)->addText("ประเภทใบอนุญาตขับรถ\nที่ขอรับ/ต่ออายุ", $fancyTableFontStyleBold);
$text->setParagraphStyle($tableParagraphStyleCenter);

$text = $table->addCell(1800, $fancyTableCellStyle)->addText("เลขที่หนังสือรับรอง\nการผ่านการอบรม", $fancyTableFontStyleBold);
$text->setParagraphStyle($tableParagraphStyleCenter);

$licenseTypeList = array(
    array(
        'value' => DL_LICENSE_TYPE_CAR,
        'text' => DL_LICENSE_TYPE_CAR_TEXT
    ),
    array(
        'value' => DL_LICENSE_TYPE_MOTOR_CYCLE,
        'text' => DL_LICENSE_TYPE_MOTOR_CYCLE_TEXT
    ),
    array(
        'value' => DL_LICENSE_TYPE_TRICYCLE,
        'text' => DL_LICENSE_TYPE_TRICYCLE_TEXT
    )
);

$i = 0;
foreach ($traineeList as $trainee) {
    $i++;
    $traineeCourseType = (int)$trainee['course_type'];
    $traineeLicenseType = (int)$trainee['license_type'];
    $displayLicenseType = '';
    $certificateNumber = thaiNumDigit($trainee['certificate_number']);

    $firstLine = true;
    foreach ($licenseTypeList as $licenseType) {
        if (($traineeLicenseType & $licenseType['value']) === $licenseType['value']) {
            if (!$firstLine) {
                $displayLicenseType .= ", \n";
            } else {
                $firstLine = false;
            }
            $displayLicenseType .= ($traineeCourseType !== 1 ? 'ต่ออายุ ' : '') . $licenseType['text'] . ($traineeCourseType === 1 ? 'ชั่วคราว' : '');
        }
    }

    $table->addRow();

    $text = $table->addCell(800, $fancyTableCellStyle)->addText(thaiNumDigit($i), $fancyTableFontStyle);
    $text->setParagraphStyle($tableParagraphStyleCenter);

    $text = $table->addCell(1600, $fancyTableCellStyle)->addText(thaiNumDigit(getThaiShortDateFullYear(date_create($trainee['course_date']))), $fancyTableFontStyle);
    $text->setParagraphStyle($tableParagraphStyleCenter);

    $text = $table->addCell(2400, $fancyTableCellStyle)->addText("{$trainee['title']}{$trainee['first_name']} {$trainee['last_name']}", $fancyTableFontStyle);

    $text = $table->addCell(1500, $fancyTableCellStyle)->addText(thaiNumDigit(formatPid($trainee['pid'], ' ')), $fancyTableFontStyle);
    $text->setParagraphStyle($tableParagraphStyleCenter);

    $text = $table->addCell(1500, $fancyTableCellStyle)->addText($displayLicenseType, $fancyTableFontStyle);

    $text = $table->addCell(1500, $fancyTableCellStyle)->addText("ที่ อว ๖๗.๔๙ /\nขสต ๒๕๖๒ / $certificateNumber", $fancyTableFontStyle);
}

$phpWord->addParagraphStyle(
    'pStyleSigning', array(
        'align' => 'center',
        'indentation' => array('left' => 4000, 'right' => 0),
        'spaceAfter' => 0
    )
);

$text = $section->addText('');
$text = $section->addText('');
$text = $section->addText('');
$text = $section->addText('');
$text = $section->addText("(ผู้ช่วยศาสตราจารย์ ดร.ศุภชัย ศรีสุชาติ)\nผู้อำนวยการสถาบันเสริมศึกษาและทรัพยากรมนุษย์", 'fStyleBody', 'pStyleSigning');

// Saving the document as OOXML file...
$writer = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
//$writer->save('helloWorld.docx');
$writer->save("php://output");

require_once '../include/foot_php.inc';