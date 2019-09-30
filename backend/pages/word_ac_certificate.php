<?php
require_once '../vendor/autoload.php';
require_once '../include/head_php.inc';

$courseId = $_GET['course_id'];
$traineeId = $_GET['trainee_id'];
$serviceType = $_GET['service_type'];

if (!isset($serviceType) || (!isset($traineeId) && !isset($courseId))) {
    echo 'Error: ระบุ parameter ไม่ครบ';
    $db->close();
    exit();
}

$where = null;
if (isset($courseId)) {
    $where = " c.id = $courseId ";
    if ($serviceType === SERVICE_TYPE_TRAINING) {
        $where .= " AND ct.register_status = 'complete' ";
    } else {
        $where .= " AND cr.register_status = 'complete' ";
    }
} else {
    if ($serviceType === SERVICE_TYPE_TRAINING) {
        $where = " ct.id = $traineeId ";
    } else {
        $where = " cr.id = $traineeId ";
    }
}

$sql = null;
switch ($serviceType) {
    case SERVICE_TYPE_TRAINING:
        $sql = "SELECT ct.form_number, ct.title, ct.first_name, ct.last_name,
                       cm.title AS course_title, c.id AS course_id, c.batch_number, c.begin_date, c.end_date
        FROM course_trainee ct 
            INNER JOIN course_registration cr 
                ON cr.id = ct.course_registration_id 
            INNER JOIN course c 
                ON c.id = cr.course_id 
            INNER JOIN course_master cm 
                ON cm.id = c.course_master_id
        WHERE $where";
        break;
    case SERVICE_TYPE_SOCIAL:
        $sql = "SELECT cr.form_number, cr.title, cr.first_name, cr.last_name, 
                       cm.title AS course_title, c.id AS course_id, c.batch_number, c.begin_date, c.end_date
        FROM course_registration_social cr  
            INNER JOIN course c 
                ON c.id = cr.course_id 
            INNER JOIN course_master cm 
                ON cm.id = c.course_master_id
        WHERE $where";
        break;
}

$traineeList = array();
if ($result = $db->query($sql)) {
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            array_push($traineeList, $row);
        }
        $result->close();
    } else {
        echo 'ไม่มีข้อมูลผู้เข้าอบรมที่มีสถานะการชำระเงินเป็น "สมบูรณ์"';
        $result->close();
        $db->close();
        exit();
    }
} else {
    echo 'เกิดข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล: ' . $db->error . $sql;
    $db->close();
    exit();
}

if (isset($courseId)) {
    $serviceTypePrefix = ($serviceType === SERVICE_TYPE_TRAINING) ? 'AC' : 'SO';
    $fileName = "Cert-{$serviceTypePrefix}-{$traineeList[0]['course_id']}.docx";
} else {
    $fileName = "Cert-{$traineeList[0]['form_number']}.docx";
}

header("Content-Type: application/vnd.openxmlformats-officedocument.wordprocessing‌​ml.document");
header('Content-Disposition: attachment; filename="' . $fileName . '"');
header('Cache-Control: max-age=0');

$phpWord = new \PhpOffice\PhpWord\PhpWord();

$section = $phpWord->addSection([
    'pageSizeH' => \PhpOffice\PhpWord\Shared\Converter::cmToTwip(19.5),
    'pageSizeW' => \PhpOffice\PhpWord\Shared\Converter::cmToTwip(25.5)
]);

