<?php

namespace Imx;

use PDO;
use PDOException;

class select2
{
    static public function get($table, $page, $id, $value, $term = "", $extras = [])
    {
        if (DB_PORT == "3306") {
            $sql_details = array(
                'user' => DB_USER,
                'pass' => DB_PASSWORD,
                'db'   => DB_NAME,
                'host' => DB_HOST
            );
        } else {
            $sql_details = array(
                'user' => DB_USER,
                'pass' => DB_PASSWORD,
                'db'   => DB_NAME,
                'host' => DB_HOST . ":" . DB_PORT
            );
        }

        $db_con = @new PDO(
            "mysql:host={$sql_details['host']};dbname={$sql_details['db']}",
            $sql_details['user'],
            $sql_details['pass'],
            array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
        );



        try {
            $resultCount = 10;
            $end = ($page - 1) * $resultCount;
            $start = $end + $resultCount;

            // {
            //     "results": [
            //       {
            //         "id": 1,
            //         "text": "Option 1"
            //       },
            //       {
            //         "id": 2,
            //         "text": "Option 2"
            //       }
            //     ],
            //     "pagination": {
            //       "more": true
            //     }
            //   }

            $filter = "$value LIKE '%" . $term . "%'";
            if (isset($_GET['id'])) {
                $idfilter = $_GET['id'];
                $filter = "$id = '" . $idfilter . "'";
            }



            $stmt = $db_con->query("SELECT count(*) as recs FROM $table WHERE $filter");
            $stmt->execute();
            $recs = $stmt->fetch(PDO::FETCH_ASSOC)['recs'];
            $stmt = $db_con->query("SELECT *FROM $table WHERE $filter LIMIT {$end},{$start}");
            $stmt->execute();
            $data = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $dt = ['id' => $row[$id], 'text' => $row[$value]];
                foreach ($extras as $e) {
                    $dt[$e] = $row[$e] ?? "*";
                }
                $data[] = $dt;
            }
            // IF SEARCH TERM IS NOT FOUND DATA WILL BE EMPTY SO
            // echo $count;
            if ($recs <= ($end + $start)) {
                $more = false;
            } else {
                $more = true;
            }
            echo utils::safe_json_encode(['results' => $data, "pagination" => ["more" => $more]]);
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
}
