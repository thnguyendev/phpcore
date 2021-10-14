<?php
    namespace Psr\Http\Message;

    class UriFactory implements UriFactoryInterface {
        public function createUri(string $uri = '') : UriInterface {
            return new Uri();
        }
    }
?>