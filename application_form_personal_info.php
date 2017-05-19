<?php
require_once('functions.php');

validate_user();
?>
<table border="0" width="960" align="center" cellpadding="2" cellspacing="3" class="borde_gris">
    <tr>
        <td colspan="6" bgcolor="#999999" class="text_form"><span class="titol_taula_mogut">personal information</span></td>
    </tr>
    <tr>
        <td colspan="6" class="text_form" align="left">
            <input name="Edit" type="button" class="boton_edit_out" id="Edit" onclick="enableField()" value="Edit information"  onmouseover="this.className = 'boton_edit'" onmouseout="this.className = 'boton_edit_out'" />
            <input name="Save" class="boton_save_disabled" type="button" id="Save" onclick="save()" value="Save" disabled="disabled" onmouseover="this.className = 'boton_save'" onmouseout="this.className = 'boton_save_out'"/>
        </td>
    </tr>
    <tr>
        <td class="text_form" align="left">Name</td>
        <td class="text_form" align="left">
            <input type="text" name="name" size="25" value="<?= $arrData[name] ?>" disabled="disabled" class="normal_text" />
        </td>
        <td class="text_form" align="left">Surname</td>
        <td class="text_form" align="left">
            <input  name="surname" type="text" disabled="disabled" class="normal_text" value="<?= $arrData[surname] ?>" size="25" />
        </td>
        <td class="text_form" align="left">Date of birth</td>
        <td class="text_form" align="left">
            <?php
            $d_birth = substr($arrData[date_of_birth], 8, 2);
            $m_birth = substr($arrData[date_of_birth], 5, 2);
            $y_birth = substr($arrData[date_of_birth], 0, 4);
            ?>
            <select name="day" class="normal_text" disabled="disabled">
                <option></option>
                <?php
                for ($i = 1; $i <= 31; $i++) {
                    $dia = $i;
                    if ($i < 10) {
                        $dia = "0" . $i;
                    }
                    ?><option
                    <?php
                    if ($dia == $d_birth) {
                        echo "selected";
                    }
                    ?>><?= $dia ?></option><?php }
                ?>
            </select>
            <select name="month" class="normal_text" disabled="disabled">
                <option></option>
                <?php
                for ($i = 1; $i <= 12; $i++) {
                    $mes = $i;
                    if ($i < 10) {
                        $mes = "0" . $i;
                    }
                    ?><option <?php
                        if ($mes == $m_birth) {
                            echo "selected";
                        }
                        ?>><?= $mes ?></option><?php }
                    ?>
            </select>
            <select name="year" class="normal_text" disabled="disabled">
                <option></option>
                <?php
                $year = date("Y");
                for ($i = 1940; $i <= $year; $i++) {
                    ?><option
                        <?php
                        if ($i == $y_birth) {
                            echo "selected";
                        }
                        ?>><?= $i ?></option>
                <?php }
                ?>
            </select>
        </td>
    </tr>
    <tr>
        <td class="text_form" align="left">Address line 1</td>
        <td class="text_form" align="left">
            <input  name="address_line1" type="text" disabled="disabled" class="normal_text" value="<?= $arrData[address_line1] ?>" size="25"/>
        </td>
        <td class="text_form" align="left">Address line 2</td>
        <td class="text_form" align="left">
            <input  name="address_line2" type="text" disabled="disabled" class="normal_text" value="<?= $arrData[address_line2] ?>" size="25"/>
        </td>
        <td class="text_form" align="left">Postal code</td>
        <td class="text_form" align="left">
            <input  name="postal_code" type="text" disabled="disabled" class="normal_text" value="<?= $arrData[postal_code] ?>" size="20"/>
        </td>
    </tr>
    <tr>
        <td class="text_form" align="left">Town / city</td>
        <td class="text_form" align="left">
            <input name="city" type="text" disabled="disabled" class="normal_text" value="<?= $arrData[city] ?>" size="25"/>
        </td>
        <td class="text_form" align="left">County/Prov.</td>
        <td class="text_form" align="left">
            <input  name="county" type="text" disabled="disabled" class="normal_text" value="<?= $arrData[county] ?>" size="25" />
        </td>
        <td class="text_form" align="left">Country</td>
        <td class="text_form" align="left">
            <select name="country_id" disabled="disabled" class="normal_text">
                <option></option>
                <?php
                $r = mysqli_query($link, "SELECT * FROM countries ORDER BY country");
                while ($data = mysqli_fetch_assoc($r)) {
                    echo "<option value='$data[country_id]'";
                    if ($data[country_id] == $arrData[country_id]) {
                        echo 'selected';
                    }
                    echo ">$data[country]</option>";
                }
                ?>
            </select>
        </td>
    </tr>
    <tr>
        <td class="text_form" align="left">Nationality</td>
        <td class="text_form" align="left">
            <input  name="nationality" type="text" disabled="disabled" class="normal_text" value="<?= $arrData[nationality] ?>" size="25"/>
        </td>
        <td class="text_form" align="left">Telephone 1</td>
        <td class="text_form" align="left">
            <input  name="telephone" type="text" disabled="disabled" class="normal_text" value="<?= $arrData[telephone] ?>" size="25"/>
        </td>
        <td class="text_form" align="left">Telephone 2</td>
        <td class="text_form" align="left">
            <input  name="mobile" type="text" disabled="disabled" class="normal_text" value="<?= $arrData[mobile] ?>" size="20"/>
        </td>
    </tr>
    <tr>
        <td class="text_form" align="left">E-mail </td>
        <td class="text_form" align="left">
            <input  name="email" type="text" disabled="disabled" class="normal_text" value="<?= $arrData[email] ?>" size="25"/>
        </td>
        <td class="text_form" align="left">Religion</td>
        <td class="text_form" align="left">
            <input  name="r" type="text" disabled="disabled" class="normal_text" value="<?= $arrData[r] ?>" size="25"/>
        </td>
        <td class="text_form" align="left">UK Phone Number</td>
        <td class="text_form" align="left">
            <input name="ukphone" type="text" disabled="disabled" class="normal_text" id="ukphone" value="<?= $arrData[ukphone] ?>" size="20"/>
        </td>
    </tr>
    <tr>
        <td class="text_form" align="left">College</td>
        <td class="text_form" align="left">
            <input  name="college" type="text" disabled="disabled" class="normal_text" value="<?= $arrData[college] ?>" size="25" />
        </td>
        <td class="text_form" align="left">Subject</td>
        <td class="text_form" align="left">
            <input  name="subject" type="text" disabled="disabled" class="normal_text" value="<?= $arrData[subject] ?>" size="25"/>
        </td>
        <td class="text_form" align="left">Marital status</td>
        <td class="text_form" align="left">
            <select name="marital_status" class="normal_text" disabled="disabled">
                <option></option>
                <option <?php
                    if ($arrData[marital_status] == "Single") {
                        echo "selected";
                    }
                    ?>>Single</option>
                <option <?php
                    if ($arrData[marital_status] == "Married") {
                        echo "selected";
                    }
                    ?>>Married</option>
                <option <?php
                    if ($arrData[marital_status] == "Divorced") {
                        echo "selected";
                    }
                    ?>>Divorced</option>
            </select>
        </td>
    </tr>
    <tr>
        <td class="text_form" align="left">Course <span class="small"><br>eg BA, MSc, PhD, etc</span> </td>
        <td class="text_form" align="left">
            <input name="mycourse" type="text" value="<?= $arrData[course] ?>" disabled="disabled" size="25" />
        </td>
        <td class="text_form" align="left">Academic year <br><span class="small">eg 1, 2, 3</span> </td>
        <td class="text_form" align="left">
            <input name="academic_year" type="text" value="<?= $arrData[academic_year] ?>" disabled="disabled" size="25" />
        </td>
        <td class="text_form" align="left">Color</td>
        <td valign="middle" class="text_form" align="left">
            <input name="color" type="text" class="normal_text" id="color" value="<?= $arrData[color] ?>" size="6" disabled="disabled" />&nbsp;
            <input name="mostra_color" type="text" disabled="disabled" class="mostra_color" id="mostra_color"
                   style="background-color:<?= $arrData[color] ?>" value="" size="1" />&nbsp;
            <input name="paleta" value="" type="button" onclick="pickerPopup302('color', 'mostra_color');" class="oculta" disabled="disabled"/>
        </td>
    </tr>
    <tr>
        <td class="text_form" align="left">Arrival </td>
        <td class="text_form" align="left"><input name="arrival" type="text" value="<?= mostrar_fecha($arrData[arrival]) ?>" size="25" disabled="disabled" /></td>
        <td class="text_form" align="left">Departure </td>
        <td class="text_form" align="left"><input name="departure" type="text" value="<?= mostrar_fecha($arrData[departure]) ?>" size="25" disabled="disabled" /></td>
        <td class="text_form" align="left">&nbsp;</td>
        <td class="text_form" align="left">&nbsp;</td>
    </tr>
</table>