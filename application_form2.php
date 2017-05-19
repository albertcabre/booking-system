<?php
require_once('functions.php');

validate_user();

if ($request[operation] == "save") {
    $q = "UPDATE residents SET
            school_attended = \"$request[school_attended]\",
            school_examinations = \"$request[school_examinations]\",
            universities_attended = \"$request[universities_attended]\",
            qualifications_obtained = \"$request[qualifications_obtained]\",
            scholarship_help = \"$request[scholarship_help]\",
            occupation = \"$request[occupation]\",
            positions = \"$request[positions]\",
            name_parent = \"$request[name_parent]\",
            occupation_parent = \"$request[occupation_parent]\",
            reference1_name = \"$request[reference1_name]\",
            reference1_address = \"$request[reference1_address]\",
            reference1_telephone = \"$request[reference1_telephone]\",
            reference1_email = \"$request[reference1_email]\",
            reference1_relationship = \"$request[reference1_relationship]\",
            reference2_name = \"$request[reference2_name]\",
            reference2_address = \"$request[reference2_address]\",
            reference2_telephone = \"$request[reference2_telephone]\",
            reference2_email = \"$request[reference2_email]\",
            reference2_relationship = \"$request[reference2_relationship]\",
            how_can_contribute = \"$request[how_can_contribute]\",
            how_benefit_you = \"$request[how_benefit_you]\",
            serious_illness = \"$request[serious_illness]\",
            special_dietary = \"$request[special_dietary]\",
            intended_profession = \"$request[intended_profession]\",
            interests = \"$request[interests]\",
            sports = \"$request[sports]\",
            further_info = \"$request[further_info]\",
            informed_by = \"$request[informed_by]\"
            WHERE resident_id = $request[resident_id]";

    $r = mysqli_query($link, $q);
}
?>
<script type="text/javascript">
    function save() {
        document.miform.operation.value = "save";
        document.miform.submit();
    }
</script>

<SCRIPT src="js/calendario.js"></SCRIPT>
<script src="js/picker.js"></script>
<script src="jsp/taules_application2.jsp"></script>

