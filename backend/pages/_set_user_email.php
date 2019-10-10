<?php
echo 'ICEHR';
exit();

require_once '../vendor/autoload.php';
require_once '../include/head_php.inc';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$inputFileName = './icehr_user.xls';
$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
/**  Create a new Reader of the type defined in $inputFileType  **/
//$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
$spreadsheet = $reader->load($inputFileName);

$activeSheet = $spreadsheet->getActiveSheet();

define('COL_NAME', 3);
define('COL_EMAIL', 4);
?>
    <table>
        <?php
        $i = 1;
        for ($row = 2; $row <= 51; $row++) {
            $name = $activeSheet->getCellByColumnAndRow(COL_NAME, $row);
            $email = trim($activeSheet->getCellByColumnAndRow(COL_EMAIL, $row));
            $nameParts = preg_split('/\s+/', $name);
            $firstName = trim($nameParts[0]);
            $lastName = trim($nameParts[1]);

            //$sql = "SELECT * FROM user WHERE first_name = '$firstName' AND last_name = '$lastName' AND status = 'active'";
            $sql = "UPDATE user 
                    SET email = '$email' 
                    WHERE first_name = '$firstName' AND last_name = '$lastName' AND status = 'active'";
            $result = $db->query($sql);
            $numRows = $db->affected_rows;
            ?>
            <tr>
                <td align="right"><?= $i++; ?></td>
                <td><?= $firstName; ?></td>
                <td><?= $lastName; ?></td>
                <td><?= $email; ?></td>
                <td><?= $numRows; ?></td>
            </tr>
            <?php

        }
        ?>
    </table>
<?php
require_once '../include/foot_php.inc';

