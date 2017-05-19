<?
//ver_array("REQUEST",$_REQUEST);
$_SESSION[from_nth]=$_REQUEST[from];

$resident_id=$_REQUEST[resident_id];

if ($_REQUEST[operation]=="delete_picture") {	
	echo $resident_id;
	$path = "residentsnh/".$arrData[picture];
} elseif ($_REQUEST[operation]=="save_deposit") {	
	if (!is_numeric($_REQUEST[deposit])) { 
		$error=1; 
		$error_de="class=input_error"; 
	} else {
		$q="UPDATE residents SET deposit = '{$_REQUEST[deposit]}' WHERE resident_id = $resident_id";
		//ver("q",$q);
		mysqli_query($link, $q);
	}
} elseif ($_REQUEST[operation]=="delete_resident") {	
	mysqli_query($link, "DELETE FROM residents WHERE resident_id={$_REQUEST[resident_id]}");
	mysqli_query($link, "DELETE FROM bookings WHERE resident_id={$_REQUEST[resident_id]}");
	@unlink("residentsnh/".$_REQUEST[picture]);
	?><script>document.location='admin.php?pagetoload=residents_list.php'</script><?
} elseif ($_REQUEST[operation]=="change_status") {
	$q="UPDATE bookings SET status='{$_REQUEST[status]}' WHERE booking_id={$_REQUEST[booking_id]}";
	//ver("q",$q);
	mysqli_query($link, $q);
} elseif ($_REQUEST[operation]=="save") {
	$date_of_birth=$_REQUEST[year]."-".$_REQUEST[month]."-".$_REQUEST[day];
	$arriv = change_format_date($_REQUEST[arrival]);
	$depar = change_format_date($_REQUEST[departure]);
	if ($resident_id) {		
		$q="UPDATE residents SET 
		name = '{$_REQUEST[name]}', 
		surname = '{$_REQUEST[surname]}', 
		address_line1 = '{$_REQUEST[address_line1]}', 
		address_line2 = '{$_REQUEST[address_line2]}', 
		postal_code = '{$_REQUEST[postal_code]}', 
		city = '{$_REQUEST[city]}', 
		county = '{$_REQUEST[county]}', 
		country_id = '{$_REQUEST[country_id]}', 
		nationality = '{$_REQUEST[nationality]}', 
		r = '{$_REQUEST[r]}', 
		telephone = '{$_REQUEST[telephone]}', 
		mobile = '{$_REQUEST[mobile]}', 
		email = '{$_REQUEST[email]}', 
		date_of_birth = '$date_of_birth', 
		marital_status = '{$_REQUEST[marital_status]}', 
		smoker = '{$_REQUEST[smoker]}', 	
		college = '{$_REQUEST[college]}', 
		subject = '{$_REQUEST[subject]}', 
		course = '{$_REQUEST[mycourse]}',  
		academic_year = '{$_REQUEST[academic_year]}',
		arrival = '$arriv',
		departure = '$depar',
		deposit = '{$_REQUEST[deposit]}',
		color = '{$_REQUEST[color]}' 
		WHERE resident_id = $resident_id";
		//ver("q",$q);
		mysqli_query($link, $q);
	} else {		
		$today=date("Y-m-d H:m:s");
		$q="INSERT INTO residents (name, surname, address_line1, address_line2, postal_code, city, county, country_id, nationality, r, telephone, mobile, email, date_of_birth, marital_status, smoker, college, subject, course, academic_year, arrival, departure, color, application_date) 
		VALUES ('{$_REQUEST[name]}', '{$_REQUEST[surname]}', '{$_REQUEST[address_line1]}', '{$_REQUEST[address_line2]}', '{$_REQUEST[postal_code]}', '{$_REQUEST[city]}', '{$_REQUEST[county]}', '{$_REQUEST[country_id]}', '{$_REQUEST[nationality]}', '{$_REQUEST[r]}', '{$_REQUEST[telephone]}', '{$_REQUEST[mobile]}', '{$_REQUEST[email]}', '$date_of_birth', '{$_REQUEST[marital_status]}', '{$_REQUEST[smoker]}', '{$_REQUEST[college]}', '{$_REQUEST[subject]}', '{$_REQUEST[mycourse]}', '{$_REQUEST[academic_year]}', '$arriv', '$depar', '".random_color()."', '$today')";	
		//ver("q",$q);
		mysqli_query($link, $q);
		$resident_id=mysqli_insert_id();		
	}	
} 

