<?php

namespace Imx;
use DateTime;
use DateInterval;
use DatePeriod;
/**
 * Utils class
 * Miscellaneuos utilities for IMX namespace
 * 
 */
class utils
{

    static function safe_json_encode($data)
    {
        header("Content-type: application/json; charset=utf-8");
        $response = json_encode($data);
        if (json_last_error()) {
            array_walk_recursive($data, function (&$a) {
                if (!json_encode(["data" => $a])) {
                    $a = utf8_encode($a);
                    $a = utf8_encode($a);
                }
            });
            $response = json_encode($data, JSON_UNESCAPED_UNICODE);
        }
        return $response;
    }
    static function fileslink($string, $tag, $path)
    {
        if ($string != "") {
            $files = explode(",", $string);

            $link = "";
            $i = 0;
            $f = "";
            foreach ($files as $file) {
                $i++;
                $file = $f . $file;
                $f = "";
                if (strpos($file, ".") == false) {
                    $f = $file . ",";
                    $i--;
                } else {
                    $link .= " <a download title='$file' target='_blank' href='$path$file'>$tag $i</a> <br>";
                }
            }
            return $link;
        } else {
            return  "";
        }
    }
    static function meses($mes)
    {
        switch ($mes) {
            case '01':
                return 'Enero';

                break;
            case '02':
                return 'Febrero';

                break;
            case '03':
                return 'Marzo';

                break;
            case '04':
                return 'Abril';

                break;
            case '05':
                return 'Mayo';

                break;
            case '06':
                return 'Junio';

                break;
            case '07':
                return 'Julio';

                break;
            case '08':
                return 'Agosto';

                break;
            case '09':
                return 'Septiembre';

                break;
            case '10':
                return 'Octubre';

                break;
            case '11':
                return 'Noviembre';

                break;
            case '12':
                return 'Diciembre';

                break;
        }
    }
    // color aleatorio


    // funcion para formatear moneda
    static function monedas2($cantidad)
    {
        if (!is_numeric($cantidad)) {
            $cantidad = 0;
        }

        return '$ ' . number_format($cantidad, 2);
    }
    static function monedas4($cantidad)
    {
        return '$ ' . number_format($cantidad, 2);
    }
    static function decimales2($cantidad)
    {
        if (!is_numeric($cantidad)) {
            $cantidad = 0;
        }

        return number_format($cantidad, 2, '.', ',');
    }
    static function numeros2($cantidad)
    {
        if (!is_numeric($cantidad)) {
            $cantidad = 0;
        }

        return number_format($cantidad, 2, '.', '');
    }
    static function numeros6($cantidad)
    {
        if (!is_numeric($cantidad)) {
            $cantidad = 0;
        }

        return number_format($cantidad, 6, '.', '');
    }
    static function numeros4($cantidad)
    {
        if (!is_numeric($cantidad)) {
            $cantidad = 0;
        }

        return number_format($cantidad, 4, '.', '');
    }
    static function decimales($cantidad)
    {
        return number_format($cantidad, 2, '.', ',');
    }

    static function decimales4($cantidad)
    {
        return number_format($cantidad, 4, '.', ',');
    }

    static function decimalesf4($cantidad)
    {
        return number_format($cantidad, 4, '.', '');
    }

    static function porcentaje2($cantidad)
    {
        return sprintf('%.2f%%', $cantidad * 100);
    }




    // funciones de fecha
    static function dias($fechai, $fechaf)
    {
        $date1 = strtotime($fechai);
        $date2 = strtotime($fechaf);
        $dateDiff = $date1 - $date2;

        return floor($dateDiff / (60 * 60 * 24));
    }

    static function daysInRange($fechai, $fechaf)
    {
        $dates = [];
        $begin = new DateTime( $fechai);
        $end = new DateTime( $fechaf );
        $end = $end->modify( '+1 day' );
        $interval = new DateInterval('P1D');
        $daterange = new DatePeriod($begin, $interval ,$end);
        foreach($daterange as $date){
            $dates[] = $date->format("Y-m-d");
        }
        return $dates;


    }

    // FUNCION PARA DIA DE LA SEMANA CORTO
    static function diasemc($fecha)
    {
        $w = date('N', strtotime($fecha));
        switch ($w) {
            case '0':
                return 'Dom';

                break;
            case '1':
                return 'Lun';

                break;
            case '2':
                return 'Mar';

                break;
            case '3':
                return 'Mie';

                break;
            case '4':
                return 'Jue';
                break;
            case '5':
                return 'Vie';

                break;
            case '6':
                return 'Sab';

                break;
        }
    }

