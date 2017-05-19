<?php
require_once('fpdf16/fpdf.php');
require_once('connection.php');

validate_user();

$pdf=new FPDF();
$pdf->AliasNbPages();
$pdf->SetTopMargin(20);
$pdf->SetLeftMargin(45);
$pdf->SetAutoPageBreak(1, 0.5);
$pdf->SetDisplayMode(100);
$pdf->SetAutoPageBreak(true,10);
$pdf->AddPage();
//$pdf->AddPage('L');
$pdf->SetDisplayMode(110, "single");
$pdf->SetTitle("Netherhall House");
$border=0;

$pdf->SetFont('Arial','B',10);
$pdf->SetFillColor(255,255,255);

if ($request[name]!="") {
	$r=mysqli_query($link, "SELECT * FROM residents LEFT JOIN countries on residents.country_id = countries.country_id ".
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
		// Too mach query in resident list (residents_list.php).
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

	$q="SELECT *, bookings.arrival AS barrival, bookings.planned_departure AS bdeparture FROM bookings
	LEFT JOIN residents ON bookings.resident_id=residents.resident_id
	LEFT JOIN rooms ON bookings.room_id=rooms.room_id
	LEFT JOIN countries ON residents.country_id=countries.country_id
	WHERE (bookings.status='accepted' OR bookings.status='finished')
	$condition_search
	GROUP BY NAME, surname
	$sort";
	//echo $q."<br>";
	$r=mysqli_query($link, $q);
}
if ($request[academic_year]=="" || $request[academic_year]=="current") {
	$header="Current residents (".mysqli_num_rows($r).")";
} elseif ($request[academic_year]=="short") {
	$header="Short Stages (".mysqli_num_rows($r).")";
} else {
	$yearto=$request[academic_year]+1;
	$header="Residents {$request[academic_year]} - $yearto (".mysqli_num_rows($r).")";
}

$pdf->SetFont('Arial','B',12);
$pdf->Cell(109,5,"Netherhall House",0,0,'',true);

$pdf->SetFont('Arial','B',8);
$pdf->Cell(10,5,date("d/m/Y"),0,0,'R',true);

$pdf->Ln(10);
$pdf->SetFont('Arial','',9);
$pdf->SetFillColor(255,255,255);

$i=0;
while ($arrData=mysqli_fetch_assoc($r)) {
	$i++;

	$pdf->Cell(18,4,"Surname",$border);
	$pdf->Cell(35,4,$arrData[surname],$border);
	$pdf->Ln();
	$pdf->Cell(18,4,"Name",$border);
	$pdf->Cell(35,4,$arrData[name],$border);
	$pdf->Ln();
	$pdf->Cell(18,4,"Nationality",$border);
	$pdf->Cell(35,4,$arrData[nationality],$border);
	$pdf->Ln();
	$pdf->Cell(18,4,"Subject",$border);
	$pdf->Cell(35,4,$arrData[subject],$border);
	$pdf->Ln();
	$pdf->Cell(18,4,"College",$border);
	$pdf->Cell(35,4,$arrData[college],$border);

    if ($request[extra] == 1) {
        $pdf->Ln();
        $pdf->Cell(18,4,"UK phone",$border);
        $pdf->Cell(35,4,$arrData[ukphone],$border);
        $pdf->Ln();
        $pdf->Cell(18,4,"EMail",$border);
        $pdf->Cell(35,4,$arrData[email],$border);
    } else {
        $pdf->Ln();
    }

	$pdf->Ln();
	if ($arrData[picture]!="" && file_exists("../residentsnh/".$arrData[picture])) {
		$path_parts = pathinfo($arrData[picture]);
		if (strtoupper($path_parts[extension]) == 'GIF' ||
            strtoupper($path_parts[extension]) == 'JPG' ||
            strtoupper($path_parts[extension]) == 'JPEG') {
			$pdf->Image("../residentsnh/".$arrData[picture], $pdf->GetX()+100, $pdf->GetY()-25, 18);
		}
		else {
			echo "Error, ".$arrData[name]." ".$arrData[surname]." has it's picture in a wrong format ".$path_parts[extension]."! Please replace or delete this photo to generate this PDF.<br>";
		}
	} else {
		$pdf->Image("imgs/no_picture.png", $pdf->GetX()+100, $pdf->GetY()-25, 18);
	}
	$pdf->Line(46, $pdf->GetY(), 163, $pdf->GetY());
	$pdf->Ln();
    if ($request[extra] != 1) {
        $pdf->Ln();
    }
	if ($i==8) {
		$pdf->AddPage();
		$i=0;
	}
}
$pdf->Output();