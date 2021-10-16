<?php
namespace Psr\Http\Message;

class Uri implements UriInterface
{
    protected $scheme = '';
    protected $user = '';
    protected $password = null;
    protected $host = '';
    protected $port = null;
    protected $path = '';
    protected $query = '';
    protected $fragment = '';

    public const SUPPORTED_SCHEMES = 
    [
        '' => null,
        'http' => 80,
        'https' => 443
    ];

    public function getScheme()
    {
        return $this->scheme;
    }

    public function getAuthority()
    {
        $user = $this->getUserInfo();
        $port = $this->getPort();
        $authority = $this->getHost();
        if (isset($this->user) && $user !== '')
            $authority = $user . '@' . $authority;
        if ($port !== null)
            $authority = $authority . ':' . $port;
        return $authority;
    }

    public function getUserInfo()
    {
        $user = $this->user;
        if (isset($this->password) && $this->password !== '')
            $user .= ':' . $this->password;
        return $user;
    }

    public function getHost()
    {
        return $this->host;
    }

    public function getPort()
    {
        return static::SUPPORTED_SCHEMES[$this->scheme] === $this->port ? null : $this->port;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getQuery()
    {
        return $this->query;
    }

    public function getFragment()
    {
        return $this->fragment;
    }

    public function withScheme($scheme)
    {
        if (!is_string($scheme))
            throw new \InvalidArgumentException(ErrorMessage::invalidScheme);
        $scheme = strtolower($scheme);
        if (!key_exists($scheme, static::SUPPORTED_SCHEMES))
            throw new \InvalidArgumentException(ErrorMessage::unsupportedScheme);
        $clone = clone $this;
        $clone->scheme = $scheme;
        return $clone;
    }

    public function withUserInfo($user, $password = null)
    {
        $clone = clone $this;
        $clone->user = $user;
        $clone->password = $password;
        return $clone;
    }

    public function withHost($host)
    {
        if (!is_string($host))
            throw new \InvalidArgumentException(ErrorMessage::invalidHostname);
        $host = strtolower($host);
        $clone = clone $this;
        $clone->host = $host;
        return $clone;
    }

    public function withPort($port)
    {
        if (!is_null($port) && (!is_integer($port) || ($port < 1 && $port > 65535)))
            throw new \InvalidArgumentException(ErrorMessage::invalidPort);
        $clone = clone $this;
        $clone->port = $port;
        return $clone;
    }

    public function withPath($path)
    {
        if (!is_string($path))
            throw new \InvalidArgumentException(ErrorMessage::invalidPath);
        $match = preg_replace_callback(
            '/(?:[^a-zA-Z0-9_\-\.~:@&=\+\$,\/;%]+|%(?![A-Fa-f0-9]{2}))/',
            function ($match) {
                return rawurlencode($match[0]);
            },
            $path
        );
        $path = is_string($match) ? $match : '';
        $clone = clone $this;
        $clone->path = $path;
        return $clone;
    }

    public function withQuery($query)
    {
        if (!is_string($query))
            throw new \InvalidArgumentException(ErrorMessage::invalidQuery);
        $query = ltrim($query, '?');
        $match = preg_replace_callback(
            '/(?:[^a-zA-Z0-9_\-\.~!\$&\'\(\)\*\+,;=%:@\/\?]+|%(?![A-Fa-f0-9]{2}))/',
            function ($match) {
                return rawurlencode($match[0]);
            },
            $query
        );
        $query = is_string($match) ? $match : '';
        $clone = clone $this;
        $clone->query = $query;
        return $clone;
    }

    public function withFragment($fragment)
    {
        if (!is_string($fragment))
            throw new \InvalidArgumentException(ErrorMessage::invalidFragment);
        $fragment = ltrim($fragment, '#');
        $match = preg_replace_callback
        (
            '/(?:[^a-zA-Z0-9_\-\.~!\$&\'\(\)\*\+,;=%:@\/\?]+|%(?![A-Fa-f0-9]{2}))/',
            function ($match)
            {
                return rawurlencode($match[0]);
            },
            $fragment
        );
        $fragment = is_string($match) ? $match : '';
        $clone = clone $this;
        $clone->fragment = $fragment;
        return $clone;
    }

    public function __toString()
    {
        $scheme = $this->getScheme();
        $authority = $this->getAuthority();
        $path = $this->getPath();
        $query = $this->getQuery();
        $fragment = $this->getFragment();
        $path = '/' . ltrim($path, '/');
        return ($scheme !== '' ? $scheme . ':' : '')
            . ($authority !== '' ? '//' . $authority : '')
            . $path
            . ($query !== '' ? '?' . $query : '')
            . ($fragment !== '' ? '#' . $fragment : '');
    }
}
?>