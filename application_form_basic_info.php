<table width="99%" border="0" cellspacing="0" cellpadding="0">
    <tr>
        <td colspan="2" align="center">
            <?php
            if ($error) {
                ?><span class="error_message">There is an error in the accounts section. Please check below.</span><?php
            }
            ?>
        </td>
    </tr>
    <tr>
        <td width="10%">
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
        <td valign="bottom">
            <div align="left">
                <span class="questionCopia2">&nbsp;<?php echo$arrData[name] . " " . $arrData[surname]; ?></span>
            </div>
        </td>
    </tr>
    <tr>
        <td>
            <div align="left">
                <?php $href = "admin.php?pagetoload=application_form_image.php&resident_id=$request[resident_id]&from=$request[from]"; ?>
                <a href="<?= $href ?>" class="table_link2_small">Upload picture</a>
                <?php
                if ($arrData[picture] != "") {
                    ?>
                    <br>
                    <a href="admin.php?pagetoload=application_form_image_delete.php&resident_id=<?= $request[resident_id] ?>" class="table_link2_small">Delete picture</a>
                    <?php
                }
                ?>
            </div>
        </td>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td colspan="2">&nbsp;</td>
    </tr>
    <tr>
        <td colspan="2">
            <div class="additional_active">Basic information</div>
            <div class="additional_in" onmouseover="this.className = 'additional_out'"
                 onmouseout="this.className = 'additional_in'"
                 onclick="window.open('admin.php?pagetoload=application_form2.php&resident_id=<?= $request[resident_id] ?>', '_self');">Additional information</div>
        </td>
    </tr>
</table>