<?php
require_once('fpdf16/fpdf.php');
require_once('connection.php');
require_once('functions.php');

validate_user();

$pdf = new FPDF();
$pdf->AliasNbPages();
$pdf->SetTopMargin(4);
$pdf->SetLeftMargin(6);
$pdf->SetAutoPageBreak(1, 0.5);
$pdf->SetDisplayMode(100);
$pdf->SetAutoPageBreak(true, 7);
$pdf->AddPage();
//$pdf->AddPage('L');
$pdf->SetDisplayMode(110, "single");
$pdf->SetTitle("Netherhall House");
$border = 1;

$pdf->SetFont('Arial', 'B', 10);
$pdf->SetFillColor(255, 255, 255);

//------------

$pdf->Cell(180, 5, "Netherhall House", 0, 0, '', true);

$pdf->Ln();

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(95, 5, $header, 0, 0, '', true);

$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(103, 5, date("d/m/Y"), 0, 0, 'R', true);

$pdf->SetFont('Arial', '', 7);

//The purpose of this page should only be to keep track of the residents who are currently in Netherhall.
$today = date('Y-m-d');
$q = "SELECT residents.resident_id, NAME, surname ".
     "FROM residents LEFT JOIN bookings ON residents.resident_id = bookings.resident_id " .
     "WHERE bookings.status='accepted' AND bookings.done=0 AND bookings.arrival <= '$today' " .
     "GROUP BY residents.resident_id ORDER BY surname, NAME";