    static function fechamex($fecha)
    {
        $sfecha = explode('-', $fecha);

        return $sfecha[2] . '/' . $sfecha[1] . '/' . $sfecha[0];
    }
    static function fechamexshort($fecha)
    {
        $sfecha = explode('-', $fecha);

        return $sfecha[2] . '/' . $sfecha[1] . '/' . $sfecha[0];
    }
    static function fechahoramex($fecha)
    {
        $hora = ' ' . substr($fecha, 11);
        $sfecha = explode('-', substr($fecha, 0, 11));

        return trim($sfecha[2]) . '/' . $sfecha[1] . '/' . $sfecha[0] . $hora;
    }
    static function fechaest($fecha)
    {
        $sfecha = explode('/', $fecha);

        return $sfecha[2] . '-' . $sfecha[1] . '-' . $sfecha[0];
    }
    static function sql2date($fecha)
    {
        $sfecha = explode('-', $fecha);
        return $sfecha[1] . '/' . $sfecha[2] . '/' . $sfecha[0];
    }
    static function date2sql($fecha)
    {
        $sfecha = explode('/', $fecha);

        return $sfecha[2] . '-' . $sfecha[0] . '-' . $sfecha[1];
    }
    static function sql2datetime($fecha)
    {
        $hora = ' ' . substr($fecha, 11);
        $sfecha = explode('-', substr($fecha, 0, 11));

        return trim($sfecha[1]) . '/' . $sfecha[2] . '/' . $sfecha[0] . $hora;
    }
    static function fechahoraest($fecha)
    {
        $hora = ' ' . substr($fecha, 11);
        $sfecha = explode('/', substr($fecha, 0, 10));

        return $sfecha[2] . '-' . $sfecha[1] . '-' . $sfecha[0] . $hora;
    }
    static function diasdif($fecha1, $fecha2)
    {
        $date_diff = strtotime($fecha1) - strtotime($fecha2);

        return $date_diff / (60 * 60 * 24); //( 60 * 60 * 24) // seconds into days
    }
    static function agregadias($fecha, $cantidad)
    {
        return date('Y-m-d', strtotime($fecha . " +{$cantidad} days"));
    }
    static function quitadias($fecha, $cantidad)
    {
        return date('Y-m-d', strtotime($fecha . " -{$cantidad} days"));
    }





    static function valida_rfc($valor)
    {
        $valor = str_replace('-', '', $valor);
        $cuartoValor = substr($valor, 3, 1);
        //RFC sin homoclave
        if (10 == strlen($valor)) {
            $letras = substr($valor, 0, 4);
            $numeros = substr($valor, 4, 6);
            if (ctype_digit($numeros)) {
                return true;
            }

            return false;
        }
        // S�lo la homoclave
        if (3 == strlen($valor)) {
            $homoclave = $valor;
            if (ctype_alnum($homoclave)) {
                return true;
            }

            return false;
        }
        //RFC Persona Moral.
        if (ctype_digit($cuartoValor) && 12 == strlen($valor)) {
            $letras = substr($valor, 0, 3);
            $numeros = substr($valor, 3, 6);
            $homoclave = substr($valor, 9, 3);
            if (ctype_digit($numeros) && ctype_alnum($homoclave)) {
                return true;
            }

            return false;
            //RFC Persona F�sica.
        }
        if (ctype_alpha($cuartoValor) && 13 == strlen($valor)) {
            $letras = substr($valor, 0, 4);
            $numeros = substr($valor, 4, 6);
            $homoclave = substr($valor, 10, 3);
            if (ctype_digit($numeros) && ctype_alnum($homoclave)) {
                return true;
            }

            return false;
        }

        return false;
    } //fin validaRFC

    //# convertir array a xml

    //# FUNCION PARA CONVERTIR ARREGLO EN XML
    static function XML2Array(SimpleXMLElement $parent)
    {
        $array = [];

        foreach ($parent as $name => $element) {
            ($node = &$array[$name])
                && (1 === count($node) ? $node = [$node] : 1)
                && $node = &$node[];

            $node = $element->count() ? XML2Array($element) : trim($element);
        }

        return $array;
    }

