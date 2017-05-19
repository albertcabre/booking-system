<?php
require_once('connection.php');
require_once('functions.php');

validate_user();

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=netherhall-residents.csv');

$fp = fopen('php://output', 'w');

if ($request[name] != "") {
    $r = mysqli_query($link, "SELECT * FROM residents " .
        "LEFT JOIN countries on residents.country_id = countries.country_id " .
        "WHERE name LIKE '%{$request[name]}%' OR surname LIKE '%{$request[name]}%'");
} else {
    $today = date("Y", time()) . "-" . date("m", time()) . "-" . date("d", time());
    if (!isset($request[academic_year]) || $request[academic_year] == "current") {
        // >= 28 days = 4 weeks
        //$condition_search=" AND bookings.arrival <= '$today' AND bookings.departure >= '$today' AND DATEDIFF(bookings.departure,bookings.arrival) >= 28  ";
        // 06-Feb-2010 - They want to see all the residents in the current residents list. So I remove the condition about days.
        $condition_search = " AND bookings.arrival <= '$today' AND bookings.planned_departure >= '$today' ";
    } elseif ($request[academic_year] == "short") {
        // < 28 days = 4 weeks
        $condition_search = " AND bookings.arrival <= '$today' AND bookings.planned_departure >= '$today' AND DATEDIFF(bookings.planned_departure,bookings.arrival) < 28 ";
    } else {
        //$condition_search=" AND bookings.arrival>='{$request[academic_year]}-09-01' AND DATEDIFF(bookings.departure,bookings.arrival) >= 28 ";
        //$condition_search=" AND bookings.planned_departure>'{$request[academic_year]}-10-01' AND DATEDIFF(bookings.planned_departure,bookings.arrival) >= 28 ";
        $condition_search = " AND bookings.planned_departure>'{$request[academic_year]}-10-01' " .
            " AND bookings.planned_departure<'" . ($request[academic_year] + 1) . "-10-01' " .
            " AND DATEDIFF(bookings.departure,bookings.arrival) > 28 ";
    }

    $type = "";
    if (isset($request[type])) {
        $type = $request[type];
    }

    if ($request[sort_by] == "name") {
        $sort = "ORDER BY name $type";
    } elseif ($request[sort_by] == "surname") {
        $sort = "ORDER BY surname $type";
    } elseif ($request[sort_by] == "arrival") {
        $sort = "ORDER BY barrival $type";
    } elseif ($request[sort_by] == "departure") {
        $sort = "ORDER BY bdeparture $type";
    } elseif ($request[sort_by] == "room") {
        $sort = "ORDER BY room $type";
    } else {
        $sort = "ORDER BY surname, name DESC";
    }

    if ($type == "DESC") {
        $new_type = "ASC";
    } else {
        $new_type = "DESC";
    }

    $q = "SELECT *, residents.telephone AS residentstelephone, bookings.arrival AS barrival, bookings.planned_departure AS bdeparture FROM bookings " .
        "LEFT JOIN residents ON bookings.resident_id=residents.resident_id " .
        "LEFT JOIN rooms ON bookings.room_id=rooms.room_id " .
        "LEFT JOIN countries ON residents.country_id=countries.country_id " .
        "WHERE (bookings.status='accepted' OR bookings.status='finished') " .
        $condition_search .
        "GROUP BY NAME, surname $sort";
    //ver("q",$q);
    $r = mysqli_query($link, $q);
}
if ($request[academic_year] == "" || $request[academic_year] == "current") {
    $header = "Current residents (" . mysqli_num_rows($r) . ")";
} elseif ($request[academic_year] == "short") {
    $header = "Short Stages (" . mysqli_num_rows($r) . ")";
} else {
    $yearto = $request[academic_year] + 1;
    $header = "Residents {$request[academic_year]} - $yearto (" . mysqli_num_rows($r) . ")";
}

$header = array('name', 'surname', 'arrival', 'departure', 'room', 'telephone', 'UK phone', 'nationality');
fputcsv($fp, $header);

while ($arrData = mysqli_fetch_array($r)) {
    $arrData2 = array();
    $arrData['barrival'] = ($arrData['barrival'] == "0000-00-00 00:00:00" ? "" : $arrData['barrival']);
    $arrData['bdeparture'] = ($arrData['bdeparture'] == "0000-00-00 00:00:00" ? "" : $arrData['bdeparture']);

    $arrData2['name'] = $arrData['name'];
    $arrData2['surname'] = $arrData['surname'];
    $arrData2['barrival'] = $arrData['barrival'];
    $arrData2['bdeparture'] = $arrData['bdeparture'];
    $arrData2['room'] = $arrData['room'];
    $arrData2['telephone'] = $arrData['residentstelephone'];
    $arrData2['ukphone'] = $arrData['ukphone'];
    $arrData2['country'] = $arrData['nationality'];
    fputcsv($fp, $arrData2);
}

fclose($fp);
