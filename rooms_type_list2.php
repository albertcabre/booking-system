<?php
require_once('connection.php');
require_once('functions.php');

validate_user();
?>
<LINK href="css/netherhall.css" rel="stylesheet" type="text/css">
<p>&nbsp;</p>
<table align="center" border="0" cellpadding="4" cellspacing="0">
<tr class="header">
<td>Room Type</td>
<td width="70" align="right">Weekly<br>
  Rate</td>
<td width="70" align="right">Daily<br>
  Rate</td>
</tr>
<?php
$r=mysqli_query("SELECT * FROM room_type ORDER BY sort");
$class="file1";
while ($data=mysqli_fetch_assoc($r)) {
	?>
	<tr class="row1">
	<td class="cell">
		<?php

			echo $data[room_type];

		?>
	</td>
	<td class="cell" align="right">
		<?php

			echo "&pound;".$data[rate];

		?>
	</td>
	<td class="cell" align="right">
	<?php
	echo "&pound;";
	$daily_rate=round($data[rate]/7,2);
	echo number_format($daily_rate,2,".",",");
	?>
	</td>


	</tr>
	<?php
}
?>
</table>