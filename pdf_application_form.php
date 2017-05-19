<?php
require_once('fpdf16/fpdf.php');
require_once('fpdf16/tables.php');
require_once('connection.php');
require_once('functions.php');

validate_user();

$r=mysqli_query($link, "SELECT * FROM residents "
    . "LEFT JOIN countries ON residents.country_id=countries.country_id "
    . "WHERE resident_id={$request[resident_id]}");
$arrResident=mysqli_fetch_assoc($r);

//$pdf=new FPDF();
$pdf=new PDF_MC_Table();
$pdf->AliasNbPages();

$pdf->SetTopMargin(10);
$pdf->SetLeftMargin(20);
$pdf->SetAutoPageBreak(1, 20);

$pdf->AddPage();
//$pdf->AddPage('L');
$pdf->SetDisplayMode(100, "single");
$pdf->SetTitle("Netherhall House");

$border=1;

/*
$pdf->SetFont('Arial','B',14);
$pdf->SetFillColor(255,255,255);
$pdf->Cell(180,10,"Netherhall House",$border,0,'',true);
$pdf->Ln();

$today=date("Y",time())."-".date("m",time())."-".date("d",time());
$pdf->SetFont('Arial','',8);
$pdf->Cell(180,3,$today,$border,0,'C',true);
$pdf->Ln();
$pdf->Ln();
*/
$high=6;
$width1=180;
$width2=$width1/2;
$width3=$width1/3;
$width4=$width1/4;

$pdf->SetFillColor(255,255,255);

$pdf->SetFont('Arial','B',18);
$pdf->Cell(180,$high,"Netherhall House",0,0,'C',true);
$pdf->Ln();

$pdf->SetFont('Arial','',10);
//$pdf->SetWidths(array(40,100,40));
//$pdf->SetAligns(array('','C',''));
//$pdf->SetBorders(array(0,0,0));
$pdf->SetX=70;
$pdf->SetY=15;
$cabecera="Nutley Terrace, London NW3 5SA\nTel: 020 7435 8888 (Director); 020 7472 5720 (Residents)\nFax: 020 7472 5721\nE-Mail: director@nh.netherhall.org.uk\nWeb: www.nh.netherhall.org.uk";
$pdf->MultiCell(180,4,$cabecera,0,'C');
//$pdf->Row(array("",$cabecera,""));
$pdf->Ln();

$pdf->SetAligns(array('','',''));
$pdf->SetBorders(array(1,1,1));

$pdf->Image("imgs/shield.gif",18,7,26);

if ($arrResident[picture]!="" && file_exists("../residentsnh/".$arrResident[picture])) {
	$pdf->Image("../residentsnh/".$arrResident[picture],180,10,20);
} else {
	$pdf->Image("imgs/no_picture.png",180,10,20);
}

$pdf->SetFont('Arial','B',12);
$pdf->Cell(180,$high,"APPLICATION FOR ADMISSION",0,0,'C',true);
$pdf->Ln();
$pdf->Ln();

$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Arial','B',18);
$pdf->Cell(180,$high,$arrResident[name]." ".$arrResident[surname],0,0,'',true);
$pdf->Ln();
$pdf->Ln();

$pdf->SetFont('Arial','',11);
$pdf->SetFillColor(230,230,230);
$pdf->Cell(30,$high,"First names",$border,0,'',true);
$pdf->SetFillColor(255,255,255);
$pdf->Cell(60,$high,$arrResident[name],$border,0,'',true);
$pdf->SetFillColor(230,230,230);
$pdf->Cell(30,$high,"Surname",$border,0,'',true);
$pdf->SetFillColor(255,255,255);
$pdf->Cell(60,$high,$arrResident[surname],$border,0,'',true);
$pdf->Ln();

$pdf->SetFillColor(230,230,230);
$pdf->Cell(30,$high,"Home address",$border,0,'',true);
$pdf->SetFillColor(255,255,255);
$home_address = $arrResident[address_line1];
if ($arrResident[address_line2]!="") { $home_address .= " - ".$arrResident[address_line2]; }
/*
if ($arrResident[postal_code]!="") $home_address .= " - ".$arrResident[postal_code];
if ($arrResident[city]!="") $home_address .= " - ".$arrResident[city];
if ($arrResident[country]!="") $home_address .= " - ".$arrResident[country];
*/
$pdf->Cell(150,$high,$home_address,$border,0,'',true);
$pdf->Ln();