<LINK href="css/netherhall.css" rel="stylesheet" type="text/css">
<br>
<?php
if ($request[resident_id]) {
    $r = mysqli_query($link, "SELECT * FROM residents WHERE resident_id=$request[resident_id]");
    $arrData = mysqli_fetch_assoc($r);
    $arrData = utf8_converter($arrData);
}
?>
<table width="930" border="0" cellspacing="0" cellpadding="0" align="center">
    <tr>
        <td colspan="2" align="center">
            <table width="99%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td width="15%">
                        <div align="left">
                            <?php
                            if ($arrData[picture] != "" && is_file("../residentsnh/" . $arrData[picture])) {
                                ?><img src="../residentsnh/<?= $arrData[picture] ?>" height="120" border="1" style="border-color:#2F2F5E" /><?php
                            } else {
                                ?><img src="imgs/no_picture.png" height="120" border="1" style="border-color:#2F2F5E" /><?php
                            }
                            ?>
                        </div>
                    </td>
                    <td width="85%" valign="bottom">
                        <div align="left">
                            <span class="questionCopia2">
                                <?= $arrData[name] ?>
                                <?= $arrData[surname] ?>
                            </span>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td><div align="left">&nbsp;</div></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="2">&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="2">
                        <div class="additional_in2" onmouseover="this.className = 'additional_out2'"
                             onmouseout="this.className = 'additional_in2'"
                             onclick="window.open('admin.php?pagetoload=application_form.php&resident_id=<?= $request[resident_id] ?>', '_self');">Basic information
                        </div>
                        <div class="additional_active2">Additional information</div>
                    </td>
                </tr>
            </table>
            <div align="center"></div>
        </td>
    </tr>
    <tr>
        <td colspan="2" align="center">
            <table width="99%" border="0" align="center" cellpadding="0" cellspacing="0" class="borde_blau">
                <tr>
                    <td colspan="5" align="center">&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="5" align="center">
                        <table width="96%" cellpadding="2" cellspacing="3" class="borde_gris">
                            <form name="miform" id="miform" method="post">
                                <input type="hidden" name="pagetoload" value="application_form2.php" />
                                <input type="hidden" name="operation">
                                <input type="hidden" name="resident_id" value="<?= $request[resident_id] ?>" />
                                <tr>
                                    <td colspan="2" bgcolor="#999999"><span class="titol_taula_mogut">application</span></td>
                                </tr>
                                <tr>
                                    <td colspan="2" align="left"><input name="Edit" type="button" class="boton_edit_out" id="Edit" onclick="javascript:enableField3()" value="Edit information"  onmouseover="this.className = 'boton_edit'" onmouseout="this.className = 'boton_edit_out'" />
                                        <input name="Save" class="boton_save_disabled" type="submit" id="Save" onclick="javascript:save()" value="Save" onmouseover="this.className = 'boton_save'" onmouseout="this.className = 'boton_save_out'" disabled="disabled"/>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="50%" class="text_form" align="left"><span style="padding-left:16px">Schools attended (with dates) </span></td>
                                    <td class="text_form" align="left"><span style="padding-left:15px">School examinations (with grades) </span></td>
                                </tr>
                                <tr>
                                    <td align="left" style="padding-left:15px;" class="text_field_additional">
                                        <textarea name="school_attended" cols="54" rows="4" class="text_field_additional" disabled="disabled"><?= $arrData[school_attended] ?></textarea>
                                    </td>
                                    <td align="left" style="padding-left:16px;" class="text_field_additional">
                                        <textarea name="school_examinations" cols="54" rows="4" class="text_field_additional" disabled="disabled"><?= $arrData[school_examinations] ?></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text_form" align="left"><span style="padding-left:15px">Universities attended </span></td>
                                    <td class="text_form" align="left"><span style="padding-left:16px">Qualifications obtained (with grades) </span></td>
                                </tr>
                                <tr>
                                    <td align="left" style="padding-left:15px;" class="text_field_additional">
                                        <textarea name="universities_attended" cols="54" rows="4" disabled="disabled"><?= $arrData[universities_attended] ?></textarea>
                                    </td>
                                    <td align="left" style="padding-left:16px;" class="text_field_additional">
                                        <textarea name="qualifications_obtained" cols="54" rows="4" disabled="disabled"><?= $arrData[qualifications_obtained] ?></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text_form" align="left"><span style="padding-left:15px">Scholarship(s) help </span></td>
                                    <td class="text_form" align="left"><span style="padding-left:16px">Occupation, if any, since leaving school </span></td>
                                </tr>
                                <tr>
                                    <td align="left" style="padding-left:15px;" class="text_field_additional">
                                        <textarea name="scholarship_help" cols="54" rows="4" disabled="disabled"><?= $arrData[scholarship_help] ?></textarea>
                                    </td>
                                    <td align="left" style="padding-left:16px;" class="text_field_additional">
                                        <textarea name="occupation" cols="54" rows="4" disabled="disabled"><?= $arrData[occupation] ?></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="text_form" align="left">
                                        <span style="padding-left:15px">Positions of responsibility held at school and/or college </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2" align="left" style="padding-left:15px;" class="text_field_additional">
                                        <textarea name="positions" id="positions" cols="117" rows="5" disabled="disabled"><?= $arrData[positions] ?></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text_form" align="left"><span style="padding-left:15px">Name of parent(s) or Guardian </span></td>
                                    <td class="text_form" align="left"><span style="padding-left:16px">Occupation</span></td>
                                </tr>
                                <tr>
                                    <td align="left" style="padding-left:15px;" class="text_field_additional">
                                        <input type="text" name="name_parent" value="<?= $arrData[name_parent] ?>" size="55" disabled="disabled"/>
                                    </td>
                                    <td align="left" style="padding-left:16px;" class="text_field_additional">
                                        <input type="text" name="occupation_parent" value="<?= $arrData[occupation_parent] ?>" size="55" disabled="disabled" />
                                    </td>
                                </tr>
                                <tr>
                                    <?php
                                    require_once("application_form2_references.php");
                                    ?>
                                </tr>
                                <tr>
                                    <td class="text_form" align="left"><span style="padding-left:15px">How do you think you can contribute to life at Netherhall House?</span></td>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td colspan="2" align="left" style="padding-left:15px;" class="text_field_additional">
                                        <textarea name="how_can_contribute" cols="117" rows="4" disabled="disabled"><?= $arrData[how_can_contribute] ?></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text_form" align="left"><span style="padding-left:15px">How do you think living in Netherhall House is going to benefit you?</span></td>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td colspan="2" align="left" style="padding-left:15px;" class="text_field_additional">
                                        <textarea name="how_benefit_you" cols="117" rows="4" disabled="disabled"><?= $arrData[how_benefit_you] ?></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text_form" align="left"><span style="padding-left:15px">State any serious illness (with dates)</span></td>
                                    <td align="left" class="text_form"><span style="padding-left:16px">Special dietary requirements</span></td>
                                </tr>
                                <tr>
                                    <td align="left" style="padding-left:15px;" class="text_field_additional">
                                        <textarea name="serious_illness" cols="54" rows="4" disabled="disabled"><?= $arrData[serious_illness] ?></textarea>
                                    </td>
                                    <td align="left" style="padding-left:16px;" class="text_field_additional">
                                        <textarea name="special_dietary" cols="54" rows="4" disabled="disabled"><?= $arrData[special_dietary] ?></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text_form" align="left"><span style="padding-left:15px">Intended profession of ocupation, if known </span></td>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td colspan="2" align="left" style="padding-left:15px;" class="text_field_additional">
                                        <textarea name="intended_profession" cols="117" rows="4" disabled="disabled"><?= $arrData[intended_profession] ?></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text_form" align="left"><span style="padding-left:15px">Intellectual or cultural interests </span></td>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td colspan="2" align="left" style="padding-left:15px;" class="text_field_additional">
                                        <textarea name="interests" cols="117" rows="4" disabled="disabled"><?= $arrData[interests] ?></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text_form" align="left"><span style="padding-left:15px">Sports played and/or outdoor interests </span></td>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td colspan="2" align="left" style="padding-left:15px;" class="text_field_additional">
                                        <textarea name="sports" cols="117" rows="4" disabled="disabled"><?= $arrData[sports] ?></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text_form" align="left"><span style="padding-left:15px">Any further information </span></td>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td colspan="2" align="left" style="padding-left:15px;" class="text_field_additional">
                                        <textarea name="further_info" cols="117" rows="4" disabled="disabled"><?= $arrData[further_info] ?></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text_form" align="left"><span style="padding-left:15px">I was informed of Netherhall House by </span></td>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td colspan="2" align="left" style="padding-left:15px;" class="text_field_additional">
                                        <textarea name="informed_by" cols="117" rows="4" disabled="disabled"><?= $arrData[informed_by] ?></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="left">
                                        <input name="Edit2" type="button" class="boton_edit_out" id="Edit2" onclick="javascript:enableField3()" value="Edit information"  onmouseover="this.className = 'boton_edit'" onmouseout="this.className = 'boton_edit_out'" />
                                        <input name="Save2" class="boton_save_disabled" type="submit" id="Save2" onclick="javascript:save()" value="Save" onmouseover="this.className = 'boton_save'" onmouseout="this.className = 'boton_save_out'" disabled="disabled"/>
                                    </td>
                                    <td>&nbsp;</td>
                                </tr>
                            </form>
                        </table>
                        <span class="text_form"></span>
                    </td>
                </tr>
                <tr>
                    <td colspan="5" align="center">&nbsp;</td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td colspan="2">&nbsp;</td>
    </tr>
    <tr>
        <td width="50%" align="left">
            <input name="back" type="button" class="boton_back_out" id="back"
                   onclick="document.location = 'admin.php?pagetoload=<?= $_SESSION[from_nth] ?>'"
                   value="Back"  onmouseover="this.className = 'boton_back'"
                   onmouseout="this.className = 'boton_back_out'"/>
        </td>
        <td width="" align="right">&nbsp;</td>
    </tr>
</table>