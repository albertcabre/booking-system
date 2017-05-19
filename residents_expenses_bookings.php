<?php
$condition = ($operation == 'overdue') ?  "AND DATEDIFF('$today', bookings.arrival) >= $overdue_days " : "";

$bookingQuery =
    "SELECT * FROM residents LEFT JOIN bookings ON residents.resident_id = bookings.resident_id " .
    "WHERE bookings.status='accepted' AND residents.resident_id={$arrInfo[resident_id]} " .
    $condition.
    "ORDER BY NAME, surname, bookings.arrival";
$r2 = mysql_query($bookingQuery);
while ($arrData = mysql_fetch_assoc($r2)) {
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
    $outstanding_pre = $due - $arrData['received'];
    $outstanding = round($outstanding_pre, 2);
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
        } else {
            $bi = 0;
        }
    }
    ?>
    <input type="hidden" name="booking_id_<?= $id ?>" value="<?= $id ?>">

    <?php
    if ($operation != "simplified") {
        ?>
        <tr class="row1">
        <td><?= $date_from ?></td>
        <td><?= $date_to ?></td>
        <td><?= $room ?></td>
        <td align="right"><?= $days ?></td>
        <td><input onChange="calculate(this.name)" type="text" name="ra_<?= $id ?>" value="<?= $ra ?>" size="4" class="<?= $arrClasses[$id]['ra'] ?>"></td>
        <td align="right"><?= number_format($total_rent, 2, ".", ",") ?></td>
        <td><input onChange="calculate(this.name)" type="text" name="la_<?= $id ?>" value="<?= $la ?>" size="6" class="<?= $arrClasses[$id]['la'] ?>"></td>
        <td><input onChange="calculate(this.name)" type="text" name="hc_<?= $id ?>" value="<?= $hc ?>" size="6" class="<?= $arrClasses[$id]['hc'] ?>"></td>
        <td><input onChange="calculate(this.name)" type="text" name="pr_<?= $id ?>" value="<?= $pr ?>" size="6" class="<?= $arrClasses[$id]['pr'] ?>"></td>
        <td><input onChange="calculate(this.name)" type="text" name="ex_<?= $id ?>" value="<?= $ex ?>" size="6" class="<?= $arrClasses[$id]['ex'] ?>"></td>
        <td align="right"><?= number_format($due, 2, ".", ",") ?></td>
        <td><input onChange="calculate(this.name)" type="text" name="re_<?= $id ?>" value="<?= $re ?>" size="6" class="<?= $arrClasses[$id]['re'] ?>"></td>
        <?php
        $color_text = "normal_text_verd";
        if (round($outstanding) > 0) {
            $color_text = "normal_text_red";
        }
        if (round($outstanding) == 0) {
            $outstanding = 0;
        }
        ?>
        <td align="right"><span class="<?= $color_text ?>"><?= number_format($outstanding, 2, ".", ",") ?></span></td>
        <td>
            <select name="bi_<?= $id ?>" class="normal_text">
                <option>No</option>
                <option <?php if ($bi) { echo 'selected'; } ?> >Yes</option>
            </select>
        </td>
        <td><input type="text" name="in_<?= $id ?>" value="<?= $arrData['invoice_number'] ?>" size="12" class="input_small"></td>
        <td><a href="javascript:update(<?= $arrInfo[resident_id] ?>)" class="table_link2" title="Update"><img src="imgs/arrow_refresh.png" width="16" height="16" border="0"></a></td>
        <td><?php
        if ($arrData[comments] == "") {
            ?><a href="javascript:comments(<?= $arrData['booking_id'] ?>)" title="No comments"><img src="imgs/notepad_16x16.gif" width="16" height="16" border="0"></a><?php
        } else {
            ?><a href="javascript:comments(<?= $arrData['booking_id'] ?>)" title="<?= $arrData[comments] ?>"><img src="imgs/notepad_(edit)_16x16.gif" border="0"></a><?php
        }
        ?>
        </td>
        <td>
            <a href="javascript:delete_account(<?= $arrData['booking_id'] ?>)" title="Delete"><img src="imgs/delete_16x16.gif" width="16" height="16" border="0"></a>
        </td>
        </tr>
        <?php
    }

    $total_days = $total_days + $days;
    $total_total_rent = $total_total_rent + $total_rent;
    $total_laundry = $total_laundry + $la;
    $total_hc = $total_hc + $hc;
    $total_printing = $total_printing + $pr;
    $total_extra = $total_extra + $ex;
    $total_due = $total_due + $due;
    $total_received = $total_received + $re;
    $total_outstanding_pre = $total_outstanding + $outstanding;
    $total_outstanding = round($total_outstanding_pre, 2);

    $grand_total_rent = $grand_total_rent + $total_rent;
    $grand_total_laundry = $grand_total_laundry + $la;
    $grand_total_hc = $grand_total_hc + $hc;
    $grand_total_printing = $grand_total_printing + $pr;
    $grand_total_extra = $grand_total_extra + $ex;
    $grand_total_due = $grand_total_due + $due;
    $grand_total_received = $grand_total_received + $re;
    $grand_total_outstanding = $grand_total_outstanding + $outstanding;
}

