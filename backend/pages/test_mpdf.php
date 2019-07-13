<?php
ob_start();
require_once '../vendor/autoload.php';

$defaultConfig = (new Mpdf\Config\ConfigVariables())->getDefaults();
$fontDirs = $defaultConfig['fontDir'];

$defaultFontConfig = (new Mpdf\Config\FontVariables())->getDefaults();
$fontData = $defaultFontConfig['fontdata'];

// Create an instance of the class:
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

            /*body {
                font-family:'niramit';
                font-size:15px;
            }
            .txtDash {
                border-bottom: 2px  dotted #000000 ;
                padding-left: 30px;
                padding-bottom: 0px;
                margin-bottom: 0px;
            }
            .txtTH {
                font-size:15px;
            }
            .txtEN {
                font-size:22px;
            }
            .txtNum {
                font-size:20px;
            }*/
        </style>
    </head>

    <body>
    <table width="700px" align="center" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td>
                <table width="700px" align="center" border="0" cellpadding="0" cellspacing="0">
                    <tr>
                        <td align="center" colspan="2"><img src="../images/logo_icehr.svg"/></td>
                    </tr>
                    <tr>
                        <td align="center" colspan="2" height="80px">
                            <div style="font-size: 30px">ใบสมัครเข้ารับการอบรมหลักสูตรด้านใบอนุญาตขับรถภาคทฤษฎี</div>
                        </td>
                    </tr>
                    <tr>
                        <td align="right" width="550px">เลขที่/No</td>
                        <td align="center" width="150px" class="txtDash"><strong><?= '2562/0001'; ?></strong></td>
                    </tr>
                    <tr>
                        <td align="right" width="550px">วันที่/Date</td>
                        <td align="center" width="150px" class="txtDash">
                            <strong><?= '13 ก.ค. 2562'; ?></strong>
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
                        <td width="90px">ข้าพเจ้า/Name</td>
                        <td width="550px" class="txtDash"><strong><?= 'นายพร้อมเลิศ หล่อวิจิตร'; ?></strong></td>
                    </tr>
                </table>
            </td>
        </tr>

        <tr>
            <td>
                <table width="700px" align="center" border="0" cellpadding="0" cellspacing="0" style="padding-top: 5px;">
                    <tr>
                        <td width="155px">เลขประจำประชาชน/ID No</td>
                        <td width="545px" class="txtDash"><strong><?= '3 1006 00317 06 8'; ?></strong></td>
                    </tr>
                </table>
            </td>
        </tr>

        <tr>
            <td>
                <table width="700px" align="center" border="0" cellpadding="0" cellspacing="0" style="padding-top: 5px;">
                    <tr>
                        <td width="240px">ที่อยู่บ้านเลขที่ตามบัตรประชาชน/Address</td>
                        <td width="130px" class="txtDash"><strong><?= '11/13'; ?></strong></td>
                        <td width="55px">หมู่/Moo</td>
                        <td width="50px" class="txtDash"><strong><?= '-'; ?></strong></td>
                        <td width="55px">ซอย/Soi</td>
                        <td width="170px" class="txtDash"><strong><?= 'งามวงศ์วาน 59'; ?></strong></td>
                    </tr>
                </table>
            </td>
        </tr>

        <tr>
            <td>
                <table width="700px" align="center" border="0" cellpadding="0" cellspacing="0" style="padding-top: 5px;">
                    <tr>
                        <td width="70px">ถนน/Road</td>
                        <td width="240px" class="txtDash"><strong><?= 'งามวงศ์วาน'; ?></strong></td>
                        <td width="150px">แขวง/ตำบล/Sub-District</td>
                        <td width="240px" class="txtDash"><strong><?= 'ลาดยาว'; ?></strong></td>
                    </tr>
                </table>
            </td>
        </tr>

        <tr>
            <td>
                <table width="700px" align="center" border="0" cellpadding="0" cellspacing="0" style="padding-top: 5px;">
                    <tr>
                        <td width="115px">เขต/อำเภอ/District</td>
                        <td width="240px" class="txtDash"><strong><?= 'จตุจักร'; ?></strong></td>
                        <td width="105px">จังหวัด/Province</td>
                        <td width="240px" class="txtDash"><strong><?= 'กรุงเทพมหานคร'; ?></strong></td>
                    </tr>
                </table>
            </td>
        </tr>

        <tr>
            <td>
                <table width="700px" align="center" border="0" cellpadding="0" cellspacing="0" style="padding-top: 5px;">
                    <tr>
                        <td width="105px">โทรมือถือ/Phone</td>
                        <td width="595px" class="txtDash"><strong><?= '0850581776'; ?></strong></td>
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
                        <td width="530px">๑.&nbsp;&nbsp;<?= (TRUE) ? '<img src="../images/driveTrain/checked.png">' : '<img src="../images/driveTrain/unchecked.png">'; ?>
                            &nbsp;หลักสูตรการอบรมสำหรับผู้ขอใบอนุญาตขับรถ
                        </td>
                        <td width="50px">จำนวน</td>
                        <td width="20px" align="center">๕</td>
                        <td width="30px" align="center">ชั่วโมง</td>
                    </tr>
                    <tr>
                        <td width="60px">&nbsp;</td>
                        <td width="530px">๒.&nbsp;&nbsp;<?= (FALSE) ? '<img src="../images/driveTrain/checked.png">' : '<img src="../images/driveTrain/unchecked.png">'; ?>
                            &nbsp;หลักสูตรการอบรมสำหรับผู้ขอต่ออายุใบอนุญาตขับรถ
                        </td>
                        <td width="50px">จำนวน</td>
                        <td width="20px" align="center">๒</td>
                        <td width="30px" align="center">ชั่วโมง</td>
                    </tr>
                    <tr>
                        <td width="60px">&nbsp;</td>
                        <td width="530px">๓.&nbsp;&nbsp;<?= (FALSE) ? '<img src="../images/driveTrain/checked.png">' : '<img src="../images/driveTrain/unchecked.png">'; ?>
                            &nbsp;หลักสูตรการอบรมสำหรับผู้ขอต่ออายุใบอนุญาตขับรถ
                        </td>
                        <td width="50px">จำนวน</td>
                        <td width="20px" align="center">๑</td>
                        <td width="30px" align="center">ชั่วโมง</td>
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
                            <?= (FALSE) ? '<img src="../images/driveTrain/checked.png">' : '<img src="../images/driveTrain/unchecked.png">'; ?>
                            <font size="-1">&nbsp;รถยนต์ส่วนบุคคลชั่วคราว</font>
                        </td>
                        <td width="210px">
                            <?= (TRUE) ? '<img src="../images/driveTrain/checked.png">' : '<img src="../images/driveTrain/unchecked.png">'; ?>
                            <font size="-1">&nbsp;รถจักรยานยนต์ส่วนบุคคลชั่วคราว</font>
                        </td>
                        <td width="180px">
                            <?= (TRUE) ? '<img src="../images/driveTrain/checked.png">' : '<img src="../images/driveTrain/unchecked.png">'; ?>
                            <font size="-1">&nbsp;รถสามล้อส่วนบุคคลชั่วคราว</font>
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
                        <td width="580px" class="txtDash"><strong><?= '10 ก.ค. 2562'; ?></strong></td>
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
                        <td>๑. ข้าพเจ้ามีคุณสมบัติและไม่มีลักษณะต้องห้ามตามกฎหมายว่าด้วยรถยนต์กำหนด ในการขอรับใบอนุญาตขับรถ</td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td>๒. ข้าพเจ้าได้ทราบแล้วว่า เมื่อผ่านการอบรมภาคทฤษฎีตามที่สมัครแล้ว จะต้องผ่านการทดสอบสมรรถภาพของร่างกาย</td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ก่อนเข้ารับการทดสอบข้อเขียนภาคทฤษฎีด้วยระบบอิเล็กทรอนิกส์</td>
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
                        <td align="center">( <?= 'นายพร้อมเลิศ หล่อวิจิตร'; ?> )</td>
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
    </body>
    </html>

<?php
// Write some HTML code:
$html = ob_get_contents();
ob_end_clean();

$mpdf->WriteHTML($html);

// Output a PDF file directly to the browser
$mpdf->Output();
