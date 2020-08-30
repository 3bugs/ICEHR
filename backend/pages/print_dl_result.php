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
               cr.subject_1_result, cr.subject_2_result, cr.subject_3_result, cr.subject_4_result, c.begin_date AS course_date, 
               t.title AS trainer_title, t.first_name AS trainer_first_name, t.last_name AS trainer_last_name, t.pid AS trainer_pid, 
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
        <title>:: บันทึกผลการอบรมรายบุคคล ::</title>

        <style type="text/css">
            .txtDash {
                border-bottom: 2px dotted #000000;
                padding-left: 10px;
                padding-bottom: 0px;
                margin-bottom: 0px;
            }

            td {
                line-height: 150%;
            }

            #tableSubjectList td {
                line-height: 130%;
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
                <td align="center" style="font-size: 30px; height: 90px;"><strong>บันทึกผลการอบรมรายบุคคล</strong></td>
            </tr>

            <tr>
                <td>
                    <table width="650px" align="center" border="0" cellpadding="0" cellspacing="0">
                        <tr>
                            <td width="200px" align="right" style="padding-right: 10px">ชื่อสถาบันการศึกษา</td>
                            <td width="450px" class="txtDash" style="padding-left: 10px">สถาบันเสริมศึกษาและทรัพยากรมนุษย์ มหาวิทยาลัยธรรมศาสตร์</td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr>
                <td>
                    <table width="650px" align="center" border="0" cellpadding="0" cellspacing="0" style="padding-top:5px;">
                        <?php
                        /*$separator = '&nbsp;';
                        $formatPid = substr_replace($trainee['pid'], $separator, 1, 0);
                        $formatPid = substr_replace($formatPid, $separator, 5 + strlen($separator), 0);
                        $formatPid = substr_replace($formatPid, $separator, 10 + 2 * strlen($separator), 0);
                        $formatPid = substr_replace($formatPid, $separator, 12 + 3 * strlen($separator), 0);*/
                        ?>
                        <tr>
                            <td width="120px">ชื่อผู้เข้ารับการอบรม</td>
                            <td width="210px" class="txtDash"><?= "{$trainee['title']}{$trainee['first_name']} {$trainee['last_name']}"; ?></td>
                            <td width="168px" align="right" style="padding-right: 5px">เลขที่บัตรประจำตัวประชาชน</td>
                            <td class="txtDash" width="152px"><?= thaiNumDigit(formatPid($trainee['pid'], '&nbsp;')); ?></td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr>
                <td>
                    <table width="650px" align="center" border="0" cellpadding="0" cellspacing="0" style="padding-top:5px;">
                        <tr>
                            <td width="60px">หลักสูตร</td>
                            <td width="590px" class="txtDash">
                                <?= (int)$trainee['course_type'] === 1 ? '"การอบรมสำหรับผู้ขอรับใบอนุญาตขับรถ"' : '"การอบรมสำหรับผู้ขอต่ออายุใบอนุญาตขับรถ"' ?>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr>
                <td>
                    <table width="650px" align="center" border="0" cellpadding="0" cellspacing="0" style="padding-top:5px;">
                        <tr>
                            <?php
                            //$licenseType = ((int)$trainee['license_type'] & 1) === 1 ? ('รถยนต์ส่วนบุคคล' . ((int)$trainee['course_type'] === 1 ? 'ชั่วคราว' : '')) : '';
                            //$licenseType .= ((int)$trainee['license_type'] & 2) === 2 ? ('รถจักรยานยนต์ส่วนบุคคล' . ((int)$trainee['course_type'] === 1 ? 'ชั่วคราว' : '')) : '';
                            //$licenseType .= ((int)$trainee['license_type'] & 4) === 4 ? ('รถสามล้อส่วนบุคคล' . ((int)$trainee['course_type'] === 1 ? 'ชั่วคราว' : '')) : '';
                            ?>
                            <td width="145px">สำหรับประเภทใบอนุญาต</td>
                            <td width="505px" class="txtDash">
                                <?= $licenseType === DL_LICENSE_TYPE_CAR ? 'รถยนต์ส่วนบุคคล' : ($licenseType === DL_LICENSE_TYPE_MOTOR_CYCLE ? 'รถจักรยานยนต์ส่วนบุคคล' : 'รถสามล้อส่วนบุคคล'); ?><?= (int)$trainee['course_type'] === 1 ? 'ชั่วคราว' : ''; ?>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr>
                <td>
                    <table width="650px" align="center" border="0" cellpadding="0" cellspacing="0" style="padding-top:5px;">
                        <tr>
                            <td width="63px">วันที่อบรม</td>
                            <td width="587px" class="txtDash">
                                <?= thaiNumDigit(getThaiDate2(date_create($trainee['course_date']))); ?>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <table id="tableSubjectList" width="700px" align="center" border="1" cellpadding="5px" cellspacing="0"
               style="padding-top: 5px; margin-top: 40px; border-collapse: collapse; font-size: 13pt">
            <tr>
                <td rowspan="2" align="center" width="75px">วันเ/เดือน/ปี<br/>ที่อบรม</td>
                <td colspan="2" align="center" width="120px">เวลาที่เข้าอบรม</td>
                <td rowspan="2" align="center" width="155px">วิชา</td>
                <td rowspan="2" align="center" width="60px">จำนวน<br/>ชั่วโมงที่<br/>กำหนด</td>
                <td colspan="2" align="center" width="60px">ผลการอบรม*</td>
                <td rowspan="2" align="center" width="90px">ลายมือชื่อ<br/>ผู้เข้ารับ<br/>การอบรม</td>
                <td rowspan="2" align="center" width="140px">ชื่อและเลขที่บัตร<br/>ประจำตัวประชาชนของ<br/>วิทยากร</td>
            </tr>
            <tr>
                <td align="center" width="60px">เริ่มเวลา</td>
                <td align="center" width="60px">ถึงเวลา</td>
                <td align="center" width="30px">ผ่าน</td>
                <td align="center" width="30px">ไม่ผ่าน</td>
            </tr>
            <?php
            $subjectList = array();
            switch ($trainee['course_type']) {
                case 1:
                    array_push($subjectList,
                        array(
                            'name' => 'ความรู้เกี่ยวกับกฎหมายที่เกี่ยวข้อง ได้แก่ กฎหมายว่าด้วยรถยนต์ กฎหมายว่าด้วยการจราจรทางบก และกฎหมายว่าด้วยทางหลวง',
                            'begin_time' => '08:30',
                            'end_time' => '10:00',
                            'duration' => '1.30'
                        ),
                        array(
                            'name' => 'การขับรถเชิงป้องกันอุบัติเหตุ',
                            'begin_time' => '10:00',
                            'end_time' => '12:00',
                            'duration' => '2'
                        ),
                        array(
                            'name' => 'จิตสำนึกและมารยาทในการขับรถ',
                            'begin_time' => '12:00',
                            'end_time' => '13:00',
                            'duration' => '1'
                        ),
                        array(
                            'name' => 'ข้อปฏิบัติเมื่อเกิดเหตุฉุกเฉิน การให้ความช่วยเหลือและปฐมพยาบาล',
                            'begin_time' => '13:00',
                            'end_time' => '13:30',
                            'duration' => '0.30'
                        )
                    );
                    break;
                case 2:
                    array_push($subjectList,
                        array(
                            'name' => "- การขับรถอย่างปลอดภัย\n- มารยาทในการขับรถ",
                            'begin_time' => '9:00',
                            'end_time' => '11:00',
                            'duration' => '2'
                        )
                    );
                    break;
                case 3:
                    array_push($subjectList,
                        array(
                            'name' => "- การขับรถอย่างปลอดภัย\n- มารยาทในการขับรถ",
                            'begin_time' => '10:00',
                            'end_time' => '11:00',
                            'duration' => '1'
                        )
                    );
                    break;
            }

            $i = 0;
            foreach ($subjectList as $subject) {
                $i++;
                ?>
                <tr>
                    <td align="center" style="height:40px;"><?= thaiNumDigit(getThaiShortDate(date_create($trainee['begin_date']))); ?></td>
                    <td align="center"><?= thaiNumDigit($subject['begin_time']); ?> น.</td>
                    <td align="center"><?= thaiNumDigit($subject['end_time']); ?> น.</td>
                    <td><?= nl2br($subject['name']); ?></td>
                    <td align="center"><?= thaiNumDigit($subject['duration']) ?></td>
                    <td align="center">
                        <?php
                        if ((int)$trainee["subject_{$i}_result"] === 1) {
                            ?>
                            <img height="15px" src="../images/driveTrain/correct.png"/>
                            <?php
                        } else {
                            ?>
                            ---
                            <?php
                        }
                        ?>
                    </td>
                    <td align="center">
                        <?php
                        if ($trainee["subject_{$i}_result"] != null && (int)$trainee["subject_{$i}_result"] === 0) {
                            ?>
                            <img height="15px" src="../images/driveTrain/correct.png"/>
                            <?php
                        } else {
                            ?>
                            ---
                            <?php
                        }
                        ?>
                    </td>
                    <td align="center"></td>
                    <td>
                        <?= "{$trainee['trainer_title']}{$trainee['trainer_first_name']} {$trainee['trainer_last_name']}"; ?><br/>
                        <?= thaiNumDigit(formatPid($trainee['trainer_pid'], '&nbsp;')); ?>
                    </td>
                </tr>
                <?php
            }
            ?>
        </table>

        <table width="700px" align="center" border="0" cellpadding="0" cellspacing="0" style="margin-top: 10px;">
            <tr>
                <td width="350px"></td>
                <td width="300px" align="center" style="padding-top: 20px; padding-bottom: 10px">
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
                    ผู้มีอำนาจลงนามแทนผู้อำนวยการ<br/>สถาบันเสริมศึกษาและทรัพยากรมนุษย์ มธ.
                </td>
            </tr>
        </table>

        <table width="650px" align="center" border="0" cellpadding="0" cellspacing="0" style="margin-top: 50px;">
            <tr>
                <td><u>หมายเหตุ</u></td>
                <td>*ผลการประเมิน</td>
                <td>ผ่าน</td>
                <td>หมายถึง</td>
                <td>ผู้เข้าอบรมเข้ารับการอบรมครบถ้วนตามระยะเวลาที่กำหนด</td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td>ไม่ผ่าน</td>
                <td>หมายถึง</td>
                <td>ผู้เข้าอบรมเข้ารับการอบรมไม่ครบถ้วนตามระยะเวลาที่กำหนด</td>
            </tr>
        </table>
    </div>
    <?php
}

require_once '../include/foot_php.inc';
