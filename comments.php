<?php
require_once('connection.php');
require_once("functions.php");

validate_user();
?>
<LINK href="css/netherhall.css" rel="stylesheet" type="text/css">
<?php
if (isset($request[comments])) {
	$r=mysqli_query($link, "UPDATE bookings SET comments='{$request[comments]}' WHERE booking_id={$request[booking_id]}");
}
$r=mysqli_query($link, "SELECT comments FROM bookings WHERE booking_id={$request[booking_id]}");
$arrData=mysqli_fetch_assoc($r);
?>
<br>
<table align="center" border="0">
	<form name="myform" action="comments.php" method="post">
	<input type="hidden" name="booking_id" value="<?=$request[booking_id]?>">
	<tr>
	  <td class="question">Comments</td>
	</tr>
	<tr>
	<td><textarea name="comments" cols="60" rows="8" class="text_field_additional"><?=$arrData[comments]?></textarea></td>
	</tr>
	</form>
</table>
<br>
<table cellpadding="0" cellspacing="0" align="center">
<tr>
<td align="center" width="47">
<div class="button_off_small" onMouseOver="this.className='button_on_small'" onMouseOut="this.className='button_off_small'"><a href="javascript:document.myform.submit()" class="button_link_small">Save</a></div>
</td>
<td align="center" width="30"></td>
<td align="center" width="47">
<div class="button_off_small" onMouseOver="this.className='button_on_small'" onMouseOut="this.className='button_off_small'"><a href="javascript:window.close()" class="button_link_small">Close</a></div>
</td>
</tr>
</table>
<br>