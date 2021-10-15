<?php
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\Stream;

class StreamTest extends TestCase
{
    public function testInvalidResource(): void
    {
        $handle = "not a resource";
        $this->expectException(InvalidArgumentException::class);
        $stream = new Stream($handle);
    }
}
?>