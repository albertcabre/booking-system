<?php
require_once('functions.php');
require_once('calendar.html');

validate_user();

$dateToSearch = change_format_date($request[date]);
if ($dateToSearch == "") {
    $dateToSearch = date("Y-m-d"); // today
}
?>
<link rel="stylesheet" href="/resources/demos/style.css">
<script>
$(function() {
    $("#datepicker").datepicker({ dateFormat: "dd/mm/yy" }).val();
});

function searchForThisDate() {
    var dateFromCalendar = document.getElementById('datepicker');

    if (dateFromCalendar.value !== "" && valFecha(dateFromCalendar)) {
        document.location.href = "admin.php?pagetoload=home.php&date=" + dateFromCalendar.value;
    } else {
        alert("Please enter a valid date dd/mm/yyyy");
    }

    return true;
}
</script>
<script src="js/funciones.js"></script>
<LINK href="css/netherhall.css" rel="stylesheet" type="text/css">
<table width="1200" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr>
        <td style="text-align: center" class="question">
        <br>
        <?php
        $d = substr($dateToSearch,8,2);
        $m = substr($dateToSearch,5,2);
        $y = substr($dateToSearch,0,4);
        echo date("l j F Y", mktime(0, 0, 0, $m, $d, $y));
        ?>
        <br>
        </td>
    </tr>
    <tr>
        <td style="vertical-align: top; align-content: center; padding-top: 30px; padding-bottom: 5px">
            <?php
            arrivals($dateToSearch);
            ?>
        </td>
    </tr>
    <tr>
        <td style="vertical-align: top; align-content: center; padding-top: 30px; padding-bottom: 5px">
            <?php
            departures($dateToSearch);
            ?>
        </td>
    </tr>
</table>
<br><br>
<?php
$prevDay = date('d/m/Y', strtotime($dateToSearch . ' -1 day'));
$nextDay = date('d/m/Y', strtotime($dateToSearch . ' +1 day'));
?>

<span class="button_off" onMouseOver="this.className='button_on'" onMouseOut="this.className='button_off'"
     onClick="document.location.href='admin.php?pagetoload=home.php&date=<?=$prevDay?>'; return false;"><< Previous Day</span>

<span class="button_off" onMouseOver="this.className='button_on'" onMouseOut="this.className='button_off'"
     onClick="document.location.href='admin.php?pagetoload=home.php&date=<?=$nextDay?>'; return false;">Next Day >></span>

<br><br><br>

<span class="question">Search for another date</span> <input name="mydate" id="datepicker" size="8">

<span class="button_off" onMouseOver="this.className='button_on'" onMouseOut="this.className='button_off'"
     onClick="searchForThisDate(); return false;">Search</span>

<?php
function arrivals($dateToSearch) {
    $r = getResidentsDate($dateToSearch, 'arrivals');
    if (mysqli_num_rows($r)) {
        ?>
        <table border="0" align="center" cellpadding="0" cellspacing="0">
            <tr>
                <td colspan="7" class="question" style="text-align: center; padding-bottom: 10px">Arrivals</td>
            </tr>
            <tr>
                <td class="image_cell"></td>
                <td class="titol_taula_list">Name</td>
                <td class="titol_taula_list">Departure</td>
                <td class="titol_taula_list">Room</td>
                <td class="titol_taula_list">UK Phone</td>
                <td class="titol_taula_list">Email</td>
                <td class="titol_taula_list" align="right">Deposit</td>
            </tr>
            <?php
            while ($arrData = mysqli_fetch_assoc($r)) {
                $arrData = utf8_converter($arrData);
                $location = "document.location.href='admin.php?pagetoload=application_form.php&resident_id=$arrData[resident_id]&from=home.php'";
                $goToResident = "onclick=\"$location\"";
                ?>
                <tr class="row1" onMouseOver="this.className = 'row_selected'" onMouseOut="this.className = 'row1'" <?= $goToResident ?>>
                    <td class="cell2" width="35" height="40" align="center" valign="middle" <?= $goToResident ?>>
                        <?php
                        if ($arrData[picture] != "" && is_file("../residentsnh/" . $arrData[picture])) {
                            echo "<img src='../residentsnh/" . $arrData[picture] . "' width='30' height='40'>";
                        } else {
                            echo "<img src='imgs/no_picture.png' width='30' border='0'>";
                        }
                        ?>
                    </td>
                    <td class="cell2" align="left"><?=$arrData[name]." ".$arrData[surname]?></td>
                    <td class="cell2" align="left"><?=mostrar_fecha(substr($arrData[bdeparture], 0, 10))?></td>
                    <td class="cell2" align="left"><?=$arrData[room]?></td>
                    <td class="cell2" align="left"><?=$arrData[ukphone]?></td>
                    <td class="cell2" align="left"><?=$arrData[email]?></td>
                    <td class="cell2" align="right">&pound;<?=$arrData[deposit]?></td>
                </tr>
                <?php
            }
            ?>
        </table>
        <?php
    } else {
        echo "<span class='question'>There are no arrivals</span>";
    }
}

