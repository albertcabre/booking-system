<?php
require_once('connection.php');
require_once('functions.php');
validate_user();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Netherhall House</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

<!-- TinyMCE -->
<script type="text/javascript" src="tiny_mce_v2/tinymce.min.js"></script>

<!-- /TinyMCE -->
<script type="text/javascript">
tinymce.init({
  selector: "textarea",
  height: 500,
  plugins: [
    "advlist autolink autosave link image lists charmap print preview hr anchor pagebreak spellchecker",
    "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
    "table contextmenu directionality emoticons template textcolor paste fullpage textcolor colorpicker textpattern"
  ],

  toolbar1: "newdocument fullpage | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | styleselect formatselect fontselect fontsizeselect",
  toolbar2: "cut copy paste | searchreplace | bullist numlist | outdent indent blockquote | undo redo | link unlink anchor image media code | insertdatetime preview | forecolor backcolor",
  toolbar3: "table | hr removeformat | subscript superscript | charmap emoticons | print fullscreen | ltr rtl | spellchecker | visualchars visualblocks nonbreaking template pagebreak restoredraft",

  menubar: false,
  toolbar_items_size: 'small',

  style_formats: [{
    title: 'Bold text',
    inline: 'b'
  }, {
    title: 'Red text',
    inline: 'span',
    styles: {
      color: '#ff0000'
    }
  }, {
    title: 'Red header',
    block: 'h1',
    styles: {
      color: '#ff0000'
    }
  }, {
    title: 'Example 1',
    inline: 'span',
    classes: 'example1'
  }, {
    title: 'Example 2',
    inline: 'span',
    classes: 'example2'
  }, {
    title: 'Table styles'
  }, {
    title: 'Table row 1',
    selector: 'tr',
    classes: 'tablerow1'
  }],

  templates: [{
    title: 'Test template 1',
    content: 'Test 1'
  }, {
    title: 'Test template 2',
    content: 'Test 2'
  }],
  content_css: [
    '//fast.fonts.net/cssapi/e6dc9b99-64fe-4292-ad98-6974f93cd2a2.css',
    '//www.tinymce.com/css/codepen.min.css'
  ]
});
</script>

<!-- /TinyMCE -->
<script>
function send_message() 
{
	if (document.myform.subject.value==="") 
	{
		alert("Please write a subject");
		document.myform.subject.focus();
	} 
	else if (document.myform.mail_content.value==="") 
	{
		alert("Please write a message");
		document.myform.mail_content.focus();
	}
	else 
	{
		document.myform.submit();
	}
}
</script>

<link href="css/netherhall.css" rel="stylesheet" type="text/css"></link>

</head>