$pdf->SetFillColor(230,230,230);
$pdf->Cell(30,$high,"Postal Code",$border,0,'',true);
$pdf->SetFillColor(255,255,255);
$pdf->Cell(150,$high,$arrResident[postal_code],$border,0,'',true);
$pdf->Ln();

$pdf->SetFillColor(230,230,230);
$pdf->Cell(30,$high,"City",$border,0,'',true);
$pdf->SetFillColor(255,255,255);
$pdf->Cell(150,$high,$arrResident[city],$border,0,'',true);
$pdf->Ln();

$pdf->SetFillColor(230,230,230);
$pdf->Cell(30,$high,"County/province",$border,0,'',true);
$pdf->SetFillColor(255,255,255);
$pdf->Cell(150,$high,$arrResident[county],$border,0,'',true);
$pdf->Ln();

$pdf->SetFillColor(230,230,230);
$pdf->Cell(30,$high,"Country",$border,0,'',true);
$pdf->SetFillColor(255,255,255);
$pdf->Cell(150,$high,$arrResident[country],$border,0,'',true);
$pdf->Ln();
$pdf->Ln();

$pdf->SetFillColor(230,230,230);
$pdf->Cell(30,$high,"Nationality",$border,0,'',true);
$pdf->SetFillColor(255,255,255);
$pdf->Cell(70,$high,$arrResident[nationality],$border,0,'',true);
$pdf->SetFillColor(230,230,230);
$pdf->Cell(25,$high,"Religion",$border,0,'',true);
$pdf->SetFillColor(255,255,255);
$pdf->Cell(55,$high,$arrResident[r],$border,0,'',true);
$pdf->Ln();

$pdf->SetFillColor(230,230,230);
$pdf->Cell(30,$high,"Marital status",$border,0,'',true);
$pdf->SetFillColor(255,255,255);
$pdf->Cell(70,$high,$arrResident[marital_status],$border,0,'',true);
$pdf->SetFillColor(230,230,230);
$pdf->Cell(25,$high,"Date of birth",$border,0,'',true);
$pdf->SetFillColor(255,255,255);
$pdf->Cell(55,$high,mostrar_fecha($arrResident[date_of_birth]),$border,0,'',true);
$pdf->SetFillColor(230,230,230);
$pdf->Ln();

$pdf->SetFillColor(230,230,230);
$pdf->Cell(30,$high,"Telephone 1",$border,0,'',true);
$pdf->SetFillColor(255,255,255);
$pdf->Cell(70,$high,$arrResident[telephone],$border,0,'',true);
$pdf->SetFillColor(230,230,230);
$pdf->Cell(25,$high,"Telephone 2",$border,0,'',true);
$pdf->SetFillColor(255,255,255);
$pdf->Cell(55,$high,$arrResident[mobile],$border,0,'',true);
$pdf->Ln();

$pdf->SetFillColor(230,230,230);
$pdf->Cell(30,$high,"E-Mail",$border,0,'',true);
$pdf->SetFillColor(255,255,255);
$pdf->Cell(150,$high,$arrResident[email],$border,0,'',true);

$pdf->Ln();
$pdf->Ln();

$pdf->SetFillColor(230,230,230);
$pdf->Cell(55,$high,"College",$border,0,'',true);
$pdf->SetFillColor(255,255,255);
$pdf->Cell(125,$high,$arrResident[college],$border,0,'',true);
$pdf->Ln();

$pdf->SetFillColor(230,230,230);
$pdf->Cell(55,$high,"Subject",$border,0,'',true);
$pdf->SetFillColor(255,255,255);
$pdf->Cell(125,$high,$arrResident[subject],$border,0,'',true);
$pdf->Ln();

$pdf->SetFillColor(230,230,230);
$pdf->Cell(55,$high,"Course (BA, MSc, PhD, etc)",$border,0,'',true);
$pdf->SetFillColor(255,255,255);
$pdf->Cell(125,$high,$arrResident[course],$border,0,'',true);
$pdf->Ln();

$pdf->SetFillColor(230,230,230);
$pdf->Cell(55,$high,"Academic Year",$border,0,'',true);
$pdf->SetFillColor(255,255,255);
$pdf->Cell(125,$high,$arrResident[academic_year],$border,0,'',true);
$pdf->Ln();

