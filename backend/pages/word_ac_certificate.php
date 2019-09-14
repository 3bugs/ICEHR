<?php
require_once '../vendor/autoload.php';
require_once '../include/head_php.inc';

$traineeId = $_GET['trainee_id'];
$serviceType = $_GET['service_type'];

if (!isset($traineeId) || !isset($serviceType)) {
    echo 'Error: ระบุ parameter ไม่ครบ';
    $db->close();
    exit();
}

$sql = null;
switch ($serviceType) {
    case SERVICE_TYPE_TRAINING:
        $sql = "SELECT ct.form_number, ct.title, ct.first_name, ct.last_name,   
                       cm.title AS course_title, c.batch_number, c.begin_date, c.end_date
        FROM course_trainee ct 
            INNER JOIN course_registration cr 
                ON cr.id = ct.course_registration_id 
            INNER JOIN course c 
                ON c.id = cr.course_id 
            INNER JOIN course_master cm 
                ON cm.id = c.course_master_id
        WHERE ct.id = $traineeId";
        break;
    case SERVICE_TYPE_SOCIAL:
        $sql = "SELECT cr.form_number, cr.title, cr.first_name, cr.last_name,   
                       cm.title AS course_title, c.batch_number, c.begin_date, c.end_date
        FROM course_registration_social cr  
            INNER JOIN course c 
                ON c.id = cr.course_id 
            INNER JOIN course_master cm 
                ON cm.id = c.course_master_id
        WHERE cr.id = $traineeId";
        break;
}

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

$fileName = "cert-{$trainee['form_number']}.docx";

header("Content-Type: application/vnd.openxmlformats-officedocument.wordprocessing‌​ml.document");
header('Content-Disposition: attachment; filename="' . $fileName . '"');
header('Cache-Control: max-age=0');

$phpWord = new \PhpOffice\PhpWord\PhpWord();

$section = $phpWord->addSection([
    'pageSizeH' => \PhpOffice\PhpWord\Shared\Converter::cmToTwip(19.5),
    'pageSizeW' => \PhpOffice\PhpWord\Shared\Converter::cmToTwip(25.5)
]);

$tuText = $section->addText('มหาวิทยาลัยธรรมศาสตร์');
$fontStyle = new \PhpOffice\PhpWord\Style\Font();
$fontStyle->setBold(true);
$fontStyle->setSize(57 - 5);
$tuText->setFontStyle($fontStyle);

$icehrText = $section->addText('สถาบันเสริมศึกษาและทรัพยากรมนุษย์');
$fontStyle = new \PhpOffice\PhpWord\Style\Font();
$fontStyle->setBold(false);
$fontStyle->setSize(39 - 5);
$icehrText->setFontStyle($fontStyle);

$certText = $section->addText('ใบรับรองฉบับนี้ให้ไว้เพื่อแสดงว่า');
$fontStyle = new \PhpOffice\PhpWord\Style\Font();
$fontStyle->setBold(false);
$fontStyle->setSize(28 - 5);
$certText->setFontStyle($fontStyle);

$displayName = "{$trainee['title']}{$trainee['first_name']} {$trainee['last_name']}";
$displayNameText = $section->addText($displayName);
$fontStyle = new \PhpOffice\PhpWord\Style\Font();
$fontStyle->setBold(true);
$fontStyle->setSize(40 - 5);
$displayNameText->setFontStyle($fontStyle);

$courseText = $section->addText('ได้ผ่านการอบรม หลักสูตร "' . $trainee['course_title'] . '" รุ่นที่ ' . thaiNumDigit($trainee['batch_number']));
$fontStyle = new \PhpOffice\PhpWord\Style\Font();
$fontStyle->setBold(true);
$fontStyle->setSize(26 - 5);
$courseText->setFontStyle($fontStyle);

$dateText = $section->addText(($trainee['begin_date'] === $trainee['end_date'] ? 'อบรมวันที่ ' : 'อบรมตั้งแต่วันที่ ') .
    thaiNumDigit(getThaiIntervalDate(date_create($trainee['begin_date']), date_create($trainee['end_date']))));
$fontStyle = new \PhpOffice\PhpWord\Style\Font();
$fontStyle->setBold(false);
$fontStyle->setSize(18 - 5);
$dateText->setFontStyle($fontStyle);

$blessText = $section->addText('ขอให้มีความสุข ความเจริญ และบำเพ็ญตนให้เป็นประโยชน์แก่สังคมสืบไป');
$blessText->setFontStyle($fontStyle);

define('NUM_SPACES', 3);
$spaces = str_repeat(' ', NUM_SPACES);
$giveDateText = $section->addText("ให้ไว้{$spaces}ณ{$spaces}วันที่{$spaces}" . thaiNumDigit(getThaiDateForCertificate(date_create($trainee['end_date']), NUM_SPACES)));
$giveDateText->setFontStyle($fontStyle);

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
