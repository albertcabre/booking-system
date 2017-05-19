function check_mail(texto){

    var mailres = true;            
    var cadena = "abcdefghijklmnñopqrstuvwxyzABCDEFGHIJKLMNÑOPQRSTUVWXYZ1234567890@._-";
    
    var arroba = texto.indexOf("@",0);
    if ((texto.lastIndexOf("@")) != arroba) arroba = -1;
    
    var punto = texto.lastIndexOf(".");
                
     for (var contador = 0 ; contador < texto.length ; contador++){
        if (cadena.indexOf(texto.substr(contador, 1),0) == -1){
            mailres = false;
            break;
     }
    }

    if ((arroba > 1) && (arroba + 1 < punto) && (punto + 1 < (texto.length)) && (mailres == true) && (texto.indexOf("..",0) == -1))
     mailres = true;
    else
     mailres = false;
                
    return mailres;
}

//----------------------------------------------

function esDigito(sChr){
	var sCod = sChr.charCodeAt(0);
	return ((sCod > 47) && (sCod < 58));
}

function valSep(oTxt){
	var bOk = false;
	bOk = bOk || ((oTxt.value.charAt(2) == "-") && (oTxt.value.charAt(5) == "-"));
	bOk = bOk || ((oTxt.value.charAt(2) == "/") && (oTxt.value.charAt(5) == "/"));
	return bOk;
}

function finMes(oTxt){
	var nMes = parseInt(oTxt.value.substr(3, 2), 10);
	var nRes = 0;
	switch (nMes){
	case 1: nRes = 31; break;
	case 2: nRes = 29; break;
	case 3: nRes = 31; break;
	case 4: nRes = 30; break;
	case 5: nRes = 31; break;
	case 6: nRes = 30; break;
	case 7: nRes = 31; break;
	case 8: nRes = 31; break;
	case 9: nRes = 30; break;
	case 10: nRes = 31; break;
	case 11: nRes = 30; break;
	case 12: nRes = 31; break;
	}
	return nRes;
}

function valDia(oTxt){
	var bOk = false;
	var nDia = parseInt(oTxt.value.substr(0, 2), 10);
	bOk = bOk || ((nDia >= 1) && (nDia <= finMes(oTxt)));
	return bOk;
}

function valMes(oTxt){
	var bOk = false;
	var nMes = parseInt(oTxt.value.substr(3, 2), 10);
	bOk = bOk || ((nMes >= 1) && (nMes <= 12));
	return bOk;
}

function valAno(oTxt){
	var bOk = true;
	var nAno = oTxt.value.substr(6);
	bOk = bOk && ((nAno.length == 2) || (nAno.length == 4));
	if (bOk){
		for (var i = 0; i < nAno.length; i++){
		bOk = bOk && esDigito(nAno.charAt(i));
		}
	}
	return bOk;
}

function valFecha(oTxt, missatge){
	var bOk = true;
	if (oTxt.value != ""){
		bOk = bOk && (valAno(oTxt));
		bOk = bOk && (valMes(oTxt));
		bOk = bOk && (valDia(oTxt));
		bOk = bOk && (valSep(oTxt));
		if (!bOk){
			alert(missatge);
			oTxt.value = "";
			oTxt.focus();
		}
	}
	return bOk;
}

function IsInt( numstr, allowNegatives ) {
	// Return immediately if an invalid value was passed in
	if (numstr+"" == "undefined" || numstr+"" == "null" || numstr+"" == "")
	return false;
	// Default allowNegatives to true when undefined or null
	if (allowNegatives+"" == "undefined" || allowNegatives+"" == "null")
	allowNegatives = true;
	var isValid = true;
	// convert to a string for performing string comparisons.
	numstr += "";
	// Loop through string and test each character. If any
	// character is not a number, return a false result.
	// Include special case for negative numbers (first char == '-').
	for (i = 0; i < numstr.length; i++) {
		if (!((numstr.charAt(i) >= "0") && (numstr.charAt(i) <= "9") || (numstr.charAt(i) == "-"))) {
			isValid = false;
			break;
		} else if ((numstr.charAt(i) == "-" && i != 0) ||
			(numstr.charAt(i) == "-" && !allowNegatives)) {
			isValid = false;
			break;
		}
	
	} // END for
	
	return isValid;
}

//inici---
//aquesta funció només deixa posar numeros i punts (46) per separar decimals.
var nav4 = window.Event ? true : false;
function acceptNum(evt){ 
// NOTE: Backspace = 8, Enter = 13, '0' = 48, '9' = 57 
var key = nav4 ? evt.which : evt.keyCode; 
//alert(key)
return (key <= 13 || (key >= 48 && key <= 57) || key==46);
}
//final---