<?php
namespace Psr\Http\Message;

class StreamFactory implements StreamFactoryInterface
{
    public function createStream(string $content = ''): StreamInterface
    {
        $resource = tmpfile();
        fwrite($resource, $content);
        rewind($resource);
        return new Stream($resource);
    }

    public function createStreamFromFile(string $filename, string $mode = 'r'): StreamInterface
    {
        try
        {
            $resource = fopen($filename, $mode);
            return new Stream($resource);
        }
        catch (\Throwable $e)
        {
            throw new \RuntimeException("Unable to open {$filename} using mode {$mode}: {$e->getMessage()}");
        }
    }

    public function createStreamFromResource($resource): StreamInterface
    {
        if (!is_resource($resource))
            throw new \InvalidArgumentException("Invalid resource");
        return new Stream($resource);
    }
}
?>