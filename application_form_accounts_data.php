<?php
require_once('functions.php');

validate_user();

$r = mysqli_query($link, "SELECT * FROM bookings WHERE resident_id=$request[resident_id] AND (status='' OR status IS NULL OR status='accepted') ORDER BY arrival DESC");
$num_of_accounts = mysqli_num_rows($r);
$accounts = 0;
$total_outstanding = 0;
while ($arrAccomodation = mysqli_fetch_assoc($r)) {
    $accounts++;
    //ver_array("arrAccomodation",$arrAccomodation);
    $date_from = mostrar_fecha($arrAccomodation['arrival']);
    $date_to_planned = mostrar_fecha($arrAccomodation['planned_departure']);
    $date_to = mostrar_fecha($arrAccomodation['departure']);

    $days = subtract_dates($date_from, $date_to_planned);

    // Search the name of the room
    if ($arrAccomodation[room_id]) {
        $r2 = mysqli_query($link, "SELECT * FROM rooms WHERE room_id=$arrAccomodation[room_id]");
        $room = "";
        if (mysqli_num_rows($r2)) {
            $room = mysqli_result($r2, 0, "room");
        }
    }

    $total_rent_temp = $days * ($arrAccomodation['weekly_rate'] / 7);
    $total_rent = round($total_rent_temp, 2);
    $due = $total_rent + $arrAccomodation['laundry'] + $arrAccomodation['hc'] + $arrAccomodation['printing'] + $arrAccomodation['extra'];
    $invoice_number = "NO BILL";
    //$outstanding = $due - $arrAccomodation['deposit'] - $arrAccomodation['received'];
    $outstanding = $due - $arrAccomodation['received'];
    $total_outstanding = $total_outstanding + $outstanding;
    if ($arrAccomodation['invoice_number'] != "") {
        $invoice_number = $arrAccomodation['invoice_number'];
    }
    ?>
    <tr>
        <td colspan="2">
            <div id="accom">
                <table width="100%" border="0" cellspacing="5" cellpadding="0">
                    <tr class="table_style">
                        <td colspan="16" align="left" class="Titol_pagina">
                            <span class="Titol_pagina_gris">arrival:&nbsp;</span><?= $date_from ?>
                            <span class="Titol_pagina_gris">| departure:&nbsp;</span><?= $date_to_planned ?>
                            <span class="Titol_pagina_gris">| actual departure:&nbsp;</span>
                            <input name="ad<?= $arrAccomodation['booking_id'] ?>" type="text" class="normal_text" value="<?= $date_to ?>" size="8" id="rd"  />
                            <span class="Titol_pagina_gris">| room:&nbsp;</span><?= $room ?>
                        </td>
                    </tr>

                    <tr class="table_style">
                        <td align="center" class="normal_text">Num. days</td>
                        <td class="normal_text">Weekly Rate</td>
                        <td align="right" class="text_form">Total room</td>
                        <td align="left" class="normal_text">Laundry</td>
                        <td align="left" class="normal_text">HC</td>
                        <td align="left" class="normal_text">Printing</td>
                        <td align="left" class="normal_text">Extra</td>
                        <td align="right" class="text_form">Due</td>
                        <td align="left" class="text_form">Received</td>
                        <td align="right" class="text_form">Outstand.&nbsp;</td>
                        <td align="left" class="text_form">Inv. Num.</td>
                        <td colspan="5">&nbsp;</td>
                    </tr>

                    <tr class="table_style">
                        <td align="center" class="normal_text"><?= $days ?></td>
                        <td>
                            <input onChange="calculate(this.name)" name="ra<?= $arrAccomodation['booking_id'] ?>" type="text"
                                   value="<?= $arrAccomodation['weekly_rate'] ?>" size="3"
                                       <?php
                                       if ($request[booking_id] == $arrAccomodation['booking_id']) {
                                            echo $error_ra;
                                        } ?>  />
                            <a href="javascript:fees()" title="Fees">
                                <img src="imgs/pound1.png" width="16" height="16" align="absmiddle" border="0">
                            </a>
                            <a href="javascript:terms()" title="Terms">
                                <img src="imgs/data.png" width="16" height="16" align="absmiddle" border="0">
                            </a>
                        </td>
                        <td align="right" class="normal_text"><?= number_format($total_rent, 2, ".", ",") ?></td>
                        <td>
                            <input onChange="calculate(this.name)" name="la<?= $arrAccomodation['booking_id'] ?>" type="text"
                                   class="normal_text" value="<?= $arrAccomodation['laundry'] ?>" size="4"
                                        <?php
                                        if ($request[booking_id] == $arrAccomodation['booking_id']) {
                                            echo $error_la;
                                        } ?> id="laundry"  />
                        </td>
                        <td>
                            <input onChange="calculate(this.name)" name="hc<?= $arrAccomodation['booking_id'] ?>" type="text"
                                   class="normal_text" value="<?= $arrAccomodation['hc'] ?>" size="4"
                                        <?php
                                        if ($request[booking_id] == $arrAccomodation['booking_id']) {
                                            echo $error_hc;
                                        } ?> id="hc" />
                        </td>
                        <td>
                            <input onChange="calculate(this.name)" name="pr<?= $arrAccomodation['booking_id'] ?>" type="text"
                                   class="normal_text" value="<?= $arrAccomodation['printing'] ?>" size="4"
                                        <?php
                                        if ($request[booking_id] == $arrAccomodation['booking_id']) {
                                            echo $error_pr;
                                        } ?> id="printing" />
                        </td>
                        <td>
                            <input onChange="calculate(this.name)" name="ex<?= $arrAccomodation['booking_id'] ?>" type="text"
                                   class="normal_text" value="<?= $arrAccomodation['extra'] ?>" size="4"
                                        <?php
                                        if ($request[booking_id] == $arrAccomodation['booking_id']) {
                                            echo $error_ex;
                                        } ?> id="extra"  />
                        </td>
                        <td align="right" class="normal_text"><?= number_format($due, 2, ".", ",") ?></td>
                        <td>
                            <input onChange="calculate(this.name)" name="re<?= $arrAccomodation['booking_id'] ?>" type="text"
                                    class="normal_text" value="<?= $arrAccomodation['received'] ?>" size="5"
                                        <?php
                                        if ($request[booking_id] === $arrAccomodation['booking_id']) {
                                            echo $error_re;
                                        } ?> id="received" />
                        </td>
                        <td align="right">
                            <?php
                            $color_text = "normal_text_verd";
                            if ($outstanding > 0) {
                                $color_text = "normal_text_red";
                            }
                            ?>
                            <span class="<?= $color_text ?>"><?= number_format($outstanding, 2, ".", ",") ?></span>&nbsp;
                        </td>
                        <td><input name="in<?= $arrAccomodation['booking_id'] ?>" type="text" class="normal_text" value="<?= $invoice_number ?>" size="7" id="n_bill" /></td>
                        <td align="center"><a href="javascript:update(<?= $arrAccomodation['booking_id'] ?>)" class="table_link2" title="Update"><img src="imgs/arrow_refresh.png" width="16" height="16" border="0"></a></td>
                        <td align="center"><a href="javascript:delete_booking(<?= $arrAccomodation['booking_id'] ?>,'<?= $date_from ?>','<?= $date_to_planned ?>')" title="Delete this booking"><img src="imgs/trash_16x16.gif" border="0"></a></td>
                        <td align="center"><a href="admin.php?pagetoload=application_form_dates.php&resident_id=<?= $request[resident_id] ?>&booking_id=<?= $arrAccomodation['booking_id'] ?>" title="Change booking dates"><img src="imgs/date_16x16.gif" border="0"></a></td>
                        <td align="center">
                        <?php
                        if ($arrAccomodation[comments] == "") {
                            ?>
                            <a href="javascript:comments(<?= $arrAccomodation['booking_id'] ?>)" title="No comments"><img src="imgs/notepad_16x16.gif" width="16" height="16" border="0"></a>
                            <?php
                        } else {
                            ?>
                            <a href="javascript:comments(<?= $arrAccomodation['booking_id'] ?>)" title="There are some comments"><img src="imgs/notepad_(edit)_16x16.gif" border="0"></a>
                            <?php
                        }
                        ?>
                        </td>
                        <td align="center">
                            <table cellpadding="0" cellspacing="0" align="center">
                                <tr>
                                    <td align="center" width="47">
                                        <div class="button_off_small" onMouseOver="this.className = 'button_on_small'" onMouseOut="this.className = 'button_off_small'"><a href="javascript:change_status('finished',<?= $arrAccomodation['booking_id'] ?>)" class="button_link_small">Finish</a></div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <?php
                    if ($num_of_accounts == $accounts && $num_of_accounts > 1) {
                        ?>
                        <tr class="table_style">
                            <td colspan="9"></td>
                            <td align="right" style="border-top-style:solid; border-top-width:1px; border-top-color:#333333">
                                <?php
                                $color_text = "normal_text_verd";
                                if ($total_outstanding > 0) {
                                    $color_text = "normal_text_red";
                                }
                                ?>
                                <span class="<?= $color_text ?>"><?= number_format($total_outstanding, 2, ".", ",") ?></span>&nbsp;
                            </td>
                            <td colspan="6"></td>
                        </tr>
                        <?php
                    }
                    ?>
                </table>
            </div>
        </td>
    </tr>
    <?php
    $div_num++;
}
?>