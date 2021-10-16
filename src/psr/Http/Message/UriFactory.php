<?php
namespace Psr\Http\Message;

class UriFactory implements UriFactoryInterface
{
    public function createUri(string $uri = ''): UriInterface
    {
        $components = parse_url($uri);
        if ($components === false)
            throw new \InvalidArgumentException('Uri cannot be parsed');
        $result = new Uri();
        if (isset($components["scheme"]))
            $result = $result->withScheme($components["scheme"]);
        if (isset($components["host"]))
            $result = $result->withHost($components["host"]);
        if (isset($components["port"]))
            $result = $result->withPort($components["port"]);
        if (isset($components["user"]))
            $result = $result->withUserInfo($components["user"], $components["pass"] ?? null);
        if (isset($components["path"]))
            $result = $result->withPath($components["path"]);
        if (isset($components["query"]))
            $result = $result->withQuery($components["query"]);
        if (isset($components["fragment"]))
            $result = $result->withFragment($components["fragment"]);
        return $result;
    }
}
?>