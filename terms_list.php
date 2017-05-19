<?php
require_once('connection.php');
require_once('functions.php');

validate_user();

if ($request[operation]=="delete")
{
	$r=mysqli_query("DELETE FROM terms WHERE term_id={$request[delete_term_id]}");
}
elseif ($request[operation]=="add")
{
	$r=mysqli_query("INSERT INTO terms (name, t1_from, t1_to, t2_from, t2_to, t3_from, t3_to, t4_from, t4_to, t5_from, t5_to) VALUES ('{$request[new_name]}', '{$request[new_t1_from]}', '{$request[new_t1_to]}', '{$request[new_t2_from]}', '{$request[new_t2_to]}', '{$request[new_t3_from]}', '{$request[new_t3_to]}', '{$request[new_t4_from]}', '{$request[new_t4_to]}', '{$request[new_t5_from]}', '{$request[new_t5_to]}')");
}
elseif ($request[operation]=="save")
{
	foreach ($request as $key => $value) {
		if (substr($key,0,4)=="name") {
			$term_id=substr($key,5);
			mysqli_query("UPDATE terms SET name='$value' WHERE term_id=$term_id");
		}
		if (substr($key,0,7)=="t1_from") {
			$term_id=substr($key,8);
			mysqli_query("UPDATE terms SET t1_from='$value' WHERE term_id=$term_id");
		}
		if (substr($key,0,5)=="t1_to") {
			$term_id=substr($key,6);
			mysqli_query("UPDATE terms SET t1_to='$value' WHERE term_id=$term_id");
		}
		if (substr($key,0,7)=="t2_from") {
			$term_id=substr($key,8);
			mysqli_query("UPDATE terms SET t2_from='$value' WHERE term_id=$term_id");
		}
		if (substr($key,0,5)=="t2_to") {
			$term_id=substr($key,6);
			mysqli_query("UPDATE terms SET t2_to='$value' WHERE term_id=$term_id");
		}
		if (substr($key,0,7)=="t3_from") {
			$term_id=substr($key,8);
			mysqli_query("UPDATE terms SET t3_from='$value' WHERE term_id=$term_id");
		}
		if (substr($key,0,5)=="t3_to") {
			$term_id=substr($key,6);
			mysqli_query("UPDATE terms SET t3_to='$value' WHERE term_id=$term_id");
		}
		if (substr($key,0,7)=="t4_from") {
			$term_id=substr($key,8);
			mysqli_query("UPDATE terms SET t4_from='$value' WHERE term_id=$term_id");
		}
		if (substr($key,0,5)=="t4_to") {
			$term_id=substr($key,6);
			mysqli_query("UPDATE terms SET t4_to='$value' WHERE term_id=$term_id");
		}
		if (substr($key,0,7)=="t5_from") {
			$term_id=substr($key,8);
			mysqli_query("UPDATE terms SET t5_from='$value' WHERE term_id=$term_id");
		}
		if (substr($key,0,5)=="t5_to") {
			$term_id=substr($key,6);
			mysqli_query("UPDATE terms SET t5_to='$value' WHERE term_id=$term_id");
		}
	}
}
?>
<LINK href="css/netherhall.css" rel="stylesheet" type="text/css">
<script language="javascript">
function delete_name(term_id,name) {
	confirmation=confirm("Are you sure that you want to delete "+name+" with its terms?");
	if (confirmation) {
		document.myform.operation.value="delete";
		document.myform.delete_term_id.value=term_id;
		document.myform.submit();
	}
}
function add_name() {
	if (document.myform.new_name.value!="") {
		document.myform.operation.value="add";
		document.myform.submit();
	} else {
		alert("Please indicate a name.");
		document.myform.new_name.focus();
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
		<td align="center"><div class="button_off" onMouseOver="this.className='button_on'" onMouseOut="this.className='button_off'" onClick="document.location='admin.php?pagetoload=terms_list.php'"><a href="#" class="button_link">List Terms</a></div></td>
		<td align="center"><div class="button_off" onMouseOver="this.className='button_on'" onMouseOut="this.className='button_off'" onClick="save()"><a href="javascript:save()" class="button_link">Save</a></div></td>
		<?php
	} else {
		?><td align="center"><div class="button_off" onMouseOver="this.className='button_on'" onMouseOut="this.className='button_off'" onClick="document.location='admin.php?pagetoload=terms_list.php&op=e'"><a href="#" class="button_link">Edit Terms</a></div></td><?php
	}
	?>
</tr>
</table>

<table align="center" border="0" cellpadding="4" cellspacing="0">
<form name="myform" method="post" action="admin.php">
<input type="hidden" name="pagetoload" value="terms_list.php">
<input type="hidden" name="operation">
<input type="hidden" name="delete_term_id">
<input type="hidden" name="op" value="e">
<tr class="header">
<td>&nbsp;</td>
<td colspan="2">Term 1</td>
<td>&nbsp;</td>
<td colspan="2">Term 2</td>
<td>&nbsp;</td>
<td colspan="2">Term 3</td>
<td>&nbsp;</td>
<td colspan="2">Term 4</td>
<td>&nbsp;</td>
<td colspan="2">Term 5</td>
<?php
if ($request[op]=="e") {
	?><td></td><?php
}
?>
</tr>
<tr class="header">
<td>Univeristy</td>
<td width="50">From</td>
<td width="50">To</td>
<td></td>
<td width="50">From</td>
<td width="50">To</td>
<td></td>
<td width="50">From</td>
<td width="50">To</td>
<td></td>
<td width="50">From</td>
<td width="50">To</td>
<td></td>
<td width="50">From</td>
<td width="50">To</td>
<?php
if ($request[op]=="e") {
	?>
	<td></td>
	<?php
}
?>
</tr>
<?php
$r=mysqli_query("SELECT * FROM terms ORDER BY name");
$class="file1";
while ($data=mysqli_fetch_assoc($r)) {
	?>
	<tr class="row1">
	<td class="cell">
		<?php
		if ($request[op]=="e") {
			?><input type="text" name="name_<?=$data[term_id]?>" value="<?=$data[name]?>" size="25"><?php
		} else {
			echo $data[name];
		}
		?>
	</td>
	<td class="cell">
		<?php
		if ($request[op]=="e") {
			?><input type="text" name="t1_from_<?=$data[term_id]?>" value="<?=$data[t1_from]?>" size="8" class="input_small"><?php
		} else {
			echo $data[t1_from];
		}
		?>
	</td>
	<td class="cell">
		<?php
		if ($request[op]=="e") {
			?><input type="text" name="t1_to_<?=$data[term_id]?>" value="<?=$data[t1_to]?>" size="8" class="input_small"><?php
		} else {
			echo $data[t1_to];
		}
		?>
	</td>
	<td class="cell"></td>
	<td class="cell">
		<?php
		if ($request[op]=="e") {
			?><input type="text" name="t2_from_<?=$data[term_id]?>" value="<?=$data[t2_from]?>" size="8" class="input_small"><?php
		} else {
			echo $data[t2_from];
		}
		?>
	</td>
	<td class="cell">
		<?php
		if ($request[op]=="e") {
			?><input type="text" name="t2_to_<?=$data[term_id]?>" value="<?=$data[t2_to]?>" size="8" class="input_small"><?php
		} else {
			echo $data[t2_to];
		}
		?>
	</td>
	<td class="cell"></td>
	<td class="cell">
		<?php
		if ($request[op]=="e") {
			?><input type="text" name="t3_from_<?=$data[term_id]?>" value="<?=$data[t3_from]?>" size="8" class="input_small"><?php
		} else {
			echo $data[t3_from];
		}
		?>
	</td>
	<td class="cell">
		<?php
		if ($request[op]=="e") {
			?><input type="text" name="t3_to_<?=$data[term_id]?>" value="<?=$data[t3_to]?>" size="8" class="input_small"><?php
		} else {
			echo $data[t3_to];
		}
		?>
	</td>
	<td class="cell"></td>
	<td class="cell">
		<?php
		if ($request[op]=="e") {
			?><input type="text" name="t4_from_<?=$data[term_id]?>" value="<?=$data[t4_from]?>" size="8" class="input_small"><?php
		} else {
			echo $data[t4_from];
		}
		?>
	</td>
	<td class="cell">
		<?php
		if ($request[op]=="e") {
			?><input type="text" name="t4_to_<?=$data[term_id]?>" value="<?=$data[t4_to]?>" size="8" class="input_small"><?php
		} else {
			echo $data[t4_to];
		}
		?>
	</td>
	<td class="cell"></td>
	<td class="cell">
		<?php
		if ($request[op]=="e") {
			?><input type="text" name="t5_from_<?=$data[term_id]?>" value="<?=$data[t5_from]?>" size="8" class="input_small"><?php
		} else {
			echo $data[t5_from];
		}
		?>
	</td>
	<td class="cell">
		<?php
		if ($request[op]=="e") {
			?><input type="text" name="t5_to_<?=$data[term_id]?>" value="<?=$data[t5_to]?>" size="8" class="input_small"><?php
		} else {
			echo $data[t5_to];
		}
		?>
	</td>
	<?php
	if ($request[op]=="e") {
		?><td class="cell" align="center"><div class="button_off" onMouseOver="this.className='button_on'" onMouseOut="this.className='button_off'" onClick="delete_name('<?=$data[term_id]?>','<?=$data[name]?>')"><a href="#" class="button_link">Delete</a></div></td><?php
	}
	?>
	</tr>
	<?php
}
?>
<!-- To add a univerisity with its terms -->
<?php
if ($request[op]=="e") {
	?>
	<tr class="row1">
	<td class="cell"><input type="text" name="new_name" size="25"></td>
	<td class="cell"><input type="text" name="new_t1_from" size="8" class="input_small"></td>
	<td class="cell"><input type="text" name="new_t1_to" size="8" class="input_small"></td>
	<td class="cell"></td>
	<td class="cell"><input type="text" name="new_t2_from" size="8" class="input_small"></td>
	<td class="cell"><input type="text" name="new_t2_to" size="8" class="input_small"></td>
	<td class="cell"></td>
	<td class="cell"><input type="text" name="new_t3_from" size="8" class="input_small"></td>
	<td class="cell"><input type="text" name="new_t3_to" size="8" class="input_small"></td>
	<td class="cell"></td>
	<td class="cell"><input type="text" name="new_t4_from" size="8" class="input_small"></td>
	<td class="cell"><input type="text" name="new_t4_to" size="8" class="input_small"></td>
	<td class="cell"></td>
	<td class="cell"><input type="text" name="new_t5_from" size="8" class="input_small"></td>
	<td class="cell"><input type="text" name="new_t5_to" size="8" class="input_small"></td>
	<td class="cell" align="center"><div class="button_off" onMouseOver="this.className='button_on'" onMouseOut="this.className='button_off'" onClick="add_name()"><a href="#" class="button_link">Add</a></div></td>
	</tr>
	<?php
}
?>
</form>
</table>
<br><br>