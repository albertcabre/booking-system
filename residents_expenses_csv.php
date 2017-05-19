<?php

require_once('connection.php');
require_once('functions.php');

validate_user();

$arrClasses = array();
$arrResidents = array();
$arrResidents[] = array("Name", "Outstanding");
//The purpose of this page should only be to keep track of the residents who are currently in Netherhall.
$today = date('Y-m-d');
$r = mysqli_query($link, "SELECT residents.resident_id, NAME, surname " .
    "FROM residents LEFT JOIN bookings ON residents.resident_id = bookings.resident_id " .
    "WHERE bookings.status='accepted' AND bookings.done=0 AND bookings.arrival <= '$today' " .
    "GROUP BY residents.resident_id ORDER BY surname, NAME");
if (mysqli_num_rows($r)) {

    while ($arrInfo = mysqli_fetch_assoc($r)) {

        $total_outstanding = 0;

        $r2 = mysqli_query($link, "SELECT * FROM residents LEFT JOIN bookings ON residents.resident_id = bookings.resident_id " .
            "WHERE bookings.status='accepted' AND residents.resident_id={$arrInfo[resident_id]} " .
            "ORDER BY NAME, surname, bookings.arrival");
        while ($arrData = mysqli_fetch_assoc($r2)) {
            $date_from = mostrar_fecha($arrData['arrival']);
            $date_to = mostrar_fecha($arrData['planned_departure']);

            $days = subtract_dates($date_from, $date_to);

            $total_rent = $days * ($arrData['weekly_rate'] / 7);
            $total_rent = round($total_rent, 2);
            $due = $total_rent + $arrData['laundry'] + $arrData['hc'] + $arrData['printing'] + $arrData['extra'];
            //$outstanding = $due - $arrData['deposit'] - $arrData['received'];
            $outstanding = $due - $arrData['received'];
            $outstanding = round($outstanding, 2);

            $name = "";
            if ($arrData[surname] != "") {
                $name = $arrData[surname] . ", ";
            }
            $name .= $arrData[name];
            $name = utf8_encode($name);

            if (mysqli_num_rows($r2) < 2) {
                $outstanding = number_format($outstanding, 2, ".", ",");
                $arrResidents[] = array($name, $outstanding);
            }

            $total_outstanding = $total_outstanding + $outstanding;
            $total_outstanding = round($total_outstanding, 2);
        }
        if (mysqli_num_rows($r2) > 1) {
            $total_outstanding = number_format($total_outstanding, 2, ".", ",");
            $arrResidents[] = array($name, $total_outstanding);
        }
    }
}
header("Pragma: public");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header('Content-type: text/csv');
header("Content-Disposition: attachment;filename=netherhall.csv");
header("Content-Transfer-Encoding: binary");

$fp = fopen('php://output', 'a');
foreach ($arrResidents as $fields) {
    fputcsv($fp, $fields);
}
fclose($fp);
