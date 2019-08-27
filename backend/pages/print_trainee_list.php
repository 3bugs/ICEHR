<?php
ob_start();
require_once '../vendor/autoload.php';
require_once '../include/head_php.inc';

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
    $sql = "SELECT ct.title, ct.first_name, ct.last_name, ct.job_position, ct.organization_name, ct.phone, ct.email
            FROM course c 
                INNER JOIN course_registration cr 
                    ON cr.course_id = c.id 
                INNER JOIN course_trainee ct 
                    ON ct.course_registration_id = cr.id 
            WHERE c.id = $courseId AND ct.register_status = 'complete'";

} else if ($serviceType === SERVICE_TYPE_SOCIAL) {
    $sql = "SELECT cr.title, cr.first_name, cr.last_name, cr.occupation AS job_position, cr.phone, cr.email, 
                   cr.address, cr.sub_district, cr.district, cr.province, cr.postal_code
            FROM course c 
                INNER JOIN course_registration_social cr 
                    ON cr.course_id = c.id  
            WHERE c.id = $courseId AND " . ($courseApplicationFee === 0 ? "cr.register_status <> 'cancel'" : "cr.register_status = 'complete'");

} else if ($serviceType === SERVICE_TYPE_DRIVING_LICENSE) {
    $sql = "SELECT cr.title, cr.first_name, cr.last_name, cr.phone, 
                   cr.address, cr.moo, cr.soi, cr.road, cr.sub_district, cr.district, cr.province, cr.postal_code
            FROM course c 
                INNER JOIN course_registration_driving_license cr 
                    ON cr.course_id = c.id  
            WHERE c.id = $courseId AND cr.register_status = 'complete'";
}

if ($result = $db->query($sql)) {
    if ($result->num_rows > 0) {
        $traineeList = array();

        while ($trainee = $result->fetch_assoc()) {
            array_push($traineeList, $trainee);
        }
        $result->close();
    } else {
        echo 'Error: ไม่มีข้อมูลผู้เข้ารับการอบรมในหลักสูตรนี้';
        $result->close();
        $db->close();
        exit();
    }
} else {
    echo 'เกิดข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล: ' . $db->error . $sql;
    $db->close();
    exit();
}

$defaultConfig = (new Mpdf\Config\ConfigVariables())->getDefaults();
$fontDirs = $defaultConfig['fontDir'];

$defaultFontConfig = (new Mpdf\Config\FontVariables())->getDefaults();
$fontData = $defaultFontConfig['fontdata'];

$mpdf = new \Mpdf\Mpdf([
    'mode' => 'UTF-8',
    'format' => 'A4-L', //หน้ากระดาษ A4 แนวนอน
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
    'default_font_size' => 14,
]);
?>
    <!DOCTYPE html>
    <html lang="th">
    <head>
        <meta charset="utf-8">
        <title>:: ทำเนียบผู้เข้ารับการอบรม ::</title>

        <style type="text/css">
            @Page {
                margin: 40px 80px;
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
    <div style="text-align: center; font-size: 34px; font-weight: bold">
        <img src="../images/logo_icehr.svg"
             width="250px"
             style="vertical-align: middle"/>&nbsp;&nbsp;<span>ทำเนียบผู้เข้ารับการอบรม</span>
    </div>
    <div style="text-align: center; font-size: 26px; margin: 5px 0;"><?= $courseDisplayName; ?></div>
    <div style="text-align: center; font-size: 20px;"><?= $courseDisplayDate; ?></div>
    <div style="text-align: center; font-size: 20px;"><?= $courseDisplayPlace; ?></div>

    <table width="100%" align="center" cellspacing="0" cellpadding="5px"
           style="border: 1px solid #000000; margin-top: 10px">
        <tr>
            <th style="border: 0.1pt solid #a0a0a0; vertical-align: top" width="7%">ลำดับ</th>
            <th style="border: 0.1pt solid #a0a0a0; vertical-align: top" width="23%">ชื่อ-นามสกุล / ตำแหน่ง</th>
            <th style="border: 0.1pt solid #a0a0a0; vertical-align: top" width="48%">หน่วยงาน / ที่อยู่</th>
            <th style="border: 0.1pt solid #a0a0a0; vertical-align: top" width="22%">โทรศัพท์ / อีเมล</th>
        </tr>
        <?php
        $i = 0;

        foreach ($traineeList as $trainee) {
            $i++;
            switch ($serviceType) {
                case SERVICE_TYPE_TRAINING:
                    $displayAddress = $trainee['organization_name'];
                    break;
                case SERVICE_TYPE_SOCIAL:
                    $displayAddress = "{$trainee['address']} ต.{$trainee['sub_district']} อ.{$trainee['district']} จ.{$trainee['province']} {$trainee['postal_code']}";
                    if (strpos($displayAddress, 'กรุง') || strpos($displayAddress, 'กทม')) {
                        $displayAddress = str_replace('ต.', 'แขวง', $displayAddress);
                        $displayAddress = str_replace('อ.', 'เขต', $displayAddress);
                        $displayAddress = str_replace('จ.', '', $displayAddress);
                    }
                    break;
                case SERVICE_TYPE_DRIVING_LICENSE:
                    $displayAddress = "{$trainee['address']} หมู่ {$trainee['moo']} ซ.{$trainee['soi']} ถ.{$trainee['road']} ต.{$trainee['sub_district']} อ.{$trainee['district']} จ.{$trainee['province']} {$trainee['postal_code']}";
                    if (strpos($displayAddress, 'กรุง') || strpos($displayAddress, 'กทม')) {
                        $displayAddress = str_replace('ต.', 'แขวง', $displayAddress);
                        $displayAddress = str_replace('อ.', 'เขต', $displayAddress);
                        $displayAddress = str_replace('จ.', '', $displayAddress);
                    }
                    break;
            }
            ?>
            <tr>
                <td style="border: 0.1pt solid #a0a0a0; text-align: center; vertical-align: top"><?= $i; ?></td>
                <td style="border: 0.1pt solid #a0a0a0; vertical-align: top">
                    <?= "{$trainee['title']} {$trainee['first_name']} {$trainee['last_name']}"; ?><br>
                    <?= $trainee['job_position']; ?>
                </td>
                <td style="border: 0.1pt solid #a0a0a0; vertical-align: top">
                    <?= $displayAddress; ?>
                </td>
                <td style="border: 0.1pt solid #a0a0a0; vertical-align: top">
                    <?= $trainee['phone']; ?><br>
                    <?= $trainee['email']; ?>
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