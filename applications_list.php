<?php
require_once('functions.php');

validate_user();
?>
<LINK href="css/netherhall.css" rel="stylesheet" type="text/css">
<script language="javascript">
    function update() {
        document.myform.operation.value = "update";
        document.myform.submit();
    }
</script>
<?php
$error = FALSE;
if ($request[operation] == 'update') {
    foreach ($request as $key => $value) {
        if (substr($key, 0, 11) == "resident_id") {
            $id = $value;

            // Rules Date
            $rules_date = $request['rdate' . $id];
            if ($rules_date == "" || ($rules_date != "" && valid_date($rules_date))) {
                $rules_date = change_format_date($rules_date);
                $q = "UPDATE residents SET rules_date='$rules_date' WHERE resident_id=$id";
                $r = mysqli_query($q);
            } else {
                $error = TRUE;
            }

            // Offered Date
            $offered_date = $request['odate' . $id];
            if ($offered_date == "" || ($offered_date != "" && valid_date($offered_date))) {
                $offered_date = change_format_date($offered_date);
                $q = "UPDATE residents SET offered_date='$offered_date' WHERE resident_id=$id";
                $r = mysqli_query($q);
            } else {
                $error = TRUE;
            }

            $q1 = "UPDATE residents SET rules=0 WHERE resident_id=$id";
            //ver("q",$q);
            $r1 = mysqli_query($q1);

            $q2 = "UPDATE residents SET interview=0 WHERE resident_id=$id";
            //ver("q",$q);
            $r2 = mysqli_query($q2);

            $q3 = "UPDATE residents SET offered=0 WHERE resident_id=$id";
            //ver("q",$q);
            $r3 = mysqli_query($q3);
        }

        if (substr($key, 0, 3) == "rul") {
            $q = "UPDATE residents SET rules=1 WHERE resident_id=$id";
            //ver("q",$q);
            $r = mysqli_query($q);
        }
        if (substr($key, 0, 3) == "int") {
            $q = "UPDATE residents SET interview=1 WHERE resident_id=$id";
            //ver("q",$q);
            $r = mysqli_query($q);
        }
        if (substr($key, 0, 3) == "off") {
            $q = "UPDATE residents SET offered=1 WHERE resident_id=$id";
            //ver("q",$q);
            $r = mysqli_query($q);
        }
    }
}

