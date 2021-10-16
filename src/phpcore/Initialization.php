<?php
namespace PHPCore;

class Initialization
{
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
                $headers["content-type"] = $value;
            }
            else if ($key === "CONTENT_LENGTH")
            { 
                $headers["content-length"] = $value;
            }
        }
        return $headers;
    }

    public static function getProtocol()
    {
        return $_SERVER["SERVER_PROTOCOL"];
    }

    public static function getProtocolVersion()
    {
        return substr($_SERVER["SERVER_PROTOCOL"], 5);
    }

    public static function getBody()
    {
        return "php://input";   
    }

    public static function getMethod()
    {
        return $_SERVER["REQUEST_METHOD"] ?? "";
    }

    public static function getScheme()
    {
        if (isset($_SERVER["REQUEST_SCHEME"]))
            return $_SERVER["REQUEST_SCHEME"];
        else
            return (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] === "on") || $_SERVER["SERVER_PORT"] === "443" ? "https" : "http";
    }

    public static function getUser()
    {
        return $_SERVER["PHP_AUTH_USER"] ?? null;
    }

    public static function getPassword()
    {
        return $_SERVER["PHP_AUTH_PW"] ?? null;
    }

    public static function getHost()
    {
        if (isset($_SERVER["HTTP_HOST"]))
            return explode(":", $_SERVER["HTTP_HOST"])[0];
        else
            return "";
    }

    public static function getPort()
    {
        return isset($_SERVER["SERVER_PORT"]) ? intval($_SERVER["SERVER_PORT"]) : null;
    }

    public static function getPath()
    {
        if (isset($_SERVER["REQUEST_URI"]))
            return explode("?", $_SERVER["REQUEST_URI"])[0];
        else
            return "";
    }

    public static function getQuery()
    {
        return $_SERVER["QUERY_STRING"] ?? "";
    }

    public static function getCookies()
    {
        return $_COOKIE;
    }

    public static function getUploadedFiles()
    {
        return $_FILES;
    }

    public static function getServerParams()
    {
        return $_SERVER;
    }

    public static function getQueryParams()
    {
        return $_GET;
    }

    public static function getParsedBody()
    {
        return $_POST;
    }
}
?>