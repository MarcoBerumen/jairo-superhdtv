<?php

namespace Imx;

class printers
{
    static public function cadenasi($sep, $cad, $tam = 42) // funcion para recortar o agrandar cadenas con espacios.
    {
        $var = '';
        $cad = strtoupper($cad);
        if (strlen($cad) > $tam) {
            $cad = substr($cad, 0, $tam);
        }
        $lcadena = strlen($cad);
        $dif = $tam - $lcadena;
        for ($i = 1; $i <= $dif; ++$i) {
            $var .= $sep;
        }
        $var .= $cad;

        return $var;
    }
    static public function cadenasd($sep, $cad, $tam = 42) // funcion para recortar o agrandar cadenas con espacios.
    {
        $var = '';
        // $cad = strtoupper($cad);
        if (strlen($cad) > $tam) {
            $cad = substr($cad, 0, $tam);
        }
        $lcadena = strlen($cad);
        $var .= $cad;
        $dif = $tam - $lcadena;
        for ($i = 1; $i <= $dif; ++$i) {
            $var .= $sep;
        }

        return $var;
    }
    static public function centrar($sep, $cad, $tam = 42) // funcion para recortar o agrandar cadenas con espacios.
    {
        $var = '';
        // $cad = strtoupper($cad);
        if (strlen($cad) > $tam) {
            $cad = substr($cad, 0, $tam);
        }
        $lcadena = strlen($cad);
        $dif = $tam - $lcadena;
        $dif1 = ceil($dif / 2);
        $dif2 = floor($dif / 2);
        for ($i = 1; $i <= $dif1; ++$i) {
            $var .= $sep;
        }
        $var .= $cad;

        for ($i = 1; $i <= $dif2; ++$i) {
            $var .= $sep;
        }

        return $var;
    }
}
