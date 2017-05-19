<?php
require_once('functions.php');

validate_user();
?>
<table width="960" border="0" cellpadding="2" cellspacing="3" class="borde_gris">
    <tr>
        <td colspan="2" bgcolor="#999999" class="text_form"><span class="titol_taula_mogut">accounts</span><a name="aaa"></a></td>
    </tr>
    <tr>
        <td align="left" valign="bottom" class="question">
            <input name="accomodation" type="button" class="boton_accomodation_out" id="accomodation"
                   onclick="window.open('admin.php?pagetoload=application_form_dates.php&amp;resident_id=<?= $request[resident_id] ?>&amp;operation=new', '_self');"
                   value="New account"  onmouseover="this.className = 'boton_accomodation'" onmouseout="this.className = 'boton_accomodation_out'" />
        </td>
        <td align="right">Advance Payment &nbsp;
            <input type="text" name="deposit" size="5" value="<?= $arrData[deposit] ?>" <?= $error_de ?>>&nbsp;
            <a href="javascript:save_deposit(<?= $request[resident_id] ?>)" title="Save deposit">
                <img src="imgs/disk.png" border="0" align="absmiddle">
            </a>
        </td>
    </tr>
    <?php
    $total_outstanding = 0;
    if ($request[resident_id]) {
        /**
         * Accounts Data
         */
        require 'application_form_accounts_data.php';

        /**
         * Accounts Finished
         */
        require 'application_form_accounts_finished.php';
    }
?>
</table>