<LINK href="css/netherhall.css" rel="stylesheet" type="text/css">
<script language="javascript">
function check_uncheck_all() {
	if (document.myform.all.checked) {
		for (i = 0; i < document.myform.elements.length; i++) {		
			name=document.myform.elements[i].name;
			if (name.match("resident")) { document.myform.elements[i].checked = true; }
		}
	} else {
		for (i = 0; i < document.myform.elements.length; i++) {		
			name=document.myform.elements[i].name;
			if (name.match("resident")) { document.myform.elements[i].checked = false; }
		}
	}
}

function send_mail() {
	checked=0;
	
	for (i = 0; i < document.myform.elements.length; i++) {		
		name=document.myform.elements[i].name;		
		if (name.match("resident") && document.myform.elements[i].checked == true) { checked++; }
	}
	
	if (checked==0) {
		alert("Please indicate the resident/s to send an email to.");
	} else {		
	
		window.open('mail.php','mywindow','width=650,height=400,top=50,left=50,scrollbars=1,resizable=0');	
	
		document.myform.operation.value="send_mail";
		//document.myform.pagetoload.value="mail.php";
		document.myform.target="mywindow";
		document.myform.action="mail.php";
		document.myform.submit();	
		//var a = window.setTimeout("document.myform.submit();",200); 
	}
}
</script>
<form name="myform" method="get" action="admin.php">
<?
//ver_array("REQUEST",$_REQUEST);
if ($_REQUEST[name]!="") {
	$r=mysqli_query($link, "SELECT * FROM residents LEFT JOIN countries on residents.country_id = countries.country_id WHERE name LIKE '%{$_REQUEST[name]}%' OR surname LIKE '%{$_REQUEST[name]}%'");	
} else {
	$today=date("Y",time())."-".date("m",time())."-".date("d",time());
	if (!isset($_REQUEST[academic_year]) || $_REQUEST[academic_year]=="current") {
		// >= 28 days = 4 weeks
		//$condition_search=" AND bookings.arrival <= '$today' AND bookings.departure >= '$today' AND DATEDIFF(bookings.departure,bookings.arrival) >= 28  ";
		// 06-Feb-2010 - They want to see all the residents in the current residents list. So I remove the condition about days.
		$condition_search=" AND bookings.arrival <= '$today' AND bookings.planned_departure >= '$today' ";
	} elseif ($_REQUEST[academic_year]=="short") {
		// < 28 days = 4 weeks
		$condition_search=" AND bookings.arrival <= '$today' AND bookings.planned_departure >= '$today' AND DATEDIFF(bookings.planned_departure,bookings.arrival) < 28 ";
	} else {
	 	//$condition_search=" AND bookings.arrival>='{$_REQUEST[academic_year]}-09-01' AND DATEDIFF(bookings.departure,bookings.arrival) >= 28 "; 
		$condition_search=" AND bookings.planned_departure>'{$_REQUEST[academic_year]}-10-01' AND DATEDIFF(bookings.planned_departure,bookings.arrival) >= 28 "; 
	}			
	
	$type="";
	if (isset($_REQUEST[type]))
		$type=$_REQUEST[type];
	
	if ($_REQUEST[sort_by]=="name") {
		$sort="ORDER BY name $type";
	}
	elseif ($_REQUEST[sort_by]=="surname") {
		$sort="ORDER BY surname $type";
	}
	elseif ($_REQUEST[sort_by]=="arrival") {
		$sort="ORDER BY barrival $type";
	}
	elseif ($_REQUEST[sort_by]=="departure") {
		$sort="ORDER BY bdeparture $type";
	}
	elseif ($_REQUEST[sort_by]=="room") {
		$sort="ORDER BY room $type";
	} else {
		$sort="ORDER BY surname, name DESC";
	}		
		
	if ($type=="DESC")
		$new_type="ASC";
	else
		$new_type="DESC";
		
	$q="SELECT *, bookings.arrival AS barrival, bookings.planned_departure AS bdeparture FROM bookings 
	LEFT JOIN residents ON bookings.resident_id=residents.resident_id 
	LEFT JOIN rooms ON bookings.room_id=rooms.room_id 
	LEFT JOIN countries ON residents.country_id=countries.country_id   
	WHERE bookings.status='accepted' 
	$condition_search 
	GROUP BY NAME, surname 
	$sort";
	//ver("q",$q);
	$r=mysqli_query($link, $q);
}

