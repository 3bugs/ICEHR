<?php
ob_start();
require_once '../vendor/autoload.php';
require_once '../include/head_php.inc';

//กรณีเรียกมาจาก frontend ตอนสมัครวิชาการสำเร็จ
$courseRegId = $_GET['ac_course_reg_id'];
if (isset($courseRegId)) {
    //$pdfFileName = "ac-{$courseRegId}.pdf";
    $serviceType = SERVICE_TYPE_TRAINING;
    $payment = 1;

    $sql = "SELECT id FROM course_trainee WHERE course_registration_id = $courseRegId";
    if ($result = $db->query($sql)) {
        if ($result->num_rows > 0) {
            $traineeId = '';
            while ($row = $result->fetch_assoc()) {
                $traineeId .= "{$row['id']},";
            }
            $traineeId = substr($traineeId, 0, -1); //ลบ comma ตัวสุดท้าย
            $result->close();
        } else {
            echo 'Error: ไม่พบข้อมูลใบสมัคร';
            $result->close();
            $db->close();
            exit();
        }
    } else {
        echo 'Error: เกิดข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล';
        $db->close();
        exit();
    }
} else { // กรณีเรียกจากหลังบ้าน รวมทั้ง frontend ตอนสมัครสังคมสำเร็จ
    $serviceType = $_GET['service_type'];
    $traineeId = $_GET['trainee_id'];
    $payment = $_GET['payment'];

    if (!in_array($serviceType, array(SERVICE_TYPE_TRAINING, SERVICE_TYPE_SOCIAL))) {
        echo 'Error: ระบุประเภทบริการไม่ถูกต้อง';
        $db->close();
        exit();
    }
    if (!isset($traineeId)) {
        echo 'Error: ไม่ได้ระบุ ID ใบสมัคร';
        $db->close();
        exit();
    }

    /*if ($serviceType === SERVICE_TYPE_SOCIAL) {
        $pdfFileName = "so-{$traineeId}.pdf";
    }*/
}

