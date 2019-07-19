<?php
require('fpdf.php');
$pdf=new FPDF();
$pdf->AddPage();
$pdf->AddFont('AngsanaNew','','angsana.php');
$pdf->AddFont('AngsanaNew','B','angsanab.php');
//???????????????????
$pdf->SetFont('AngsanaNew','B',16); 
//$pdf->SetFont('Arial','B',30);
$pdf->Cell(40,10,'ทดสอบ');
$pdf->Output();
?>