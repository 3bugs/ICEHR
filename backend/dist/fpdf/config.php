<?php
require('fpdf.php');

class FPDF_Config extends FPDF {

function Header() 
{ 
$this->SetFont('AngsanaNew','B',30); 
//Move to the right 
$this->Cell(80); 
//Title 
$this->Cell(20,10,'ใบสมัครอบรม / สัมมนา',0,0,'C'); 

$this->SetFont('AngsanaNew','B',20); 
$this->Cell(140,25,'เลขที่ .......................',0,0,'C'); 
//Line break 
$this->Ln(30); 
} 

//Page footer
function Footer()
{
	//Position at 1.5 cm from bottom
	$this->SetY(-15);
	//Arial italic 8
	$this->SetFont('Arial','I',8);
	//Page number
	//$this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
}

}
?>