if ($serviceType === SERVICE_TYPE_TRAINING) {
    $sql = "SELECT ct.form_number, ct.title, ct.first_name, ct.last_name, ct.phone, ct.email,
               cr.coordinator_title, cr.coordinator_first_name, cr.coordinator_last_name, cr.coordinator_phone, cr.coordinator_email,
               cm.title AS course_title, c.batch_number AS course_batch_number, c.id AS course_id, c.begin_date, c.end_date, c.place, c.responsible_user_id,
               c.application_fee
            FROM course_trainee ct 
                INNER JOIN course_registration cr 
                    ON cr.id = ct.course_registration_id 
                INNER JOIN course c 
                    ON c.id = cr.course_id 
                INNER JOIN course_master cm 
                    ON cm.id = c.course_master_id 
            WHERE ct.id IN ($traineeId) AND ct.register_status <> 'cancel'";
} else if ($serviceType === SERVICE_TYPE_SOCIAL) {
    $sql = "SELECT cr.form_number, cr.title, cr.first_name, cr.last_name, cr.phone, cr.email,
               cr.contact_name, cr.contact_phone, 
               cm.title AS course_title, c.batch_number AS course_batch_number, c.id AS course_id, c.begin_date, c.end_date, c.place, c.responsible_user_id,
               c.application_fee
            FROM course_registration_social cr  
                INNER JOIN course c 
                    ON c.id = cr.course_id 
                INNER JOIN course_master cm 
                    ON cm.id = c.course_master_id 
            WHERE cr.id IN ($traineeId) AND cr.register_status <> 'cancel'";
}

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

            $sql = "SELECT title, amount FROM course_fee WHERE course_id = {$trainee['course_id']}";

            if ($resultFee = $db->query($sql)) {
                $trainee['fee_list'] = array();

                while ($rowFee = $resultFee->fetch_assoc()) {
                    array_push($trainee['fee_list'], $rowFee);
                }

                $resultFee->close();
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

/*ข้อมูลการชำระเงิน*/
$paymentInfo = null;
$sql = "SELECT details FROM intro WHERE type = 'payment'";
if ($result = $db->query($sql)) {
    if ($result->num_rows > 0) {
        $paymentInfo = $result->fetch_assoc();
    } else {
        $paymentInfo = 'ไม่มีรายละเอียดวิธีการชำระเงิน';
    }
    $result->close();
} else {
    $paymentInfo = 'Error: เกิดข้อผิดพลาดในการอ่านข้อมูลรายละเอียดวิธีการชำระเงิน';
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

            p {
                margin: 0 0 0 70px;
                border: 0px solid black;
            }

            ul, ol {
                margin-left: 70px;
                margin-top: 5px;
                margin-bottom: 10px;
                border: 0px solid black;
            }
        </style>
    </head>
    <body>

    <?php
    $i = 0;
    $isCourseFree = FALSE;
    $courseDisplayName = '';

    foreach ($traineeList as $trainee) {
        if ($i === 0 && ((int)$trainee['application_fee'] === 0)) {
            $isCourseFree = TRUE;
        }
        if ($i === 0) {
            $courseDisplayName = "หลักสูตร '{$trainee['course_title']}' รุ่นที่ {$trainee['course_batch_number']}";
        }
        ?>
        <div <?= ($i++ !== 0) ? 'style="page-break-before: always"' : ''; ?>>
            <table width="650px" align="center" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td>
                        <table width="650px" align="center" border="0" cellpadding="0" cellspacing="0">
                            <tr>
                                <td align="center" colspan="2"><img src="../images/logo_icehr.svg" width="380px"/></td>
                            </tr>
                            <tr>
                                <td align="center" colspan="2" height="80px">
                                    <div style="font-size: 30px">ใบสมัครเข้ารับการอบรม โครงการบริการ<?= $serviceType === SERVICE_TYPE_TRAINING ? 'วิชาการ' : 'สังคม'; ?></div>
                                </td>
                            </tr>
                            <tr>
                                <td align="right" width="480px" style="padding-right: 10px">เลขที่ใบสมัคร</td>
                                <td align="center" width="170px" class="txtDash" style="padding-left: 0"><strong><?= $trainee['form_number']; ?></strong></td>
                            </tr>
                            <tr>
                                <td align="right" width="480px" style="padding-right: 10px">วันที่</td>
                                <td align="center" width="170px" class="txtDash" style="padding-left: 0">
                                    <strong><?= getThaiShortDate(date_create($trainee['created_at'])); ?></strong>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td>
                        <table width="650px" align="center" border="0" cellpadding="0" cellspacing="0" style="padding-top: 40px;">
                            <tr>
                                <td width="80px">&nbsp;</td>
                                <td width="50px">ข้าพเจ้า</td>
                                <td width="520px" class="txtDash">
                                    <strong><?= "{$trainee['title']}{$trainee['first_name']}&nbsp;{$trainee['last_name']}"; ?></strong>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td>
                        <table width="650px" align="center" border="0" cellpadding="0" cellspacing="0" style="padding-top: 5px;">
                            <tr>
                                <td width="60px">โทรมือถือ</td>
                                <td width="265px" class="txtDash"><strong><?= $trainee['phone']; ?></strong></td>
                                <td width="55px" align="right" style="padding-right: 8px">อีเมล</td>
                                <td width="270px" class="txtDash">
                                    <strong>
                                        <?= (is_null($trainee['email']) || $trainee['email'] === '') ? '-' : $trainee['email']; ?>
                                    </strong>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td>
                        <table width="650px" align="center" border="0" cellpadding="0" cellspacing="0" style="margin-top: 15px;">
                            <tr>
                                <td width="80px">&nbsp;</td>
                                <td width="350px">มีความประสงค์ขอสมัครเข้ารับการอบรมในหลักสูตร</td>
                                <td width="220px">&nbsp;</td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td>
                        <table width="650px" align="center" border="0" cellpadding="0" cellspacing="0" style="margin-top: 10px;">
                            <tr>
                                <td width="650px" align="center" class="txtDash">
                                    <strong><?= "{$trainee['course_title']} รุ่นที่ {$trainee['course_batch_number']}"; ?></strong>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td>
                        <table width="650px" align="center" border="0" cellpadding="0" cellspacing="0" style="padding-top: 15px;">
                            <tr>
                                <td width="110px">วัน/เดือน/ปี ที่อบรม</td>
                                <td width="540px" class="txtDash">
                                    <strong><?= getThaiIntervalShortDate2(date_create($trainee['begin_date']), date_create($trainee['end_date'])); ?></strong>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td>
                        <table width="650px" align="center" border="0" cellpadding="0" cellspacing="0" style="padding-top: 5px;">
                            <tr>
                                <td width="70px">สถานที่อบรม</td>
                                <td width="580px" class="txtDash">
                                    <strong><?= $trainee['place']; ?></strong>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td>
                        <table width="650px" align="center" border="0" cellpadding="0" cellspacing="0" style="padding-top: 5px;">
                            <tr>
                                <td width="170px">เจ้าหน้าที่ผู้รับผิดชอบหลักสูตร</td>
                                <td width="480px" class="txtDash">
                                    <strong><?= "{$trainee['responsible_user_first_name']} {$trainee['responsible_user_last_name']}, โทร. {$trainee['responsible_user_phone_office']}, อีเมล {$trainee['responsible_user_email']}"; ?></strong>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td>
                        <table width="650px" align="center" border="0" cellpadding="0" cellspacing="0" style="padding-top: 70px;">
                            <tr>
                                <td width="300px">&nbsp;</td>
                                <td width="350px" align="center">(ลงชื่อ) ..................................................................... ผู้สมัคร</td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                                <td align="center">( <?= "{$trainee['title']}{$trainee['first_name']}&nbsp;{$trainee['last_name']}"; ?> )</td>
                            </tr>
                        </table>
                        <table width="650px" align="center" border="0" cellpadding="0" cellspacing="0" style="padding-top: 15px;">
                            <tr>
                                <td width="300px">&nbsp;</td>
                                <td width="350px" align="center">วันที่ ........... เดือน .................................... พ.ศ. ..................</td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <?php
                if (($serviceType === SERVICE_TYPE_TRAINING && $trainee['coordinator_first_name'] != null)
                    || $serviceType === SERVICE_TYPE_SOCIAL) {
                    ?>
                    <tr>
                        <td>
                            <table width="650px" align="center" border="0" cellpadding="0" cellspacing="0" style="padding-top: 60px;">
                                <tr>
                                    <td width="80px">&nbsp;</td>
                                    <?php
                                    if ($serviceType === SERVICE_TYPE_TRAINING) {
                                        ?>
                                        <td width="85px">ผู้ประสานงาน</td>
                                        <td width="485px" class="txtDash">
                                            <strong><?= "{$trainee['coordinator_title']}{$trainee['coordinator_first_name']}&nbsp;{$trainee['coordinator_last_name']}"; ?></strong>
                                        </td>
                                        <?php
                                    } else {
                                        ?>
                                        <td width="115px">ผู้ติดต่อกรณีฉุกเฉิน</td>
                                        <td width="455px" class="txtDash">
                                            <strong><?= "{$trainee['contact_name']}"; ?></strong>
                                        </td>
                                        <?php
                                    }
                                    ?>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <table width="650px" align="center" border="0" cellpadding="0" cellspacing="0" style="padding-top: 5px;">
                                <tr>
                                    <?php
                                    if ($serviceType === SERVICE_TYPE_TRAINING) {
                                        ?>
                                        <td width="60px">โทรมือถือ</td>
                                        <td width="265px" class="txtDash"><strong><?= $trainee['coordinator_phone']; ?></strong></td>
                                        <td width="55px" align="right" style="padding-right: 8px">อีเมล</td>
                                        <td width="270px" class="txtDash"><strong><?= $trainee['coordinator_email']; ?></strong></td>
                                        <?php
                                    } else {
                                        ?>
                                        <td width="90px">เบอร์โทรติดต่อ</td>
                                        <td width="560px" class="txtDash"><strong><?= $trainee['contact_phone']; ?></strong></td>
                                        <?php
                                    }
                                    ?>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </table>
        </div>

        <?php
        if (isset($payment) && ((int)$trainee['application_fee'] !== 0)) {
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
                                        <strong><?= $trainee['form_number']; ?></strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="right" width="480px" style="padding-right: 10px">วันที่</td>
                                    <td align="center" width="170px" class="txtDash" style="padding-left: 0">
                                        <strong><?= getThaiShortDate(date_create($trainee['created_at'])); ?></strong>
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
                                        <strong><?= "{$trainee['course_title']} รุ่นที่ {$trainee['course_batch_number']}"; ?></strong>
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
                                        <strong><?= getThaiIntervalShortDate2(date_create($trainee['begin_date']), date_create($trainee['end_date'])); ?></strong>
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
                                        <strong><?= $trainee['place']; ?></strong>
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
                                        <strong><?= "{$trainee['responsible_user_first_name']} {$trainee['responsible_user_last_name']}, โทร. {$trainee['responsible_user_phone_office']}, อีเมล {$trainee['responsible_user_email']}"; ?></strong>
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
                            <table width="400px" align="center" border="1" cellpadding="5px" cellspacing="0">
                                <?php
                                foreach ($trainee['fee_list'] as $feeItem) {
                                    ?>
                                    <tr>
                                        <td width="280px" <?= $feeItem['amount'] == null ? 'colspan="2"' : ''; ?>
                                            style="padding-left: 15px">
                                            <?= $feeItem['title']; ?>
                                        </td>
                                        <?php
                                        if ($feeItem['amount'] != null) {
                                            ?>
                                            <td width="120px" align="right"
                                                style="padding-right: 15px">
                                                <strong><?= number_format($feeItem['amount']) . ' บาท'; ?></strong>
                                            </td>
                                            <?php
                                        }
                                        ?>
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

                    <!--<tr>
                        <td>
                            <table width="650px" align="center" border="0" cellpadding="2px" cellspacing="0">-->
                                <!--<tr>
                                    <td width="50px">&nbsp;</td>
                                    <td width="600px">
                                        <?/*= str_replace('<li>', '<li>&nbsp;&nbsp;', $paymentInfo['details']); */?>
                                    </td>
                                </tr>-->
                                <!--<tr>
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
                                            <li>&nbsp;เลขที่บัญชี <strong>114-220817-0</strong></li>
                                        </ul>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="80px">&nbsp;</td>
                                    <td width="570px" style="padding-top: 10px">
                                        และแจ้งโอนเงินผ่านเว็บไซต์ www.icehr.ac.th หรือส่งสำเนาหลักฐานการโอนเงินมาพร้อมกับใบสมัครนี้ทาง โทรสาร 02-225-7517 โทร. 02-613-3820-5
                                    </td>
                                </tr>-->
                            <!--</table>
                        </td>
                    </tr>-->
                </table>

                <?= $paymentInfo['details']; ?>
            </div>
            <?php
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
    if (isset($courseRegId)) { // ส่งเมลใบสมัคร บริการวิชาการ
        $sql = "SELECT cr.form_number, cr.coordinator_email, ct.email AS trainee_email, m.email AS member_email
                FROM course_registration cr 
                    INNER JOIN course_trainee ct 
                        ON ct.course_registration_id = cr.id 
                    LEFT JOIN member m 
                        ON cr.member_id = m.id 
                WHERE cr.id = $courseRegId";
        if ($result = $db->query($sql)) {
            $row = $result->fetch_assoc();
            $pdfFileName = "{$row['form_number']}.pdf";
            $coordinatorEmail = $row['coordinator_email'];
            $traineeEmail = $row['trainee_email'];
            $memberEmail = $row['member_email'];
            $result->close();
        } else {
            // Error query
            $db->close();
            exit();
        }
    } else { // ส่งเมลใบสมัคร บริการสังคม
        $sql = "SELECT cr.form_number, cr.email AS trainee_email, m.email AS member_email 
                FROM course_registration_social cr 
                    LEFT JOIN member m 
                        ON cr.member_id = m.id 
                WHERE cr.id = $traineeId";
        if ($result = $db->query($sql)) {
            $row = $result->fetch_assoc();
            $pdfFileName = "{$row['form_number']}.pdf";
            $coordinatorEmail = NULL;
            $traineeEmail = $row['trainee_email'];
            $memberEmail = $row['member_email'];
            $result->close();
        } else {
            // Error query
            $db->close();
            exit();
        }
    }

    $recipientList = array();
    if (!is_null($coordinatorEmail)) {
        array_push($recipientList, $coordinatorEmail);
    } else if (!is_null($traineeEmail)) {
        array_push($recipientList, $traineeEmail);
    }
    if (!is_null($memberEmail) && !in_array($memberEmail, $recipientList)) {
        array_push($recipientList, $memberEmail);
    }

    $pdfFileContent = $mpdf->Output('', 'S');
    sendMail($pdfFileName, $pdfFileContent, $recipientList, $courseDisplayName, $isCourseFree);
    $db->close();
} else { // ส่ง pdf กลับไปยังบราวเซอร์
    $mpdf->Output();
    $db->close();
}

require_once '../include/foot_php.inc';

//http://localhost/icehr_backend/pages/print_ac_registration_form.php?ac_course_reg_id=92&payment=1&user=1&email=1