<?php
require_once('functions.php');

validate_user();

$arrClasses = array();
//The purpose of this page should only be to keep track of the residents who are currently in Netherhall.
$today = date('Y-m-d');
$r=mysqli_query("SELECT residents.resident_id, NAME, surname ".
	           "FROM residents LEFT JOIN bookings ON residents.resident_id = bookings.resident_id ".
	           "WHERE bookings.status='accepted' AND bookings.done=0 AND bookings.arrival <= '$today' ".
	           "GROUP BY residents.resident_id ORDER BY surname, NAME");
if (mysqli_num_rows($r)) {
	?>
	<table align="center" border="1" cellpadding="4" cellspacing="0">
	<?php
	while ($arrInfo=mysqli_fetch_assoc($r)) {

		$total_outstanding=0;

		$r2=mysqli_query("SELECT * FROM residents LEFT JOIN bookings ON residents.resident_id = bookings.resident_id ".
						"WHERE bookings.status='accepted' AND residents.resident_id={$arrInfo[resident_id]} ".
						"ORDER BY NAME, surname, bookings.arrival");
		while ($arrData=mysqli_fetch_assoc($r2)) {
			//ver_array("arrData",$arrData);
			$date_from = mostrar_fecha($arrData['arrival']);
			$date_to   = mostrar_fecha($arrData['planned_departure']);

			$days = subtract_dates($date_from, $date_to);

			$total_rent = $days * ($arrData['weekly_rate']/7);
			$total_rent = round($total_rent,2);
			$due = $total_rent + $arrData['laundry'] + $arrData['hc'] + $arrData['printing'] + $arrData['extra'];
			//$outstanding = $due - $arrData['deposit'] - $arrData['received'];
			$outstanding = $due - $arrData['received'];
			$outstanding  = round($outstanding,2);

			$name = "";
			if ($arrData[surname]!="") $name = $arrData[surname].", ";
			$name .= $arrData[name];

			if (mysqli_num_rows($r2)<2) {
			?>
			<tr class="row1">
			<td><?=$name?></td>
			<td align="right"><?=number_format($outstanding,2,".",",")?></td>
			</tr>
			<?php
			}

			$total_outstanding 	= $total_outstanding + $outstanding;
			$total_outstanding  = round($total_outstanding,2);
		}
		if (mysqli_num_rows($r2)>1) {
			?>
			<tr class="row1">
			<td class="td_total"><?=$name?></td></td>
			<td class="td_total" align="right"><?=number_format($total_outstanding,2,".",",")?></td>
			</tr>
			<?php
		}
	}
	?>
	</table>
	<?php
}
?>