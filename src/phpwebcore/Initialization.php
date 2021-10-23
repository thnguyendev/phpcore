<?php
namespace PHPWebCore;

class Initialization
{
    /**
     * Get request headers.
     *
     * @return array
     */
    public static function getHeaders()
    {
        $headers = [];
        foreach ($_SERVER as $key => $value)
        {
            if (preg_match("/^HTTP_/", $key))
            {
                $key = str_replace(" ", "-", strtolower(str_replace("_", " ", substr($key, 5)))); 
                $headers[$key] = $value;
            } 
            else if ($key === "CONTENT_TYPE")
            { 
                $headers[HttpHeader::ContentType] = $value;
            }
            else if ($key === "CONTENT_LENGTH")
            { 
                $headers[HttpHeader::ContentLength] = $value;
            }
        }
        return $headers;
    }

    /**
     * Get request protocol.
     *
     * @return string
     */
    public static function getProtocol()
    {
        return $_SERVER["SERVER_PROTOCOL"];
    }

    /**
     * Get requset protocol version.
     *
     * @return string
     */
    public static function getProtocolVersion()
    {
        return substr($_SERVER["SERVER_PROTOCOL"], 5);
    }

    /**
     * Get request body.
     *
     * @return string
     */
    public static function getBody()
    {
        return "php://input";   
    }

    /**
     * Get request HTTP method.
     *
     * @return string
     */
    public static function getMethod()
    {
        return $_SERVER["REQUEST_METHOD"] ?? "";
    }

    /**
     * Get request scheme.
     *
     * @return string
     */
    public static function getScheme()
    {
        if (isset($_SERVER["REQUEST_SCHEME"]))
            return $_SERVER["REQUEST_SCHEME"];
        else
            return (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] === "on") || $_SERVER["SERVER_PORT"] === "443" ? "https" : "http";
    }

    /**
     * Get request user name authority.
     *
     * @return string
     */
    public static function getUser()
    {
        return $_SERVER["PHP_AUTH_USER"] ?? null;
    }

    /**
     * Get request password authority.
     *
     * @return string
     */
    public static function getPassword()
    {
        return $_SERVER["PHP_AUTH_PW"] ?? null;
    }

    /**
     * Get request host.
     *
     * @return string
     */
    public static function getHost()
    {
        if (isset($_SERVER["HTTP_HOST"]))
            return explode(":", $_SERVER["HTTP_HOST"])[0];
        else
            return "";
    }

    /**
     * Get request port.
     *
     * @return int
     */
    public static function getPort()
    {
        return isset($_SERVER["SERVER_PORT"]) ? intval($_SERVER["SERVER_PORT"]) : null;
    }

    /**
     * Get request path.
     *
     * @return string
     */
    public static function getPath()
    {
        if (isset($_SERVER["REQUEST_URI"]))
            return explode("?", $_SERVER["REQUEST_URI"])[0];
        else
            return "";
    }

    /**
     * Get request query.
     *
     * @return string
     */
    public static function getQuery()
    {
        return $_SERVER["QUERY_STRING"] ?? "";
    }

    /**
     * Get request cookies.
     *
     * @return array
     */
    public static function getCookies()
    {
        return $_COOKIE;
    }

    /**
     * Get request uploaded files.
     *
     * @return array
     */
    public static function getUploadedFiles()
    {
        return $_FILES;
    }

    /**
     * Get request server params.
     *
     * @return array
     */
    public static function getServerParams()
    {
        return $_SERVER;
    }

    /**
     * Get request queries params.
     *
     * @return array
     */
    public static function getQueryParams()
    {
        return $_GET;
    }

    /**
     * Get request bosy params.
     *
     * @return array
     */
    public static function getParsedBody()
    {
        return $_POST;
    }
}
?>