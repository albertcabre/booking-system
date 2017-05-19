<?php
require_once('functions.php');

validate_user();

$res_bookings = mysql_query("SELECT * FROM bookings WHERE resident_id=$request[resident_id] AND status='finished' ORDER BY arrival DESC");
while ($arrAccomodation = mysql_fetch_assoc($res_bookings)) {
    $date_from = mostrar_fecha($arrAccomodation['arrival']);
    $date_to_planned = mostrar_fecha($arrAccomodation['departure']);
    $date_to = mostrar_fecha($arrAccomodation['actual_departure']);

    $days = subtract_dates($date_from, $date_to_planned);

    // Search the name of the room
    if ($arrAccomodation[room_id]) {
        $r2 = mysql_query("SELECT * FROM rooms WHERE room_id=$arrAccomodation[room_id]");
        $room = "";
        if (mysql_numrows($r2)) {
            $room = mysql_result($r2, 0, "room");
        }
    }

    //ver_array("arrAccomodation",$arrAccomodation);
    $total_rent_temp = $days * ($arrAccomodation['weekly_rate'] / 7);
    $total_rent = round($total_rent_temp, 2);
    $due = $total_rent + $arrAccomodation['laundry'] + $arrAccomodation['hc'] + $arrAccomodation['printing'] + $arrAccomodation['extra'];
    $invoice_number = "NO BILL";
    $outstanding = $arrAccomodation['received'] - $due;
    if ($arrAccomodation['invoice_number'] != "") {
        $invoice_number = $arrAccomodation['invoice_number'];
    }
    ?>
    <tr>
        <td colspan="2">
        <div id="accom2">
        <table width="100%" border="0" cellspacing="5" cellpadding="0">
            <tr class="table_style">
                <td align="left" colspan="15" class="Titol_pagina">
                    <span class="Titol_pagina_gris">arrival:&nbsp;</span><?= $date_from ?>
                    <span class="Titol_pagina_gris">| departure:&nbsp;</span><?= $date_to_planned ?>
                    <span class="Titol_pagina_gris">| actual departure:&nbsp;</span><?= $date_to ?>
                    <span class="Titol_pagina_gris">| room:&nbsp;</span><?= $room ?>
                </td>
            </tr>
            <tr class="table_style">
                <td align="right" class="normal_text">Num. days</td>
                <td align="right" class="normal_text">Weekly rate</td>
                <td align="right" class="text_form">Total room</td>
                <td align="right" class="normal_text">Laundry</td>
                <td align="right" class="normal_text">HC</td>
                <td align="right" class="normal_text">Printing</td>
                <td align="right" class="normal_text">Extra</td>
                <td align="right" class="text_form">Due</td>
                <td align="right" class="text_form">Received</td>
                <td align="right" class="text_form">Outstand.&nbsp;</td>
                <td align="left" class="text_form">Inv. Num.</td>
                <td colspan="4">&nbsp;</td>
            </tr>
            <tr class="table_style">
                <td align="right"><?= $days ?></td>
                <td align="right"><?= $arrAccomodation['weekly_rate'] ?></td>
                <td align="right"><?= number_format($total_rent, 2, ".", ",") ?></td>
                <td align="right"><?= $arrAccomodation['laundry'] ?></td>
                <td align="right"><?= $arrAccomodation['hc'] ?></td>
                <td align="right"><?= $arrAccomodation['printing'] ?></td>
                <td align="right"><?= $arrAccomodation['extra'] ?></td>
                <td align="right"><?= number_format($due, 2, ".", ",") ?></td>
                <td align="right"><?= $arrAccomodation['received'] ?></td>
                <?php
                $color_text = "normal_text_verd";
                if ($outstanding > 0) {
                    $color_text = "normal_text_red";
                }
                ?>
                <td align="right"><span class="<?= $color_text ?>"><?= number_format($outstanding, 2, ".", ",") ?></span>&nbsp;</td>
                <td align="center"><?= $invoice_number ?></td>
                <td height="25" class="text_form_small" align="center">
                <?php
                $icon = "ok_16x16.gif";
                if ($outstanding) {
                    $icon = "attention3_16x16.gif";
                }
                echo "<img src='imgs/$icon' border='0'>";
                ?>
                </td>
                <td align="center">
                    <a href="javascript:delete_booking(<?= $arrAccomodation['booking_id'] ?>,'<?= $date_from ?>','<?= $date_to_planned ?>')">
                        <img src="imgs/trash_16x16.gif" border="0">
                    </a>
                </td>
                <td align="center"><?php
                if ($arrAccomodation[comments] == "") {
                    ?>
                    <a href="javascript:comments(<?= $arrAccomodation['booking_id'] ?>)" title="No comments">
                        <img src="imgs/notepad_16x16.gif" width="16" height="16" border="0">
                    </a>
                    <?php
                } else {
                    ?>
                    <a href="javascript:comments(<?= $arrAccomodation['booking_id'] ?>)" title="There are some comments">
                        <img src="imgs/notepad_(edit)_16x16.gif" border="0">
                    </a>
                    <?php
                }
                ?>
                </td>
                <td align="center">
                    <table cellpadding="0" cellspacing="0">
                        <tr>
                            <td align="center" width="47">
                                <div class="button_off_small" onMouseOver="this.className = 'button_on_small'" onMouseOut="this.className = 'button_off_small'">
                                    <a href="javascript:change_status('accepted',<?= $arrAccomodation['booking_id'] ?>)" class="button_link_small">Back</a>
                                </div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        </div>
        </td>
    </tr>
    <?php
}