<?php
require_once('fpdf16/fpdf.php');
require_once('fpdf16/tables.php');
require_once('connection.php');
require_once('functions.php');

validate_user();

$resident_id = $request[resident_id];
$r=mysqli_query($link, "SELECT * FROM residents LEFT JOIN countries ON residents.country_id=countries.country_id WHERE resident_id=$resident_id");
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

$high=6;
$width1=180;
$width2=$width1/2;
$width3=$width1/3;
$width4=$width1/4;
/*
$pdf->SetFont('Arial','B',12);
$pdf->Cell(180,$high,"OUTSTANDING SUMMARY",0,0,'C',true);
$pdf->Ln();
$pdf->Ln();
*/
$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Arial','B',18);
$pdf->Cell(180,$high,$arrResident[name]." ".$arrResident[surname],0,0,'',true);
$pdf->SetFont('Arial','',8);
$pdf->Ln();
$pdf->Ln();
$pdf->SetFillColor(230,230,230);
$pdf->Cell(16,$high,"Arrival",$border,0,'',true);
$pdf->Cell(16,$high,"Departure",$border,0,'',true);
$pdf->Cell(9,$high,"Days",$border,0,'R',true);
$pdf->Cell(10,$high,"Room",$border,0,'',true);
$pdf->Cell(13,$high,"W. Rate",$border,0,'R',true);
$pdf->Cell(15,$high,"Total",$border,0,'R',true);
$pdf->Cell(13,$high,"Laundry",$border,0,'R',true);
$pdf->Cell(13,$high,"HC",$border,0,'R',true);
$pdf->Cell(13,$high,"Printing",$border,0,'R',true);
$pdf->Cell(13,$high,"Extra",$border,0,'R',true);
$pdf->Cell(15,$high,"Due",$border,0,'R',true);
$pdf->Cell(15,$high,"Received",$border,0,'R',true);
$pdf->Cell(18,$high,"Outstanding",$border,0,'R',true);
$pdf->Ln();

//################################################################################################################

$total_outstanding=0;
if ($resident_id) {
	$r=mysqli_query($link, "SELECT * FROM bookings WHERE resident_id=$resident_id AND (status='' OR status IS NULL OR status='accepted') ORDER BY arrival DESC");
	$num_of_accounts=mysqli_num_rows($r);
	$accounts=0;
	$total_outstanding=0;
	while ($arrAccomodation=mysqli_fetch_assoc($r)) {
		$accounts++;
		//ver_array("arrAccomodation",$arrAccomodation);
		$date_from = mostrar_fecha($arrAccomodation['arrival']);
		$date_to   = mostrar_fecha($arrAccomodation['planned_departure']);

		$days=subtract_dates($date_from, $date_to);

		// Search the name of the room
		if ($arrAccomodation[room_id]) {
			$r2=mysqli_query($link, "SELECT * FROM rooms WHERE room_id={$arrAccomodation[room_id]}");
			$room = "";
			if (mysqli_numrows($r2)) {
				$room=mysqli_result($r2,0,"room");
            }
		}

		$total_rent_pre = $days * ($arrAccomodation['weekly_rate']/7);
		$total_rent = round($total_rent_pre,2);
		$due = $total_rent + $arrAccomodation['laundry'] + $arrAccomodation['hc'] + $arrAccomodation['printing'] + $arrAccomodation['extra'];
		$invoice_number = "NO BILL";
		$outstanding = $due - $arrAccomodation['deposit'] - $arrAccomodation['received'];
		$total_outstanding = $total_outstanding + $outstanding;
        if ($arrAccomodation['invoice_number']!="") { $invoice_number = $arrAccomodation['invoice_number']; }

		$pdf->SetFillColor(255,255,255);
		$pdf->Cell(16,$high,$date_from,$border,0,'',true);
		$pdf->Cell(16,$high,$date_to,$border,0,'',true);
		$pdf->Cell(9,$high,$days,$border,0,'R',true);
		$pdf->Cell(10,$high,$room,$border,0,'',true);
		$pdf->Cell(13,$high,number_format($arrAccomodation['weekly_rate'],2,".",","),$border,0,'R',true);
		$pdf->Cell(15,$high,number_format($total_rent,2,".",","),$border,0,'R',true);
		$pdf->Cell(13,$high,number_format($arrAccomodation['laundry'],2,".",","),$border,0,'R',true);
		$pdf->Cell(13,$high,number_format($arrAccomodation['hc'],2,".",","),$border,0,'R',true);
		$pdf->Cell(13,$high,number_format($arrAccomodation['printing'],2,".",","),$border,0,'R',true);
		$pdf->Cell(13,$high,number_format($arrAccomodation['extra'],2,".",","),$border,0,'R',true);
		$pdf->Cell(15,$high,number_format($due,2,".",","),$border,0,'R',true);
		$pdf->Cell(15,$high,number_format($arrAccomodation['received'],2,".",","),$border,0,'R',true);
		$pdf->Cell(18,$high,number_format($outstanding,2,".",","),$border,0,'R',true);
		$pdf->Ln();
	}
	if ($accounts > 1) {
		$pdf->Cell(161,$high,"",$border,0,'',true);
		$pdf->Cell(18,$high,number_format($total_outstanding,2,".",","),$border,0,'R',true);
	}
}

$pdf->Output();
