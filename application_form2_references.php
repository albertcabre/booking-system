<?php
require_once('functions.php');

validate_user();
?>
<td style="padding: 10px">
    <!-- First Reference box -->
    <table width="100%" bgcolor="#CCCCCC">
        <tr>
            <td class="text_form" align="left">&nbsp;</td>
        </tr>
        <tr>
            <td class="text_form" align="left"><span style="padding-left:15px">First reference</span></td>
        </tr>
        <tr>
            <td align="left" class="text_form"><span style="padding-left:30px">Name</span></td>
        </tr>
        <tr>
            <td align="left" style="padding-left:30px;" class="text_field_additional">
                <input type="text" name="reference1_name" value="<?= $arrData[reference1_name] ?>" size="50" disabled="disabled" />
            </td>
        </tr>
        <tr>
            <td align="left" valign="top" class="text_form"><span style="padding-left:30px">Address</span></td>
        </tr>
        <tr>
            <td align="left" class="text_field_additional" style="padding-left: 28px">
                <textarea name="reference1_address" cols="49" rows="3" disabled="disabled"><?= $arrData[reference1_address] ?></textarea>
            </td>
        </tr>
        <tr>
            <td class="text_form" align="left"><span style="padding-left:30px">Telephone</span></td>
        </tr>
        <tr>
            <td align="left" style="padding-left:30px;" class="text_field_additional">
                <input type="text" name="reference1_telephone" value="<?= $arrData[reference1_telephone] ?>" size="50" disabled="disabled"/>
            </td>
        </tr>
        <tr>
            <td class="text_form" align="left"><span style="padding-left:30px">Email</span></td>
        </tr>
        <tr>
            <td align="left" style="padding-left:30px;" class="text_field_additional">
                <input type="text" name="reference1_email" value="<?= $arrData[reference1_email] ?>" size="50" disabled="disabled"/>
            </td>
        </tr>
        <tr>
            <td class="text_form" align="left"><span style="padding-left:30px">Referee&acute;s relationship to applicant</span></td>
        </tr>
        <tr>
            <td align="left" style="padding-left:30px;" class="text_field_additional">
                <input type="text" name="reference1_relationship" value="<?= $arrData[reference1_relationship] ?>" size="50" disabled="disabled"/>
            </td>
        </tr>
        <tr>
            <td class="text_form" align="left">&nbsp;</td>
        </tr>
    </table>
    <!-- End First Reference box -->
</td>
<td style="padding: 10px">
    <!-- Second Reference box -->
    <table width="100%" bgcolor="#CCCCCC">
        <tr>
            <td class="text_form" align="left">&nbsp;</td>
        </tr>
        <tr>
            <td class="text_form" align="left"><span style="padding-left:15px">Second reference</span></td>
        </tr>
        <tr>
            <td align="left" class="text_form"><span style="padding-left:30px">Name</span></td>
        </tr>
        <tr>
            <td align="left" style="padding-left:30px;" class="text_field_additional">
                <input type="text" name="reference2_name" value="<?= $arrData[reference2_name] ?>" size="50" disabled="disabled" />
            </td>
        </tr>
        <tr>
            <td align="left" valign="top" class="text_form"><span style="padding-left:30px">Address</span></td>
        </tr>
        <tr>
            <td align="left" class="text_field_additional" style="padding-left: 28px">
                <textarea name="reference2_address" cols="49" rows="3" disabled="disabled"><?= $arrData[reference2_address] ?></textarea>
            </td>
        </tr>
        <tr>
            <td class="text_form" align="left"><span style="padding-left:30px">Telephone</span></td>
        </tr>
        <tr>
            <td align="left" style="padding-left:30px;" class="text_field_additional">
                <input type="text" name="reference2_telephone" value="<?= $arrData[reference2_telephone] ?>" size="50" disabled="disabled"/>
            </td>
        </tr>
        <tr>
            <td class="text_form" align="left"><span style="padding-left:30px">Email</span></td>
        </tr>
        <tr>
            <td align="left" style="padding-left:30px;" class="text_field_additional">
                <input type="text" name="reference2_email" value="<?= $arrData[reference2_email] ?>" size="50" disabled="disabled"/>
            </td>
        </tr>
        <tr>
            <td class="text_form" align="left"><span style="padding-left:30px">Referee&acute;s relationship to applicant</span></td>
        </tr>
        <tr>
            <td align="left" style="padding-left:30px;" class="text_field_additional">
                <input type="text" name="reference2_relationship" value="<?= $arrData[reference2_relationship] ?>" size="50" disabled="disabled" />
            </td>
        </tr>
        <tr>
            <td class="text_form" align="left">&nbsp;</td>
        </tr>
    </table>
    <!-- End Second Reference box -->
</td>