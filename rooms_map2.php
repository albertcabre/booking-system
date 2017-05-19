<?php
require_once('connection.php');
require_once('functions.php');

validate_user();
?>
<LINK href="css/netherhall.css" rel="stylesheet" type="text/css">
<br>
<table border="0" cellpadding="1" cellspacing="1" style="margin-left:10px">
<tr class="header">
<td></td>
<?php
$first_day=time();
if (isset($request["first_day"])) {
	$first_day=$request["first_day"];
}
if ($request["when"]=="week") {
	// 7 dias * 24 hours * 60 minutes * 60 seconds.
	$first_day=$first_day-(7 * 24 * 60 * 60);
} elseif ($request["when"]=="month") {
	// 4 weeks * 7 days * 24 hours * 60 minutes * 60 seconds.
	$first_day=$first_day-(4 * 7 * 24 * 60 * 60);
}
for ($i=0; $i<100; $i++) {
	$the_day=$first_day+($i * 24 * 60 * 60);
	//date("D", $the_day)."<br>".date("d/m", $the_day)
	?><td align="center" class="small"><?="&nbsp;".date("d", $the_day)."&nbsp;"."<br>"."&nbsp;".date("m", $the_day)."&nbsp;"?></td><?php
}
?>
</tr>
<?php
$r=mysqli_query($link, "SELECT * FROM rooms as a LEFT JOIN room_type as b on a.room_type_id=b.room_type_id ORDER BY room");
$class="file1";
while ($data=mysqli_fetch_assoc($r)) {
	?>
	<tr><td bgcolor="#999999" class="small"><?=$data[room]?></td><!--."({$data[room_id]})"-->
	<?php
	$last_resident_id=0;
	//$first_day=time();//+(40 * 24 * 60 * 60);
	for ($i=0; $i<100; $i++) {
		$the_day=$first_day+($i * 24 * 60 * 60);
		$the_day_to_search=date("Y/m/d", $the_day);
		/*$q="SELECT room_id FROM bookings WHERE
		(arrival   BETWEEN '{$the_day_to_search} 00:00' AND '{$the_day_to_search} 23:59' OR
		departure BETWEEN '{$the_day_to_search} 00:00' AND '{$the_day_to_search} 23:59') OR
		('{$the_day_to_search}' > arrival AND '{$the_day_to_search}' < departure) AND
		room_id={$data[room_id]} ";
		*/

		$q="SELECT room_id, resident_id FROM bookings WHERE
		(arrival <= '{$the_day_to_search}' AND '{$the_day_to_search}' < planned_departure) AND	room_id={$data[room_id]} ";
		//ver("q",$q);

		$r2=mysqli_query($link, $q);
		if (!mysqli_num_rows($r2)) {
			// FREE ROOM
			$color="#00CC33";
			//$color="#FFFFFF";
			$resident_name="";
			$resident_name_surname="";
			$room="";
		} else {
			// BUSY ROOM
			$resident_id=mysqli_result($r2,0,"resident_id");
			if ($last_resident_id!=$resident_id) {
				$last_resident_id=$resident_id;
			}

			$room_id=mysqli_result($r2,0,"room_id");
			$q="SELECT name, surname, color FROM residents WHERE resident_id=$resident_id";
			$r3=mysqli_query($link, $q);
			$resident_name=@mysqli_result($r3,0,"name");
			$resident_name_surname=@mysqli_result($r3,0,"name")." ".@mysqli_result($r3,0,"surname");
			$color=@mysqli_result($r3,0,"color");

			$q="SELECT room FROM rooms WHERE room_id=$room_id";
			$r3=mysqli_query($link, $q);
			$room="<br>(".mysqli_result($r3,0,"room").")";
		}

		?><td bgcolor="<?=$color?>" class="small white" align="center" title="<?=$resident_name_surname?>" onClick="document.location='admin.php?pagetoload=application_form.php&resident_id=<?=$resident_id?>&from=rooms_map.php'"><?=substr($resident_name,0,3)?></td><?php
		//.$room
	}
	?>
	</tr>
	<?php
}
?>
</table>
<br>
<table align="left" cellpadding="5" cellspacing="0" style="margin-left:5px">
<tr>
	<td align="center"><div class="button_off" onMouseOver="this.className='button_on'" onMouseOut="this.className='button_off'"><a href="admin.php?pagetoload=rooms_map.php&when=week&first_day=<?=$first_day?>" class="button_link">Back one week</a></div></td>
	<td align="center"><div class="button_off" onMouseOver="this.className='button_on'" onMouseOut="this.className='button_off'"><a href="admin.php?pagetoload=rooms_map.php&when=month&first_day=<?=$first_day?>" class="button_link">Back four weeks </a></div></td>
</tr>
</table>