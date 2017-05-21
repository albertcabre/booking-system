<?php
$request = filter_input_array(INPUT_POST);
if ($request == NULL) {
    $request = filter_input_array(INPUT_GET);
}

function validate_user() {
    if (!$_SESSION['worldresidents_rgstrd']) {
        exit(header("Location: index.php"));
    }
}

function ValidateData($campo) {
    //Array con las posibles cadenas a utilizar por un hacker
    $CadenasProhibidas = array("ALTER DATABASE", "ALTER TABLE", "DELETE DATABASE", "DELETE FROM", "DROP TABLE", "DROP DATABASE", "SELECT", "INSERT", "UPDATE", "CREATE", "TRUNCATE", "RENAME");

    //Comprobamos que entre los datos no se encuentre alguna de
    //las cadenas del array. Si se encuentra alguna cadena se
    //dirige a la página anterior
    foreach ($CadenasProhibidas as $valor) {
        if (strpos(strtolower($campo), strtolower($valor)) !== false) {
            //echo "sql injection by '$campo' !!!!!!!!!!!!!!!!!<br>";
            //header("www.nh.netherhall.org.uk");
            exit;
        }
    }
    //echo "Everything is allright '$campo'<br>";
}

/**
 * VER_ARRAY
 * Li pases un array i et mostra tots els seus valors de manera que es vegui millor
 * que un simple print_f.
 */
function ver_array($nom_array, $array) {
    if (is_array($array)) {
        echo "<b>$nom_array</b><br>";
        if (count($array) == 0) {
            echo "esta vacio!<br>";
        }
        foreach ($array as $key => $value) {
            echo "-><b>$key</b>=";
            print_r($value);
            echo "<br>";
        }
    } else {
        echo "<b>" . $nom_array . " no es un array!!!</b><br>";
    }
}

function ver($nom_variable, $variable) {
    echo "<b>$nom_variable</b>($variable)<br>";
}

/**
 * Retorna un timestamp en formato año/mes/dia hora:minuto:segundo.
 */
function mostrar_timestamp($timestamp) {
    //ver("timestamp",$timestamp);
    return substr($timestamp['fecha_registro'], 6, 2) . "/" . substr($timestamp['fecha_registro'], 4, 2) . "/" . substr($timestamp['fecha_registro'], 0, 4) . substr($timestamp['fecha_registro'], 8, 2) . ":" . substr($timestamp['fecha_registro'], 10, 2) . ":" . substr($timestamp['fecha_registro'], 12, 2);
}

/**
 * Retorna un timestamp en formato dia/mes/año hora:minuto:segundo.
 */
function mostrar_timestamp2($timestamp) {
    //ver("timestamp",$timestamp);
    return substr($timestamp, 8, 2) . "/" . substr($timestamp, 5, 2) . "/" . substr($timestamp, 0, 4) . " " . substr($timestamp, 11, 2) . ":" . substr($timestamp, 14, 2) . ":" . substr($timestamp, 17, 2);
}

function check_file_to_upload($arrArchivo) {
    $error = 0;
    $archivo_name = $arrArchivo[name];
    $archivo_size = $arrArchivo[size];
    $archivo = $arrArchivo[tmp_name];
    //ver("archivo_name",$archivo_name);
    if ($archivo_name) {
        // Si nos pasan un archivo y es correcto lo guardamos.
        $extension = explode(".", $archivo_name);
        $num = count($extension) - 1;
        $extension[$num] = strtolower($extension[$num]);
        //if ($extension[$num] == "pdf" || $extension[$num] == "gif" || $extension[$num] == "jpg" || $extension[$num] == "jpeg" || $extension[$num] == "doc" || $extension[$num] == "png" || $extension[$num] == "bmp" || $extension[$num] == "docx") {
        if ($extension[$num] == "gif" || $extension[$num] == "jpg" || $extension[$num] == "jpeg" || $extension[$num] == "png" || $extension[$num] == "bmp") {
            //ver("archivo_size",$archivo_size);
            // 8388608 = 1MB
            if ($archivo_size > 8388608) {
                return array("error" => 1, "message" => "The maximum file size to upload is 1MB");
            }
        } else {
            return array("error" => 1, "message" => "The file with the picture must be one of these: gif, jpg, jpeg, png or bmp &nbsp;&nbsp;&nbsp;");
        }
    } /* else {
      return array("error"=>1, "message"=>"The file to upload hasn't been found");
      } */
    return array("error" => 0, "message" => "ok");
}

