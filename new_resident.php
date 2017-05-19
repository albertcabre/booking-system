<?php
require_once('functions.php');

validate_user();

if ($request[operation]=="save") {
	echo "save";
	mysql_query("INSERT INTO residenst () VALUES ()");
}
?>
<script type="text/javascript">
function save() {
	document.miform.operation.value="save";
	document.miform.submit();
}
</script>
<LINK href="css/admin.css" rel="stylesheet" type="text/css">
<LINK href="css/netherhall.css" rel="stylesheet" type="text/css">
<TABLE border="0" height="100%" align="center" cellpadding="0" cellspacing="0">
	<TR>
	  <TD valign="middle">
		<p align="center" class="question">New Resident?</p>
		<table align="center" cellpadding="5" cellspacing="0">
		<tr>
			<td align="center"><div class="button_off" onMouseOver="this.className='button_on'" onMouseOut="this.className='button_off'"><a href="admin.php?pagetoload=application_form.php&from=new_resident.php" class="button_link">Yes</a></div></td>

			<td align="center"><div class="button_off" onMouseOver="this.className='button_on'" onMouseOut="this.className='button_off'"><a href="admin.php?pagetoload=search.php" class="button_link">No</a></div></td>

		</tr>
		</table>
	</td>
</tr>
</table>