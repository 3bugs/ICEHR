<?php
ob_start();
require_once '../vendor/autoload.php';
require_once '../include/head_php.inc';
define("_VALID_ACCESS", 1);

//include("configuration.php");
//require_once("includes/ms_com.php");
//require_once("includes/datetime.class.php");

//$dbObj = new DBConn;

$today = date("Y-m-d");

//require('MPDF/mpdf.php');
//$mpdf = new mPDF("UTF-8", "A4", 15, "niramit");
//$mpdf->SetFont('niramit');

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

//$applyID = mosGetParam($_FORM, 'A', '');

//$qryA = "SELECT drive_person_apply.id, drive_person_apply.date_train, drive_person_apply.year_apply, drive_prefix.prefix_th, drive_person_apply.fname, drive_person_apply.lname, drive_person_apply.year_apply, drive_person_apply.no_apply, drive_person_apply.date_record, drive_person_apply.idcard_no, drive_person_apply.address, drive_person_apply.moo, drive_person_apply.soi, drive_person_apply.road, drive_person_apply.sub_district, drive_person_apply.district, drive_person_apply.province, drive_person_apply.mobile, drive_person_apply.course_train, drive_person_apply.type_car1, drive_person_apply.type_car2, drive_person_apply.type_car3 FROM drive_person_apply INNER JOIN drive_prefix ON drive_person_apply.prefix_id=drive_prefix.id WHERE drive_person_apply.id='$applyID' ";
//$resA = $dbObj->execQuery($qryA);
//$row = $dbObj->fetchObject($resA);
//	$address = thainumDigit($row->address);
//	$moo = ($row->moo=="")? "" : " หมู่ ".thainumDigit($row->moo);
//	$soi = ($row->soi=="")? "" : " ซอย".thainumDigit($row->soi);
//	$road = ($row->road=="")? "" : " ถนน".thainumDigit($row->road);
//	$subDistrict = ($row->sub_district=="")? "" : " แขวง/ตำบล".thainumDigit($row->sub_district);
//	$district = ($row->district=="")? "" : " เขต/อำเภอ".thainumDigit($row->district);
//	$province = ($row->province=="")? "" : " จังหวัด".thainumDigit($row->province);

//	$courseTrain = $row->course_train;