function upload_file($arrArchivo, $donde, $id, $conservar_nombre, $borrar) {
    $error = 0;
    $archivo_name = $arrArchivo[name];
    $archivo_size = $arrArchivo[size];
    $archivo = $arrArchivo[tmp_name];
    //ver("archivo_name",$archivo_name);
    if ($archivo_name) {
        // Si nos pasan un archivo y es correcto lo guardamos.
        $extension = explode(".", $archivo_name);
        $num = count($extension) - 1;
        $extension[$num] = strtolower($extension[$num]);
        //if ($extension[$num] == "pdf" || $extension[$num] == "gif" || $extension[$num] != "jpg" || $extension[$num] != "jpeg" || $extension[$num] != "doc" || $extension[$num] != "png" || $extension[$num] != "bmp" || $extension[$num] != "docx") {
        if ($extension[$num] == "gif" || $extension[$num] != "jpg" || $extension[$num] != "jpeg" || $extension[$num] != "png" || $extension[$num] != "bmp") {
            //ver("archivo_size",$archivo_size);
            // 8388608 = 1MB
            if ($archivo_size <= 8388608) {
                if ($borrar != "") {
                    @unlink($borrar);
                }
                if ($conservar_nombre) {
                    $archivo_destino = $donde . "/" . $archivo_name;
                } else {
                    $archivo_destino = $donde . "/" . $id . "." . $extension[$num];
                }
                //ver("archivo_destino",$archivo_destino);
                if (!copy($archivo, $archivo_destino)) {
                    return array("error" => 1, "message" => "Error saving file");
                } else {
                    return array("error" => 0, "message" => $id . "." . $extension[$num]);
                }
            } else {
                return array("error" => 1, "message" => "Error: maximum file size is 1MB");
            }
        } else {
            return array("error" => 1, "message" => "Error: Only gif, jpg, jpeg, png, or bmp");
        }
    } else {
        return array("error" => 1, "message" => "Error: file not found");
    }
}

function texto_con_br($mensaje) {
    // Le pasas un texto de la base de datos con \r\n y te los convierte en
    // <br> para que en la web se vean los cambios de linea.
    return str_replace("\r\n", "<br>", $mensaje);
}

/**
 * Retorna una fecha en formato año/mes/dia para ser guardada.
 */
function change_format_date($fecha) {
    if ($fecha != "")
        return substr($fecha, 6, 4) . "/" . substr($fecha, 3, 2) . "/" . substr($fecha, 0, 2);
    //return substr($fecha,6,4)."-".substr($fecha,3,2)."-".substr($fecha,0,2);
    else
        return "";
}

function reverse_date($fecha) {
    if ($fecha != "")
        return substr($fecha, 6, 4) . "/" . substr($fecha, 3, 2) . "/" . substr($fecha, 0, 2);
    //return substr($fecha,6,4)."-".substr($fecha,3,2)."-".substr($fecha,0,2);
    else
        return "";
}

/**
 * Retorna una fecha en formato dia/mes/año para ser mostrada.
 */
function mostrar_fecha($fecha) {
    if ($fecha != "" && $fecha != "0000/00/00" && $fecha != "0000-00-00")
        return substr($fecha, 8, 2) . "/" . substr($fecha, 5, 2) . "/" . substr($fecha, 0, 4);
    //return substr($fecha,8,2)."-".substr($fecha,5,2)."-".substr($fecha,0,4);
    else
        return "";
}

function random_color() {
    mt_srand((double) microtime() * 1000000);
    /*
      $color = '';
      while(strlen($color)<6){
      $color .= sprintf("%02X", mt_rand(0, 255));
      }
     */
    $r = mt_rand(0, 200);
    $g = mt_rand(0, 200);
    $b = mt_rand(0, 200);

    return rgb2html($r, $g, $b);
}

function rgb2html($r, $g = -1, $b = -1) {
    if (is_array($r) && sizeof($r) == 3)
        list($r, $g, $b) = $r;

    $r = intval($r);
    $g = intval($g);
    $b = intval($b);
    $r = dechex($r < 0 ? 0 : ($r > 255 ? 255 : $r));
    $g = dechex($g < 0 ? 0 : ($g > 255 ? 255 : $g));
    $b = dechex($b < 0 ? 0 : ($b > 255 ? 255 : $b));
    $color = (strlen($r) < 2 ? '0' : '') . $r;
    $color .= (strlen($g) < 2 ? '0' : '') . $g;
    $color .= (strlen($b) < 2 ? '0' : '') . $b;

    return '#' . $color;
}

