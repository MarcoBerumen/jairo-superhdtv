<?php

namespace Imx;

use mysqli;

class db
{

    function die($err)
    {
        die("<div class='error'>Error de conexi&oacute;n con el motor de base de datos MySQL: {$err} </div>");
    }

    /**
     * @param $query
     * @return string
     */
    public static function iquery($query)
    { // funcion para insertar registros devoldiendo error
        include 'db.php';
        $db = self::mycon();
        if ($db->connect_error) {
            die($db->connect_error);
        } // funcion de error mysqli
        $db->multi_query($query);
        if ('' != $db->error) {
            if ('1451' == $db->errno) {
                die('No se puede alterar este registro, ya que contiene claves foraneas en uso');
            }
            die("Error en consulta SQL <br> Error : " . $db->error . "<br> Consulta : {$query}");
        }

        return $db->insert_id;
    }

    public static function iquerySkip($query, $modulo = '')
    { // funcion para insertar registros devoldiendo error
        $db = self::mycon();
        if ($db->connect_error) {
            die($db->connect_error);
        } // funcion de error mysqli
        $db->multi_query($query);
    }

    public static function insert(string $table, array $fields, array $values)
    { // funcion para insertar registros devoldiendo error


        if (count($fields) == 0) {
            die('No se especificaron los campos');
        }

        if (count($fields) != count($values)) {
            die('Los campos definidos y los parametros no coinciden');
        }

        $val = [];
        $fieldstring = [];
        foreach ($fields as $field) {
            $fieldstring[] = "`$field`";
        }
        $params = "";

        // print_r($values);
        foreach ($values as $value) {
            // $value =
            $params .= "s";

            $value = self::mycon()->real_escape_string($value);

            $val[] = "'$value'";
        }
        // exit;
        $val = implode(",", $val);
        $fieldstring = implode(",", $fieldstring);
        $query = "insert into $table ({$fieldstring}) values ($val)";
        return self::iquery($query);
    }

