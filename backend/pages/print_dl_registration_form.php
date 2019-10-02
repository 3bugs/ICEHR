<?php
ob_start();
require_once '../vendor/autoload.php';
require_once '../include/head_php.inc';

$traineeId = $_GET['trainee_id'];
$payment = $_GET['payment'];
$isThaiDigit = isset($_GET['thai']);

if (!isset($traineeId)) {
    echo 'Error: ไม่ได้ระบุ ID ใบสมัคร';
    $db->close();
    exit();
}
$sql = "SELECT cr.form_number, cr.title, cr.first_name, cr.last_name, cr.pid, cr.address, cr.moo, cr.soi, cr.road, 
               cr.sub_district, cr.district, cr.province, cr.phone,
               cr.course_type, cr.license_type, cr.created_at, c.place, c.responsible_user_id, c.begin_date AS course_date,
               cm.title AS course_title
        FROM course_registration_driving_license cr 
            INNER JOIN course c 
                ON c.id = cr.course_id 
            INNER JOIN course_master cm 
                ON c.course_master_id = cm.id
        WHERE cr.id IN ($traineeId)";

if ($result = $db->query($sql)) {
    if ($result->num_rows > 0) {
        $traineeList = array();

        while ($trainee = $result->fetch_assoc()) {
            $sql = "SELECT * FROM user WHERE id = {$trainee['responsible_user_id']}";

            if ($resultUser = $db->query($sql)) {
                if ($resultUser->num_rows > 0) {
                    $rowUser = $resultUser->fetch_assoc();
                    $resultUser->close();

                    $trainee['responsible_user_first_name'] = $rowUser['first_name'];
                    $trainee['responsible_user_last_name'] = $rowUser['last_name'];
                    $trainee['responsible_user_phone_office'] = $rowUser['phone_office'];
                    $trainee['responsible_user_email'] = $rowUser['email'];
                } else {
                    echo 'Error: ไม่พบข้อมูลผู้รับผิดชอบหลักสูตร';
                    $resultUser->close();
                    $db->close();
                    exit();
                }
            } else {
                echo 'เกิดข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล: ' . $db->error . $sql;
                $db->close();
                exit();
            }

            array_push($traineeList, $trainee);
        }
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

$sql = "SELECT id, title, application_fee FROM driving_license_course_type ORDER BY id";
$courseTypeList = array();
if ($result = $db->query($sql)) {
    while ($row = $result->fetch_assoc()) {
        array_push($courseTypeList, $row);
    }
    $result->close();
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
        <title>:: ใบสมัคร ::</title>

        <style type="text/css">
            .txtDash {
                border-bottom: 2px dotted #000000;
                padding-left: 10px;
                padding-bottom: 0px;
                margin-bottom: 0px;
            }

            td {
                line-height: 140%;
            }
        </style>
    </head>

    <body>
    <?php
    $i = 0;
    $courseDisplayName = '';

    $licenseTypeList = array(
        DL_LICENSE_TYPE_CAR, DL_LICENSE_TYPE_MOTOR_CYCLE, DL_LICENSE_TYPE_TRICYCLE
    );

    foreach ($traineeList as $trainee) {
        if ($i === 0) {
            $courseDisplayName = "หลักสูตร '{$trainee['course_title']}'";
        }

        if (isset($_GET['user'])) { // กรณีส่งให้ user จะรวมทุกประเภท license ในใบสมัครหน้าเดียว
            $selectedLicenseTypeList = array();
            foreach ($licenseTypeList as $licenseType) {
                if (((int)$trainee['license_type'] & $licenseType) === $licenseType) {
                    array_push($selectedLicenseTypeList, $licenseType);
                }
            }
            getPage($trainee, $selectedLicenseTypeList);
        } else { // กรณี print หลังบ้าน จะแยกประเภท license ประเภทละ 1 หน้า
            foreach ($licenseTypeList as $licenseType) {
                if (((int)$trainee['license_type'] & $licenseType) === $licenseType) {
                    getPage($trainee, array($licenseType));
                }
            }
        }

        if (isset($payment)) {
            getPaymentPage($trainee);
        }
    }
    ?>
    </body>
    </html>

<?php
$html = ob_get_contents();
ob_end_clean();

$mpdf->WriteHTML($html);

if (isset($_GET['email'])) { // ส่ง pdf ไปทางอีเมล
    $sql = "SELECT cr.form_number, m.email AS member_email 
                FROM course_registration_driving_license cr 
                    LEFT JOIN member m 
                        ON cr.member_id = m.id 
                WHERE cr.id = $traineeId";
    if ($result = $db->query($sql)) {
        $row = $result->fetch_assoc();
        $pdfFileName = "{$row['form_number']}.pdf";
        $memberEmail = $row['member_email'];
        $result->close();
    } else {
        // Error query
        $db->close();
        exit();
    }

    $recipientList = array();
    if (!is_null($memberEmail)) {
        array_push($recipientList, $memberEmail);
    }

    $pdfFileContent = $mpdf->Output('', 'S');
    sendMail($pdfFileName, $pdfFileContent, $recipientList, $courseDisplayName, FALSE);
    $db->close();
} else { // ส่ง pdf กลับไปยังบราวเซอร์
    $mpdf->Output();
    $db->close();
}

function getPage($trainee, $licenseType)
{
    global $i, $courseTypeList, $isThaiDigit;

    ?>
    <div <?= ($i++ !== 0) ? 'style="page-break-before: always"' : ''; ?>>
        <table width="700px" align="center" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td>
                    <table width="700px" align="center" border="0" cellpadding="0" cellspacing="0">
                        <tr>
                            <td align="center" colspan="2"><img src="../images/logo_icehr.svg" width="380px"/></td>
                        </tr>
                        <tr>
                            <td align="center" colspan="2" height="80px">
                                <div style="font-size: 30px">ใบสมัครเข้ารับการอบรมหลักสูตรด้านใบอนุญาตขับรถภาคทฤษฎี</div>
                            </td>
                        </tr>
                        <tr>
                            <td align="right" width="550px">เลขที่/No</td>
                            <td align="center" width="150px" class="txtDash"><strong><?= thaiNumDigit($trainee['form_number'], $isThaiDigit); ?></strong></td>
                        </tr>
                        <tr>
                            <td align="right" width="550px">วันที่/Date</td>
                            <td align="center" width="150px" class="txtDash">
                                <strong><?= thaiNumDigit(getThaiShortDate(date_create($trainee['created_at'])), $isThaiDigit); ?></strong>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr>
                <td>
                    <table width="700px" align="center" border="0" cellpadding="0" cellspacing="0" style="padding-top: 40px;">
                        <tr>
                            <td width="60px">&nbsp;</td>
                            <td width="95px">ข้าพเจ้า/Name</td>
                            <td width="545px" class="txtDash">
                                <strong><?= "{$trainee['title']}{$trainee['first_name']}&nbsp;{$trainee['last_name']}"; ?></strong>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr>
                <td>
                    <table width="700px" align="center" border="0" cellpadding="0" cellspacing="0" style="padding-top: 5px;">
                        <tr>
                            <td width="170px">เลขประจำตัวประชาชน/ID No</td>
                            <td width="530px" class="txtDash"><strong><?= thaiNumDigit(formatPid($trainee['pid'], '&nbsp;&nbsp;'), $isThaiDigit); ?></strong></td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr>
                <td>
                    <table width="700px" align="center" border="0" cellpadding="0" cellspacing="0" style="padding-top: 5px;">
                        <tr>
                            <td width="240px">ที่อยู่บ้านเลขที่ตามบัตรประชาชน/Address</td>
                            <td width="130px" class="txtDash"><strong><?= thaiNumDigit($trainee['address'], $isThaiDigit); ?></strong></td>
                            <td width="55px">หมู่/Moo</td>
                            <td width="50px" class="txtDash"><strong><?= thaiNumDigit($trainee['moo'], $isThaiDigit); ?></strong></td>
                            <td width="55px">ซอย/Soi</td>
                            <td width="170px" class="txtDash"><strong><?= thaiNumDigit($trainee['soi'], $isThaiDigit); ?></strong></td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr>
                <td>
                    <table width="700px" align="center" border="0" cellpadding="0" cellspacing="0" style="padding-top: 5px;">
                        <tr>
                            <td width="70px">ถนน/Road</td>
                            <td width="240px" class="txtDash"><strong><?= thaiNumDigit($trainee['road'], $isThaiDigit); ?></strong></td>
                            <td width="150px">แขวง/ตำบล/Sub-District</td>
                            <td width="240px" class="txtDash"><strong><?= thaiNumDigit($trainee['sub_district'], $isThaiDigit); ?></strong></td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr>
                <td>
                    <table width="700px" align="center" border="0" cellpadding="0" cellspacing="0" style="padding-top: 5px;">
                        <tr>
                            <td width="115px">เขต/อำเภอ/District</td>
                            <td width="240px" class="txtDash"><strong><?= $trainee['district']; ?></strong></td>
                            <td width="105px">จังหวัด/Province</td>
                            <td width="240px" class="txtDash"><strong><?= $trainee['province']; ?></strong></td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr>
                <td>
                    <table width="700px" align="center" border="0" cellpadding="0" cellspacing="0" style="padding-top: 5px;">
                        <tr>
                            <td width="105px">โทรมือถือ/Phone</td>
                            <td width="595px" class="txtDash"><strong><?= thaiNumDigit($trainee['phone'], $isThaiDigit); ?></strong></td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr>
                <td>
                    <table width="700px" align="center" border="0" cellpadding="0" cellspacing="0" style="margin-top: 25px;">
                        <tr>
                            <td width="60px">&nbsp;</td>
                            <td width="640px" colspan="4">มีความประสงค์ขอสมัครเข้ารับการอบรมภาคทฤษฎีในหลักสูตร ดังนี้</td>
                        </tr>
                        <tr>
                            <td width="60px">&nbsp;</td>
                            <td width="630px">
                                1.&nbsp;&nbsp;<img src="../images/driveTrain/<?= ((int)$trainee['course_type'] === 1) ? 'checked.png' : 'unchecked.png'; ?>">
                                &nbsp;<?= thaiNumDigit(removeLastParenthesis($courseTypeList[0]['title']), $isThaiDigit); ?>
                            </td>
                            <!--<td width="50px">จำนวน</td>
                            <td width="20px" align="center">๕</td>
                            <td width="30px" align="center">ชั่วโมง</td>-->
                        </tr>
                        <tr>
                            <td width="60px">&nbsp;</td>
                            <td width="630px">
                                2.&nbsp;&nbsp;<img src="../images/driveTrain/<?= ((int)$trainee['course_type'] === 2) ? 'checked.png' : 'unchecked.png'; ?>">
                                &nbsp;<?= thaiNumDigit(removeLastParenthesis($courseTypeList[1]['title']), $isThaiDigit); ?>
                            </td>
                            <!--<td width="50px">จำนวน</td>
                            <td width="20px" align="center">๒</td>
                            <td width="30px" align="center">ชั่วโมง</td>-->
                        </tr>
                        <tr>
                            <td width="60px">&nbsp;</td>
                            <td width="630px">
                                3.&nbsp;&nbsp;<img src="../images/driveTrain/<?= ((int)$trainee['course_type'] === 3) ? 'checked.png' : 'unchecked.png'; ?>">
                                &nbsp;<?= thaiNumDigit(removeLastParenthesis($courseTypeList[2]['title']), $isThaiDigit); ?>
                            </td>
                            <!--<td width="50px">จำนวน</td>
                            <td width="20px" align="center">๑</td>
                            <td width="30px" align="center">ชั่วโมง</td>-->
                        </tr>
                    </table>
                </td>
            </tr>

            <tr>
                <td>
                    <table width="700px" align="center" border="0" cellpadding="0" cellspacing="0" style="padding-top: 15px;">
                        <tr>
                            <td width="145px">ประเภทใบอนุญาตขับรถ</td>
                            <td width="165px">
                                <?= (in_array(DL_LICENSE_TYPE_CAR, $licenseType)) ? '<img src="../images/driveTrain/checked.png">' : '<img src="../images/driveTrain/unchecked.png">'; ?>
                                <font size="-1">&nbsp;รถยนต์ส่วนบุคคล<?= (int)$trainee['course_type'] === 1 ? 'ชั่วคราว' : ''; ?></font>
                            </td>
                            <td width="210px">
                                <?= (in_array(DL_LICENSE_TYPE_MOTOR_CYCLE, $licenseType)) ? '<img src="../images/driveTrain/checked.png">' : '<img src="../images/driveTrain/unchecked.png">'; ?>
                                <font size="-1">&nbsp;รถจักรยานยนต์ส่วนบุคคล<?= (int)$trainee['course_type'] === 1 ? 'ชั่วคราว' : ''; ?></font>
                            </td>
                            <td width="180px">
                                <?= (in_array(DL_LICENSE_TYPE_TRICYCLE, $licenseType)) ? '<img src="../images/driveTrain/checked.png">' : '<img src="../images/driveTrain/unchecked.png">'; ?>
                                <font size="-1">&nbsp;รถสามล้อส่วนบุคคล<?= (int)$trainee['course_type'] === 1 ? 'ชั่วคราว' : ''; ?></font>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr>
                <td>
                    <table width="700px" align="center" border="0" cellpadding="0" cellspacing="0" style="padding-top: 15px;">
                        <tr>
                            <td width="120px">วัน/เดือน/ปี ที่อบรม</td>
                            <td width="580px" class="txtDash"><strong><?= thaiNumDigit(getThaiShortDate(date_create($trainee['course_date'])), $isThaiDigit); ?></strong></td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr>
                <td>
                    <table width="700px" align="center" border="0" cellpadding="0" cellspacing="0" style="padding-top: 25px;">
                        <tr>
                            <td width="60px">&nbsp;</td>
                            <td width="640px">ข้าพเจ้าขอรับรองว่า</td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td>1. ข้าพเจ้ามีคุณสมบัติและไม่มีลักษณะต้องห้ามตามกฎหมายว่าด้วยรถยนต์กำหนด ในการขอรับใบอนุญาตขับรถ</td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td>2. ข้าพเจ้าได้ทราบแล้วว่า เมื่อผ่านการอบรมภาคทฤษฎีตามที่สมัครแล้ว จะต้องผ่านการทดสอบสมรรถภาพของร่างกาย</td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td style="padding-left: 15px">ก่อนเข้ารับการทดสอบข้อเขียนภาคทฤษฎีด้วยระบบอิเล็กทรอนิกส์</td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr>
                <td>
                    <table width="700px" align="center" border="0" cellpadding="0" cellspacing="0" style="padding-top: 50px;">
                        <tr>
                            <td width="350px">&nbsp;</td>
                            <td width="350px" align="center">(ลงชื่อ) ..................................................................... ผู้สมัคร</td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td align="center">( <?= "{$trainee['title']}{$trainee['first_name']}&nbsp;{$trainee['last_name']}"; ?> )</td>
                        </tr>
                    </table>
                    <table width="700px" align="center" border="0" cellpadding="0" cellspacing="0" style="padding-top: 15px;">
                        <tr>
                            <td width="350px">&nbsp;</td>
                            <td width="350px" align="center">วันที่ ........... เดือน .................................... พ.ศ. ..................</td>
                        </tr>
                    </table>
                </td>
            </tr>

        </table>
    </div>
    <?php
}

function getPaymentPage($trainee)
{
    global $courseTypeList, $isThaiDigit;

    ?>
    <div style="page-break-before: always">
        <table width="650px" align="center" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td>
                    <table width="650px" align="center" border="0" cellpadding="0" cellspacing="0">
                        <tr>
                            <td align="center" colspan="2"><img src="../images/logo_icehr.svg" width="380px"/></td>
                        </tr>
                        <tr>
                            <td align="center" colspan="2" height="80px">
                                <div style="font-size: 30px">อัตราค่าสมัครและวิธีการชำระเงิน</div>
                            </td>
                        </tr>
                        <tr>
                            <td align="right" width="480px" style="padding-right: 10px">เลขที่ใบสมัคร</td>
                            <td align="center" width="170px" class="txtDash" style="padding-left: 0">
                                <strong><?= thaiNumDigit($trainee['form_number'], $isThaiDigit); ?></strong>
                            </td>
                        </tr>
                        <tr>
                            <td align="right" width="480px" style="padding-right: 10px">วันที่</td>
                            <td align="center" width="170px" class="txtDash" style="padding-left: 0">
                                <strong><?= thaiNumDigit(getThaiShortDate(date_create($trainee['created_at'])), $isThaiDigit); ?></strong>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr>
                <td>
                    <table width="650px" align="center" border="0" cellpadding="0" cellspacing="0" style="padding-top: 40px;">
                        <tr>
                            <td width="120px">ชื่อหลักสูตรที่สมัคร</td>
                            <td width="530px" class="txtDash">
                                <strong><?= $trainee['course_title']; ?></strong>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr>
                <td>
                    <table width="650px" align="center" border="0" cellpadding="0" cellspacing="0" style="padding-top: 5px;">
                        <tr>
                            <td width="140px">ประเภทหลักสูตรที่สมัคร</td>
                            <td width="510px" class="txtDash">
                                <strong><?= thaiNumDigit(removeLastParenthesis($courseTypeList[(int)$trainee['course_type'] - 1]['title']), $isThaiDigit); ?></strong>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr>
                <td>
                    <table width="650px" align="center" border="0" cellpadding="0" cellspacing="0" style="padding-top: 5px;">
                        <tr>
                            <td width="120px">วัน/เดือน/ปี ที่อบรม</td>
                            <td width="530px" class="txtDash">
                                <strong><?= thaiNumDigit(getThaiShortDate(date_create($trainee['course_date'])), $isThaiDigit); ?></strong>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr>
                <td>
                    <table width="650px" align="center" border="0" cellpadding="0" cellspacing="0" style="padding-top: 5px;">
                        <tr>
                            <td width="85px">สถานที่อบรม</td>
                            <td width="565px" class="txtDash">
                                <strong><?= thaiNumDigit($trainee['place'], $isThaiDigit); ?></strong>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr>
                <td>
                    <table width="650px" align="center" border="0" cellpadding="0" cellspacing="0" style="padding-top: 5px;">
                        <tr>
                            <td width="180px">เจ้าหน้าที่ผู้รับผิดชอบหลักสูตร</td>
                            <td width="470px" class="txtDash">
                                <strong><?= thaiNumDigit("{$trainee['responsible_user_first_name']} {$trainee['responsible_user_last_name']}, โทร. {$trainee['responsible_user_phone_office']}, อีเมล {$trainee['responsible_user_email']}", $isThaiDigit); ?></strong>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr>
                <td>
                    <table width="650px" align="center" border="0" cellpadding="0" cellspacing="0" style="padding-top: 20px; padding-bottom: 5px;">
                        <tr>
                            <td width="650px"><strong>อัตราค่าสมัคร</strong></td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr>
                <td>
                    <table width="580px" align="center" border="1" cellpadding="5px" cellspacing="0">
                        <?php
                        foreach ($courseTypeList as $courseType) {
                            ?>
                            <tr>
                                <td width="480px" <?= $courseType['amount'] == null ? 'colspan="2"' : ''; ?>
                                    style="padding-left: 15px">
                                    <?= thaiNumDigit(removeLastParenthesis($courseType['title']), $isThaiDigit); ?>
                                </td>
                                <td width="100px" align="right"
                                    style="padding-right: 15px">
                                    <strong><?= thaiNumDigit(number_format($courseType['application_fee']), $isThaiDigit) . ' บาท'; ?></strong>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                    </table>
                </td>
            </tr>

            <tr>
                <td>
                    <table width="650px" align="center" border="0" cellpadding="0" cellspacing="0" style="padding-top: 20px; padding-bottom: 5px;">
                        <tr>
                            <td width="650px"><strong>วิธีการชำระเงิน</strong></td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr>
                <td>
                    <table width="650px" align="center" border="0" cellpadding="2px" cellspacing="0">
                        <tr>
                            <td width="80px">&nbsp;</td>
                            <td width="570px">ชำระเงินโดยโอนเงินเข้า <strong>ธนาคารไทยพาณิชย์</strong> สาขาย่อยท่าพระจันทร์ บัญชีเงินฝากออมทรัพย์</td>
                        </tr>
                        <tr>
                            <td width="80px">&nbsp;</td>
                            <td width="570px">
                                <ul>
                                    <li>&nbsp;ชื่อบัญชี <strong>สถาบันเสริมศึกษาและทรัพยากรมนุษย์ มหาวิทยาลัยธรรมศาสตร์</strong></li>
                                </ul>
                            </td>
                        </tr>
                        <tr>
                            <td width="80px">&nbsp;</td>
                            <td width="570px">
                                <ul>
                                    <li>&nbsp;เลขที่บัญชี <strong><?= thaiNumDigit('114-220817-0', $isThaiDigit); ?></strong></li>
                                </ul>
                            </td>
                        </tr>
                        <tr>
                            <td width="80px">&nbsp;</td>
                            <td width="570px" style="padding-top: 10px">
                                <?= thaiNumDigit('และแจ้งโอนเงินผ่านเว็บไซต์ www.icehr.ac.th หรือส่งสำเนาหลักฐานการโอนเงินมาพร้อมกับใบสมัครนี้ทาง โทรสาร 02-225-7517 โทร. 02-613-3820-5', $isThaiDigit); ?>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

        </table>
    </div>
    <?php
}

function removeLastParenthesis($text) {
    global $isThaiDigit;

    if ($isThaiDigit) {
        $openingPosition = strrpos($text, '(');
        //$closingPosition = strrpos($text, ')');
        if ($openingPosition !== false) {
            return trim(substr($text, 0, $openingPosition));
        } else {
            return $text;
        }
    } else {
        return $text;
    }
}

require_once '../include/foot_php.inc';