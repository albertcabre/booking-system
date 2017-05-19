<?php
require_once('functions.php');

validate_user();

if ($request[operation]=="save") {
	foreach ($request as $key => $value) {
		if (substr($key,0,14)=="group_resident") {
			$resident_id=substr($key,14);
			$q="UPDATE residents SET name='$value' WHERE resident_id=$resident_id";
			//ver("",$q);
			mysqli_query($q);
		}
	}
} elseif ($request[operation]=="delete") {
	$q1="DELETE FROM residents_groups WHERE resident_group_id={$request[resident_group_id]}";
	mysqli_query($q1);

	$q2="DELETE FROM residents WHERE resident_id={$request[resident_id]}";
	mysqli_query($q2);

	$q3="DELETE FROM bookings WHERE resident_id={$request[resident_id]} AND group_id={$request[group_id]}";
	mysqli_query($q3);
}
?>
<LINK href="css/netherhall.css" rel="stylesheet" type="text/css">
<script language="javascript">
function delete_resident(resident_group_id, resident_id, resident_name) {
	confirmation=confirm("Do you want to delete "+resident_name+"?");
	if (confirmation) {
		document.myform.operation.value="delete";
		document.myform.resident_group_id.value=resident_group_id;
		document.myform.resident_id.value=resident_id;
		document.myform.submit();
	}
}
function save() {
	document.myform.operation.value="save";
	document.myform.submit();
}
</script>
<?php
$r=mysqli_query("SELECT resident_group_id, residents.resident_id, name, surname
FROM residents_groups LEFT JOIN residents ON residents_groups.resident_id=residents.resident_id
WHERE residents_groups.group_id={$request[group_id]} ORDER BY name");
if (mysqli_num_rows($r)==0) {
	?><p align="center" class="question">There are no residents in this group</p><?php
} else {
	$r2=mysqli_query("SELECT name, color FROM groups WHERE group_id={$request[group_id]}");
	$arrData2=mysqli_fetch_assoc($r2);
	?>
	<p class="question" align="center">Group <?=mysqli_result($r2,0,"name")?></p>
	<table align="center" border="0" cellpadding="4" cellspacing="0">
	<form name="myform" method="post" action="admin.php">
	<input type="hidden" name="pagetoload" value="groups_members.php">
	<input type="hidden" name="operation">
	<input type="hidden" name="group_id" value="<?=$request[group_id]?>">
	<input type="hidden" name="color" value="<?=$arrData2[color]?>">
	<input type="hidden" name="resident_group_id">
	<input type="hidden" name="resident_id">
	<tr class="header">
	<td>Name</td>
	<td></td>
	</tr>
	<?php
	while ($arrData=mysqli_fetch_assoc($r)) {
		?>
		<tr class="row1">
		<td class="cell">
            <input type="text" name="group_resident<?=$arrData[resident_id]?>" value="<?=$arrData[name]?>" size="30">
        </td>
		<td class="cell">
            <a href="javascript:delete_resident(<?=$arrData[resident_group_id]?>,<?=$arrData[resident_id]?>,'<?=$arrData[name]." ".$data[surname]?>')" title="Delete this resident">
                <img src="imgs/trash_16x16.gif" width="16" height="16" border="0">
            </a>
        </td>
		</tr>
		<?php
	}
	?>
	</form>
	</table>
	<br>
	<table align="center" cellpadding="5" cellspacing="0">
	<tr>
	<td align="center"><div class="button_off" onMouseOver="this.className='button_on'" onMouseOut="this.className='button_off'"><a href="admin.php?pagetoload=groups_list.php" class="button_link">Back</a></div></td>
	<td align="center"><div class="button_off" onMouseOver="this.className='button_on'" onMouseOut="this.className='button_off'"><a href="javascript:save()" class="button_link">Save</a></div></td>
	</tr>
	</table>
	<?php
}
?>