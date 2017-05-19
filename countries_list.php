<?php
require_once('connection.php');
require_once('functions.php');

validate_user();

if ($request[operation]=="delete") {
	$r=mysql_query("DELETE FROM countries WHERE country_id={$request[delete_country_id]}");
} elseif ($request[operation]=="add") {
	$r=mysql_query("INSERT INTO countries (country) VALUES ('{$request[new_country]}')");
} elseif ($request[operation]=="save") {
	foreach ($request as $key => $value) {
		if (substr($key,0,7)=="country") {
			$country_id=substr($key,8);
			mysql_query("UPDATE countries set country='$value' WHERE country_id=$country_id");
		}
	}
}
?>
<LINK href="css/netherhall.css" rel="stylesheet" type="text/css">
<script language="javascript">
function delete_country(country_id,country) {
	confirmation=confirm("Are you sure that you want to delete the country "+country+"?");
	if (confirmation) {
		document.myform.operation.value="delete";
		document.myform.delete_country_id.value=country_id;
		document.myform.submit();
	}
}
function add_country() {
	if (document.myform.new_country.value!=="") {
		document.myform.operation.value="add";
		document.myform.submit();
	} else {
		alert("Please indicate a name for the country.");
		document.myform.new_country.focus();
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
		<td align="center"><div class="button_off" onMouseOver="this.className='button_on'" onMouseOut="this.className='button_off'" onClick="document.location='admin.php?pagetoload=countries_list.php'"><a href="#" class="button_link">List Countries</a></div></td>
		<td align="center"><div class="button_off" onMouseOver="this.className='button_on'" onMouseOut="this.className='button_off'" onClick="save()"><a href="#" class="button_link">Save</a></div></td>
		<?php
	} else {
		?><td align="center"><div class="button_off" onMouseOver="this.className='button_on'" onMouseOut="this.className='button_off'" onClick="document.location='admin.php?pagetoload=countries_list.php&op=e'"><a href="#" class="button_link">Edit Countries</a></div></td><?php
	}
	?>
</tr>
</table>

<table align="center" border="0" cellpadding="4" cellspacing="0">
<form name="myform" method="post" action="admin.php">
<input type="hidden" name="pagetoload" value="countries_list.php">
<input type="hidden" name="operation">
<input type="hidden" name="delete_country_id">
<input type="hidden" name="op" value="e">
<tr class="header">
    <td class="titol_taula_list" align="left">Country</td>
<?php
if ($request[op]=="e") {
	?>
    <td class="titol_taula_list"></td>
	<?php
}
?>
</tr>
<?php
$r=mysql_query("SELECT * FROM countries ORDER BY country");
while ($data=mysql_fetch_assoc($r)) {
	?>
	<tr class="row1">
        <td class="cell" style="text-align: left">
		<?php
		if ($request[op]=="e") {
			?><input type="text" name="country_<?=$data[country_id]?>" value="<?=$data[country]?>" size="20"><?php
		} else {
			echo $data[country];
		}
		?>
	</td>

	<?php
	if ($request[op]=="e") {
		?>
		<td class="cell" align="center"><div class="button_off" onMouseOver="this.className='button_on'" onMouseOut="this.className='button_off'" onClick="delete_country('<?=$data[country_id]?>','<?=$data[country]?>')"><a href="#" class="button_link">Delete</a></div></td>
		<?php
	}
	?>
	</tr>
	<?php
}
?>
<!-- To add a country -->
<?php
if ($request[op]=="e") {
	?>
	<tr class="row1">
	<td class="cell"><input type="text" name="new_country" size="20"></td>
	<td class="cell" align="center"><div class="button_off" onMouseOver="this.className='button_on'" onMouseOut="this.className='button_off'" onClick="add_country()"><a href="#" class="button_link">Add</a></div></td>
	</tr>
	<?php
}
?>
</form>
</table>
<br><br>