$r = mysqli_query($link, $q);
if (mysqli_num_rows($r)) {
    $count = 0;
    while ($arrInfo = mysqli_fetch_assoc($r)) {
        $count++;
        if ($count == 30) { $count = 1; }
        $total_days = 0;
        $total_total_rent = 0;
        $total_laundry = 0;
        $total_hc = 0;
        $total_printing = 0;
        $total_extra = 0;
        $total_due = 0;
        $total_received = 0;
        $total_outstanding = 0;
        // For each residents get it's bookings.
        $q = "SELECT * FROM residents LEFT JOIN bookings ON residents.resident_id = bookings.resident_id " .
            "WHERE bookings.status='accepted' AND residents.resident_id={$arrInfo[resident_id]} " .
            "ORDER BY NAME, surname, bookings.arrival";
        $r2 = mysqli_query($link, $q);
        while ($arrData = mysqli_fetch_assoc($r2)) {
            $arrData = iso_8859_1_converter($arrData);
            if ($count == 1) {
                // Header
                $pdf->SetFillColor(192, 192, 192);
                $name = "";
                if ($arrData[surname] != "") {
                    $name = $arrData[surname] . ", ";
                }
                $name.= $arrData[name];
                $pdf->Ln();
                $pdf->SetFont('Arial', 'B', 8);
                $pdf->SetFont('Arial', 'B', 7);
                $pdf->Ln();

                printHeader($pdf, $border);

                $pdf->Ln();
                $pdf->SetFont('Arial', '', 7);
            }
            $date_from = mostrar_fecha($arrData['arrival']);
            $date_to = mostrar_fecha($arrData['planned_departure']);

            $days = subtract_dates($date_from, $date_to);

            $total_rent_pre = $days * ($arrData['weekly_rate'] / 7);
            $total_rent = round($total_rent_pre, 2);
            $due = $total_rent + $arrData['laundry'] + $arrData['hc'] + $arrData['printing'] + $arrData['extra'];
            $invoice_number = "NO BILL";
            $outstanding = $due - $arrData['received'];
            if ($arrData['invoice_number'] != "") {
                $invoice_number = $arrData['invoice_number'];
            }
            $id = $arrData['booking_id'];

            // Load values from table
            $ra = $arrData['weekly_rate'];
            $la = $arrData['laundry'];
            $hc = $arrData['hc'];
            $pr = $arrData['printing'];
            $ex = $arrData['extra'];
            $de = $arrData['deposit'];
            $re = $arrData['received'];
            $bi = $arrData['billed'];

            // If there are values in the request then we put the values of the request.
            if (isset($request['ra_' . $id])) {
                $ra = $request['ra_' . $id];
            }
            if (isset($request['la_' . $id])) {
                $la = $request['la_' . $id];
            }
            if (isset($request['hc_' . $id])) {
                $hc = $request['hc_' . $id];
            }
            if (isset($request['pr_' . $id])) {
                $pr = $request['pr_' . $id];
            }
            if (isset($request['ex_' . $id])) {
                $ex = $request['ex_' . $id];
            }
            if (isset($request['de_' . $id])) {
                $de = $request['de_' . $id];
            }
            if (isset($request['re_' . $id])) {
                $re = $request['re_' . $id];
            }
            if (isset($request['bi_' . $id])) {
                $bi = $request['bi_' . $id];
                if ($bi == 'Yes') {
                    $bi = 1;
                }
            } else {
                $bi = 0;
            }

            $total_days = $total_days + $days;
            $total_total_rent = $total_total_rent + $total_rent;
            $total_laundry = $total_laundry + $la;
            $total_hc + $total_hc + $hc;
            $total_printing = $total_printing + $pr;
            $total_extra = $total_extra + $ex;
            $total_due = $total_due + $due;
            $total_received = $total_received + $re;
            $total_outstanding = $total_outstanding + $outstanding;

            $grand_total_rent = $grand_total_rent + $total_rent;
            $grand_total_laundry = $grand_total_laundry + $la;
            $grand_total_hc = $grand_total_hc + $hc;
            $grand_total_printing = $grand_total_printing + $pr;
            $grand_total_extra = $grand_total_extra + $ex;
            $grand_total_due = $grand_total_due + $due;
            $grand_total_received = $grand_total_received + $re;
            $grand_total_outstanding = $grand_total_outstanding + $outstanding;
        }

        if (mysqli_num_rows($r2) > 1) {
            $pdf->Cell(50, 4, $count." ".$name, $border);
            $pdf->Cell(8, 4, $total_days, $border, 0, 'R');
            $pdf->Cell(14, 4, number_format($total_total_rent, 2, ".", ","), $border, 0, 'R');
            $pdf->Cell(13, 4, number_format($total_laundry, 2, ".", ","), $border, 0, 'R');
            $pdf->Cell(13, 4, number_format($total_hc, 2, ".", ","), $border, 0, 'R');
            $pdf->Cell(13, 4, number_format($total_printing, 2, ".", ","), $border, 0, 'R');
            $pdf->Cell(13, 4, number_format($total_extra, 2, ".", ","), $border, 0, 'R');
            $pdf->Cell(14, 4, number_format($total_due, 2, ".", ","), $border, 0, 'R');
            $pdf->Cell(14, 4, number_format($total_received, 2, ".", ","), $border, 0, 'R');
            $pdf->Cell(14, 4, number_format($total_outstanding, 2, ".", ","), $border, 0, 'R');
            $pdf->Ln();
        }
    }

    $pdf->Ln();
    $pdf->Cell(61, 4, "", 0);
    $pdf->Cell(61, 4, "GRAND TOTAL", 0);
    $pdf->Ln();
    $pdf->Cell(61, 4, "", 0);
    $pdf->Cell(14, 4, "Total", $border, 0, 'R', true);
    $pdf->Cell(13, 4, "Laundry", $border, 0, 'R', true);
    $pdf->Cell(13, 4, "HC", $border, 0, 'R', true);
    $pdf->Cell(13, 4, "Printing", $border, 0, 'R', true);
    $pdf->Cell(13, 4, "Extra", $border, 0, 'R', true);
    $pdf->Cell(14, 4, "Due", $border, 0, 'R', true);
    $pdf->Cell(14, 4, "Received", $border, 0, 'R', true);
    $pdf->Cell(14, 4, "Outstand.", $border, 0, 'R', true);

    $pdf->Ln();
    $pdf->Cell(61, 4, "", 0);
    $pdf->Cell(14, 4, number_format($grand_total_rent, 2, ".", ","), $border, 0, 'R');
    $pdf->Cell(13, 4, number_format($grand_total_laundry, 2, ".", ","), $border, 0, 'R');
    $pdf->Cell(13, 4, number_format($grand_total_hc, 2, ".", ","), $border, 0, 'R');
    $pdf->Cell(13, 4, number_format($grand_total_printing, 2, ".", ","), $border, 0, 'R');
    $pdf->Cell(13, 4, number_format($grand_total_extra, 2, ".", ","), $border, 0, 'R');
    $pdf->Cell(14, 4, number_format($grand_total_due, 2, ".", ","), $border, 0, 'R');
    $pdf->Cell(14, 4, number_format($grand_total_received, 2, ".", ","), $border, 0, 'R');
    $pdf->Cell(14, 4, number_format($grand_total_outstanding, 2, ".", ","), $border, 0, 'R');
}
$pdf->Output();

function getRoomName($room_id) {
    global $link;
    $room = "";
    if ($room_id) {
        $r = mysqli_query($link, "SELECT * FROM rooms WHERE room_id=$room_id");
        if (mysqli_numrows($r)) {
            $room = mysqli_result($r, 0, "room");
        }
    }
    return $room;
}

function printHeader($pdf, $border) {
    $pdf->Cell(50, 4, "Name", $border, 0, 'L', true);
    $pdf->Cell(8, 4, "Days", $border, 0, 'R', true);
    $pdf->Cell(14, 4, "Total", $border, 0, 'R', true);
    $pdf->Cell(13, 4, "Laundry", $border, 0, 'R', true);
    $pdf->Cell(13, 4, "HC", $border, 0, 'R', true);
    $pdf->Cell(13, 4, "Printing", $border, 0, 'R', true);
    $pdf->Cell(13, 4, "Extra", $border, 0, 'R', true);
    $pdf->Cell(14, 4, "Due", $border, 0, 'R', true);
    $pdf->Cell(14, 4, "Received", $border, 0, 'R', true);
    $pdf->Cell(14, 4, "Outstand.", $border, 0, 'R', true);
}