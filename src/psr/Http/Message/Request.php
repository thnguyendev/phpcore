<?php
namespace Psr\Http\Message;

class Request extends Message implements RequestInterface
{
    protected $requestTarget = null;
    protected $uri = null;
    protected $method;

    public function getRequestTarget()
    {
        if ($this->requestTarget)
            return $this->requestTarget;    
        if ($this->uri === null)
            return '/';
        $path = $this->uri->getPath();
        $path = '/' . ltrim($path, '/');
        $query = $this->uri->getQuery();
        return $query ? $path . '?' . $query : $path;
    }

    public function withRequestTarget($requestTarget)
    {
        if (preg_match('#\s#', $requestTarget))
            throw new \InvalidArgumentException(ErrorMessage::invalideRequestTarget);
        $clone = clone $this;
        $clone->requestTarget = $requestTarget;
        return $clone;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function withMethod($method)
    {
        if (!is_string($method) || preg_match('/^[!#$%&\'*+.^_`|~0-9a-z-]+$/i', $method) !== 1)
            throw new \InvalidArgumentException(ErrorMessage::unsupportedMethod);
        $clone = clone $this;
        $clone->method = $method;
        return $clone;
    }

    public function getUri()
    {
        return $this->uri;
    }

    public function withUri(UriInterface $uri, $preserveHost = false)
    {
        $name = 'Host';
        $clone = clone $this;
        $host = $uri->getHost();
        if ($host !== '' && (!$preserveHost || !$clone->hasHeader($name) || $clone->getHeaderLine($name) === ''))
            $clone = $clone->withHeader($name, $host);
        $clone->uri = $uri;
        return $clone;
    }
}
?>