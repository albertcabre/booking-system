<?php
require_once('functions.php');

validate_user();

$operation = $request[operation];
$overdue_days = ($request[overdue_days] != "") ? $request[overdue_days] : 15;
?>
<LINK href="css/netherhall.css" rel="stylesheet" type="text/css">
<script type="text/javascript">
    function delete_account(booking_id) {
        confirmation = confirm("Do you want to remove this account from this list?");
        if (confirmation) {
            document.miform.operation.value = "delete_account";
            document.miform.booking_id_to_delete.value = booking_id;
            document.miform.submit();
        }
    }
    function update(id_to_jump) {
        //alert(document.miform.focus.value);
        document.miform.operation.value = "update";
        document.miform.id_to_jump.value = id_to_jump;
        document.miform.submit();
    }
    function comments(booking_id) {
        window.open('comments.php?booking_id=' + booking_id + '', 'mywindow', 'width=420,height=240,top=200,left=200');
    }
    function calculate(name) {
        expression = eval("document.miform." + name + ".value");
        value = eval(expression);
        eval("document.miform." + name + ".value=" + value);
    }
    function simplified() {
        document.miform.operation.value = "simplified";
        document.miform.submit();
    }
    function full() {
        document.miform.operation.value = "";
        document.miform.submit();
    }
    function overdue_payments() {
        document.myform_menu.operation.value = "overdue";
        document.myform_menu.submit();
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
            overdue_payments();
            return false;
        } else {
            return true;
        }
    }
</script>
<?php
$arrClasses = array();

if ($request[operation] == "delete_account") {
    $q = "UPDATE bookings SET done=1 WHERE booking_id={$request[booking_id_to_delete]}";
    $r = mysqli_query($link, $q);
} elseif ($request[operation] == "update") {
    foreach ($request as $key => $value) {
        //ver("key",$key);
        //ver("value",$value);
        if (substr($key, 0, 10) == "booking_id") {
            $id = $value;
        }

        if (substr($key, 0, 2) == "ra") {
            $ra = $value;
            $arrClasses[$id]['ra'] = "input_small";
            if (!is_numeric($ra)) {
                $error = 1;
                $arrClasses[$id]['ra'] = "input_small_error";
            } else {
                $q = "UPDATE bookings SET weekly_rate='$ra' WHERE booking_id=$id";
                //ver("q",$q);
                $r = mysqli_query($link, $q);
            }
        }
        if (substr($key, 0, 2) == "la") {
            $la = $value;
            $arrClasses[$id]['la'] = "input_small";
            if (!is_numeric($la)) {
                $error = 1;
                $arrClasses[$id]['la'] = "input_small_error";
            } else {
                $q = "UPDATE bookings SET laundry='$la' WHERE booking_id=$id";
                //ver("q",$q);
                $r = mysqli_query($link, $q);
            }
        }
        if (substr($key, 0, 2) == "hc") {
            $hc = $value;
            $arrClasses[$id]['hc'] = "input_small";
            if (!is_numeric($hc)) {
                $error = 1;
                $arrClasses[$id]['hc'] = "input_small_error";
            } else {
                $q = "UPDATE bookings SET hc='$hc' WHERE booking_id=$id";
                //ver("q",$q);
                $r = mysqli_query($link, $q);
            }
        }
        if (substr($key, 0, 2) == "pr") {
            $pr = $value;
            $arrClasses[$id]['pr'] = "input_small";
            if (!is_numeric($pr)) {
                $error = 1;
                $arrClasses[$id]['pr'] = "input_small_error";
            } else {
                $q = "UPDATE bookings SET printing='$pr' WHERE booking_id=$id";
                //ver("q",$q);
                $r = mysqli_query($link, $q);
            }
        }
        if (substr($key, 0, 2) == "ex") {
            $ex = $value;
            $arrClasses[$id]['ex'] = "input_small";
            if (!is_numeric($ex)) {
                $error = 1;
                $arrClasses[$id]['ex'] = "input_small_error";
            } else {
                $q = "UPDATE bookings SET extra='$ex' WHERE booking_id=$id";
                //ver("q",$q);
                $r = mysqli_query($link, $q);
            }
        }
        if (substr($key, 0, 2) == "re") {
            $re = $value;
            $arrClasses[$id]['re'] = "input_small";
            if (!is_numeric($re)) {
                $error = 1;
                $arrClasses[$id]['re'] = "input_small_error";
            } else {
                $q = "UPDATE bookings SET received='$re' WHERE booking_id=$id";
                //ver("q",$q);
                $r = mysqli_query($link, $q);
            }
        }
        if (substr($key, 0, 2) == "bi") {
            if ($value == 'Yes') {
                $q = "UPDATE bookings SET billed=1 WHERE booking_id=$id";
            } else {
                $q = "UPDATE bookings SET billed=0 WHERE booking_id=$id";
            }
            //ver("q",$q);
            $r = mysqli_query($link, $q);
        }
        if (substr($key, 0, 2) == "in") {
            $q = "UPDATE bookings SET invoice_number='$value' WHERE booking_id=$id";
            //ver("q",$q);
            $r = mysqli_query($link, $q);
        }
    }
}
$width = ($operation == "simplified") ? 800 : 1000;
?>
<form name="myform_menu" method="post">
<table width="<?= $width ?>" align="center" border="0" cellpadding="4" cellspacing="0">
    <input type="hidden" name="pagetoload" value="residents_expenses.php">
    <input type="hidden" name="operation">
    <tr>
        <td width="90%" align="center">
            <?php
            if ($operation == 'simplified') {
                ?>
                <a href="javascript:full()" class="table_link2">View outstanding full</a>
                <?php
            } else {
                ?>
                <a href="javascript:simplified()" class="table_link2">View outstanding simplified</a>
                <?php
            }
            ?>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <a href="javascript:overdue_payments()" class="table_link2">View overdue payments</a>
            <input type="text" name="overdue_days" value="<?= $overdue_days ?>" size="3" class="input_small" onKeyPress="return submitenter(this, event)">
            days
            <img src="imgs/information.png" title="Overdue payments for the specified date and since the arrival date">
        </td>
        <td align="right">
            <a href="residents_expenses_csv.php" title="CSV" target="_blank"><img src="imgs/page_white_excel.png" border="0" width="16" height="16"></a>&nbsp;
            <a href="pdf_residents_expenses.php" title="PDF" target="_blank"><img src="imgs/doc_pdf.png" border="0" width="16" height="16"></a>
        </td>
    </tr>
