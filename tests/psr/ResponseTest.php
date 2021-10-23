<?php
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactory;

class ResponseTest extends TestCase
{
    public function testResponse(): void
    {
        $factory = new ResponseFactory();
        $response = $factory->createResponse();
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame("OK", $response->getReasonPhrase());
    }
}
?>