$i = 0;
foreach ($traineeList as $trainee) {
    if ($i !== 0) {
        $section->addPageBreak();
    }

    //$phpWord->addParagraphStyle('centerStyle', array('align' => 'center', 'spaceAfter' => 0));

    // มหาวิทยาลัยธรรมศาสตร์
    $text = $section->addText('มหาวิทยาลัยธรรมศาสตร์');

    $fontStyle = new \PhpOffice\PhpWord\Style\Font();
    $fontStyle->setName('IrisUPC');
    $fontStyle->setBold(true);
    $fontStyle->setSize(57 - 5);
    $text->setFontStyle($fontStyle);

    $paragraphStyle = new \PhpOffice\PhpWord\Style\Paragraph();
    $paragraphStyle->setAlignment('center');
    $paragraphStyle->setSpaceBefore(150);
    $paragraphStyle->setLineHeight(0.8);
    $text->setParagraphStyle($paragraphStyle);

    // สถาบันเสริมศึกษาและทรัพยากรมนุษย์
    $text = $section->addText('สถาบันเสริมศึกษาและทรัพยากรมนุษย์');

    $fontStyle = new \PhpOffice\PhpWord\Style\Font();
    $fontStyle->setName('IrisUPC');
    $fontStyle->setBold(false);
    $fontStyle->setSize(39 - 5);
    $text->setFontStyle($fontStyle);

    $paragraphStyle = new \PhpOffice\PhpWord\Style\Paragraph();
    $paragraphStyle->setAlignment('center');
    $paragraphStyle->setLineHeight(0.8);
    $paragraphStyle->setSpaceAfter(100);
    $text->setParagraphStyle($paragraphStyle);

    // ใบรับรองฉบับนี้ให้ไว้เพื่อแสดงว่า
    $text = $section->addText('ใบรับรองฉบับนี้ให้ไว้เพื่อแสดงว่า');

    $fontStyle = new \PhpOffice\PhpWord\Style\Font();
    $fontStyle->setName('IrisUPC');
    $fontStyle->setBold(false);
    $fontStyle->setSize(28 - 5);
    $text->setFontStyle($fontStyle);

    $paragraphStyle = new \PhpOffice\PhpWord\Style\Paragraph();
    $paragraphStyle->setAlignment('center');
    $paragraphStyle->setLineHeight(0.8);
    $text->setParagraphStyle($paragraphStyle);

    // ชื่อผู้เข้าอบรม
    $displayName = "{$trainee['title']}{$trainee['first_name']} {$trainee['last_name']}";
    $text = $section->addText($displayName);
    $fontStyle = new \PhpOffice\PhpWord\Style\Font();
    $fontStyle->setName('TH Charm of AU');
    $fontStyle->setBold(false);
    $fontStyle->setSize(40 - 2);
    $text->setFontStyle($fontStyle);

    $paragraphStyle = new \PhpOffice\PhpWord\Style\Paragraph();
    $paragraphStyle->setAlignment('center');
    $paragraphStyle->setLineHeight(0.6);
    $text->setParagraphStyle($paragraphStyle);

    // ได้ผ่านการอบรมหลักสูตร
    $text = $section->addText('ได้ผ่านการอบรม หลักสูตร "' . $trainee['course_title'] . '" รุ่นที่ ' . thaiNumDigit($trainee['batch_number']));
    $fontStyle = new \PhpOffice\PhpWord\Style\Font();
    $fontStyle->setName('IrisUPC');
    $fontStyle->setBold(true);
    $fontStyle->setSize(26 - 2);
    $text->setFontStyle($fontStyle);

    $paragraphStyle = new \PhpOffice\PhpWord\Style\Paragraph();
    $paragraphStyle->setAlignment('center');
    $paragraphStyle->setLineHeight(0.8);
    $paragraphStyle->setSpaceAfter(250);
    $text->setParagraphStyle($paragraphStyle);

    // อบรมวันที่
    $text = $section->addText(($trainee['begin_date'] === $trainee['end_date'] ? 'อบรมวันที่ ' : 'อบรมตั้งแต่วันที่ ') .
        thaiNumDigit(getThaiIntervalDate(date_create($trainee['begin_date']), date_create($trainee['end_date']))));
    $fontStyle = new \PhpOffice\PhpWord\Style\Font();
    $fontStyle->setName('IrisUPC');
    $fontStyle->setBold(false);
    $fontStyle->setSize(20);
    $text->setFontStyle($fontStyle);

    $paragraphStyle = new \PhpOffice\PhpWord\Style\Paragraph();
    $paragraphStyle->setAlignment('center');
    $paragraphStyle->setLineHeight(0.8);
    $paragraphStyle->setSpaceAfter(100);
    $text->setParagraphStyle($paragraphStyle);

    // ขอให้มีความสุข ความเจริญ และบำเพ็ญตนให้เป็นประโยชน์แก่สังคมสืบไป
    $text = $section->addText('ขอให้มีความสุข ความเจริญ และบำเพ็ญตนให้เป็นประโยชน์แก่สังคมสืบไป');
    $text->setFontStyle($fontStyle);

    $paragraphStyle = new \PhpOffice\PhpWord\Style\Paragraph();
    $paragraphStyle->setAlignment('center');
    $paragraphStyle->setLineHeight(0.8);
    $paragraphStyle->setSpaceAfter(100);
    $text->setParagraphStyle($paragraphStyle);

    // ให้ไว้ ณ วันที่
    define('NUM_SPACES', 3);
    $spaces = str_repeat(' ', NUM_SPACES);
    $text = $section->addText("ให้ไว้{$spaces}ณ{$spaces}วันที่{$spaces}" . thaiNumDigit(getThaiDateForCertificate(date_create($trainee['end_date']), NUM_SPACES)));
    $text->setFontStyle($fontStyle);

    $paragraphStyle = new \PhpOffice\PhpWord\Style\Paragraph();
    $paragraphStyle->setAlignment('center');
    $paragraphStyle->setLineHeight(0.8);
    $paragraphStyle->setSpaceAfter(350);
    $text->setParagraphStyle($paragraphStyle);

    //$section->addText("\n");

//$section->addImage('../uploads/signatures/signature001.jpg');
//$section->addImage('../uploads/signatures/signature002.png');

    $section->addImage(dirname(__FILE__) . '/../uploads/signatures/signature001.jpg',
        array(
            'height' => 30
        )
    );

    $table = $section->addTable();
    $row = $table->addRow(-1);
    $leftCell = $row->addCell(5000);
    $row->addCell(1100);
    $rightCell = $row->addCell(5700);

    $paragraphStyle = new \PhpOffice\PhpWord\Style\Paragraph();
    $paragraphStyle->setAlignment('center');

    $leftTextRun = $leftCell->addTextRun();
    $leftTextRun->setParagraphStyle($paragraphStyle);
    $leftTextRun->addImage(
        dirname(__FILE__) . '/../uploads/signatures/signature001.jpg',
        array(
            'height' => 30
        )
    );
    $rightTextRun = $rightCell->addTextRun();
    $rightTextRun->setParagraphStyle($paragraphStyle);
    $rightTextRun->addImage(
        dirname(__FILE__) . '/../uploads/signatures/signature002.png',
        array(
            'height' => 40
        )
    );

    /*$leftSignatureHtml = <<<EOT
<table style="width: 100%">
<tr>
    <td style="text-align: center"><img src="../uploads/signatures/signature001.jpg" height="40"/></td>
</tr>
</table>
EOT;
    $rightSignatureHtml = <<<EOT
<table style="width: 100%">
<tr>
    <td style="text-align: center"><img src="../uploads/signatures/signature002.png" height="50"/></td>
</tr>
</table>
EOT;*/

    //\PhpOffice\PhpWord\Shared\Html::addHtml($leftCell, $leftSignatureHtml);
    //\PhpOffice\PhpWord\Shared\Html::addHtml($rightCell, $rightSignatureHtml);

    $row = $table->addRow(-1);
    $leftCell = $row->addCell(5000);
    $leftCellText = $leftCell->addText("(รองศาสตราจารย์เกศินี วิฑูรชาติ)\nอธิการบดีมหาวิทยาลัยธรรมศาสตร์");
    $row->addCell(1100);
    $rightCell = $row->addCell(5700);
    $rightCellText = $rightCell->addText("(ผู้ช่วยศาสตราจารย์ ดร.ศุภชัย ศรีสุชาติ)\nผู้อำนวยการสถาบันเสริมศึกษาและทรัพยากรมนุษย์");

    $fontStyle = new \PhpOffice\PhpWord\Style\Font();
    $fontStyle->setName('IrisUPC');
    $fontStyle->setBold(true);
    $fontStyle->setSize(18 - 2);

    $paragraphStyle = new \PhpOffice\PhpWord\Style\Paragraph();
    $paragraphStyle->setAlignment('center');
    $paragraphStyle->setLineHeight(1);
    $paragraphStyle->setSpaceBefore(100);

    $leftCellText->setFontStyle($fontStyle);
    $rightCellText->setFontStyle($fontStyle);
    $leftCellText->setParagraphStyle($paragraphStyle);
    $rightCellText->setParagraphStyle($paragraphStyle);

    $i++;
}