$pdf->SetFillColor(230,230,230);
$pdf->Cell(55,$high,"Accommodation Dates",$border,0,'',true);
$pdf->SetFillColor(255,255,255);
$arr=mostrar_fecha($arrResident[arrival]);
$dep=mostrar_fecha($arrResident[departure]);
$pdf->Cell(63,$high,"From: ".$arr,$border,0,'',true);
$pdf->Cell(62,$high,"To: ".$dep,$border,0,'',true);
$pdf->Ln();
$pdf->Ln();

$pdf->SetFillColor(230,230,230);
$pdf->Cell($width2,$high,"Schools attended (with dates)",$border,0,'',true);
$pdf->Cell($width2,$high,"School examinations passed (with grades)",$border,0,'',true);
$pdf->Ln();
$pdf->SetWidths(array($width2,$width2));
$pdf->Row(array($arrResident[school_attended],$arrResident[school_examinations]));

$pdf->SetFillColor(230,230,230);
$pdf->Cell($width2,$high,"Universities attended",$border,0,'',true);
$pdf->Cell($width2,$high,"Qualifications obtained (with grades)",$border,0,'',true);
$pdf->Ln();
$pdf->SetFillColor(255,255,255);
$pdf->SetWidths(array($width2,$width2));
$pdf->Row(array($arrResident[universities_attended],$arrResident[qualifications_obtained]));
$pdf->Ln();

$pdf->SetFillColor(230,230,230);
$pdf->Cell($width2,$high,"Scholarship(s) held",$border,0,'',true);
$pdf->Cell($width2,$high,"Occupation, if any, since leaving school",$border,0,'',true);
$pdf->Ln();
$pdf->SetFillColor(255,255,255);
$pdf->SetWidths(array($width2,$width2));
$pdf->Row(array($arrResident[scholarship_help],$arrResident[occupation]));
$pdf->Ln();

$pdf->SetFillColor(230,230,230);
$pdf->Cell(180,$high,"Positions of responsibility held at school and/or college",$border,0,'',true);
$pdf->Ln();
$pdf->SetFillColor(255,255,255);
$pdf->MultiCell(180,$high,$arrResident[positions],$border,'',true);
$pdf->Ln();

$pdf->SetFillColor(230,230,230);
$pdf->Cell(60,$high,"Name of parent(s) or Guardian",$border,0,'',true);
$pdf->SetFillColor(255,255,255);
$pdf->Cell(120,$high,$arrResident[name_parent],$border,0,'',true);
$pdf->SetFillColor(230,230,230);
$pdf->Ln();
$pdf->Cell(25,$high,"Occupation",$border,0,'',true);
$pdf->SetFillColor(255,255,255);
$pdf->Cell(155,$high,$arrResident[occupation_parent],$border,0,'',true);
$pdf->Ln();
$pdf->Ln();

$pdf->SetFillColor(230,230,230);
$pdf->Cell(180,$high,"Referee 1 (not a family member)",$border,0,'',true);
$pdf->Ln();

$pdf->SetFillColor(230,230,230);
$pdf->Cell(22,$high,"Name",$border,0,'',true);
$pdf->SetFillColor(255,255,255);
$pdf->Cell(158,$high,$arrResident[reference1_name],$border,0,'',true);
$pdf->Ln();

$pdf->SetFillColor(230,230,230);
$pdf->Cell(22,$high,"Address",$border,0,'',true);
$pdf->SetFillColor(255,255,255);
//$pdf->Cell(158,$high,$arrResident[reference1_address],$border,0,'',true);
$pdf->MultiCell(158,$high,$arrResident[reference1_address],$border,'',true);

$pdf->SetFillColor(230,230,230);
$pdf->Cell(22,$high,"Telephone",$border,0,'',true);
$pdf->SetFillColor(255,255,255);
$pdf->Cell(158,$high,$arrResident[reference1_telephone],$border,0,'',true);
$pdf->Ln();

$pdf->SetFillColor(230,230,230);
$pdf->Cell(22,$high,"Email",$border,0,'',true);
$pdf->SetFillColor(255,255,255);
$pdf->Cell(158,$high,$arrResident[reference1_email],$border,0,'',true);
$pdf->Ln();

$pdf->SetFillColor(230,230,230);
$pdf->Cell(65,$high,"Referee's relationship to applicant",$border,0,'',true);
$pdf->SetFillColor(255,255,255);
$pdf->Cell(115,$high,$arrResident[reference1_relationship],$border,0,'',true);
$pdf->Ln();
$pdf->Ln();

