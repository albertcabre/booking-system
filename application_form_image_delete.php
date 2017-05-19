<?php
require_once('functions.php');

validate_user();

if ($request[operation] == "delete") {
    $dir = getcwd();
    $path = $dir . "../residentsnh/" . $request[picture];
    $delete_result = unlink($path);
    if ($delete_result == TRUE) {
        $q = "UPDATE residents SET picture='' WHERE resident_id=$request[resident_id]";
        mysql_query($q);
    }
}
?>
<SCRIPT language="JavaScript" src="js/calendario.js"></SCRIPT>
<script type="text/javascript">
    function delete_image() {
        document.miform.operation.value = 'delete';
        document.miform.submit();
    }
</script>
<LINK href="css/netherhall.css" rel="stylesheet" type="text/css">
<br>
<?php
if ($request[resident_id] > 0) {
    $r = mysql_query("SELECT * FROM residents WHERE resident_id=$request[resident_id]");
    $arrData = mysql_fetch_assoc($r);
    $arrData = utf8_converter($arrData);
}
?>
<table cellpadding="0" cellspacing="0" align="center">
    <form name="miform" method="post" enctype="multipart/form-data">
        <input type="hidden" name="pagetoload" value="application_form_image_delete.php">
        <input type="hidden" name="operation">
        <input type="hidden" name="resident_id" value="<?= $request[resident_id] ?>">
        <input type="hidden" name="picture" value="<?= $arrData[picture] ?>">
        <tr>
            <td valign="top">
                <table class="text_form" align="center" cellpadding="1" cellspacing="3" border="0">
                    <tr>
                        <td colspan="3" class="question" align="center">
                            <?php
                            if ($request[operation] == 'delete') {
                                if ($delete_result == TRUE) {
                                    echo "Picture deleted";
                                } else {
                                    echo "Sorry the system can not delete this picture";
                                }
                            } else {
                                echo "Do you want to delete the picture of " . $arrData[name] . " " . $arrData[surname] . "?";
                            }
                            ?>
                        </td>
                    </tr>
                    <?php
                    if ($request[operation] != 'delete' || ($request[operation] == 'delete' && $delete_result == FALSE)) {
                        ?>
                        <tr>
                            <td colspan="3" align="center" class="question">
                                <br><img src="../residentsnh/<?= $arrData[picture] ?>" height="120" border="1" style="border-color:#2F2F5E">
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </table>
            </td>
        </tr>
    </form>
</table>
<br>
<table align="center" cellpadding="5" cellspacing="0">
    <tr>
        <td align="center">
            <div class="button_off" onMouseOver="this.className = 'button_on'" onMouseOut="this.className = 'button_off'">
                <a href="admin.php?pagetoload=application_form.php&resident_id=<?= $request[resident_id] ?>&from=<?= $request[from] ?>" class="button_link">Back</a>
            </div>
        </td>
        <?php
        if ($request[operation] != 'delete' || ($request[operation] == 'delete' && $delete_result == FALSE)) {
            ?>
            <td align="center">
                <div class="button_off" onMouseOver="this.className = 'button_on'" onMouseOut="this.className = 'button_off'">
                    <a href="javascript:delete_image()" class="button_link">Delete</a>
                </div>
            </td>
            <?php
        }
        ?>
    </tr>
</table>
