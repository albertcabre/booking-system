<?php
require_once('functions.php');

validate_user();

$_SESSION[from_nth] = $request[from];

if ($request[operation] == "save_deposit") {
    if (!is_numeric($request[deposit])) {
        $error = 1;
        $error_de = "class=input_error";
    } else {
        $q = "UPDATE residents SET deposit = '$request[deposit]' WHERE resident_id = $request[resident_id]";
        //ver("q",$q);
        mysql_query($q);
    }
} elseif ($request[operation] == "delete_resident") {
    mysql_query("DELETE FROM residents WHERE resident_id=$request[resident_id]");
    mysql_query("DELETE FROM bookings WHERE resident_id=$request[resident_id]");
    @unlink("../residentsnh/" . $request[picture]);
    echo "<script>document.location='admin.php?pagetoload=residents_list.php';</script>";
} elseif ($request[operation] == "change_status") {
    $q = "UPDATE bookings SET status='$request[status]' WHERE booking_id=$request[booking_id]";
    mysql_query($q);
} elseif ($request[operation] == "save") {
    $date_of_birth = $request[year] . "-" . $request[month] . "-" . $request[day];
    $arriv = change_format_date($request[arrival]);
    $depar = change_format_date($request[departure]);
    if ($request[resident_id]) {
        $q = "UPDATE residents SET
                name = \"$request[name]\",
                surname = \"$request[surname]\",
                address_line1 = \"$request[address_line1]\",
                address_line2 = \"$request[address_line2]\",
                postal_code = \"$request[postal_code]\",
                city = \"$request[city]\",
                county = \"$request[county]\",
                country_id = \"$request[country_id]\",
                nationality = \"$request[nationality]\",
                r = \"$request[r]\",
                telephone = \"$request[telephone]\",
                mobile = \"$request[mobile]\",
                ukphone = \"$request[ukphone]\",
                email = \"$request[email]\",
                date_of_birth = \"$date_of_birth\",
                marital_status = \"$request[marital_status]\",
                smoker = \"$request[smoker]\",
                college = \"$request[college]\",
                subject = \"$request[subject]\",
                course = \"$request[mycourse]\",
                academic_year = \"$request[academic_year]\",
                arrival = \"$arriv\",
                departure = \"$depar\",
                deposit = \"$request[deposit]\",
                color = \"$request[color]\"
                WHERE resident_id = $request[resident_id]";
        mysql_query($q);
    } else {
        $today = date("Y-m-d H:m:s");
        $q = "INSERT INTO residents (name, surname, address_line1, address_line2, postal_code, city, county, country_id, nationality, r, telephone, mobile, ukphone, email, date_of_birth, marital_status, smoker, college, subject, course, academic_year, arrival, departure, color, application_date)
		VALUES (\"$request[name]\", \"$request[surname]\", \"$request[address_line1]\", \"$request[address_line2]\", \"$request[postal_code]\", \"$request[city]\", \"$request[county]\", \"$request[country_id]\", \"$request[nationality]\", \"$request[r]\", \"$request[telephone]\", \"$request[mobile]\", \"$request[ukphone]\", \"$request[email]\", \"$date_of_birth\", \"$request[marital_status]\", \"$request[smoker]\", \"$request[college]\", \"$request[subject]\", \"$request[mycourse]\", \"$request[academic_year]\", \"$arriv\", \"$depar\", \"" . random_color() . "\", \"$today\")";
        mysql_query($q);
        $resident_id = mysql_insert_id();
        $request[resident_id] = $resident_id;
    }
}

if ($request[operation] == "refresh") {
    // Check valid numbers before update.
    $error = 0;
    foreach ($request as $key => $value) {
        if (substr($key, 0, 2) == "ra" && substr($key, 2) == $request[booking_id]) {
            $ra = $value;
            $error_ra = "input";
            if (!is_numeric($ra)) {
                $error = 1;
                $error_ra = "class=input_error";
            }
        }
        if (substr($key, 0, 2) == "la" && substr($key, 2) == $request[booking_id]) {
            $la = $value;
            $error_la = "input";
            if (!is_numeric($la)) {
                $error = 1;
                $error_la = "class=input_error";
            }
        }
        if (substr($key, 0, 2) == "hc" && substr($key, 2) == $request[booking_id]) {
            $hc = $value;
            $error_hc = "input";
            if (!is_numeric($hc)) {
                $error = 1;
                $error_hc = "class=input_error";
            }
        }
        if (substr($key, 0, 2) == "pr" && substr($key, 2) == $request[booking_id]) {
            $pr = $value;
            $error_pr = "input";
            if (!is_numeric($pr)) {
                $error = 1;
                $error_pr = "class=input_error";
            }
        }
        if (substr($key, 0, 2) == "ex" && substr($key, 2) == $request[booking_id]) {
            $ex = $value;
            $error_ex = "input";
            if (!is_numeric($ex)) {
                $error = 1;
                $error_ex = "class=input_error";
            }
        }
        if (substr($key, 0, 2) == "re" && substr($key, 2) == $request[booking_id]) {
            $re = $value;
            $error_re = "input";
            if (!is_numeric($re)) {
                $error = 1;
                $error_re = "class=input_error";
            }
        }
        if (substr($key, 0, 2) == "in" && substr($key, 2) == $request[booking_id]) {
            $in = $value;
        }
        if (substr($key, 0, 2) == "ad" && substr($key, 2) == $request[booking_id]) {
            $ad = change_format_date($value);
        }
    }
    if (!$error) {
        $q = "UPDATE bookings SET weekly_rate=$ra, laundry=$la, hc=$hc, printing=$pr, extra=$ex, received=$re, invoice_number='$in', departure='$ad' WHERE booking_id=$request[booking_id]";
        $r = mysql_query($q);
    }
}

if ($request[operation] == "delete") {
    $q1 = "DELETE FROM bookings WHERE booking_id=$request[booking_id]";
    $r1 = mysql_query($q1);

    // Now we check if this resident doesn't have any other booking. If it is true then we move him to received applications.
    $q2 = "SELECT * FROM bookings WHERE resident_id=$request[resident_id]";
    $r2 = mysql_query($q2);
    if (mysql_num_rows($r2) == 0) {
        $q3 = "UPDATE residents SET status=NULL WHERE resident_id=$request[resident_id]";
        $r3 = mysql_query($q3);
    }
}
?>
<script language="JavaScript" src="js/picker.js"></script>
<script language="JavaScript" src="jsp/taules.jsp"></script>
<script language="JavaScript" src="jsp/taules_accomodation.jsp"></script>
<script language="JavaScript" src="js/302pop.js"></script>
<script language="JavaScript" src="js/application_form.js"></script>

<LINK href="css/netherhall.css" rel="stylesheet" type="text/css">
<br>
<?php
if ($request[resident_id]) {
    $r = mysql_query("SELECT * FROM residents WHERE resident_id=$request[resident_id]");
    $arrData = mysql_fetch_assoc($r);
    $arrData = utf8_converter($arrData);
}
?>
<table width="1000" border="0" cellspacing="0" cellpadding="0" align="center">
    <tr>
        <td align="center">
            <?php
            /**
             * Basic Information
             */
            require 'application_form_basic_info.php';
            ?>
        </td>
    </tr>
    <tr>
        <td align="center">
            <table width="99%" border="0" align="center" cellpadding="0" cellspacing="0" class="borde_blau">
                <form method="post" name="miform" id="miform">
                    <input type="hidden" name="pagetoload" value="application_form.php" />
                    <input type="hidden" name="operation" />
                    <input type="hidden" name="status" />
                    <input type="hidden" name="booking_id" />
                    <input type="hidden" name="resident_id" value="<?= $request[resident_id] ?>" />
                    <input type="hidden" name="picture" value="<?= $arrData[picture] ?>">
                    <tr><td>&nbsp;</td></tr>
                    <tr>
                        <td align="center">
                            <?php
                            /**
                             * PERSONAL INFORMATION
                             */
                            require 'application_form_personal_info.php';
                            ?>
                        </td>
                    </tr>
                    <tr><td>&nbsp;</td></tr>
                    <?php
                    if ($request[resident_id]) {
                        ?>
                        <tr>
                            <td align="center">
                                <?php
                                /**
                                 * ACCOUNTS
                                 */
                                require_once("application_form_accounts.php")
                                ?>
                            </td>
                        </tr>
                        <tr><td>&nbsp;</td></tr>
                        <?php
                    }
                    ?>
                </form>
            </table>
        </td>
    </tr>
</table>
<?php
// Display button to go back, View in PDF, Outstanding, Delete resident, etc.
if ($request[resident_id]) {
    ?>
    <table border="0" cellspacing="0" cellpadding="10" align="center">
        <tr>
            <td>
                <input name="back" type="button" class="boton_back_out" id="back"
                       onclick="document.location = 'admin.php?pagetoload=<?= $_SESSION[from_nth] ?>'" value="Back"
                       onmouseover="this.className = 'boton_back'" onmouseout="this.className = 'boton_back_out'"/>
            </td>
            <td>
                <input name="pdf_resident" type="button" class="boton_pdf_out" id="pdf_resident"
                       onclick="pdf(<?= $request[resident_id] ?>)" value="View in pdf"
                       onmouseover="this.className = 'boton_pdf'" onmouseout="this.className = 'boton_pdf_out'"/>
            </td>
            <td>
                <input name="pdf_outstanding" type="button" class="boton_pdf_out" id="pdf_outstanding"
                       onclick="pdf_outstanding(<?= $request[resident_id] ?>)" value="Outstanding"
                       onmouseover="this.className = 'boton_pdf'" onmouseout="this.className = 'boton_pdf_out'"/>
            </td>
            <td>
                <input name="send_mail" type="button" class="boton_mail_out" id="send_mail"
                       onclick="send_mail(<?= $request[resident_id] ?>, '<?= $arrData[email] ?>')" value="Send E-Mail"
                       onmouseover="this.className = 'boton_mail'" onmouseout="this.className = 'boton_mail_out'"/>
            </td>
            <td>
                <input name="send_mail" type="button" class="boton_mail_out" id="send_mail"
                       onclick="send_bill(<?= $request[resident_id] ?>, '<?= $arrData[email] ?>')" value="Send Bill"
                       onmouseover="this.className = 'boton_mail'" onmouseout="this.className = 'boton_mail_out'"/>
            </td>
            <td>
                <input name="delete_resident" type="button" class="boton_delete2_out" id="delete_resident"
                       onclick="javascript:delete_resident('<?= addslashes($arrData[name] . " " . $arrData[surname]) ?>')" value="Delete resident"
                       onmouseover="this.className = 'boton_delete2'" onmouseout="this.className = 'boton_delete2_out'"/>
            </td>
        </tr>
    </table>
    <?php
}

if ($request[operation] == "refresh") {
    echo "<script>window.document.location='#aaa';</script>";
}
?>
