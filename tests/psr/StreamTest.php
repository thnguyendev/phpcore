<?php
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\Stream;

use function PHPUnit\Framework\assertInstanceOf;
use function PHPUnit\Framework\assertSame;

class StreamTest extends TestCase
{
    public function testStream(): void
    {
        $content = "testing content";
        $resource = tmpfile();
        fwrite($resource, $content);
        
        $stream = new Stream($resource);
        assertInstanceOf(Stream::class, $stream);
        assertSame($content, $stream->__toString());

        fclose($resource);
    }
}
?>