<?php
require_once('functions.php');
require_once('calendar.html');

validate_user();

$resident_id=$request[resident_id];

if ($request[operation]=="save") {
	$arrival   = substr($request[date_from],6,4)."-".substr($request[date_from],3,2)."-".substr($request[date_from],0,2);
	$departure = substr($request[date_to],6,4)."-".substr($request[date_to],3,2)."-".substr($request[date_to],0,2);
	if ($request["booking_id"]) {
		if ($arrival!="" && $departure!="") {
			$q="UPDATE bookings SET arrival='$arrival', planned_departure='$departure', ".
               "room_id='{$request[room_id_new]}' WHERE booking_id={$request[booking_id]}";
			mysqli_query($link, $q);
		} else {
			$error=1;
		}
	} else {
		// If there wasn't dates until now, then we have to insert.
		// As I am doing the booking, automatically the status is accepted.
		if ($arrival!="" && $departure!="") {
			$today=date("Y-m-d H:m:s");
			$q="INSERT INTO bookings (arrival, planned_departure, departure, room_id, resident_id, status, booking_date) ".
               "VALUES ('$arrival', '$departure', '$departure', '{$request[room_id_new]}', $resident_id, 'accepted', '$today')";
			mysqli_query($link, $q);
			$q="UPDATE residents SET status='accepted' WHERE resident_id={$request[resident_id]}";
			mysqli_query($link, $q);
		}
	}
	//ver("q",$q);
	?><script>document.location='admin.php?pagetoload=application_form.php&resident_id=<?=$resident_id?>&from=residents_list.php';</script><?php
} elseif ($request[operation]=="swap") {
	$room_id      = $request['room_id'];
    $book_id      = $request['booking_id'];
	$room_id_dest = $request['room_id_dest'];
    $book_id_dest = $request['booking_id_dest'];
	$q1="UPDATE bookings SET room_id=$room_id_dest WHERE booking_id=$book_id";
	mysqli_query($link, $q1);
	$q2="UPDATE bookings SET room_id=$room_id WHERE booking_id=$book_id_dest";
	mysqli_query($link, $q2);
	?><script>document.location='admin.php?pagetoload=application_form.php&resident_id=<?=$resident_id?>&from=residents_list.php';</script><?php
}
?>
<script src="js/calendar_eu.js"></script>
<SCRIPT src="js/funciones.js"></SCRIPT>
<link rel="stylesheet" href="css/calendar.css">
<script>
function save(date_from, date_to, room_id, room) {
	if (confirm("Do you want to book room " + room + " from " + date_from + " to " + date_to + "?")) {
		document.miform.operation.value="save";
		document.miform.room_id_new.value=room_id;
		document.miform.submit();
	}
}
function swap(date_from, date_to, current_room, room_id, room, name, booking_id) {
	if (confirm("Do you want to swap room "+current_room+" with room " + room + " with " + name + " for these dates?\nArrival:       " + date_from + "\nDeparture: " + date_to)) {
		document.miform.operation.value       = "swap";
		document.miform.booking_id_dest.value = booking_id;
		document.miform.room_id_dest.value    = room_id;
		document.miform.submit();
	}
}
function find_rooms() {
	if (document.miform.date_from.value!=="" && document.miform.date_to.value!=="") {
		if (valFecha(document.miform.date_from, "Invalid arrival date")) {
			if (valFecha(document.miform.date_to, "Invalid departure date")) {
				str1=document.miform.date_from.value;
				str2=document.miform.date_to.value;
				var dt1   = parseInt(str1.substring(0,2),10);
				var mon1  = parseInt(str1.substring(3,5),10);
				var yr1   = parseInt(str1.substring(6,10),10);
				var dt2   = parseInt(str2.substring(0,2),10);
				var mon2  = parseInt(str2.substring(3,5),10);
				var yr2   = parseInt(str2.substring(6,10),10);
				var date1 = new Date(yr1, mon1, dt1);
				var date2 = new Date(yr2, mon2, dt2);

				if (date2>date1) {
					document.miform.operation.value="find";
					document.miform.submit();
				} else {
					alert("Departure date must be later than the arrival date");
				}
			}
		}
	} else {
		alert("Invalid dates");
	}
}
function terms() {
	mywindow = window.open('terms_list2.php','mywindow','width=900,height=500,top=25,left=25');
}
$(function() {
    $("#datepickerfrom").datepicker({ dateFormat: "dd-mm-yy" }).val();
});
$(function() {
    $("#datepickerto").datepicker({ dateFormat: "dd-mm-yy" }).val();
});
</script>
<LINK href="css/netherhall.css" rel="stylesheet" type="text/css">
<br>
<?php
// Get data from the resident (name and surname).
if ($request[resident_id]) {
	$r=mysqli_query($link, "SELECT * FROM residents WHERE resident_id={$request[resident_id]}");
	$arrData=mysqli_fetch_assoc($r);
    $arrData = utf8_converter($arrData);
}
$from_page="application_form.php&resident_id={$request[resident_id]}&from=residents_list.php";
?>
<table cellpadding="0" cellspacing="0" align="center">
<form name="miform" method="post">
<input type="hidden" name="pagetoload" value="application_form_dates.php">
<input type="hidden" name="operation">
<input type="hidden" name="resident_id" value="<?=$request[resident_id]?>">
<input type="hidden" name="from" value="<?=$from_page?>">
<input type="hidden" name="booking_id_dest">
<input type="hidden" name="room_id_dest">
<input type="hidden" name="room_id_new">
<tr>
<td valign="top">
	<table class="text_form" align="center" cellpadding="1" cellspacing="3" border="0">
	<tr>
	<td colspan="3" class="question" align="center">
	<?php
	if (isset($request["booking_id"])) {
		echo "Changing booking dates for:";
	} else {
		echo "New booking for:";
	}
	echo "<br>".$arrData[name]." ".$arrData[surname];
	?>
	</td>
	</tr>
	<?php
	if (isset($request["booking_id"]) && $request["booking_id"]) {
	    //Get info from the booking we want to change.
		$r=mysqli_query($link, "SELECT * FROM bookings WHERE booking_id={$request[booking_id]}");
		if (mysqli_num_rows($r)) {
			$arrAccomodation=mysqli_fetch_assoc($r);
			//Get 'from' and 'to' dates for this booking.
			$booked_date_fr=substr($arrAccomodation['arrival'],8,2)."-".substr($arrAccomodation['arrival'],5,2)."-".substr($arrAccomodation['arrival'],0,4);
			$booked_date_to=substr($arrAccomodation['planned_departure'],8,2)."-".substr($arrAccomodation['planned_departure'],5,2)."-".substr($arrAccomodation['planned_departure'],0,4);

			// Search the name of the room
			if ($arrAccomodation[room_id]) {
				$r=mysqli_query($link, "SELECT * FROM rooms WHERE room_id={$arrAccomodation[room_id]}");
				$room=mysqli_result($r,0,"room");
			}
			?>
			<input type="hidden" name="booking_id" value="<?=$arrAccomodation["booking_id"]?>">
			<input type="hidden" name="room_id"    value="<?=$arrAccomodation["room_id"]?>">
			<tr><td class="main_message" height="20" colspan="3" valign="bottom">Now</td></tr>
			<tr><td class="text_form" height="25">Arrival</td><td class="text_form" height="25">Departure</td><td class="text_form" height="25">Room</td></tr>
			<tr><td><?=$booked_date_fr?></td><td><?=$booked_date_to?></td><td><?=$room?></td></tr>
			<tr><td class="main_message" height="7" colspan="3"></td></tr>
			<tr><td class="main_message" height="1" colspan="3" valign="bottom" bgcolor="#2F2F5E"></td></tr>
			<tr><td class="main_message" height="30" colspan="3" valign="bottom">Change to </td></tr>
			<?php
		} else {
			echo "<br>";
		}
	}
	?>
    </table>
    <table class="text_form" align="center" cellpadding="1" cellspacing="3" border="0" width="300px">
	<tr>
		<td class="text_form" height="25">Arrival</td>
		<td class="text_form" height="25">Departure</td>
		<td class="text_form" height="25">Room</td>
	</tr>
	<tr>
		<td>
		<?php
		$new_booked_date_fr=$booked_date_fr;
		if (isset($request[date_from])) { $new_booked_date_fr=$request[date_from]; }

		$new_booked_date_to=$booked_date_to;
		if (isset($request[date_to])) { $new_booked_date_to=$request[date_to]; }
		?>
		<input type="text" name="date_from" value="<?=$new_booked_date_fr?>" size="8" id="datepickerfrom" />
		<input type="hidden" name="booking_id" value="<?=$arrAccomodation["booking_id"]?>" />
		</td>
		<td>
            <input type="text" name="date_to" value="<?=$new_booked_date_to?>" size="8" id="datepickerto" />
		</td>
        <td align="center">
			<table cellpadding="0" cellspacing="0">
			<tr>
			<td align="center"><div class="button_off" onMouseOver="this.className='button_on'" onMouseOut="this.className='button_off'">
			<a href="javascript:find_rooms()" class="button_link">Find Rooms</a></div></td>
			</tr>
			</table>
		</td>
	</tr>
	</table>
	<table class="text_form" align="center" cellpadding="1" cellspacing="3" border="0">
	<tr>
	  <td colspan="3"><br>
	  	<?php
		if ( ($request["operation"]=="find") ) {
			// We add one day to the from date. Because the departure date is free. And we rest one for the same reason.
			$timefr_tmp = strtotime($new_booked_date_fr);
			$timefr = $timefr_tmp + (1 * 24 * 60 * 60);
			$new_booked_date_fr = date("Y/m/d", $timefr);

			$timeto_tmp = strtotime($new_booked_date_to);
			$timeto = $timeto_tmp - (1 * 24 * 60 * 60);
			$new_booked_date_to = date("Y/m/d", $timeto);

			// Give me the free rooms for these dates.
			$q="SELECT room_id, room FROM rooms WHERE room_id NOT IN ( ".
			   "SELECT room_id FROM bookings WHERE ".
			   "bookings.arrival           BETWEEN '$new_booked_date_fr' AND '$new_booked_date_to' OR ".
			   "bookings.planned_departure BETWEEN '$new_booked_date_fr' AND '$new_booked_date_to' OR ".
			   "(bookings.arrival <= '$new_booked_date_fr' AND bookings.planned_departure >= '$new_booked_date_to') ) ".
			   "ORDER BY room";
			//AND (status='accepted' OR status='finished') )
			//ver("",$q);
			$r=mysqli_query($link, $q);

			$arrRoom    = array();
			$arrRoomIds = array();
			while ($arrDate=mysqli_fetch_assoc($r)) {
				$arrRooms[]   = $arrDate;
				$arrRoomIds[] = $arrDate['room_id'];
			}

			if ($arrAccomodation[room_id]) {
			    // If I am changing a booking already done, get the resident_id for this room_id.
				$q2="SELECT resident_id FROM bookings WHERE ".
				    "(arrival           BETWEEN '$new_booked_date_fr' AND '$new_booked_date_to' OR ".
				    "planned_departure  BETWEEN '$new_booked_date_fr' AND '$new_booked_date_to' OR ".
				    "(arrival <= '$new_booked_date_fr' AND planned_departure >= '$new_booked_date_to')) AND ".
				    "room_id = {$arrAccomodation[room_id]} AND status='accepted'";
				//ver("q2",$q2);
				$r2=mysqli_query($link, $q2);
				if (mysqli_num_rows($r2)==1) {
					if (mysqli_result($r2,0,"resident_id")==$request["resident_id"]) {
						$arrCurrent["room_id"] = $arrAccomodation["room_id"];
						$arrCurrent["room"]    = $room;
						$arrRooms[]   = $arrCurrent;
						$arrRoomIds[] = $arrCurrent["room_id"];
					}
				}
			}
			function cmp($a, $b) { return strcmp($a["room"], $b["room"]); }
			if ($error) {
				?><p class="question" align="center"><?=$error?></p><?php
			}
			if (count($arrRooms)==0) {
				?><p class="question" align="center">There are no available rooms for these dates</p><?php
			} else {
				?>
				<p class="question" align="center">The following rooms are available for these dates:</p>
				<table align="center" border="0" cellpadding="10" cellspacing="0">
					<?php
					$i=0;
					uasort($arrRooms, "cmp");
					foreach ($arrRooms as $key => $element) {
						if ($i==0) {
							?>
							<tr>
							<?php
						}
						?>
						<td align="center" class="main_message">
						<?php
						if ($request[resident_id]) {
							?><a href="javascript:save('<?=$request[date_from]?>','<?=$request[date_to]?>','<?=$element[room_id]?>','<?=$element[room]?>')"><?=$element[room]?></a><?php
						} else {
							echo $element[room];
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

            // We need to know the room id to do the swap. If we don't have it its
            // becuase we are doing a new booking and in that case there is no swapping to do.
            if (isset($arrAccomodation['room_id'])) {
                // #########################################################################
                // ROOMS TO SWAP
                $strRooms = implode(",",$arrRoomIds);
                // Select rooms that are not free.
                $q="SELECT * FROM rooms WHERE room_id NOT IN (".implode(",",$arrRoomIds).") ORDER BY room";
                //ver("q",$q);
                $r=mysqli_query($link, $q);
                $arrSwapRooms = array();
                if (mysqli_numrows($r)) {
                    while ($arrData=mysqli_fetch_assoc($r)) {
                        // See for each one of these rooms whether we can swap.
                        $q2="SELECT booking_id, room_id, arrival AS arr, planned_departure AS dep, resident_id FROM bookings ".
                            "WHERE (bookings.arrival           BETWEEN '$new_booked_date_fr' AND '$new_booked_date_to' ".
                            "OR     bookings.planned_departure BETWEEN '$new_booked_date_fr' AND '$new_booked_date_to' ".
                            "OR    (bookings.arrival <= '$new_booked_date_fr' AND bookings.planned_departure >= '$new_booked_date_to')) ".
                            "AND    bookings.room_id = {$arrData['room_id']}";
                        //ver("q2",$q2);
                        $r2=mysqli_query($link, $q2);
                        if (mysqli_num_rows($r2)==1) {
                            $arrData2=mysqli_fetch_assoc($r2);
                            $booking_id = $arrData2['booking_id'];
                            $room_id    = $arrData2['room_id'];
                            $resi_id    = $arrData2['resident_id'];
                            $arr       = substr($arrData2['arr'],0,4)."/".substr($arrData2['arr'],5,2)."/".substr($arrData2['arr'],8,2);
                            $dep       = substr($arrData2['dep'],0,4)."/".substr($arrData2['dep'],5,2)."/".substr($arrData2['dep'],8,2);
                            $arrival   = substr($arrData2['arr'],8,2)."/".substr($arrData2['arr'],5,2)."/".substr($arrData2['arr'],0,4);
                            $departure = substr($arrData2['dep'],8,2)."/".substr($arrData2['dep'],5,2)."/".substr($arrData2['dep'],0,4);
                            //echo "room = ".$room_id." arrival = ".$arr." departure = ".$dep."<br>";
                            // Check that this booking fits in just one booking for the selected room.
                            // It's like checking the other way around.
                            $q2="SELECT room_id, arrival AS arr, planned_departure AS dep FROM bookings ".
                                "WHERE (bookings.arrival           BETWEEN '$arr' AND '$dep' ".
                                "OR     bookings.planned_departure BETWEEN '$arr' AND '$dep' ".
                                "OR    (bookings.arrival <= '$arr' AND bookings.planned_departure >= '$dep')) ".
                                "AND    bookings.room_id = $arrAccomodation[room_id]";
                            //ver("q2",$q2);
                            $r2=mysqli_query($link, $q2);
                            //echo "Num Bookings=".mysqli_num_rows($r2)."<br>";
                            if (mysqli_num_rows($r2)==1) {
                                // Search for resident name and surname.
                                $q3="SELECT name, surname FROM residents WHERE resident_id = $resi_id";
                                //ver("q3",$q3);
                                $r3=mysqli_query($link, $q3);
                                $name    = mysqli_result($r3,0,"name");
                                $surname = mysqli_result($r3,0,"surname");
                                $arrData['name']        = $name." ".$surname;
                                $arrData['dates']       = $arrival." - ".$departure;
                                $arrData['resident_id'] = $resi_id;
                                $arrData['booking_id']  = $booking_id;
                                $arrSwapRooms[] = $arrData;
                            }
                        }
                    }
                }

                if (count($arrSwapRooms)==0) {
                    ?><p class="question" align="center">There are no available rooms to swap for these dates</p><?php
                } else {
                    ?>
                    <p class="question" align="center">The following rooms can be swapped for these dates:</p>
                    <table align="center" border="0" cellpadding="10" cellspacing="0">
                        <?php
                        $i=0;
                        uasort($arrSwapRooms, "cmp");
                        foreach ($arrSwapRooms as $key => $element) {
                            if ($i==0) {
                                ?>
                                <tr>
                                <?php
                            }
                            ?>
                            <td align="center" class="main_message">
                            <?php
                            $from = $request[date_from];
                            $to   = $request[date_to];
                            ?>
                            <a href="javascript:swap('<?=$from?>','<?=$to?>','<?=$room?>','<?=$element[room_id]?>','<?=$element[room]?>','<?=$element[name]?>','<?=$element[booking_id]?>')"><?=$element[room]?></a><br><?=$element[name]?><br><?=$element['dates']?>
                            </td>
                            <?php
                            $i++;
                            if ($i==5) { $i=0; }
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
		}
		?>
	  </td>
	  </tr>
	</table>
</td>
</tr>
</form>
</table>
<br>
<table align="center" cellpadding="5" cellspacing="0">
<tr>
	<td align="center"><div class="button_off" onMouseOver="this.className='button_on'" onMouseOut="this.className='button_off'"><a href="admin.php?pagetoload=<?=$from_page?>" class="button_link">Back</a></div></td>
	<!--<td align="center"><div class="button_off" onMouseOver="this.className='button_on'" onMouseOut="this.className='button_off'"><a href="javascript:terms()" title="Terms" class="button_link">Terms</a></div></td>	-->
</tr>
</table>
<br>