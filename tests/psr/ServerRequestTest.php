<?php
use PHPUnit\Framework\TestCase;
use PHPWebCore\HttpMethod;
use Psr\Http\Message\ServerRequestFactory;

class ServerRequestTest extends TestCase
{
    public function testServerRequest(): void
    {
        $factory = new ServerRequestFactory();
        $request = $factory->createServerRequest(HttpMethod::Get, "https://user:password@phpwebcore.test:4443/path?query=test#fragment");
        $this->assertNotEmpty($request->getQueryParams());
        $this->assertEmpty($request->getCookieParams());
        $this->assertEmpty($request->getUploadedFiles());
    }
}
?>