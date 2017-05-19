<?php
require_once('functions.php');
require_once('calendar.html');

validate_user();

$date_fr = change_format_date($request[date_from]);
if (!isset($request[date_from])) { $date_fr=date("d/m/Y"); }

$date_to = change_format_date($request[date_to]);
if (!isset($request[date_to])) { $date_to=date("d/m/Y"); }
?>
<link rel="stylesheet" href="css/calendar.css">
<script language="JavaScript" src="js/calendar_eu.js"></script>
<SCRIPT language="JavaScript" src="js/funciones.js"></SCRIPT>
<script type="text/javascript">

function find_room(room_id, room) {
	if (document.myform.date_from.value!=="") {
		if (valFecha(document.myform.date_from, "Invalid 'from' date")) {
			str1=document.myform.date_from.value;
			var dt1   = parseInt(str1.substring(0,2),10);
			var mon1  = parseInt(str1.substring(3,5),10);
			var yr1   = parseInt(str1.substring(6,10),10);
			var date1 = new Date(yr1, mon1, dt1);

			document.myform.operation.value="find";
			document.myform.room_id.value=room_id;
			document.myform.room.value=room;
			document.myform.submit();
		}
	} else {
		alert("Invalid dates");
	}
}

$(function() {
    $("#datepicker").datepicker({ dateFormat: "dd/mm/yy" }).val();
});
</script>
<LINK href="css/netherhall.css" rel="stylesheet" type="text/css">
<br>
<?php
if ($request["operation"]=="find") {
	/*
	$q="SELECT b.room_id, r.resident_id, r.name, r.surname, r.color, b.arrival, b.planned_departure FROM bookings b LEFT JOIN residents r
		ON b.resident_id=r.resident_id
		WHERE ( b.arrival <= '{$date_fr}' OR b.arrival <= '{$date_to}')
		AND	b.room_id={$request[room_id]}
		AND b.status='accepted'
		ORDER BY b.planned_departure";
		*/
	$q="SELECT b.room_id, r.resident_id, r.name, r.surname, r.color, b.arrival, b.planned_departure
        FROM bookings b
        LEFT JOIN residents r ON b.resident_id=r.resident_id
        WHERE ( b.arrival <= '{$date_fr}' AND b.planned_departure > '{$date_fr}')
        AND	b.room_id={$request[room_id]}
        AND b.status='accepted'
        ORDER BY b.planned_departure";
	//ver("q",$q);
	$r2=mysql_query($q);
	?>
	<table align="center" cellpadding="2" cellspacing="1">
	<tr><td class="text_form">Date</td><td></td><td><?=$request[date_from]?></td></tr>
	<!--<tr><td class="text_form">To</td><td></td><td><?//=$request[date_to]?></td></tr>-->
	<tr><td class="text_form">Room</td><td></td><td><?=$request[room]?></td></tr>
	</table>
	<?php
	if (mysql_num_rows($r2)) {
		?>
		<TABLE width="900" border="0" align="center" cellpadding="10" cellspacing="0" bgcolor="#FFFFFF">
		<TR><TD align="center" class="question">This room is booked for:</td></tr>
		<?php
		while ($arrData=mysql_fetch_assoc($r2)) {
			?>
			<TR>
			<TD align="center"><a href="admin.php?pagetoload=application_form.php&resident_id=<?=$arrData[resident_id]?>&from=residents_list.php" class="table_link2"><?=$arrData["name"]." ".$arrData["surname"]?></a>
			<?php
			echo "<br>(Arrival: ".mostrar_fecha($arrData[arrival]). " Departure: ".mostrar_fecha($arrData[planned_departure]).")";
			?>
			</TD>
			</TR>
			<?php
		}
		?>
		</table>
		<br>
		<?php
	} else {
		?><p align="center" class="question">This room is free</p><?php
	}
	button("admin.php?pagetoload=booking_busy_list.php", "Find again");
} else {
	?>
	<table class="text_form" align="center" cellpadding="1" cellspacing="0">
	<form name="myform" method="post" action="admin.php">
	<input type="hidden" name="pagetoload" value="booking_busy_list.php">
	<input type="hidden" name="room_id">
	<input type="hidden" name="room">
	<input type="hidden" name="operation">
	<tr>
	<td>Date</td>
	<td>
	<input type="text" name="date_from" value="<?=$date_fr?>" size="8" id="datepicker" />
	</td>
	<!--<td width="10"></td>
	<td>To</td>
	<td>
	<input type="text" name="date_to" value="<?//=$date_to?>" size="8" />
	<script language="JavaScript">
	new tcal ({
		'formname': 'myform',
		'controlname': 'date_to'
	});
	</script>
	</td>-->
	</tr>
	</form>
	</table>
	<br>
	<?php
	//ver_array("SESSION",$_SESSION);
	if ( (!$request['date_from'] && !$request['date_to'] && !$request['resident_id']) || ($request["operation"]=="find") ) {
		$fr_pre = change_format_date($date_fr);
		$to_pre = change_format_date($date_to);

		$time = strtotime($fr_pre);
		$fr = date("Y/m/d", $time);
		$to = date("Y/m/d", $time);

		$r=mysql_query("SELECT * FROM rooms ORDER BY room");
		if ($error) {
			?><p class="question" align="center"><?=$error?></p><?php
		}
		?>
		<table align="center" border="0" cellpadding="10" cellspacing="0">
			<?php
			$i=0;
			while ($arrDate=mysql_fetch_assoc($r)) {
				if ($i==0) {
					?>
					<tr>
					<?php
				}
				?>
				<td align="center" class="main_message"><a href="javascript:find_room(<?=$arrDate[room_id]?>,'<?=$arrDate[room]?>')" class="table_link2"><?=$arrDate[room]?></a></td>
				<?php
				$i++;
				if ($i==20) { $i=0; }
				if ($i==0) {
					?>
					</tr>
				<?php
				}
			}
			?>
		</table>
		<?php
	}
}
?>