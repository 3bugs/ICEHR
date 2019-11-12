<?php
require_once '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

$fileName = './data.xlsx';

$reader = new Xlsx();
$reader->setReadDataOnly(true);
$spreadsheet = $reader->load($fileName);
$sheet = $spreadsheet->getActiveSheet();

header('Content-Type: text/html');

for ($row = 2; $row <= 52; $row++) {
    $momEnglish = trim($sheet->getCellByColumnAndRow(1, $row));
    $momSoundFile = trim($sheet->getCellByColumnAndRow(2, $row));
    $momThai = trim($sheet->getCellByColumnAndRow(3, $row));
    $childEnglish = trim($sheet->getCellByColumnAndRow(4, $row));
    $childSoundFile = trim($sheet->getCellByColumnAndRow(5, $row));
    $childThai = trim($sheet->getCellByColumnAndRow(6, $row));
    $category = strtoupper(trim($sheet->getCellByColumnAndRow(7, $row)));

    if ($momEnglish === '') continue;

    echo "sentenceList.add(new Sentence(<br>\n
        0,<br>\n
        \"{$momEnglish}\",<br>\n
        \"{$momSoundFile}.mp3\",<br>\n
        \"{$momThai}\",<br>\n
        \"{$childEnglish}\",<br>\n
        \"{$childSoundFile}.mp3\",<br>\n
        \"{$childThai}\",<br>\n
        Sentence.CATEGORY_{$category}<br>\n
    ));<br>\n<br>\n";
}

/*

sentenceList.add(new Sentence(
        0,
        "It's been a tiring week. Let's go somewhere to rest and relax.",
        "เป็นสัปดาห์ที่น่าเบื่อ ไปหาที่ที่ผ่อนคลายเพื่อพักผ่อนกัน",
        "Yeah! Can we go to the beach? I love to swim.",
        "เย่! พวกเราไปทะเลกันไหมคะ หนูชอบว่ายน้ำค่ะแม่",
        Sentence.CATEGORY_HOLIDAY
));

 */