<?php
    namespace Psr\Http\Message;

    class StreamFactory implements StreamFactoryInterface
    {
        public function createStream(string $content = ''): StreamInterface
        {
            $resource = tmpfile();
            fwrite($resource, $content);
            fseek($resource, 0);
            return new Stream($resource);
        }

        public function createStreamFromFile(string $filename, string $mode = 'r'): StreamInterface
        {
            $resource = fopen($filename, $mode);
            return new Stream($resource);
        }

        public function createStreamFromResource($resource): StreamInterface
        {
            return new Stream($resource);
        }
    }
?>