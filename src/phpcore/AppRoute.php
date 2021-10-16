<?php
namespace PHPCore;

abstract class AppRoute
{
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
            if (!is_array($value) || !isset($value[RouteProperties::Path]) || !is_string($value[RouteProperties::Path]))
                continue;
            if (isset($value[RouteProperties::Methods]) && !in_array($method, $value[RouteProperties::Methods]))
                continue;
            $trim = trim($value[RouteProperties::Path], "/");
            $keys = $trim === "" ? [] : explode("/", $trim);
            $keyCount = count($keys);
            $paramCount = 0;
            if (isset($value[RouteProperties::Parameters]) && is_array($value[RouteProperties::Parameters]))
                $paramCount = count($value[RouteProperties::Parameters]);
            if ($keyCount + $paramCount !== $wordCount || ($route !== null && $keyCount <= $count))
                continue;
            $paths = array_slice($words, 0, $keyCount);
            if (strtolower(join("/", $paths)) === strtolower(trim($value[RouteProperties::Path], "/")))
            {
                $params = [];
                if ($paramCount > 0)
                {
                    $paramValues = array_slice($words, $keyCount);
                    $i = 0;
                    while ($i < $paramCount)
                    {
                        if (isset($paramValues[$i]))
                            $params[$value[RouteProperties::Parameters][$i]] = $paramValues[$i];
                        $i++;
                    }
                }
                $clone = array_merge(array(), $value);
                $clone[RouteProperties::Parameters] = $params;
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