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
            $courseDisplayDate = getThaiDate(date_create($row['begin_date']));
        } else {
            $courseDisplayDate = getThaiDate(date_create($row['begin_date'])) . ' - ' . getThaiDate(date_create($row['end_date']));
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
    /*$sql = "SELECT ct.title, ct.first_name, ct.last_name, ct.job_position, ct.organization_name, ct.phone, ct.email,
                   cr.receipt_name, cr.receipt_address, cr.receipt_sub_district, cr.receipt_district, cr.receipt_province, cr.receipt_postal_code, cr.receipt_organization_phone
            FROM course c 
                INNER JOIN course_registration cr 
                    ON cr.course_id = c.id 
                INNER JOIN course_trainee ct 
                    ON ct.course_registration_id = cr.id 
            WHERE c.id = $courseId AND ct.register_status = 'complete' 
            ORDER BY ct.first_name, ct.last_name";*/

    echo 'Error: ไม่พบข้อมูลหลักสูตรใบขับขี่';
    $db->close();
    exit();

} else if ($serviceType === SERVICE_TYPE_SOCIAL) {
    /*$sql = "SELECT cr.title, cr.first_name, cr.last_name, cr.occupation AS job_position, cr.phone, cr.email,
                   cr.address, cr.sub_district, cr.district, cr.province, cr.postal_code
            FROM course c 
                INNER JOIN course_registration_social cr 
                    ON cr.course_id = c.id  
            WHERE c.id = $courseId AND " . ($courseApplicationFee === 0 ? "cr.register_status <> 'cancel'" : "cr.register_status = 'complete'") . " ORDER BY cr.first_name, cr.last_name";*/

    echo 'Error: ไม่พบข้อมูลหลักสูตรใบขับขี่';
    $db->close();
    exit();

} else if ($serviceType === SERVICE_TYPE_DRIVING_LICENSE) {
    $sql = "SELECT cr.title, cr.first_name, cr.last_name, cr.phone, cr.pid, cr.course_type, cr.license_type
            FROM course c 
                INNER JOIN course_registration_driving_license cr 
                    ON cr.course_id = c.id  
            WHERE c.id = $courseId AND cr.register_status <> 'cancel' 
            ORDER BY cr.id";
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

$defaultConfig = (new Mpdf\Config\ConfigVariables())->getDefaults();
$fontDirs = $defaultConfig['fontDir'];

$defaultFontConfig = (new Mpdf\Config\FontVariables())->getDefaults();
$fontData = $defaultFontConfig['fontdata'];

$mpdf = new \Mpdf\Mpdf([
    'mode' => 'UTF-8',
    'format' => 'A4', //ถ้าแนวนอนใช้ A4-L
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
        <title>:: ใบเซ็นชื่อ ::</title>

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
    <div style="text-align: center;">
        <img src="../images/logo_icehr.svg" width="250px" style="vertical-align: middle"/>
    </div>
    <div style="text-align: center; font-size: 20px; margin: 2px 0;">รายชื่อผู้เข้ารับการอบรม</div>
    <div style="text-align: center; font-size: 20px; margin: 2px 0;"><strong><?= $courseDisplayName; ?></strong></div>
    <div style="text-align: center; font-size: 20px; margin: 2px 0;">ณ สถาบันเสริมศึกษาและทรัพยากรมนุษย์ มหาวิทยาลัยธรรมศาสตร์ ท่าพระจันทร์</div>
    <div style="text-align: center; font-size: 20px; margin: 2px 0;"><?= "{$courseDisplayDate}&nbsp;&nbsp;&nbsp; เวลา 08.30 - 14.30 น."; ?></div>

    <table width="100%" align="center" cellspacing="0" cellpadding="5px"
           style="border: 1px solid #000000; margin-top: 10px">
        <tr>
            <th style="border: 0.1pt solid #a0a0a0; vertical-align: middle" width="5%">ลำดับ</th>
            <!--<th style="border: 0.1pt solid #a0a0a0; vertical-align: middle" width="31%">ชื่อ-นามสกุล</th>-->
            <th style="border: 0.1pt solid #a0a0a0; vertical-align: middle" width="5%">คำนำหน้า</th>
            <th style="border: 0.1pt solid #a0a0a0; vertical-align: middle" width="15%">ชื่อ</th>
            <th style="border: 0.1pt solid #a0a0a0; vertical-align: middle" width="15%">นามสกุล</th>
            <th style="border: 0.1pt solid #a0a0a0; vertical-align: middle" width="20%">เลขที่บัตรประชาชน/<br>เลขที่หนังสือเดินทาง</th>
            <th style="border: 0.1pt solid #a0a0a0; vertical-align: middle" width="24%">ประเภทบัตร/<br>ประเภทรถ</th>
            <th style="border: 0.1pt solid #a0a0a0; vertical-align: middle" width="16%">ลายเซ็น</th>
        </tr>
        <?php
        $i = 0;

        $displayAddress = null;
        foreach ($traineeList as $trainee) {
            $i++;
            /*switch ($serviceType) {
                case SERVICE_TYPE_TRAINING:
                    //$displayAddress = $trainee['organization_name'];

                    if (mb_substr(trim($trainee['receipt_province']), 0, 4) == 'กรุง' || mb_substr(trim($trainee['receipt_province']), 0, 2) == 'กท') {
                        $displayAddress = "{$trainee['receipt_name']} ที่อยู่ {$trainee['receipt_address']} แขวง{$trainee['receipt_sub_district']} เขต{$trainee['receipt_district']} "
                            . "กรุงเทพฯ {$trainee['receipt_postal_code']} โทร. {$trainee['receipt_organization_phone']}";
                    } else {
                        $displayAddress = "{$trainee['receipt_name']} ที่อยู่ {$trainee['receipt_address']} ต.{$trainee['receipt_sub_district']} อ.{$trainee['receipt_district']} "
                            . "จ.{$trainee['receipt_province']} {$trainee['receipt_postal_code']} โทร. {$trainee['receipt_organization_phone']}";
                    }
                    break;
                case SERVICE_TYPE_SOCIAL:
                    if (mb_substr(trim($trainee['province']), 0, 4) == 'กรุง' || mb_substr(trim($trainee['province']), 0, 2) == 'กท') {
                        $displayAddress = "{$trainee['address']} แขวง{$trainee['sub_district']} เขต{$trainee['district']} กรุงเทพฯ {$trainee['postal_code']}";
                    } else {
                        $displayAddress = "{$trainee['address']} ต.{$trainee['sub_district']} อ.{$trainee['district']} จ.{$trainee['province']} {$trainee['postal_code']}";
                    }
                    break;
                case SERVICE_TYPE_DRIVING_LICENSE:
                    if (mb_substr(trim($trainee['province']), 0, 4) == 'กรุง' || mb_substr(trim($trainee['province']), 0, 2) == 'กท') {
                        $displayAddress = "{$trainee['address']} หมู่ {$trainee['moo']} ซ.{$trainee['soi']} ถ.{$trainee['road']} แขวง{$trainee['sub_district']} เขต{$trainee['district']} "
                            . "กรุงเทพฯ {$trainee['postal_code']}";
                    } else {
                        $displayAddress = "{$trainee['address']} หมู่ {$trainee['moo']} ซ.{$trainee['soi']} ถ.{$trainee['road']} ต.{$trainee['sub_district']} อ.{$trainee['district']} "
                            . "จ.{$trainee['province']} {$trainee['postal_code']}";
                    }
                    break;
            }*/

            $licenseType = (int)$trainee['license_type'];
            $licenseTypeText = '';
            if (($licenseType & 1) > 0) {
                $licenseTypeText .= 'รถยนต์/';
            }
            if (($licenseType & 2) > 0) {
                $licenseTypeText .= 'จยย./';
            }
            if (($licenseType & 4) > 0) {
                $licenseTypeText .= 'สามล้อ/';
            }
            $licenseTypeText = mb_substr($licenseTypeText, 0, -1);

            $pid = trim($trainee['pid']);
            ?>

            <tr>
                <td style="border: 0.1pt solid #a0a0a0; text-align: center; vertical-align: top"><?= $i; ?></td>
                <td style="border: 0.1pt solid #a0a0a0; vertical-align: top">
                    <?= "{$trainee['title']}"; ?>
                </td>
                <td style="border: 0.1pt solid #a0a0a0; vertical-align: top">
                    <?= "{$trainee['first_name']}"; ?>
                </td>
                <td style="border: 0.1pt solid #a0a0a0; vertical-align: top">
                    <?= "{$trainee['last_name']}"; ?>
                </td>
                <td style="border: 0.1pt solid #a0a0a0; text-align: center; vertical-align: top">
                    <?= strlen($pid) === 13 ? formatPid($pid) : $pid; ?>
                </td>
                <td style="border: 0.1pt solid #a0a0a0; vertical-align: top">
                    <?= (int)$trainee['course_type'] === 1 ? 'ขอใหม่/' : 'ต่ออายุ/'; ?><?= $licenseTypeText; ?>
                </td>
                <td style="border: 0.1pt solid #a0a0a0; text-align: center; vertical-align: top">
                    &nbsp;<?= ''; //ลายเซ็น  ?>
                </td>
            </tr>
            <?php
        }

        for ($j = 0; $j < 0; $j++) {
            ?>
            <tr>
                <td style="border: 0.1pt solid #a0a0a0; text-align: center; vertical-align: top"><?= ++$i; ?></td>
                <td style="border: 0.1pt solid #a0a0a0; vertical-align: top">&nbsp;</td>
                <td style="border: 0.1pt solid #a0a0a0; text-align: center; vertical-align: top">&nbsp;</td>
                <td style="border: 0.1pt solid #a0a0a0; vertical-align: top">&nbsp;</td>
                <td style="border: 0.1pt solid #a0a0a0; text-align: center; vertical-align: top">&nbsp;</td>
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