<?php
    namespace Psr\Http\Message;

    class UriFactory implements UriFactoryInterface
    {
        public function createUri(string $uri = '') : UriInterface
        {
            $components = parse_url($uri);
            if ($components === false)
                throw new \InvalidArgumentException('Uri cannot be parsed');
            return (new Uri())
                ->withScheme($components["scheme"])
                ->withHost($components["host"])
                ->withPort($components["port"])
                ->withUserInfo($components["user"], $components["pass"])
                ->withPath($components["path"])
                ->withQuery($components["query"])
                ->withFragment($components["fragment"]);
        }
    }
?>