<body>
<?php
if (isset($request[mail_content]))
{
    $headers  = "MIME-Version: 1.0"."\r\n";
    $headers .= "Content-Type: text/html; charset=iso-8859-1"."\r\n";
    $headers .= "From: Netherhall House <netherhall@web528.webfaction.com>\r\n";
    $headers .= "Cc: ".$request[cc]."\r\n";
    $headers .= "Reply-To: ".$request[replyto]."\r\n";
	
	// tags
	$date_stamp = date("d/m/Y");
	$bank_details =
	"<h3>Bank details</h3>".
	"<b>Bank</b>: HSBC, 122 Finchley Road, Hampstead, London, NW3 5JD<br>".
	"<b>Account name</b>: Netherhall House<br>".
	"<b>Sort Code</b>: 40-03-02<br>".
	"<b>Account number</b>: 01125613<br>".
	"<b>IBAN</b>: GB40MIDL40030201125613<br>".
	"<b>Branch Identifier Code</b>: MIDLGB2106H<br>".
	"<b>SWIFT Code</b>: MIDLGB22<br>".
	"<br>".
	"In order to make an international payment to Netherhall, we recommend <a href=\"https://transferwise.com/\">Transferwise</a>.<br>".
	"With the following link you may benefit from a first free transfer:<br>".
	"<a href=\"https://transferwise.com/u/fde5\">Free Transferwise payment to Netherhall</a><br>";
	
	$mails_sent=0;
	
	foreach($request as $key => $value) 
	{
		if (substr($key,0,8)=="resident") 
		{
			if (valid_email($value)) 
			{
				$id=substr($key,8);
				$r2=mysqli_query("SELECT * FROM residents WHERE resident_id=$id");
				
				// tags
				$first_name = mysqli_result($r2, 0, "name");	
				$last_name = mysqli_result($r2, 0, "surname");	
				$email = mysqli_result($r2,0,"email");	

				$message = $request[mail_content];
				$message = str_replace("@FIRST_NAME", $first_name, $message);
				$message = str_replace("@LAST_NAME", $last_name, $message);
				$message = str_replace("@EMAIL", $email, $message);
				$message = str_replace("@DATE", $date_stamp, $message);
				$message = str_replace("@BANK_DETAILS", $bank_details, $message);
				$message .= "<br>";
				$message = stripslashes($message);
				
				$result=mail($value, $request[subject], $message, $headers);
				$mails_sent++;
			}
			else 
			{
				echo "Error, $value is an invalid email format!<br>";
			}
		}
	}
	?>
	<br>
	<p align="center" class="question">
	<?php
	if ($mails_sent) 
	{
		echo "The message has been sent";
	} 
	else 
	{
		echo "No message has been sent";
	}
	?>
	</p>
	<br>
  	<a href="javascript:window.close()" class="table_link2">Close</a><br>
  	<?php
} 
else 
{
	$initial_msg = "Dear @FIRST_NAME,<br>";
	$tags = "@FIRST NAME, @LAST_NAME, @EMAIL, @DATE, @BANK_DETAILS";
	$to = "";
	
	foreach($request as $key => $value) 
	{
		if (substr($key,0,8)=="resident") 
		{
			echo "<input type='hidden' name='resident$id' value='$value'>";

			$id=substr($key,8);
			$r2=mysqli_query("SELECT * FROM residents WHERE resident_id=$id");
			
			$first_name = mysqli_result($r2, 0, "name");	
			$last_name = mysqli_result($r2, 0, "surname");	
			$email = mysqli_result($r2,0,"email");	
			$to_append = $first_name." ".$last_name." &lt;".$email."&gt;";
			
			if (valid_email($email)) 
			{
				if ($to=="") 
				{
					$to = $to_append;
				} 
				else 
				{
					$to .= "<br>".$to_append;
				}
			}
			else
			{
				$to.="&nbsp;<span style='font:Arial; font-size:11px; font-weight:bold; color:#FF0000;'>".$to_append."</span>";
			}
		}
	}
	?>
	<br>
	<table width="850px" align="center" border="0">
	<form name="myform" action="mail.php" method="post" enctype='multipart/form-data'>
	<tr>
	<td class="text_form" valign="top" align="left">To</td>
	<td align="left"><?=$to?></td>
	</tr>
	<tr>
	<td class="text_form" valign="top" align="left">Cc</td>
	<td align="left">
	<select name="cc">
	<option></option>
	<option>director@nh.netherhall.org.uk</option>
	<option>secretary@nh.netherhall.org.uk</option>
	<option>bursar@nh.netherhall.org.uk</option>
	<option>alumni@nh.netherhall.org.uk</option>
	</select>
	</td>
	</tr>
	<tr>
	<td class="text_form" valign="top" align="left">Reply to</td>
	<td align="left">
	<select name="replyto">
	<option>director@nh.netherhall.org.uk</option>
	<option>secretary@nh.netherhall.org.uk</option>
	<option>bursar@nh.netherhall.org.uk</option>
	<option>alumni@nh.netherhall.org.uk</option>
	</select>
	</td>
	</tr>
	<tr>
	<td width="60px" class="text_form" align="left">Subject</td>
	<td align="left"><input type="text" name="subject" size="50" maxlength="100" value=""></td>
	</tr>
	<tr>
	<td class="text_form" valign="top" align="left">Tags</td>
	<td align="left"><?=$tags?></td>
	</tr>
    <tr>
	<td valign="top" class="text_form" align="left">Message</td>
	<td>
	<?php
	foreach($request as $key => $value) 
	{
		if (substr($key,0,8)=="resident") 
		{
			$id=substr($key,8);
			echo "<input type='hidden' name='resident$id' value='$value'>";
		}
	}
	?>
	<textarea id="elm1" name="mail_content" rows="30" cols="80" style="width:100%"><?=$initial_msg?></textarea>
	</td>
	</tr>
	<tr><td colspan="2" align="center"><br><input type="button" value="Submit" onclick="send_message()"></td></tr>
	</form>
	</table>
	<?php
}
?>
</body>
</html>