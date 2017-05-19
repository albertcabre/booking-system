<?php
require_once('functions.php');

validate_user();

$arrError = array();
$file_uploaded = FALSE;

if ($_FILES[picture] != "") {
    $old_picture = filter_input(INPUT_GET, "old_picture");
    $arrError = upload_file($_FILES[picture], "../residentsnh", $request[resident_id], 0, $old_picture);

    if ($arrError[error] == 0) {
        $file_uploaded = TRUE;
        $q = "UPDATE residents SET picture='{$arrError[message]}' WHERE resident_id=$request[resident_id]";
        mysqli_query($link, $q);
    }
}
?>
<!-- This is to force the browser to get the image from the web server and not to use any cache -->
<meta http-equiv="expires" content="0">



<SCRIPT language="JavaScript" src="js/calendario.js"></SCRIPT>
<script type="text/javascript">
    function upload() {
        document.miform.submit();
    }
</script>
<LINK href="css/netherhall.css" rel="stylesheet" type="text/css">
<br>
<?php
if ($request[resident_id]) {
    $r = mysqli_query($link, "SELECT * FROM residents WHERE resident_id=$request[resident_id]");
    $arrData = mysqli_fetch_assoc($r);
    $arrData = utf8_converter($arrData);
}
?>
<form name="miform" method="post" enctype="multipart/form-data">
    <table cellpadding="0" cellspacing="0" align="center">
        <input type="hidden" name="pagetoload" value="application_form_image.php">
        <input type="hidden" name="operation">
        <input type="hidden" name="resident_id" value="<?= $request[resident_id] ?>">
        <input type="hidden" name="old_picture" value="<?= $arrData[picture] ?>">
        <tr>
            <td valign="top">
                <table class="text_form" align="center" cellpadding="1" cellspacing="3" border="0">
                    <tr>
                        <td colspan="3" class="question" align="center">
                            <?php
                            if ($file_uploaded == FALSE) {
                                echo "Upload a picture for " . $arrData[name] . " " . $arrData[surname];
                            }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" align="center" class="question">
                            <?php
                            if ($file_uploaded == FALSE) {
                                ?>
                                <br><input type="file" name="picture"><br><br>
                                <?php
                            }

                            if ($arrError[error]) {
                                ?>
                                <br><br><span class="error_message"><?= $arrError[message] ?></span>
                                <?php
                            } elseif ($arrError[message] != "") {
                                ?>
                                <img src="../residentsnh/<?= $arrError[message] ?>" height="120" border="1" style="border-color:#2F2F5E"><br><br>
                                File uploaded!
                                <?php
                            }
                            ?>
                        </td>
                    </tr>
                </table>
            <td>
        <tr>
    </table>
</form>
<br>
<table align="center" cellpadding="5" cellspacing="0">
    <tr>
        <td align="center">
            <div class="button_off" onMouseOver="this.className = 'button_on'" onMouseOut="this.className = 'button_off'">
                <a href="admin.php?pagetoload=application_form.php&resident_id=<?= $request[resident_id] ?>&from=<?= $request[from] ?>"
                   class="button_link">Back</a>
            </div>
        </td>
        <?php
        if (!isset($_FILES[picture]) || $arrError[error]) {
            ?>
            <td align="center">
                <div class="button_off" onMouseOver="this.className = 'button_on'" onMouseOut="this.className = 'button_off'">
                    <a href="javascript:upload()" class="button_link">Upload</a>
                </div>
            </td>
            <?php
        }
        ?>
    </tr>
</table>