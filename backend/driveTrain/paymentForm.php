<?php
define("_VALID_ACCESS", 1);

//include("configuration.php");
//require_once("includes/ms_com.php");
//require_once("includes/datetime.class2.php");
require('../dist/fpdf/fpdf2.php');

//$dbObj = new DBConn;

//$today = date("Y-m-d");
//$applyID = mosGetParam($_FORM, 'A', '');

//$datePay = DT::DateTimeShortFormat2($today, 0, 1, "Th");

function convert($number)
{
    $txtnum1 = array('�ٹ��', '˹��', '�ͧ', '���', '���', '���', 'ˡ', '��', 'Ỵ', '���', '�Ժ');
    $txtnum2 = array('', '�Ժ', '����', '�ѹ', '����', '�ʹ', '��ҹ', '�Ժ', '����', '�ѹ', '����', '�ʹ', '��ҹ');
    $number = str_replace(",", "", $number);
    $number = str_replace(" ", "", $number);
    $number = str_replace("�ҷ", "", $number);
    $number = explode(".", $number);

    if (sizeof($number) > 2) {
        return 'Error: �ըش�ȹ����ҡ���� 1 ���';
    }

    $length = strlen($number[0]);
    $convert = '';

    for ($i = 0; $i < $length; $i++) {
        $n = substr($number[0], $i, 1);
        if ($n != 0) {
            if ($i == ($length - 1) AND $n == 1) {
                $convert .= '���';
            } elseif ($i == ($length - 2) AND $n == 2) {
                $convert .= '���';
            } elseif ($i == ($length - 2) AND $n == 1) {
                $convert .= '';
            } else {
                $convert .= $txtnum1[$n];
            }
            $convert .= $txtnum2[$length - $i - 1];
        }
    }

    $convert .= '�ҷ';
    if ($number[1] == '0' OR $number[1] == '00' OR $number[1] == '') {
        $convert .= '��ǹ';
    } else {
        $length = strlen($number[1]);
        for ($i = 0; $i < $length; $i++) {
            $n = substr($number[1], $i, 1);
            if ($n != 0) {
                if ($i == ($length - 1) AND $n == 1) {
                    $convert .= '���';
                } elseif ($i == ($length - 2) AND $n == 2) {
                    $convert .= '���';
                } elseif ($i == ($length - 2) AND $n == 1) {
                    $convert .= '';
                } else {
                    $convert .= $txtnum1[$n];
                }
                $convert .= $txtnum2[$length - $i - 1];
            }
        }
        $convert .= 'ʵҧ��';
    }

    return $convert;
}

//Create new pdf file
$pdf = new FPDF('P', 'mm', array(190, 200));
$pdf->SetTopMargin(0);

$pdf->AddFont('AngsanaNew', '', 'angsana.php');
$pdf->AddFont('AngsanaNew', 'B', 'angsanab.php');

//Disable automatic page break
$pdf->SetAutoPageBreak(false);

//Add first page
$pdf->AddPage();

//$qryTrain = "SELECT * FROM drive_person_apply INNER JOIN drive_prefix ON drive_person_apply.prefix_id=drive_prefix.id WHERE drive_person_apply.id='$applyID' ";
//$resTrain = $dbObj->execQuery($qryTrain);
//$row = $dbObj->fetchObject($resTrain);
//$name = iconv("UTF-8", "TIS-620", $row->prefix_th . $row->fname . "  " . $row->lname);
//$address = $row->address;
//$moo = ($row->moo == "") ? "" : " ���� " . $row->moo;
//$soi = ($row->soi == "") ? "" : " ���" . iconv("UTF-8", "TIS-620", $row->soi);
//$road = ($row->road == "") ? "" : " ���" . iconv("UTF-8", "TIS-620", $row->road);
//$subDistrict = ($row->sub_district == "") ? "" : " �ǧ/�Ӻ�" . iconv("UTF-8", "TIS-620", $row->sub_district);
//$district = ($row->district == "") ? "" : " ࢵ/�����" . iconv("UTF-8", "TIS-620", $row->district);
//$province = ($row->province == "") ? "" : " �ѧ��Ѵ" . iconv("UTF-8", "TIS-620", $row->province);
//$aa = $address . $moo . $soi . $road . $subDistrict . $district . $province;
//$courseTrain = $row->course_train;
//$course = ($courseTrain == '1') ? '��ѡ�ٵá��ͺ������Ѻ�����Ѻ�͹حҵ�Ѻö' : '��ѡ�ٵá��ͺ������Ѻ���͵�������͹حҵ�Ѻö';
//$cost = ($courseTrain == '1') ? '500' : '200';

$pdf->SetFont('AngsanaNew', '', 14);
$pdf->SetXY(80, 22);
//$pdf->SetXY(80,18);
$pdf->Cell(50, 6, '16 �.�. 62', 0, 0, 'L', 0);
$pdf->SetXY(76, 27);
//$pdf->SetXY(76,24);
$pdf->Cell(50, 6, '��¾�������� �����ԨԵ�', 0, 0, 'L', 0);
$pdf->SetXY(60, 34);
//$pdf->SetXY(60,30);
$pdf->MultiCell(120, 5, '11/13 ����ó ���� �͹������� �.���ǧ���ҹ 59 �Ҵ��� ��بѡ� ��ا෾� 10900', 0, 'L');
$pdf->SetXY(34, 55);
//$pdf->SetXY(40,53);
$pdf->Cell(13, 6, '1', 0, 0, 'C', 0);
$pdf->Cell(110, 6, '���ŧ����¹ ' . '��ѡ�ٵá��ͺ������Ѻ�����Ѻ�͹حҵ�Ѻö', 0, 0, 'L', 0);
$pdf->Cell(25, 6, number_format('500', 2), 0, 0, 'R', 0);
$pdf->SetXY(63, 148);
//$pdf->SetXY(53,135);
$pdf->Cell(100, 8, '(' . '������ºҷ��ǹ' . ')', 0, 0, 'L', 0);
$pdf->Cell(25, 8, number_format('500', 2), 0, 0, 'R', 0);

//Send file
$pdf->Output();

//$dbObj->disconn();
//unset($dbObj);
?>