    static function xmlToArray($contents, $getAttributes = true, $tagPriority = true, $encoding = 'utf-8')
    {
        $contents = trim($contents);
        if (empty($contents)) {
            return [];
        }
        $parser = xml_parser_create('');
        xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, $encoding);
        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
        xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
        if (0 === xml_parse_into_struct($parser, $contents, $xmlValues)) {
            xml_parser_free($parser);

            return [];
        }
        xml_parser_free($parser);
        if (empty($xmlValues)) {
            return [];
        }
        unset($contents, $parser);
        $xmlArray = [];
        $current = &$xmlArray;
        $repeatedTagIndex = [];
        foreach ($xmlValues as $num => $xmlTag) {
            $result = null;
            $attributesData = null;
            if (isset($xmlTag['value'])) {
                if ($tagPriority) {
                    $result = $xmlTag['value'];
                } else {
                    $result['value'] = $xmlTag['value'];
                }
            }
            if (isset($xmlTag['attributes']) and $getAttributes) {
                foreach ($xmlTag['attributes'] as $attr => $val) {
                    if ($tagPriority) {
                        $attributesData[$attr] = $val;
                    } else {
                        $result['@attributes'][$attr] = $val;
                    }
                }
            }
            if ('open' == $xmlTag['type']) {
                $parent[$xmlTag['level'] - 1] = &$current;
                if (!is_array($current) or (!in_array($xmlTag['tag'], array_keys($current)))) {
                    $current[$xmlTag['tag']] = $result;
                    unset($result);
                    if ($attributesData) {
                        $current['@' . $xmlTag['tag']] = $attributesData;
                    }
                    $repeatedTagIndex[$xmlTag['tag'] . '_' . $xmlTag['level']] = 1;
                    $current = &$current[$xmlTag['tag']];
                } else {
                    if (isset($current[$xmlTag['tag']]['0'])) {
                        $current[$xmlTag['tag']][$repeatedTagIndex[$xmlTag['tag'] . '_' . $xmlTag['level']]] = $result;
                        unset($result);
                        if ($attributesData) {
                            if (isset($repeatedTagIndex['@' . $xmlTag['tag'] . '_' . $xmlTag['level']])) {
                                $current[$xmlTag['tag']][$repeatedTagIndex['@' . $xmlTag['tag'] . '_' . $xmlTag['level']]] =
                                    $attributesData;
                            }
                        }
                        ++$repeatedTagIndex[$xmlTag['tag'] . '_' . $xmlTag['level']];
                    } else {
                        $current[$xmlTag['tag']] = [$current[$xmlTag['tag']], $result];
                        unset($result);
                        $repeatedTagIndex[$xmlTag['tag'] . '_' . $xmlTag['level']] = 2;
                        if (isset($current['@' . $xmlTag['tag']])) {
                            $current[$xmlTag['tag']]['@0'] = $current['@' . $xmlTag['tag']];
                            unset($current['@' . $xmlTag['tag']]);
                        }
                        if ($attributesData) {
                            $current[$xmlTag['tag']]['@1'] = $attributesData;
                        }
                    }
                    $lastItemIndex = $repeatedTagIndex[$xmlTag['tag'] . '_' . $xmlTag['level']] - 1;
                    $current = &$current[$xmlTag['tag']][$lastItemIndex];
                }
            } elseif ('complete' == $xmlTag['type']) {
                if (!isset($current[$xmlTag['tag']]) and empty($current['@' . $xmlTag['tag']])) {
                    $current[$xmlTag['tag']] = $result;
                    unset($result);
                    $repeatedTagIndex[$xmlTag['tag'] . '_' . $xmlTag['level']] = 1;
                    if ($tagPriority and $attributesData) {
                        $current['@' . $xmlTag['tag']] = $attributesData;
                    }
                } else {
                    if (isset($current[$xmlTag['tag']]['0']) and is_array($current[$xmlTag['tag']])) {
                        $current[$xmlTag['tag']][$repeatedTagIndex[$xmlTag['tag'] . '_' . $xmlTag['level']]] = $result;
                        unset($result);
                        if ($tagPriority and $getAttributes and $attributesData) {
                            $current[$xmlTag['tag']]['@' . $repeatedTagIndex[$xmlTag['tag'] . '_' . $xmlTag['level']]] =
                                $attributesData;
                        }
                        ++$repeatedTagIndex[$xmlTag['tag'] . '_' . $xmlTag['level']];
                    } else {
                        $current[$xmlTag['tag']] = [
                            $current[$xmlTag['tag']],
                            $result,
                        ];
                        unset($result);
                        $repeatedTagIndex[$xmlTag['tag'] . '_' . $xmlTag['level']] = 1;
                        if ($tagPriority and $getAttributes) {
                            if (isset($current['@' . $xmlTag['tag']])) {
                                $current[$xmlTag['tag']]['@0'] = $current['@' . $xmlTag['tag']];
                                unset($current['@' . $xmlTag['tag']]);
                            }
                            if ($attributesData) {
                                $current[$xmlTag['tag']]['@' . $repeatedTagIndex[$xmlTag['tag'] . '_' . $xmlTag['level']]] =
                                    $attributesData;
                            }
                        }
                        ++$repeatedTagIndex[$xmlTag['tag'] . '_' . $xmlTag['level']];
                    }
                }
            } elseif ('close' == $xmlTag['type']) {
                $current = &$parent[$xmlTag['level'] - 1];
            }
            unset($xmlValues[$num]);
        }

