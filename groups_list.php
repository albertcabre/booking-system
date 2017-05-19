<?php
require_once('functions.php');

validate_user();

if ($request[operation] == "delete") {
    // Delete the group.
    mysql_query("DELETE FROM groups WHERE group_id={$request[group_id]}");
    // For each member of the group, deletes the info of the resident and his booking.
    $r = mysql_query("SELECT resident_id FROM residents_groups WHERE group_id={$request[group_id]}");
    while ($arrResident = mysql_fetch_assoc($r)) {
        $q2 = "DELETE FROM residents WHERE resident_id={$arrResident[resident_id]}";
        mysql_query($q2);
        $q3 = "DELETE FROM bookings WHERE resident_id={$arrResident[resident_id]} AND group_id={$request[group_id]}";
        mysql_query($q3);
    }
    // Delete the resident from this group.
    $qdr = "DELETE * FROM residents_groups WHERE group_id={$request[group_id]}";
    mysql_query($qdr);
}
?>
<LINK href="css/netherhall.css" rel="stylesheet" type="text/css">
<script language="javascript">
    function delete_group(group_id, group_name) {
        confirmation = confirm("Do you want to delete the group " + group_name + "?");
        if (confirmation) {
            document.myform.operation.value = "delete";
            document.myform.group_id.value = group_id;
            document.myform.submit();
        }
    }
</script>
<?php
button("admin.php?pagetoload=new_group.php", "New Group");

$today = date("Y", time()) . "-" . date("m", time()) . "-" . date("d", time());
$r = mysql_query("SELECT * FROM groups ORDER BY arrival");
if (mysql_num_rows($r) == 0) {
    ?><p align="center" class="question">There are no groups</p><?php
} else {
    ?>
    <table align="center" border="0" cellpadding="4" cellspacing="0">
        <form name="myform" method="post" action="admin.php">
            <input type="hidden" name="pagetoload" value="groups_list.php">
            <input type="hidden" name="operation">
            <input type="hidden" name="group_id">
        </form>
        <tr>
            <td class="titol_taula_list">Name</td>
            <td class="titol_taula_list">Arrival</td>
            <td class="titol_taula_list">Departure</td>
            <td class="titol_taula_list"></td>
        </tr>
        <?php
        while ($arrData = mysql_fetch_assoc($r)) {
            ?>		
            <tr class="row1" onMouseOver="this.className = 'row_selected'" onMouseOut="this.className = 'row1'">
                <td class="cell2 left">
                    <a href="admin.php?pagetoload=groups_members.php&group_id=<?= $arrData[group_id] ?>" class="table_link2"><?= $arrData[name] ?></a>
                </td>
                <td class="cell2 left"><?= mostrar_fecha($arrData[arrival]) ?></td>
                <td class="cell2 left"><?= mostrar_fecha($arrData[departure]) ?></td>
                <td class="cell2">
                    <a href="javascript:delete_group(<?= $arrData[group_id] ?>,'<?= $arrData[name] ?>')"><img src="imgs/trash_16x16.gif" border="0"></a>
                </td>
            </tr>
            <?php
        }
        ?>
    </table>
    <?php
}?>