function departures($dateToSearch) {
    $r = getResidentsDate($dateToSearch, 'departures');
    if (mysqli_num_rows($r)) {
        ?>
        <table border="0" align="center" cellpadding="0" cellspacing="0">
            <tr>
                <td colspan="9" class="question" style="text-align: center; padding-bottom: 10px">Departures</td>
            </tr>
            <tr>
                <td class="image_cell"></td>
                <td class="titol_taula_list">Name</td>
                <td class="titol_taula_list">Arrival</td>
                <td class="titol_taula_list">Room</td>
                <td class="titol_taula_list">UK Phone</td>
                <td class="titol_taula_list">Email</td>
                <td class="titol_taula_list_rigth">Deposit</td>
                <td class="titol_taula_list_rigth">Outstanding</td>
                <td class="titol_taula_list_rigth">Total</td>
            </tr>
            <?php
            while ($arrData = mysqli_fetch_assoc($r)) {
                $arrData = utf8_converter($arrData);
                $location = "document.location.href='admin.php?pagetoload=application_form.php&resident_id=$arrData[resident_id]&from=home.php'";
                $goToResident = "onclick=\"$location\"";
                $outstanding = getOutstanding($arrData[resident_id]);
                ?>
                <tr class="row1" onMouseOver="this.className = 'row_selected'" onMouseOut="this.className = 'row1'" <?= $goToResident ?>>
                    <td class="cell2" width="35" height="40" align="center" valign="middle" <?= $goToResident ?>>
                        <?php
                        if ($arrData[picture] != "" && is_file("../residentsnh/" . $arrData[picture])) {
                            echo "<img src='../residentsnh/" . $arrData[picture] . "' width='30' height='40'>";
                        } else {
                            echo "<img src='imgs/no_picture.png' width='30' border='0'>";
                        }
                        ?>
                    </td>
                    <td class="cell2" align="left"><?=$arrData[name]." ".$arrData[surname]?></td>
                    <td class="cell2" align="left"><?=mostrar_fecha(substr($arrData[barrival], 0, 10))?></td>
                    <td class="cell2" align="left"><?=$arrData[room]?></td>
                    <td class="cell2" align="left"><?=$arrData[ukphone]?></td>
                    <td class="cell2" align="left"><?=$arrData[email]?></td>
                    <td class="cell2" align="right">&pound;<?=$arrData[deposit]?></td>
                    <td class="cell2" align="right">&pound;<?=number_format($outstanding, 2, ".", ",")?></td>
                    <td class="cell2" align="right">&pound;<?=number_format($outstanding-$arrData[deposit], 2, ".", ",")?></td>
                </tr>
                <?php
            }
            ?>
        </table>
        <?php
    } else {
        echo "<span class='question'>There are no departures</span>";
    }
}

function getResidentsDate($dateToSearch, $type) {
    global $link;
    $condition = "AND bookings.arrival='$dateToSearch'";
    if ($type == 'departures') {
        $condition = "AND bookings.planned_departure='$dateToSearch'";
    }
    $q =
        "SELECT *, residents.telephone AS residentstelephone, bookings.arrival AS barrival, bookings.planned_departure AS bdeparture ".
        "FROM bookings " .
        "LEFT JOIN residents ON bookings.resident_id=residents.resident_id " .
        "LEFT JOIN rooms ON bookings.room_id=rooms.room_id " .
        "LEFT JOIN countries ON residents.country_id=countries.country_id " .
        "WHERE (bookings.status='accepted' OR bookings.status='finished') " .
        $condition .
        "GROUP BY NAME, surname";
    return mysqli_query($link, $q);
}