// Display resident bookings total
if (($operation == "simplified" && mysql_num_rows($r2) > 0) || ($operation != "simplified" && mysql_num_rows($r2) > 1)) {
    ?>
    <tr class="row1">

    <?php
    if ($operation == "simplified") {
        ?>
        <td class="td_total" align="left"><a name="jump<?= $arrInfo[resident_id] ?>" href="<?= $href ?>" class="table_link2"><?= $name ?></a></td>
        <td class="td_total" align="right"><?= $total_days ?></td>
        <td class="td_total" align="right"><?= number_format($total_total_rent, 2, ".", ",") ?></td>
        <td class="td_total" align="right"><?= number_format($total_laundry, 2, ".", ",") ?></td>
        <td class="td_total" align="right"><?= number_format($total_hc, 2, ".", ",") ?></td>
        <td class="td_total" align="right"><?= number_format($total_printing, 2, ".", ",") ?></td>
        <td class="td_total" align="right"><?= number_format($total_extra, 2, ".", ",") ?></td>
        <td class="td_total" align="right"><?= number_format($total_due, 2, ".", ",") ?></td>
        <td class="td_total" align="right"><?= number_format($total_received, 2, ".", ",") ?></td>
        <?php
    } else {
        ?>
        <td class='td_total' colspan='3'>&nbsp;</td>
        <td class="td_total" align="right"><?= $total_days ?></td>
        <td class="td_total">&nbsp;</td>
        <td class="td_total" align="right"><?= number_format($total_total_rent, 2, ".", ",") ?></td>
        <td class="td_total"><?= number_format($total_laundry, 2, ".", ",") ?></td>
        <td class="td_total"><?= number_format($total_hc, 2, ".", ",") ?></td>
        <td class="td_total"><?= number_format($total_printing, 2, ".", ",") ?></td>
        <td class="td_total"><?= number_format($total_extra, 2, ".", ",") ?></td>
        <td class="td_total" align="right"><?= number_format($total_due, 2, ".", ",") ?></td>
        <td class="td_total"><?= number_format($total_received, 2, ".", ",") ?></td>
        <?php
    }

    $color_text = "normal_text_verd";
    if (round($total_outstanding) > 0) {
        $color_text = "normal_text_red";
    }
    ?>
    <td class="td_total" align="right"><span class="<?= $color_text ?>"><?= number_format($total_outstanding, 2, ".", ",") ?></span></td>
    <?php
    if ($operation != "simplified") {
        ?><td class="td_total" colspan="5">&nbsp;</td><?php
    }
    ?>
    </tr>
    <?php
}
?>