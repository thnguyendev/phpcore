<?php
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\Message;

class MessageTest extends TestCase
{
    public function testMessage()
    {
        $message = new Message();
        $this->assertSame("1.0", $message->withProtocolVersion("1.0")->getProtocolVersion());
        $message = $message->withHeader("String", "value");
        $this->assertNotEmpty($message->getHeaders());
        $this->assertTrue($message->hasHeader("string"));
        $this->assertSame(["value"], $message->getHeader("string"));
        $message = $message->withHeader("Array", array("value1", "value2"));
        $this->assertSame("value", $message->getHeaderLine("string"));
        $this->assertSame("value1,value2", $message->getHeaderLine("array"));
        $message = $message->withAddedHeader("string", "added value");
        $this->assertSame("value,added value", $message->getHeaderLine("string"));
        $message = $message->withAddedHeader("array", array("added value"));
        $this->assertSame("value1,value2,added value", $message->getHeaderLine("array"));
        $message = $message->withoutHeader("string");
        $this->assertFalse($message->hasHeader("string"));
    }
}
?>