if ($request[sort_by] == "date") {
    $sort = "ORDER BY resident_id DESC";
} elseif ($request[sort_by] == "name") {
    $sort = "ORDER BY surname, name DESC";
} else {
    $sort = "ORDER BY resident_id DESC";
}
$r = mysqli_query("SELECT r.*, c.country FROM residents r LEFT JOIN countries c ON r.country_id = c.country_id WHERE (r.status IS NULL || r.status = '') $sort");
if (mysqli_num_rows($r)) {
    ?>
    <div align="center" class="question">Received Applications<?= " (" . mysqli_num_rows($r) . ")" ?></div>
    <?php
    if ($error == TRUE) {
        ?><div align="center" class="error_message">Can't save date as it is not valid!<br>Valid format: dd/mm/yyyy</div><?php
    }
    ?>
    <br>
    <table width="1200" align="center" border="0" cellpadding="0" cellspacing="0">
        <form name="myform" method="post" action="admin.php">
            <input type="hidden" name="pagetoload" value="applications_list.php">
            <input type="hidden" name="operation">
            <tr valign="middle">
                <td class="image_cell"></td>
                <td class="titol_taula_list">
                    <a href="admin.php?pagetoload=applications_list.php&sort_by=date" class="header_link2">App. Date</a>
                </td>
                <td class="titol_taula_list">
                    <a href="admin.php?pagetoload=applications_list.php&sort_by=name" class="header_link2">Name</a>
                </td>
                <td class="titol_taula_list">Arrival</td>
                <td class="titol_taula_list">Departure</td>
                <td class="titol_taula_list">Country</td>
                <td class="titol_taula_list">College</td>
                <td class="titol_taula_list">Subject</td>
                <td class="titol_taula_list">Rules</td>
                <td class="titol_taula_list">Interview</td>
                <td class="titol_taula_list">Offered</td>
            </tr>
            <?php
        }

        while ($arrData = mysqli_fetch_assoc($r)) {
            ?>
            <input type="hidden" name="resident_id_<?= $arrData[resident_id] ?>" value="<?= $arrData[resident_id] ?>">
            <tr class="row1" onMouseOver="this.className = 'row_selected'" onMouseOut="this.className = 'row1'">
                <td class="cell2">
                    <?php
                    if ($arrData[picture] != "") {
                        echo "<img src='../residentsnh/" . $arrData[picture] . "' width='25' >";
                    } else {
                        echo "<img src='imgs/no_picture.png' width='25' border='0'>";
                    }
                    ?>
                </td>
                <td class="cell2" align="left"><?= mostrar_fecha(substr($arrData[application_date], 0, 10)) . " " . substr($arrData[application_date], 11, 8) ?></td>
                <td class="cell2" align="left"><a href="admin.php?pagetoload=application_form.php&resident_id=<?= $arrData[resident_id] ?>&from=applications_list.php" class="table_link2"><?= $arrData[surname] . ", " . $arrData[name] ?></a></td>
                <td class="cell2" align="left"><?= mostrar_fecha(substr($arrData[arrival], 0, 10)) ?></td>
                <td class="cell2" align="left"><?= mostrar_fecha(substr($arrData[departure], 0, 10)) ?></td>
                <td class="cell2" align="left"><?= $arrData[country] ?></td>
                <td class="cell2" align="left"><?= $arrData[college] ?></td>
                <td class="cell2" align="left"><?= $arrData[subject] ?></td>
                <td class="cell2" align="left">
                    <input type="checkbox" name="rul<?= $arrData[resident_id] ?>" <? if ($arrData[rules]) echo 'checked'; ?>>
                    <?php
                    $rules_date = $request['rdate' . $arrData[resident_id]];
                    //echo "rules_date=($rules_date)<br>";
                    if ($rules_date != "" && !valid_date($rules_date)) {
                        echo "<br><font color=red>Invalid Date!</font>";
                    } else {
                        $rules_date = mostrar_fecha(substr($arrData[rules_date], 0, 10));
                    }
                    ?>
                           <input type="text" name="rdate<?= $arrData[resident_id] ?>" value="<?= $rules_date ?>" size="8">
                </td>
                <td class="cell2" align="left">
                    <input type="checkbox" name="int<?= $arrData[resident_id] ?>" <?php if ($arrData[interview]) echo 'checked'; ?>>
                </td>
                <td class="cell2" align="left">
                    <input type="checkbox" name="off<?= $arrData[resident_id] ?>" <?php if ($arrData[offered]) echo 'checked'; ?>>
                    <?php
                    $offered_date = $request['odate' . $arrData[resident_id]];
                    if ($offered_date != "" && !valid_date($offered_date)) {
                        echo "<br><font color=red>Invalid Date!</font>";
                    } else {
                        $offered_date = mostrar_fecha(substr($arrData[offered_date], 0, 10));
                    }
                    ?>
                    <input type="text" name="odate<?= $arrData[resident_id] ?>" value="<?= $offered_date ?>" size="8">
                </td>
            </tr>
            <?php
        }

        if (mysqli_num_rows($r)) {
            ?>
        </form>
    </table>
    <br>
    <table align="center" cellpadding="5" cellspacing="0">
        <tr>
            <td align="center">
                <div class="button_off" onMouseOver="this.className = 'button_on'" onMouseOut="this.className = 'button_off'">
                    <a href="javascript:update()" class="button_link">Update</a>
                </div>
            </td>
        </tr>
    </table>
    <?php
} else {
    ?><br><br><div align="center" class="question">There are no received applications</div><br><br><?php
}
?>