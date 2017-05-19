<?php
require_once('functions.php');

validate_user();
?>
<LINK href="css/admin.css" rel="stylesheet" type="text/css">
<LINK href="css/netherhall.css" rel="stylesheet" type="text/css">
<TABLE border="0" height="100%" align="center" cellpadding="0" cellspacing="0">
<form name="myform" action="admin.php">
<input type="hidden" name="pagetoload" value="residents_list.php">
	<TR>
	  <TD valign="middle">
		<p align="center" class="question">Search by name, surname, email, telephone, university and nationality
		<br>
		<input type="text" name="name" class="input1" size="20"></p>
		<table align="center" cellpadding="5" cellspacing="0">
		<tr>
			<td align="center">
                <div class="button_off" onMouseOver="this.className='button_on'" onMouseOut="this.className='button_off'">
                    <a href="javascript:document.myform.submit()" class="button_link">Search</a>
                </div>
            </td>
		</tr>
		</table>
	</td>
</tr>
</form>
</table>
<script type="text/javascript">
document.myform.name.focus();
</script>