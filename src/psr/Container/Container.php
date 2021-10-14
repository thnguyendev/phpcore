<?php
    namespace Psr\Container;

    use Psr\Container\ContainerInterface;

    class Container implements ContainerInterface {
        public function get($id) {

        }

        public function has($id): bool {
            return false;
        }
    }
?>