if (mysqli_num_rows($r)) {
	?>	
	<table width="950" border="0" align="center" cellpadding="0" cellspacing="0">		
	<input type="hidden" name="pagetoload" value="residents_list.php">	
	<input type="hidden" name="operation">		
	<tr>
	  <td colspan="22" onMouseOver="this.style.color='#FFFFFF'" onMouseOut="this.style.color='#CCCCCC'" align="right" style="padding-right:0px">		  
		  <table width="100%" border="0" cellpadding="5" cellspacing="0">
		  <tr>
		    <td>
			<div align="left" class="Titol_pagina"><?
			if (!isset($_REQUEST[academic_year]) || $_REQUEST[academic_year]=="current") {
				echo "Current residents";
				$the_academic_year="current";
			} elseif ($_REQUEST[academic_year]=="short") {
				echo "Short Stages";
				$the_academic_year="short";
			} else {
				$yearto=$_REQUEST[academic_year]+1;
				echo "Residents {$_REQUEST[academic_year]} - $yearto";
				$the_academic_year=$_REQUEST[academic_year];
			}
			echo " (".mysqli_num_rows($r).")";
			?>
			</div>			
			</td>
			<td width="10">
		  	<select name="academic_year" onChange="document.myform.submit()">
			<option value="current" <? if ($_REQUEST[academic_year]=="current") { echo "selected"; } ?> >Current residents</option>
			<option value="short"   <? if ($_REQUEST[academic_year]=="short")   { echo "selected"; } ?>>Short stays</option>
		  	<?
		  	$r2=mysqli_query($link, "SELECT SUBSTR(arrival,1,4) AS year FROM bookings GROUP BY year");
		  	while ($arrYears=mysqli_fetch_assoc($r2)) {
				$r3=mysqli_query($link, "SELECT count(*) AS total FROM bookings WHERE arrival>='{$arrYears[year]}-09-01'");
				if (mysqli_result($r3,0,"total")>0) {
					$year1=$arrYears[year];
					$year2=$arrYears[year]+1;
					$academic_year=$year1."-".$year2;
					?><option value="<?=$year1?>" <? if ($_REQUEST[academic_year]==$year1) echo "selected" ?> ><?=$academic_year?></option><?
				}
		  	}
		  	?>		  		  
		  	</select>
		  </td>
		  <!--<td width="10"><a href="admin.php?pagetoload=search.php" title="Search Resident"><img src="imgs/search.png" width="16" height="16" border="0"></a></td>-->
		  <!--<td width="10"><a href="admin.php?pagetoload=residents_expenses.php" title="Revenues"><img src="imgs/coins.png" width="16" height="16" border="0"></a></td>		 
		  <td width="10"><a href="admin.php?pagetoload=groups_list.php" title="Groups"><img src="imgs/group6_16x16.gif" width="16" height="16" border="0"></a></td>	  -->
		  <td width="10"><a href="pdf_residents.php?&sort_by=<?=$_REQUEST[sort_by]?>&academic_year=<?=$the_academic_year?>&type=<?=$type?>" title="PDF" target="_blank"><img src="imgs/doc_pdf.png" width="16" height="16" border="0"></a></td>
		  <td width="10"><a href="pdf_residents_pictures.php?&sort_by=<?=$_REQUEST[sort_by]?>&academic_year=<?=$the_academic_year?>&type=<?=$type?>" title="PDF with photos" target="_blank"><img src="imgs/back.png" width="16" height="16" border="0"></a></td>	  
		  <td width="21" align="left"><a href="javascript:send_mail()" title="Send E-Mail"><img src="imgs/mail2_16x16.gif" width="16" height="16" border="0"></a></td>
		  </tr>
	  </table>	  </td>
	  </tr>
	<tr valign="middle">
	  <td width="2" align="right"><img src="imgs/linea_left.png" alt="" width="6" height="27" align="middle" /></td>
	<td background="imgs/1px.jpg" bgcolor="#A5A5A5" class="titol_taula" align="center" valign="middle"><img src="imgs/person_icon.png" width="12" height="12" align="middle" /></td>
	<td background="imgs/1px.jpg" bgcolor="#A5A5A5" class="titol_taula">&nbsp;</td>
	<td background="imgs/1px.jpg" bgcolor="#A5A5A5" class="titol_taula"><a href="admin.php?pagetoload=residents_list.php&sort_by=name&academic_year=<?=$the_academic_year?>&type=<?=$new_type?>" class="header_link2">Name</a></td>
	<td background="imgs/1px.jpg" bgcolor="#A5A5A5" class="titol_taula">&nbsp;</td>
	<td background="imgs/1px.jpg" bgcolor="#A5A5A5" class="titol_taula"><a href="admin.php?pagetoload=residents_list.php&sort_by=surname&academic_year=<?=$the_academic_year?>&type=<?=$new_type?>" class="header_link2">Surname</a></td>
	<td background="imgs/1px.jpg" bgcolor="#A5A5A5" class="titol_taula">&nbsp;</td>
	<td background="imgs/1px.jpg" bgcolor="#A5A5A5" class="titol_taula"><a href="admin.php?pagetoload=residents_list.php&sort_by=arrival&academic_year=<?=$the_academic_year?>&type=<?=$new_type?>" class="header_link2">Arrival</a></div></td>
	<td background="imgs/1px.jpg" bgcolor="#A5A5A5" class="titol_taula">&nbsp;</td>
	<td background="imgs/1px.jpg" bgcolor="#A5A5A5" class="titol_taula"><a href="admin.php?pagetoload=residents_list.php&sort_by=departure&academic_year=<?=$the_academic_year?>&type=<?=$new_type?>" class="header_link2">Departure</a></div></td>
	<td background="imgs/1px.jpg" bgcolor="#A5A5A5" class="titol_taula">&nbsp;</td>
	<td background="imgs/1px.jpg" bgcolor="#A5A5A5" class="titol_taula"><a href="admin.php?pagetoload=residents_list.php&sort_by=room&academic_year=<?=$the_academic_year?>&type=<?=$new_type?>" class="header_link2">Room</a></div></td>
	<td background="imgs/1px.jpg" bgcolor="#A5A5A5" class="titol_taula">&nbsp;</td>
	<td background="imgs/1px.jpg" bgcolor="#A5A5A5" class="titol_taula">Tele</td>
	<td background="imgs/1px.jpg" bgcolor="#A5A5A5" class="titol_taula">&nbsp;</td>
	<td background="imgs/1px.jpg" bgcolor="#A5A5A5" class="titol_taula">City</td>
	<td background="imgs/1px.jpg" bgcolor="#A5A5A5" class="titol_taula">&nbsp;</td>
	<td background="imgs/1px.jpg" bgcolor="#A5A5A5" class="titol_taula">Country</td>	
	<td background="imgs/1px.jpg" bgcolor="#A5A5A5" class="titol_taula">&nbsp;</td>
	<td background="imgs/1px.jpg" bgcolor="#A5A5A5" class="titol_taula">College</td>
	<td background="imgs/1px.jpg" bgcolor="#A5A5A5" class="titol_taula">&nbsp;</td>
	<td background="imgs/1px.jpg" bgcolor="#A5A5A5" class="titol_taula">Subject</td>	
  	<td width="24" background="imgs/1px.jpg" class="titol_taula" align="center"><input type="checkbox" name="all" onClick="check_uncheck_all()"></td>	
	<td width="5" class="titol_taula"><img src="imgs/linia_right.png" alt="" width="6" height="27" align="middle" /></td>
	</tr>	
	<?
	while ($arrData=mysqli_fetch_assoc($r)) {
		//ver_array("",$arrData);		
		//$r2=mysqli_query($link, "SELECT arrival, departure");
		?>
		<tr class="row1" onMouseOver="this.className='row_selected'" onMouseOut="this.className='row1'">
		  <td class="cell"></td>
		  <td class="cell" height="40" align="center" valign="middle">
		  <? 
		  if ($arrData[picture]!="") { 
		  	echo "<img src='residentsnh/".$arrData[picture]."' width='25' height='28'>";
		  } else { 
		  	echo "<img src='imgs/no_picture.png' width='25' border='0'>"; 
		  } 
		  ?>
		  </td>
		  <td valign="middle" class="cell">&nbsp;</td>
		  <td valign="middle" class="cell" align="left"><a href="admin.php?pagetoload=application_form.php&resident_id=<?=$arrData[resident_id]?>&from=residents_list.php" class="table_link2"><?=$arrData[name]?></a></td>
		  <td valign="middle" class="cell">&nbsp;</td>
		  <td valign="middle" class="cell" align="left"><a href="admin.php?pagetoload=application_form.php&resident_id=<?=$arrData[resident_id]?>&from=residents_list.php" class="table_link2"><?=$arrData[surname]?></a></td>
		  <td valign="middle" class="cell">&nbsp;</td>
		  <td valign="middle" class="cell" align="left"><?=mostrar_fecha(substr($arrData[barrival],0,10))?></td>
		  <td valign="middle" class="cell">&nbsp;</td>
		  <td valign="middle" class="cell" align="left"><?=mostrar_fecha(substr($arrData[bdeparture],0,10))?></td>	
		  <td valign="middle" class="cell">&nbsp;</td>
		  <td valign="middle" class="cell" align="left"><?=$arrData[room]?></td>
		  <td valign="middle" class="cell">&nbsp;</td>
		  <td valign="middle" class="cell" align="left"><?=$arrData[telephone]?></td>
		  <td valign="middle" class="cell">&nbsp;</td>
		  <td valign="middle" class="cell" align="left"><?=$arrData[city]?></td>
		  <td valign="middle" class="cell">&nbsp;</td>
		  <td valign="middle" class="cell" align="left"><?=$arrData[country]?></td>
		  <td valign="middle" class="cell">&nbsp;</td>
		  <td valign="middle" class="cell" align="left"><?=$arrData[college]?></td>
		  <td valign="middle" class="cell">&nbsp;</td>
		  <td valign="middle" class="cell" align="left"><?=$arrData[subject]?></td>
		  <td class="cell" align="center"><input type="checkbox" name="resident<?=$arrData[resident_id]?>" value="<?=$arrData[email]?>"></td>
		  <td class="cell"></td>
		</tr>
		<?
	}
	?>	
	</table>
	<?	
} else {
	?>
	<TABLE border="0" height="100%" align="center" cellpadding="0" cellspacing="0">
	<?
 	if ($_REQUEST[name]!="") {
		?>
		<TR>
	  	<TD valign="middle">	
		<p align="center" class="question">No residents with this name "<?=$_REQUEST[name]?>"</p>
		<table align="center" cellpadding="5" cellspacing="0">
		<tr>
		<td align="center"><div class="button_off" onMouseOver="this.className='button_on'" onMouseOut="this.className='button_off'"><a href="admin.php?pagetoload=search.php" class="button_link">Search again</a></div></td>
		</tr>
		</table>
	  	</td>
		</tr>
		<?
	} elseif ($_REQUEST[academic_year]!="" || !isset($_REQUEST[academic_year])) {
		?>
		<tr>
			<td>
				<table align="center">
				<tr>				
				<td>
				<select name="academic_year" onChange="document.myform.submit()">
				<option value="current" <? if ($_REQUEST[academic_year]=="current") { echo "selected"; } ?> >Current residents</option>
				<option value="short"   <? if ($_REQUEST[academic_year]=="short")   { echo "selected"; } ?>>Short stages</option>
				<?
				$r2=mysqli_query($link, "SELECT SUBSTR(arrival,1,4) AS year FROM bookings GROUP BY year");
				while ($arrYears=mysqli_fetch_assoc($r2)) {
					$r3=mysqli_query($link, "SELECT count(*) AS total FROM bookings WHERE arrival>='{$arrYears[year]}-09-01'");
					if (mysqli_result($r3,0,"total")>0) {
						$year1=$arrYears[year];
						$year2=$arrYears[year]+1;
						$academic_year=$year1."-".$year2;
						?><option value="<?=$year1?>" <? if ($_REQUEST[academic_year]==$year1) echo "selected" ?> ><?=$academic_year?></option><?
					}
				}
				?>		  		  
				</select>
			  	</td>
			  	<!--<td width="10"><a href="admin.php?pagetoload=search.php" title="Search Resident"><img src="imgs/search.png" width="16" height="16" border="0"></a></td>
			  	<td width="10"><a href="admin.php?pagetoload=residents_expenses.php" title="Revenues"><img src="imgs/coins.png" width="16" height="16" border="0"></a></td>-->
			  	<td width="10"><a href="admin.php?pagetoload=groups_list.php" title="Groups"><img src="imgs/group6_16x16.gif" width="16" height="16" border="0"></a></td>	  			  	
			  	</tr>			
				</table>
			</td>
		</tr>
		<TR>
		  <TD valign="middle">	
			<p align="center" class="question">There are no people in 
			<?
			if (!isset($_REQUEST[academic_year]) || $_REQUEST[academic_year]=="current") { echo "Current residents"; }
			elseif ($_REQUEST[academic_year]=="short") { echo "Short stages"; }
			?>
			</p>			
		  </td>
		</tr>
		<?
	}
	?>
	</table>
	<?
}
?>
</form>