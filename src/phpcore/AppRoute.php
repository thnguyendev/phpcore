<?php
namespace PHPCore;

use Psr\Http\Message\Response;

abstract class AppRoute
{
    protected $routes = [];

    abstract public function initialize();

    public function getRoute(string $method, string $path)
    {
        $route = null;
        $count = 0;
        $trim = trim($path, "/");
        $words = $trim === "" ? [] : explode("/", $trim);
        $wordCount = count($words);
        foreach ($this->routes as $item)
        {
            if (!is_array($item) || !isset($item[RouteProperties::Path]) || !is_string($item[RouteProperties::Path]))
                continue;
            if (isset($item[RouteProperties::Methods]) && !in_array($method, $item[RouteProperties::Methods]))
                continue;
            $trim = trim($item[RouteProperties::Path], "/");
            $keys = $trim === "" ? [] : explode("/", $trim);
            $keyCount = count($keys);
            $params = [];
            if (isset($item[RouteProperties::Parameters]) && is_array($item[RouteProperties::Parameters]))
                $params = $item[RouteProperties::Parameters];
            $paramCount = count($params);
            if ($keyCount + $paramCount !== $wordCount || ($route !== null && $keyCount <= $count))
                continue;
            $paths = array_slice($words, 0, $keyCount);
            if (strtolower(join("/", $paths)) === strtolower(trim($item[RouteProperties::Path], "/")))
            {
                $args = [];
                if ($paramCount > 0)
                {
                    $values = array_slice($words, $keyCount);
                    $i = 0;
                    while ($i < $paramCount)
                    {
                        $name = $params[$i];
                        if (empty($name))
                            $args[] = $values[$i];
                        else
                            $args[$name] = $values[$i];
                        $i++;
                    }
                }
                $clone = array_merge(array(), $item);
                $clone[RouteProperties::Parameters] = $args;
                $route = $clone;
                $count = $keyCount;
            }
        }
        if ($route === null)
            throw new NotFoundException("Route {$path} for {$method} method not found", 404);
        if (isset($route[RouteProperties::Redirect]))
        {
            if (!is_string($route[RouteProperties::Redirect]))
                throw new \Exception("Redirect URL must be a string", 500);
            header(Initialization::getProtocol()." 301 ".Response::$defaultReasonPhrase[301], true);
            header("Location: ".$route[RouteProperties::Redirect]);
            exit;
        }
        return $route;
    }
}
?>