function html2rgb($color) {
    if ($color[0] == '#')
        $color = substr($color, 1);

    if (strlen($color) == 6)
        list($r, $g, $b) = array($color[0] . $color[1],
            $color[2] . $color[3],
            $color[4] . $color[5]);
    elseif (strlen($color) == 3)
        list($r, $g, $b) = array($color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2]);
    else
        return false;

    $r = hexdec($r);
    $g = hexdec($g);
    $b = hexdec($b);

    return array($r, $g, $b);
}

function subtract_dates($dFecIni, $dFecFin) {
    $dFecIni = str_replace("-", "", $dFecIni);
    $dFecIni = str_replace("/", "", $dFecIni);
    $dFecFin = str_replace("-", "", $dFecFin);
    $dFecFin = str_replace("/", "", $dFecFin);

    preg_match("/([0-9]{1,2})([0-9]{1,2})([0-9]{2,4})/", $dFecIni, $aFecIni);
    preg_match("/([0-9]{1,2})([0-9]{1,2})([0-9]{2,4})/", $dFecFin, $aFecFin);

    $date1 = mktime(0, 0, 0, $aFecIni[2], $aFecIni[1], $aFecIni[3]);
    $date2 = mktime(0, 0, 0, $aFecFin[2], $aFecFin[1], $aFecFin[3]);

    return round(($date2 - $date1) / (60 * 60 * 24));
}

function button($action, $title) {
    ?>
    <table align="center" cellpadding="5" cellspacing="0">
        <tr>
            <td align="center"><div class="button_off" onMouseOver="this.className = 'button_on'" onMouseOut="this.className = 'button_off'">
                    <a href="<?= $action ?>" class="button_link"><?= $title ?></a></div></td>
        </tr>
    </table>
    <?php
}

function valid_email($email) {
    if (preg_match("/^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@([_a-zA-Z0-9-]+\.)*[a-zA-Z0-9-]{2,200}\.[a-zA-Z]{2,6}$/", $email)) {
        return true;
    } else {
        return false;
    }
}

function valid_date($strdate) {

    //Check the length of the entered Date value
    if ((strlen($strdate) < 10)OR ( strlen($strdate) > 10)) {
        return false;
    } else {

        //The entered value is checked for proper Date format
        if ((substr_count($strdate, "/")) <> 2) {
            return false;
        } else {
            $pos = strpos($strdate, "/");
            $date = substr($strdate, 0, ($pos));
            $result = preg_match("/^[0-9]+$/", $date, $trashed);
            if (!($result)) {
                return false;
            } else {
                if (($date <= 0)OR ( $date > 31)) {
                    return false;
                }
            }
            $month = substr($strdate, ($pos + 1), ($pos));
            if (($month <= 0)OR ( $month > 12)) {
                return false;
            } else {
                $result = preg_match("/^[0-9]+$/", $month, $trashed);
                if (!($result)) {
                    return false;
                }
            }
            $year = substr($strdate, ($pos + 4), strlen($strdate));
            $result = preg_match("/^[0-9]+$/", $year, $trashed);
            if (!($result)) {
                return false;
            } else {
                if (($year < 1900)OR ( $year > 2200)) {
                    return false;
                }
            }
        }
    }
    return true;
}

function utf8_converter($array)
{
    array_walk_recursive($array, function(&$item, $key){
        if(!mb_detect_encoding($item, 'utf-8', true)){
            $item = utf8_encode($item);
        }
    });

    return $array;
}

function iso_8859_1_converter($array)
{
    array_walk_recursive($array, function(&$item, $key){
        if(mb_detect_encoding($item, 'utf-8', true)){
            $item = utf8_decode($item);
        }
    });

    return $array;
}

/**
 * As of PHP 5.5, the MySQL functions are deprecated and are removed in PHP 7.
 * mysql_result() is used to write less code when your database query is returning
 * only a single row (LIMIT 1) and/or a single column.
 * $output = mysql_result($result,0);
 * Pretty simple and straightforward. To replicate this in MySQLi, the following
 * function can be used:
 */
function mysqli_result($res,$row=0,$col=0){
    $numrows = mysqli_num_rows($res);
    if ($numrows && $row <= ($numrows-1) && $row >=0){
        mysqli_data_seek($res,$row);
        $resrow = (is_numeric($col)) ? mysqli_fetch_row($res) : mysqli_fetch_assoc($res);
        if (isset($resrow[$col])){
            return $resrow[$col];
        }
    }
    return false;
}
?>
