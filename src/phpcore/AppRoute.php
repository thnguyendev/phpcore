<?php
namespace PHPCore;

abstract class AppRoute
{
    const GetMethod = "GET";
    const PostMethod = "POST";
    const PutMethod = "PUT";
    const DeleteMethod = "DELETE";
    const PatchMethod = "PATCH";
    const OptionsMethod = "OPTIONS";

    const RouteMethods = "Methods";
    const RoutePath = "Path";
    const RouteParameters = "Parameters";
    const RouteController = "Controller";
    const RouteView = "View";
    const RouteAuthorized = "Authorized";
    const RouteRoles = "Roles";

    protected $mapping = [];

    abstract public function initialize();

    public function getRoute(string $method, string $path)
    {
        $route = null;
        $count = 0;
        $trim = trim($path, "/");
        $words = $trim === "" ? [] : explode("/", $trim);
        $wordCount = count($words);
        foreach ($this->mapping as $value)
        {
            if (!is_array($value) || !isset($value[static::RoutePath]) || !is_string($value[static::RoutePath]))
                continue;
            if (isset($value[static::RouteMethods]) && !in_array($method, $value[static::RouteMethods]))
                continue;
            $trim = trim($value[static::RoutePath], "/");
            $keys = $trim === "" ? [] : explode("/", $trim);
            $keyCount = count($keys);
            $paramCount = 0;
            if (isset($value[static::RouteParameters]) && is_array($value[static::RouteParameters]))
                $paramCount = count($value[static::RouteParameters]);
            if ($keyCount + $paramCount !== $wordCount || ($route !== null && $keyCount <= $count))
                continue;
            $paths = array_slice($words, 0, $keyCount);
            if (strtolower(join("/", $paths)) === strtolower(trim($value[static::RoutePath], "/")))
            {
                $params = [];
                if ($paramCount > 0)
                {
                    $paramValues = array_slice($words, $keyCount);
                    $i = 0;
                    while ($i < $paramCount)
                    {
                        if (isset($paramValues[$i]))
                            $params[$value[static::RouteParameters][$i]] = $paramValues[$i];
                        $i++;
                    }
                }
                $clone = array_merge(array(), $value);
                $clone[static::RouteParameters] = $params;
                $route = $clone;
                $count = $keyCount;
            }
        }
        if ($route === null)
            throw new NotFoundException("Route {$path} for {$method} method not found", 404);
        else
            return $route;
    }
}
?>