function generateSheet($memberList, $memberType, $sheet)
{
    global $title;

    $sheet->setTitle(ucfirst($memberType));
    if (empty($memberList)) {
        $sheet->setCellValueByColumnAndRow(1, 1, 'ไม่มีข้อมูล')->getStyleByColumnAndRow(1, 1)->getAlignment()->setVertical('top')->setHorizontal('left');
        return;
    }

    if ($memberType === MEMBER_TYPE_ORGANIZATION) {
        $sheet->mergeCells("A1:K1");
    } else {
        $sheet->mergeCells("A1:H1");
    }
    $sheet->setCellValueByColumnAndRow(1, 1, $title)->getStyleByColumnAndRow(1, 1)->getAlignment()->setVertical('top')->setHorizontal('left');

    $sheet->freezePane('A3');

    $sheet->getStyle("A1:Z1")->getFont()->setBold(true);
    $sheet->getStyle("A2:Z2")->getFont()->setBold(true);

    $sheet->getColumnDimension('A')->setAutoSize(true);
    $sheet->getColumnDimension('B')->setAutoSize(true);
    $sheet->getColumnDimension('C')->setAutoSize(true);
    $sheet->getColumnDimension('D')->setAutoSize(true);
    $sheet->getColumnDimension('E')->setAutoSize(true);
    $sheet->getColumnDimension('F')->setAutoSize(true);
    $sheet->getColumnDimension('G')->setAutoSize(true);
    $sheet->getColumnDimension('H')->setAutoSize(true);

    if ($memberType === MEMBER_TYPE_ORGANIZATION) {
        $sheet->getColumnDimension('I')->setAutoSize(true);
        $sheet->getColumnDimension('J')->setAutoSize(true);
        $sheet->getColumnDimension('K')->setAutoSize(true);
    }

    $row = 2;
    $sheet->setCellValueByColumnAndRow(1, $row, 'ลำดับ')->getStyleByColumnAndRow(1, $row)->getAlignment()->setHorizontal('center');;
    $sheet->setCellValueByColumnAndRow(2, $row, 'ชื่อ-นามสกุล')->getStyleByColumnAndRow(2, $row)->getAlignment()->setHorizontal('center');
    $sheet->setCellValueByColumnAndRow(3, $row, 'เบอร์โทร')->getStyleByColumnAndRow(3, $row)->getAlignment()->setHorizontal('center');
    $sheet->setCellValueByColumnAndRow(4, $row, 'อีเมล')->getStyleByColumnAndRow(4, $row)->getAlignment()->setHorizontal('center');
    $sheet->setCellValueByColumnAndRow(5, $row, 'ตำแหน่งงาน')->getStyleByColumnAndRow(5, $row)->getAlignment()->setHorizontal('center');
    $sheet->setCellValueByColumnAndRow(6, $row, 'ชื่อหน่วยงาน')->getStyleByColumnAndRow(6, $row)->getAlignment()->setHorizontal('center');
    $sheet->setCellValueByColumnAndRow(7, $row, 'ประเภทหน่วยงาน')->getStyleByColumnAndRow(7, $row)->getAlignment()->setHorizontal('center');
    if ($memberType === MEMBER_TYPE_ORGANIZATION) {
        $sheet->setCellValueByColumnAndRow(8, $row, 'ที่อยู่หน่วยงาน')->getStyleByColumnAndRow(8, $row)->getAlignment()->setHorizontal('center');
        $sheet->setCellValueByColumnAndRow(9, $row, 'จังหวัด')->getStyleByColumnAndRow(9, $row)->getAlignment()->setHorizontal('center');
        $sheet->setCellValueByColumnAndRow(10, $row, 'เบอร์โทรหน่วยงาน')->getStyleByColumnAndRow(10, $row)->getAlignment()->setHorizontal('center');
        $sheet->setCellValueByColumnAndRow(11, $row, 'วันที่สมัคร')->getStyleByColumnAndRow(11, $row)->getAlignment()->setHorizontal('center');
    } else {
        $sheet->setCellValueByColumnAndRow(8, $row, 'วันที่สมัคร')->getStyleByColumnAndRow(8, $row)->getAlignment()->setHorizontal('center');
    }
    $sheet->getRowDimension($row)->setRowHeight(-1); // set auto height

    define('START_ROW', 3);
    $row = START_ROW;
    foreach ($memberList as $member) {
        $sheet->setCellValueByColumnAndRow(1, $row, ($row - START_ROW) + 1)->getStyleByColumnAndRow(1, $row)->getAlignment()->setVertical('top')->setHorizontal('center');

        $displayName = "{$member['title']}{$member['first_name']} {$member['last_name']}";
        $sheet->setCellValueByColumnAndRow(2, $row, $displayName)->getStyleByColumnAndRow(2, $row)->getAlignment()->setVertical('top');

        $sheet->setCellValueByColumnAndRow(3, $row, $member['phone'])->getStyleByColumnAndRow(3, $row)->getAlignment()->setVertical('top')->setHorizontal('left');
        $sheet->setCellValueByColumnAndRow(4, $row, $member['email'])->getStyleByColumnAndRow(4, $row)->getAlignment()->setVertical('top');
        $sheet->setCellValueByColumnAndRow(5, $row, $member['job_position'])->getStyleByColumnAndRow(5, $row)->getAlignment()->setVertical('top');
        $sheet->setCellValueByColumnAndRow(6, $row, $member['organization_name'])->getStyleByColumnAndRow(6, $row)->getAlignment()->setVertical('top');
        $sheet->setCellValueByColumnAndRow(7, $row, is_null($member['organization_type_name']) ? $member['organization_type_custom'] : $member['organization_type_name'])->getStyleByColumnAndRow(7, $row)->getAlignment()->setVertical('top');

        if ($memberType === MEMBER_TYPE_ORGANIZATION) {
            $province = $member['province'];
            $isBangkok = false;
            if (mb_substr($province, 0, 4) == 'กรุง' || substr($province, 0, 2) == 'กท') {
                $isBangkok = true;
            }

            if ($isBangkok) {
                $displayAddress = "{$member['address']} แขวง{$member['sub_district']} เขต{$member['district']} กรุงเทพฯ {$member['postal_code']}";
            } else {
                $displayAddress = "{$member['address']} ต.{$member['sub_district']} อ.{$member['district']} จ.{$member['province']} {$member['postal_code']}";
            }
            $sheet->setCellValueByColumnAndRow(8, $row, $displayAddress)->getStyleByColumnAndRow(8, $row)->getAlignment()->setVertical('top');

            $sheet->setCellValueByColumnAndRow(9, $row, $isBangkok ? 'กรุงเทพฯ' : $province)->getStyleByColumnAndRow(9, $row)->getAlignment()->setVertical('top');
            $sheet->setCellValueByColumnAndRow(10, $row, $member['organization_phone'])->getStyleByColumnAndRow(10, $row)->getAlignment()->setVertical('top')->setHorizontal('left');
            $sheet->setCellValueByColumnAndRow(11, $row, $member['created_at'])->getStyleByColumnAndRow(11, $row)->getAlignment()->setVertical('top')->setHorizontal('center');
        } else {
            $sheet->setCellValueByColumnAndRow(8, $row, $member['created_at'])->getStyleByColumnAndRow(8, $row)->getAlignment()->setVertical('top')->setHorizontal('center');
        }

        $sheet->getRowDimension($row)->setRowHeight(-1); // set auto height
        $row++;
    }
}