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
        $words = explode("/", trim($path, "/"));
        $wordCount = count($words);
        foreach ($this->mapping as $key => $value)
        {
            if (!is_string($key) || !is_array($value))
                continue;
            if (isset($value["methods"]) && !in_array($method, $value["methods"]))
                continue;
            $keys = explode("/", trim($key, "/"));
            $keyCount = count($keys);
            $paramCount = 0;
            if (isset($value["params"]) && is_array($value["params"]))
                $paramCount = count($value["params"]);
            if ($keyCount + $paramCount !== $wordCount || ($route !== null && $keyCount <= $count))
                continue;
            $paths = array_slice($words, 0, $keyCount);
            if (strtolower(join("/", $paths)) === strtolower(trim($key, "/")))
            {
                $params = [];
                if ($paramCount > 0)
                {
                    $paramValues = array_slice($words, $keyCount);
                    $i = 0;
                    while ($i < $paramCount)
                    {
                        if (isset($paramValues[$i]))
                            $params[$value["params"][$i]] = $paramValues[$i];
                        $i++;
                    }
                }
                $clone = array_merge(array(), $value);
                $clone["params"] = $params;
                $route = $clone;
                $count = $keyCount;
            }
        }
        if ($route === null)
            throw new NotFoundException($code = 404);
        else
            return $route;
    }
}
?>