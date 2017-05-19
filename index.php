<?php
require_once('connection.php');
require_once('functions.php');

if ($_SESSION['worldresidents_rgstrd']) {
    exit(header("Location: admin.php"));
}

$error = 0;

if ($request[username] === "" || $request[password] === "") {
    $error = 1;
}

if ($request[password] != "") {
    $md5pwd = md5($request[password]);
    $rc_admin = mysqli_query($link, "SELECT * FROM user WHERE username='$request[username]' AND password='$md5pwd'");
    if ($rc_admin) {
        if (mysqli_num_rows($rc_admin) > 0) {
            $_SESSION['worldresidents_rgstrd'] = 1;
            $_SESSION['worldresidents_username'] = $request[username];
            header("Location: admin.php");
        } else {
            $error = 1;
        }
    }
}
?>
<HTML>
    <HEAD>
        <TITLE>Netherhall House</TITLE>
        <META http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <LINK href="css/admin.css" rel="stylesheet" type="text/css">
        <LINK href="css/netherhall.css" rel="stylesheet" type="text/css">
    </HEAD>
    <SCRIPT language="javascript" type="text/javascript">
        <!--
        function submit_data() {
            if (document.getElementById('username').value === "" || document.getElementById('password').value === "") {
                alert("Username and password can't be empty!");
            } else {
                document.form1.submit();
            }
        }

        function submitenter(myfield, e) {
            var keycode;
            if (window.event)
                keycode = window.event.keyCode;
            else if (e)
                keycode = e.which;
            else
                return true;

            if (keycode === 13) {
                myfield.form.submit();
                return false;
            } else {
                return true;
            }
        }
-->
    </SCRIPT>
    <BODY onLoad="document.getElementById('username').focus();">
        <TABLE width="900" border="0" height="100%" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
            <TR>
                <TD valign="middle">
                    <TABLE width="240" border="0" cellspacing="0" cellpadding="5" align="center" style="border: 2px solid #2F2F5E;">
                        <FORM name="form1" method="post" action="index.php">
                            <TR>
                                <TD align="center">
                                    <TABLE width="200" border="0" cellpadding="5" cellspacing="5">
                                        <TR>
                                            <TD height="7" colspan="2" class="question" align="center">Netherhall House</TD>
                                        </TR>
                                        <TR>
                                            <TD height="24" align="left" class="text_login">Username</TD>
                                            <TD height="24" align="right">
                                                <INPUT name="username" type="text" class="input_username" id="username" size="24"
                                                       onKeyPress="return submitenter(this, event)" maxlength="40">
                                            </TD>
                                        </TR>
                                        <TR>
                                            <TD height="24" align="left" class="text_login">Password</TD>
                                            <TD height="24" align="right">
                                                <INPUT name="password" type="password" class="input_pwd" id="password" size="24"
                                                       onKeyPress="return submitenter(this, event)" maxlength="40">
                                            </TD>
                                        </TR>
                                        <?php
                                        if ($error) {
                                            ?>
                                            <TR>
                                                <TD colspan="2" class="error_message"
                                                    align="center">We don't recognise that email and password.<br>Please try again.</TD>
                                            </TR>
                                            <?php
                                        }
                                        ?>
                                    </TABLE>
                                    <?php button("javascript:submit_data()", "Sign In") ?>
                                </TD>
                            </TR>
                        </FORM>
                    </TABLE>
                </TD>
            </TR>
        </TABLE>
    </BODY>
</HTML>