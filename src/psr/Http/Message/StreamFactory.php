<?php
    namespace Psr\Http\Message;

    class StreamFactory implements StreamFactoryInterface
    {
        public function createStream(string $content = ''): StreamInterface
        {
            return new Stream(null);
        }

        public function createStreamFromFile(string $filename, string $mode = 'r'): StreamInterface
        {
            return new Stream(null);
        }

        public function createStreamFromResource($resource): StreamInterface
        {
            return new Stream(null);
        }
    }
?>