//	$qryDoc = "SELECT * FROM drive_docno WHERE apply_id='$applyID' ";
//	$resDoc = $dbObj->execQuery($qryDoc);
//	$rsDoc = $dbObj->fetchObject($resDoc);
//	$docNo = "0516.34/ขสต ".$row->year_apply."/".$rsDoc->doc_no;
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

        <?php
        function thaiNumDigit($num)
        {
            return str_replace(
                array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9'),
                array('๐', '๑', '๒', '๓', '๔', '๕', '๖', '๗', '๘', '๙'),
                $num
            );
        }

        ?>
    </head>

    <body>
    <table width="650px" align="center" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td>
                <table width="650px" align="center" border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse;">
                    <tr>
                        <td width="220px" valign="top" style="padding-top: 110px">
                            ที่ อว ๖๗.๔๙ / ขสต ๒๕๖๒ / ๙๐๘
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
                <td width="320px" align="left" style="font-size: 22px"><strong><?= 'นายพร้อมเลิศ หล่อวิจิตร'; ?></strong></td>
            </tr>
            <tr>
                <td colspan="3" align="justify">
                    เลขประจำตัวประชาชน <?= '๓ ๑๐๐๖ ๐๐๓๑๗ ๐๖ ๘'; ?>&nbsp;&nbsp;&nbsp;
                    อยู่บ้านเลขที่ <?= '๑๑/๑๓ วรวรรณ พาร์ค คอนโดมิเนียม แขวงลาดยาว เขตจตุจักร กรุงเทพมหานคร'; ?>
                </td>
            </tr>
        </table>
    </div>

    <div class="center_div" style="margin-top: 15px; font-size: 21px">
        เป็นผู้มีความสามารถ โดยผ่านการอบรมตามหลักสูตรที่กรมการขนส่งทางบกกำหนด ดังนี้
    </div>

    <table width="100%" align="center" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td>
                <table width="100%" align="center" border="0" cellpadding="0" cellspacing="0" style="margin-top: 10px;">
                    <tr>
                        <td style="padding-left: 25px"><img src="../images/driveTrain/<?= (TRUE) ? 'checked.png' : 'unchecked.png'; ?>"/></td>
                        <td width="500px">หลักสูตรการอบรมสำหรับผู้ขอรับใบอนุญาตขับรถตามกฎหมายว่าด้วยรถยนต์</td>
                        <td>จำนวน</td>
                        <td><?= '๕'; ?></td>
                        <td> ชั่วโมง</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table width="100%" align="center" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td>
                <table width="100%" align="center" border="0" cellpadding="0" cellspacing="0" style=" margin-top: 10px;">
                    <tr>
                        <td style="padding-left: 25px"><img src="../images/driveTrain/<?= (FALSE) ? 'checked.png' : 'unchecked.png'; ?>"/></td></td>
                        <td width="500px">หลักสูตรการอบรมสำหรับผู้ขอต่ออายุใบอนุญาตขับรถตามกฎหมายว่าด้วยรถยนต์</td>
                        <td>จำนวน</td>
                        <td><?= '๒'; ?></td>
                        <td> ชั่วโมง</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table width="100%" align="center" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td>
                <table width="100%" align="center" border="0" cellpadding="0" cellspacing="0" style=" margin-top: 10px;">
                    <tr>
                        <td style="padding-left: 25px"><img src="../images/driveTrain/<?= (FALSE) ? 'checked.png' : 'unchecked.png'; ?>"/></td></td>
                        <td width="500px">หลักสูตรการอบรมสำหรับผู้ขอต่ออายุใบอนุญาตขับรถตามกฎหมายว่าด้วยรถยนต์</td>
                        <td>จำนวน</td>
                        <td><?= '๑'; ?></td>
                        <td> ชั่วโมง</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table width="650px" align="center" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td>
                <table width="650px" align="center" border="0" cellpadding="0" cellspacing="0" style="margin-top: 20px;">
                    <tr>
                        <td>
                            สำหรับประเภทใบอนุญาตขับ รถยนต์ส่วนบุคคลชั่วคราว
                            <? /*
                            if ($row->type_car1 == '1') {
                                echo "รถยนต์ส่วนบุคคลชั่วคราว&nbsp;&nbsp;&nbsp;";
                            }
                            if ($row->type_car2 == '1') {
                                echo "รถจักรยานยนต์ส่วนบุคคลชั่วคราว&nbsp;&nbsp;&nbsp;";
                            }
                            if ($row->type_car3 == '1') {
                                echo "รถสามล้อส่วนบุคคลชั่วคราว";
                            }
                            */ ?>
                        </td>
                    </tr>
                    <tr>
                        <td>เมื่อวันที่ <?= '๑๑ เดือนกรกฎาคม พ.ศ. ๒๕๖๒'; ?></td>
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
            <td width="325px" align="center">
                ให้ไว้ ณ วันที่ <?= '๑๖ เดือนกรกฎาคม พ.ศ. ๒๕๖๒'; ?>
            </td>
        </tr>
        <tr>
            <td></td>
            <td align="center" style="padding-top: 20px; padding-bottom: 10px">
                <img height="60px" src="../uploads/signatures/signature_sample.png"/>
            </td>
        </tr>
        <tr>
            <td></td>
            <td align="center">
                ( อาจารย์อิสรานุวัฒน์ ศรีคุณ )
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

    </body>
    </html>
<?php
$html = ob_get_contents();
ob_end_clean();

$mpdf->WriteHTML($html);
$mpdf->Output();
?>