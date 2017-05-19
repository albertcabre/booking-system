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

//ver_array("REQUEST", $request);

$arrClasses = array();

//ver_array("arrClasses",$arrClasses);
//The purpose of this page should only be to keep track of the residents who are currently in Netherhall.
$today = date('Y-m-d');
$q = "SELECT residents.resident_id, NAME, surname FROM residents LEFT JOIN bookings ON residents.resident_id = bookings.resident_id " .
    "WHERE bookings.status='accepted' AND bookings.done=0 AND bookings.arrival <= '$today' " .
    "GROUP BY residents.resident_id ORDER BY surname, NAME";
$r = mysql_query($q);
if (mysql_num_rows($r)) {
    while ($arrInfo = mysql_fetch_assoc($r)) {
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
        $r2 = mysql_query($q);
        $count = 0;
        while ($arrData = mysql_fetch_assoc($r2)) {
            $arrData = iso_8859_1_converter($arrData);
            if ($count == 0) {
                // Header
                $pdf->SetFillColor(192, 192, 192);
                $name = "";
                if ($arrData[surname] != "") {
                    $name = $arrData[surname] . ", ";
                }
                $name.= $arrData[name];
                $pdf->Ln();
                $pdf->SetFont('Arial', 'B', 8);
                $pdf->Cell(50, 4, $name, 0);
                $pdf->SetFont('Arial', 'B', 7);
                $pdf->Ln();

                $pdf->Cell(15, 4, "Arrival", $border, 0, '', true);
                $pdf->Cell(15, 4, "Departure", $border, 0, '', true);
                $pdf->Cell(10, 4, "Room", $border, 0, '', true);
                $pdf->Cell(8, 4, "Days", $border, 0, 'R', true);
                $pdf->Cell(13, 4, "W. Rate", $border, 0, 'R', true);
                $pdf->Cell(14, 4, "Total", $border, 0, 'R', true);
                $pdf->Cell(13, 4, "Laundry", $border, 0, 'R', true);
                $pdf->Cell(13, 4, "HC", $border, 0, 'R', true);
                $pdf->Cell(13, 4, "Printing", $border, 0, 'R', true);
                $pdf->Cell(13, 4, "Extra", $border, 0, 'R', true);
                $pdf->Cell(14, 4, "Due", $border, 0, 'R', true);
                $pdf->Cell(14, 4, "Received", $border, 0, 'R', true);
                $pdf->Cell(14, 4, "Outstand.", $border, 0, 'R', true);
                $pdf->Cell(8, 4, "Billed", $border, 0, '', true);
                $pdf->Cell(20, 4, "Inv.Num.", $border, 0, '', true);
                $pdf->Ln();
                //$pdf->Line(6, $pdf->GetY(), 203, $pdf->GetY());
                $pdf->SetFont('Arial', '', 7);
            }
            $count++;
            $date_from = mostrar_fecha($arrData['arrival']);
            $date_to = mostrar_fecha($arrData['planned_departure']);

            $days = subtract_dates($date_from, $date_to);

            // Search the name of the room
            if ($arrData[room_id]) {
                $r3 = mysql_query("SELECT * FROM rooms WHERE room_id={$arrData[room_id]}");
                $room = "";
                if (mysql_numrows($r3)) {
                    $room = mysql_result($r3, 0, "room");
                }
            }

            //ver_array("arrData",$arrData);
            $total_rent_pre = $days * ($arrData['weekly_rate'] / 7);
            $total_rent = round($total_rent_pre, 2);
            $due = $total_rent + $arrData['laundry'] + $arrData['hc'] + $arrData['printing'] + $arrData['extra'];
            $invoice_number = "NO BILL";
            //$outstanding = $due - $arrData['deposit'] - $arrData['received'];
            $outstanding = $due - $arrData['received'];
            if ($arrData['invoice_number'] != "") {
                $invoice_number = $arrData['invoice_number'];
            }
            $id = $arrData['booking_id'];

            // If there are no errors, we put the default class name.
            if ($arrClasses[$id]['ra'] == "") {
                $arrClasses[$id]['ra'] = "input_small";
            }
            if ($arrClasses[$id]['la'] == "") {
                $arrClasses[$id]['la'] = "input_small";
            }
            if ($arrClasses[$id]['hc'] == "") {
                $arrClasses[$id]['hc'] = "input_small";
            }
            if ($arrClasses[$id]['pr'] == "") {
                $arrClasses[$id]['pr'] = "input_small";
            }
            if ($arrClasses[$id]['ex'] == "") {
                $arrClasses[$id]['ex'] = "input_small";
            }
            if ($arrClasses[$id]['de'] == "") {
                $arrClasses[$id]['de'] = "input_small";
            }
            if ($arrClasses[$id]['re'] == "") {
                $arrClasses[$id]['re'] = "input_small";
            }

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
            $pdf->Cell(15, 4, $date_from, $border);
            $pdf->Cell(15, 4, $date_to, $border);
            $pdf->Cell(10, 4, $room, $border);
            $pdf->Cell(8, 4, $days, $border, 0, 'R');
            $pdf->Cell(13, 4, $ra, $border, 0, 'R');
            $pdf->Cell(14, 4, number_format($total_rent, 2, ".", ","), $border, 0, 'R');
            $pdf->Cell(13, 4, $la, $border, 0, 'R');
            $pdf->Cell(13, 4, $hc, $border, 0, 'R');
            $pdf->Cell(13, 4, $pr, $border, 0, 'R');
            $pdf->Cell(13, 4, $ex, $border, 0, 'R');
            $pdf->Cell(14, 4, number_format($due, 2, ".", ","), $border, 0, 'R');
            $pdf->Cell(14, 4, $re, $border, 0, 'R');
            $color_text = "normal_text_verd";
            if ($outstanding > 0) {
                $color_text = "normal_text_red";
            }
            $pdf->Cell(14, 4, number_format($outstanding, 2, ".", ","), $border, 0, 'R');
            if ($bi) {
                $bill = "Yes";
            } else {
                $bill = "No";
            }
            $pdf->Cell(8, 4, $bill, $border);
            $pdf->Cell(20, 4, $arrData['invoice_number'], $border);
            $pdf->Ln();

            if ($arrData[comments] != "") {
                $pdf->Cell(15, 4, "Comments", $border, 0, '', true);
                $pdf->MultiCell(182, 4, $arrData[comments], $border);
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

        if (mysql_num_rows($r2) > 1) {
            //$pdf->Line(6, $pdf->GetY(), 203, $pdf->GetY());
            $pdf->Cell(15, 4, "", $border);
            $pdf->Cell(15, 4, "", $border);
            $pdf->Cell(10, 4, "", $border);
            $pdf->Cell(8, 4, $total_days, $border, 0, 'R');
            $pdf->Cell(13, 4, "", $border);
            $pdf->Cell(14, 4, number_format($total_total_rent, 2, ".", ","), $border, 0, 'R');
            $pdf->Cell(13, 4, number_format($total_laundry, 2, ".", ","), $border, 0, 'R');
            $pdf->Cell(13, 4, number_format($total_hc, 2, ".", ","), $border, 0, 'R');
            $pdf->Cell(13, 4, number_format($total_printing, 2, ".", ","), $border, 0, 'R');
            $pdf->Cell(13, 4, number_format($total_extra, 2, ".", ","), $border, 0, 'R');
            $pdf->Cell(14, 4, number_format($total_due, 2, ".", ","), $border, 0, 'R');
            $pdf->Cell(14, 4, number_format($total_received, 2, ".", ","), $border, 0, 'R');
            $color_text = "normal_text_verd";
            if ($outstanding > 0) {
                $color_text = "normal_text_red";
            }
            $pdf->Cell(14, 4, number_format($total_outstanding, 2, ".", ","), $border, 0, 'R');
            $pdf->Cell(8, 4, "", $border);
            $pdf->Cell(20, 4, "", $border);
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
