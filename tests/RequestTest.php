<?php
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestFactory;
use Psr\Http\Message\StreamFactory;
use Psr\Http\Message\UriFactory;
use PHPCore\Initialization;

class RequestTest extends TestCase
{
    public function testInitializeRequest()
    {
        $uriFactory = new UriFactory();
        $streamFactory = new StreamFactory();
        $requestFactory = new ServerRequestFactory();
        $request = $requestFactory->createServerRequest(Initialization::getMethod(), Initialization::getServerParams())
            ->withProtocolVersion(Initialization::getProtocolVersion())
            ->withQueryParams(Initialization::getQueryParams())
            ->withBody($streamFactory->createStreamFromFile(Initialization::getBody()))
            ->withParsedBody(Initialization::getParsedBody())
            ->withCookieParams(Initialization::getCookies())
            ->withUploadedFiles(Initialization::getUploadedFiles())
            ->withUri($uriFactory->createUri()
                ->withScheme(Initialization::getScheme())
                ->withUserInfo(Initialization::getUser(), Initialization::getPassword())
                ->withHost(Initialization::getHost())
                ->withPort(Initialization::getPort())
                ->withPath(Initialization::getPath())
                ->withQuery(Initialization::getQuery()));
        foreach (Initialization::getHeaders() as $name => $value)
            $request = $request->withAddedHeader($name, $value);
        var_dump($request);
    }
}
?>