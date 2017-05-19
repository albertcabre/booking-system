<?php
require_once('connection.php');
require_once('functions.php');

validate_user();

if ($request[operation]=="delete") {
	$r=mysqli_query($link, "DELETE FROM rooms WHERE room_id={$request[delete_room_id]}");
} elseif ($request[operation]=="add") {
	$r=mysqli_query($link, "INSERT INTO rooms (room, telephone) VALUES ('{$request[new_room]}', '{$request[new_telephone]}')");
} elseif ($request[operation]=="save") {
	foreach ($request as $key => $value) {
		if (substr($key,0,4)=="room") {
			$room_id=substr($key,5);
			mysqli_query($link, "UPDATE rooms set room='$value' WHERE room_id=$room_id");
		}
		if (substr($key,0,9)=="telephone") {
			$room_id=substr($key,10);
			mysqli_query($link, "UPDATE rooms set telephone='$value' WHERE room_id=$room_id");
		}
	}
}
?>
<LINK href="css/netherhall.css" rel="stylesheet" type="text/css">
<script language="javascript">
function delete_room(room_id,room) {
	confirmation=confirm("Are you sure that you want to delete room "+room+"?");
	if (confirmation) {
		document.myform.operation.value="delete";
		document.myform.delete_room_id.value=room_id;
		document.myform.submit();
	}
}
function add_room() {
	if (document.myform.new_room.value!="") {
		document.myform.operation.value="add";
		document.myform.submit();
	} else {
		alert("Please indicate a name for the room.");
		document.myform.new_room.focus();
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
            <div class="button_off" onMouseOver="this.className='button_on'" onMouseOut="this.className='button_off'" onClick="document.location='admin.php?pagetoload=rooms_list.php'">
                <a href="#" class="button_link">List Rooms</a>
            </div>
        </td>
		<td align="center">
            <div class="button_off" onMouseOver="this.className='button_on'" onMouseOut="this.className='button_off'" onClick="save()">
                <a href="#" class="button_link">Save</a>
            </div>
        </td>
		<?php
	} else {
		?>
        <td align="center">
            <div class="button_off" onMouseOver="this.className='button_on'" onMouseOut="this.className='button_off'" onClick="document.location='admin.php?pagetoload=rooms_list.php&op=e'">
                <a href="#" class="button_link">Edit Rooms</a>
            </div>
        </td>
        <?php
	}
	?>
</tr>
</table>

<table align="center" border="0" cellpadding="4" cellspacing="0">
<form name="myform" method="post" action="admin.php">
<input type="hidden" name="pagetoload" value="rooms_list.php">
<input type="hidden" name="operation">
<input type="hidden" name="delete_room_id">
<input type="hidden" name="op" value="e">
<tr class="header">
<td>Room</td>
<td>Extension</td>
<?php
if ($request[op]=="e") {
	?>
	<td></td>
	<?php
}
?>
</tr>
<?php
$r=mysqli_query($link, "SELECT * FROM rooms ORDER BY room");
$i=0;
while ($data=mysqli_fetch_assoc($r)) {
	$i++;
	?>
	<tr class="row1">
	<td class="cell">
		<?php
		if ($request[op]=="e") {
			?><input type="text" name="room_<?=$data[room_id]?>" value="<?=$data[room]?>" size="5"><?php
		} else {
			echo $data[room];
		}
		?>
	</td>
	<td class="cell">
		<?php
		if ($request[op]=="e") {
			?><input type="text" name="telephone_<?=$data[room_id]?>" value="<?=$data[telephone]?>" size="5"><?php
		} else {
			echo $data[telephone]."&nbsp;";
		}
		?>
	</td>

	<?php
	if ($request[op]=="e") {
		?>
		<td class="cell" align="center"><div class="button_off" onMouseOver="this.className='button_on'" onMouseOut="this.className='button_off'" onClick="delete_room('<?=$data[room_id]?>','<?=$data[room]?>')"><a href="#" class="button_link">Delete</a></div></td>
		<?php
	}
	?>
	</tr>
	<?php
}
?>
<!-- To add a room -->
<?php
if ($request[op]=="e") {
	?>
	<tr class="row1">
	<td class="cell"><input type="text" name="new_room" size="5"></td>
	<td class="cell"><input type="text" name="new_telephone" size="5"></td>
	<td class="cell" align="center"><div class="button_off" onMouseOver="this.className='button_on'" onMouseOut="this.className='button_off'" onClick="add_room()"><a href="#" class="button_link">Add</a></div></td>
	</tr>
	<?php
}
?>
</form>
</table>
<br><br>