<?php


namespace Imx;

use Aws\S3\S3Client;
use Aws\Exception\AwsException;
use Cocur\Slugify\Slugify;
use RuntimeException;
use LogicException;

/**
 * Clase para simplificar operaciones a Servicios tipo S3
 * Los parametros de conexion se invocan desde el archivo .env
 * y su respeccta inicializacion  en $_ENV
 * 
 * @package Imx\
 * @author Josue Jimenez
 * 
 */
class s3
{

    /**
     * Function privada para instancia un objeto S3
     * obtendra las variables desde $_ENV
     * 
     * @return S3Client 
     */
    static private function s3Client()
    {
        // $params = array(
        //     'endpoint' => $_ENV['S3_URL'],
        //     'region' => $_ENV['AWS_DEFAULT_REGION'],
        //     'version' => 'latest',
        //     'use_path_style_endpoint' => true,
        //     'credentials' => [
        //         'key' => $_ENV['AWS_ACCESS_KEY_ID'],
        //         'secret' => $_ENV['AWS_SECRET_ACCESS_KEY']
        //     ],
        // );


        $params = array(
            // 'endpoint' => $_ENV['S3_URL'],
            'region' => $_ENV['AWS_DEFAULT_REGION'],
            'version' => 'latest',
            // 'use_path_style_endpoint' => true,
            'credentials' => [
                'key' => $_ENV['AWS_ACCESS_KEY_ID'],
                'secret' => $_ENV['AWS_SECRET_ACCESS_KEY']
            ],
            'http'    => [
                'verify' => false
            ]

        );

        // print_r($params);
        return S3Client::factory($params);
    }
    /**
     * Function estatica para obtener un enlace temporal de 20 minutos
     * a partir del parametro $key
     * 
     * @param mixed $key 
     * @return string 
     * @throws RuntimeException 
     * @throws LogicException 
     */
    static public function getAsLink($key)
    {
        $client = self::s3Client();
        $result = $client->getCommand('GetObject', [
            'Bucket' => $_ENV['AWS_BUCKET'],
            'Key' => $key
        ]);
        $request = $client->createPresignedRequest($result, '+20 minutes');
        $presignedUrl = (string)$request->getUri();
        return $presignedUrl;
    }
    /**
     * Funcion estatica para obtener una descarga de un objecto
     * 
     * @param mixed $key 
     * @return mixed 
     */
    static public function removeFile($key)
    {
        $client = self::s3Client();
        $result =  $client->deleteObject([
            'Bucket' => $_ENV['AWS_BUCKET'],
            'Key'    => $key
        ]);
        // print_R($result);
    }
    static public function getAsFile($key, $download = true)
    {
        $client = self::s3Client();
        $result =  $client->GetObject([
            'Bucket' => $_ENV['AWS_BUCKET'],
            'Key'    => $key
        ]);
        // $result =  $client->GetObject([
        //     'Bucket' => $_ENV['AWS_BUCKET'],
        //     'Key'    => $key
        // ]);
        $extension = pathinfo($key, PATHINFO_EXTENSION);

        switch ($extension) {
            case "jpg":
                $mime = 'image/jpeg';
                break;
            case "pdf":
                $mime = 'application/pdf';
                break;
            case "png":
                $mime = 'image/png';
                break;
            default:
                $mime = "application/octet-stream";
                break;
        }

        if ($download)
            header('Content-Description: File Transfer');
        header('Content-Type:' . $mime);
        if ($download)
            header('Content-Disposition: attachment; filename="' . basename($key) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . $result['content-length']);
        return $result['Body'];
    }
    /**
     * Funcion estatica para almacenar un nuevo objeto 
     * 
     * @param mixed $filename  El nombre del archivo
     * @param mixed $path La ruta donde se almacenara el archivo en el bucket
     * @param mixed $source  La ruta temporal del archivo
     * @return array Nos retornara un objecto que siempre tendra la propiedad Valid
     */
    static public function store($filename, $path, $source)
    {

        $client = self::s3Client();

        $key = "$path$filename";

        try {
            $result = $client->putObject(array(
                'Bucket'     => $_ENV['AWS_BUCKET'],
                'Key'        => $key,
                'SourceFile' => $source

            ));

            $etag = $result['ETag'];
            $version = $result['VersionId'];

            return [
                'valid' => true,
                'etag' => $etag,
                'version' => $version,
                'key' => $key,
            ];
        } catch (S3Exception $e) {
            return [
                'valid' => false,
                'error' => $e->getMessage() . PHP_EOL,
            ];
        }
    }
    static public function storeString($filename, $path, $string)
    {
        $client = self::s3Client();
        // exit;
        $key = "$path$filename";

        try {
            $result = $client->putObject(array(
                'Bucket'     => $_ENV['AWS_BUCKET'],
                'Key'        => $key,
                'Body' => $string
            ));

            $etag = $result['ETag'];
            $version = $result['VersionId'];

            return [
                'valid' => true,
                'etag' => $etag,
                'version' => $version,
                'key' => $key,
            ];
        } catch (S3Exception $e) {
            return [
                'valid' => false,
                'error' => $e->getMessage() . PHP_EOL,
            ];
        }
    }

    static public function storePng($filename, $path, $string)
    {

        // header('Content-Type: image/png; charset=utf-8');


        $client = self::s3Client();


        // exit;
        $key = "$path$filename";

        try {
            $result = $client->putObject(array(
                'Bucket'     => $_ENV['AWS_BUCKET'],
                'Key'        => $key,
                'Body' => $string
            ));

            $etag = $result['ETag'];
            $version = $result['VersionId'];

            return [
                'valid' => true,
                'etag' => $etag,
                'version' => $version,
                'key' => $key,
                'Content-Type' => 'image/png'

            ];
        } catch (S3Exception $e) {
            return [
                'valid' => false,
                'error' => $e->getMessage() . PHP_EOL,
            ];
        }
    }

    /**
     * Metodo estatico para obtener lista de archivos
     * con un prefix del bucket del sistema.
     * 
     * @param mixed $prefix ruta ej . xxxx/yyyy/zzzz/
     * @return void Array de objetos
     */
    static public function listfiles($prefix)
    {
        $client = self::s3Client();
        try {
            $objects = $client->listObjects([
                'Bucket' => $_ENV['AWS_BUCKET'],
                'Prefix' => $prefix,
            ]);

            $objectArray = array();
            foreach ($objects as $object) {
                $objectArray[] = $object;
            }

            // print_r($objectArray);
        } catch (S3Exception $e) {
            echo $e->getMessage() . PHP_EOL;
        }
    }
}