//---------------

$pdf->SetFillColor(230,230,230);
$pdf->Cell(180,$high,"Referee 2 (not a family member)",$border,0,'',true);
$pdf->Ln();

$pdf->SetFillColor(230,230,230);
$pdf->Cell(22,$high,"Name",$border,0,'',true);
$pdf->SetFillColor(255,255,255);
$pdf->Cell(158,$high,$arrResident[reference2_name],$border,0,'',true);
$pdf->Ln();

$pdf->SetFillColor(230,230,230);
$pdf->Cell(22,$high,"Address",$border,0,'',true);
$pdf->SetFillColor(255,255,255);
//$pdf->Cell(158,$high,$arrResident[reference2_address],$border,0,'',true);
$pdf->MultiCell(158,$high,$arrResident[reference2_address],$border,'',true);

$pdf->SetFillColor(230,230,230);
$pdf->Cell(22,$high,"Telephone",$border,0,'',true);
$pdf->SetFillColor(255,255,255);
$pdf->Cell(158,$high,$arrResident[reference2_telephone],$border,0,'',true);
$pdf->Ln();

$pdf->SetFillColor(230,230,230);
$pdf->Cell(22,$high,"Email",$border,0,'',true);
$pdf->SetFillColor(255,255,255);
$pdf->Cell(158,$high,$arrResident[reference2_email],$border,0,'',true);
$pdf->Ln();

$pdf->SetFillColor(230,230,230);
$pdf->Cell(65,$high,"Referee's relationship to applicant",$border,0,'',true);
$pdf->SetFillColor(255,255,255);
$pdf->Cell(115,$high,$arrResident[reference2_relationship],$border,0,'',true);
$pdf->Ln();
$pdf->Ln();

$pdf->SetFillColor(230,230,230);
$pdf->Cell(180,$high,"State any serious illness",$border,0,'',true);
$pdf->Ln();
$pdf->SetFillColor(255,255,255);
$pdf->MultiCell(180,$high,$arrResident[serious_illness],$border,'',true);
$pdf->Ln();

$pdf->SetFillColor(230,230,230);
$pdf->Cell(180,$high,"Special dietary requirements",$border,0,'',true);
$pdf->Ln();
$pdf->SetFillColor(255,255,255);
$pdf->MultiCell(180,$high,$arrResident[special_dietary],$border,'',true);
$pdf->Ln();

$pdf->SetFillColor(230,230,230);
$pdf->Cell(180,$high,"Intended profession of occupation, if known",$border,0,'',true);
$pdf->Ln();
$pdf->SetFillColor(255,255,255);
$pdf->MultiCell(180,$high,$arrResident[intended_profession],$border,'',true);
$pdf->Ln();

$pdf->SetFillColor(230,230,230);
$pdf->Cell(180,$high,"Intellectual or cultural interests",$border,0,'',true);
$pdf->Ln();
$pdf->SetFillColor(255,255,255);
$pdf->MultiCell(180,$high,$arrResident[interests],$border,'',true);
$pdf->Ln();

$pdf->SetFillColor(230,230,230);
$pdf->Cell(180,$high,"Sports played and/or outdoor interests",$border,0,'',true);
$pdf->Ln();
$pdf->SetFillColor(255,255,255);
$pdf->MultiCell(180,$high,$arrResident[sports],$border,'',true);
$pdf->Ln();

$pdf->SetFillColor(230,230,230);
$pdf->Cell(180,$high,"Any further information",$border,0,'',true);
$pdf->Ln();
$pdf->SetFillColor(255,255,255);
$pdf->MultiCell(180,$high,$arrResident[further_info],$border,'',true);
$pdf->Ln();
$pdf->SetFillColor(230,230,230);
$pdf->Cell(180,$high,"I was informed of Netherhall House by",$border,0,'',true);
$pdf->Ln();
$pdf->SetFillColor(255,255,255);
$pdf->MultiCell(180,$high,$arrResident[informed_by],$border,'',true);
/*
$xx=$pdf->GetX();
$yy=$pdf->GetX();
$pdf->Line($xx, $yy, $xx+30, $yy);

$pdf->Cell(180,$high,"x=".$x." y=".$y,$border,0,'',true);
*/
$pdf->Ln(30);
$pdf->Cell(90,$high,"Signature",0,0,'',true);
$pdf->Cell(90,$high,"Date",0,0,'',true);

$pdf->Output();
