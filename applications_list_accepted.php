<?php
require_once('functions.php');

validate_user();

if ($request[operation] == "accept") {
    echo "accept";
} elseif ($request[operation] == "reject") {
    echo "rejecting";
}
?>
<LINK href="css/netherhall.css" rel="stylesheet" type="text/css">
<script language="javascript">

    function reject(resident_id, resident_name) {
        confirmation = confirm("Do you want to reject " + resident_name + "?");
        if (confirmation) {
            document.myform.operation.value = "reject";
            document.myform.resident_id.value = resident_id;
            document.myform.submit();
        }
    }

    function check_uncheck_all() {
        if (document.myform.all.checked) {
            for (i = 0; i < document.myform.elements.length; i++) {
                name = document.myform.elements[i].name;
                if (name.match("resident")) {
                    document.myform.elements[i].checked = true;
                }
            }
        } else {
            for (i = 0; i < document.myform.elements.length; i++) {
                name = document.myform.elements[i].name;
                if (name.match("resident")) {
                    document.myform.elements[i].checked = false;
                }
            }
        }
    }

    function send_mail() {
        checked = 0;

        for (i = 0; i < document.myform.elements.length; i++) {
            name = document.myform.elements[i].name;
            if (name.match("resident") && document.myform.elements[i].checked === true) {
                checked++;
            }
        }

        if (checked === 0) {
            alert("Please indicate the resident/s to send an email to.");
        } else {

            window.open('mail.php', 'mywindow', 'width=650,height=400,top=50,left=50,scrollbars=1,resizable=0');

            document.myform.operation.value = "send_mail";
            //document.myform.pagetoload.value="mail.php";
            document.myform.target = "mywindow";
            document.myform.action = "mail.php";
            document.myform.submit();
            //var a = window.setTimeout("document.myform.submit();",200);
        }
    }
</script>
<?php
$today = date("Y", time()) . "-" . date("m", time()) . "-" . date("d", time());
$r = mysqli_query("SELECT r.*, c.country, b.room_id, b.arrival, b.planned_departure, b.booking_date FROM residents r
LEFT JOIN bookings b ON r.resident_id=b.resident_id
LEFT JOIN countries c ON r.country_id = c.country_id WHERE b.status = 'accepted'
AND b.arrival > '$today' ORDER BY b.arrival, r.name, r.surname");
if (mysqli_num_rows($r)) {
    ?>
    <div align="center" class="question">Accepted Applications<?= " (" . mysqli_num_rows($r) . ")" ?></div>
    <br>
    <table width="1200" align="center" border="0" cellpadding="0" cellspacing="0">
        <form name="myform" method="post" action="admin.php">
            <input type="hidden" name="pagetoload" value="applications_list.php">
            <input type="hidden" name="operation">
            <tr>
                <td colspan="25" align="right">
                    <table border="0" width="100%" cellpadding="5" cellspacing="0">
                        <tr>
                            <td></td>
                            <td width="10"><a href="pdf_residents_accepted.php" title="PDF" target="_blank"><img src="imgs/doc_pdf.png" width="16" height="16" border="0"></a></td>
                            <td width="21" align="left"><a href="javascript:send_mail()" title="Send E-Mail"><img src="imgs/mail2_16x16.gif" width="16" height="16" border="0"></a></td>
                        </tr>
                    </table>
                </td>
            <tr>
                <td class="image_cell"></td>
                <td class="titol_taula_list">Date</td>
                <td class="titol_taula_list">Name</td>
                <td class="titol_taula_list">Arrival</td>
                <td class="titol_taula_list">Departure</td>
                <td class="titol_taula_list">Room</td>
                <td class="titol_taula_list">Tele</td>
                <td class="titol_taula_list">City</td>
                <td class="titol_taula_list">Country</td>
                <td class="titol_taula_list">College</td>
                <td class="titol_taula_list">Subject</td>
                <td class="titol_taula_list"><input type="checkbox" name="all" onClick="check_uncheck_all()"></td>
            </tr>
            <?php
        }
        while ($arrData = mysqli_fetch_assoc($r)) {
            //$r2=mysqli_query("SELECT room, telephone FROM bookings LEFT JOIN rooms ON bookings.room_id=rooms.room_id WHERE resident_id={$arrData[resident_id]} AND status='accepted'");
            $r2 = mysqli_query("SELECT room, telephone FROM rooms WHERE room_id={$arrData[room_id]}");
            if (mysqli_num_rows($r2)) {
                $room = mysqli_result($r2, 0, "room");
                $telephone = mysqli_result($r2, 0, "telephone");
            }
            ?>
            <tr class="row1" onMouseOver="this.className = 'row_selected'" onMouseOut="this.className = 'row1'">
                <td class="cell2" height="40">
                    <?php
                    if ($arrData[picture] != "") {
                        echo "<img src='../residentsnh/" . $arrData[picture] . "' width='25' >";
                    } else {
                        echo "<img src='imgs/no_picture.png' width='25' border='0'>";
                    }
                    ?>
                </td>
                <td class="cell2" align="left"><?= $arrData[booking_date] ?></td>
                <td class="cell2" align="left"><a href="admin.php?pagetoload=application_form.php&resident_id=<?= $arrData[resident_id] ?>&from=applications_list_accepted.php" class="table_link2"><?= $arrData[name] . " " . $arrData[surname] ?></a></td>
                <td class="cell2" align="left"><?= mostrar_fecha(substr($arrData[arrival], 0, 10)) ?></td>
                <td class="cell2" align="left"><?= mostrar_fecha(substr($arrData[planned_departure], 0, 10)) ?></td>
                <td class="cell2" align="left"><?= $room ?></td>
                <td class="cell2" align="left"><?= $telephone ?></td>
                <td class="cell2" align="left"><?= $arrData[city] ?></td>
                <td class="cell2" align="left"><?= $arrData[country] ?></td>
                <td class="cell2" align="left"><?= $arrData[college] ?></td>
                <td class="cell2" align="left"><?= $arrData[subject] ?></td>
                <td class="cell2" align="center">
                    <input type="checkbox" name="resident<?= $arrData[resident_id] ?>" value="<?= $arrData[email] ?>">
                </td>
            </tr>
            <?php
        }
        if (mysqli_num_rows($r)) {
            ?>
        </form>
    </table>
    <?php
} else {
    ?><br><br><div align="center" class="question">There are no accepted applications</div><br><br><?php
}
?>