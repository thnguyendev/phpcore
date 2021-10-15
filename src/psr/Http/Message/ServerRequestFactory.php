<?php
namespace Psr\Http\Message;

class ServerRequestFactory implements ServerRequestFactoryInterface
{
    public function createServerRequest(string $method, $uri, array $serverParams = []): ServerRequestInterface
    {
        if (!is_string($uri) && !$uri instanceof UriInterface)
            throw new \InvalidArgumentException("Uri must be a string or an instance of UriInterface");
        if (is_string($uri))
            return (new ServerRequest($serverParams))
                ->withMethod($method)
                ->withUri((new UriFactory())->createUri($uri));
        else
            return (new ServerRequest($serverParams))
                ->withMethod($method)
                ->withUri($uri);
    }
}
?>