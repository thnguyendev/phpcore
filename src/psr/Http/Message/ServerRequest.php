<?php
namespace Psr\Http\Message;

class ServerRequest extends Request implements ServerRequestInterface
{
    protected $serverParams = null;
    protected $cookies = null;
    protected $parsedBody = null;
    protected $uploadedFiles;
    protected $attributes = [];

    public function __construct($serverParams = [])
    {
        $this->serverParams = $serverParams;
    }

    public function getServerParams()
    {
        return $this->serverParams;
    }

    public function getCookieParams()
    {
        return $this->cookies;
    }

    public function withCookieParams(array $cookies)
    {
        $clone = clone $this;
        $clone->cookies = $cookies;
        return $clone;
    }

    public function getQueryParams()
    {
        if ($this->uri === null)
            return [];
        parse_str($this->uri->getQuery(), $queryParams);
        return $queryParams;
    }

    public function withQueryParams(array $query)
    {
        $clone = clone $this;
        if (!isset($clone->uri))
            $clone->uri = new Uri();
        $clone->uri = $clone->uri->withQuery(http_build_query($query, encoding_type: PHP_QUERY_RFC3986));
        return $clone;
    }

    public function getUploadedFiles()
    {
        return $this->uploadedFiles;
    }

    public function withUploadedFiles(array $uploadedFiles)
    {
        $clone = clone $this;
        $clone->uploadedFiles = $uploadedFiles;
        return $clone;
    }

    public function getParsedBody()
    {
        return $this->parsedBody;
    }

    public function withParsedBody($data)
    {
        if (!is_null($data) && !is_object($data) && !is_array($data))
            throw new \InvalidArgumentException(ErrorMessage::invalidParsedBody);
        $clone = clone $this;
        $clone->parsedBody = $data;
        return $clone;
    }

    public function getAttributes()
    {
        return $this->attributes;
    }

    public function getAttribute($name, $default = null)
    {
        return isset($this->attributes[$name]) ? $this->attributes[$name] : $default;
    }

    public function withAttribute($name, $value)
    {
        $clone = clone $this;
        $clone->attributes[$name] = $value;
        return $clone;
    }

    public function withoutAttribute($name)
    {
        $clone = clone $this;
        unset($clone->attributes[$name]);
        return $clone;
    }
}
?>