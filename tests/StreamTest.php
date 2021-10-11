<?php
    use PHPUnit\Framework\TestCase;
    use psr\Stream;

    class StreamTest extends TestCase {
        public function testInvalidResource(): void {
            $handle = "not a resource";
            $this->expectException(InvalidArgumentException::class);
            $stream = new Stream($handle);
        }
    }
?>