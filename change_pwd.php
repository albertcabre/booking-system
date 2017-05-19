<?php
require_once('functions.php');

validate_user();

if ($request[operation]=="change") {
	if ($request[pwd1]==$request[pwd2]) {
		$r=mysqli_query("SELECT word FROM users");
		$pwd=mysqli_result($r,0,"word");
		if (md5($request[oldpwd])==$pwd) {
			$new_md5_pwd=md5($request[pwd1]);
			$q=mysqli_query("UPDATE users SET word='$new_md5_pwd'");
			$message="Password changed!";
		} else {
			$error="Wrong password!";
		}
	} else {
		$error="The new and old passwords don't match";
	}
}
?>
<LINK href="css/netherhall.css" rel="stylesheet" type="text/css">
<script language="javascript">
function change() {
	if (document.myform.oldpwd.value!=="" && document.myform.pwd1.value!=="" && document.myform.pwd2.value!=="") {
		document.myform.operation.value="change";
		document.myform.submit();
	} else {
		alert("Please insert all the required passwords");
	}
}
</script>
<br>
<TABLE width="900" border="0" height="100%" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<form name="myform" method="post" action="admin.php">
	<input type="hidden" name="pagetoload" value="change_pwd.php">
	<input type="hidden" name="operation">
	<TR>
	  <TD valign="middle">
		<table class="text_form" align="center" cellpadding="2" cellspacing="0">
			<tr>
			<td height="25">Old password </td>
			<td><input type="password" name="oldpwd" size="15"></td>
			</tr>
			<tr>
			<td height="25">New password </td>
			<td><input type="password" name="pwd1" size="15"></td>
			</tr>
			<tr>
			<td height="25">Retype new password </td>
			<td><input type="password" name="pwd2" size="15"></td>
			</tr>
		</table>
		<?php
		if ($error) {
			echo "<p align=center class=error_message>$error</p>";
		} elseif ($message) {
			echo "<p align=center class=main_message>$message</p>";
		}
		?>
		<br>
		<table align="center" cellpadding="5" cellspacing="0">
		<tr>
			<td align="center"><div class="button_off" onMouseOver="this.className='button_on'" onMouseOut="this.className='button_off'"><a href="javascript:change()" class="button_link">Change Password</a></div></td>
		</tr>
		</table>
	  </td>
	</tr>
	</form>
</table>