<?php
use PHPUnit\Framework\TestCase;
use PHPWebCore\HttpMethod;
use Psr\Http\Message\RequestFactory;
use Psr\Http\Message\UriInterface;

class RequestTest extends TestCase
{
    public function testRequest(): void
    {
        $factory = new RequestFactory();
        $request = $factory->createRequest(HttpMethod::Get, "https://user:password@phpwebcore.test:4443/path?query=test#fragment");
        $this->assertSame(HttpMethod::Get, $request->getMethod());
        $this->assertSame("/path?query=test", $request->getRequestTarget());
        $this->assertInstanceOf(UriInterface::class, $request->getUri());
    }
}
?>