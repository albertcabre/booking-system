<?php
require_once('fpdf16/fpdf.php');
require_once('connection.php');
require_once('functions.php');

validate_user();

$pdf=new FPDF();
$pdf->AliasNbPages();
$pdf->SetTopMargin(4);
$pdf->SetLeftMargin(4);
$pdf->SetAutoPageBreak(1, 0.5);
$pdf->SetDisplayMode(100);
$pdf->SetAutoPageBreak(true,7);
//$pdf->AddPage();
$pdf->AddPage('L');
$pdf->SetDisplayMode(110, "single");
$pdf->SetTitle("Netherhall House");
$border=1;

$pdf->SetFont('Arial','B',10);
$pdf->SetFillColor(255,255,255);

/*
$q="SELECT *, bookings.arrival AS barrival, bookings.departure AS bdeparture FROM bookings
LEFT JOIN residents ON bookings.resident_id=residents.resident_id
LEFT JOIN rooms ON bookings.room_id=rooms.room_id
LEFT JOIN countries ON residents.country_id=countries.country_id
WHERE bookings.status='accepted'
$condition_search
GROUP BY NAME, surname
ORDER BY name, surname";
*/
$today=date("Y",time())."-".date("m",time())."-".date("d",time());
$q="SELECT r.*, c.country, b.room_id, b.arrival, b.departure, b.booking_date, b.room_id, rooms.* FROM residents r
LEFT JOIN bookings b ON r.resident_id=b.resident_id
LEFT JOIN rooms ON b.room_id=rooms.room_id
LEFT JOIN countries c ON r.country_id = c.country_id WHERE b.status = 'accepted'
AND b.arrival > '$today' ORDER BY b.arrival, r.name, r.surname";

$r=mysqli_query($q);

$header="Accepted residents (".mysqli_num_rows($r).")";

$pdf->Cell(180,5,"Netherhall House",0,0,'',true);

$pdf->Ln();

$pdf->SetFont('Arial','B',10);
$pdf->Cell(100,5,$header,0,0,'',true);

$pdf->SetFont('Arial','B',8);
$pdf->Cell(183,5,date("d/m/Y"),0,0,'R',true);

$pdf->Ln(6);

$pdf->SetFont('Arial','',7);

// Header
$pdf->SetFillColor(192,192,192);
$pdf->Cell(35,4,"Surname",$border,0,'',true);
$pdf->Cell(30,4,"Name",$border,0,'',true);
$pdf->Cell(18,4,"Arrival",$border,0,'',true);
$pdf->Cell(18,4,"Departure",$border,0,'',true);
$pdf->Cell(9,4,"Room",$border,0,'',true);
$pdf->Cell(9,4,"Tele",$border,0,'',true);
$pdf->Cell(25,4,"City",$border,0,'',true);
$pdf->Cell(25,4,"Country",$border,0,'',true);
$pdf->Cell(57,4,"College",$border,0,'',true);
$pdf->Cell(57,4,"Subject",$border,0,'',true);
$pdf->Ln();
$pdf->SetFillColor(255,255,255);

$i=0;
while ($arrData=mysqli_fetch_assoc($r)) {
	$i++;

	$surname=$arrData[surname];
	$name=$arrData[name];
	if ($surname=="") {
		$surname=$arrData[name];
		$name="";
	}

	$pdf->Cell(35,4,$surname,$border);
	$pdf->Cell(30,4,$name,$border);
	$pdf->Cell(18,4,mostrar_fecha(substr($arrData[arrival],0,10)),$border);
	$pdf->Cell(18,4,mostrar_fecha(substr($arrData[departure],0,10)),$border);
	$pdf->Cell(9,4,$arrData[room],$border);
	$pdf->Cell(9,4,$arrData[telephone],$border);
	$pdf->Cell(25,4,$arrData[city],$border);
	$pdf->Cell(25,4,$arrData[country],$border);
	$pdf->Cell(57,4,substr($arrData[college],0,56),$border);
	$pdf->Cell(57,4,substr($arrData[subject],0,56),$border);
	$pdf->Ln();
}
$pdf->Output();