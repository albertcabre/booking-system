<?php
require_once('connection.php');
require_once('functions.php');

validate_user();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <title>Netherhall House</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"/>
        <LINK href="css/netherhall.css" rel="stylesheet" type="text/css"/>
        <script src="js/302pop.js" type="text/javascript"></script>
    </head>
    <body>
        <?php
        $pagetoload = $request[pagetoload];

        if ($pagetoload == "") {
            $pagetoload = "home.php";
        }
        ?>
        <table width="1230" align="center" border="0" cellpadding="0" cellspacing="0">
            <tr>
                <td style="height: 20px; background-color: #0395CC; color: white; font-weight: bold; text-align: right; padding-right: 5px">
                    <div style="padding-left: 5px; float: left">Netherhall House</div>
                    Netherhall House, <?php echo $_SESSION['worldresidents_username']; ?>
                </td>
            </tr>
            <tr>
                <td style="background-color: black"><?php include("menu.php"); ?></td>
            </tr>
            <tr>
                <td style="padding-top: 10px; padding-bottom: 30px; background-color: white; background-repeat: repeat;">
                <?php
                include($pagetoload);
                ?>
                </td>
            </tr>
            <tr>
                <td class="peu_pagina">&copy; 2017 Netherhall House</td>
            </tr>
        </table>
    </body>
</html>