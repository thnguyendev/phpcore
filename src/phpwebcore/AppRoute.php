<?php
namespace PHPWebCore;

use Psr\Http\Message\Response;

abstract class AppRoute
{
    protected array $routes = [];

    /**
     * Initilize the AppRoute.
     */
    abstract public function initialize();

    /**
     * Find a route from HTTP method and Uri path.
     * 
     * @param string $method HTTP method
     * @param string @path Uri path
     * @return array a route is NULL if not found
     */
    public function getRoute(string $method, string $path)
    {
        $route = null;
        $count = 0;
        $trim = trim($path, "/");
        $words = $trim === "" ? [] : explode("/", $trim);
        $wordCount = count($words);
        foreach ($this->routes as $item)
        {
            if (!is_array($item) || !isset($item[RouteProperty::Path]) || !is_string($item[RouteProperty::Path]))
                continue;
            if ($method !== HttpMethod::Options && isset($item[RouteProperty::Methods]) && !in_array($method, $item[RouteProperty::Methods]))
                continue;
            $trim = trim($item[RouteProperty::Path], "/");
            $keys = $trim === "" ? [] : explode("/", $trim);
            $keyCount = count($keys);
            $params = [];
            if (isset($item[RouteProperty::Parameters]) && is_array($item[RouteProperty::Parameters]))
                $params = $item[RouteProperty::Parameters];
            $paramCount = count($params);
            if ($keyCount + $paramCount !== $wordCount || ($route !== null && $keyCount <= $count))
                continue;
            $paths = array_slice($words, 0, $keyCount);
            if (strtolower(join("/", $paths)) === strtolower(trim($item[RouteProperty::Path], "/")))
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
                $clone[RouteProperty::Parameters] = $args;
                $route = $clone;
                $count = $keyCount;
            }
        }
        if ($route === null)
            throw new NotFoundException("Route {$path} for {$method} method not found", 404);
        if (isset($route[RouteProperty::Redirect]))
        {
            if (!is_string($route[RouteProperty::Redirect]))
                throw new \Exception("Redirect URL must be a string", 500);
            header(Initialization::getProtocol()." 301 ".Response::ReasonPhrase[301], true);
            header("Location: ".$route[RouteProperty::Redirect]);
            exit;
        }
        return $route;
    }
}
?>