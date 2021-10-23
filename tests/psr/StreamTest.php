<?php
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\Stream;
use Psr\Http\Message\StreamFactory;

class StreamTest extends TestCase
{
    public function testStream(): void
    {
        $content = "testing content";
        $resource = tmpfile();
        fwrite($resource, $content);
        rewind($resource);
        $factory = new StreamFactory();
        $stream = $factory->createStreamFromResource($resource);
        $this->assertInstanceOf(Stream::class, $stream);
        $this->assertSame($content, $stream->__toString());
        fclose($resource);
    }
}
?>