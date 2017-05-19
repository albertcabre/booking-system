<?php
require_once('functions.php');

validate_user();
?>
<LINK href="css/netherhall.css" rel="stylesheet" type="text/css">
<script language="javascript">
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

    function send_mail_resident(resident_id, email) {
        url = 'mail.php?resident' + resident_id + '=' + email;
        window.open(url, 'mywindow', 'width=650,height=400,top=50,left=50,scrollbars=1,resizable=0');
        return false;
    }
</script>
<form name="myform" method="get" action="admin.php">
    <?php
    if ($request[name] != "") {
        $q = "SELECT *, residents.telephone AS residentstelephone FROM residents " .
             "LEFT JOIN countries on residents.country_id = countries.country_id " .
             "WHERE name     LIKE '%{$request[name]}%' ".
             "OR surname     LIKE '%{$request[name]}%' ".
             "OR email       LIKE '%{$request[name]}%' ".
             "OR telephone   LIKE '%{$request[name]}%' ".
             "OR ukphone     LIKE '%{$request[name]}%' ".
             "OR college     LIKE '%{$request[name]}%' ".
             "OR nationality LIKE '%{$request[name]}%' ";
        $r = mysqli_query($link, $q);
    } else {
        $today = date("Y", time()) . "-" . date("m", time()) . "-" . date("d", time());
        if (!isset($request[academic_year]) || $request[academic_year] == "current") {
            // >= 28 days = 4 weeks
            //$condition_search=" AND bookings.arrival <= '$today' AND bookings.departure >= '$today' AND DATEDIFF(bookings.departure,bookings.arrival) >= 28  ";
            // 06-Feb-2010 - They want to see all the residents in the current residents list. So I remove the condition about days.
            $condition_search = " AND bookings.arrival <= '$today' AND bookings.planned_departure >= '$today' ";
        } elseif ($request[academic_year] == "short") {
            // < 28 days = 4 weeks
            $condition_search = " AND bookings.arrival <= '$today' AND bookings.planned_departure >= '$today' ".
                                " AND DATEDIFF(bookings.planned_departure,bookings.arrival) < 28 ";
        } else {
            //$condition_search=" AND bookings.arrival>='{$request[academic_year]}-09-01' AND DATEDIFF(bookings.departure,bookings.arrival) >= 28 ";
            //$condition_search=" AND bookings.planned_departure>'{$request[academic_year]}-10-01' AND DATEDIFF(bookings.planned_departure,bookings.arrival) >= 28 ";
            $condition_search = " AND bookings.planned_departure>'{$request[academic_year]}-10-01' " .
                                " AND bookings.planned_departure<'" . ($request[academic_year] + 1) . "-10-01' " .
                                " AND DATEDIFF(bookings.departure,bookings.arrival) > 28 ";
        }

        $type = "";
        if (isset($request[type])) {
            $type = $request[type];
        }

        if ($request[sort_by] == "name") {
            $sort = "ORDER BY name $type";
        } elseif ($request[sort_by] == "surname") {
            $sort = "ORDER BY surname $type";
        } elseif ($request[sort_by] == "arrival") {
            $sort = "ORDER BY barrival $type";
        } elseif ($request[sort_by] == "departure") {
            $sort = "ORDER BY bdeparture $type";
        } elseif ($request[sort_by] == "room") {
            $sort = "ORDER BY room $type";
        } elseif ($request[sort_by] == "nationality") {
            $sort = "ORDER BY nationality $type";
        } else {
            $sort = "ORDER BY surname, name DESC";
        }

        if ($type == "DESC") {
            $new_type = "ASC";
        } else {
            $new_type = "DESC";
        }

        $q = "SELECT *, residents.telephone AS residentstelephone, bookings.arrival AS barrival, bookings.planned_departure AS bdeparture ".
             "FROM bookings " .
             "LEFT JOIN residents ON bookings.resident_id=residents.resident_id " .
             "LEFT JOIN rooms ON bookings.room_id=rooms.room_id " .
             "LEFT JOIN countries ON residents.country_id=countries.country_id " .
             "WHERE (bookings.status='accepted' OR bookings.status='finished') " .
             $condition_search .
             "GROUP BY NAME, surname $sort";
        $r = mysqli_query($link, $q);
    }

    if (mysqli_num_rows($r)) {
        ?>
        <table width="1200" border="0" align="center" cellpadding="0" cellspacing="0">
            <input type="hidden" name="pagetoload" value="residents_list.php">
            <input type="hidden" name="operation">

            <tr>
                <td colspan="13" onMouseOver="this.style.color = '#FFFFFF'" onMouseOut="this.style.color = '#CCCCCC'" align="right" style="padding-right:0px">

                <?php
                /**
                 * This is the header with buttons to export into CSV, make a PDF, send an email, etc.
                 */
                require 'residents_list_header.php';
                ?>

                </td>
            </tr>

            <!-- Report Header: Name, Surname, etc -->
            <tr valign="middle">
                <td class="image_cell"></td>

                <?php $href1 = "admin.php?pagetoload=residents_list.php&sort_by=name&academic_year=$the_academic_year&type=$new_type"; ?>
                <td class="titol_taula_list"><a href="<?= $href1 ?>" class="header_link2">Name</a></td>

                <?php $href2 = "admin.php?pagetoload=residents_list.php&sort_by=surname&academic_year=$the_academic_year&type=$new_type"; ?>
                <td class="titol_taula_list"><a href="<?= $href2 ?>" class="header_link2">Surname</a></td>

                <?php $href3 = "admin.php?pagetoload=residents_list.php&sort_by=arrival&academic_year=$the_academic_year&type=$new_type"; ?>
                <td class="titol_taula_list"><a href="<?= $href3 ?>" class="header_link2">Arrival</a></td>

                <?php $href4 = "admin.php?pagetoload=residents_list.php&sort_by=departure&academic_year=$the_academic_year&type=$new_type"; ?>
                <td class="titol_taula_list"><a href="<?= $href4 ?>" class="header_link2">Departure</a></td>

                <?php $href5 = "admin.php?pagetoload=residents_list.php&sort_by=room&academic_year=$the_academic_year&type=$new_type"; ?>
                <td class="titol_taula_list"><a href="<?= $href5 ?>" class="header_link2">Room</a></td>

                <td class="titol_taula_list">Telephone</td>

                <td class="titol_taula_list">UK Phone</td>

                <td class="titol_taula_list">Email</td>

                <?php $href6 = "admin.php?pagetoload=residents_list.php&sort_by=nationality&academic_year=$the_academic_year&type=$new_type"; ?>
                <td class="titol_taula_list"><a href="<?= $href6 ?>" class="header_link2">Nationality</a></td>

                <td class="titol_taula_list" align="center" width="24">
                    <input type="checkbox" name="all" onClick="check_uncheck_all()">
                </td>
            </tr>

            <?php
            $arrDataIDs = array();
            while ($arrData = mysqli_fetch_assoc($r)) {
                $arrData = utf8_converter($arrData);
                // If we are searching then we need to filter by residnet ID otherwise we would have duplicates.
                if ($request[name] != "") {
                    if (!in_array($arrData[resident_id], $arrDataIDs)) {
                        $arrDataIDs[] = $arrData[resident_id];
                    } else {
                        continue;
                    }
                }
                // Display basic data for every resident.
                $location = "document.location.href='admin.php?pagetoload=application_form.php&resident_id=$arrData[resident_id]&from=residents_list.php'";
                $goToResident = "onclick=\"$location\"";
                ?>
                <tr class="row1" onMouseOver="this.className = 'row_selected'" onMouseOut="this.className = 'row1'">
                    <td class="cell2" height="40" align="center" valign="middle" <?= $goToResident ?>>
                        <?php
                        if ($arrData[picture] != "" && is_file("../residentsnh/" . $arrData[picture])) {
                            echo "<img src='../residentsnh/" . $arrData[picture] . "' width='30' height='40'>";
                        } else {
                            echo "<img src='imgs/no_picture.png' width='30' border='0'>";
                        }
                        ?>
                    </td>
                    <td valign="middle" class="cell2" align="left" <?= $goToResident ?>><?= $arrData[name] ?></td>
                    <td valign="middle" class="cell2" align="left" <?= $goToResident ?>><?= $arrData[surname] ?></td>
                    <td valign="middle" class="cell2" align="left" <?= $goToResident ?>><?= mostrar_fecha(substr($arrData[barrival], 0, 10)) ?></td>
                    <td valign="middle" class="cell2" align="left" <?= $goToResident ?>><?= mostrar_fecha(substr($arrData[bdeparture], 0, 10)) ?></td>
                    <td valign="middle" class="cell2" align="left" <?= $goToResident ?>><?= $arrData[room] ?></td>
                    <td valign="middle" class="cell2" align="left" <?= $goToResident ?>><?= $arrData[residentstelephone] ?></td>
                    <td valign="middle" class="cell2" align="left" <?= $goToResident ?>><?= $arrData[ukphone] ?></td>
                    <td valign="middle" class="cell2" align="left">
                        <a href="#" class="table_link2" title="Click to send an email"
                           onclick="send_mail_resident(<?= $arrData[resident_id] ?>,'<?= $arrData[email] ?>'); return false;"><?= $arrData[email] ?></a>
                    </td>
                    <td valign="middle" class="cell2" align="left" <?= $goToResident ?>><?= $arrData[nationality] ?></td>
                    <td class="cell2" align="center">
                        <input type="checkbox" name="resident<?= $arrData[resident_id] ?>" value="<?= $arrData[email] ?>">
                    </td>
                </tr>
                <?php
            }
            ?>
        </table>
        <?php
    } else {
        // If there are no residents for the selected year or search display a message.
        ?>
        <table border="0" height="100%" align="center" cellpadding="0" cellspacing="0">
        <?php
        if ($request[name] != "") {
            // If there are no residents for the search display a message.
            ?>
            <tr>
                <td valign="middle">
                    <p align="center" class="question">No residents found with "<?= $request[name] ?>"</p>
                    <table align="center" cellpadding="5" cellspacing="0">
                        <tr>
                            <td align="center">
                                <div class="button_off" onMouseOver="this.className = 'button_on'" onMouseOut="this.className = 'button_off'">
                                    <a href="admin.php?pagetoload=search.php" class="button_link">Search again</a>
                                </div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <?php
        } elseif ($request[academic_year] != "" || !isset($request[academic_year])) {
            // If there are no residents for the selected year display year drop down list
            ?>
            <tr>
                <td>
                    <table align="center">
                        <tr>
                            <td>
                                <select name="academic_year" onChange="document.myform.submit()">
                                    <option value="current"
                                    <?php if ($request[academic_year] == "current") {
                                        echo "selected";
                                    }
                                    ?> >Current residents</option>
                                    <option value="short"
                                    <?php
                                    if ($request[academic_year] == "short") {
                                        echo "selected";
                                    } ?>>Short stages</option>
                                    <?php
                                    $r2 = mysqli_query($link, "SELECT SUBSTR(arrival,1,4) AS year FROM bookings GROUP BY year");
                                    while ($arrYears = mysqli_fetch_assoc($r2)) {
                                        $r3 = mysqli_query($link, "SELECT count(*) AS total FROM bookings WHERE arrival>='{$arrYears[year]}-09-01'");
                                        if (mysqli_result($r3, 0, "total") > 0) {
                                            $year1 = $arrYears[year];
                                            $year2 = $arrYears[year] + 1;
                                            $academic_year_display = $year1 . "-" . $year2;
                                            ?><option value="<?= $year1 ?>" <?php if ($request[academic_year]==$year1) echo "selected" ?> ><?= $academic_year_display ?></option><?php
                                        }
                                    }
                                    ?>
                                </select>
                            </td>
                            <td width="10"><a href="admin.php?pagetoload=groups_list.php" title="Groups"><img src="imgs/group6_16x16.gif" width="16" height="16" border="0"></a></td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td valign="middle">
                    <p align="center" class="question">There are no people in
                    <?php
                    if (!isset($request[academic_year]) || $request[academic_year] == "current") {
                        echo "Current residents";
                    } elseif ($request[academic_year] == "short") {
                        echo "Short stages";
                    }
                    ?>
                    </p>
                </td>
            </tr>
            <?php
        }
        ?>
        </table>
        <?php
    }
    ?>
</form>
