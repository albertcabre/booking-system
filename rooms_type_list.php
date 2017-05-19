<?php
require_once('connection.php');
require_once('functions.php');

validate_user();

if ($request[operation]=="delete") {
	$r=mysqli_query($link, "DELETE FROM room_type WHERE room_type_id={$request[delete_room_type_id]}");
} elseif ($request[operation]=="add") {
	$r=mysqli_query($link, "INSERT INTO room_type (room_type, rate, sort) VALUES ('{$request[new_room_type]}', '{$request[new_rate]}', '{$request[new_sort]}')");
} elseif ($request[operation]=="save") {
	foreach ($request as $key => $value) {
		if (substr($key,0,9)=="room_type") {
			$room_type_id=substr($key,10);
			mysqli_query($link, "UPDATE room_type set room_type='$value' WHERE room_type_id=$room_type_id");
		}
		if (substr($key,0,4)=="rate") {
			$room_type_id=substr($key,5);
			mysqli_query($link, "UPDATE room_type set rate='$value' WHERE room_type_id=$room_type_id");
		}
		if (substr($key,0,4)=="sort") {
			$room_type_id=substr($key,5);
			mysqli_query($link, "UPDATE room_type set sort='$value' WHERE room_type_id=$room_type_id");
		}
	}
}
?>
<LINK href="css/netherhall.css" rel="stylesheet" type="text/css">
<script language="javascript">
function delete_room_type(room_type_id,room_type) {
	confirmation=confirm("Are you sure that you want to delete room type "+room_type+"?");
	if (confirmation) {
		document.myform.operation.value="delete";
		document.myform.delete_room_type_id.value=room_type_id;
		document.myform.submit();
	}
}
function add_room_type() {
	if (document.myform.new_room_type.value!="") {
		document.myform.operation.value="add";
		document.myform.submit();
	} else {
		alert("Please indicate a name for the room type.");
		document.myform.new_room_type.focus();
	}
}
function save() {
	document.myform.operation.value="save";
	document.myform.submit();
}
</script>
<table align="center" cellpadding="5" cellspacing="0">
<tr>
	<?php
	if ($request[op]=="e") {
		?>
		<td align="center">
            <div class="button_off" onMouseOver="this.className='button_on'" onMouseOut="this.className='button_off'"
                onClick="document.location='admin.php?pagetoload=rooms_type_list.php'"><a href="#" class="button_link">List Fees</a></div>
        </td>
		<td align="center">
            <div class="button_off" onMouseOver="this.className='button_on'" onMouseOut="this.className='button_off'"
                onClick="save()"><a href="javascript:save()" class="button_link">Save</a></div>
        </td>
		<?php
	} else {
		?>
        <td align="center">
            <div class="button_off" onMouseOver="this.className='button_on'" onMouseOut="this.className='button_off'"
                onClick="document.location='admin.php?pagetoload=rooms_type_list.php&op=e'"><a href="#" class="button_link">Edit Fees</a></div>
        </td>
        <?php
	}
	?>
</tr>
</table>

<table align="center" border="0" cellpadding="4" cellspacing="0">
<form name="myform" method="post" action="admin.php">
<input type="hidden" name="pagetoload" value="rooms_type_list.php">
<input type="hidden" name="operation">
<input type="hidden" name="delete_room_type_id">
<input type="hidden" name="op" value="e">
<tr class="header">
<td class="titol_taula_list" align="left">Room Type</td>
<td class="titol_taula_list" align="right">Weekly Rate</td>
<td class="titol_taula_list" align="right">Daily Rate</td>
<td class="titol_taula_list" align="right">Order</td>
<?php
if ($request[op]=="e") {
	?>
	<td class="titol_taula_list"></td>
	<?php
}
?>
</tr>
<?php
$r=mysqli_query($link, "SELECT * FROM room_type ORDER BY sort");
$class="file1";
while ($data=mysqli_fetch_assoc($r)) {
	?>
	<tr class="row1">
	<td class="cell" align="left">
		<?php
		if ($request[op]=="e") {
			?><input type="text" name="room_type_<?=$data[room_type_id]?>" value="<?=$data[room_type]?>" size="30"><?php
		} else {
			echo $data[room_type];
		}
		?>
	</td>
	<td class="cell" align="right">
		<?php
		if ($request[op]=="e") {
			?><input type="text" name="rate_<?=$data[room_type_id]?>" value="<?=$data[rate]?>" size="5"><?php
		} else {
			echo "&pound;".$data[rate];
		}
		?>
	</td>
	<td class="cell" align="right">
	<?php
	echo "&pound;";
	$daily_rate=round($data[rate]/7,2);
	echo number_format($daily_rate,2,",",".");
	?>
	</td>
	<td class="cell" align="right">
		<?php
		if ($request[op]=="e") {
			?><input type="text" name="sort_<?=$data[room_type_id]?>" value="<?=$data[sort]?>" size="5"><?php
		} else {
			echo $data[sort];
		}
		?>
	</td>
	<?php
	if ($request[op]=="e") {
		?>
		<td class="cell" align="center">
            <div class="button_off" onMouseOver="this.className='button_on'" onMouseOut="this.className='button_off'" onClick="delete_room_type('<?=$data[room_type_id]?>','<?=$data[room_type]?>')">
                <a href="#" class="button_link">Delete</a>
            </div>
        </td>
        <?php
	}
	?>
	</tr>
	<?php
}
?>
<!-- To add a room type -->
<?php
if ($request[op]=="e") {
	?>
	<tr class="row1">
	<td class="cell"><input type="text" name="new_room_type" size="30"></td>
	<td class="cell"><input type="text" name="new_rate" size="5"></td>
	<td class="cell">&nbsp;</td>
	<td class="cell"><input type="text" name="new_sort" size="5"></td>
	<td class="cell" align="center"><div class="button_off" onMouseOver="this.className='button_on'" onMouseOut="this.className='button_off'" onClick="add_room_type()"><a href="#" class="button_link">Add</a></div></td>
	</tr>
	<?php
}
?>
</form>
</table>
<br><br>