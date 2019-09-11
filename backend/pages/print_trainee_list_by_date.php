<?php
ob_start();
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
        echo 'Error: ไม่มีข้อมูลผู้เข้ารับการอบรมในช่วงเวลาที่เลือก';
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

$defaultConfig = (new Mpdf\Config\ConfigVariables())->getDefaults();
$fontDirs = $defaultConfig['fontDir'];

$defaultFontConfig = (new Mpdf\Config\FontVariables())->getDefaults();
$fontData = $defaultFontConfig['fontdata'];

$mpdf = new \Mpdf\Mpdf([
    'mode' => 'UTF-8',
    'format' => 'A4', //หน้ากระดาษ A4
    'fontDir' => array_merge($fontDirs, [
        __DIR__ . '/../fonts',
    ]),
    'fontdata' => $fontData + [
            'dbheavent' => [
                'R' => 'DBHeavent.ttf',
                'B' => 'DBHeavent-Med.ttf',
            ]
        ],
    'default_font' => 'dbheavent',
    'default_font_size' => 13,
]);
?>
    <!DOCTYPE html>
    <html lang="th">
    <head>
        <meta charset="utf-8">
        <title>:: รายงานสรุปผู้เข้ารับการอบรม ::</title>

        <style type="text/css">
            @Page {
                margin: 40px 40px;
            }

            .txtDash {
                border-bottom: 2px dotted #000000;
                padding-left: 10px;
                padding-bottom: 0px;
                margin-bottom: 0px;
            }

            td {
                line-height: 100%;
            }
        </style>
    </head>
    <body>
    <div style="text-align: center; font-size: 30px; font-weight: bold">
        <img src="../images/logo_icehr.svg"
             width="300px"
             style="vertical-align: middle"/><br>
        <span>รายงานสรุปผู้เข้ารับการอบรม</span>
    </div>
    <?php
    $courseDisplayName = "หลักสูตร {$courseTitle}";
    $courseDisplayDate = 'ประจำเดือน' . $monthNames[$month - 1] . ' ' . thaiNumDigit($year + 543);
    $academyName = 'สถาบันเสริมศึกษาและทรัพยากรมนุษย์ มหาวิทยาลัยธรรมศาสตร์';
    $academyAddress = thaiNumDigit('เลขที่ 2 ถนนพระจันทร์ แขวงพระบรมมหาราชวัง เขตพระนคร กรุงเทพมหานคร');
    $traineeCountText = 'จำนวนผู้เข้ารับการอบรมทั้งหมด ' . sizeof($traineeList) . ' คน';
    ?>
    <div style="text-align: center; font-size: 25px; margin: 5px 0;"><?= $courseDisplayName; ?></div>
    <div style="text-align: center; font-size: 25px;"><u><?= $courseDisplayDate; ?></u></div>
    <!--<div style="text-align: center; font-size: 20px;"><? /*= $courseDisplayPlace; */ ?></div>-->

    <div style="text-align: left; font-size: 14pt; margin-top: 15px"><strong>ชื่อสถาบันการศึกษา</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?= $academyName; ?></div>
    <div style="text-align: left; font-size: 14pt;"><strong>ที่ตั้ง</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?= $academyAddress; ?></div>
    <div style="text-align: left; font-size: 16pt; margin-top: 15px"><strong><?= thaiNumDigit($traineeCountText); ?></strong></div>

    <table width="100%" align="center" cellspacing="0" cellpadding="5px"
           style="border: 1px solid #000000; margin-top: 10px">
        <tr>
            <th style="border: 0.1pt solid #a0a0a0; vertical-align: top" width="6%">ลำดับ</th>
            <th style="border: 0.1pt solid #a0a0a0; vertical-align: top" width="11%">วันที่อบรม</th>
            <th style="border: 0.1pt solid #a0a0a0; vertical-align: top" width="27%">ชื่อ-นามสกุล<br>ผู้สมัครเข้ารับการอบรม</th>
            <th style="border: 0.1pt solid #a0a0a0; vertical-align: top" width="18%">เลขประจำตัว<br>ประชาชน</th>
            <th style="border: 0.1pt solid #a0a0a0; vertical-align: top" width="22%">ประเภทใบอนุญาตขับรถ<br>ที่ขอรับ/ต่ออายุ</th>
            <th style="border: 0.1pt solid #a0a0a0; vertical-align: top" width="16%">เลขที่หนังสือรับรอง<br>การผ่านการอบรม</th>
        </tr>
        <?php
        $i = 0;

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
                        $displayLicenseType .= ',&nbsp;<br>';
                    } else {
                        $firstLine = false;
                    }
                    $displayLicenseType .= $licenseType['text']. ($traineeCourseType === 1 ? 'ชั่วคราว' : '');
                }
            }
            ?>
            <tr>
                <td style="border: 0.1pt solid #a0a0a0; text-align: center; vertical-align: top"><?= thaiNumDigit($i); ?></td>
                <td style="border: 0.1pt solid #a0a0a0; text-align: center; vertical-align: top">
                    <?= thaiNumDigit(getThaiShortDate(date_create($trainee['course_date']))); ?>
                </td>
                <td style="border: 0.1pt solid #a0a0a0; vertical-align: top">
                    <?= "{$trainee['title']}{$trainee['first_name']} {$trainee['last_name']}"; ?>
                </td>
                <td style="border: 0.1pt solid #a0a0a0; text-align: center; vertical-align: top">
                    <?= thaiNumDigit(formatPid($trainee['pid'], '&nbsp;')); ?>
                </td>
                <td style="border: 0.1pt solid #a0a0a0; vertical-align: top">
                    <?= $displayLicenseType; ?>
                </td>
                <td style="border: 0.1pt solid #a0a0a0; vertical-align: top">
                    <?= "ที่ อว ๖๗.๔๙ /<br>ขสต ๒๕๖๒ / $certificateNumber"; ?>
                </td>
            </tr>
            <?php
        }
        ?>
    </table>
    </body>
    </html>

<?php
$html = ob_get_contents();
ob_end_clean();

$mpdf->WriteHTML($html);
$mpdf->Output();

require_once '../include/foot_php.inc';