        return $xmlArray;
    }




    //## funcion para escanear un directorio y ordernar archivos por fecha de creacion

    static function scandir_by_mtime($folder)
    {
        $dircontent = scandir($folder);
        $arr = [];
        foreach ($dircontent as $filename) {
            if ('.' != $filename && '..' != $filename) {
                if (false === filemtime($folder . $filename)) {
                    return false;
                }
                $dat = date('YmdHis', filemtime($folder . $filename));
                $arr[$dat] = $filename;
            }
        }
        if (!sort($arr)) {
            return false;
        }

        return $arr;
    }

    static function print_r_cool($array)
    {
        echo '
            <pre>';
        print_r($array);
        echo '<pre>';
    }


    static function get_guid()
    {
        if (function_exists('com_create_guid')) {
            return trim(com_create_guid(), '{}');
        } else {
            mt_srand((float)microtime() * 10000); //optional for php 4.2.0 and up.
            $charid = strtoupper(md5(uniqid(rand(), true)));
            $hyphen = chr(45); // "-"
            $uuid = substr($charid, 0, 8) . $hyphen
                . substr($charid, 8, 4) . $hyphen
                . substr($charid, 12, 4) . $hyphen
                . substr($charid, 16, 4) . $hyphen
                . substr($charid, 20, 12);
            return trim($uuid);
        }

        // return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
    }

    static function clean($string)
    {
        return   str_replace(array('  ', "\n", "\t", "\r"), '', $string);
    }
    /**
     * Encrypt function
     * 
     * Encrypt a string with salt stored on .env file
     *
     * @param [type] $textToEncrypt
     * @param string $salt
     * @return string
     */
    static function encrypt($textToEncrypt, $salt = "")
    {
        if ($salt == "") {
            $salt = $_ENV['ENCRYPTION_KEY'];
        }
        $encryptionMethod = "AES-256-CBC";  // AES is used by the U.S. gov't to encrypt top secret documents.

        //To encrypt
        return openssl_encrypt($textToEncrypt, $encryptionMethod, $salt);
    }

    static function decrypt($encryptedMessage, $salt = "")
    {
        if ($salt == "") {
            $salt = $_ENV['ENCRYPTION_KEY'];
        }
        $encryptionMethod = "AES-256-CBC";  // AES is used by the U.S. gov't to encrypt top secret documents.

        return openssl_decrypt($encryptedMessage, $encryptionMethod, $salt);
    }

    static function resizer($source, $destination, $size, $quality = null)
    {
        // $source - Original image file
        // $destination - Resized image file name
        // $size - Single number for percentage resize
        // Array of 2 numbers for fixed width + height
        // $quality - Optional image quality. JPG & WEBP = 0 to 100, PNG = -1 to 9

        // (A) FILE CHECKS
        // Allowed image file extensions
        $ext = strtolower(pathinfo($source)["extension"]);
        if (!in_array($ext, ["bmp", "gif", "jpg", "jpeg", "png", "webp"])) {
            throw new Exception("Invalid image file type");
        }

        // Source image not found!
        if (!file_exists($source)) {
            throw new Exception("Source image file not found");
        }

        // (B) IMAGE DIMENSIONS
        $dimensions = getimagesize($source);
        $width = $dimensions[0];
        $height = $dimensions[1];

        if (is_array($size)) {
            $new_width = $size[0];
            $new_height = $size[1];
        } else {
            $new_width = ceil(($size / 100) * $width);
            $new_height = ceil(($size / 100) * $height);
        }

        // (C) RESIZE
        // Respective PHP image functions
        $fnCreate = "imagecreatefrom" . ($ext == "jpg" ? "jpeg" : $ext);
        $fnOutput = "image" . ($ext == "jpg" ? "jpeg" : $ext);

        // Image objects
        $original = $fnCreate($source);
        $resized = imagecreatetruecolor($new_width, $new_height);

        // Transparent images only
        if ($ext == "png" || $ext == "gif") {
            imagealphablending($resized, false);
            imagesavealpha($resized, true);
            imagefilledrectangle(
                $resized,
                0,
                0,
                $new_width,
                $new_height,
                imagecolorallocatealpha($resized, 255, 255, 255, 127)
            );
        }

        // Copy & resize
        imagecopyresampled(
            $resized,
            $original,
            0,
            0,
            0,
            0,
            $new_width,
            $new_height,
            $width,
            $height
        );

        // (D) OUTPUT & CLEAN UP
        if (is_numeric($quality)) {
            $fnOutput($resized, $destination, $quality);
        } else {
            $fnOutput($resized, $destination);
        }
        imagedestroy($original);
        imagedestroy($resized);
    }
}
