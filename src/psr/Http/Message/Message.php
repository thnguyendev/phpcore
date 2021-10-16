<?php
namespace Psr\Http\Message;

class Message implements MessageInterface
{
    /* @var string */ 
    protected string $protocolVersion;
    /* @var string[][] */
    protected $headers = [];
    /* @var StreamInterface */
    protected StreamInterface $body;

    public function getProtocolVersion()
    {
        return $this->protocolVersion;
    }

    public function withProtocolVersion($version)
    {
        $clone = clone $this;
        $clone->protocolVersion = $version;
        return $clone;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function hasHeader($name)
    {
        return isset($this->headers[strtolower($name)]);
    }

    public function getHeader($name)
    {
        $value = [];
        $normalized = strtolower($name);
        if (isset($this->headers[$normalized]))
            $value = $this->headers[$normalized];
        return $value;
    }

    public function getHeaderLine($name)
    {
        return implode(',', $this->getHeader($name));
    }

    public function withHeader($name, $value)
    {
        $this->validateHeader($name, $value);
        $normalized = strtolower($name);
        $clone = clone $this;
        if (is_array($value))
            $clone->headers[$normalized] = $value;
        else {
            $clone->headers[$normalized] = [];
            array_push($clone->headers[$normalized], $value);
        }
        return $clone;
    }

    public function withAddedHeader($name, $value)
    {
        $this->validateHeader($name, $value);
        $normalized = strtolower($name);
        $clone = clone $this;
        if (!isset($clone->headers[$normalized]))
            $clone->headers[$normalized] = [];
        if (is_array($value))
            $clone->headers[$normalized] = array_merge($clone->headers[$normalized], $value);
        else
            array_push($clone->headers[$normalized], $value);
        return $clone;
    }

    public function withoutHeader($name)
    {
        $normalized = strtolower($name);
        $clone = clone $this;
        if (isset($clone->headers[$normalized]))
            unset($clone->headers[$normalized]);
        return $clone;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function withBody(StreamInterface $body)
    {
        if (!$body instanceof StreamInterface)
            throw new \InvalidArgumentException(ErrorMessage::invalidBody);
        $clone = clone $this;
        $clone->body = $body;
        return $clone;
    }

    protected function validateHeader($name, $value)
    {
        if (!is_string($name) || empty($name))
            throw new \InvalidArgumentException(ErrorMessage::invalidHeaderName);
        if ((!is_string($value) && !is_array($value)) || empty($value))
            throw new \InvalidArgumentException(ErrorMessage::invalidHeaderValue);
        if (is_array($value))
        {
            foreach($value as $item)
            {
                if (!is_string($item))
                throw new \InvalidArgumentException(ErrorMessage::invalidHeaderValue);
            }
        }
    }
}
?>