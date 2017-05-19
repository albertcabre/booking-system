<?php
require_once('functions.php');
require_once('calendar.html');

validate_user();

$resident_id=0;
if ($request[resident_id]!="") { $resident_id=$request[resident_id]; }

$booking_id=0;
if ($request[booking_id]!="") { $booking_id=$request[booking_id]; }

$date_fr=$request[date_from];
if (!isset($request[date_from])) { $date_fr=date("d-m-Y"); }

$date_to=$request[date_to];
if (!isset($request[date_to])) {
	$tomorrow=time() + (1 * 24 * 60 * 60);
	$date_to=date("d-m-Y", $tomorrow);
}

if ($request[operation]=="save") {
	$arrival   = change_format_date($request[date_from]);
	$departure = change_format_date($request[date_to]);

	if ($arrival!="" && $departure!="") {
		$today=date("Y-m-d H:m:s");
		if ($booking_id) {
			$q="UPDATE bookings SET arrival='$arrival', planned_departure='$departure', room_id={$request[room_id]}, booking_date='$today'
			WHERE booking_id=$booking_id";
			mysqli_query($link, $q);
		} else {
			$q1="INSERT INTO bookings (arrival, planned_departure, room_id, resident_id, booking_date, status)
			VALUES ('$arrival', '$departure', '{$request[room_id]}', $resident_id, '$today', 'accepted')";
			mysqli_query($link, $q1);
			$q2="UPDATE residents SET status='accepted' WHERE resident_id=$resident_id";
			mysqli_query($link, $q2);
		}
		$result="ok";
	} else {
		$error="Please indicate dates for accomodation";
	}
	//ver("q",$q);
}
?>
<!--<SCRIPT language="JavaScript" src="js/calendario.js"></SCRIPT>-->
<script language="JavaScript" src="js/calendar_eu.js"></script>
<SCRIPT language="JavaScript" src="js/funciones.js"></SCRIPT>
<link rel="stylesheet" href="css/calendar.css">
<script type="text/javascript">
function save(room_id, room) {
	document.myform.operation.value="save";
	document.myform.room_id.value=room_id;
	document.myform.room.value=room;
	document.myform.submit();
}
function find_rooms() {
	if (document.myform.date_from.value!=="" && document.myform.date_to.value!=="") {
		if (valFecha(document.myform.date_from, "Invalid arrival date")) {
			if (valFecha(document.myform.date_to, "Invalid departure date")) {
				str1=document.myform.date_from.value;
				str2=document.myform.date_to.value;
				var dt1   = parseInt(str1.substring(0,2),10);
				var mon1  = parseInt(str1.substring(3,5),10);
				var yr1   = parseInt(str1.substring(6,10),10);
				var dt2   = parseInt(str2.substring(0,2),10);
				var mon2  = parseInt(str2.substring(3,5),10);
				var yr2   = parseInt(str2.substring(6,10),10);
				var date1 = new Date(yr1, mon1, dt1);
				var date2 = new Date(yr2, mon2, dt2);

				if (date2>date1) {
					document.myform.operation.value="find";
					document.myform.submit();
				} else {
					alert("Departure date must be later than the arrival date");
				}
			}
		}
	} else {
		alert("Invalid dates");
	}
}
$(function() {
    $("#datepickerfrom").datepicker({ dateFormat: "dd/mm/yy" }).val();
});
$(function() {
    $("#datepickerto").datepicker({ dateFormat: "dd/mm/yy" }).val();
});
</script>
<LINK href="css/netherhall.css" rel="stylesheet" type="text/css">
<br>
<?php
if ($result=="ok") {
	// If the room has been booked then whe show a message.
	$q="SELECT name, surname FROM residents WHERE resident_id=$resident_id";
	$r=mysqli_query($link, $q);
	$arrData=mysqli_fetch_assoc($r);
	?>
	<TABLE width="900" border="0" height="100%" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
		<TR>
			<TD valign="middle">
				<p align="center" class="question">The room <?=$request["room"]?> has been booked for <?=$arrData["name"]." ".$arrData["surname"]?></p>
				<?php button("admin.php?pagetoload=application_form.php&resident_id=$resident_id", "Continue") ?>
		  	</td>
		</tr>
	</table>
	<?php
} else {
	// Shows the input to put the dates to search a room.
	?>
	<TABLE width="900" border="0" height="100%" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<TR>
	<TD valign="middle">
	<?php
	// Searches the information of the resident.
	if ($request[resident_id]) {
		$r=mysqli_query($link, "SELECT * FROM residents WHERE resident_id={$request[resident_id]}");
		$arrData=mysqli_fetch_assoc($r);
		?><p align="center" class="question">Booking a room for <?=$arrData[name]." ".$arrData[surname]?><br>for these dates</p><?php
	}
	?>
	<table class="text_form" align="center" cellpadding="1" cellspacing="0">
	<form name="myform" method="post" action="admin.php">
	<input type="hidden" name="pagetoload" value="booking_list.php">
	<input type="hidden" name="resident_id" value="<?=$request['resident_id']?>">
	<input type="hidden" name="room_id">
	<input type="hidden" name="room">
	<input type="hidden" name="operation">
	<tr>
	<td>Arrival</td>
	<td>
        <input type="text" name="date_from" value="<?=$date_fr?>" size="8" id="datepickerfrom" />
	</td>
    <td width="10"></td>
	<td>Departure</td>
	<td>
        <input type="text" name="date_to" value="<?=$date_to?>" size="8" id="datepickerto" />
	</td>
	</tr>
	</form>
	</table>
	<br>
	<?php
	button("javascript:find_rooms()", "Find Rooms");

	if ( (!$request['date_from'] && !$request['date_to'] && !$request['resident_id']) || ($request["operation"]=="find") ) {
		$fr_pre = change_format_date($date_fr);
		$to_pre = change_format_date($date_to);

		// We add one day to the from date. Because the departure date is free. And we rest one for the same reason.
		$timefr_pre = strtotime($fr_pre);
		$timefr = $timefr_pre + (1 * 24 * 60 * 60);
		$fr = date("Y/m/d", $timefr);

		$timeto_pre = strtotime($to_pre);
		$timeto = $timeto_pre - (1 * 24 * 60 * 60);
		$to = date("Y/m/d", $timeto);

		$q="SELECT room_id, room FROM rooms WHERE room_id NOT IN ( ".
		     "SELECT room_id FROM bookings WHERE ".
			 "bookings.arrival           BETWEEN '$fr' AND '$to' OR ".
			 "bookings.planned_departure BETWEEN '$fr' AND '$to' OR ".
			 "(bookings.arrival <= '$fr' AND bookings.planned_departure >= '$to') ) ".
			 "ORDER BY room";
		$r=mysqli_query($link, $q);
		if ($error) {
			?><p class="question" align="center"><?=$error?></p><?php
		}
		if (!mysqli_num_rows($r)) {
			?><p class="question" align="center">There are no available rooms for this dates</p><?php
		} else {
			?>
			<p class="question" align="center">These are the available rooms for these dates:</p>
			<table align="center" border="0" cellpadding="10" cellspacing="0">
				<?php
				$i=0;
				while ($arrDate=mysqli_fetch_assoc($r)) {
					if ($i==0) {
						?>
						<tr>
						<?php
					}
					?>
					<td align="center" class="main_message">
					<?php
					if ($request[resident_id]) {
						?><a href="javascript:save(<?=$arrDate[room_id]?>,'<?=$arrDate[room]?>')"><?=$arrDate[room]?></a><?php
					} else {
						?><a href="admin.php?pagetoload=new_resident.php" class="table_link2"><?=$arrDate[room]?></a><?php
					}?>
					</td>
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
	</td>
	</tr>
	</table>
	<?php
}
?>