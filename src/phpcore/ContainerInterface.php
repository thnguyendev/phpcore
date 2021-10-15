<?php
    namespace PHPCore;

    interface ContainerInterface
    {
        public function withSingleton(string $id, string|object $entry);
        public function withTransient(string $id, string $entry);
        public function get(string $id);
        public function has(string $id);
    }
?>