</table>
</form>
<?php
if ($operation == "simplified") { echo "<br>"; }
//The purpose of this page should only be to keep track of the residents who are currently in Netherhall.
$today = date('Y-m-d');
if ($operation == 'overdue') {
    $condition = "AND DATEDIFF('$today', bookings.arrival) >= $overdue_days ";
} else {
    $condition = "AND bookings.arrival <= '$today' ";
}
$q = "SELECT residents.resident_id, NAME, surname " .
     "FROM residents LEFT JOIN bookings ON residents.resident_id = bookings.resident_id " .
     "WHERE bookings.status='accepted' " .
     "AND bookings.done=0 " .
     $condition.
     "GROUP BY residents.resident_id ORDER BY surname, NAME";
$r = mysqli_query($link, $q);
if (mysqli_num_rows($r)) {
    ?>
    <table width="<?= $width ?>" align="center" border="0" cellpadding="4" cellspacing="0">
    <form name="miform" method="post">
    <input type="hidden" name="pagetoload" value="residents_expenses.php">
    <input type="hidden" name="operation">
    <input type="hidden" name="id_to_jump">
    <input type="hidden" name="booking_id_to_delete">
    <?php
    $count = 0;
    while ($arrInfo = mysqli_fetch_assoc($r)) {
        $arrInfo = utf8_converter($arrInfo);
        $count++;
        if ($count == 20) $count = 1;
        $total_days = 0;
        $total_total_rent = 0;
        $total_laundry = 0;
        $total_hc = 0;
        $total_printing = 0;
        $total_extra = 0;
        $total_due = 0;
        $total_received = 0;
        $total_outstanding = 0;

        $href = "admin.php?pagetoload=application_form.php&resident_id=" . $arrInfo[resident_id] . "&from=residents_expenses.php";
        $name = "";
        if ($arrInfo[surname] != "") {
            $name = $arrInfo[surname] . ", ";
        }
        $name.= $arrInfo[NAME];

        if ($operation != "simplified") {
            // Display resident name
            ?>
            <tr class="row1">
                <td colspan="19" height="40" valign="bottom" align="left">
                    <a name="jump<?= $arrInfo[resident_id] ?>" href="<?= $href ?>" class="table_link2"><?= $name ?></a>
                </td>
            </tr>
            <?php
        }
        ?>

        <!-- Display header -->

        <?php
        if ($operation == "simplified") {
            if ($count == 1) {
                // Display empty row
                ?>
                <tr class="row1_small" style="height: 40px">
                <td class="td_header" align="left">Name</td>
                <td class="td_header" align="right">Days</td>
                <td class="td_header" align="right">Total</td>
                <td class="td_header" align="right">Laundry</td>
                <td class="td_header" align="right">HC</td>
                <td class="td_header" align="right">Printing</td>
                <td class="td_header" align="right">Extra</td>
                <td class="td_header" align="right">Due</td>
                <td class="td_header" align="right">Received</td>
                <td class="td_header" align="right">Outstanding</td>
                </tr>
                <?php
            }
        } else {
            ?>
            <tr class="row1_small">
            <td class="td_header">Arrival</td>
            <td class="td_header">Departure</td>
            <td class="td_header">Room</td>
            <td class="td_header" align="right">Days</td>
            <td class="td_header">W.Rate</td>
            <td class="td_header" align="right">Total</td>
            <td class="td_header">Laundry</td>
            <td class="td_header">HC</td>
            <td class="td_header">Printing</td>
            <td class="td_header">Extra</td>
            <td class="td_header" align="right">Due</td>
            <td class="td_header">Received</td>
            <td class="td_header" align="right">Outstanding</td>
            <td class="td_header">Billed</td>
            <td class="td_header">Inv.Num.</td>
            <td class="td_header"></td>
            <td class="td_header"></td>
            <td class="td_header"></td>
            </tr>
            <?php
        }
        require 'residents_expenses_bookings.php';
    }
    ?>

    <!-- GRAND TOTAL HEADER -->

    <?php
    $colspan1 = ($operation == "simplified") ? 10 : 19;
    ?>
    <tr class="row1_small"><td colspan="<?= $colspan1 ?>" height='20'></td></tr>

    <tr class="row1_small">
    <?php
    $colspan2 = ($operation == "simplified") ? 2 : 5;
    ?>
    <td  height="30" colspan="<?= $colspan2 ?>" class="td_header"></td>
    <td class="td_header" align="right">Total</td>
    <td class="td_header" align="right">Total<br>Laundry</td>
    <td class="td_header" align="right">Total<br>HC</td>
    <td class="td_header" align="right">Total<br>Printing</td>
    <td class="td_header" align="right">Total<br>Extra</td>
    <td class="td_header" align="right">Total<br>Due</td>
    <td class="td_header" align="right">Total<br>Received</td>
    <td class="td_header" align="right">Total<br>Outstanding</td>
    <?php
    if ($operation != "simplified") {
        ?><td colspan="5" class="td_header">&nbsp;</td><?php
    }
    ?>
    </tr>

    <!-- GRAND TOTAL VALUES -->
    <tr class="row1_small">
    <td class="td_total" colspan="<?= $colspan2 ?>">&nbsp;</td>
    <td class="td_total" align="right"><?= number_format($grand_total_rent, 2, ".", ",") ?></td>
    <td class="td_total" align="right"><?= number_format($grand_total_laundry, 2, ".", ",") ?></td>
    <td class="td_total" align="right"><?= number_format($grand_total_hc, 2, ".", ",") ?></td>
    <td class="td_total" align="right"><?= number_format($grand_total_printing, 2, ".", ",") ?></td>
    <td class="td_total" align="right"><?= number_format($grand_total_extra, 2, ".", ",") ?></td>
    <td class="td_total" align="right"><?= number_format($grand_total_due, 2, ".", ",") ?></td>
    <td class="td_total" align="right"><?= number_format($grand_total_received, 2, ".", ",") ?></td>
    <td class="td_total" align="right"><?= number_format($grand_total_outstanding, 2, ".", ",") ?></td>
    <?php
    if ($operation != "simplified") {
        ?><td colspan="5" class="td_total">&nbsp;</td><?php
    }
    ?>
    </tr>
    </form>
    </table>
    <br>
    <?php
    if ($operation != "simplified") {
        ?>
        <table align="center" cellpadding="5" cellspacing="0">
        <tr>
            <td align="center">
                <div class="button_off" onMouseOver="this.className = 'button_on'" onMouseOut="this.className = 'button_off'">
                    <a href="javascript:update()" class="button_link">Update</a>
                </div>
            </td>
        </tr>
        </table>
        <?php
    }
}

if ($request[operation] == "update") {
    ?><script>window.document.location = '#jump<?= $request[id_to_jump] ?>';</script><?php
}
?>