$writer = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');

//$writer->save('helloWorld.docx');
$writer->save("php://output");

require_once '../include/foot_php.inc';

exit();

/*
 * Note: it's possible to customize font style of the Text element you add in three ways:
 * - inline;
 * - using named font style (new font style object will be implicitly created);
 * - using explicitly created font style object.
 */

// Adding Text element with font customized inline...
$section->addText(
    '"Great achievement is usually born of great sacrifice, '
    . 'and is never the result of selfishness." '
    . '(Napoleon Hill)',
    array('name' => 'Tahoma', 'size' => 10)
);

// Adding Text element with font customized using named font style...
$fontStyleName = 'oneUserDefinedStyle';
$phpWord->addFontStyle(
    $fontStyleName,
    array('name' => 'Tahoma', 'size' => 10, 'color' => '1B2232', 'bold' => true)
);
$section->addText(
    '"The greatest accomplishment is not in never falling, '
    . 'but in rising again after you fall." '
    . '(Vince Lombardi)',
    $fontStyleName
);

// Adding Text element with font customized using explicitly created font style object...
$fontStyle = new \PhpOffice\PhpWord\Style\Font();
$fontStyle->setBold(true);
$fontStyle->setName('Tahoma');
$fontStyle->setSize(13);
$myTextElement = $section->addText('"Believe you can and you\'re halfway there." (Theodor Roosevelt)');
$myTextElement->setFontStyle($fontStyle);

// Saving the document as OOXML file...
$writer = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
//$writer->save('helloWorld.docx');
$writer->save("php://output");

// Saving the document as ODF file...
/*$writer = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'ODText');
$writer->save('helloWorld.odt');*/

// Saving the document as HTML file...
/*$writer = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'HTML');
$writer->save('helloWorld.html');*/

/* Note: we skip RTF, because it's not XML-based and requires a different example. */
/* Note: we skip PDF, because "HTML-to-PDF" approach is used to create PDF documents. */
