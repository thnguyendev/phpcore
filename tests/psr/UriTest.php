<?php
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UriFactory;

class UriTest extends TestCase
{
    public function testUri(): void
    {
        $factory = new UriFactory();
        $uri = $factory->createUri("https://user:password@phpwebcore.test:4443/path?query=test#fragment");
        $this->assertSame("https", $uri->getScheme());
        $this->assertSame("user:password@phpwebcore.test:4443", $uri->getAuthority());
        $this->assertSame("/path", $uri->getPath());
        $this->assertSame("query=test", $uri->getQuery());
        $this->assertSame("fragment", $uri->getFragment());
    }
}
?>