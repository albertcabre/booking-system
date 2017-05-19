<!-- Header with drop down list for academic years, CSV, PDF, Photos, Send E-Mail -->
<table width="100%" border="0" cellpadding="5" cellspacing="0">
    <tr>
        <td>
            <div align="left" class="Titol_pagina"><?php
                if (!isset($request[academic_year]) || $request[academic_year] == "current") {
                    echo "Current residents";
                    $the_academic_year = "current";
                } elseif ($request[academic_year] == "short") {
                    echo "Short Stages";
                    $the_academic_year = "short";
                } else {
                    $yearto = $request[academic_year] + 1;
                    echo "Residents {$request[academic_year]} - $yearto";
                    $the_academic_year = $request[academic_year];
                }
                echo " (" . mysqli_num_rows($r) . ")";
                ?>
            </div>
        </td>
        <td width="10">
            <select name="academic_year" onChange="document.myform.submit()">
                <option value="current"
                <?php
                if ($request[academic_year] == "current") {
                    echo "selected";
                }
                ?> >Current residents</option>
                <option value="short"
                <?php
                if ($request[academic_year] == "short") {
                    echo "selected";
                }
                ?>>Short stays</option>
                <?php
                $r2 = mysqli_query("SELECT SUBSTR(arrival,1,4) AS year FROM bookings GROUP BY year");
                while ($arrYears = mysqli_fetch_assoc($r2)) {
                    $r3 = mysqli_query("SELECT count(*) AS total FROM bookings WHERE arrival>='{$arrYears[year]}-09-01'");
                    if (mysqli_result($r3, 0, "total") > 0) {
                        $year1 = $arrYears[year];
                        $year2 = $arrYears[year] + 1;
                        $academic_year = $year1 . "-" . $year2;
                        ?>
                        <option value="<?= $year1 ?>"
                        <?php
                        if ($request[academic_year] == $year1) {
                            echo "selected";
                        }
                        ?>>
                        <?= $academic_year ?>
                        </option>
                        <?php
                    }
                }
                ?>
            </select>
        </td>
        <td width="10"><a href="admin.php?pagetoload=groups_list.php" title="Groups"><img src="imgs/group6_16x16.gif" width="16" height="16" border="0"></a></td>
        <td width="10">
            <a href="excel_residents.php?&sort_by=<?= $sort_by ?>&academic_year=<?= $the_academic_year ?>&type=<?= $type ?>" title="Export" target="_blank">
                <img src="imgs/page-white-excel-icon.png" width="16" height="16" border="0">
            </a>
        </td>
        <!-- PDF -->
        <td width="10">
            <a href="pdf_residents.php?&sort_by=<?= $sort_by ?>&academic_year=<?= $the_academic_year ?>&type=<?= $type ?>" title="PDF" target="_blank">
                <img src="imgs/doc_pdf.png" width="16" height="16" border="0">
            </a>
        </td>
        <!-- PDF with photos -->
        <td width="10">
            <a href="pdf_residents_pictures.php?&sort_by=<?= $sort_by ?>&academic_year=<?= $the_academic_year ?>&type=<?= $type ?>" title="PDF with photos" target="_blank">
                <img src="imgs/back.png" width="16" height="16" border="0">
            </a>
        </td>
        <!-- PDF with photos with extra information for internal use-->
        <td width="10">
            <a href="pdf_residents_pictures.php?&sort_by=<?= $sort_by ?>&academic_year=<?= $the_academic_year ?>&type=<?= $type ?>&extra=1" title="PDF with photos, UK phone and Email (only for internal use)" target="_blank">
                <img src="imgs/back_gris.png" width="16" height="16" border="0">
            </a>
        </td>
        <!-- Send E-mail -->
        <td width="21" align="left"><a href="javascript:send_mail()" title="Send E-Mail"><img src="imgs/mail2_16x16.gif" width="16" height="16" border="0"></a></td>
    </tr>
</table>