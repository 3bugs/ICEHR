<?php
ob_start();
require_once '../vendor/autoload.php';
require_once '../include/head_php.inc';

$traineeId = $_GET['trainee_id'];
if (!isset($traineeId)) {
    echo 'Error: ไม่ได้ระบุ ID ใบสมัคร';
    $db->close();
    exit();
}
$sql = "SELECT cr.form_number, cr.title, cr.first_name, cr.last_name, cr.pid, cr.address, cr.moo, cr.soi, cr.road, 
               cr.sub_district, cr.district, cr.province, cr.phone, cr.course_type, cr.license_type, 
               cr.certificate_number, c.begin_date AS course_date, YEAR(c.begin_date) + 543 AS course_year,
               t.title AS trainer_title, t.first_name AS trainer_first_name, t.last_name AS trainer_last_name, 
               t.signature_image AS trainer_signature_image, c.show_trainer_signature
        FROM course_registration_driving_license cr 
            INNER JOIN course c 
                ON c.id = cr.course_id 
            INNER JOIN trainer t 
                ON t.id = c.trainer_id
        WHERE cr.id = $traineeId";

$trainee = null;
if ($result = $db->query($sql)) {
    if ($result->num_rows > 0) {
        $trainee = $result->fetch_assoc();
        $result->close();
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

$defaultConfig = (new Mpdf\Config\ConfigVariables())->getDefaults();
$fontDirs = $defaultConfig['fontDir'];

$defaultFontConfig = (new Mpdf\Config\FontVariables())->getDefaults();
$fontData = $defaultFontConfig['fontdata'];

$mpdf = new \Mpdf\Mpdf([
    'mode' => 'UTF-8',
    'format' => 'A4',
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
    'default_font_size' => 15,
]);

?>
    <!DOCTYPE html>
    <html lang="th">
    <head>
        <meta charset="utf-8">
        <title>:: หนังสือรับรองการผ่านการอบรม ::</title>

        <style type="text/css">
            .center_div {
                margin: auto;
                width: 650px;
                /*border: solid 1px #000000;*/
                line-height: 155%;
                text-align: justify;
            }

            td {
                font-size: 21px;
                line-height: 140%;
            }
        </style>
    </head>

    <body>
    <?php
    $i = 0;
    $licenseTypeList = array(
        DL_LICENSE_TYPE_CAR, DL_LICENSE_TYPE_MOTOR_CYCLE, DL_LICENSE_TYPE_TRICYCLE
    );

    foreach ($licenseTypeList as $licenseType) {
        if (((int)$trainee['license_type'] & $licenseType) === $licenseType) {
            getPage($licenseType);
        }
    }
    ?>
    </body>
    </html>
<?php
$html = ob_get_contents();
ob_end_clean();

$mpdf->WriteHTML($html);
$mpdf->Output();

function getPage($licenseType)
{
    global $trainee, $i;

    ?>
    <div <?= ($i++ !== 0) ? 'style="page-break-before: always"' : ''; ?>>
        <table width="650px" align="center" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td>
                    <table width="650px" align="center" border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse;">
                        <tr>
                            <td width="220px" valign="top" style="padding-top: 110px">
                                ที่ อว ๖๗.๔๙ / ขสต <?= thaiNumDigit($trainee['course_year']); ?> / <?= thaiNumDigit($trainee['certificate_number']); ?>
                            </td>
                            <td width="210px" align="center" valign="top" style="padding-top: 10px">
                                <img width="120px" src="../images/ic_garuda_emblem.png" border="0"/>
                            </td>
                            <td width="220px" valign="top" style="padding-top: 110px">
                                สถาบันเสริมศึกษาและทรัพยากรมนุษย์<br/>
                                มหาวิทยาลัยธรรมศาสตร์<br/>
                                ถนนพระจันทร์ เขตพระนคร<br/>
                                กรุงเทพฯ ๑๐๒๐๐
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    <table width="650px" align="center" border="0" cellpadding="0" cellspacing="0" style="margin-top: 5px; margin-bottom: 10px">
                        <tr>
                            <td align="center" style="font-size: 30px; padding-top: 10px">หนังสือรับรองการผ่านการอบรม</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <div class="center_div">
            <table width="500px" align="center" border="0" cellpadding="0" cellspacing="0" style="text-align: justify; line-height: 155%; margin-top: 10px;">
                <tr>
                    <td width="80px"></td>
                    <td width="250px">หนังสือรับรองฉบับนี้ ออกให้เพื่อแสดงว่า</td>
                    <td width="320px" align="left" style="font-size: 22px"><?= "{$trainee['title']}{$trainee['first_name']} {$trainee['last_name']}"; ?></td>
                </tr>
                <tr>
                    <?php
                    $address = trim($trainee['address']);
                    $address .= ((trim($trainee['moo']) === '-') || (trim($trainee['moo']) === '') || is_null($trainee['moo'])) ? '' : ' หมู่ ' . trim($trainee['moo']);
                    $address .= ((trim($trainee['soi']) === '-') || (trim($trainee['soi']) === '') || is_null($trainee['soi'])) ? '' : ' ซอย' . trim($trainee['soi']);
                    $address .= ((trim($trainee['road']) === '-') || (trim($trainee['road']) === '') || is_null($trainee['road'])) ? '' : ' ถนน' . trim($trainee['road']);
                    $address .= ((trim($trainee['sub_district']) === '-') || (trim($trainee['sub_district']) === '') || is_null($trainee['sub_district'])) ? '' : ' แขวง/ตำบล' . trim($trainee['sub_district']);
                    $address .= ((trim($trainee['district']) === '-') || (trim($trainee['district']) === '') || is_null($trainee['district'])) ? '' : ' เขต/อำเภอ' . trim($trainee['district']);
                    $address .= ((trim($trainee['province']) === '-') || (trim($trainee['province']) === '') || is_null($trainee['province'])) ? '' : ' จังหวัด' . trim($trainee['province']);

                    ?>
                    <td colspan="3" align="justify">
                        เลขประจำตัวประชาชน <?= thaiNumDigit(formatPid($trainee['pid'], '&nbsp;')); ?>&nbsp;&nbsp;&nbsp;
                        อยู่บ้านเลขที่ <?= thaiNumDigit($address); ?>
                    </td>
                </tr>
            </table>
        </div>

        <div class="center_div" style="margin-top: 15px; font-size: 21px">
            เป็นผู้มีความสามารถ โดยผ่านการอบรมตามหลักสูตรที่กรมการขนส่งทางบกกำหนด ดังนี้
        </div>

        <table width="100%" align="center" border="0" cellpadding="0" cellspacing="0" style="margin-top: 10px;">
            <tr>
                <td style="padding-left: 25px"><img src="../images/driveTrain/<?= ((int)$trainee['course_type'] === 1) ? 'checked.png' : 'unchecked.png'; ?>"/></td>
                <td width="500px">หลักสูตรการอบรมสำหรับผู้ขอรับใบอนุญาตขับรถตามกฎหมายว่าด้วยรถยนต์</td>
                <td>จำนวน</td>
                <td><?= '๕'; ?></td>
                <td> ชั่วโมง</td>
            </tr>
        </table>

        <table width="100%" align="center" border="0" cellpadding="0" cellspacing="0" style=" margin-top: 8px;">
            <tr>
                <td style="padding-left: 25px"><img src="../images/driveTrain/<?= ((int)$trainee['course_type'] === 2) ? 'checked.png' : 'unchecked.png'; ?>"/></td>
                </td>
                <td width="500px">หลักสูตรการอบรมสำหรับผู้ขอต่ออายุใบอนุญาตขับรถตามกฎหมายว่าด้วยรถยนต์</td>
                <td>จำนวน</td>
                <td><?= '๒'; ?></td>
                <td> ชั่วโมง</td>
            </tr>
        </table>

        <table width="100%" align="center" border="0" cellpadding="0" cellspacing="0" style=" margin-top: 8px;">
            <tr>
                <td style="padding-left: 25px"><img src="../images/driveTrain/<?= ((int)$trainee['course_type'] === 3) ? 'checked.png' : 'unchecked.png'; ?>"/></td>
                </td>
                <td width="500px">หลักสูตรการอบรมสำหรับผู้ขอต่ออายุใบอนุญาตขับรถตามกฎหมายว่าด้วยรถยนต์</td>
                <td>จำนวน</td>
                <td><?= '๑'; ?></td>
                <td> ชั่วโมง</td>
            </tr>
        </table>

        <table width="650px" align="center" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td>
                    <table width="650px" align="center" border="0" cellpadding="0" cellspacing="0" style="margin-top: 20px;">
                        <tr>
                            <td>
                                สำหรับประเภทใบอนุญาตขับ&nbsp;<?= $licenseType === DL_LICENSE_TYPE_CAR ? 'รถยนต์ส่วนบุคคล' : ($licenseType === DL_LICENSE_TYPE_MOTOR_CYCLE ? 'รถจักรยานยนต์ส่วนบุคคล' : 'รถสามล้อส่วนบุคคล'); ?><?= (int)$trainee['course_type'] === 1 ? 'ชั่วคราว' : ''; ?>
                            </td>
                        </tr>
                        <tr>
                            <td>เมื่อวันที่ <?= thaiNumDigit(getThaiDate2(date_create($trainee['course_date']))); ?></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <div class="center_div">
            <p style="text-indent: 80px; text-align: justify; font-size: 21px">
                หนังสือรับรองฉบับนี้สามารถใช้เป็นหลักฐานแสดงต่อกรมการขนส่งทางบก เพื่อขอรับใบอนุญาตขับรถ
                หรือขอต่ออายุใบอนุญาตขับรถ แล้วแต่กรณี ได้ภายในกำหนดเวลาไม่เกิน ๖ เดือน
                นับแต่วันที่ออกหนังสือรับรอง
            </p>
        </div>

        <table width="650px" align="center" border="0" cellpadding="0" cellspacing="0" style="margin-top: 10px;">
            <tr>
                <td width="325px"></td>
                <?php $today = date("Y-m-d"); ?>
                <td width="325px" align="center">
                    ให้ไว้ ณ วันที่ <?= thaiNumDigit(getThaiDate2(date_create($today))); ?>
                </td>
            </tr>
            <tr>
                <td></td>
                <td align="center" style="padding-top: 20px; padding-bottom: 10px">
                    <?php
                    if ((int)$trainee['show_trainer_signature'] === 1) {
                        ?>
                        <img height="50px" src="<?= UPLOAD_DIR_SIGNATURES . $trainee['trainer_signature_image']; ?>"/>
                        <?php
                    } else {
                        ?>
                        <img height="50px" src="<?= UPLOAD_DIR_SIGNATURES . 'signature_blank.png'; ?>"/>
                        <?php
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <td></td>
                <td align="center">
                    ( อาจารย์<?= "{$trainee['trainer_first_name']} {$trainee['trainer_last_name']}"; ?> )
                </td>
            </tr>
            <tr>
                <td></td>
                <td align="center">
                    ผู้มีอำนาจลงนามแทนผู้อำนวยการ<br/>
                    สถาบันเสริมศึกษาและทรัพยากรมนุษย์ มธ.
                </td>
            </tr>
        </table>
    </div>
    <?php
}

require_once '../include/foot_php.inc';