<?php
    namespace PHPCore;

    interface ErrorServiceInterface {
        public function process(\Throwable $e);
    }
?>