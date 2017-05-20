<?php
require_once('functions.php');

validate_user();

if ($request[operation] == "book") {
    $arrival = change_format_date($request[date_from]);
    $departure = change_format_date($request[date_to]);

    $number_rooms = 0;
    $color = random_color();

    $q = "INSERT INTO groups (name,color,arrival,departure) VALUES ('{$request[group_name]}', '$color' , '$arrival', '$departure')";
    $r = mysqli_query($link, $q);
    $group_id = mysqli_insert_id($link);

    foreach ($request as $key => $value) {
        //ver("",$key);
        if (substr($key, 0, 4) == "room") {
            $room_id = substr($key, 4);
            $number_rooms++;
            $nr = $number_rooms;
            if ($nr < 10) {
                $nr = "0" . $nr;
            }

            $today = date("Y-m-d H:m:s");
            $q1 = "INSERT INTO residents (name,color, arrival, departure, application_date, status) " .
                    "VALUES ('{$request[group_name]} $nr', '$color', '$arrival', '$departure', '$today', 'accepted')";
            mysqli_query($link, $q1);
            $resident_id = mysqli_insert_id($link);

            $q2 = "INSERT INTO bookings (arrival, planned_departure, departure, room_id, resident_id, status, booking_date, group_id) " .
                    "VALUES ('$arrival', '$departure', '$departure', '$room_id', $resident_id, 'accepted', '$today', $group_id)";
            mysqli_query($link, $q2);

            $q3 = "INSERT INTO residents_groups (group_id, resident_id) VALUES ('$group_id', '$resident_id')";
            mysqli_query($link, $q3);
        }
    }
    ?><script>document.location = 'admin.php?pagetoload=groups_list.php';</script><?php
}
?>
<script language="JavaScript" src="js/funciones.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<script>
$(function() {
    $("#date_from").datepicker({ dateFormat: "dd/mm/yy" }).val();
    $("#date_to").datepicker({ dateFormat: "dd/mm/yy" }).val();
});

function find_rooms() {
    if (document.myform.date_from.value !== "" && document.myform.date_to.value !== "") {
        if (valFecha(document.myform.date_from, "Invalid arrival date")) {
            if (valFecha(document.myform.date_to, "Invalid departure date")) {
                str1 = document.myform.date_from.value;
                str2 = document.myform.date_to.value;
                var dt1 = parseInt(str1.substring(0, 2), 10);
                var mon1 = parseInt(str1.substring(3, 5), 10);
                var yr1 = parseInt(str1.substring(6, 10), 10);
                var dt2 = parseInt(str2.substring(0, 2), 10);
                var mon2 = parseInt(str2.substring(3, 5), 10);
                var yr2 = parseInt(str2.substring(6, 10), 10);
                var date1 = new Date(yr1, mon1, dt1);
                var date2 = new Date(yr2, mon2, dt2);

                if (date2 > date1) {
                    document.myform.operation.value = "find";
                    document.myform.submit();
                } else {
                    alert("Departure date must be later than the arrival date");
                }
            }
        }
    } else {
        alert("Invalid dates");
    }
}
function book() {
    document.myform.operation.value = "book";
    document.myform.submit();
}
</script>
<LINK href="css/netherhall.css" rel="stylesheet" type="text/css">
<br>
<TABLE width="900" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
    <form name="myform" method="post" action="admin.php">
        <input type="hidden" name="pagetoload" value="new_group.php">
        <input type="hidden" name="group_id" value="<?= $request['group_id'] ?>">
        <input type="hidden" name="operation">
        <tr>
            <td valign="middle" align="center">
                <?php
                // Searches the information of the resident.
                if ($request[resident_id]) {
                    $r = mysqli_query($link, "SELECT * FROM residents WHERE resident_id={$request[resident_id]}");
                    $arrData = mysqli_fetch_assoc($r);
                    ?>
                    <p align="center" class="question">
                        Booking a room for <?= $arrData[name] . " " . $arrData[surname] ?><br>for these dates
                    </p>
                    <?php
                }
                ?>
                <table class="text_form" cellpadding="8" cellspacing="0" border="0">
                    <tr>
                        <td style="text-align: left;">Group Name</td>
                        <td><input type="text" name="group_name" size="20" value="<?= $request[group_name] ?>"></td>
                    </tr>
                    <tr>
                        <td style="text-align: left;">From Date </td>
                        <td>
                            <input type="text" name="date_from" id="date_from" size="20" value="<?= $request['date_from'] ?>" />
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: left;">To Date</td>
                        <td>
                            <input type="text" name="date_to" id="date_to" size="20" value="<?= $request['date_to'] ?>" />
                        </td>
                    </tr>
                </table>
                <br>
                <?php
                button("javascript:find_rooms()", "Find Rooms");

                if ($request["operation"] == "find" || $request["operation"] == "book") {
                    $date_fr_pre = change_format_date($request[date_from]);
                    $date_to_pre = change_format_date($request[date_to]);

                    // We add one day to the from date. Because the departure date is free. And we rest one for the same reason.
                    $timefr_pre = strtotime($date_fr_pre);
                    $timefr = $timefr_pre + (1 * 24 * 60 * 60);
                    $date_fr = date("Y/m/d", $timefr);

                    $timeto_pre = strtotime($date_to_pre);
                    $timeto = $timeto_pre - (1 * 24 * 60 * 60);
                    $date_to = date("Y/m/d", $timeto);

                    $q = "SELECT room_id, room FROM rooms WHERE room_id NOT IN ( " .
                            " SELECT room_id FROM bookings WHERE " .
                            " bookings.arrival           BETWEEN '$date_fr' AND '$date_to' OR " .
                            " bookings.planned_departure BETWEEN '$date_fr' AND '$date_to' OR " .
                            " (bookings.arrival <= '$date_fr' AND bookings.planned_departure >= '$date_to') ) " .
                            "ORDER BY room";
                    $r = mysqli_query($link, $q);
                    if ($error) {
                        ?><p class="question" align="center"><?= $error ?></p><?php
                    }
                    if (!mysqli_num_rows($r)) {
                        ?><p class="question" align="center">There are no available rooms for these dates</p><?php
                    } else {
                        ?>
                        <p class="question" align="center">The following rooms are available for these dates:</p>
                        <!-- SHOW FREE ROOMS -->
                        <table align="center" border="0" cellpadding="10" cellspacing="0">
                            <?php
                            $i = 0;
                            while ($arrData = mysqli_fetch_assoc($r)) {
                                if ($i == 0) {
                                    ?>
                                    <tr>
                                    <?php
                                }
                                $current_room = "room" . $arrData[room_id];
                                ?>
                                <td align="center" class="main_message">
                                    <span class="main_message"><?= $arrData[room] ?></span><br>
                                    <input type="checkbox" name="room<?= $arrData[room_id] ?>" value="<?= $arrData[room_id] ?>"
                                        <?php
                                        if ($request[$current_room]) {
                                            echo "checked";
                                        } ?>>
                                </td>
                                <?php
                                $i++;
                                if ($i == 20) {
                                    $i = 0;
                                }
                                if ($i == 0) {
                                    ?>
                                    </tr>
                                    <?php
                                }
                            }
                        ?>
                        </table>
                        <br>
                        <?php
                        button("javascript:book()", "Book Rooms");
                    }
                }
                ?>
            </td>
        </tr>
    </form>
</table>