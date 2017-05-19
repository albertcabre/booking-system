<?php
require_once('fpdf16/fpdf.php');
require_once('connection.php');

validate_user();

$pdf=new FPDF();
$pdf->AliasNbPages();
$pdf->SetTopMargin(4);
$pdf->SetLeftMargin(4);
$pdf->SetAutoPageBreak(1, 0.5);
$pdf->SetDisplayMode(100);
$pdf->SetAutoPageBreak(true,7);
$pdf->AddPage();
//$pdf->AddPage('L');
$pdf->SetDisplayMode(110, "single");
$pdf->SetTitle("Netherhall House");
$border=1;

$pdf->SetFont('Arial','B',10);
$pdf->SetFillColor(255,255,255);

if ($request[name]!="") {
	$r=mysql_query("SELECT * FROM residents ".
	               "LEFT JOIN countries on residents.country_id = countries.country_id ".
	               "WHERE name LIKE '%{$request[name]}%' OR surname LIKE '%{$request[name]}%'");
} else {
	$today=date("Y",time())."-".date("m",time())."-".date("d",time());
	if (!isset($request[academic_year]) || $request[academic_year]=="current") {
		// >= 28 days = 4 weeks
		//$condition_search=" AND bookings.arrival <= '$today' AND bookings.departure >= '$today' AND DATEDIFF(bookings.departure,bookings.arrival) >= 28  ";
		// 06-Feb-2010 - They want to see all the residents in the current residents list. So I remove the condition about days.
		$condition_search=" AND bookings.arrival <= '$today' AND bookings.planned_departure >= '$today' ";
	} elseif ($request[academic_year]=="short") {
		// < 28 days = 4 weeks
		$condition_search=" AND bookings.arrival <= '$today' AND bookings.planned_departure >= '$today' AND DATEDIFF(bookings.planned_departure,bookings.arrival) < 28 ";
	} else {
	 	//$condition_search=" AND bookings.arrival>='{$request[academic_year]}-09-01' AND DATEDIFF(bookings.departure,bookings.arrival) >= 28 ";
		//$condition_search=" AND bookings.planned_departure>'{$request[academic_year]}-10-01' AND DATEDIFF(bookings.planned_departure,bookings.arrival) >= 28 ";
		$condition_search=" AND bookings.planned_departure>'{$request[academic_year]}-10-01' ".
						  " AND bookings.planned_departure<'".($request[academic_year]+1)."-10-01' ".
		                  " AND DATEDIFF(bookings.departure,bookings.arrival) > 28 ";
	}

	$type="";
	if (isset($request[type])) {
		$type=$request[type];
    }

	if ($request[sort_by]=="name") {
		$sort="ORDER BY name $type";
	}
	elseif ($request[sort_by]=="surname") {
		$sort="ORDER BY surname $type";
	}
	elseif ($request[sort_by]=="arrival") {
		$sort="ORDER BY barrival $type";
	}
	elseif ($request[sort_by]=="departure") {
		$sort="ORDER BY bdeparture $type";
	}
	elseif ($request[sort_by]=="room") {
		$sort="ORDER BY room $type";
	} else {
		$sort="ORDER BY surname, name DESC";
	}

	if ($type=="DESC") {
		$new_type="ASC";
    } else {
		$new_type="DESC";
    }

	$q="SELECT *, residents.telephone AS residentstelephone, bookings.arrival AS barrival, bookings.planned_departure AS bdeparture FROM bookings ".
	   "LEFT JOIN residents ON bookings.resident_id=residents.resident_id ".
	   "LEFT JOIN rooms ON bookings.room_id=rooms.room_id ".
	   "LEFT JOIN countries ON residents.country_id=countries.country_id ".
	   "WHERE (bookings.status='accepted' OR bookings.status='finished') ".
	   $condition_search.
	   "GROUP BY NAME, surname $sort";
	//ver("q",$q);
	$r=mysql_query($q);
}
if ($request[academic_year]=="" || $request[academic_year]=="current") {
	$header="Current residents (".mysql_num_rows($r).")";
} elseif ($request[academic_year]=="short") {
	$header="Short Stages (".mysql_num_rows($r).")";
} else {
	$yearto=$request[academic_year]+1;
	$header="Residents {$request[academic_year]} - $yearto (".mysql_num_rows($r).")";
}

$pdf->Cell(180,5,"Netherhall House",0,0,'',true);

$pdf->Ln();

$pdf->SetFont('Arial','B',10);
$pdf->Cell(100,5,$header,0,0,'',true);

$pdf->SetFont('Arial','B',8);
$pdf->Cell(103,5,date("d/m/Y"),0,0,'R',true);

$pdf->Ln(6);

$pdf->SetFont('Arial','',7);

// Header
$pdf->SetFillColor(192,192,192);
$pdf->Cell(39,4,"Surname",$border,0,'',true);
$pdf->Cell(30,4,"Name",$border,0,'',true);
$pdf->Cell(9,4,"Room",$border,0,'',true);
$pdf->Cell(29,4,"Telephone",$border,0,'',true);
$pdf->Cell(26,4,"UK Phone",$border,0,'',true);
$pdf->Cell(40,4,"Email",$border,0,'',true);
$pdf->Cell(29,4,"Nationality",$border,0,'',true);
$pdf->Ln();
$pdf->SetFillColor(255,255,255);

$i=0;
while ($arrData=mysql_fetch_assoc($r)) {
    $arrData = iso_8859_1_converter($arrData);
	$i++;

	$surname=$arrData[surname];
	$name=$arrData[name];
	if ($surname=="") {
		$surname=$arrData[name];
		$name="";
	}

	$pdf->Cell(39,4,$surname,$border);
	$pdf->Cell(30,4,$name,$border);
	$pdf->Cell(9,4,$arrData[room],$border);
	$pdf->Cell(29,4,$arrData[residentstelephone],$border);
	$pdf->Cell(26,4,$arrData[ukphone],$border);
	$pdf->Cell(40,4,substr($arrData[email],0,26),$border);
    $pdf->Cell(29,4,$arrData[nationality],$border);
	$pdf->Ln();
}
$pdf->Output();
