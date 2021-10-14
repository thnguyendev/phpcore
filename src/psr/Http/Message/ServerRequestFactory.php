<?php
    namespace Psr\Http\Message;

    class ServerRequestFactory implements ServerRequestFactoryInterface {
        public function createServerRequest(string $method, $uri, array $serverParams = []): ServerRequestInterface {
            return new ServerRequest();
        }
    }
?>