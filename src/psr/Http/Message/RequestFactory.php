<?php
namespace Psr\Http\Message;

class RequestFactory implements RequestFactoryInterface
{
    public function createRequest(string $method, $uri): RequestInterface
    {
        if (!is_string($uri) && !$uri instanceof UriInterface)
            throw new \InvalidArgumentException("Uri must be a string or an instance of UriInterface");
        if (is_string($uri))
            return (new Request())
                ->withMethod($method)
                ->withUri((new UriFactory())->createUri($uri));
        else
            return (new Request())
                ->withMethod($method)
                ->withUri($uri);
    }
}
?>