    //# function rquery
    public static function mycon()
    {

        if ($_ENV['DB_PORT'] != 3306) {

            $myobj = new mysqli($_ENV['DB_HOST'] . ":" . $_ENV['DB_PORT'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD'], $_ENV['DB_NAME']);
        } else {

            $myobj = new mysqli($_ENV['DB_HOST'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD'], $_ENV['DB_NAME']);
        }
        $myobj->query('SET SESSION group_concat_max_len = 1000000;');
        // $myobj->query("SET GLOBAL sql_mode = ''");

        if ($myobj->connect_error) {
            die("DB Error: " . $myobj->connect_error);
        } else {

            return $myobj;
        }
    }

    //# function rquery
    public static function rquery($query, $campo = 0)
    {
        if (!isset($modulo)) {
            $modulo = '';
        }
        $db = self::mycon();
        $db->query($query);
        if ('' != $db->error) {
            $error = $db->error;
            $db->close();
            die("Error en consulta SQL <br>Modulo: {$modulo} <br> Error : {$error} <br> Consulta : {$query}");
        } else {
            $consulta = $db->query($query);
            $rs = $consulta->fetch_array();

            return $rs[$campo];
            $db->close();
        }
    }

    //# funciopara actualizar tabla a partir de un post
    public static function e_post($data, $tabla, $id, $valcampo = '', $idtabla = '') //# edita post
    {
        if ('' == $idtabla) {
            $idtabla = "id";
        }
        $db = self::mycon();

        if ('' != $valcampo) {
            //# validamos que este campo no este repetido
            // if (!is_array($valcampo)) {
            $dato =  self::mycon()->real_escape_string(trim($data[$valcampo]['value']));;

            $query = "select count(*) as rec from {$tabla} where {$valcampo} ='{$dato}' and {$idtabla} <> '{$id}'";
            if (self::rquery($query, 'rec') > 0) {
                return ("Ya existe un {$valcampo} con el valor {$dato}");
            }
            // ? TODO ARRAY VAL CAMPO
            // } else {
            //     $str = '';
            //     $campos = '';
            //     foreach ($valcampo as $campo) {
            //         $dato = $_POST[$campo];
            //         $campos .= "{$campo},";
            //         if ('' == $str) {
            //             $str .= " {$campo} ='{$dato}'";
            //         } else {
            //             $str .= " and {$campo} ='{$dato}'";
            //         }
            //     }

            //     $query = "select count(*) as rec from {$tabla} where {$str} and {$idtabla} <> '{$id}'";
            //     if (self::rquery($query, 'rec') > 0) {
            //         return ("Ya existe un registro con los mismos valores : {$campos}");
            //     }
            // }
        }
        $sql = "update `{$tabla}` set ";
        foreach ($data as $key => $dato) {
            if (substr($key, 0, 2) == "x_") continue;
            $value = self::mycon()->real_escape_string(trim($dato['value']));;
            if (2 == substr_count($value, '/') && 10 == strlen($value)) {
                $sfecha = explode('/', $value);
                $value = $sfecha[2] . '-' . $sfecha[1] . '-' . $sfecha[0];
            }
            if (2 == substr_count($value, '/') && 2 == substr_count($value, ':')) {
                $sfechahora = explode(' ', $value);
                $sfecha = explode('/', $sfechahora[0]);
                $value = $sfecha[2] . '-' . $sfecha[1] . '-' . $sfecha[0] . ' ' . $sfechahora[1];
            }

            if (is_numeric($value)) {
                $sql = $sql . '`' . $key . '`' . " = '{$value}' ,";
            } else if ($value == 'NULL') {
                $sql = $sql . '`' . $key . '`' . " = NULL ,";
            } else {
                $sql = $sql . '`' . $key . '`' . " = '{$value}' ,";
            }
        }
        $sql = substr($sql, 0, -1);
        $sql = $sql . " where {$idtabla} ='{$id}'";
        $db->query($sql);
        if ($db->error) {
            return "Mysql query error : " . $db->error;
        } else {
            return $id;
        }
        // return self::iquery($sql);
    }
    /**
     * jsonPut function
     * 
     *  Update a database record from a json object
     *
     * @param [type] $data
     * @param [type] $tabla
     * @param [type] $id
     * @param string $valcampo
     * @param string $idtabla
     * @return void
     */
    public static function jsonPut($data, $tabla, $id, $valcampo = '', $idtabla = '') //# edita post
    {
        if ('' == $idtabla) {
            $idtabla = "id";
        }
        $db = self::mycon();

        if ('' != $valcampo) {
            //# validamos que este campo no este repetido
            // if (!is_array($valcampo)) {
            $dato =  self::mycon()->real_escape_string(trim($data[$valcampo]));;

            $query = "select count(*) as rec from {$tabla} where {$valcampo} ='{$dato}' and {$idtabla} <> '{$id}'";
            if (self::rquery($query, 'rec') > 0) {
                return ("Ya existe un {$valcampo} con el valor {$dato}");
            }
        }
        $sql = "update `{$tabla}` set ";
        foreach ($data as $key => $dato) {
            if (substr($key, 0, 2) == "x_") continue;
            $value = self::mycon()->real_escape_string(trim($dato));;
            if (2 == substr_count($value, '/') && 10 == strlen($value)) {
                $sfecha = explode('/', $value);
                $value = $sfecha[2] . '-' . $sfecha[1] . '-' . $sfecha[0];
            }
            if (2 == substr_count($value, '/') && 2 == substr_count($value, ':')) {
                $sfechahora = explode(' ', $value);
                $sfecha = explode('/', $sfechahora[0]);
                $value = $sfecha[2] . '-' . $sfecha[1] . '-' . $sfecha[0] . ' ' . $sfechahora[1];
            }

            if (is_numeric($value)) {
                $sql = $sql . '`' . $key . '`' . " = '{$value}' ,";
            } else if ($value == 'NULL') {
                $sql = $sql . '`' . $key . '`' . " = NULL ,";
            } else {
                $sql = $sql . '`' . $key . '`' . " = '{$value}' ,";
            }
        }
        $sql = substr($sql, 0, -1);
        $sql = $sql . " where {$idtabla} ='{$id}'";
        $db->query($sql);
        if ($db->error) {
            return "Mysql query error : " . $db->error;
        } else {
            return "1";
        }
    }


    //## funcion para saber los registros que tiene un query

    //# funcion para insertar tabla a partir de un post
    public static function i_post($data, $tabla, $valcampo = '') //# itp = inserta tabla post
    {
        if ('' != $valcampo) {
            //# validamos que este campo no este repetido
            // if (!is_array($valcampo)) {
            $dato =  self::mycon()->real_escape_string(trim($data[$valcampo]['value']));;

            $query = "select count(*) as rec from {$tabla} where {$valcampo} ='{$dato}' ";
            if (self::rquery($query, 'rec') > 0) {
                return ("Ya existe un {$valcampo} con el valor {$dato}");
            }
        }
        // ? TODO ARRAY VAL CAMPO
        // } else {
        //     $str = '';
        //     $campos = '';
        //     foreach ($valcampo as $campo) {
        //         $dato = $_POST[$campo];
        //         $campos .= "{$campo},";
        //         if ('' == $str) {
        //             $str .= " {$campo} ='{$dato}'";
        //         } else {
        //             $str .= " and {$campo} ='{$dato}'";
        //         }
        //     }

        //         $query = "select count(*) as rec from {$tabla} where {$str}";
        //         if (self::rquery($query, 'rec') > 0) {
        //             return ("Ya existe un registro con los mismos valores : {$campos}");
        //         }
        //     }
        // }
        $sql1 = '';
        $sql2 = '';
        $sql_datos = "insert into `{$tabla}` (";
        foreach ($data as $key => $dato) {
            if (substr($key, 0, 2) == "x_") continue;

            $value = self::mycon()->real_escape_string(trim($dato['value']));;
            //echo substr_count($value, '/');
            if (2 == substr_count($value, '/') && 10 == strlen($value)) {
                $sfecha = explode('/', $value);
                $value = $sfecha[2] . '-' . $sfecha[1] . '-' . $sfecha[0];
            }
            if (2 == substr_count($value, '/') && 2 == substr_count($value, ':')) {
                $sfechahora = explode(' ', $value);
                $sfecha = explode('/', $sfechahora[0]);
                $value = $sfecha[2] . '-' . $sfecha[1] . '-' . $sfecha[0] . ' ' . $sfechahora[1];
            }
            $sql1 = $sql1 . '`' . $key . '`' . ',';
            $sql2 = $sql2 . "'" . $value . "',";
        }
        $sql1 = substr($sql1, 0, -1);
        $sql2 = substr($sql2, 0, -1);
        $sql = $sql_datos . $sql1 . ") values({$sql2})";
        //echo $sql;exit;
        return self::iquery($sql);
    }
    public static function jsonPost($data, $tabla, $valcampo = '') //# itp = inserta tabla post
    {
        if ('' != $valcampo) {
            //# validamos que este campo no este repetido
            $dato =  self::mycon()->real_escape_string(trim($data[$valcampo]));;
            $query = "select count(*) as rec from {$tabla} where {$valcampo} ='{$dato}' ";
            if (self::rquery($query, 'rec') > 0) {
                return "Data duplicated";
            }
        }

        $sql1 = '';
        $sql2 = '';
        $sql_datos = "insert into `{$tabla}` (";
        foreach ($data as $key => $dato) {
            if (substr($key, 0, 2) == "x_") continue;

            $value = self::mycon()->real_escape_string(trim($dato));;
            //echo substr_count($value, '/');
            if (2 == substr_count($value, '/') && 10 == strlen($value)) {
                $sfecha = explode('/', $value);
                $value = $sfecha[2] . '-' . $sfecha[1] . '-' . $sfecha[0];
            }
            if (2 == substr_count($value, '/') && 2 == substr_count($value, ':')) {
                $sfechahora = explode(' ', $value);
                $sfecha = explode('/', $sfechahora[0]);
                $value = $sfecha[2] . '-' . $sfecha[1] . '-' . $sfecha[0] . ' ' . $sfechahora[1];
            }
            $sql1 = $sql1 . '`' . $key . '`' . ',';
            $sql2 = $sql2 . "'" . $value . "',";
        }
        $sql1 = substr($sql1, 0, -1);
        $sql2 = substr($sql2, 0, -1);
        $sql = $sql_datos . $sql1 . ") values({$sql2})";
        //echo $sql;exit;
        return self::iquery($sql);
    }
    // funcion para generar seleccionables
    public static function sel(
        $nombre,
        $tabla,
        $valor,
        $etiqueta,
        $linea1 = '',
        $selval = '',
        $clase = '',
        $attr = '',
        $filtro = '',
        $orden = ''
    ) {
        // nombe del select, tabla y campos a mostrar
        if ('' != $clase) {
            $clase = "class='{$clase}'";
        }
        if ('' == $orden) {
            $orden = "order by {$etiqueta}";
        }
        $query_sp = "select {$valor},{$etiqueta} from {$tabla} {$filtro} {$orden}";

        self::iquery($query_sp);

        // echo $query_sp;
        // linea 1 es el valor del primer selccionable
        // selval es el valor que debe estar seleccionado
        // clase de estil y atributos adicionales a agregar
        $db = self::mycon();
        $qr = $db->query($query_sp);
        //echo $query_sp;
        echo "<select name='{$nombre}' id='{$nombre}' {$clase} {$attr}>\n";
        if ('' != $linea1) {
            echo "<option value=''>{$linea1}</option>\n";
        }
        // comenzamos a filtrar en tabla

        $selected = '';
        while ($rs = $qr->fetch_array()) {
            if ($rs["{$valor}"] == $selval) {
                $selected = "selected='selected' class='defecto'";
            }
            echo "<option value='" . $rs[$valor] . "' {$selected}>" . $rs[1] . "</option>\n";
            $selected = '';
        }

        echo '</select>';
        $db->close();
    }

    public static function jsonQuery($query)
    {
        $db = self::mycon();

        if ($db->connect_error) {
            die($db->connect_error);
        } // funcion de error mysqli
        $db->query('SET CHARACTER SET utf8');
        $db->query($query);
        if ('' != $db->error) {
            $error = $db->error;
            $db->close();
            header("HTTP/1.0 500 Server Error");

            die("SQL  Error : {$error} <br>  : {$query}");
        } else {
            $data = [];
            $consulta = $db->query($query);
            while ($rs = $consulta->fetch_assoc()) {
                array_push($data, $rs);
            }
            $db->close();

            return json_encode($data);
        }
    }

    public static function jsonQuery0($query)
    {
        $db = self::mycon(); // new mysqli($this->mysql_host, $this->mysql_usuario,$mthis->ysql_password,
        if ($db->connect_error) {
            die($db->connect_error);
        } // funcion de error mysqli
        $db->query($query);
        if ('' != $db->error) {
            $error = $db->error;
            $db->close();
            header("HTTP/1.0 500 Server Error");
            die("SQL  Error : {$error} <br> Query : {$query}");
        } else {
            $data = [];
            $consulta = $db->query($query);
            while ($rs = $consulta->fetch_assoc()) {
                $data[] = $rs;
            }
            $db->close();

            return json_encode($data[0]);
        }
    }

    public static function dataQuery($query)
    {
        $db = self::mycon();
        if ($db->connect_error) {
            die($db->connect_error);
        } // funcion de error mysqli
        $db->query($query);
        if ('' != $db->error) {
            $error = $db->error;
            $db->close();
            header("HTTP/1.0 500 Server Error");

            die("SQL Error <br> Error : {$error} <br> Query : {$query}");
        } else {
            $data = [];
            $consulta = $db->query($query);
            if (1 == $consulta->num_rows) {
                return $consulta->fetch_assoc();
            }

            $data = [];
            while ($rs = $consulta->fetch_assoc()) {
                $data[] = $rs;
            }

            return $data;
        }
    }
    public static function dataQueryMultiple($query)
    {
        $db = self::mycon();

        if ($db->connect_error) {
            die($db->connect_error);
        } // funcion de error mysqli
        $db->query($query);
        if ('' != $db->error) {
            $error = $db->error;
            $db->close();
            die("Error en consulta SQL <br>Modulo: {$modulo} <br> Error : {$error} <br> Consulta : {$query}");
        } else {
            $consulta = $db->query($query);
            // if (1 == $consulta->num_rows) {
            // return $consulta->fetch_assoc();
            // }

            $data = [];
            while ($rs = $consulta->fetch_assoc()) {
                $data[] = $rs;
            }

            return $data;
        }
    }
}
