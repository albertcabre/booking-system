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
$today=time(); //today
$strToday=date("d/m/Y", $today);
$first_day=time()-(0 * 24 * 60 * 60); // today
//$first_day=time()-(7 * 24 * 60 * 60); // one less week
//$first_day=time()-(285 * 24 * 60 * 60); // one less week
if ($request["small"]==1) $first_day=time();

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

//$to=365;
$to=120;
if ($request["small"]==1) $to=30;

for ($i=0; $i<$to; $i++) {
	$the_day=$first_day+($i * 24 * 60 * 60);
	$strColumnDay=date("d/m/Y", $the_day);
	?>
	<td align="center" class="small" <? if ($strToday==$strColumnDay) echo "bgcolor=#FF0000"; ?>>
	<?php
	if ($strToday==$strColumnDay) {
		echo "<span style='color:FFFFFF'>";
	}
	//echo date("d", $the_day)."<br>".date("m", $the_day);
	echo date("d/m", $the_day)."<br>".date("Y", $the_day);
	if ($strToday==$strColumnDay) {
		echo "</span>";
	}
	?>
	</td>
	<?php
}
?>
</tr>
<?php
$r=mysql_query("SELECT * FROM rooms as a LEFT JOIN room_type as b on a.room_type_id=b.room_type_id ORDER BY room");
$class="file1";
while ($data=mysql_fetch_assoc($r)) {
	?>
	<tr><td bgcolor="#999999" class="small"><?=$data[room]?></td>
	<?php
	$last_resident_id=0;
	//$first_day=time();//+(40 * 24 * 60 * 60);
	for ($i=0; $i<$to; $i++) {
		$the_day=$first_day+($i * 24 * 60 * 60);
		$the_day_to_search=date("Y/m/d", $the_day);

		$q="SELECT b.room_id, r.resident_id, r.name, r.surname, r.color FROM bookings b LEFT JOIN residents r
		ON b.resident_id=r.resident_id
		WHERE (b.arrival <= '{$the_day_to_search}' AND '{$the_day_to_search}' < b.planned_departure) AND b.room_id={$data[room_id]} ";
		//AND b.status='accepted'
		//ver("q",$q);

		$r2=mysql_query($q);
		if (!mysql_num_rows($r2)) {
			// FREE ROOM
			//$color="#00CC33";
			$color="#FFFFFF";
			$textcolor="black";
			$resident_name="";
			$resident_name_surname="Free";
			$room="";
			$action="";
		} else {
			// BUSY ROOM
			$textcolor="white";
			$resident_id=mysql_result($r2,0,"resident_id");
			if ($last_resident_id!=$resident_id) {
				$last_resident_id=$resident_id;
			}

			$room_id=mysql_result($r2,0,"room_id");
			/*
			$q="SELECT name, surname, color FROM residents WHERE resident_id=$resident_id";
			$r3=mysql_query($q);
			$resident_name=@mysql_result($r3,0,"name");
			$resident_name_surname=@mysql_result($r3,0,"name")." ".@mysql_result($r3,0,"surname");
			$color=@mysql_result($r3,0,"color");
			*/
			$resident_name=@mysql_result($r2,0,"name");
			$resident_name_surname=@mysql_result($r2,0,"name")." ".@mysql_result($r2,0,"surname");
			$color=@mysql_result($r2,0,"color");
			//$action="document.location='admin.php?pagetoload=application_form.php&resident_id=$resident_id&from=rooms_map.php'";
			$action="admin.php?pagetoload=application_form.php&resident_id=$resident_id&from=rooms_map3.php";
		}

		$q="SELECT room FROM rooms WHERE room_id={$data[room_id]}";
		$r3=mysql_query($q);
		$room=mysql_result($r3,0,"room");
		?>
		<td bgcolor="<?=$color?>" class="small <?=$textcolor?>" align="center" title="<?=$resident_name_surname." - ".date("D d/m/Y", $the_day)." - Room ".$room?>">
		<?php if ($action) { ?>
			<a href="<?=$action?>" class="small white" target="_blank"><?=$room?></a>
		<?php } else {
			echo $room;
		}
		?>
		</td>
		<?php
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
	<td align="center"><div class="button_off" onMouseOver="this.className='button_on'" onMouseOut="this.className='button_off'"><a href="admin.php?pagetoload=rooms_map3.php&when=week&first_day=<?=$first_day?>" class="button_link">Back one week</a></div></td>
	<td align="center"><div class="button_off" onMouseOver="this.className='button_on'" onMouseOut="this.className='button_off'"><a href="admin.php?pagetoload=rooms_map3.php&when=month&first_day=<?=$first_day?>" class="button_link">Back four weeks </a></div></td>
</tr>
</table>