<?php

namespace Imx;

/**
 * Class for sending HTTP Valid headers
 */
class headers
{

    /**
     * json
     *
     * Send json Application/Json Headers 
     * and expiry for non caching app
     * @return void
     */
    static public function json()
    {
        $ts = gmdate("D, d M Y 00:00:00") . " GMT";
        header("Expires: $ts");
        header("Last-Modified: $ts");
        header("Pragma: no-cache");
        header("Cache-Control: no-cache, must-revalidate");
        header('Content-Type: application/json; charset=utf-8');
    }
    /**
     * notFound function
     *
     *  Returns 404 header error 
     * @return void
     */
    static public  function notfound()
    {
        header('HTTP/1.0 404 Not Found');
    }
    /**
     * 500 error function
     *
     *  Returns 404 header error 
     * @return void
     */
    static public  function error500()
    {
        header('HTTP/1.0 500 Cannot process');
    }
    /**
     * 409 conflict function
     *
     *  Returns 404 header error 
     * @return void
     */
    static public  function conflict()
    {
        header('HTTP/1.0 409 Conflict');
    }
    /**
     * rofbidden function
     *
     *  Returns 403 header error 
     * @return void
     */
    static public  function forbidden()
    {
        header('HTTP/1.0 403 Forbidden');
    }
    /**
     * aunautorized function
     * 
     *  Sends httpd header 401 
     * @return void
     */
    static public  function unauthorized()
    {
        header('HTTP/1.0 401 Unauthorized');
    }
}