function getOutstanding($resident_id) {
    global $link;
    $q = "SELECT * FROM bookings ".
         "WHERE resident_id=$resident_id AND (status='' OR status IS NULL OR status='accepted') ".
         "ORDER BY arrival DESC";
    $r = mysqli_query($link, $q);
    $total_outstanding = 0;
    while ($arrAccomodation = mysqli_fetch_assoc($r)) {
        $date_from = mostrar_fecha($arrAccomodation['arrival']);
        $date_to_planned = mostrar_fecha($arrAccomodation['planned_departure']);
        $days = subtract_dates($date_from, $date_to_planned);

        $total_rent_temp = $days * ($arrAccomodation['weekly_rate'] / 7);
        $total_rent = round($total_rent_temp, 2);
        $due = $total_rent + $arrAccomodation['laundry'] + $arrAccomodation['hc'] + $arrAccomodation['printing'] + $arrAccomodation['extra'];
        //$outstanding = $due - $arrAccomodation['deposit'] - $arrAccomodation['received'];
        $outstanding = $due - $arrAccomodation['received'];
        $total_outstanding = $total_outstanding + $outstanding;
    }

    return $total_outstanding;
}

function displayBirthdays() {
    global $link;
    $today = date("Y", time()) . "-" . date("m", time()) . "-" . date("d", time());
    $condition_search = " AND bookings.arrival <= '$today' AND bookings.departure >= '$today' ";

    $q = "SELECT *, bookings.arrival AS barrival, bookings.departure AS bdeparture FROM bookings " .
            "LEFT JOIN residents ON bookings.resident_id=residents.resident_id " .
            "LEFT JOIN rooms ON bookings.room_id=rooms.room_id " .
            "LEFT JOIN countries ON residents.country_id=countries.country_id " .
            "WHERE bookings.status IN ('accepted','finished') " . $condition_search .
            "GROUP BY NAME, surname ORDER BY SUBSTR(date_of_birth,6,5), surname, name DESC";
    $r = mysqli_query($link, $q);

    if (mysqli_num_rows($r)) {
        ?>
        <p class="question" align="center">Birthdays</p>
        <?php
        // TODAY
        $birthday_names = "";
        while ($arrData = mysqli_fetch_assoc($r)) {
            //if (substr($arrData[date_of_birth], 5, 5) == date("m-d")) {
            if (substr($arrData[date_of_birth], 5, 5) == date("03-04")) {
                $age = date("Y") - substr($arrData[date_of_birth], 0, 4);
                $birthday_names.=$arrData[name] . " " . $arrData[surname] . " - " . mostrar_fecha(substr($arrData[date_of_birth], 0, 10)) . " (" . $age . ")<br>";
            }
        }
        if ($birthday_names != "") {
            echo "<p align=center class='question'>Today<br>$birthday_names</p>";
        }

        // TOMORROW
        $birthday_names = "";
        $q = "SELECT *, bookings.arrival AS barrival, bookings.departure AS bdeparture FROM bookings " .
                "LEFT JOIN residents ON bookings.resident_id=residents.resident_id " .
                "LEFT JOIN rooms ON bookings.room_id=rooms.room_id " .
                "LEFT JOIN countries ON residents.country_id=countries.country_id " .
                "WHERE bookings.status IN ('accepted','finished') " . $condition_search .
                "AND SUBSTR(date_of_birth,6,5) > '" . date('m-d') . "' " .
                "AND SUBSTR(date_of_birth,1,10)!='0000-00-00' " .
                "GROUP BY NAME, surname ORDER BY SUBSTR(date_of_birth,6,5), surname, name DESC LIMIT 1";
        //ver("q",$q);
        $r = mysqli_query($link, $q);
        while ($arrData = mysqli_fetch_assoc($r)) {
            $age = date("Y") - substr($arrData[date_of_birth], 0, 4);
            $birthday_names.=$arrData[name] . " " . $arrData[surname] . " - " . mostrar_fecha(substr($arrData[date_of_birth], 0, 10)) . " (" . $age . ")<br>";
        }
        if ($birthday_names != "") {
            echo "<p align=center class='question'>Next<br>$birthday_names</p>";
        }
    }
}
?>