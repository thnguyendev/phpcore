<?php
    namespace Psr\Http\Message;

    class RequestFactory implements RequestFactoryInterface {
        public function createRequest(string $method, $uri): RequestInterface {
            return new Request();
        }
    }
?>