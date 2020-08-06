<?php
require_once '../vendor/autoload.php';
require_once '../include/head_php.inc';

error_reporting(E_ERROR | E_PARSE);
header('Content-type: text/html; charset=utf-8');
header('Expires: Sun, 01 Jan 2014 00:00:00 GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', FALSE);
header('Pragma: no-cache');

$courseId = $_GET['course_id'];
$traineeId = $_GET['trainee_id'];
$serviceType = $_GET['service_type'];

$courseText = $_POST['courseText'];
$fontSize = $_POST['fontSize'];

if (!isset($serviceType) || (!isset($traineeId) && !isset($courseId))) {
  echo 'Error: ระบุ parameter ไม่ครบ';
  $db->close();
  exit();
}

$where = null;
if (isset($courseId)) {
  $where = " c.id = $courseId ";
  if ($serviceType === SERVICE_TYPE_TRAINING) {
    //$where .= " AND ct.register_status = 'complete' ";
  } else {
    //$where .= " AND cr.register_status = 'complete' ";
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

$sql = "SELECT * FROM certificate_signer WHERE id = 1 OR id = 2";
$signerList = array();
if ($result = $db->query($sql)) {
  $signerCount = $result->num_rows;
  if ($signerCount >= 2) {
    while ($row = $result->fetch_assoc()) {
      array_push($signerList, $row);
    }
    $result->close();
  } else {
    echo 'ข้อมูลผู้ลงนามใบรับรองไม่ครบ 2 ท่าน: certificate signer count = ' . $signerCount;
    $result->close();
    $db->close();
    exit();
  }
} else {
  echo 'เกิดข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล: ' . $db->error . $sql;
  $db->close();
  exit();
}

header("Content-Type: application/vnd.openxmlformats-officedocument.wordprocessing‌​ml.document");
header('Content-Disposition: attachment; filename="' . $fileName . '"');
header('Cache-Control: max-age=0');

$phpWord = new \PhpOffice\PhpWord\PhpWord();

$section = $phpWord->addSection([
    'pageSizeH' => \PhpOffice\PhpWord\Shared\Converter::cmToTwip(19.7),
    'pageSizeW' => \PhpOffice\PhpWord\Shared\Converter::cmToTwip(25.5),
    'marginLeft' => \PhpOffice\PhpWord\Shared\Converter::cmToTwip(2.0),
    'marginRight' => \PhpOffice\PhpWord\Shared\Converter::cmToTwip(2.0),
    'marginTop' => \PhpOffice\PhpWord\Shared\Converter::cmToTwip(4.5),
    'marginBottom' => \PhpOffice\PhpWord\Shared\Converter::cmToTwip(1.0)
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
  $fontStyle->setName('Angsana New');
  $fontStyle->setBold(true);
  $fontStyle->setSize(48);
  $text->setFontStyle($fontStyle);

  $paragraphStyle = new \PhpOffice\PhpWord\Style\Paragraph();
  $paragraphStyle->setAlignment('center');
  $paragraphStyle->setSpaceBefore(0);
  //$paragraphStyle->setSpaceAfter(\PhpOffice\PhpWord\Shared\Converter::cmToTwip(0.212));
  $paragraphStyle->setSpaceAfter(0.0);
  $paragraphStyle->setLineHeight(1.0);
  $text->setParagraphStyle($paragraphStyle);

  // สถาบันเสริมศึกษาและทรัพยากรมนุษย์
  $text = $section->addText('สถาบันเสริมศึกษาและทรัพยากรมนุษย์');

  $fontStyle = new \PhpOffice\PhpWord\Style\Font();
  $fontStyle->setName('Angsana New');
  $fontStyle->setBold(false);
  $fontStyle->setSize(36);
  $text->setFontStyle($fontStyle);

  $paragraphStyle = new \PhpOffice\PhpWord\Style\Paragraph();
  $paragraphStyle->setAlignment('center');
  $paragraphStyle->setLineHeight(1.0);
  //$paragraphStyle->setSpaceAfter(\PhpOffice\PhpWord\Shared\Converter::cmToTwip(0.212));
  $paragraphStyle->setSpaceAfter(0.0);
  $text->setParagraphStyle($paragraphStyle);

  // ใบรับรองฉบับนี้ให้ไว้เพื่อแสดงว่า
  $text = $section->addText('ใบรับรองฉบับนี้ให้ไว้เพื่อแสดงว่า');

  $fontStyle = new \PhpOffice\PhpWord\Style\Font();
  $fontStyle->setName('Angsana New');
  $fontStyle->setBold(false);
  $fontStyle->setSize(22);
  $text->setFontStyle($fontStyle);

  $paragraphStyle = new \PhpOffice\PhpWord\Style\Paragraph();
  $paragraphStyle->setAlignment('center');
  $paragraphStyle->setLineHeight(1.0);
  //$paragraphStyle->setSpaceAfter(\PhpOffice\PhpWord\Shared\Converter::cmToTwip(0.212));
  $paragraphStyle->setSpaceAfter(0.0);
  $text->setParagraphStyle($paragraphStyle);

  // ชื่อผู้เข้าอบรม
  $displayName = "{$trainee['title']}{$trainee['first_name']} {$trainee['last_name']}";
  $text = $section->addText($displayName);
  $fontStyle = new \PhpOffice\PhpWord\Style\Font();
  //$fontStyle->setName('TH Charm of AU');
  $fontStyle->setName('Browallia New');
  $fontStyle->setBold(true);
  $fontStyle->setSize(36);
  $text->setFontStyle($fontStyle);

  $paragraphStyle = new \PhpOffice\PhpWord\Style\Paragraph();
  $paragraphStyle->setAlignment('center');
  $paragraphStyle->setLineHeight(1.0);
  $paragraphStyle->setSpaceAfter(0.0);
  $text->setParagraphStyle($paragraphStyle);

  // ได้ผ่านการอบรมหลักสูตร
  if ($courseText) {
    $text = $section->addText($courseText);
  } else {
    $text = $section->addText('ได้ผ่านการอบรม หลักสูตร "' . $trainee['course_title'] . '" รุ่นที่ ' . $trainee['batch_number']);
  }
  $fontStyle = new \PhpOffice\PhpWord\Style\Font();
  $fontStyle->setName('Angsana New');
  $fontStyle->setBold(true);
  if ($fontSize) {
    $fontStyle->setSize(floatval($fontSize));
  } else {
    $fontStyle->setSize(22);
  }
  $text->setFontStyle($fontStyle);

  $paragraphStyle = new \PhpOffice\PhpWord\Style\Paragraph();
  $paragraphStyle->setAlignment('center');
  $paragraphStyle->setLineHeight(1.0);
  //$paragraphStyle->setSpaceAfter(250);
  $paragraphStyle->setSpaceAfter(0.0);
  $text->setParagraphStyle($paragraphStyle);

  // อบรมวันที่
  $text = $section->addText(($trainee['begin_date'] === $trainee['end_date'] ? 'อบรมวันที่ ' : 'อบรมตั้งแต่วันที่ ') .
      getThaiIntervalDate(date_create($trainee['begin_date']), date_create($trainee['end_date'])));
  $fontStyle = new \PhpOffice\PhpWord\Style\Font();
  $fontStyle->setName('Angsana New');
  $fontStyle->setBold(false);
  $fontStyle->setSize(18);
  $text->setFontStyle($fontStyle);

  $paragraphStyle = new \PhpOffice\PhpWord\Style\Paragraph();
  $paragraphStyle->setAlignment('center');
  $paragraphStyle->setLineHeight(1.0);
  //$paragraphStyle->setSpaceAfter(100);
  $paragraphStyle->setSpaceAfter(0.0);
  $text->setParagraphStyle($paragraphStyle);

  // ขอให้มีความสุข ความเจริญ และบำเพ็ญตนให้เป็นประโยชน์แก่สังคมสืบไป
  $text = $section->addText('ขอให้มีความสุข ความเจริญ และบำเพ็ญตนให้เป็นประโยชน์แก่สังคมสืบไป');
  $text->setFontStyle($fontStyle);

  $paragraphStyle = new \PhpOffice\PhpWord\Style\Paragraph();
  $paragraphStyle->setAlignment('center');
  $paragraphStyle->setLineHeight(1.0);
  //$paragraphStyle->setSpaceAfter(100);
  $paragraphStyle->setSpaceAfter(0.0);
  $text->setParagraphStyle($paragraphStyle);

  // ให้ไว้ ณ วันที่
  define('NUM_SPACES', 3);
  $spaces = str_repeat(' ', NUM_SPACES);
  $text = $section->addText("ให้ไว้{$spaces}ณ{$spaces}วันที่{$spaces}" . getThaiDateForCertificate(date_create($trainee['end_date']), NUM_SPACES));
  $text->setFontStyle($fontStyle);

  $paragraphStyle = new \PhpOffice\PhpWord\Style\Paragraph();
  $paragraphStyle->setAlignment('center');
  $paragraphStyle->setLineHeight(1.0);
  //$paragraphStyle->setSpaceBefore(350);
  $paragraphStyle->setSpaceAfter(\PhpOffice\PhpWord\Shared\Converter::cmToTwip(0.2));
  $text->setParagraphStyle($paragraphStyle);

  //$section->addText("\n");

//$section->addImage('../uploads/signatures/signature001.jpg');
//$section->addImage('../uploads/signatures/signature002.png');

  $table = $section->addTable();
  $row = $table->addRow();
  $leftCell = $row->addCell(5400);
  $row->addCell(800);
  $rightCell = $row->addCell(6200);

  $paragraphStyle = new \PhpOffice\PhpWord\Style\Paragraph();
  $paragraphStyle->setAlignment('center')->setSpaceBefore(200);
  $leftTextRun = $leftCell->addTextRun();
  $leftTextRun->setParagraphStyle($paragraphStyle);
  $leftTextRun->addImage(
      dirname(__FILE__) . '/../uploads/signatures/' . $signerList[0]['signature_image'],
      array(
          'height' => 30
      )
  );

  $paragraphStyle = new \PhpOffice\PhpWord\Style\Paragraph();
  $paragraphStyle->setAlignment('center');
  $rightTextRun = $rightCell->addTextRun();
  $rightTextRun->setParagraphStyle($paragraphStyle);
  $rightTextRun->addImage(
      dirname(__FILE__) . '/../uploads/signatures/' . $signerList[1]['signature_image'],
      array(
          'height' => 40
      )
  );

  //\PhpOffice\PhpWord\Shared\Html::addHtml($leftCell, $leftSignatureHtml);
  //\PhpOffice\PhpWord\Shared\Html::addHtml($rightCell, $rightSignatureHtml);

  $row = $table->addRow();

  $title = $signerList[0]['title'];
  $firstName = $signerList[0]['first_name'];
  $lastName = $signerList[0]['last_name'];
  $displayName = "{$title}{$firstName} {$lastName}";
  $position = $signerList[0]['position'];

  $leftCell = $row->addCell(5000);
  $leftCellText = $leftCell->addText("({$displayName})\n{$position}");

  $row->addCell(1100);

  $title = $signerList[1]['title'];
  $firstName = $signerList[1]['first_name'];
  $lastName = $signerList[1]['last_name'];
  $displayName = "{$title}{$firstName} {$lastName}";
  $position = $signerList[1]['position'];

  $rightCell = $row->addCell(5700);
  $rightCellText = $rightCell->addText("({$displayName})\n{$position}");

  $fontStyle = new \PhpOffice\PhpWord\Style\Font();
  $fontStyle->setName('Angsana New');
  $fontStyle->setBold(true);
  $fontStyle->setSize(14);

  $paragraphStyle = new \PhpOffice\PhpWord\Style\Paragraph();
  $paragraphStyle->setAlignment('center');
  $paragraphStyle->setLineHeight(1.0);
  $paragraphStyle->setSpaceAfter(0.0);
  //$paragraphStyle->setSpaceBefore(100);

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