if ($_REQUEST[operation]=="refresh") {	
	// Check valid numbers before update.
	$error=0;
	foreach ($_REQUEST as $key => $value) {
		if (substr($key,0,2) == "ra" && substr($key,2) == $_REQUEST["booking_id"]) {			
			$ra=$value;
			$error_ra="input";
			if (!is_numeric($ra)) { $error=1; $error_ra="class=input_error"; }
		}
		if (substr($key,0,2) == "la" && substr($key,2) == $_REQUEST["booking_id"]) {
		    $la=$value;
			$error_la="input";
			if (!is_numeric($la)) { $error=1; $error_la="class=input_error"; }
		}
		if (substr($key,0,2) == "hc" && substr($key,2) == $_REQUEST["booking_id"]) {
			$hc=$value;
			$error_hc="input";
			if (!is_numeric($hc)) { $error=1; $error_hc="class=input_error"; }
		}
		if (substr($key,0,2) == "pr" && substr($key,2) == $_REQUEST["booking_id"]) {
			$pr=$value;
			$error_pr="input";
			if (!is_numeric($pr)) { $error=1; $error_pr="class=input_error"; }
		}			
		if (substr($key,0,2) == "ex" && substr($key,2) == $_REQUEST["booking_id"]) {
			$ex=$value;
			$error_ex="input";
			if (!is_numeric($ex)) { $error=1; $error_ex="class=input_error"; }
		}					
		if (substr($key,0,2) == "re" && substr($key,2) == $_REQUEST["booking_id"]) {
			$re=$value;
			$error_re="input";
			if (!is_numeric($re)) { $error=1; $error_re="class=input_error"; }
		}
		if (substr($key,0,2) == "in" && substr($key,2) == $_REQUEST["booking_id"]) {
			$in=$value;
		}
		if (substr($key,0,2) == "ad" && substr($key,2) == $_REQUEST["booking_id"]) {
			$ad = change_format_date($value);
		}
	}
	if (!$error) {
	 	$q="UPDATE bookings SET weekly_rate=$ra, laundry=$la, hc=$hc, printing=$pr, extra=$ex, received=$re, invoice_number='$in', departure='$ad' WHERE booking_id={$_REQUEST[booking_id]}";
		//ver("q",$q);
		$r=mysqli_query($link, $q);
	}		
}

if ($_REQUEST[operation]=="delete") {	
 	$q="DELETE FROM bookings WHERE booking_id={$_REQUEST[booking_id]}";
	$r=mysqli_query($link, $q);
	
	// Now we check if this resident doesn't have any other booking. If it is true then we move him to received applications.
	$q="SELECT * FROM bookings WHERE resident_id={$_REQUEST[resident_id]}";
	$r=mysqli_query($link, $q);
	if (mysqli_num_rows($r)==0) {		
		$q="UPDATE residents SET status=NULL WHERE resident_id={$_REQUEST[resident_id]}";
		$r=mysqli_query($link, $q);
	}
}

//ver("q",$q);
?>
<script language="JavaScript" src="js/picker.js"></script>
<script language="JavaScript" src="jsp/taules.jsp"></script>
<script language="JavaScript" src="jsp/taules_accomodation.jsp"></script>

<script type="text/javascript">
function comments(booking_id) {
	window.open('comments.php?booking_id='+booking_id+'','mywindow','width=420,height=240,top=200,left=200');
}

function save() {	
	if (document.miform.name.value!="" && document.miform.surname.value!="") {
		/*
		var alphaExp = /^[a-zA-Z]+$/;
		if (!document.miform.name.value.match(alphaExp)) {
			alert("Sorry, only letters (a-z) are allowed.");
			document.miform.name.focus();		
		} else if (!document.miform.surname.value.match(alphaExp)) {
			alert("Sorry, only letters (a-z) are allowed.");
			document.miform.surname.focus();
		} else {			
		*/
			document.miform.operation.value="save";
			document.miform.submit();
		//}
	} else {		
		alert("Please indicate a name and a surname");
	}	
}

function update(booking_id) {	
	document.miform.operation.value="refresh";
	document.miform.booking_id.value=booking_id;
	document.miform.submit();
}

function save_deposit(resident_id) {
	document.miform.operation.value="save_deposit";
	document.miform.resident_id.value=resident_id;
	document.miform.submit();
}

function delete_booking(booking_id, from, to) {	
	confirmation=confirm("Do you want to delete this booking?\nFrom " + from + " To " + to);	
	if (confirmation) {
		document.miform.booking_id.value=booking_id;
		document.miform.operation.value="delete";
		document.miform.submit();
	}
}

function delete_picture(resident_id) {	
	confirmation=confirm("Do you want to delete this picture?");	
	if (confirmation) {
		document.miform.resident_id.value=resident_id;
		document.miform.operation.value="delete_picture";
		document.miform.submit();
	}
}

function delete_resident(name) {	
	confirmation=confirm("Do you want to delete the information of " + name + " and all his accounts?");	
	if (confirmation) {	
		confirmation=confirm("You are going to delete the information of " + name + " and all his accounts");	
		if (confirmation) {	
			document.miform.operation.value="delete_resident";
			document.miform.submit();
		}
	}
}

function change_status(status, booking_id) {
	document.miform.operation.value="change_status";
	document.miform.status.value=status;
	document.miform.booking_id.value=booking_id;
	document.miform.submit();
}

function pdf(resident_id) {
	window.open('pdf_application_form.php?resident_id='+resident_id,'mywindow');
}

function pdf_outstanding(resident_id) {
	window.open('pdf_outstanding.php?resident_id='+resident_id,'mywindow');
}

