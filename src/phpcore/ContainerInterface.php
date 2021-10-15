<?php
    namespace Phpcore;

    interface ContainerInterface
    {
        public function withSingleton($id, $entry);
        public function withTransient($id, $entry);
        public function get($id);
        public function has($id);
    }
?>