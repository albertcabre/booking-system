<?php
require_once('connection.php');
require_once('functions.php');

validate_user();
?>
<LINK href="css/netherhall.css" rel="stylesheet" type="text/css">
<p>&nbsp;</p>
<table align="center" border="0" cellpadding="4" cellspacing="0">
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
</tr>
<?php
$r=mysqli_query("SELECT * FROM terms ORDER BY name");
while ($data=mysqli_fetch_assoc($r)) {
	?>
	<tr class="row1">
	<td class="cell"><?=$data[name]?>&nbsp;</td>
	<td class="cell"><?=$data[t1_from]?></td>
	<td class="cell"><?=$data[t1_to]?></td>
	<td class="cell">&nbsp;</td>
	<td class="cell"><?=$data[t2_from]?></td>
	<td class="cell"><?=$data[t2_to]?></td>
	<td class="cell">&nbsp;</td>
	<td class="cell"><?=$data[t3_from]?></td>
	<td class="cell"><?=$data[t3_to]?></td>
	<td class="cell">&nbsp;</td>
	<td class="cell"><?=$data[t4_from]?></td>
	<td class="cell"><?=$data[t4_to]?></td>
	<td class="cell">&nbsp;</td>
	<td class="cell"><?=$data[t5_from]?></td>
	<td class="cell"><?=$data[t5_to]?></td>
	</tr>
	<?php
}
?>
</table>