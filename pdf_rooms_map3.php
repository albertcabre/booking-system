<?php
require_once('fpdf16/fpdf.php');
require_once('connection.php');
require_once('functions.php');

$pdf=new FPDF();
$pdf->AliasNbPages();
//$pdf->AddPage();
//SetMargins(float left, float top [, float right])
//$pdf->SetMargins(0.5, 0.5, 0.5);
$pdf->SetMargins(0, 0, 0);
$pdf->SetAutoPageBreak(1, 0);
// Create a A3 page size.
$pdf->AddPage('L','A3');
$pdf->SetFont('Arial','',5);
$border=1;

// Header
$pdf->SetFillColor(255,255,255);
$pdf->Cell(4.4,2.8,"",$border,0,'',true);

$today=time(); //today
$strToday=date("d/m/Y", $today);
//$first_day=time()-(7 * 24 * 60 * 60); // one less week
//$first_day=time()-(285 * 24 * 60 * 60);
$first_day=time()-(24 * 60 * 60); // today
if ($request["small"]==1) { $first_day=time(); }

if (isset($request["first_day"])) {
	$first_day=$request["first_day"];
}
if ($request["when"]=="week") {
	// 7 dias * 24 hours * 60 minutes * 60 seconds.
	$first_day=$first_day-(7 * 24 * 60 * 60);
} elseif ($request["when"]=="month") {
	// 4 weeks * 7 days * 24 hours * 60 minutes * 60 seconds.
	$first_day=$first_day-(4 * 7 * 24 * 60 * 60);
}

$to=120;
if ($request["small"]==1) { $to=30; }

for ($i=0; $i<$to; $i++) {
	$the_day=$first_day+($i * 24 * 60 * 60);
	$strColumnDay=date("d/m/Y", $the_day);

	//$pdf->SetFillColor(47,47,94);
	$pdf->SetFillColor(192,192,192);
    if ($strToday==$strColumnDay) { $pdf->SetFillColor(255,0,0); }

	/*
	if ($strToday==$strColumnDay) {
		echo "<span style='color:FFFFFF'>";
	}
	echo date("d/m", $the_day)."<br>".date("Y", $the_day);
	if ($strToday==$strColumnDay) {
		echo "</span>";
	}
	*/

	//$pdf->Cell(6,3,date("d/m", $the_day)."\n".date("Y", $the_day),$border,0,'',true);
	$pdf->SetFont('Arial','',4);
	$pdf->Cell(4.4,2.8,date("d/m", $the_day),$border,0,'C',true);

}

$pdf->SetFont('Arial','',5);
$pdf->Ln();

$r=mysqli_query($link, "SELECT * FROM rooms as a LEFT JOIN room_type as b on a.room_type_id=b.room_type_id ORDER BY room");
$class="file1";
while ($data=mysqli_fetch_assoc($r)) {

	$pdf->SetFillColor(192,192,192);	//#999999
	$pdf->Cell(4.4,2.8,$data[room],$border,0,'C',true);

	$last_resident_id=0;

	for ($i=0; $i<$to; $i++) {
		$the_day=$first_day+($i * 24 * 60 * 60);
		$the_day_to_search=date("Y/m/d", $the_day);

		$q2="SELECT room_id, resident_id FROM bookings
		WHERE (arrival <= '{$the_day_to_search}' AND '{$the_day_to_search}' < planned_departure) AND room_id={$data[room_id]} ";
		//AND status='accepted'
		//ver("q",$q);

		$r2=mysqli_query($link, $q2);
		if (!mysqli_num_rows($r2)) {
			// FREE ROOM
			$color="#00CC33";

			//$pdf->SetFillColor(0,204,51);
			//$pdf->SetFillColor(100,255,100);
			$pdf->SetFillColor(255,255,255);

			$resident_name="";
			$resident_name_surname="Free";
			$room="";
			$surname="";
		} else {
			// BUSY ROOM
			$resident_id=mysqli_result($r2,0,"resident_id");
			if ($last_resident_id!=$resident_id) {
				$last_resident_id=$resident_id;
			}

			$room_id=mysqli_result($r2,0,"room_id");
			$q="SELECT name, surname, color FROM residents WHERE resident_id=$resident_id";
			$r3=mysqli_query($link, $q);
			/*
			$resident_name=@mysqli_result($r3,0,"name");
			$resident_name_surname=@mysqli_result($r3,0,"name")." ".@mysqli_result($r3,0,"surname");
			$surname=@mysqli_result($r3,0,"surname");
			*/
			$color=mysqli_result($r3,0,"color");

			$surname=mysqli_result($r3,0,"surname");
			$name=mysqli_result($r3,0,"name");
			if ($surname=="") {
				$surname=mysqli_result($r3,0,"name");
				$name="";
			}

			$arrRGBcolors=html2rgb($color);
			$pdf->SetFillColor($arrRGBcolors[0],$arrRGBcolors[1],$arrRGBcolors[2]);
		}

		$q="SELECT room FROM rooms WHERE room_id={$data[room_id]}";
		$r3=mysqli_query($link, $q);
		$room=mysqli_result($r3,0,"room");

		//$pdf->Cell(6,3,substr($surname,0,5),$border,0,'C',true);
		if (!mysqli_num_rows($r2)) {
			// FREE ROOM
			$pdf->Cell(4.4,2.8,$room,$border,0,'C',true);
		} else {
			// BUSY ROOM
			$pdf->Cell(4.4,2.8,substr($surname,0,3),$border,0,'C',true);
		}
	}

	$pdf->Ln();
}

$pdf->Output();