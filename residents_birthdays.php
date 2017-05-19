<?php
require_once('functions.php');

validate_user();
?>
<LINK href="css/netherhall.css" rel="stylesheet" type="text/css">
<form name="myform" method="get" action="admin.php">

    <?php
    $today = date("Y", time()) . "-" . date("m", time()) . "-" . date("d", time());
    $condition_search = " AND bookings.arrival <= '$today' AND bookings.departure >= '$today' ";

    $q = "SELECT *, bookings.arrival AS barrival, bookings.departure AS bdeparture FROM bookings " .
            "LEFT JOIN residents ON bookings.resident_id=residents.resident_id " .
            "LEFT JOIN rooms ON bookings.room_id=rooms.room_id " .
            "LEFT JOIN countries ON residents.country_id=countries.country_id " .
            "WHERE bookings.status IN ('accepted','finished') " . $condition_search .
            "GROUP BY NAME, surname ORDER BY SUBSTR(date_of_birth,6,5), surname, name DESC";
    $r = mysqli_query($q);

    if (mysqli_num_rows($r)) {
        ?>
        <p class="question" align="center">Birthdays</p>
        <?php
        // TODAY
        $birthday_names = "";
        while ($arrData = mysqli_fetch_assoc($r)) {
            if (substr($arrData[date_of_birth], 5, 5) == date("m-d")) {
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
        $r = mysqli_query($q);
        while ($arrData = mysqli_fetch_assoc($r)) {
            $age = date("Y") - substr($arrData[date_of_birth], 0, 4);
            $birthday_names.=$arrData[name] . " " . $arrData[surname] . " - " . mostrar_fecha(substr($arrData[date_of_birth], 0, 10)) . " (" . $age . ")<br>";
        }
        if ($birthday_names != "") {
            echo "<p align=center class='question'>Next<br>$birthday_names</p>";
        }
        ?>
        <table border="0" align="center" cellpadding="0" cellspacing="0">
            <input type="hidden" name="pagetoload" value="residents_list.php">
            <input type="hidden" name="operation">
            <tr valign="middle">
                <td class="image_cell"></td>
                <td class="titol_taula_list">Name</td>
                <td class="titol_taula_list">Birthday</td>
                <td class="titol_taula_list" align="right">Age</div></td>
            </tr>
            <?php
            $q = "SELECT *, bookings.arrival AS barrival, bookings.departure AS bdeparture FROM bookings " .
                    "LEFT JOIN residents ON bookings.resident_id=residents.resident_id " .
                    "LEFT JOIN rooms ON bookings.room_id=rooms.room_id " .
                    "LEFT JOIN countries ON residents.country_id=countries.country_id " .
                    "WHERE bookings.status IN ('accepted','finished') " . $condition_search .
                    "AND SUBSTR(date_of_birth,1,10)!='0000-00-00' " .
                    "GROUP BY NAME, surname ORDER BY SUBSTR(date_of_birth,6,5), surname, name DESC";
            $r = mysqli_query($q);
            while ($arrData = mysqli_fetch_assoc($r)) {
                $birthday = "";
                if (substr($arrData[date_of_birth], 5, 5) == date("m-d")) {
                    $birthday = "bgcolor=#C6DBFF";
                }
                ?>
                <tr class="row1" onMouseOver="this.className = 'row_selected'" onMouseOut="this.className = 'row1'">
                    <td class="cell2" height="40" align="center" valign="middle" <?= $birthday ?>>
                    <?php
                    if ($arrData[picture] != "") {
                        echo "<img src='../residentsnh/" . $arrData[picture] . "' width='25' height='28'>";
                    } else {
                        echo "<img src='imgs/no_picture.png' width='25' border='0'>";
                    }
                    ?>
                    </td>
                    <td class="cell2 left" <?= $birthday ?>><a href="admin.php?pagetoload=application_form.php&resident_id=<?= $arrData[resident_id] ?>&from=residents_list.php" class="table_link2" <?= $birthday ?>><?= $arrData[surname] . ", " . $arrData[name] ?></a></td>
                    <td class="cell2 left" <?= $birthday ?>><?= mostrar_fecha(substr($arrData[date_of_birth], 0, 10)) ?></td>
                    <td class="cell2 left" <?= $birthday ?>>
                        <?php
                        $age = date("Y") - substr($arrData[date_of_birth], 0, 4);
                        if ($age < 100) {
                            echo $age;
                        }
                        ?>
                    </td>
                </tr>
                <?php
            }
            ?>
        </table>
        <?php
        //############################################################################
        // NO BIRTHDAY (date is 00/00/0000)
        //############################################################################
        $q = "SELECT *, bookings.arrival AS barrival, bookings.departure AS bdeparture FROM bookings " .
                "LEFT JOIN residents ON bookings.resident_id=residents.resident_id " .
                "LEFT JOIN rooms ON bookings.room_id=rooms.room_id " .
                "LEFT JOIN countries ON residents.country_id=countries.country_id " .
                "WHERE bookings.status IN ('accepted','finished') " . $condition_search .
                "AND SUBSTR(date_of_birth,1,10)='0000-00-00' " .
                "GROUP BY NAME, surname DESC";
        $r = mysqli_query($q);
        if (mysqli_num_rows($r)) {
            ?>
            <p class="question" align="center">People without birthday date</p>
            <table border="0" align="center" cellpadding="0" cellspacing="0">
                <input type="hidden" name="pagetoload" value="residents_list.php">
                <input type="hidden" name="operation">
                <tr valign="middle">
                    <td class="image_cell"></td>
                    <td class="titol_taula_list"><a href="admin.php?pagetoload=residents_birthdays.php&sort_by=name" class="header_link2">Name</a></td>
                </tr>
                <?php
                while ($arrData = mysqli_fetch_assoc($r)) {
                    ?>
                    <tr class="row1" onMouseOver="this.className = 'row_selected'" onMouseOut="this.className = 'row1'">
                        <td class="cell2" height="40" align="center" valign="middle">
                            <?php
                            if ($arrData[picture] != "") {
                                echo "<img src='../residentsnh/" . $arrData[picture] . "' width='25' height='28'>";
                            } else {
                                echo "<img src='imgs/no_picture.png' width='25' border='0'>";
                            }
                            ?>
                        </td>
                        <td valign="middle" class="cell2 left">
                            <a href="admin.php?pagetoload=application_form.php&resident_id=<?= $arrData[resident_id] ?>&from=residents_list.php" class="table_link2" <?= $birthday ?>><?= $arrData[surname] . ", " . $arrData[name] ?></a>
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </table>
            <?php
        }
    }
    ?>
</form>