function fees() {
	mywindow = window.open('rooms_type_list2.php','mywindow','width=400,height=400,top=25,left=25');	
}

function terms() {
	mywindow = window.open('terms_list2.php','mywindow','width=900,height=500,top=25,left=25');	
}

function send_mail(resident_id,email) {	
	window.open('mail.php?resident'+resident_id+'='+email,'mywindow','width=650,height=400,top=50,left=50,scrollbars=1,resizable=0');		
}

function send_bill(resident_id,email) {	
	window.open('mail2.php?resident'+resident_id+'='+email,'mywindow','width=900,height=700,top=50,left=50,scrollbars=1,resizable=0');		
}

function calculate(name){			
	expression=eval("document.miform."+name+".value");
	value=eval(expression);
	eval("document.miform."+name+".value="+value);
}
</script>

<LINK href="css/netherhall.css" rel="stylesheet" type="text/css">
<br>
<?
if ($resident_id) {
	$r=mysqli_query($link, "SELECT * FROM residents WHERE resident_id=$resident_id");
	$arrData=mysqli_fetch_assoc($r);
	//ver_array("",$arrData);
}
?>
<table width="930" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr>
    <td colspan="4" align="center"><table width="99%" border="0" cellspacing="0" cellpadding="0">
	<tr>
	  <td colspan="2" align="center"><?
			if ($error) {
				?><span class="error_message">There is an error in the accounts section. Please check below.</span><?
			}
			?></td>
	</tr>
    <tr>
        <td width="15%"><div align="left">
        <?
        echo $arrData[picture];
		if ($arrData[picture]!="" && is_file("residentsnh/".$arrData[picture])) {
			?><img src="residentsnh/<?=$arrData[picture]?>" height="120" border="1" style="border-color:#2F2F5E" /><?
		} else {
			?><img src="imgs/no_picture.png" height="120" border="1" style="border-color:#2F2F5E" /><?
		} 
		?>
        </div></td>
        <td width="85%" valign="bottom">
		<div align="left">
			<span class="questionCopia2">
          	<?=$arrData[name]?>
          	<?=$arrData[surname]?>
        	</span>			
		</div>
		</td>
      </tr>
      <tr>
        <td>
        	<div align="left">
        	<a href="admin.php?pagetoload=application_form_image.php&resident_id=<?=$resident_id?>&from=<?=$_REQUEST[from]?>" class="table_link2_small">Upload picture</a><br>
        	<a href="javascript:delete_picture('<?=$resident_id?>')" class="table_link2_small">Delete picture</a>
        	</div>
        </td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="2"><div class="additional_active">Basic information</div><div class="additional_in" onmouseover="this.className='additional_out'" onmouseout="this.className='additional_in'" onclick="window.open('admin.php?pagetoload=application_form2.php&resident_id=<?=$resident_id?>','_self');">Additional information</div></td>
      </tr>
    </table>
	</td>
  </tr>
  <tr>
    <td colspan="6" align="center"><table width="99%" border="0" align="center" cellpadding="0" cellspacing="0" class="borde_blau">
      <form method="post" name="miform" id="miform" onsubmit="v=document.miform.name.value.length>0;if(!v)alert('Remember to write the name!');return v">
        <input type="hidden" name="pagetoload" value="application_form-test.php" />
        <input type="hidden" name="operation" />
        <input type="hidden" name="status" />
        <input type="hidden" name="booking_id" />
        <input type="hidden" name="resident_id" value="<?=$resident_id?>" />
        <input type="hidden" name="picture" value="<?=$arrData[picture]?>">
        <tr>
          <td valign="top" align="center">&nbsp;</td>
        </tr>
        <tr>
          <td colspan="5" align="center"><table width="890" align="center" cellpadding="2" cellspacing="3" class="borde_gris">
              <tr>
                <td colspan="6" bgcolor="#999999" class="text_form"><span class="titol_taula_mogut">personal information</span></td>
              </tr>
              <tr>
                <td colspan="2" class="text_form" align="left">
                  <input name="Edit" type="button" class="boton_edit_out" id="Edit" onclick="enableField()" value="Edit information"  onmouseover="this.className='boton_edit'" onmouseout="this.className='boton_edit_out'" />
                  <input name="Save" class="boton_save_disabled" type="button" id="Save" onclick="save()" value="Save" disabled="disabled" onmouseover="this.className='boton_save'" onmouseout="this.className='boton_save_out'"/>                </td>
                <td class="text_form">&nbsp;</td>
                <td class="text_form">&nbsp;</td>
                <td class="text_form">&nbsp;</td>
                <td class="text_form">&nbsp;</td>
              </tr>
              <tr>
                <td class="text_form" align="left">Name</td>
                <td class="text_form" align="left">
                  <input type="text" name="name" size="29" value="<?=$arrData[name]?>" disabled="disabled" />               </td>
                <td class="text_form" align="left">Surname</td>
                <td class="text_form" align="left">
                  <input  name="surname" type="text" disabled="disabled" class="normal_text" value='<?=addslashes($arrData[surname])?>' size="29" />                </td>
                <td class="text_form" align="left">Date of birth</td>
                <td class="text_form" align="left">
                  	<?
					$d_birth=substr($arrData[date_of_birth],8,2);
					$m_birth=substr($arrData[date_of_birth],5,2);
					$y_birth=substr($arrData[date_of_birth],0,4);	
					?>
                  	<select name="day" class="normal_text" disabled="disabled">
                    <option></option>
                    <?
					for ($i=1; $i<=31; $i++) {
						$dia=$i;
						if ($i<10) $dia="0".$i;
						?><option <? if ($dia==$d_birth) echo "selected"; ?>><?=$dia?></option><?
					}?>
                  	</select>
                  	<select name="month" class="normal_text" disabled="disabled">
                    <option></option>
                    <?
					for ($i=1; $i<=12; $i++) {
						$mes=$i;
						if ($i<10) $mes="0".$i;
						?><option <? if ($mes==$m_birth) echo "selected"; ?>><?=$mes?></option><?
					}?>
                  	</select>
                  	<select name="year" class="normal_text" disabled="disabled">
                    <option></option>
                    <?
					$year=date("Y");
					for ($i=1940; $i<=$year; $i++) {
						?><option <? if ($i==$y_birth) echo "selected"; ?>><?=$i?></option><?
					}?>
                  	</select>
				</td>
              </tr>
              <tr>
                <td class="text_form" align="left">Address line 1</td>
                <td class="text_form" align="left"><input  name="address_line1" type="text" disabled="disabled" class="normal_text" value="<?=$arrData[address_line1]?>" size="29"/></td>
                <td class="text_form" align="left">Address line 2</td>
                <td class="text_form" align="left"><input  name="address_line2" type="text" disabled="disabled" class="normal_text" value="<?=$arrData[address_line2]?>" size="29"/></td>
                <td class="text_form" align="left">Postal code</td>
                <td class="text_form" align="left"><input  name="postal_code" type="text" disabled="disabled" class="normal_text" value="<?=$arrData[postal_code]?>" size="27"/>                </td>
              </tr>
              <tr>
                <td class="text_form" align="left">Town / city</td>
                <td class="text_form" align="left"><input name="city" type="text" disabled="disabled" class="normal_text" value="<?=$arrData[city]?>" size="29"/>                </td>
                <td class="text_form" align="left">County / province</td>
                <td class="text_form" align="left"><input  name="county" type="text" disabled="disabled" class="normal_text" value="<?=$arrData[county]?>" size="29" />                </td>
                <td class="text_form" align="left">Country</td>
                <td class="text_form" align="left">
                  <select name="country_id" disabled="disabled" class="normal_text">
                    <option></option>
                    <?
					$r=mysqli_query($link, "SELECT * FROM countries ORDER BY country");
					while ($data=mysqli_fetch_assoc($r)) {
						?>
                    	<option value="<?=$data[country_id]?>" <? if ($data[country_id]==$arrData[country_id]) { echo "selected"; } ?>><?=$data[country]?></option>
                    	<?
					}
					?>
                  	</select>                
					</td>
              </tr>
              <tr>
                <td class="text_form" align="left">Nationality</td>
                <td class="text_form" align="left"><input  name="nationality" type="text" disabled="disabled" class="normal_text" value="<?=$arrData[nationality]?>" size="29"/></td>
                <td class="text_form" align="left">Telephone 1</td>
                <td class="text_form" align="left"><input  name="telephone" type="text" disabled="disabled" class="normal_text" value="<?=$arrData[telephone]?>" size="29"/></td>
                <td class="text_form" align="left">Telephone 2</td>
                <td class="text_form" align="left"><input  name="mobile" type="text" disabled="disabled" class="normal_text" value="<?=$arrData[mobile]?>" size="27"/></td>
              </tr>
              <tr>
                <td class="text_form" align="left">E-mail </td>
                <td class="text_form" align="left"><input  name="email" type="text" disabled="disabled" class="normal_text" value="<?=$arrData[email]?>" size="29"/></td>
                <td class="text_form" align="left">Religion</td>
                <td class="text_form" align="left"><input  name="r" type="text" disabled="disabled" class="normal_text" value="<?=$arrData[r]?>" size="29"/></td>
                <td class="text_form" align="left">Marital status</td>
                <td class="text_form" align="left">
                  <select name="marital_status" class="normal_text" disabled="disabled">
                    <option></option>
                    <option <? if ($arrData[marital_status]=="Single") { echo "selected"; } ?>>Single</option>
                    <option <? if ($arrData[marital_status]=="Married") { echo "selected"; } ?>>Married</option>
                    <option <? if ($arrData[marital_status]=="Divorced") { echo "selected"; } ?>>Divorced</option>
                  </select></td>
              </tr>
              <tr>
                <td class="text_form" align="left">College</td>
                <td class="text_form" align="left"><input  name="college" type="text" disabled="disabled" class="normal_text" value="<?=$arrData[college]?>" size="29" /></td>
                <td class="text_form" align="left">Subject</td>
                <td class="text_form" align="left"><input  name="subject" type="text" disabled="disabled" class="normal_text" value="<?=$arrData[subject]?>" size="29"/></td>
                <td class="text_form" align="left">Color</td>
                <td valign="middle" class="text_form" align="left"><input name="color" type="text" class="normal_text" id="color" value="<?=$arrData[color]?>" size="6" disabled="disabled" />&nbsp;<input name="mostra_color" type="text" disabled="disabled" class="mostra_color" id="mostra_color" style="background-color:<?=$arrData[color]?>" value="" size="1" />&nbsp;<input name="paleta" value="" type="button" onclick="pickerPopup302('color','mostra_color');" class="oculta" disabled="disabled"/></td>
              </tr>
              <tr>
                <td class="text_form" align="left">Course <span class="small"><br>eg BA, MSc, PhD, etc</span> </td>
                <td class="text_form" align="left"><input name="mycourse" type="text" value="<?=$arrData[course]?>" disabled="disabled" size="29" /></td>
                <td class="text_form" align="left">Academic year <br>
                  <span class="small">eg 1, 2, 3</span> </td>
                <td class="text_form" align="left"><input name="academic_year" type="text" value="<?=$arrData[academic_year]?>" disabled="disabled" size="29" /></td>
                <td class="text_form" align="left">&nbsp;</td>
                <td class="text_form" align="left">&nbsp;</td>
              </tr>
              <tr>
                <td class="text_form" align="left">Arrival </td>
                <td class="text_form" align="left"><input name="arrival" type="text" value="<?=mostrar_fecha($arrData[arrival])?>" size="29" disabled="disabled" /></td>
                <td class="text_form" align="left">Departure </td>
                <td class="text_form" align="left"><input name="departure" type="text" value="<?=mostrar_fecha($arrData[departure])?>" size="29" disabled="disabled" /></td>
                <td class="text_form" align="left">&nbsp;</td>
                <td class="text_form" align="left">&nbsp;</td>
              </tr>
          </table></td>
        </tr>
        <tr>
          <td colspan="5" align="center">&nbsp;</td>
        </tr>
		<?
		if ($resident_id) {
		?>
        <tr>
          <td colspan="5" align="center">
              <table width="890" border="0" cellpadding="2" cellspacing="3" class="borde_gris">
                <tr>
                  <td colspan="18" bgcolor="#999999" class="text_form"><span class="titol_taula_mogut">accounts</span><a name="aaa"></a></td>
                </tr>
                <tr>
                  <td colspan="17" align="left" valign="bottom" class="question">
                  <input name="accomodation" type="button" class="boton_accomodation_out" id="accomodation" onclick="window.open('admin.php?pagetoload=application_form_dates.php&amp;resident_id=<?=$resident_id?>&amp;operation=new','_self');" value="New account"  onmouseover="this.className='boton_accomodation'" onmouseout="this.className='boton_accomodation_out'" />
                	<?
					/*
					if ($resident_id) {
						$r=mysqli_query($link, "SELECT * FROM bookings WHERE resident_id=$resident_id");
						$div_num=1;
						if (mysqli_num_rows($r)) {
						*/
						?>
                  </td>
				  <td align="right">Advance Payment &nbsp; <input type="text" name="deposit" size="5" value="<?=$arrData[deposit]?>" <?=$error_de?>>&nbsp;<a href="javascript:save_deposit(<?=$resident_id?>)" title="Save deposit"><img src="imgs/disk.png" border="0" align="absmiddle"></a></td>
                </tr>
                <?	
				$total_outstanding=0;
			if ($resident_id) {
				$r=mysqli_query($link, "SELECT * FROM bookings WHERE resident_id=$resident_id AND (status='' OR status IS NULL OR status='accepted') ORDER BY arrival DESC");
				$num_of_accounts=mysqli_num_rows($r);
				$accounts=0;
				$total_outstanding=0;
				while ($arrAccomodation=mysqli_fetch_assoc($r)) {						
					$accounts++;	
					//ver_array("arrAccomodation",$arrAccomodation);
					$date_from       = mostrar_fecha($arrAccomodation['arrival']);	
					$date_to_planned = mostrar_fecha($arrAccomodation['planned_departure']);	
					$date_to         = mostrar_fecha($arrAccomodation['departure']);	
					
					$days=subtract_dates($date_from, $date_to_planned);
					
					// Search the name of the room
					if ($arrAccomodation[room_id]) {
						$r2=mysqli_query($link, "SELECT * FROM rooms WHERE room_id={$arrAccomodation[room_id]}");
						$room="";
						if (mysqli_numrows($r2))
							$room=mysqli_result($r2,0,"room");
					}
					
					$total_rent = $days * ($arrAccomodation['weekly_rate']/7);
					$total_rent = round($total_rent,2);
					$due = $total_rent + $arrAccomodation['laundry'] + $arrAccomodation['hc'] + $arrAccomodation['printing'] + $arrAccomodation['extra'];
					$invoice_number = "NO BILL";
					//$outstanding = $due - $arrAccomodation['deposit'] - $arrAccomodation['received'];
					$outstanding = $due - $arrAccomodation['received'];
					$total_outstanding = $total_outstanding + $outstanding;
					if ($arrAccomodation['invoice_number']!="") $invoice_number = $arrAccomodation['invoice_number'];
					?>
                	<tr>
                  	<td colspan="18">
				  	<div id="accom">
                    <table width="100%" border="0" cellspacing="5" cellpadding="0">                       
                        <tr class="table_style">
                          	<td colspan="16" align="left" class="Titol_pagina">
						  	<span class="Titol_pagina_gris">arrival:&nbsp;</span><?=$date_from?>
                            <span class="Titol_pagina_gris">| departure:&nbsp;</span><?=$date_to_planned?>
							<span class="Titol_pagina_gris">| actual departure:&nbsp;</span><input name="ad<?=$arrAccomodation['booking_id']?>" type="text" class="normal_text" value="<?=$date_to?>" size="7" id="rd"  />
                            <span class="Titol_pagina_gris">| room:&nbsp;</span><?=$room?>
							</td>							
                        </tr>                       
                        <tr class="table_style">
                          <td align="center" class="normal_text">Num. days</td>
                          <td class="normal_text">Weekly Rate</td>
                          <td align="right" class="text_form">Total room</td>
                          <td align="left" class="normal_text">Laundry</td>
                          <td align="left" class="normal_text">HC</td>
                          <td align="left" class="normal_text">Printing</td>
                          <td align="left" class="normal_text">Extra</td>
                          <td align="right" class="text_form">Due</td>
                          <td align="left" class="text_form">Received</td>
                          <td align="right" class="text_form">Outstand.&nbsp;</td>
                          <td align="left" class="text_form">Inv. Num.</td>
                          <td colspan="5">&nbsp;</td>
                        </tr>
                        
                        <tr class="table_style">
                          	<td align="center" class="normal_text"><?=$days?></td>
                          	<td><input onChange="calculate(this.name)" name="ra<?=$arrAccomodation['booking_id']?>" type="text" value="<?=$arrAccomodation['weekly_rate']?>" size="3" <? if ($_REQUEST['booking_id']==$arrAccomodation['booking_id']) echo $error_ra; ?>  />
						  	<a href="javascript:fees()" title="Fees"><img src="imgs/pound1.png" width="16" height="16" align="absmiddle" border="0"></a><a href="javascript:terms()" title="Terms"><img src="imgs/data.png" width="16" height="16" align="absmiddle" border="0"></a>
						  	</td>
                          	<td align="right" class="normal_text"><?=number_format($total_rent,2,".",",")?></td>
                          	<td><input onChange="calculate(this.name)" name="la<?=$arrAccomodation['booking_id']?>" type="text" class="normal_text" value="<?=$arrAccomodation['laundry']?>" size="4" <? if ($_REQUEST['booking_id']==$arrAccomodation['booking_id']) echo $error_la; ?> id="laundry"  /></td>
                          	<td><input onChange="calculate(this.name)" name="hc<?=$arrAccomodation['booking_id']?>" type="text" class="normal_text" value="<?=$arrAccomodation['hc']?>" size="4"  <? if ($_REQUEST['booking_id']==$arrAccomodation['booking_id']) echo $error_hc; ?> id="hc" /></td>
                          	<td><input onChange="calculate(this.name)" name="pr<?=$arrAccomodation['booking_id']?>" type="text" class="normal_text" value="<?=$arrAccomodation['printing']?>" size="4" <? if ($_REQUEST['booking_id']==$arrAccomodation['booking_id']) echo $error_pr; ?> id="printing" /></td>
                           	<td><input onChange="calculate(this.name)" name="ex<?=$arrAccomodation['booking_id']?>" type="text" class="normal_text" value="<?=$arrAccomodation['extra']?>" size="4" <? if ($_REQUEST['booking_id']==$arrAccomodation['booking_id']) echo $error_ex; ?> id="extra"  /></td>
                           	<td align="right" class="normal_text"><?=number_format($due,2,".",",")?></td>
                          	<td><input onChange="calculate(this.name)" name="re<?=$arrAccomodation['booking_id']?>" type="text" class="normal_text" value="<?=$arrAccomodation['received']?>" size="5" <? if ($_REQUEST['booking_id']==$arrAccomodation['booking_id']) echo $error_re; ?> id="received" /></td>
                         	<td align="right">
						  	<?
							$color_text="normal_text_verd";
							if ($outstanding>0) $color_text="normal_text_red";
							?>
						  	<span class="<?=$color_text?>"><?=number_format($outstanding,2,".",",")?></span>&nbsp;
							</td>
                          	<td><input name="in<?=$arrAccomodation['booking_id']?>" type="text" class="normal_text" value="<?=$invoice_number?>" size="7" id="n_bill" /></td>
                          	<td align="center"><a href="javascript:update(<?=$arrAccomodation['booking_id']?>)" class="table_link2" title="Update"><img src="imgs/arrow_refresh.png" width="16" height="16" border="0"></a></td>
							<td align="center"><a href="javascript:delete_booking(<?=$arrAccomodation['booking_id']?>,'<?=$date_from?>','<?=$date_to_planned?>')" title="Delete this booking"><img src="imgs/trash_16x16.gif" border="0"></a></td>
							<td align="center"><a href="admin.php?pagetoload=application_form_dates.php&resident_id=<?=$resident_id?>&booking_id=<?=$arrAccomodation['booking_id']?>" title="Change booking dates"><img src="imgs/date_16x16.gif" border="0"></a></td>						
							<td align="center">
							<?
							if ($arrAccomodation[comments]=="") {
								?><a href="javascript:comments(<?=$arrAccomodation['booking_id']?>)" title="No comments"><img src="imgs/notepad_16x16.gif" width="16" height="16" border="0"></a><?
							} else {
								?><a href="javascript:comments(<?=$arrAccomodation['booking_id']?>)" title="There are some comments"><img src="imgs/notepad_(edit)_16x16.gif" border="0"></a><?
							}
							?>
							</td>
							<td align="center">
							<table cellpadding="0" cellspacing="0" align="center">
							<tr>
							<td align="center" width="47">
							<div class="button_off_small" onMouseOver="this.className='button_on_small'" onMouseOut="this.className='button_off_small'"><a href="javascript:change_status('finished',<?=$arrAccomodation['booking_id']?>)" class="button_link_small">Finish</a></div>
							</td>
							</tr>
							</table>
							</td>
                        </tr>						
						<?
						if ($num_of_accounts==$accounts && $num_of_accounts>1) {
							?>							
							<tr class="table_style">
                          	<td colspan="9"></td>
                          	<td align="right" style="border-top-style:solid; border-top-width:1px; border-top-color:#333333">
						  	<?
							$color_text="normal_text_verd";
							if ($total_outstanding>0) $color_text="normal_text_red";
							?>
						  	<span class="<?=$color_text?>"><?=number_format($total_outstanding,2,".",",")?></span>&nbsp;
							</td>
                          	<td colspan="6"></td>
                          	</tr>
							<?
						}
						?>
                   	</table>
                  	</div>
					</td>
                	</tr>
                	<?					
					$div_num++;
					}																
						
				$r=mysqli_query($link, "SELECT * FROM bookings WHERE resident_id=$resident_id AND status='finished' ORDER BY arrival DESC");
				while ($arrAccomodation=mysqli_fetch_assoc($r)) {		
					$date_from       = mostrar_fecha($arrAccomodation['arrival']);
					$date_to_planned = mostrar_fecha($arrAccomodation['departure']);
					$date_to         = mostrar_fecha($arrAccomodation['actual_departure']);
		
					$days=subtract_dates($date_from, $date_to_planned);
		
					// Search the name of the room
					if ($arrAccomodation[room_id]) {
						$r2=mysqli_query($link, "SELECT * FROM rooms WHERE room_id={$arrAccomodation[room_id]}");
						$room = "";
						if (mysqli_numrows($r2))
							$room=mysqli_result($r2,0,"room");				
					}
					
					//ver_array("arrAccomodation",$arrAccomodation);					
					$total_rent = $days * ($arrAccomodation['weekly_rate']/7);
					$total_rent = round($total_rent,2);					
					$due = $total_rent + $arrAccomodation['laundry'] + $arrAccomodation['hc'] + $arrAccomodation['printing'] + $arrAccomodation['extra'];
					$invoice_number = "NO BILL";
					$outstanding = $arrAccomodation['received'] - $due;
					if ($arrAccomodation['invoice_number']!="") $invoice_number = $arrAccomodation['invoice_number'];
					?>
                	<tr>
                  	<td colspan="18"><div id="accom2">
                      	<table width="100%" border="0" cellspacing="5" cellpadding="0">                       
                        <tr class="table_style">
                          <td align="left" colspan="15" class="Titol_pagina">
						  <span class="Titol_pagina_gris">arrival:&nbsp;</span><?=$date_from?>
                          <span class="Titol_pagina_gris">| departure:&nbsp;</span><?=$date_to_planned?>
						  <span class="Titol_pagina_gris">| actual departure:&nbsp;</span><?=$date_to?>
                          <span class="Titol_pagina_gris">| room:&nbsp;</span><?=$room?>
						  </td>
                        </tr>                       
                        <tr class="table_style">
                          <td align="center" class="normal_text">Num. days</td>
                          <td align="right" class="normal_text">Weekly rate</td>
                          <td align="right" class="text_form">Total room</td>
                          <td align="right" class="normal_text">Laundry</td>
                          <td align="right" class="normal_text">HC</td>
                          <td align="right" class="normal_text">Printing</td>
                          <td align="right" class="normal_text">Extra</td>
                          <td align="right" class="text_form">Due</td>
                          <td align="right" class="text_form">Received</td>
                          <td align="right" class="text_form">Outstand.&nbsp;</td>
                          <td align="left" class="text_form">Inv. Num.</td>
                          <td colspan="4">&nbsp;</td>
                        </tr>                        
						<tr class="table_style">
                          	<td align="center"><?=$days?></td>
                          	<td align="right"><?=$arrAccomodation['weekly_rate']?></td>
                          	<td align="right"><?=number_format($total_rent,2,".",",")?></td>
                          	<td align="right"><?=$arrAccomodation['laundry']?></td>
                          	<td align="right"><?=$arrAccomodation['hc']?></td>
                          	<td align="right"><?=$arrAccomodation['printing']?></td>
                          	<td align="right"><?=$arrAccomodation['extra']?></td>
                          	<td align="right"><?=number_format($due,2,".",",")?></td>
                          	<td align="right"><?=$arrAccomodation['received']?></td>
						  	<?
						  	$color_text="normal_text_verd";
							if ($outstanding>0) $color_text="normal_text_red";
							?>
                          	<td align="right"><span class="<?=$color_text?>"><?=number_format($outstanding,2,".",",")?></span>&nbsp;</td>
                          	<td align="center"><?=$invoice_number?></td>
                         	<td height="25" class="text_form_small" align="center">			
							<?
							$icon="ok_16x16.gif";
							if ($outstanding) $icon="attention3_16x16.gif";
							echo "<img src='imgs/$icon' border='0'>";
							?>
							</td>	
							<td align="center"><a href="javascript:delete_booking(<?=$arrAccomodation['booking_id']?>,'<?=$date_from?>','<?=$date_to_planned?>')"><img src="imgs/trash_16x16.gif" border="0"></a></td>
							<td align="center"><?
							if ($arrAccomodation[comments]=="") {
								?><a href="javascript:comments(<?=$arrAccomodation['booking_id']?>)" title="No comments"><img src="imgs/notepad_16x16.gif" width="16" height="16" border="0"></a><?
							} else {
								?><a href="javascript:comments(<?=$arrAccomodation['booking_id']?>)" title="There are some comments"><img src="imgs/notepad_(edit)_16x16.gif" border="0"></a><?
							}
							?>
							</td>
							<td align="center">
							<table cellpadding="0" cellspacing="0">
							<tr>
							<td align="center" width="47"><div class="button_off_small" onMouseOver="this.className='button_on_small'" onMouseOut="this.className='button_off_small'"><a href="javascript:change_status('accepted',<?=$arrAccomodation['booking_id']?>)" class="button_link_small">Back</a></div></td>
							</tr>
							</table>					
							</td>  
                      		</tr>
                      </table>
                  </div></td>
                </tr>
                <?				
				}
			}
			?>              
              </table>
           </td>
        </tr>              
        <tr><td colspan="5" align="center">&nbsp;</td></tr>
		<?
		}
		?>
      </form>
    </table></td>
  </tr>  
  <?
  if ($resident_id) {
  ?>
  <tr>
    <td width="20%" align="left"><input name="back" type="button" class="boton_back_out" id="back" onclick="document.location='admin.php?pagetoload=<?=$_SESSION[from_nth]?>'" value="Back"  onmouseover="this.className='boton_back'" onmouseout="this.className='boton_back_out'"/></td>        
    <td width="20%" align="center"><input name="pdf_resident" type="button" class="boton_pdf_out" id="pdf_resident" onclick="pdf(<?=$resident_id?>)" value="View in pdf"  onmouseover="this.className='boton_pdf'" onmouseout="this.className='boton_pdf_out'"/></td>	
	<td width="20%" align="center"><input name="pdf_outstanding" type="button" class="boton_pdf_out" id="pdf_outstanding" onclick="pdf_outstanding(<?=$resident_id?>)" value="Outstanding"  onmouseover="this.className='boton_pdf'" onmouseout="this.className='boton_pdf_out'"/></td>	
    <td width="20%" align="center"><input name="send_mail" type="button" class="boton_mail_out" id="send_mail" onclick="send_mail(<?=$resident_id?>,'<?=$arrData[email]?>')" value="Send E-Mail"  onmouseover="this.className='boton_mail'" onmouseout="this.className='boton_mail_out'"/></td>
    <td width="20%" align="center"><input name="send_mail" type="button" class="boton_mail_out" id="send_mail" onclick="send_bill(<?=$resident_id?>,'<?=$arrData[email]?>')" value="Send Bill"  onmouseover="this.className='boton_mail'" onmouseout="this.className='boton_mail_out'"/></td>
    <td width="20%" align="right"><input name="delete_resident" type="button" class="boton_delete2_out" id="delete_resident" onclick="javascript:delete_resident('<?=addslashes($arrData[name]." ".$arrData[surname])?>')" value="Delete resident"  onmouseover="this.className='boton_delete2'" onmouseout="this.className='boton_delete2_out'"/></td>
  </tr>
  <?
  }
  ?>
</table>
<?
if ($_REQUEST[operation]=="refresh") {	
	?><script>window.document.location='#aaa'</script><?
}
?>